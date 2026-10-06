<?php
declare(strict_types=1);

namespace App\controllers;

use App\helpers\Session;
use App\helpers\Validator;
use App\models\Attendance;
use App\models\Department;
use App\models\Employee;
use App\models\LeaveBalance;
use App\models\LeaveRequest;
use App\models\User;
use App\services\ActivityLogger;
use App\services\FileUploader;

class EmployeeController extends Controller
{
    private Employee $employeeModel;
    private Department $departmentModel;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->employeeModel = new Employee();
        $this->departmentModel = new Department();
    }

    public function index(): void
    {
        $search = trim((string)$this->request->input('search', ''));
        $deptId = $this->request->input('department_id') !== '' ? (int)$this->request->input('department_id') : null;
        $status = $this->request->input('status') !== '' ? (string)$this->request->input('status') : null;
        $page = (int)$this->request->input('page', 1);

        $employees = $this->employeeModel->getAllWithDetails($search, $deptId, $status, $page, 10);
        $departments = $this->departmentModel->all('name ASC');

        $empIds = array_column($employees['data'], 'id');
        $employeeBalances = [];
        $employeeLeaves = [];
        if (!empty($empIds)) {
            $leaveBalanceModel = new \App\models\LeaveBalance();
            $employeeBalances = $leaveBalanceModel->getBalancesForEmployees($empIds, (int)date('Y'));
            $leaveRequestModel = new \App\models\LeaveRequest();
            $employeeLeaves = $leaveRequestModel->getRecentLeavesForEmployees($empIds, 5);
        }

        $this->render('admin/employees/index', [
            'title' => 'Employee Directory — ELMS',
            'employees' => $employees,
            'departments' => $departments,
            'employeeBalances' => $employeeBalances,
            'employeeLeaves' => $employeeLeaves,
            'filters' => [
                'search' => $search,
                'department_id' => $deptId,
                'status' => $status,
            ],
        ]);
    }

    public function create(): void
    {
        $departments = $this->departmentModel->where('status', 'active', 'name ASC');
        $suggestedCode = $this->employeeModel->generateEmployeeCode();

        $this->render('admin/employees/create', [
            'title' => 'Add New Employee — ELMS',
            'departments' => $departments,
            'suggestedCode' => $suggestedCode,
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();

        $data = $this->request->all();

        $validator = new Validator($data, [
            'first_name' => 'required|min:2|max:100',
            'last_name' => 'required|min:2|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'employee_code' => 'required|unique:employees,employee_code',
            'department_id' => 'required|numeric',
            'designation' => 'required|min:2|max:100',
            'joining_date' => 'required|date',
            'salary' => 'required|numeric',
            'gender' => 'required|in:male,female,other',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode('<br>', $validator->errors()));
            $this->redirect('/admin/employees/create');
        }

        // Handle Avatar Upload
        $avatarPath = null;
        if (!empty($_FILES['profile_image']['name'])) {
            $uploader = new FileUploader();
            $avatarPath = $uploader->uploadAvatar($_FILES['profile_image']);
            if (!$avatarPath && !empty($uploader->getErrors())) {
                Session::flash('error', $uploader->getFirstError());
                $this->redirect('/admin/employees/create');
            }
        }

        $employeeData = [
            'employee_code' => trim((string)$data['employee_code']),
            'first_name' => trim((string)$data['first_name']),
            'last_name' => trim((string)$data['last_name']),
            'phone' => trim((string)($data['phone'] ?? '')),
            'gender' => $data['gender'],
            'date_of_birth' => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
            'address' => trim((string)($data['address'] ?? '')),
            'department_id' => (int)$data['department_id'],
            'designation' => trim((string)$data['designation']),
            'joining_date' => $data['joining_date'],
            'salary' => (float)$data['salary'],
            'profile_image' => $avatarPath,
            'status' => 'active',
        ];

        $res = $this->employeeModel->createWithUserAndBalances(
            $employeeData,
            trim((string)$data['email']),
            (string)$data['password']
        );

        if ($res['success']) {
            $this->redirect('/admin/employees', 'success', "Employee {$employeeData['first_name']} {$employeeData['last_name']} added successfully with login access.");
        } else {
            $this->redirect('/admin/employees/create', 'error', "Error creating employee: " . $res['message']);
        }
    }

    public function show(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        $employee = $this->employeeModel->getDetails($id);

        if (!$employee) {
            $this->redirect('/admin/employees', 'error', 'Employee record not found.');
        }

        $currentYear = (int)date('Y');
        $leaveBalanceModel = new LeaveBalance();
        $balances = $leaveBalanceModel->getEmployeeBalances($id, $currentYear);

        $leaveRequestModel = new LeaveRequest();
        $leaves = $leaveRequestModel->getAllWithDetails(['employee_id' => $id], 1, 10)['data'];

        $attendanceModel = new Attendance();
        $attendance = $attendanceModel->getEmployeeAttendance($id, date('Y-m-01'), date('Y-m-t'));

        $this->render('admin/employees/show', [
            'title' => "{$employee['first_name']} {$employee['last_name']} — Profile",
            'employee' => $employee,
            'balances' => $balances,
            'leaves' => $leaves,
            'attendance' => $attendance,
        ]);
    }

    public function edit(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        $employee = $this->employeeModel->getDetails($id);

        if (!$employee) {
            $this->redirect('/admin/employees', 'error', 'Employee not found.');
        }

        $departments = $this->departmentModel->all('name ASC');

        $this->render('admin/employees/edit', [
            'title' => "Edit Employee — {$employee['first_name']} {$employee['last_name']}",
            'employee' => $employee,
            'departments' => $departments,
        ]);
    }

    public function update(array $params): void
    {
        $this->validateCsrf();

        $id = (int)($params['id'] ?? 0);
        $employee = $this->employeeModel->find($id);

        if (!$employee) {
            $this->redirect('/admin/employees', 'error', 'Employee not found.');
        }

        $data = $this->request->all();

        $validator = new Validator($data, [
            'first_name' => 'required|min:2|max:100',
            'last_name' => 'required|min:2|max:100',
            'email' => "required|email|unique:users,email,{$employee['user_id']}",
            'department_id' => 'required|numeric',
            'designation' => 'required|min:2|max:100',
            'joining_date' => 'required|date',
            'salary' => 'required|numeric',
            'gender' => 'required|in:male,female,other',
            'status' => 'required|in:active,inactive,terminated',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode('<br>', $validator->errors()));
            $this->redirect("/admin/employees/{$id}/edit");
        }

        $avatarPath = $employee['profile_image'];
        if (!empty($_FILES['profile_image']['name'])) {
            $uploader = new FileUploader();
            $newAvatar = $uploader->uploadAvatar($_FILES['profile_image']);
            if ($newAvatar) {
                $avatarPath = $newAvatar;
            }
        }

        $updateData = [
            'first_name' => trim((string)$data['first_name']),
            'last_name' => trim((string)$data['last_name']),
            'phone' => trim((string)($data['phone'] ?? '')),
            'gender' => $data['gender'],
            'date_of_birth' => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
            'address' => trim((string)($data['address'] ?? '')),
            'department_id' => (int)$data['department_id'],
            'designation' => trim((string)$data['designation']),
            'joining_date' => $data['joining_date'],
            'salary' => (float)$data['salary'],
            'profile_image' => $avatarPath,
            'status' => $data['status'],
        ];

        $this->employeeModel->update($id, $updateData);

        // Update email and status on user account
        if (!empty($employee['user_id'])) {
            $userModel = new User();
            $userUpdate = [
                'email' => trim((string)$data['email']),
                'status' => $data['status'] === 'terminated' ? 'suspended' : $data['status'],
            ];
            if (!empty($data['password'])) {
                $userUpdate['password'] = password_hash((string)$data['password'], PASSWORD_DEFAULT);
            }
            $userModel->update((int)$employee['user_id'], $userUpdate);
        }

        ActivityLogger::log('Employee Updated', "Updated employee records for {$updateData['first_name']} {$updateData['last_name']} (EMP-{$id}).");

        $this->redirect("/admin/employees/{$id}", 'success', 'Employee profile updated successfully.');
    }

    public function toggleStatus(array $params): void
    {
        $this->validateCsrf();

        $id = (int)($params['id'] ?? 0);
        $employee = $this->employeeModel->find($id);

        if (!$employee) {
            $this->redirect('/admin/employees', 'error', 'Employee not found.');
        }

        $newStatus = ($employee['status'] === 'active') ? 'inactive' : 'active';
        $this->employeeModel->update($id, ['status' => $newStatus]);

        if (!empty($employee['user_id'])) {
            $userModel = new User();
            $userModel->update((int)$employee['user_id'], ['status' => $newStatus]);
        }

        ActivityLogger::log('Employee Status Toggled', "Changed status of employee #{$id} to {$newStatus}.");

        $this->redirect('/admin/employees', 'success', "Employee status set to {$newStatus}.");
    }

    public function delete(array $params): void
    {
        $this->validateCsrf();

        $id = (int)($params['id'] ?? 0);
        $employee = $this->employeeModel->find($id);

        if (!$employee) {
            $this->redirect('/admin/employees', 'error', 'Employee not found.');
        }

        $fullName = "{$employee['first_name']} {$employee['last_name']}";
        $code = $employee['employee_code'];

        $success = $this->employeeModel->deleteWithRelations($id);

        if ($success) {
            $this->redirect('/admin/employees', 'success', "Employee {$fullName} ({$code}) and all related records have been permanently removed.");
        } else {
            $this->redirect('/admin/employees', 'error', "Failed to remove employee {$fullName}.");
        }
    }
}

