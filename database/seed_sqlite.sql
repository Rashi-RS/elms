-- SQLite Seed Data for ELMS
INSERT INTO departments (id, name, description, status, created_at) VALUES
(1, 'Engineering', 'Software engineering, system architecture, and technical infrastructure', 'active', CURRENT_TIMESTAMP),
(2, 'Human Resources', 'Talent acquisition, employee engagement, payroll, and workplace relations', 'active', CURRENT_TIMESTAMP),
(3, 'Finance & Accounts', 'Financial planning, accounting, auditing, and budget governance', 'active', CURRENT_TIMESTAMP),
(4, 'Marketing & Sales', 'Product marketing, brand strategy, lead generation, and client outreach', 'active', CURRENT_TIMESTAMP),
(5, 'Operations', 'Daily business operations, vendor relations, and logistical management', 'active', CURRENT_TIMESTAMP);

INSERT INTO leave_types (id, name, description, annual_limit, status, created_at) VALUES
(1, 'Casual Leave', 'Short-term personal time off and urgent personal errands', 12, 'active', CURRENT_TIMESTAMP),
(2, 'Sick Leave', 'Medical leave for illness, surgery, recuperation, or healthcare appointments', 10, 'active', CURRENT_TIMESTAMP),
(3, 'Earned Leave', 'Annual vacation and privilege leave accrued over tenure', 15, 'active', CURRENT_TIMESTAMP),
(4, 'Optional Leave', 'Floating festive and cultural observance holidays', 3, 'active', CURRENT_TIMESTAMP),
(5, 'Work From Home', 'Remote telecommuting allowance approved under hybrid work guidelines', 24, 'active', CURRENT_TIMESTAMP);

