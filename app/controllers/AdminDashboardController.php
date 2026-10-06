<?php
declare(strict_types=1);

namespace App\controllers;

use App\models\ActivityLog;
use App\models\Attendance;
use App\models\Department;
use App\models\Employee;
use App\models\LeaveRequest;

class AdminDashboardController extends Controller
{
    public function index(): void
    {
        $employeeModel = new Employee();
        $departmentModel = new Department();
        $leaveRequestModel = new LeaveRequest();
        $attendanceModel = new Attendance();
        $activityLogModel = new ActivityLog();

        // Dynamic KPI Statistics from MySQL
        $totalEmployees = $employeeModel->count("status != 'terminated'");
        $activeEmployees = $employeeModel->count("status = 'active'");
        $totalDepartments = $departmentModel->count("status = 'active'");

        $todayAttendance = $attendanceModel->getTodaySummary();
        $leaveCounts = $leaveRequestModel->getStatusCounts();

        // Chart Data 1: Department Distribution
        $departments = $departmentModel->getAllWithCounts();
        $deptLabels = [];
        $deptCounts = [];
        foreach ($departments as $d) {
            $deptLabels[] = $d['name'];
            $deptCounts[] = (int)$d['employee_count'];
        }

        // Chart Data 2: Attendance Distribution Today
        $attendanceChartData = [
            'labels' => ['Present', 'Absent', 'Half Day', 'On Leave'],
            'data' => [
                $todayAttendance['present'],
                $todayAttendance['absent'],
                $todayAttendance['half_day'],
                $todayAttendance['leave'],
            ],
        ];

        // Chart Data 3: Leave Distribution
        $leaveChartData = [
            'labels' => ['Approved', 'Pending', 'Rejected'],
            'data' => [
                $leaveCounts['approved'],
                $leaveCounts['pending'],
                $leaveCounts['rejected'],
            ],
        ];

        // Recent Activity Logs
        $recentActivities = $activityLogModel->getRecent(8);

        // Recent Pending Leaves (for quick action)
        $pendingLeaves = $leaveRequestModel->getAllWithDetails(['status' => 'pending'], 1, 5)['data'];

        $this->render('admin/dashboard', [
            'title' => 'Admin Dashboard — ELMS',
            'stats' => [
                'total_employees' => $totalEmployees,
                'active_employees' => $activeEmployees,
                'present_today' => $todayAttendance['present'],
                'absent_today' => $todayAttendance['absent'],
                'half_day_today' => $todayAttendance['half_day'],
                'leave_today' => $todayAttendance['leave'],
                'pending_leaves' => $leaveCounts['pending'],
                'approved_leaves' => $leaveCounts['approved'],
                'rejected_leaves' => $leaveCounts['rejected'],
                'total_departments' => $totalDepartments,
            ],
            'deptChart' => [
                'labels' => $deptLabels,
                'counts' => $deptCounts,
            ],
            'attendanceChart' => $attendanceChartData,
            'leaveChart' => $leaveChartData,
            'recentActivities' => $recentActivities,
            'pendingLeaves' => $pendingLeaves,
        ]);
    }
}
