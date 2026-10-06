<?php
declare(strict_types=1);

namespace App\controllers;

use App\models\Database;
use App\models\Department;
use App\models\Employee;
use App\models\LeaveType;
use PDO;

class ReportController extends Controller
{
    private Department $deptModel;
    private Employee $empModel;
    private LeaveType $leaveTypeModel;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->deptModel = new Department();
        $this->empModel = new Employee();
        $this->leaveTypeModel = new LeaveType();
    }

    public function index(): void
    {
        $this->render('admin/reports/index', [
            'title' => 'Analytics & Reports Hub — ELMS',
        ]);
    }

    public function attendance(): void
    {
        $deptId = $this->request->input('department_id') !== '' ? (int)$this->request->input('department_id') : null;
        $empId = $this->request->input('employee_id') !== '' ? (int)$this->request->input('employee_id') : null;
        $startDate = (string)$this->request->input('start_date', date('Y-m-01'));
        $endDate = (string)$this->request->input('end_date', date('Y-m-d'));
        $status = $this->request->input('status', '');
        $export = $this->request->input('export', '');

        $where = ["a.attendance_date BETWEEN :start AND :end"];
        $params = [':start' => $startDate, ':end' => $endDate];

        if ($deptId) {
            $where[] = "e.department_id = :deptId";
            $params[':deptId'] = $deptId;
        }
        if ($empId) {
            $where[] = "e.id = :empId";
            $params[':empId'] = $empId;
        }
        if ($status !== '') {
            $where[] = "a.status = :status";
            $params[':status'] = $status;
        }

        $whereClause = implode(' AND ', $where);
        $sql = "SELECT a.*, e.employee_code, e.first_name, e.last_name, d.name as department_name 
                FROM `attendance` a 
                JOIN `employees` e ON e.id = a.employee_id 
                LEFT JOIN `departments` d ON d.id = e.department_id 
                WHERE {$whereClause} 
                ORDER BY a.attendance_date DESC, e.first_name ASC";

        $db = Database::pdo();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $records = $stmt->fetchAll();

        if ($export === 'csv') {
            $this->exportCsv('attendance_report_' . date('Ymd_His') . '.csv', [
                'Date', 'Employee Code', 'Employee Name', 'Department', 'Status', 'Check In', 'Check Out', 'Hours', 'Remarks'
            ], array_map(function ($r) {
                return [
                    $r['attendance_date'],
                    $r['employee_code'],
                    $r['first_name'] . ' ' . $r['last_name'],
                    $r['department_name'] ?? 'N/A',
                    strtoupper($r['status']),
                    $r['check_in'] ?? '—',
                    $r['check_out'] ?? '—',
                    $r['working_hours'],
                    $r['remarks'] ?? '',
                ];
            }, $records));
            return;
        }

        $this->render('admin/reports/attendance', [
            'title' => 'Attendance Report — ELMS',
            'records' => $records,
            'departments' => $this->deptModel->all('name ASC'),
            'employees' => $this->empModel->all('first_name ASC'),
            'filters' => [
                'department_id' => $deptId,
                'employee_id' => $empId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status,
            ],
        ]);
    }

    public function leaves(): void
    {
        $empId = $this->request->input('employee_id') !== '' ? (int)$this->request->input('employee_id') : null;
        $typeId = $this->request->input('leave_type_id') !== '' ? (int)$this->request->input('leave_type_id') : null;
        $status = $this->request->input('status', '');
        $startDate = (string)$this->request->input('start_date', '');
        $endDate = (string)$this->request->input('end_date', '');
        $export = $this->request->input('export', '');

        $where = ["1=1"];
        $params = [];

        if ($empId) {
            $where[] = "lr.employee_id = :empId";
            $params[':empId'] = $empId;
        }
        if ($typeId) {
            $where[] = "lr.leave_type_id = :typeId";
            $params[':typeId'] = $typeId;
        }
        if ($status !== '') {
            $where[] = "lr.status = :status";
            $params[':status'] = $status;
        }
        if ($startDate !== '') {
            $where[] = "lr.start_date >= :start";
            $params[':start'] = $startDate;
        }
        if ($endDate !== '') {
            $where[] = "lr.end_date <= :end";
            $params[':end'] = $endDate;
        }

        $whereClause = implode(' AND ', $where);
        $sql = "SELECT lr.*, lt.name as leave_type_name, e.employee_code, e.first_name, e.last_name, d.name as department_name 
                FROM `leave_requests` lr 
                JOIN `employees` e ON e.id = lr.employee_id 
                JOIN `leave_types` lt ON lt.id = lr.leave_type_id 
                LEFT JOIN `departments` d ON d.id = e.department_id 
                WHERE {$whereClause} 
                ORDER BY lr.created_at DESC";

        $db = Database::pdo();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $records = $stmt->fetchAll();

        if ($export === 'csv') {
            $this->exportCsv('leave_report_' . date('Ymd_His') . '.csv', [
                'ID', 'Employee Code', 'Employee Name', 'Department', 'Leave Category', 'Start Date', 'End Date', 'Days', 'Status', 'Reason'
            ], array_map(function ($r) {
                return [
                    $r['id'],
                    $r['employee_code'],
                    $r['first_name'] . ' ' . $r['last_name'],
                    $r['department_name'] ?? 'N/A',
                    $r['leave_type_name'],
                    $r['start_date'],
                    $r['end_date'],
                    $r['total_days'],
                    strtoupper($r['status']),
                    $r['reason'],
                ];
            }, $records));
            return;
        }

        $this->render('admin/reports/leaves', [
            'title' => 'Leave Report — ELMS',
            'records' => $records,
            'leaveTypes' => $this->leaveTypeModel->all('name ASC'),
            'employees' => $this->empModel->all('first_name ASC'),
            'filters' => [
                'employee_id' => $empId,
                'leave_type_id' => $typeId,
                'status' => $status,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    public function employees(): void
    {
        $deptId = $this->request->input('department_id') !== '' ? (int)$this->request->input('department_id') : null;
        $status = $this->request->input('status', '');
        $export = $this->request->input('export', '');

        $where = ["1=1"];
        $params = [];

        if ($deptId) {
            $where[] = "e.department_id = :deptId";
            $params[':deptId'] = $deptId;
        }
        if ($status !== '') {
            $where[] = "e.status = :status";
            $params[':status'] = $status;
        }

        $whereClause = implode(' AND ', $where);
        $sql = "SELECT e.*, d.name as department_name, u.email 
                FROM `employees` e 
                LEFT JOIN `departments` d ON d.id = e.department_id 
                LEFT JOIN `users` u ON u.employee_id = e.id 
                WHERE {$whereClause} 
                ORDER BY e.employee_code ASC";

        $db = Database::pdo();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $records = $stmt->fetchAll();

        if ($export === 'csv') {
            $this->exportCsv('employee_directory_' . date('Ymd_His') . '.csv', [
                'Employee Code', 'Name', 'Email', 'Phone', 'Department', 'Designation', 'Joining Date', 'Salary', 'Status'
            ], array_map(function ($r) {
                return [
                    $r['employee_code'],
                    $r['first_name'] . ' ' . $r['last_name'],
                    $r['email'] ?? '—',
                    $r['phone'] ?? '—',
                    $r['department_name'] ?? 'N/A',
                    $r['designation'],
                    $r['joining_date'],
                    $r['salary'],
                    strtoupper($r['status']),
                ];
            }, $records));
            return;
        }

        $this->render('admin/reports/employees', [
            'title' => 'Employee Directory Report — ELMS',
            'records' => $records,
            'departments' => $this->deptModel->all('name ASC'),
            'filters' => [
                'department_id' => $deptId,
                'status' => $status,
            ],
        ]);
    }

    private function exportCsv(string $filename, array $headers, array $rows): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);

        foreach ($rows as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }
}
