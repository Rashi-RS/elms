<?php
declare(strict_types=1);

namespace App\controllers;

use App\helpers\Session;
use App\models\Attendance;
use App\models\Employee;
use App\models\LeaveBalance;
use App\models\LeaveRequest;
use App\models\Notification;

class EmployeeDashboardController extends Controller
{
    public function index(): void
    {
        $userId = Session::userId();
        $employeeModel = new Employee();
        $employee = $employeeModel->getByUserId($userId);

        if (!$employee) {
            $this->redirect('/login', 'error', 'Employee record not associated with this account.');
        }

        $employeeId = (int)$employee['id'];
        $currentYear = (int)date('Y');

        $leaveBalanceModel = new LeaveBalance();
        $leaveRequestModel = new LeaveRequest();
        $attendanceModel = new Attendance();
        $notificationModel = new Notification();

        // 1. Leave Balances for current year
        $balances = $leaveBalanceModel->getEmployeeBalances($employeeId, $currentYear);

        // 2. Today's Attendance
        $todayAttendance = $attendanceModel->getTodayRecordForEmployee($employeeId);

        // 3. Employee's Recent Leave Requests
        $recentLeaves = $leaveRequestModel->getAllWithDetails(['employee_id' => $employeeId], 1, 5)['data'];

        // 4. Monthly Attendance Stats (Current month)
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');
        $monthlyStats = $attendanceModel->getEmployeeStats($employeeId, $startOfMonth, $endOfMonth);

        // 5. Recent Notifications
        $notifications = $notificationModel->getAllForUser($userId, 5);
        $unreadNotifCount = $notificationModel->getUnreadCount($userId);

        $this->render('employee/dashboard', [
            'title' => 'Employee Portal — ELMS',
            'employee' => $employee,
            'balances' => $balances,
            'todayAttendance' => $todayAttendance,
            'recentLeaves' => $recentLeaves,
            'monthlyStats' => $monthlyStats,
            'notifications' => $notifications,
            'unreadNotifCount' => $unreadNotifCount,
        ]);
    }
}
