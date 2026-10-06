<?php
declare(strict_types=1);

namespace App\controllers;

use App\helpers\Session;
use App\helpers\Validator;
use App\models\Employee;
use App\models\User;
use App\services\ActivityLogger;
use App\services\FileUploader;

class ProfileController extends Controller
{
    private Employee $employeeModel;
    private User $userModel;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->employeeModel = new Employee();
        $this->userModel = new User();
    }

    public function show(): void
    {
        $userId = Session::userId();
        $employee = $this->employeeModel->getByUserId($userId);

        if (!$employee) {
            $this->redirect('/employee/dashboard', 'error', 'Profile not found.');
        }

        $this->render('employee/profile', [
            'title' => 'My Profile — ELMS',
            'employee' => $employee,
        ]);
    }

    public function update(): void
    {
        $this->validateCsrf();

        $userId = Session::userId();
        $employee = $this->employeeModel->getByUserId($userId);

        if (!$employee) {
            $this->redirect('/employee/profile', 'error', 'Profile not found.');
        }

        $phone = trim((string)$this->request->input('phone', ''));
        $address = trim((string)$this->request->input('address', ''));

        $avatarPath = $employee['profile_image'];
        if (!empty($_FILES['profile_image']['name'])) {
            $uploader = new FileUploader();
            $newAvatar = $uploader->uploadAvatar($_FILES['profile_image']);
            if ($newAvatar) {
                $avatarPath = $newAvatar;
                // Update session avatar preview
                $sessionUser = Session::user();
                $sessionUser['profile_image'] = $newAvatar;
                Session::set('user', $sessionUser);
            } elseif (!empty($uploader->getErrors())) {
                $this->redirect('/employee/profile', 'error', $uploader->getFirstError());
            }
        }

        // STRICT SECURITY: Only safe fields can be edited by the employee!
        $this->employeeModel->update((int)$employee['id'], [
            'phone' => $phone,
            'address' => $address,
            'profile_image' => $avatarPath,
        ]);

        ActivityLogger::log('Profile Updated', "Employee updated contact details.", $userId);

        $this->redirect('/employee/profile', 'success', 'Profile information updated successfully.');
    }

    public function changePassword(): void
    {
        $this->validateCsrf();

        $userId = Session::userId();
        $user = $this->userModel->find($userId);

        $currentPassword = (string)$this->request->input('current_password', '');
        $newPassword = (string)$this->request->input('new_password', '');
        $confirmPassword = (string)$this->request->input('confirm_password', '');

        $validator = new Validator([
            'current_password' => $currentPassword,
            'new_password' => $newPassword,
            'confirm_password' => $confirmPassword,
        ], [
            'current_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode('<br>', $validator->errors()));
            $this->redirect('/employee/profile');
        }

        if (!password_verify($currentPassword, $user['password'])) {
            $this->redirect('/employee/profile', 'error', 'Incorrect current password.');
        }

        $this->userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        ActivityLogger::log('Password Changed', "User changed their login password.", $userId);

        $this->redirect('/employee/profile', 'success', 'Your password has been changed successfully.');
    }
}
