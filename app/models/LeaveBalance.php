<?php
declare(strict_types=1);

namespace App\models;

class LeaveBalance extends Model
{
    protected string $table = 'leave_balances';

    public function getEmployeeBalances(int $employeeId, int $year): array
    {
        $sql = "SELECT lb.*, lt.name as leave_type_name, lt.description as leave_type_description 
                FROM `leave_balances` lb 
                JOIN `leave_types` lt ON lt.id = lb.leave_type_id 
                WHERE lb.employee_id = :empId AND lb.year = :year 
                ORDER BY lt.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':empId' => $employeeId,
            ':year' => $year,
        ]);
        return $stmt->fetchAll();
    }

    public function initializeForEmployee(int $employeeId, int $year): void
    {
        $leaveTypes = (new LeaveType())->getActive();
        $stmt = $this->db->prepare("INSERT IGNORE INTO `leave_balances` 
            (`employee_id`, `leave_type_id`, `year`, `allocated_days`, `used_days`, `remaining_days`) 
            VALUES (:empId, :typeId, :year, :limit, 0, :limit)");

        foreach ($leaveTypes as $type) {
            $stmt->execute([
                ':empId' => $employeeId,
                ':typeId' => $type['id'],
                ':year' => $year,
                ':limit' => $type['annual_limit'],
            ]);
        }
    }

    public function getBalancesForEmployees(array $employeeIds, int $year): array
    {
        if (empty($employeeIds)) {
            return [];
        }
        $in = implode(',', array_map('intval', $employeeIds));
        $sql = "SELECT lb.*, lt.name as leave_type_name 
                FROM `leave_balances` lb 
                JOIN `leave_types` lt ON lt.id = lb.leave_type_id 
                WHERE lb.employee_id IN ({$in}) AND lb.year = :year 
                ORDER BY lt.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':year' => $year]);
        $rows = $stmt->fetchAll();

        $byEmployee = [];
        foreach ($rows as $r) {
            $byEmployee[$r['employee_id']][] = $r;
        }
        return $byEmployee;
    }
}

