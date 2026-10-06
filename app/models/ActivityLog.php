<?php
declare(strict_types=1);

namespace App\models;

use PDO;

class ActivityLog extends Model
{
    protected string $table = 'activity_logs';

    public function getRecent(int $limit = 10): array
    {
        $sql = "SELECT al.*, u.email, u.role, e.first_name, e.last_name 
                FROM `activity_logs` al 
                LEFT JOIN `users` u ON u.id = al.user_id 
                LEFT JOIN `employees` e ON e.user_id = u.id 
                ORDER BY al.id DESC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllWithUser(string $search = '', int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $where = ["1=1"];
        $params = [];

        if ($search !== '') {
            $where[] = "(al.action LIKE :s1 OR al.description LIKE :s2 OR u.email LIKE :s3 OR al.ip_address LIKE :s4)";
            $params[':s1'] = "%{$search}%";
            $params[':s2'] = "%{$search}%";
            $params[':s3'] = "%{$search}%";
            $params[':s4'] = "%{$search}%";
        }

        $whereClause = implode(' AND ', $where);

        $countSql = "SELECT COUNT(*) FROM `activity_logs` al 
                     LEFT JOIN `users` u ON u.id = al.user_id 
                     WHERE {$whereClause}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $sql = "SELECT al.*, u.email, u.role, e.first_name, e.last_name 
                FROM `activity_logs` al 
                LEFT JOIN `users` u ON u.id = al.user_id 
                LEFT JOIN `employees` e ON e.user_id = u.id 
                WHERE {$whereClause} 
                ORDER BY al.id DESC 
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $lastPage = (int)ceil($total / $perPage);

        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => max(1, $lastPage),
            'has_next' => $page < $lastPage,
            'has_prev' => $page > 1,
        ];
    }
}
