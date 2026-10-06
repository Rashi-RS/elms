<?php
declare(strict_types=1);

namespace App\controllers;

use App\helpers\Session;
use App\helpers\Validator;
use App\models\Attendance;
use App\models\Employee;
use App\models\LeaveRequest;
use App\services\LeaveService;

class ApiController extends Controller
{
    private Employee $employeeModel;
    private LeaveRequest $leaveModel;
    private LeaveService $leaveService;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->employeeModel = new Employee();
        $this->leaveModel = new LeaveRequest();
        $this->leaveService = new LeaveService();
    }

    private function requireAuth(): void
    {
        if (!Session::isLoggedIn()) {
            $this->json(['success' => false, 'message' => 'Unauthorized: Please authenticate first.'], 401);
        }
    }

    private function requireAdmin(): void
    {
        $this->requireAuth();
        if (!Session::isAdmin()) {
            $this->json(['success' => false, 'message' => 'Forbidden: Administrator privileges required.'], 403);
        }
    }

    public function getEmployees(): void
    {
        $this->requireAdmin();

        $search = trim((string)$this->request->input('search', ''));
        $deptId = $this->request->input('department_id') !== null ? (int)$this->request->input('department_id') : null;
        $status = $this->request->input('status', null);
        $page = (int)$this->request->input('page', 1);
        $perPage = (int)$this->request->input('per_page', 10);

        $result = $this->employeeModel->getAllWithDetails($search, $deptId, $status, $page, $perPage);
        $this->json(['success' => true, 'payload' => $result], 200);
    }

    public function getEmployee(array $params): void
    {
        $this->requireAuth();

        $id = (int)($params['id'] ?? 0);
        $employee = $this->employeeModel->getDetails($id);

        if (!$employee) {
            $this->json(['success' => false, 'message' => 'Employee not found.'], 404);
        }

        // Employees can only view their own record unless admin
        if (!Session::isAdmin() && Session::employeeId() !== $id) {
            $this->json(['success' => false, 'message' => 'Forbidden: You cannot view other employee details.'], 403);
        }

        $this->json(['success' => true, 'payload' => $employee], 200);
    }

    public function createEmployee(): void
    {
        $this->requireAdmin();

        $data = $this->request->all();
        $validator = new Validator($data, [
            'first_name' => 'required|min:2|max:100',
            'last_name' => 'required|min:2|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'department_id' => 'required|numeric',
            'designation' => 'required|min:2|max:100',
            'joining_date' => 'required|date',
            'salary' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $this->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $code = $this->employeeModel->generateEmployeeCode();
        $employeeData = [
            'employee_code' => $code,
            'first_name' => trim((string)$data['first_name']),
            'last_name' => trim((string)$data['last_name']),
            'phone' => trim((string)($data['phone'] ?? '')),
            'gender' => $data['gender'] ?? 'male',
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'address' => trim((string)($data['address'] ?? '')),
            'department_id' => (int)$data['department_id'],
            'designation' => trim((string)$data['designation']),
            'joining_date' => $data['joining_date'],
            'salary' => (float)$data['salary'],
            'status' => 'active',
        ];

        $res = $this->employeeModel->createWithUserAndBalances($employeeData, $data['email'], $data['password']);

        if ($res['success']) {
            $this->json([
                'success' => true,
                'message' => 'Employee created successfully',
                'payload' => $res,
            ], 201);
        } else {
            $this->json(['success' => false, 'message' => $res['message']], 500);
        }
    }

    public function updateEmployee(array $params): void
    {
        $this->requireAdmin();

        $id = (int)($params['id'] ?? 0);
        $employee = $this->employeeModel->find($id);

        if (!$employee) {
            $this->json(['success' => false, 'message' => 'Employee not found.'], 404);
        }

        $data = $this->request->all();
        $allowed = ['first_name', 'last_name', 'phone', 'address', 'designation', 'salary', 'status', 'department_id'];
        $updateData = [];

        foreach ($allowed as $f) {
            if (isset($data[$f])) {
                $updateData[$f] = $data[$f];
            }
        }

        if (empty($updateData)) {
            $this->json(['success' => false, 'message' => 'No fields to update.'], 400);
        }

        $this->employeeModel->update($id, $updateData);
        $this->json(['success' => true, 'message' => 'Employee updated successfully.'], 200);
    }

    public function deleteEmployee(array $params): void
    {
        $this->requireAdmin();

        $id = (int)($params['id'] ?? 0);
        $employee = $this->employeeModel->find($id);

        if (!$employee) {
            $this->json(['success' => false, 'message' => 'Employee not found.'], 404);
        }

        // Soft delete / deactivate
        $this->employeeModel->update($id, ['status' => 'inactive']);
        $this->json(['success' => true, 'message' => 'Employee deactivated successfully.'], 200);
    }

    public function getLeaves(): void
    {
        $this->requireAuth();

        $filters = [];
        if (!Session::isAdmin()) {
            $filters['employee_id'] = Session::employeeId();
        } elseif ($this->request->input('employee_id')) {
            $filters['employee_id'] = (int)$this->request->input('employee_id');
        }

        if ($this->request->input('status')) {
            $filters['status'] = (string)$this->request->input('status');
        }

        $page = (int)$this->request->input('page', 1);
        $result = $this->leaveModel->getAllWithDetails($filters, $page, 15);

        $this->json(['success' => true, 'payload' => $result], 200);
    }

    public function applyLeave(): void
    {
        $this->requireAuth();

        $employeeId = Session::employeeId();
        if (!$employeeId) {
            $this->json(['success' => false, 'message' => 'Only registered employees can submit leave requests.'], 403);
        }

        $data = $this->request->all();
        $validator = new Validator($data, [
            'leave_type_id' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|min:5',
        ]);

        if ($validator->fails()) {
            $this->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $res = $this->leaveService->applyLeave(
            $employeeId,
            (int)$data['leave_type_id'],
            $data['start_date'],
            $data['end_date'],
            trim((string)$data['reason'])
        );

        $statusCode = $res['success'] ? 201 : 422;
        $this->json($res, $statusCode);
    }

    public function updateLeave(array $params): void
    {
        $this->requireAdmin();

        $id = (int)($params['id'] ?? 0);
        $action = (string)$this->request->input('action', '');
        $adminId = Session::userId();

        if ($action === 'approve') {
            $res = $this->leaveService->approveLeave($id, $adminId);
            $this->json($res, $res['success'] ? 200 : 422);
        } elseif ($action === 'reject') {
            $reason = trim((string)$this->request->input('rejection_reason', ''));
            if ($reason === '') {
                $this->json(['success' => false, 'message' => 'Rejection reason is required.'], 422);
            }
            $res = $this->leaveService->rejectLeave($id, $adminId, $reason);
            $this->json($res, $res['success'] ? 200 : 422);
        } else {
            $this->json(['success' => false, 'message' => "Invalid action. Supported actions: 'approve', 'reject'."], 400);
        }
    }

    public function getTodayAttendance(): void
    {
        $this->requireAdmin();
        $attendanceModel = new Attendance();
        $summary = $attendanceModel->getTodaySummary();
        $this->json(['success' => true, 'payload' => $summary], 200);
    }
}
