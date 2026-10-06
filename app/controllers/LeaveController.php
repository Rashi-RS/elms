<?php
declare(strict_types=1);

namespace App\controllers;

use App\helpers\Session;
use App\helpers\Validator;
use App\models\Employee;
use App\models\LeaveBalance;
use App\models\LeaveRequest;
use App\models\LeaveType;
use App\services\LeaveService;

class LeaveController extends Controller
{
    private LeaveRequest $leaveRequestModel;
    private LeaveType $leaveTypeModel;
    private LeaveService $leaveService;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->leaveRequestModel = new LeaveRequest();
        $this->leaveTypeModel = new LeaveType();
        $this->leaveService = new LeaveService();
    }

    // ==========================================
    // ADMIN ACTIONS
    // ==========================================

    public function adminIndex(): void
    {
        $status = $this->request->input('status', '');
        $typeId = $this->request->input('leave_type_id', '');
        $startDate = $this->request->input('start_date', '');
        $endDate = $this->request->input('end_date', '');
        $page = (int)$this->request->input('page', 1);

        $filters = [];
        if ($status !== '') $filters['status'] = $status;
        if ($typeId !== '') $filters['leave_type_id'] = (int)$typeId;
        if ($startDate !== '') $filters['start_date'] = $startDate;
        if ($endDate !== '') $filters['end_date'] = $endDate;

        $leaves = $this->leaveRequestModel->getAllWithDetails($filters, $page, 10);
        $leaveTypes = $this->leaveTypeModel->all('name ASC');
        $statusCounts = $this->leaveRequestModel->getStatusCounts();

        $this->render('admin/leaves/index', [
            'title' => 'Leave Management & Approvals — ELMS',
            'leaves' => $leaves,
            'leaveTypes' => $leaveTypes,
            'statusCounts' => $statusCounts,
            'filters' => [
                'status' => $status,
                'leave_type_id' => $typeId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    public function adminShow(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        $request = $this->leaveRequestModel->getDetails($id);

        if (!$request) {
            $this->redirect('/admin/leaves', 'error', 'Leave request not found.');
        }

        $leaveBalanceModel = new LeaveBalance();
        $currentYear = (int)date('Y', strtotime($request['start_date']));
        $balances = $leaveBalanceModel->getEmployeeBalances((int)$request['employee_id'], $currentYear);

        $this->render('admin/leaves/show', [
            'title' => "Leave Request #{$id} Review — ELMS",
            'leave' => $request,
            'balances' => $balances,
        ]);
    }

    public function approve(array $params): void
    {
        $this->validateCsrf();

        $id = (int)($params['id'] ?? 0);
        $adminId = Session::userId();

        $result = $this->leaveService->approveLeave($id, $adminId);

        if ($this->request->isAjax()) {
            $this->json($result, $result['success'] ? 200 : 422);
        }

        $redirectUrl = (string)$this->request->input('redirect_to', "/admin/leaves/{$id}");

        if ($result['success']) {
            $this->redirect($redirectUrl, 'success', $result['message']);
        } else {
            $this->redirect($redirectUrl, 'error', $result['message']);
        }
    }

    public function reject(array $params): void
    {
        $this->validateCsrf();

        $id = (int)($params['id'] ?? 0);
        $adminId = Session::userId();
        $reason = trim((string)$this->request->input('rejection_reason', ''));
        $redirectUrl = (string)$this->request->input('redirect_to', "/admin/leaves/{$id}");

        if ($reason === '') {
            if ($this->request->isAjax()) {
                $this->json(['success' => false, 'message' => 'A rejection reason is required.'], 422);
            }
            $this->redirect($redirectUrl, 'error', 'Please provide a reason when rejecting a leave request.');
        }

        $result = $this->leaveService->rejectLeave($id, $adminId, $reason);

        if ($this->request->isAjax()) {
            $this->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            $this->redirect($redirectUrl, 'success', $result['message']);
        } else {
            $this->redirect($redirectUrl, 'error', $result['message']);
        }
    }

    // ==========================================
    // EMPLOYEE ACTIONS
    // ==========================================

    public function applyForm(): void
    {
        $userId = Session::userId();
        $employeeModel = new Employee();
        $employee = $employeeModel->getByUserId($userId);

        if (!$employee) {
            $this->redirect('/employee/dashboard', 'error', 'Employee record not found.');
        }

        $currentYear = (int)date('Y');
        $leaveBalanceModel = new LeaveBalance();
        $balances = $leaveBalanceModel->getEmployeeBalances((int)$employee['id'], $currentYear);
        $leaveTypes = $this->leaveTypeModel->getActive();

        $this->render('employee/apply_leave', [
            'title' => 'Apply for Leave — ELMS',
            'employee' => $employee,
            'balances' => $balances,
            'leaveTypes' => $leaveTypes,
        ]);
    }

    public function apply(): void
    {
        $this->validateCsrf();

        $userId = Session::userId();
        $employeeModel = new Employee();
        $employee = $employeeModel->getByUserId($userId);

        if (!$employee) {
            $this->redirect('/employee/dashboard', 'error', 'Employee record not found.');
        }

        $employeeId = (int)$employee['id'];
        $data = $this->request->all();

        $validator = new Validator($data, [
            'leave_type_id' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|min:5|max:1000',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode('<br>', $validator->errors()));
            $this->redirect('/employee/apply-leave');
        }

        $result = $this->leaveService->applyLeave(
            $employeeId,
            (int)$data['leave_type_id'],
            $data['start_date'],
            $data['end_date'],
            trim((string)$data['reason'])
        );

        if ($this->request->isAjax()) {
            $this->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            $this->redirect('/employee/my-leaves', 'success', $result['message']);
        } else {
            $this->redirect('/employee/apply-leave', 'error', $result['message']);
        }
    }

    public function myLeaves(): void
    {
        $userId = Session::userId();
        $employeeModel = new Employee();
        $employee = $employeeModel->getByUserId($userId);

        if (!$employee) {
            $this->redirect('/employee/dashboard', 'error', 'Employee record not found.');
        }

        $employeeId = (int)$employee['id'];
        $status = $this->request->input('status', '');
        $page = (int)$this->request->input('page', 1);

        $filters = ['employee_id' => $employeeId];
        if ($status !== '') {
            $filters['status'] = $status;
        }

        $leaves = $this->leaveRequestModel->getAllWithDetails($filters, $page, 10);

        $this->render('employee/my_leaves', [
            'title' => 'My Leave Applications — ELMS',
            'employee' => $employee,
            'leaves' => $leaves,
            'currentStatus' => $status,
        ]);
    }

    public function cancel(array $params): void
    {
        $this->validateCsrf();

        $userId = Session::userId();
        $employeeModel = new Employee();
        $employee = $employeeModel->getByUserId($userId);

        if (!$employee) {
            $this->redirect('/employee/my-leaves', 'error', 'Employee record not found.');
        }

        $id = (int)($params['id'] ?? 0);
        $result = $this->leaveService->cancelLeave($id, (int)$employee['id']);

        if ($this->request->isAjax()) {
            $this->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            $this->redirect('/employee/my-leaves', 'success', $result['message']);
        } else {
            $this->redirect('/employee/my-leaves', 'error', $result['message']);
        }
    }
}