-- Admin User
INSERT INTO users (id, employee_id, email, password, role, status, last_login, created_at) VALUES
(1, NULL, 'admin@elms.test', '$2y$10$WmDwdwddItrC7beGK4KajeRM6nAVL0WFgHENDCW7mXc2IRqomSO5m', 'admin', 'active', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- 10 Employee Users
INSERT INTO users (id, employee_id, email, password, role, status, last_login, created_at) VALUES
(2, 1, 'alex.turner@elms.test', '$2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK', 'employee', 'active', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(3, 2, 'sarah.jenkins@elms.test', '$2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK', 'employee', 'active', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(4, 3, 'michael.chen@elms.test', '$2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK', 'employee', 'active', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(5, 4, 'emily.watson@elms.test', '$2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK', 'employee', 'active', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(6, 5, 'david.miller@elms.test', '$2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK', 'employee', 'active', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(7, 6, 'jessica.taylor@elms.test', '$2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK', 'employee', 'active', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(8, 7, 'robert.wilson@elms.test', '$2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK', 'employee', 'active', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(9, 8, 'olivia.martinez@elms.test', '$2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK', 'employee', 'active', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(10, 9, 'james.anderson@elms.test', '$2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK', 'employee', 'active', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(11, 10, 'sophia.thomas@elms.test', '$2y$10$apo/seUaQeN3CLjvJuGwRejYJBT/qRrJFf5PWpogyUFTIdb.OjwxK', 'employee', 'active', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- 10 Employees
INSERT INTO employees (id, user_id, employee_code, first_name, last_name, phone, gender, date_of_birth, address, department_id, designation, joining_date, salary, status, created_at) VALUES
(1, 2, 'EMP-1001', 'Alex', 'Turner', '+1-555-0101', 'male', '1992-04-15', '742 Evergreen Terrace, Springfield', 1, 'Senior Backend Engineer', '2022-01-10', 85000.00, 'active', CURRENT_TIMESTAMP),
(2, 3, 'EMP-1002', 'Sarah', 'Jenkins', '+1-555-0102', 'female', '1994-08-22', '124 Conch Street, Bikini Bottom', 1, 'Frontend UI/UX Specialist', '2022-03-15', 72000.00, 'active', CURRENT_TIMESTAMP),
(3, 4, 'EMP-1003', 'Michael', 'Chen', '+1-555-0103', 'male', '1990-11-05', '42 Wallaby Way, Sydney', 1, 'DevOps & Cloud Engineer', '2021-06-01', 92000.00, 'active', CURRENT_TIMESTAMP),
(4, 5, 'EMP-1004', 'Emily', 'Watson', '+1-555-0104', 'female', '1995-02-18', '221B Baker Street, London', 2, 'HR Business Partner', '2023-01-16', 64000.00, 'active', CURRENT_TIMESTAMP),
(5, 6, 'EMP-1005', 'David', 'Miller', '+1-555-0105', 'male', '1989-07-30', '350 Fifth Avenue, New York', 3, 'Chief Financial Analyst', '2020-09-12', 88000.00, 'active', CURRENT_TIMESTAMP),
(6, 7, 'EMP-1006', 'Jessica', 'Taylor', '+1-555-0106', 'female', '1993-12-11', '10 Downing Street, London', 4, 'Product Marketing Lead', '2022-08-20', 70000.00, 'active', CURRENT_TIMESTAMP),
(7, 8, 'EMP-1007', 'Robert', 'Wilson', '+1-555-0107', 'male', '1988-03-25', '1600 Pennsylvania Ave, Washington', 5, 'Operations Manager', '2019-11-04', 78000.00, 'active', CURRENT_TIMESTAMP),
(8, 9, 'EMP-1008', 'Olivia', 'Martinez', '+1-555-0108', 'female', '1996-05-14', '77 Massachusetts Ave, Cambridge', 1, 'QA Automation Engineer', '2023-04-10', 65000.00, 'active', CURRENT_TIMESTAMP),
(9, 10, 'EMP-1009', 'James', 'Anderson', '+1-555-0109', 'male', '1991-09-09', '1 Infinite Loop, Cupertino', 3, 'Senior Accountant', '2021-10-18', 69000.00, 'active', CURRENT_TIMESTAMP),
(10, 11, 'EMP-1010', 'Sophia', 'Thomas', '+1-555-0110', 'female', '1997-01-29', '30 Rockefeller Plaza, New York', 4, 'Digital Growth Specialist', '2023-07-03', 60000.00, 'active', CURRENT_TIMESTAMP);

-- Leave Balances for 2026
INSERT INTO leave_balances (employee_id, leave_type_id, year, allocated_days, used_days, remaining_days) VALUES
(1, 1, 2026, 12, 2, 10), (1, 2, 2026, 10, 1, 9), (1, 3, 2026, 15, 0, 15), (1, 4, 2026, 3, 1, 2), (1, 5, 2026, 24, 4, 20),
(2, 1, 2026, 12, 3, 9), (2, 2, 2026, 10, 0, 10), (2, 3, 2026, 15, 5, 10), (2, 4, 2026, 3, 0, 3), (2, 5, 2026, 24, 2, 22),
(3, 1, 2026, 12, 1, 11), (3, 2, 2026, 10, 2, 8), (3, 3, 2026, 15, 0, 15), (3, 4, 2026, 3, 0, 3), (3, 5, 2026, 24, 6, 18),
(4, 1, 2026, 12, 2, 10), (4, 2, 2026, 10, 0, 10), (4, 3, 2026, 15, 2, 13), (4, 4, 2026, 3, 1, 2), (4, 5, 2026, 24, 1, 23),
(5, 1, 2026, 12, 0, 12), (5, 2, 2026, 10, 1, 9), (5, 3, 2026, 15, 4, 11), (5, 4, 2026, 3, 0, 3), (5, 5, 2026, 24, 3, 21),
(6, 1, 2026, 12, 4, 8), (6, 2, 2026, 10, 0, 10), (6, 3, 2026, 15, 0, 15), (6, 4, 2026, 3, 0, 3), (6, 5, 2026, 24, 5, 19),
(7, 1, 2026, 12, 1, 11), (7, 2, 2026, 10, 3, 7), (7, 3, 2026, 15, 3, 12), (7, 4, 2026, 3, 1, 2), (7, 5, 2026, 24, 0, 24),
(8, 1, 2026, 12, 0, 12), (8, 2, 2026, 10, 0, 10), (8, 3, 2026, 15, 0, 15), (8, 4, 2026, 3, 0, 3), (8, 5, 2026, 24, 2, 22),
(9, 1, 2026, 12, 2, 10), (9, 2, 2026, 10, 1, 9), (9, 3, 2026, 15, 1, 14), (9, 4, 2026, 3, 0, 3), (9, 5, 2026, 24, 4, 20),
(10, 1, 2026, 12, 1, 11), (10, 2, 2026, 10, 0, 10), (10, 3, 2026, 15, 0, 15), (10, 4, 2026, 3, 1, 2), (10, 5, 2026, 24, 1, 23);

-- Leave Requests
INSERT INTO leave_requests (id, employee_id, leave_type_id, start_date, end_date, total_days, reason, status, approved_by, approved_at, rejection_reason, created_at) VALUES
(1, 1, 1, '2026-09-10', '2026-09-11', 2, 'Attending family wedding ceremony', 'approved', 1, '2026-09-08 14:30:00', NULL, '2026-09-07 10:15:00'),
(2, 2, 3, '2026-09-15', '2026-09-19', 5, 'Annual family holiday vacation to national park', 'approved', 1, '2026-09-12 11:20:00', NULL, '2026-09-10 09:40:00'),
(3, 1, 2, '2026-10-01', '2026-10-01', 1, 'Dental appointment and recovery', 'approved', 1, '2026-09-30 16:00:00', NULL, '2026-09-30 11:00:00'),
(4, 3, 1, '2026-10-12', '2026-10-14', 3, 'Personal domestic relocation and moving house', 'pending', NULL, NULL, NULL, CURRENT_TIMESTAMP),
(5, 4, 5, '2026-10-15', '2026-10-16', 2, 'Home electrical wiring and utility servicing', 'pending', NULL, NULL, NULL, CURRENT_TIMESTAMP),
(6, 6, 1, '2026-08-05', '2026-08-08', 4, 'Unscheduled trip without project handover', 'rejected', 1, '2026-08-04 15:00:00', 'Project deadline critical release period; please reschedule with team lead.', '2026-08-03 10:00:00'),
(7, 5, 3, '2026-10-20', '2026-10-23', 4, 'Attending international finance summit in Chicago', 'pending', NULL, NULL, NULL, CURRENT_TIMESTAMP);

-- Attendance (Today: 2026-10-06)
INSERT INTO attendance (employee_id, attendance_date, check_in, check_out, status, working_hours, remarks, created_at) VALUES
(1, date('now'), '09:02:00', '17:35:00', 'present', 8.55, 'On-time arrival', CURRENT_TIMESTAMP),
(2, date('now'), '09:10:00', '17:40:00', 'present', 8.50, 'Regular shift', CURRENT_TIMESTAMP),
(3, date('now'), '08:50:00', '17:20:00', 'present', 8.50, 'On-time arrival', CURRENT_TIMESTAMP),
(4, date('now'), '09:30:00', '18:00:00', 'present', 8.50, 'Regular shift', CURRENT_TIMESTAMP),
(5, date('now'), '09:00:00', '17:30:00', 'present', 8.50, 'Regular shift', CURRENT_TIMESTAMP),
(6, date('now'), NULL, NULL, 'leave', 0.00, 'Approved casual leave', CURRENT_TIMESTAMP),
(7, date('now'), '09:05:00', '17:35:00', 'present', 8.50, 'Regular shift', CURRENT_TIMESTAMP),
(8, date('now'), '09:12:00', '17:42:00', 'present', 8.50, 'Regular shift', CURRENT_TIMESTAMP),
(9, date('now'), '08:55:00', '17:25:00', 'present', 8.50, 'On-time arrival', CURRENT_TIMESTAMP),
(10, date('now'), '09:00:00', '17:30:00', 'present', 8.50, 'Regular shift', CURRENT_TIMESTAMP);

-- Notifications
INSERT INTO notifications (user_id, title, message, type, is_read, created_at) VALUES
(2, 'Leave Request Approved', 'Your Casual Leave request for 10-11 Sep 2026 has been approved by Admin.', 'leave', 1, CURRENT_TIMESTAMP),
(3, 'Leave Request Approved', 'Your Earned Leave request for 15-19 Sep 2026 has been approved by Admin.', 'leave', 1, CURRENT_TIMESTAMP),
(7, 'Leave Request Rejected', 'Your Casual Leave request for 05-08 Aug 2026 was rejected: Project deadline critical release period.', 'leave', 0, CURRENT_TIMESTAMP),
(2, 'New Policy Update', 'Welcome to the new ELMS portal! Please verify your contact information in My Profile.', 'system', 0, CURRENT_TIMESTAMP),
(1, 'New Leave Application', 'Michael Chen (EMP-1003) submitted a leave request awaiting your review.', 'leave', 0, CURRENT_TIMESTAMP);

-- Activity Logs
INSERT INTO activity_logs (user_id, action, description, ip_address, created_at) VALUES
(1, 'System Seeding', 'Initial system seed data loaded with sample employees, departments, and leave types', '127.0.0.1', CURRENT_TIMESTAMP),
(1, 'Admin Login', 'Administrator signed into ELMS dashboard', '127.0.0.1', CURRENT_TIMESTAMP),
(2, 'Employee Login', 'Alex Turner signed in to employee portal', '127.0.0.1', CURRENT_TIMESTAMP),
(1, 'Leave Approved', 'Approved leave request #1 for Alex Turner (2 days)', '127.0.0.1', CURRENT_TIMESTAMP),
(1, 'Leave Rejected', 'Rejected leave request #6 for Jessica Taylor', '127.0.0.1', CURRENT_TIMESTAMP);
