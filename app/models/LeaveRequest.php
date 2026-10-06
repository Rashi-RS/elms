<?php
declare(strict_types=1);

namespace App\models;

use PDO;

class LeaveRequest extends Model
{
    protected string $table = 'leave_requests';

    public function getAllWithDetails(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $where = ["1=1"];
        $params = [];

        if (!empty($filters['employee_id'])) {
            $where[] = "lr.employee_id = :empId";
            $params[':empId'] = (int)$filters['employee_id'];
        }

        if (!empty($filters['status'])) {
            $where[] = "lr.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['leave_type_id'])) {
            $where[] = "lr.leave_type_id = :typeId";
            $params[':typeId'] = (int)$filters['leave_type_id'];
        }

        if (!empty($filters['start_date'])) {
            $where[] = "lr.start_date >= :startDate";
            $params[':startDate'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $where[] = "lr.end_date <= :endDate";
            $params[':endDate'] = $filters['end_date'];
        }

        $whereClause = implode(' AND ', $where);

        $countSql = "SELECT COUNT(*) as total 
                     FROM `leave_requests` lr 
                     JOIN `employees` e ON e.id = lr.employee_id 
                     JOIN `leave_types` lt ON lt.id = lr.leave_type_id 
                     WHERE {$whereClause}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $sql = "SELECT lr.*, lt.name as leave_type_name, e.first_name, e.last_name, e.employee_code, d.name as department_name, u.email as approver_email 
                FROM `leave_requests` lr 
                JOIN `employees` e ON e.id = lr.employee_id 
                JOIN `leave_types` lt ON lt.id = lr.leave_type_id 
                LEFT JOIN `departments` d ON d.id = e.department_id 
                LEFT JOIN `users` u ON u.id = lr.approved_by 
                WHERE {$whereClause} 
                ORDER BY lr.id DESC 
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

    public function getDetails(int $id): ?array
    {
        $sql = "SELECT lr.*, lt.name as leave_type_name, e.first_name, e.last_name, e.employee_code, e.department_id, d.name as department_name, u.email as approver_email 
                FROM `leave_requests` lr 
                JOIN `employees` e ON e.id = lr.employee_id 
                JOIN `leave_types` lt ON lt.id = lr.leave_type_id 
                LEFT JOIN `departments` d ON d.id = e.department_id 
                LEFT JOIN `users` u ON u.id = lr.approved_by 
                WHERE lr.id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getStatusCounts(): array
    {
        $stmt = $this->db->query("SELECT status, COUNT(*) as count FROM `leave_requests` GROUP BY status");
        $results = $stmt->fetchAll();

        $counts = ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'cancelled' => 0];
        foreach ($results as $r) {
            $counts[$r['status']] = (int)$r['count'];
        }
        return $counts;
    }

    public function getRecentLeavesForEmployees(array $employeeIds, int $limit = 5): array
    {
        if (empty($employeeIds)) {
            return [];
        }
        $in = implode(',', array_map('intval', $employeeIds));
        $sql = "SELECT lr.*, lt.name as leave_type_name 
                FROM `leave_requests` lr 
                JOIN `leave_types` lt ON lt.id = lr.leave_type_id 
                WHERE lr.employee_id IN ({$in}) 
                ORDER BY lr.created_at DESC";
        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll();

        $byEmployee = [];
        foreach ($rows as $r) {
            $empId = (int)$r['employee_id'];
            if (!isset($byEmployee[$empId])) {
                $byEmployee[$empId] = [];
            }
            if (count($byEmployee[$empId]) < $limit) {
                $byEmployee[$empId][] = $r;
            }
        }
        return $byEmployee;
    }
}

