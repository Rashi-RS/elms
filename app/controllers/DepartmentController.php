<?php
declare(strict_types=1);

namespace App\controllers;

use App\helpers\Session;
use App\helpers\Validator;
use App\models\Department;
use App\services\ActivityLogger;

class DepartmentController extends Controller
{
    private Department $departmentModel;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->departmentModel = new Department();
    }

    public function index(): void
    {
        $departments = $this->departmentModel->getAllWithCounts();

        $this->render('admin/departments/index', [
            'title' => 'Departments Management — ELMS',
            'departments' => $departments,
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();

        $name = trim((string)$this->request->input('name', ''));
        $description = trim((string)$this->request->input('description', ''));

        $validator = new Validator([
            'name' => $name,
        ], [
            'name' => 'required|min:2|max:100|unique:departments,name',
        ]);

        if ($validator->fails()) {
            $this->redirect('/admin/departments', 'error', $validator->firstError('name') ?? 'Invalid department details.');
        }

        $this->departmentModel->create([
            'name' => $name,
            'description' => $description,
            'status' => 'active',
        ]);

        ActivityLogger::log('Department Created', "Created department '{$name}'.");

        $this->redirect('/admin/departments', 'success', "Department '{$name}' created successfully.");
    }

    public function update(array $params): void
    {
        $this->validateCsrf();

        $id = (int)($params['id'] ?? 0);
        $dept = $this->departmentModel->find($id);

        if (!$dept) {
            $this->redirect('/admin/departments', 'error', 'Department not found.');
        }

        $name = trim((string)$this->request->input('name', ''));
        $description = trim((string)$this->request->input('description', ''));
        $status = (string)$this->request->input('status', 'active');

        $validator = new Validator([
            'name' => $name,
            'status' => $status,
        ], [
            'name' => "required|min:2|max:100|unique:departments,name,{$id}",
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            $this->redirect('/admin/departments', 'error', $validator->firstError('name') ?? 'Validation error.');
        }

        // Prevent deactivating if active employees belong to it
        if ($status === 'inactive' && $this->departmentModel->hasEmployees($id)) {
            $this->redirect('/admin/departments', 'error', "Cannot deactivate '{$name}' because active employees are currently assigned to it.");
        }

        $this->departmentModel->update($id, [
            'name' => $name,
            'description' => $description,
            'status' => $status,
        ]);

        ActivityLogger::log('Department Updated', "Updated department '{$name}' (ID: {$id}).");

        $this->redirect('/admin/departments', 'success', "Department '{$name}' updated successfully.");
    }
}
