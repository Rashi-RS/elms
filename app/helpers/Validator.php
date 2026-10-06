<?php
declare(strict_types=1);

namespace App\helpers;

use App\models\Database;
use PDO;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        if (!empty($rules)) {
            $this->validate();
        }
    }

    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $fieldRules) {
            $ruleList = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;
            $value = $this->data[$field] ?? null;

            foreach ($ruleList as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$ruleName, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                } else {
                    $ruleName = $rule;
                }

                $this->applyRule($field, $ruleName, $params, $value);
            }
        }

        return empty($this->errors);
    }

    private function applyRule(string $field, string $rule, array $params, mixed $value): void
    {
        $label = ucwords(str_replace('_', ' ', $field));

        switch ($rule) {
            case 'required':
                if ($value === null || (is_string($value) && trim($value) === '') || (is_array($value) && empty($value))) {
                    $this->addError($field, "{$label} is required.");
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "{$label} must be a valid email address.");
                }
                break;

            case 'min':
                $min = (int)($params[0] ?? 0);
                if (!empty($value) && strlen((string)$value) < $min) {
                    $this->addError($field, "{$label} must be at least {$min} characters.");
                }
                break;

            case 'max':
                $max = (int)($params[0] ?? 0);
                if (!empty($value) && strlen((string)$value) > $max) {
                    $this->addError($field, "{$label} cannot exceed {$max} characters.");
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "{$label} must be numeric.");
                }
                break;

            case 'date':
                if (!empty($value)) {
                    $d = \DateTime::createFromFormat('Y-m-d', (string)$value);
                    if (!$d || $d->format('Y-m-d') !== $value) {
                        $this->addError($field, "{$label} must be a valid date in YYYY-MM-DD format.");
                    }
                }
                break;

            case 'after_or_equal':
                $targetField = $params[0] ?? '';
                $targetValue = $this->data[$targetField] ?? null;
                if (!empty($value) && !empty($targetValue)) {
                    if (strtotime((string)$value) < strtotime((string)$targetValue)) {
                        $targetLabel = ucwords(str_replace('_', ' ', $targetField));
                        $this->addError($field, "{$label} must be on or after {$targetLabel}.");
                    }
                }
                break;

            case 'in':
                if (!empty($value) && !in_array((string)$value, $params, true)) {
                    $allowed = implode(', ', $params);
                    $this->addError($field, "{$label} must be one of: {$allowed}.");
                }
                break;

            case 'same':
                $targetField = $params[0] ?? '';
                $targetValue = $this->data[$targetField] ?? null;
                if ($value !== $targetValue) {
                    $targetLabel = ucwords(str_replace('_', ' ', $targetField));
                    $this->addError($field, "{$label} does not match {$targetLabel}.");
                }
                break;

            case 'unique':
                $table = $params[0] ?? '';
                $column = $params[1] ?? $field;
                $exceptId = $params[2] ?? null;

                if (!empty($value) && !empty($table)) {
                    $sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = :val";
                    $binds = [':val' => $value];
                    if ($exceptId !== null && $exceptId !== '') {
                        $sql .= " AND `id` != :exceptId";
                        $binds[':exceptId'] = (int)$exceptId;
                    }
                    $stmt = Database::pdo()->prepare($sql);
                    $stmt->execute($binds);
                    if ((int)$stmt->fetchColumn() > 0) {
                        $this->addError($field, "This {$label} is already taken.");
                    }
                }
                break;
        }
    }

    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }
}
