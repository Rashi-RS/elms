<?php
declare(strict_types=1);

namespace App\models;

use App\services\ActivityLogger;
use PDO;
use Throwable;

class Employee extends Model
{
    protected string $table = 'employees';

    public function getAllWithDetails(string $search = '', ?int $departmentId = null, ?string $status = null, int $page = 1, int $perPage = 10): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $where = ["1=1"];
        $params = [];

        if ($search !== '') {
            $where[] = "(e.first_name LIKE :s1 OR e.last_name LIKE :s2 OR e.employee_code LIKE :s3 OR u.email LIKE :s4)";
            $params[':s1'] = "%{$search}%";
            $params[':s2'] = "%{$search}%";
            $params[':s3'] = "%{$search}%";
            $params[':s4'] = "%{$search}%";
        }

        if ($departmentId !== null && $departmentId > 0) {
            $where[] = "e.department_id = :deptId";
            $params[':deptId'] = $departmentId;
        }

        if ($status !== null && $status !== '') {
            $where[] = "e.status = :status";
            $params[':status'] = $status;
        }

        $whereClause = implode(' AND ', $where);

        // Count total matching
        $countSql = "SELECT COUNT(*) as total 
                     FROM `employees` e 
                     LEFT JOIN `users` u ON u.employee_id = e.id 
                     WHERE {$whereClause}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Data query
        $sql = "SELECT e.*, d.name as department_name, u.email, u.last_login, u.status as user_status 
                FROM `employees` e 
                LEFT JOIN `departments` d ON d.id = e.department_id 
                LEFT JOIN `users` u ON u.employee_id = e.id 
                WHERE {$whereClause} 
                ORDER BY e.id DESC 
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
        $stmt = $this->db->prepare("SELECT e.*, d.name as department_name, u.email, u.last_login, u.status as user_status, u.id as user_id 
                                    FROM `employees` e 
                                    LEFT JOIN `departments` d ON d.id = e.department_id 
                                    LEFT JOIN `users` u ON u.employee_id = e.id 
                                    WHERE e.id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT e.*, d.name as department_name, u.email 
                                    FROM `employees` e 
                                    JOIN `users` u ON u.employee_id = e.id 
                                    LEFT JOIN `departments` d ON d.id = e.department_id 
                                    WHERE u.id = :uid LIMIT 1");
        $stmt->execute([':uid' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function generateEmployeeCode(): string
    {
        $stmt = $this->db->query("SELECT MAX(id) as max_id FROM `employees`");
        $row = $stmt->fetch();
        $nextId = ((int)($row['max_id'] ?? 0)) + 1;
        return 'EMP-' . str_pad((string)(1000 + $nextId), 4, '0', STR_PAD_LEFT);
    }

    public function createWithUserAndBalances(array $employeeData, string $email, string $password): array
    {
        try {
            $this->db->beginTransaction();

            // 1. Create User account first
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $userStmt = $this->db->prepare("INSERT INTO `users` (`email`, `password`, `role`, `status`, `created_at`) VALUES (:email, :pwd, 'employee', 'active', NOW())");
            $userStmt->execute([
                ':email' => $email,
                ':pwd' => $passwordHash,
            ]);
            $userId = (int)$this->db->lastInsertId();

            // 2. Create Employee record with user_id
            $employeeData['user_id'] = $userId;
            $columns = array_keys($employeeData);
            $fields = implode('`, `', $columns);
            $placeholders = ':' . implode(', :', $columns);

            $empStmt = $this->db->prepare("INSERT INTO `employees` (`{$fields}`) VALUES ({$placeholders})");
            $empParams = [];
            foreach ($employeeData as $k => $v) {
                $empParams[':' . $k] = $v;
            }
            $empStmt->execute($empParams);
            $employeeId = (int)$this->db->lastInsertId();

            // 3. Link employee_id back into users table
            $linkStmt = $this->db->prepare("UPDATE `users` SET `employee_id` = :empId WHERE `id` = :uid");
            $linkStmt->execute([':empId' => $employeeId, ':uid' => $userId]);

            // 4. Initialize leave balances for all active leave types for current year
            $currentYear = (int)date('Y');
            $leaveTypesStmt = $this->db->query("SELECT id, annual_limit FROM `leave_types` WHERE `status` = 'active'");
            $leaveTypes = $leaveTypesStmt->fetchAll();

            $balStmt = $this->db->prepare("INSERT INTO `leave_balances` 
                (`employee_id`, `leave_type_id`, `year`, `allocated_days`, `used_days`, `remaining_days`) 
                VALUES (:empId, :typeId, :year, :limit, 0, :limit)");

            foreach ($leaveTypes as $type) {
                $balStmt->execute([
                    ':empId' => $employeeId,
                    ':typeId' => $type['id'],
                    ':year' => $currentYear,
                    ':limit' => $type['annual_limit'],
                ]);
            }

            // 5. Create initial welcome notification
            $notif = $this->db->prepare("INSERT INTO `notifications` (`user_id`, `title`, `message`, `type`, `created_at`) 
                                        VALUES (:uid, 'Welcome to ELMS', 'Your employee account has been created. Explore your dashboard and profile.', 'system', NOW())");
            $notif->execute([':uid' => $userId]);

            // 6. Log activity
            ActivityLogger::log('Employee Created', "Created employee {$employeeData['first_name']} {$employeeData['last_name']} ({$employeeData['employee_code']}) with user account.");

            $this->db->commit();
            return ['success' => true, 'employee_id' => $employeeId, 'user_id' => $userId];

        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Failed creating employee with user: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function deleteWithRelations(int $employeeId): bool
    {
        $employee = $this->find($employeeId);
        if (!$employee) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            // 1. Delete attendance logs
            $stmt = $this->db->prepare("DELETE FROM `attendance` WHERE `employee_id` = :id");
            $stmt->execute([':id' => $employeeId]);

            // 2. Delete leave requests
            $stmt = $this->db->prepare("DELETE FROM `leave_requests` WHERE `employee_id` = :id");
            $stmt->execute([':id' => $employeeId]);

            // 3. Delete leave balances
            $stmt = $this->db->prepare("DELETE FROM `leave_balances` WHERE `employee_id` = :id");
            $stmt->execute([':id' => $employeeId]);

            // 4. Clean user notifications & account
            if (!empty($employee['user_id'])) {
                $userId = (int)$employee['user_id'];
                $stmt = $this->db->prepare("DELETE FROM `notifications` WHERE `user_id` = :uid");
                $stmt->execute([':uid' => $userId]);

                // Break circular link before deleting user
                $stmt = $this->db->prepare("UPDATE `employees` SET `user_id` = NULL WHERE `id` = :id");
                $stmt->execute([':id' => $employeeId]);

                $stmt = $this->db->prepare("DELETE FROM `users` WHERE `id` = :uid");
                $stmt->execute([':uid' => $userId]);
            }

            // 5. Delete employee record
            $stmt = $this->db->prepare("DELETE FROM `employees` WHERE `id` = :id");
            $stmt->execute([':id' => $employeeId]);

            // 6. Log system activity
            ActivityLogger::log(
                'Employee Removed',
                "Permanently removed employee {$employee['first_name']} {$employee['last_name']} ({$employee['employee_code']})."
            );

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Failed deleting employee #{$employeeId}: " . $e->getMessage());
            return false;
        }
    }
}

