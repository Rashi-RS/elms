<?php
declare(strict_types=1);

namespace App\models;

class Department extends Model
{
    protected string $table = 'departments';

    public function getAllWithCounts(): array
    {
        $sql = "SELECT d.*, COUNT(e.id) as employee_count 
                FROM `departments` d 
                LEFT JOIN `employees` e ON e.department_id = d.id AND e.status != 'terminated' 
                GROUP BY d.id 
                ORDER BY d.name ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function hasEmployees(int $departmentId): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM `employees` WHERE `department_id` = :deptId AND `status` != 'terminated'");
        $stmt->execute([':deptId' => $departmentId]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
