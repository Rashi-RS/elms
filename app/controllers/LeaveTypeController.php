<?php
declare(strict_types=1);

namespace App\controllers;

use App\helpers\Session;
use App\helpers\Validator;
use App\models\LeaveType;
use App\services\ActivityLogger;

class LeaveTypeController extends Controller
{
    private LeaveType $leaveTypeModel;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->leaveTypeModel = new LeaveType();
    }

    public function index(): void
    {
        $leaveTypes = $this->leaveTypeModel->all('name ASC');

        $this->render('admin/leave_types/index', [
            'title' => 'Leave Policies & Types — ELMS',
            'leaveTypes' => $leaveTypes,
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();

        $name = trim((string)$this->request->input('name', ''));
        $description = trim((string)$this->request->input('description', ''));
        $limit = (int)$this->request->input('annual_limit', 12);

        $validator = new Validator([
            'name' => $name,
            'annual_limit' => $limit,
        ], [
            'name' => 'required|min:2|max:100|unique:leave_types,name',
            'annual_limit' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $this->redirect('/admin/leave-types', 'error', $validator->firstError('name') ?? 'Invalid leave type input.');
        }

        $this->leaveTypeModel->create([
            'name' => $name,
            'description' => $description,
            'annual_limit' => $limit,
            'status' => 'active',
        ]);

        ActivityLogger::log('Leave Type Created', "Configured new leave category: '{$name}' with {$limit} annual days.");

        $this->redirect('/admin/leave-types', 'success', "Leave policy '{$name}' created successfully.");
    }

    public function update(array $params): void
    {
        $this->validateCsrf();

        $id = (int)($params['id'] ?? 0);
        $leaveType = $this->leaveTypeModel->find($id);

        if (!$leaveType) {
            $this->redirect('/admin/leave-types', 'error', 'Leave type not found.');
        }

        $name = trim((string)$this->request->input('name', ''));
        $description = trim((string)$this->request->input('description', ''));
        $limit = (int)$this->request->input('annual_limit', 12);
        $status = (string)$this->request->input('status', 'active');

        $validator = new Validator([
            'name' => $name,
            'annual_limit' => $limit,
            'status' => $status,
        ], [
            'name' => "required|min:2|max:100|unique:leave_types,name,{$id}",
            'annual_limit' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            $this->redirect('/admin/leave-types', 'error', $validator->firstError('name') ?? 'Validation failed.');
        }

        $this->leaveTypeModel->update($id, [
            'name' => $name,
            'description' => $description,
            'annual_limit' => $limit,
            'status' => $status,
        ]);

        ActivityLogger::log('Leave Type Updated', "Updated leave category '{$name}' (Annual limit: {$limit}).");

        $this->redirect('/admin/leave-types', 'success', "Leave policy '{$name}' updated successfully.");
    }
}
