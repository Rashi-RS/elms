<?php
declare(strict_types=1);

namespace App\models;

use DateTime;
use PDO;

class Attendance extends Model
{
    protected string $table = 'attendance';

    public function getTodaySummary(): array
    {
        $sql = "SELECT 
                    SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_count,
                    SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_count,
                    SUM(CASE WHEN a.status = 'half_day' THEN 1 ELSE 0 END) as half_day_count,
                    SUM(CASE WHEN a.status = 'leave' THEN 1 ELSE 0 END) as leave_count
                FROM `attendance` a 
                WHERE a.attendance_date = CURDATE()";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch();

        return [
            'present' => (int)($row['present_count'] ?? 0),
            'absent' => (int)($row['absent_count'] ?? 0),
            'half_day' => (int)($row['half_day_count'] ?? 0),
            'leave' => (int)($row['leave_count'] ?? 0),
        ];
    }

    public function getRosterForDate(string $date, ?int $departmentId = null): array
    {
        $sql = "SELECT e.id as employee_id, e.employee_code, e.first_name, e.last_name, e.designation, d.name as department_name, 
                       a.id as attendance_id, a.check_in, a.check_out, a.status as attendance_status, a.working_hours, a.remarks 
                FROM `employees` e 
                LEFT JOIN `departments` d ON d.id = e.department_id 
                LEFT JOIN `attendance` a ON a.employee_id = e.id AND a.attendance_date = :attDate 
                WHERE e.status = 'active'";

        $params = [':attDate' => $date];
        if ($departmentId !== null && $departmentId > 0) {
            $sql .= " AND e.department_id = :deptId";
            $params[':deptId'] = $departmentId;
        }

        $sql .= " ORDER BY e.first_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function calculateWorkingHours(?string $checkIn, ?string $checkOut): float
    {
        if (empty($checkIn) || empty($checkOut)) {
            return 0.00;
        }

        $t1 = new DateTime("2000-01-01 " . $checkIn);
        $t2 = new DateTime("2000-01-01 " . $checkOut);

        if ($t2 <= $t1) {
            return 0.00;
        }

        $diff = $t1->diff($t2);
        $hours = $diff->h + ($diff->i / 60.0);
        return round($hours, 2);
    }

    public function markOrUpdate(int $employeeId, string $date, string $status, ?string $checkIn = null, ?string $checkOut = null, ?string $remarks = null): bool
    {
        $workingHours = 0.00;
        if ($status === 'present' || $status === 'half_day') {
            if ($checkIn && $checkOut) {
                $workingHours = $this->calculateWorkingHours($checkIn, $checkOut);
            } elseif ($status === 'present' && !$checkOut) {
                $workingHours = 8.50; // standard shift placeholder if currently checked in
            } elseif ($status === 'half_day' && !$checkOut) {
                $workingHours = 4.25;
            }
        }

        // Check if record exists for this employee and date (portable across MySQL and SQLite)
        $checkStmt = $this->db->prepare("SELECT `id` FROM `attendance` WHERE `employee_id` = :empId AND `attendance_date` = :attDate LIMIT 1");
        $checkStmt->execute([':empId' => $employeeId, ':attDate' => $date]);
        $existing = $checkStmt->fetch();

        if ($existing) {
            $sql = "UPDATE `attendance` 
                    SET `check_in` = :checkIn, 
                        `check_out` = :checkOut, 
                        `status` = :status, 
                        `working_hours` = :hours, 
                        `remarks` = :remarks, 
                        `updated_at` = NOW() 
                    WHERE `id` = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $existing['id'],
                ':checkIn' => $checkIn ?: null,
                ':checkOut' => $checkOut ?: null,
                ':status' => $status,
                ':hours' => $workingHours,
                ':remarks' => $remarks ?: null,
            ]);
        } else {
            $sql = "INSERT INTO `attendance` 
                    (`employee_id`, `attendance_date`, `check_in`, `check_out`, `status`, `working_hours`, `remarks`, `created_at`, `updated_at`) 
                    VALUES (:empId, :attDate, :checkIn, :checkOut, :status, :hours, :remarks, NOW(), NOW())";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':empId' => $employeeId,
                ':attDate' => $date,
                ':checkIn' => $checkIn ?: null,
                ':checkOut' => $checkOut ?: null,
                ':status' => $status,
                ':hours' => $workingHours,
                ':remarks' => $remarks ?: null,
            ]);
        }
    }

    public function getEmployeeAttendance(int $employeeId, ?string $startDate = null, ?string $endDate = null): array
    {
        $where = ["a.employee_id = :empId"];
        $params = [':empId' => $employeeId];

        if ($startDate) {
            $where[] = "a.attendance_date >= :start";
            $params[':start'] = $startDate;
        }

        if ($endDate) {
            $where[] = "a.attendance_date <= :end";
            $params[':end'] = $endDate;
        }

        $whereClause = implode(' AND ', $where);
        $sql = "SELECT a.* 
                FROM `attendance` a 
                WHERE {$whereClause} 
                ORDER BY a.attendance_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function getEmployeeStats(int $employeeId, string $startDate, string $endDate): array
    {
        $sql = "SELECT 
                    SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_count,
                    SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_count,
                    SUM(CASE WHEN a.status = 'half_day' THEN 1 ELSE 0 END) as half_day_count,
                    SUM(CASE WHEN a.status = 'leave' THEN 1 ELSE 0 END) as leave_count,
                    COUNT(a.id) as total_logged_days
                FROM `attendance` a 
                WHERE a.employee_id = :empId 
                AND a.attendance_date BETWEEN :start AND :end";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':empId' => $employeeId,
            ':start' => $startDate,
            ':end' => $endDate,
        ]);
        $row = $stmt->fetch();

        $present = (int)($row['present_count'] ?? 0);
        $absent = (int)($row['absent_count'] ?? 0);
        $halfDay = (int)($row['half_day_count'] ?? 0);
        $leave = (int)($row['leave_count'] ?? 0);
        $total = $present + $absent + $halfDay + $leave;

        $effectivePresent = $present + ($halfDay * 0.5);
        $percentage = $total > 0 ? round(($effectivePresent / $total) * 100, 1) : 0.0;

        return [
            'present' => $present,
            'absent' => $absent,
            'half_day' => $halfDay,
            'leave' => $leave,
            'total' => $total,
            'percentage' => $percentage,
        ];
    }

    public function getTodayRecordForEmployee(int $employeeId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `attendance` WHERE `employee_id` = :empId AND `attendance_date` = CURDATE() LIMIT 1");
        $stmt->execute([':empId' => $employeeId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
