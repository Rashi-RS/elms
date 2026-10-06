<?php
declare(strict_types=1);

namespace App\services;

use App\models\Database;
use DateTime;
use PDO;
use Throwable;

class LeaveService
{
    private PDO $db;
    private string $forUpdate;

    public function __construct()
    {
        $this->db = Database::pdo();
        $this->forUpdate = Database::getInstance()->isSqlite() ? '' : ' FOR UPDATE';
    }

    public function calculateDays(string $startDate, string $endDate): int
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);

        if ($start > $end) {
            return 0;
        }

        return (int)$start->diff($end)->days + 1;
    }

    public function hasOverlappingLeave(int $employeeId, string $startDate, string $endDate, ?int $excludeRequestId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM `leave_requests` 
                WHERE `employee_id` = :empId 
                AND `status` IN ('pending', 'approved') 
                AND (`start_date` <= :endDate AND `end_date` >= :startDate)";
        
        $params = [
            ':empId' => $employeeId,
            ':startDate' => $startDate,
            ':endDate' => $endDate,
        ];

        if ($excludeRequestId !== null) {
            $sql .= " AND `id` != :excludeId";
            $params[':excludeId'] = $excludeRequestId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn() > 0;
    }

    public function getBalance(int $employeeId, int $leaveTypeId, int $year): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `leave_balances` WHERE `employee_id` = :empId AND `leave_type_id` = :typeId AND `year` = :year LIMIT 1");
        $stmt->execute([
            ':empId' => $employeeId,
            ':typeId' => $leaveTypeId,
            ':year' => $year,
        ]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function applyLeave(int $employeeId, int $leaveTypeId, string $startDate, string $endDate, string $reason): array
    {
        $days = $this->calculateDays($startDate, $endDate);
        if ($days <= 0) {
            return ['success' => false, 'message' => 'End date cannot be prior to start date.'];
        }

        $year = (int)(new DateTime($startDate))->format('Y');

        // Check if employee has balance ledger
        $balance = $this->getBalance($employeeId, $leaveTypeId, $year);
        if (!$balance) {
            return ['success' => false, 'message' => 'No leave quota allocated for this leave category in ' . $year . '.'];
        }

        if ($balance['remaining_days'] < $days) {
            return [
                'success' => false, 
                'message' => "Insufficient leave balance. Requested {$days} day(s), but only {$balance['remaining_days']} day(s) remain."
            ];
        }

        // Check for overlapping leaves
        if ($this->hasOverlappingLeave($employeeId, $startDate, $endDate)) {
            return ['success' => false, 'message' => 'You already have an existing pending or approved leave request covering these dates.'];
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("INSERT INTO `leave_requests` 
                (`employee_id`, `leave_type_id`, `start_date`, `end_date`, `total_days`, `reason`, `status`, `created_at`) 
                VALUES (:empId, :typeId, :start, :end, :days, :reason, 'pending', NOW())");
            
            $stmt->execute([
                ':empId' => $employeeId,
                ':typeId' => $leaveTypeId,
                ':start' => $startDate,
                ':end' => $endDate,
                ':days' => $days,
                ':reason' => $reason,
            ]);

            $requestId = (int)$this->db->lastInsertId();

            // Fetch employee and user details for notification
            $empStmt = $this->db->prepare("SELECT e.first_name, e.last_name, e.employee_code, u.id as user_id 
                                           FROM `employees` e 
                                           LEFT JOIN `users` u ON u.employee_id = e.id 
                                           WHERE e.id = :id");
            $empStmt->execute([':id' => $employeeId]);
            $emp = $empStmt->fetch();
            $empName = $emp ? "{$emp['first_name']} {$emp['last_name']} ({$emp['employee_code']})" : "Employee #{$employeeId}";

            // Notify Admin
            $adminStmt = $this->db->query("SELECT id FROM `users` WHERE `role` = 'admin'");
            $admins = $adminStmt->fetchAll();
            $notifStmt = $this->db->prepare("INSERT INTO `notifications` (`user_id`, `title`, `message`, `type`, `created_at`) VALUES (:uid, :title, :msg, 'leave', NOW())");
            foreach ($admins as $admin) {
                $notifStmt->execute([
                    ':uid' => $admin['id'],
                    ':title' => 'New Leave Application',
                    ':msg' => "{$empName} has requested {$days} day(s) of leave ({$startDate} to {$endDate}).",
                ]);
            }

            // Also create a confirmation notification for the employee
            if (!empty($emp['user_id'])) {
                $notifStmt->execute([
                    ':uid' => $emp['user_id'],
                    ':title' => 'Leave Request Submitted',
                    ':msg' => "Your leave request for {$days} day(s) ({$startDate} to {$endDate}) is submitted and pending review.",
                ]);
            }

            // Log activity
            $userId = $emp['user_id'] ?? null;
            ActivityLogger::log('Leave Application', "{$empName} applied for {$days} day(s) of leave.", $userId ? (int)$userId : null);

            $this->db->commit();
            return ['success' => true, 'message' => "Leave application submitted successfully for {$days} day(s).", 'request_id' => $requestId];

        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Apply leave failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred while processing leave application.'];
        }
    }

    public function approveLeave(int $requestId, int $adminUserId): array
    {
        try {
            $this->db->beginTransaction();

            // Lock request record for update (pessimistic lock on MySQL, omitted on SQLite)
            $stmt = $this->db->prepare("SELECT lr.*, lt.name as leave_type_name, e.first_name, e.last_name, e.user_id 
                                        FROM `leave_requests` lr 
                                        JOIN `leave_types` lt ON lt.id = lr.leave_type_id 
                                        JOIN `employees` e ON e.id = lr.employee_id 
                                        WHERE lr.id = :id" . $this->forUpdate);
            $stmt->execute([':id' => $requestId]);
            $request = $stmt->fetch();

            if (!$request) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Leave request not found.'];
            }

            if ($request['status'] !== 'pending') {
                $this->db->rollBack();
                return ['success' => false, 'message' => "Request cannot be approved because its current status is '{$request['status']}'."];
            }

            $year = (int)(new DateTime($request['start_date']))->format('Y');

            // Lock and fetch leave balance
            $balStmt = $this->db->prepare("SELECT * FROM `leave_balances` 
                                           WHERE `employee_id` = :empId 
                                           AND `leave_type_id` = :typeId 
                                           AND `year` = :year" . $this->forUpdate);
            $balStmt->execute([
                ':empId' => $request['employee_id'],
                ':typeId' => $request['leave_type_id'],
                ':year' => $year,
            ]);
            $balance = $balStmt->fetch();

            if (!$balance) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Leave balance record not found for employee.'];
            }

            $totalDays = (int)$request['total_days'];
            if ($balance['remaining_days'] < $totalDays) {
                $this->db->rollBack();
                return ['success' => false, 'message' => "Insufficient remaining days ({$balance['remaining_days']} remaining, {$totalDays} requested)."];
            }

            // 1. Update leave request status
            $updReq = $this->db->prepare("UPDATE `leave_requests` 
                                          SET `status` = 'approved', 
                                              `approved_by` = :adminId, 
                                              `approved_at` = NOW() 
                                          WHERE `id` = :id");
            $updReq->execute([
                ':adminId' => $adminUserId,
                ':id' => $requestId,
            ]);

            // 2. Safely deduct from leave balance
            $updBal = $this->db->prepare("UPDATE `leave_balances` 
                                          SET `used_days` = `used_days` + :days, 
                                              `remaining_days` = `remaining_days` - :days 
                                          WHERE `id` = :balId");
            $updBal->execute([
                ':days' => $totalDays,
                ':balId' => $balance['id'],
            ]);

            // 3. Create notification for employee
            if (!empty($request['user_id'])) {
                $notif = $this->db->prepare("INSERT INTO `notifications` (`user_id`, `title`, `message`, `type`, `created_at`) 
                                            VALUES (:uid, :title, :msg, 'leave', NOW())");
                $notif->execute([
                    ':uid' => $request['user_id'],
                    ':title' => 'Leave Request Approved',
                    ':msg' => "Your {$request['leave_type_name']} request from {$request['start_date']} to {$request['end_date']} ({$totalDays} days) has been APPROVED.",
                ]);
            }

            // 4. Log system activity
            ActivityLogger::log(
                'Leave Approved', 
                "Approved leave request #{$requestId} for {$request['first_name']} {$request['last_name']} ({$totalDays} days).", 
                $adminUserId
            );

            $this->db->commit();
            return ['success' => true, 'message' => "Leave request #{$requestId} approved successfully."];

        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Approve leave error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database transaction failed: ' . $e->getMessage()];
        }
    }

    public function rejectLeave(int $requestId, int $adminUserId, string $reason): array
    {
        if (trim($reason) === '') {
            return ['success' => false, 'message' => 'Rejection reason is required.'];
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT lr.*, lt.name as leave_type_name, e.first_name, e.last_name, e.user_id 
                                        FROM `leave_requests` lr 
                                        JOIN `leave_types` lt ON lt.id = lr.leave_type_id 
                                        JOIN `employees` e ON e.id = lr.employee_id 
                                        WHERE lr.id = :id" . $this->forUpdate);
            $stmt->execute([':id' => $requestId]);
            $request = $stmt->fetch();

            if (!$request) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Leave request not found.'];
            }

            if ($request['status'] !== 'pending') {
                $this->db->rollBack();
                return ['success' => false, 'message' => "Request cannot be rejected because its status is '{$request['status']}'."];
            }

            // Update status and store rejection reason; Leave balance is NOT touched
            $upd = $this->db->prepare("UPDATE `leave_requests` 
                                       SET `status` = 'rejected', 
                                           `approved_by` = :adminId, 
                                           `approved_at` = NOW(), 
                                           `rejection_reason` = :reason 
                                       WHERE `id` = :id");
            $upd->execute([
                ':adminId' => $adminUserId,
                ':reason' => $reason,
                ':id' => $requestId,
            ]);

            // Notify employee
            if (!empty($request['user_id'])) {
                $notif = $this->db->prepare("INSERT INTO `notifications` (`user_id`, `title`, `message`, `type`, `created_at`) 
                                            VALUES (:uid, :title, :msg, 'leave', NOW())");
                $notif->execute([
                    ':uid' => $request['user_id'],
                    ':title' => 'Leave Request Rejected',
                    ':msg' => "Your {$request['leave_type_name']} request from {$request['start_date']} to {$request['end_date']} was REJECTED: " . $reason,
                ]);
            }

            // Log activity
            ActivityLogger::log(
                'Leave Rejected', 
                "Rejected leave request #{$requestId} for {$request['first_name']} {$request['last_name']}. Reason: {$reason}", 
                $adminUserId
            );

            $this->db->commit();
            return ['success' => true, 'message' => "Leave request #{$requestId} rejected successfully."];

        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Reject leave error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database transaction failed: ' . $e->getMessage()];
        }
    }

    public function cancelLeave(int $requestId, int $employeeId): array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM `leave_requests` WHERE `id` = :id AND `employee_id` = :empId");
            $stmt->execute([':id' => $requestId, ':empId' => $employeeId]);
            $request = $stmt->fetch();

            if (!$request) {
                return ['success' => false, 'message' => 'Leave request not found or not owned by you.'];
            }

            if ($request['status'] !== 'pending') {
                return ['success' => false, 'message' => 'Only pending leave requests can be cancelled.'];
            }

            $upd = $this->db->prepare("UPDATE `leave_requests` SET `status` = 'cancelled' WHERE `id` = :id");
            $upd->execute([':id' => $requestId]);

            ActivityLogger::log('Leave Cancelled', "Employee cancelled pending leave request #{$requestId}.");

            return ['success' => true, 'message' => 'Leave request cancelled successfully.'];
        } catch (Throwable $e) {
            return ['success' => false, 'message' => 'Error cancelling leave request.'];
        }
    }
}
