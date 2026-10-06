<?php
declare(strict_types=1);

namespace App\controllers;

use App\helpers\Session;
use App\helpers\Validator;
use App\models\Attendance;
use App\models\Department;
use App\models\Employee;
use App\services\ActivityLogger;

class AttendanceController extends Controller
{
    private Attendance $attendanceModel;
    private Employee $employeeModel;
    private Department $departmentModel;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->attendanceModel = new Attendance();
        $this->employeeModel = new Employee();
        $this->departmentModel = new Department();
    }

    // ==========================================
    // ADMIN ACTIONS
    // ==========================================

    public function adminIndex(): void
    {
        $date = (string)$this->request->input('date', date('Y-m-d'));
        $deptId = $this->request->input('department_id') !== '' ? (int)$this->request->input('department_id') : null;

        $roster = $this->attendanceModel->getRosterForDate($date, $deptId);
        $departments = $this->departmentModel->all('name ASC');

        $this->render('admin/attendance/index', [
            'title' => 'Daily Attendance Roster — ELMS',
            'date' => $date,
            'deptId' => $deptId,
            'roster' => $roster,
            'departments' => $departments,
        ]);
    }

    public function mark(): void
    {
        $this->validateCsrf();

        $data = $this->request->all();

        $validator = new Validator($data, [
            'employee_id' => 'required|numeric',
            'attendance_date' => 'required|date',
            'status' => 'required|in:present,absent,half_day,leave',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode('<br>', $validator->errors()));
            $this->redirect('/admin/attendance?date=' . ($data['attendance_date'] ?? date('Y-m-d')));
        }

        $employeeId = (int)$data['employee_id'];
        $date = $data['attendance_date'];
        $status = $data['status'];
        $checkIn = !empty($data['check_in']) ? $data['check_in'] : null;
        $checkOut = !empty($data['check_out']) ? $data['check_out'] : null;
        $remarks = trim((string)($data['remarks'] ?? ''));

        $res = $this->attendanceModel->markOrUpdate($employeeId, $date, $status, $checkIn, $checkOut, $remarks);

        ActivityLogger::log('Attendance Recorded', "Admin marked attendance for employee #{$employeeId} on {$date} as {$status}.");

        if ($this->request->isAjax()) {
            $this->json(['success' => $res, 'message' => 'Attendance updated successfully.']);
        }

        $this->redirect("/admin/attendance?date={$date}", 'success', 'Attendance record saved.');
    }

    // ==========================================
    // EMPLOYEE ACTIONS
    // ==========================================

    public function myAttendance(): void
    {
        $userId = Session::userId();
        $employee = $this->employeeModel->getByUserId($userId);

        if (!$employee) {
            $this->redirect('/employee/dashboard', 'error', 'Employee record not found.');
        }

        $employeeId = (int)$employee['id'];

        $monthYear = (string)$this->request->input('month', date('Y-m'));
        $startDate = $monthYear . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        $records = $this->attendanceModel->getEmployeeAttendance($employeeId, $startDate, $endDate);
        $stats = $this->attendanceModel->getEmployeeStats($employeeId, $startDate, $endDate);

        $this->render('employee/my_attendance', [
            'title' => 'My Attendance History — ELMS',
            'employee' => $employee,
            'records' => $records,
            'stats' => $stats,
            'selectedMonth' => $monthYear,
        ]);
    }

    public function clockAction(): void
    {
        $this->validateCsrf();

        $userId = Session::userId();
        $employee = $this->employeeModel->getByUserId($userId);

        if (!$employee) {
            $this->redirect('/employee/dashboard', 'error', 'Employee record not found.');
        }

        $employeeId = (int)$employee['id'];
        $action = (string)$this->request->input('action', 'in');
        $today = date('Y-m-d');
        $now = date('H:i:s');

        $record = $this->attendanceModel->getTodayRecordForEmployee($employeeId);

        if ($action === 'reset') {
            $db = \App\models\Database::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM `attendance` WHERE `employee_id` = :empId AND `attendance_date` = :today");
            $stmt->execute([':empId' => $employeeId, ':today' => $today]);
            $this->redirect('/employee/dashboard', 'info', "Today's shift attendance has been reset for testing.");
        }

        if ($action === 'in') {
            if ($record && !empty($record['check_in'])) {
                $formattedTime = date('h:i A', strtotime($record['check_in']));
                $this->redirect('/employee/dashboard', 'info', "You have already checked in today at {$formattedTime}.");
            }
            $this->attendanceModel->markOrUpdate($employeeId, $today, 'present', $now, null, 'Self clock-in');
            ActivityLogger::log('Employee Clock In', "Employee {$employee['first_name']} clocked in at {$now}.", $userId);
            $formattedTime = date('h:i A', strtotime($now));
            $this->redirect('/employee/dashboard', 'success', "Successfully clocked in at {$formattedTime}. Have a productive day!");
        } else {
            // Clock out
            $checkInTime = ($record && !empty($record['check_in'])) ? $record['check_in'] : $now;
            $this->attendanceModel->markOrUpdate($employeeId, $today, 'present', $checkInTime, $now, 'Self clock-out');
            ActivityLogger::log('Employee Clock Out', "Employee {$employee['first_name']} clocked out at {$now}.", $userId);
            $formattedTime = date('h:i A', strtotime($now));
            $this->redirect('/employee/dashboard', 'success', "Successfully clocked out at {$formattedTime}. See you tomorrow!");
        }
    }
}
