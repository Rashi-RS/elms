<?php
declare(strict_types=1);

namespace App\controllers;

use App\helpers\Session;
use App\helpers\Validator;
use App\models\User;
use App\services\ActivityLogger;

class AuthController extends Controller
{
    private User $userModel;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->userModel = new User();
    }

    public function showLogin(): void
    {
        $this->render('auth/login', [
            'title' => 'Sign In — ELMS',
            'email' => Session::get('_old_email', ''),
        ], 'layouts/auth');
    }

    public function login(): void
    {
        $this->validateCsrf();

        $email = trim((string)$this->request->input('email', ''));
        $password = (string)$this->request->input('password', '');
        $remember = (bool)$this->request->input('remember');

        Session::set('_old_email', $email);

        // Basic Brute Force Prevention (Throttle after 5 consecutive failed attempts per session)
        $failedAttempts = (int)Session::get('login_attempts', 0);
        $lastAttemptTime = (int)Session::get('last_attempt_time', 0);

        if ($failedAttempts >= 5 && (time() - $lastAttemptTime) < 300) {
            $remaining = 300 - (time() - $lastAttemptTime);
            $this->redirect('/login', 'error', "Too many failed attempts. Security cooldown active for {$remaining} seconds.");
        }

        $validator = new Validator([
            'email' => $email,
            'password' => $password,
        ], [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->redirect('/login', 'error', $validator->firstError('email') ?? $validator->firstError('password') ?? 'Invalid credentials.');
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            Session::set('login_attempts', $failedAttempts + 1);
            Session::set('last_attempt_time', time());
            ActivityLogger::log('Failed Login', "Failed login attempt for email: {$email}");
            $this->redirect('/login', 'error', 'Invalid email address or password.');
        }

        if ($user['status'] !== 'active') {
            $this->redirect('/login', 'error', 'Your account has been deactivated or suspended. Please contact Administrator.');
        }

        // Login Succeeded: Reset attempt counter
        Session::remove('login_attempts');
        Session::remove('last_attempt_time');
        Session::remove('_old_email');

        // Prevent Session Fixation attack
        Session::regenerate();

        // Attach employee details if role is employee
        $employeeDetails = null;
        if (!empty($user['employee_id'])) {
            $employeeModel = new \App\models\Employee();
            $employeeDetails = $employeeModel->find((int)$user['employee_id']);
        }

        // Store user payload in session
        Session::set('user', [
            'id' => (int)$user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'status' => $user['status'],
            'employee_id' => $user['employee_id'] ? (int)$user['employee_id'] : null,
            'first_name' => $employeeDetails['first_name'] ?? 'System',
            'last_name' => $employeeDetails['last_name'] ?? 'Admin',
            'designation' => $employeeDetails['designation'] ?? 'Administrator',
            'profile_image' => $employeeDetails['profile_image'] ?? null,
        ]);

        $this->userModel->updateLastLogin((int)$user['id']);
        ActivityLogger::log('User Login', "User {$user['email']} signed into the system.", (int)$user['id']);

        if ($user['role'] === 'admin') {
            $this->redirect('/admin/dashboard', 'success', 'Welcome back, Administrator!');
        } else {
            $this->redirect('/employee/dashboard', 'success', 'Welcome back, ' . ($employeeDetails['first_name'] ?? 'Employee') . '!');
        }
    }

    public function logout(): void
    {
        $userId = Session::userId();
        if ($userId) {
            ActivityLogger::log('User Logout', "User logged out.", $userId);
        }

        Session::destroy();
        $this->redirect('/login', 'success', 'You have been signed out successfully.');
    }

    public function showForgotPassword(): void
    {
        $this->render('auth/forgot_password', [
            'title' => 'Password Recovery — ELMS',
        ], 'layouts/auth');
    }

    public function sendResetLink(): void
    {
        $this->validateCsrf();

        $email = trim((string)$this->request->input('email', ''));

        $validator = new Validator(['email' => $email], ['email' => 'required|email']);
        if ($validator->fails()) {
            $this->redirect('/forgot-password', 'error', 'Please provide a valid email address.');
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            // For security, show uniform response to prevent user enumeration
            $this->redirect('/forgot-password', 'info', 'If an account exists with that email, a password recovery link has been generated.');
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour
        $this->userModel->setResetToken($email, $token, $expires);

        ActivityLogger::log('Password Reset Requested', "Password reset token generated for {$email}.");

        // In local development / interview portfolio, provide simulation feedback
        Session::flash('info', "DEMO RECOVERY: In production an email is sent. For local demonstration, your reset token is: <strong>{$token}</strong> (Valid for 1 hour).");
        $this->redirect('/forgot-password');
    }
}
