<?php
declare(strict_types=1);

namespace App\models;

class Notification extends Model
{
    protected string $table = 'notifications';

    public function getUnreadCount(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM `notifications` WHERE `user_id` = :uid AND `is_read` = 0");
        $stmt->execute([':uid' => $userId]);
        return (int)$stmt->fetchColumn();
    }

    public function getAllForUser(int $userId, int $limit = 30): array
    {
        $stmt = $this->db->prepare("SELECT * FROM `notifications` WHERE `user_id` = :uid ORDER BY `id` DESC LIMIT :limit");
        $stmt->bindValue(':uid', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function markAsRead(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE `notifications` SET `is_read` = 1 WHERE `id` = :id AND `user_id` = :uid");
        return $stmt->execute([':id' => $id, ':uid' => $userId]);
    }

    public function markAllAsRead(int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE `notifications` SET `is_read` = 1 WHERE `user_id` = :uid");
        return $stmt->execute([':uid' => $userId]);
    }
}
