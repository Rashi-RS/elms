<?php
declare(strict_types=1);

namespace App\services;

use App\helpers\Session;
use App\models\Database;
use PDO;

class ActivityLogger
{
    public static function log(string $action, string $description, ?int $userId = null, ?string $ip = null): bool
    {
        try {
            $userId = $userId ?? Session::userId();
            $ip = $ip ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');

            $db = Database::pdo();
            $stmt = $db->prepare("INSERT INTO `activity_logs` (`user_id`, `action`, `description`, `ip_address`, `created_at`) VALUES (:user_id, :action, :description, :ip, NOW())");
            return $stmt->execute([
                ':user_id' => $userId,
                ':action' => $action,
                ':description' => $description,
                ':ip' => $ip,
            ]);
        } catch (\Throwable $e) {
            error_log("Failed to write activity log: " . $e->getMessage());
            return false;
        }
    }
}
