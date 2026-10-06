-- ============================================================================
-- EMPLOYEE & LEAVE MANAGEMENT SYSTEM (ELMS)
-- Database Schema: elms_db
-- Compatibility: MySQL 8.0+ / MariaDB 10.4+ / InnoDB Engine
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `elms_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `elms_db`;

-- Drop existing tables in reverse dependency order to prevent FK lockouts
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `attendance`;
DROP TABLE IF EXISTS `leave_requests`;
DROP TABLE IF EXISTS `leave_balances`;
DROP TABLE IF EXISTS `leave_types`;
DROP TABLE IF EXISTS `employees`;
DROP TABLE IF EXISTS `departments`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- 1. Table: users
-- Purpose: Authentication credentials, roles, and session security
-- ----------------------------------------------------------------------------
CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `employee_id` INT UNSIGNED NULL,
    `email` VARCHAR(191) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'employee') NOT NULL DEFAULT 'employee',
    `status` ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
    `last_login` DATETIME NULL,
    `reset_token` VARCHAR(100) NULL,
    `reset_token_expires` DATETIME NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user_role` (`role`),
    INDEX `idx_user_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. Table: departments
-- Purpose: Organizational division categorization
-- ----------------------------------------------------------------------------
CREATE TABLE `departments` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_dept_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. Table: employees
-- Purpose: Comprehensive employee profile, employment and compensation data
-- ----------------------------------------------------------------------------
CREATE TABLE `employees` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NULL UNIQUE,
    `employee_code` VARCHAR(50) NOT NULL UNIQUE,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(25) NULL,
    `gender` ENUM('male', 'female', 'other') NULL,
    `date_of_birth` DATE NULL,
    `address` TEXT NULL,
    `department_id` INT UNSIGNED NULL,
    `designation` VARCHAR(100) NOT NULL,
    `joining_date` DATE NOT NULL,
    `salary` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `profile_image` VARCHAR(255) NULL,
    `status` ENUM('active', 'inactive', 'terminated') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_emp_department` (`department_id`),
    INDEX `idx_emp_status` (`status`),
    INDEX `idx_emp_code` (`employee_code`),
    CONSTRAINT `fk_employee_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_employee_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Set foreign key link back on users table for circular navigation
ALTER TABLE `users`
    ADD CONSTRAINT `fk_user_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- ----------------------------------------------------------------------------
-- 4. Table: leave_types
-- Purpose: Leave policy rules and annual quotas
-- ----------------------------------------------------------------------------
CREATE TABLE `leave_types` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `annual_limit` INT UNSIGNED NOT NULL DEFAULT 12,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_leave_type_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 5. Table: leave_balances
-- Purpose: Annual leave ledger allocated and consumed per employee per leave type
-- ----------------------------------------------------------------------------
CREATE TABLE `leave_balances` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `employee_id` INT UNSIGNED NOT NULL,
    `leave_type_id` INT UNSIGNED NOT NULL,
    `year` SMALLINT UNSIGNED NOT NULL,
    `allocated_days` INT UNSIGNED NOT NULL DEFAULT 0,
    `used_days` INT UNSIGNED NOT NULL DEFAULT 0,
    `remaining_days` INT UNSIGNED NOT NULL DEFAULT 0,
    UNIQUE KEY `uk_employee_leave_year` (`employee_id`, `leave_type_id`, `year`),
    INDEX `idx_balance_year` (`year`),
    CONSTRAINT `fk_balance_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_balance_type` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 6. Table: leave_requests
-- Purpose: Leave applications, review states, approver linkage, and audit notes
-- ----------------------------------------------------------------------------
CREATE TABLE `leave_requests` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `employee_id` INT UNSIGNED NOT NULL,
    `leave_type_id` INT UNSIGNED NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `total_days` INT UNSIGNED NOT NULL,
    `reason` TEXT NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
    `approved_by` INT UNSIGNED NULL,
    `approved_at` DATETIME NULL,
    `rejection_reason` TEXT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_leave_status` (`status`),
    INDEX `idx_leave_dates` (`start_date`, `end_date`),
    CONSTRAINT `fk_request_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_request_type` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_request_approver` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 7. Table: attendance
-- Purpose: Daily employee check-in/out records, working hours and status
-- ----------------------------------------------------------------------------
CREATE TABLE `attendance` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `employee_id` INT UNSIGNED NOT NULL,
    `attendance_date` DATE NOT NULL,
    `check_in` TIME NULL,
    `check_out` TIME NULL,
    `status` ENUM('present', 'absent', 'half_day', 'leave') NOT NULL DEFAULT 'present',
    `working_hours` DECIMAL(4, 2) NOT NULL DEFAULT 0.00,
    `remarks` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_emp_attendance_date` (`employee_id`, `attendance_date`),
    INDEX `idx_attendance_date` (`attendance_date`),
    INDEX `idx_attendance_status` (`status`),
    CONSTRAINT `fk_attendance_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 8. Table: notifications
-- Purpose: In-app notifications for leave alerts, approvals, and reminders
-- ----------------------------------------------------------------------------
CREATE TABLE `notifications` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `message` TEXT NOT NULL,
    `type` ENUM('leave', 'attendance', 'system', 'general') NOT NULL DEFAULT 'general',
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_notif_user_read` (`user_id`, `is_read`),
    CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 9. Table: activity_logs
-- Purpose: Audit log tracking system actions, security events, and IP addresses
-- ----------------------------------------------------------------------------
CREATE TABLE `activity_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NULL,
    `action` VARCHAR(100) NOT NULL,
    `description` TEXT NOT NULL,
    `ip_address` VARCHAR(45) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_activity_user` (`user_id`),
    INDEX `idx_activity_created` (`created_at`),
    CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
