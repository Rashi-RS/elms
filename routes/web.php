<?php
declare(strict_types=1);

/**
 * ELMS - Central Web & API Route Manifest
 */

use App\controllers\ActivityLogController;
use App\controllers\AdminDashboardController;
use App\controllers\ApiController;
use App\controllers\AttendanceController;
use App\controllers\AuthController;
use App\controllers\DepartmentController;
use App\controllers\EmployeeController;
use App\controllers\EmployeeDashboardController;
use App\controllers\LeaveController;
use App\controllers\LeaveTypeController;
use App\controllers\NotificationController;
use App\controllers\ProfileController;
use App\controllers\ReportController;
use App\core\Router;
use App\helpers\Session;
use App\middleware\AdminMiddleware;
use App\middleware\AuthMiddleware;
use App\middleware\EmployeeMiddleware;
use App\middleware\GuestMiddleware;

/** @var Router $router */

// ----------------------------------------------------------------------------
// ROOT LANDING & AUTHENTICATION
// ----------------------------------------------------------------------------
$router->get('/', function ($params, $request, $response) {
    if (Session::isLoggedIn()) {
        if (Session::isAdmin()) {
            $response->redirect('/admin/dashboard');
        } else {
            $response->redirect('/employee/dashboard');
        }
    }
    $response->redirect('/login');
});

$router->get('/login', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/forgot-password', [AuthController::class, 'showForgotPassword'], [GuestMiddleware::class]);
$router->post('/forgot-password', [AuthController::class, 'sendResetLink']);

// ----------------------------------------------------------------------------
// ADMIN MANAGEMENT PORTAL (Strictly AdminMiddleware)
// ----------------------------------------------------------------------------
$router->get('/admin/dashboard', [AdminDashboardController::class, 'index'], [AdminMiddleware::class]);

// Employee Management
$router->get('/admin/employees', [EmployeeController::class, 'index'], [AdminMiddleware::class]);
$router->get('/admin/employees/create', [EmployeeController::class, 'create'], [AdminMiddleware::class]);
$router->post('/admin/employees', [EmployeeController::class, 'store'], [AdminMiddleware::class]);
$router->get('/admin/employees/{id}', [EmployeeController::class, 'show'], [AdminMiddleware::class]);
$router->get('/admin/employees/{id}/edit', [EmployeeController::class, 'edit'], [AdminMiddleware::class]);
$router->post('/admin/employees/{id}/edit', [EmployeeController::class, 'update'], [AdminMiddleware::class]);
$router->post('/admin/employees/{id}/toggle-status', [EmployeeController::class, 'toggleStatus'], [AdminMiddleware::class]);
$router->post('/admin/employees/{id}/delete', [EmployeeController::class, 'delete'], [AdminMiddleware::class]);

// Department Management
$router->get('/admin/departments', [DepartmentController::class, 'index'], [AdminMiddleware::class]);
$router->post('/admin/departments', [DepartmentController::class, 'store'], [AdminMiddleware::class]);
$router->post('/admin/departments/{id}/edit', [DepartmentController::class, 'update'], [AdminMiddleware::class]);

// Leave Policy & Type Management
$router->get('/admin/leave-types', [LeaveTypeController::class, 'index'], [AdminMiddleware::class]);
$router->post('/admin/leave-types', [LeaveTypeController::class, 'store'], [AdminMiddleware::class]);
$router->post('/admin/leave-types/{id}/edit', [LeaveTypeController::class, 'update'], [AdminMiddleware::class]);

// Leave Approvals Workflow
$router->get('/admin/leaves', [LeaveController::class, 'adminIndex'], [AdminMiddleware::class]);
$router->get('/admin/leaves/{id}', [LeaveController::class, 'adminShow'], [AdminMiddleware::class]);
$router->post('/admin/leaves/{id}/approve', [LeaveController::class, 'approve'], [AdminMiddleware::class]);
$router->post('/admin/leaves/{id}/reject', [LeaveController::class, 'reject'], [AdminMiddleware::class]);

// Attendance Management
$router->get('/admin/attendance', [AttendanceController::class, 'adminIndex'], [AdminMiddleware::class]);
$router->post('/admin/attendance/mark', [AttendanceController::class, 'mark'], [AdminMiddleware::class]);

// Reports & CSV Exports
$router->get('/admin/reports', [ReportController::class, 'index'], [AdminMiddleware::class]);
$router->get('/admin/reports/attendance', [ReportController::class, 'attendance'], [AdminMiddleware::class]);
$router->get('/admin/reports/leaves', [ReportController::class, 'leaves'], [AdminMiddleware::class]);
$router->get('/admin/reports/employees', [ReportController::class, 'employees'], [AdminMiddleware::class]);

// Audit Activity Logs
$router->get('/admin/activity-logs', [ActivityLogController::class, 'index'], [AdminMiddleware::class]);

// ----------------------------------------------------------------------------
// EMPLOYEE SELF-SERVICE PORTAL (Strictly EmployeeMiddleware)
// ----------------------------------------------------------------------------
$router->get('/employee/dashboard', [EmployeeDashboardController::class, 'index'], [EmployeeMiddleware::class]);

// Profile Management
$router->get('/employee/profile', [ProfileController::class, 'show'], [EmployeeMiddleware::class]);
$router->post('/employee/profile', [ProfileController::class, 'update'], [EmployeeMiddleware::class]);
$router->post('/employee/change-password', [ProfileController::class, 'changePassword'], [EmployeeMiddleware::class]);

// Leave Applications & History
$router->get('/employee/apply-leave', [LeaveController::class, 'applyForm'], [EmployeeMiddleware::class]);
$router->post('/employee/apply-leave', [LeaveController::class, 'apply'], [EmployeeMiddleware::class]);
$router->get('/employee/my-leaves', [LeaveController::class, 'myLeaves'], [EmployeeMiddleware::class]);
$router->post('/employee/leaves/{id}/cancel', [LeaveController::class, 'cancel'], [EmployeeMiddleware::class]);

// Attendance Self-Service & Clocking
$router->get('/employee/my-attendance', [AttendanceController::class, 'myAttendance'], [EmployeeMiddleware::class]);
$router->post('/employee/clock', [AttendanceController::class, 'clockAction'], [EmployeeMiddleware::class]);

// ----------------------------------------------------------------------------
// NOTIFICATIONS (Common to authenticated users)
// ----------------------------------------------------------------------------
$router->get('/notifications', [NotificationController::class, 'index'], [AuthMiddleware::class]);
$router->post('/notifications/{id}/read', [NotificationController::class, 'markRead'], [AuthMiddleware::class]);
$router->post('/notifications/read-all', [NotificationController::class, 'markAllRead'], [AuthMiddleware::class]);
$router->get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'], [AuthMiddleware::class]);

// ----------------------------------------------------------------------------
// RESTFUL API ENDPOINTS (Supports JSON requests with standard HTTP status codes)
// ----------------------------------------------------------------------------
$router->get('/api/employees', [ApiController::class, 'getEmployees']);
$router->get('/api/employees/{id}', [ApiController::class, 'getEmployee']);
$router->post('/api/employees', [ApiController::class, 'createEmployee']);
$router->put('/api/employees/{id}', [ApiController::class, 'updateEmployee']);
$router->delete('/api/employees/{id}', [ApiController::class, 'deleteEmployee']);

$router->get('/api/leaves', [ApiController::class, 'getLeaves']);
$router->post('/api/leaves', [ApiController::class, 'applyLeave']);
$router->put('/api/leaves/{id}', [ApiController::class, 'updateLeave']);
$router->get('/api/attendance/today', [ApiController::class, 'getTodayAttendance']);
