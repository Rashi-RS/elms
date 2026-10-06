-- ============================================================================
-- EMPLOYEE & LEAVE MANAGEMENT SYSTEM (ELMS)
-- Database Seed Data: seed.sql
-- ============================================================================

USE `elms_db`;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `activity_logs`;
TRUNCATE TABLE `notifications`;
TRUNCATE TABLE `attendance`;
TRUNCATE TABLE `leave_requests`;
TRUNCATE TABLE `leave_balances`;
TRUNCATE TABLE `leave_types`;
TRUNCATE TABLE `employees`;
TRUNCATE TABLE `departments`;
TRUNCATE TABLE `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- 1. Seed: departments
-- ----------------------------------------------------------------------------
INSERT INTO `departments` (`id`, `name`, `description`, `status`, `created_at`) VALUES
(1, 'Engineering', 'Software engineering, system architecture, and technical infrastructure', 'active', NOW()),
(2, 'Human Resources', 'Talent acquisition, employee engagement, payroll, and workplace relations', 'active', NOW()),
(3, 'Finance & Accounts', 'Financial planning, accounting, auditing, and budget governance', 'active', NOW()),
(4, 'Marketing & Sales', 'Product marketing, brand strategy, lead generation, and client outreach', 'active', NOW()),
(5, 'Operations', 'Daily business operations, vendor relations, and logistical management', 'active', NOW());

-- ----------------------------------------------------------------------------
-- 2. Seed: leave_types
-- ----------------------------------------------------------------------------
INSERT INTO `leave_types` (`id`, `name`, `description`, `annual_limit`, `status`, `created_at`) VALUES
(1, 'Casual Leave', 'Short-term personal time off and urgent personal errands', 12, 'active', NOW()),
(2, 'Sick Leave', 'Medical leave for illness, surgery, recuperation, or healthcare appointments', 10, 'active', NOW()),
(3, 'Earned Leave', 'Annual vacation and privilege leave accrued over tenure', 15, 'active', NOW()),
(4, 'Optional Leave', 'Floating festive and cultural observance holidays', 3, 'active', NOW()),
(5, 'Work From Home', 'Remote telecommuting allowance approved under hybrid work guidelines', 24, 'active', NOW());

-- ----------------------------------------------------------------------------
-- 3. Seed: users & employees
-- Password Hashes:
-- Admin@123:    $2y$10$WmDwdwddItrC7beGK4KajeRM6nAVL0WFgHENDCW7mXc2IRqomSO5m
-- Employee@123: $2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK
-- ----------------------------------------------------------------------------

-- Admin User (ID: 1)
INSERT INTO `users` (`id`, `employee_id`, `email`, `password`, `role`, `status`, `last_login`, `created_at`) VALUES
(1, NULL, 'admin@elms.test', '$2y$10$WmDwdwddItrC7beGK4KajeRM6nAVL0WFgHENDCW7mXc2IRqomSO5m', 'admin', 'active', NOW(), NOW());

-- 10 Employee Users (IDs: 2 - 11)
INSERT INTO `users` (`id`, `employee_id`, `email`, `password`, `role`, `status`, `last_login`, `created_at`) VALUES
(2, 1, 'alex.turner@elms.test', '$2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK', 'employee', 'active', NOW(), NOW()),
(3, 2, 'sarah.jenkins@elms.test', '$2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK', 'employee', 'active', NOW(), NOW()),
(4, 3, 'michael.chen@elms.test', '$2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK', 'employee', 'active', NOW(), NOW()),
(5, 4, 'emily.watson@elms.test', '$2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK', 'employee', 'active', NOW(), NOW()),
(6, 5, 'david.miller@elms.test', '$2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK', 'employee', 'active', NOW(), NOW()),
(7, 6, 'jessica.taylor@elms.test', '$2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK', 'employee', 'active', NOW(), NOW()),
(8, 7, 'robert.wilson@elms.test', '$2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK', 'employee', 'active', NOW(), NOW()),
(9, 8, 'olivia.martinez@elms.test', '$2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK', 'employee', 'active', NOW(), NOW()),
(10, 9, 'james.anderson@elms.test', '$2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK', 'employee', 'active', NOW(), NOW()),
(11, 10, 'sophia.thomas@elms.test', '$2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK', 'employee', 'active', NOW(), NOW());

-- 10 Employee Records (IDs: 1 - 10)
INSERT INTO `employees` (`id`, `user_id`, `employee_code`, `first_name`, `last_name`, `phone`, `gender`, `date_of_birth`, `address`, `department_id`, `designation`, `joining_date`, `salary`, `status`, `created_at`) VALUES
(1, 2, 'EMP-1001', 'Alex', 'Turner', '+1-555-0101', 'male', '1992-04-15', '742 Evergreen Terrace, Springfield', 1, 'Senior Backend Engineer', '2022-01-10', 85000.00, 'active', NOW()),
(2, 3, 'EMP-1002', 'Sarah', 'Jenkins', '+1-555-0102', 'female', '1994-08-22', '124 Conch Street, Bikini Bottom', 1, 'Frontend UI/UX Specialist', '2022-03-15', 72000.00, 'active', NOW()),
(3, 4, 'EMP-1003', 'Michael', 'Chen', '+1-555-0103', 'male', '1990-11-05', '42 Wallaby Way, Sydney', 1, 'DevOps & Cloud Engineer', '2021-06-01', 92000.00, 'active', NOW()),
(4, 5, 'EMP-1004', 'Emily', 'Watson', '+1-555-0104', 'female', '1995-02-18', '221B Baker Street, London', 2, 'HR Business Partner', '2023-01-16', 64000.00, 'active', NOW()),
(5, 6, 'EMP-1005', 'David', 'Miller', '+1-555-0105', 'male', '1989-07-30', '350 Fifth Avenue, New York', 3, 'Chief Financial Analyst', '2020-09-12', 88000.00, 'active', NOW()),
(6, 7, 'EMP-1006', 'Jessica', 'Taylor', '+1-555-0106', 'female', '1993-12-11', '10 Downing Street, London', 4, 'Product Marketing Lead', '2022-08-20', 70000.00, 'active', NOW()),
(7, 8, 'EMP-1007', 'Robert', 'Wilson', '+1-555-0107', 'male', '1988-03-25', '1600 Pennsylvania Ave, Washington', 5, 'Operations Manager', '2019-11-04', 78000.00, 'active', NOW()),
(8, 9, 'EMP-1008', 'Olivia', 'Martinez', '+1-555-0108', 'female', '1996-05-14', '77 Massachusetts Ave, Cambridge', 1, 'QA Automation Engineer', '2023-04-10', 65000.00, 'active', NOW()),
(9, 10, 'EMP-1009', 'James', 'Anderson', '+1-555-0109', 'male', '1991-09-09', '1 Infinite Loop, Cupertino', 3, 'Senior Accountant', '2021-10-18', 69000.00, 'active', NOW()),
(10, 11, 'EMP-1010', 'Sophia', 'Thomas', '+1-555-0110', 'female', '1997-01-29', '30 Rockefeller Plaza, New York', 4, 'Digital Growth Specialist', '2023-07-03', 60000.00, 'active', NOW());

-- ----------------------------------------------------------------------------
-- 4. Seed: leave_balances (Current Year 2026 for all 10 employees x 5 leave types)
-- ----------------------------------------------------------------------------
INSERT INTO `leave_balances` (`employee_id`, `leave_type_id`, `year`, `allocated_days`, `used_days`, `remaining_days`) VALUES
-- Alex Turner (EMP-1001)
(1, 1, 2026, 12, 2, 10),
(1, 2, 2026, 10, 1, 9),
(1, 3, 2026, 15, 0, 15),
(1, 4, 2026, 3, 1, 2),
(1, 5, 2026, 24, 4, 20),

-- Sarah Jenkins (EMP-1002)
(2, 1, 2026, 12, 3, 9),
(2, 2, 2026, 10, 0, 10),
(2, 3, 2026, 15, 5, 10),
(2, 4, 2026, 3, 0, 3),
(2, 5, 2026, 24, 2, 22),

-- Michael Chen (EMP-1003)
(3, 1, 2026, 12, 1, 11),
(3, 2, 2026, 10, 2, 8),
(3, 3, 2026, 15, 0, 15),
(3, 4, 2026, 3, 0, 3),
(3, 5, 2026, 24, 6, 18),

-- Emily Watson (EMP-1004)
(4, 1, 2026, 12, 2, 10),
(4, 2, 2026, 10, 0, 10),
(4, 3, 2026, 15, 2, 13),
(4, 4, 2026, 3, 1, 2),
(4, 5, 2026, 24, 1, 23),

-- David Miller (EMP-1005)
(5, 1, 2026, 12, 0, 12),
(5, 2, 2026, 10, 1, 9),
(5, 3, 2026, 15, 4, 11),
(5, 4, 2026, 3, 0, 3),
(5, 5, 2026, 24, 3, 21),

-- Jessica Taylor (EMP-1006)
(6, 1, 2026, 12, 4, 8),
(6, 2, 2026, 10, 0, 10),
(6, 3, 2026, 15, 0, 15),
(6, 4, 2026, 3, 0, 3),
(6, 5, 2026, 24, 5, 19),

-- Robert Wilson (EMP-1007)
(7, 1, 2026, 12, 1, 11),
(7, 2, 2026, 10, 3, 7),
(7, 3, 2026, 15, 3, 12),
(7, 4, 2026, 3, 1, 2),
(7, 5, 2026, 24, 0, 24),

-- Olivia Martinez (EMP-1008)
(8, 1, 2026, 12, 0, 12),
(8, 2, 2026, 10, 0, 10),
(8, 3, 2026, 15, 0, 15),
(8, 4, 2026, 3, 0, 3),
(8, 5, 2026, 24, 2, 22),

-- James Anderson (EMP-1009)
(9, 1, 2026, 12, 2, 10),
(9, 2, 2026, 10, 1, 9),
(9, 3, 2026, 15, 1, 14),
(9, 4, 2026, 3, 0, 3),
(9, 5, 2026, 24, 4, 20),

-- Sophia Thomas (EMP-1010)
(10, 1, 2026, 12, 1, 11),
(10, 2, 2026, 10, 0, 10),
(10, 3, 2026, 15, 0, 15),
(10, 4, 2026, 3, 1, 2),
(10, 5, 2026, 24, 1, 23);

-- ----------------------------------------------------------------------------
-- 5. Seed: leave_requests
-- ----------------------------------------------------------------------------
INSERT INTO `leave_requests` (`id`, `employee_id`, `leave_type_id`, `start_date`, `end_date`, `total_days`, `reason`, `status`, `approved_by`, `approved_at`, `rejection_reason`, `created_at`) VALUES
(1, 1, 1, '2026-09-10', '2026-09-11', 2, 'Attending family wedding ceremony', 'approved', 1, '2026-09-08 14:30:00', NULL, '2026-09-07 10:15:00'),
(2, 2, 3, '2026-09-15', '2026-09-19', 5, 'Annual family holiday vacation to national park', 'approved', 1, '2026-09-12 11:20:00', NULL, '2026-09-10 09:40:00'),
(3, 1, 2, '2026-10-01', '2026-10-01', 1, 'Dental appointment and recovery', 'approved', 1, '2026-09-30 16:00:00', NULL, '2026-09-30 11:00:00'),
(4, 3, 1, '2026-10-12', '2026-10-14', 3, 'Personal domestic relocation and moving house', 'pending', NULL, NULL, NULL, NOW()),
(5, 4, 5, '2026-10-15', '2026-10-16', 2, 'Home electrical wiring and utility servicing', 'pending', NULL, NULL, NULL, NOW()),
(6, 6, 1, '2026-08-05', '2026-08-08', 4, 'Unscheduled trip without project handover', 'rejected', 1, '2026-08-04 15:00:00', 'Project deadline critical release period; please reschedule with team lead.', '2026-08-03 10:00:00'),
(7, 5, 3, '2026-10-20', '2026-10-23', 4, 'Attending international finance summit in Chicago', 'pending', NULL, NULL, NULL, NOW());

-- ----------------------------------------------------------------------------
-- 6. Seed: attendance (Sample historical & recent dates)
-- ----------------------------------------------------------------------------
INSERT INTO `attendance` (`employee_id`, `attendance_date`, `check_in`, `check_out`, `status`, `working_hours`, `remarks`, `created_at`) VALUES
-- EMP 1 (Alex Turner)
(1, CURDATE(), '09:02:00', '17:35:00', 'present', 8.55, 'On-time arrival', NOW()),
(1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '08:58:00', '17:30:00', 'present', 8.53, 'Regular shift', NOW()),
(1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '09:15:00', '13:30:00', 'half_day', 4.25, 'Doctor appointment afternoon', NOW()),

-- EMP 2 (Sarah Jenkins)
(2, CURDATE(), '09:10:00', '17:40:00', 'present', 8.50, 'Regular shift', NOW()),
(2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '09:05:00', '17:32:00', 'present', 8.45, 'Regular shift', NOW()),

-- EMP 3 (Michael Chen)
(3, CURDATE(), '08:50:00', '17:20:00', 'present', 8.50, 'On-time arrival', NOW()),
(3, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '08:55:00', '17:25:00', 'present', 8.50, 'Regular shift', NOW()),

-- EMP 4 (Emily Watson)
(4, CURDATE(), '09:30:00', '18:00:00', 'present', 8.50, 'Regular shift', NOW()),
(4, DATE_SUB(CURDATE(), INTERVAL 1 DAY), NULL, NULL, 'absent', 0.00, 'Uninformed absence', NOW()),

-- EMP 5 (David Miller)
(5, CURDATE(), '09:00:00', '17:30:00', 'present', 8.50, 'Regular shift', NOW()),

-- EMP 6 (Jessica Taylor)
(6, CURDATE(), NULL, NULL, 'leave', 0.00, 'Approved casual leave', NOW()),

-- EMP 7 (Robert Wilson)
(7, CURDATE(), '09:05:00', '17:35:00', 'present', 8.50, 'Regular shift', NOW()),

-- EMP 8 (Olivia Martinez)
(8, CURDATE(), '09:12:00', '17:42:00', 'present', 8.50, 'Regular shift', NOW()),

-- EMP 9 (James Anderson)
(9, CURDATE(), '08:55:00', '17:25:00', 'present', 8.50, 'On-time arrival', NOW()),

-- EMP 10 (Sophia Thomas)
(10, CURDATE(), '09:00:00', '17:30:00', 'present', 8.50, 'Regular shift', NOW());

-- ----------------------------------------------------------------------------
-- 7. Seed: notifications
-- ----------------------------------------------------------------------------
INSERT INTO `notifications` (`user_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(2, 'Leave Request Approved', 'Your Casual Leave request for 10-11 Sep 2026 has been approved by Admin.', 'leave', 1, DATE_SUB(NOW(), INTERVAL 20 DAY)),
(3, 'Leave Request Approved', 'Your Earned Leave request for 15-19 Sep 2026 has been approved by Admin.', 'leave', 1, DATE_SUB(NOW(), INTERVAL 15 DAY)),
(7, 'Leave Request Rejected', 'Your Casual Leave request for 05-08 Aug 2026 was rejected: Project deadline critical release period.', 'leave', 0, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(2, 'New Policy Update', 'Welcome to the new ELMS portal! Please verify your contact information in My Profile.', 'system', 0, NOW()),
(1, 'New Leave Application', 'Michael Chen (EMP-1003) submitted a leave request awaiting your review.', 'leave', 0, NOW());

-- ----------------------------------------------------------------------------
-- 8. Seed: activity_logs
-- ----------------------------------------------------------------------------
INSERT INTO `activity_logs` (`user_id`, `action`, `description`, `ip_address`, `created_at`) VALUES
(1, 'System Seeding', 'Initial system seed data loaded with sample employees, departments, and leave types', '127.0.0.1', NOW()),
(1, 'Admin Login', 'Administrator signed into ELMS dashboard', '127.0.0.1', NOW()),
(2, 'Employee Login', 'Alex Turner signed in to employee portal', '127.0.0.1', NOW()),
(1, 'Leave Approved', 'Approved leave request #1 for Alex Turner (2 days)', '127.0.0.1', NOW()),
(1, 'Leave Rejected', 'Rejected leave request #6 for Jessica Taylor', '127.0.0.1', NOW());
