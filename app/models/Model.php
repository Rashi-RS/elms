<?php
declare(strict_types=1);

namespace App\models;

use PDO;

abstract class Model
{
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function all(string $orderBy = 'id DESC'): array
    {
        $stmt = $this->db->query("SELECT * FROM `{$this->table}` ORDER BY {$orderBy}");
        return $stmt->fetchAll();
    }

    public function where(string $column, mixed $value, string $orderBy = 'id DESC'): array
    {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `{$column}` = :val ORDER BY {$orderBy}");
        $stmt->execute([':val' => $value]);
        return $stmt->fetchAll();
    }

    public function firstWhere(string $column, mixed $value): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `{$column}` = :val LIMIT 1");
        $stmt->execute([':val' => $value]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $columns = array_keys($data);
        $fields = implode('`, `', $columns);
        $placeholders = ':' . implode(', :', $columns);

        $sql = "INSERT INTO `{$this->table}` (`{$fields}`) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);

        $params = [];
        foreach ($data as $key => $val) {
            $params[':' . $key] = $val;
        }

        $stmt->execute($params);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $setClauses = [];
        $params = [':pk' => $id];

        foreach ($data as $column => $value) {
            $setClauses[] = "`{$column}` = :{$column}";
            $params[':' . $column] = $value;
        }

        $setString = implode(', ', $setClauses);
        $sql = "UPDATE `{$this->table}` SET {$setString} WHERE `{$this->primaryKey}` = :pk";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function count(string $where = '1=1', array $params = []): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM `{$this->table}` WHERE {$where}");
        $stmt->execute($params);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }

    public function paginate(int $page = 1, int $perPage = 10, string $where = '1=1', array $params = [], string $orderBy = 'id DESC'): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $total = $this->count($where, $params);
        $lastPage = (int)ceil($total / $perPage);

        $sql = "SELECT * FROM `{$this->table}` WHERE {$where} ORDER BY {$orderBy} LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetchAll();

        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => max(1, $lastPage),
            'has_next' => $page < $lastPage,
            'has_prev' => $page > 1,
        ];
    }
}
