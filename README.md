# ELMS — Employee & Leave Management System

A production-style, enterprise-grade **Employee & Leave Management System (ELMS)** built using **Core PHP 8.2+**, **Object-Oriented PHP**, a custom **MVC Architecture**, **PDO with MySQL 8+**, and modern **Bootstrap 5.3 / Vanilla JS / Chart.js**.

Designed specifically as a **senior-grade portfolio codebase** and a comprehensive showcase of backend software architecture for **PHP Developer technical interviews**.

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Key Features](#2-key-features)
3. [Technology Stack](#3-technology-stack)
4. [MVC Architecture & Request Lifecycle](#4-mvc-architecture--request-lifecycle)
5. [Directory Structure](#5-directory-structure)
6. [Database Schema & ER Relationships](#6-database-schema--er-relationships)
7. [Installation & Setup](#7-installation--setup)
8. [Environment Configuration](#8-environment-configuration)
9. [Database Setup & Seeding](#9-database-setup--seeding)
10. [Demo Credentials](#10-demo-credentials)
11. [REST API Documentation](#11-rest-api-documentation)
12. [Security Architecture](#12-security-architecture)
13. [ACID Database Transactions](#13-acid-database-transactions)
14. [Testing & Verification Guide](#14-testing--verification-guide)
15. [How to Pitch This Project in an Interview](#15-how-to-pitch-this-project-in-an-interview)
16. [35 PHP Developer Interview Questions & Answers From This Project](#16-35-php-developer-interview-questions--answers-from-this-project)

---

## 1. Project Overview

**ELMS** is an enterprise human resource solution that streamlines employee record administration, attendance timesheets, departmental grouping, and multi-tier leave approval lifecycles.

Unlike typical monolithic or procedural tutorial projects that mix raw SQL and unescaped HTML, ELMS is structured around **clean separation of concerns**:
- A unified **Front Controller** pattern (`public/index.php`) routing all traffic.
- Custom **HTTP Request & Response abstractions**.
- Native **PSR-4 class autoloading**.
- Comprehensive **Server-Side Validation Engine**.
- **Role-Based Access Control Middleware** strictly segregating Admin from Employee privileges.
- Multi-table **ACID Transactions** for atomic leave deductions.
- An asynchronous **AJAX/Fetch API** layer for instant reviews and notifications without full-page reloads.

---

## 2. Key Features

### Admin Portal
- **Executive Analytics Dashboard:** Dynamic KPI metric cards (Total staff, Active employees, Today's Present/Absent/Half-day breakdown, Pending leaves), and interactive Chart.js visualizations (Department distribution, Today's attendance pulse, Leave ratio).
- **Employee Management (CRUD):** Searchable, department-filtered, status-filtered directory with server-side pagination. Onboarding transaction automatically creates employee profile, Bcrypt-hashed user account, and annual leave quotas.
- **Department Governance:** Add, edit, and toggle departments with referential safety preventing deactivation of departments containing active personnel.
- **Leave Policy & Entitlement Engine:** Configure categories (Casual, Sick, Earned, Optional, Work From Home) and annual quotas.
- **Leave Review Workflow:** Multi-tab interface (All, Pending, Approved, Rejected) with date filters. Instant AJAX approval and rejection with mandatory reason tracking.
- **Attendance Roster:** Daily attendance sheet with time-difference working-hour calculator and modal updates.
- **Audit Reports & CSV Exports:** Filterable reports for Attendance, Leaves, and Employee Census with single-click CSV export and print-ready stylesheets.
- **System Audit Trail:** Immutable log recording IP addresses, authenticated users, timestamps, and actions.

### Employee Self-Service Portal
- **Personalized Dashboard:** Shift clock-in/clock-out actions, current-month attendance percentage progress bar, annual leave balance counters, and recent applications.
- **Leave Application Engine:** Dynamic client-side date calculator, quota verification, overlap detection against active leaves, and instant confirmation alerts.
- **Leave History:** Personal ledger with status badges, administrative feedback notes, and ability to cancel pending requests.
- **Monthly Attendance Timesheet:** Filter by month, summary counters (Present, Absent, Half Day, Leave), and shift time tracking.
- **Profile Self-Service:** Update personal contact details (phone, address, profile image) with strict server-side safeguards preventing tampering with salary, department, or job title.
- **Notifications Center:** In-app inbox with unread counter badges and AJAX mark-as-read toggles.

---

## 3. Technology Stack

| Layer | Technologies Used |
| :--- | :--- |
| **Backend Core** | PHP 8.2+ (Strict typing `declare(strict_types=1);`, OOP, Match expressions) |
| **Architecture** | Custom MVC (Front Controller, Router, Controllers, Models, Middleware, Services) |
| **Database Engine** | MySQL 8.0+ / InnoDB (Foreign keys, composite unique constraints, ACID transactions) |
| **Database Access** | PDO (PHP Data Objects) with 100% prepared statement parameterization |
| **Authentication** | Native PHP Sessions (`httponly`, `samesite=Lax`), `password_hash()`, `password_verify()` |
| **Frontend UI** | HTML5, CSS3, Bootstrap 5.3, Bootstrap Icons, Google Fonts (Inter) |
| **Client Scripts** | Vanilla JavaScript (ES6+), Fetch API, CSRF Header Injection |
| **Data Visualization** | Chart.js 4.4 (Doughnut, Bar, and Pie charts) |
| **API Architecture**| RESTful JSON endpoints returning standardized HTTP status codes |

---

## 4. MVC Architecture & Request Lifecycle

```text
Browser Request (GET/POST/PUT/DELETE)
       │
       ▼
public/index.php (Front Controller)
       │
       ▼
config/config.php (Loads .env, starts secure session, registers PSR-4 Autoloader)
       │
       ▼
app/core/Router.php (Regex pattern matching & route parameter extraction)
       │
       ▼
app/middleware/ (AuthMiddleware, AdminMiddleware, EmployeeMiddleware, GuestMiddleware)
       │
       ▼
app/controllers/ (Dispatches to Controller method, validates CSRF token & input)
       │
       ├──► app/services/ (LeaveService, FileUploader, ActivityLogger)
       │          │
       │          ▼
       ├──► app/models/ (Employee, LeaveRequest, Attendance, Database PDO Singleton)
       │          │
       │          ▼
       │     MySQL 8 (elms_db)
       │
       ▼
app/views/ (Renders view within app/views/layouts/main.php or outputs JSON via Response)
       │
       ▼
Browser Display / AJAX DOM Update
```

---

## 5. Directory Structure

```text
elms/
│
├── app/
│   ├── core/
│   │   ├── App.php             # Application kernel & top-level exception handler
│   │   ├── Request.php         # HTTP request wrapper (URI, method spoofing, inputs, files)
│   │   ├── Response.php        # HTTP response builder (Headers, JSON, redirects)
│   │   └── Router.php          # Route registry with regex route parameter matching
│   │
│   ├── controllers/
│   │   ├── Controller.php              # Base controller (render, json, redirect, CSRF validation)
│   │   ├── AuthController.php          # Login, brute-force throttle, logout, password recovery
│   │   ├── AdminDashboardController.php# Dynamic KPI metrics & Chart.js data feeds
│   │   ├── EmployeeDashboardController.php # Employee overview, clocking & balances
│   │   ├── EmployeeController.php      # Admin employee CRUD & file upload handling
│   │   ├── DepartmentController.php    # Department management with referential protection
│   │   ├── LeaveTypeController.php     # Leave policy & quota administration
│   │   ├── LeaveController.php         # Leave review (Admin) & leave application (Employee)
│   │   ├── AttendanceController.php    # Daily attendance roster & employee timesheets
│   │   ├── ProfileController.php       # Employee profile & password change
│   │   ├── ReportController.php        # Audit reports & CSV file streaming
│   │   ├── NotificationController.php  # Notifications & AJAX unread counter
│   │   ├── ActivityLogController.php   # System audit log viewer
│   │   └── ApiController.php           # RESTful JSON endpoints
│   │
│   ├── models/
│   │   ├── Database.php        # PDO Singleton connection & transaction manager
│   │   ├── Model.php           # Base Model with prepared query helpers & pagination
│   │   ├── User.php            # User authentication records & reset tokens
│   │   ├── Employee.php        # Employee records & multi-table transaction factory
│   │   ├── Department.php      # Department records & headcount counts
│   │   ├── LeaveType.php       # Leave policy categories & annual limits
│   │   ├── LeaveBalance.php    # Annual employee balance quotas
│   │   ├── LeaveRequest.php    # Leave applications & approval metadata
│   │   ├── Attendance.php      # Daily check-in/out & hours calculation
│   │   ├── Notification.php    # In-app notifications
│   │   └── ActivityLog.php     # Audit logging records
│   │
│   ├── middleware/
│   │   ├── Middleware.php          # Middleware interface contract
│   │   ├── AuthMiddleware.php      # Enforces logged-in state
│   │   ├── GuestMiddleware.php     # Redirects authenticated users away from login
│   │   ├── AdminMiddleware.php     # Strictly requires role = 'admin' (aborts with 403)
│   │   └── EmployeeMiddleware.php  # Requires role = 'employee' or 'admin'
│   │
│   ├── helpers/
│   │   ├── Csrf.php            # Cryptographic token generation & validation
│   │   ├── Session.php         # Flash messages, auth state, and cookie security
│   │   ├── Validator.php       # Chainable server-side validation rules
│   │   └── ViewHelper.php      # HTML output escaping (e()), formatting & badges
│   │
│   ├── services/
│   │   ├── LeaveService.php    # ACID transaction-based leave approval/rejection
│   │   ├── FileUploader.php    # MIME & extension avatar upload validator
│   │   └── ActivityLogger.php  # Audit trail writer
│   │
│   └── views/
│       ├── layouts/
│       │   ├── main.php        # Primary authenticated SaaS dashboard wrapper
│       │   └── auth.php        # Clean authentication card layout
│       ├── partials/
│       │   ├── topbar.php      # User badge, notification bell & breadcrumbs
│       │   ├── admin_sidebar.php   # Admin navigation with active state indicators
│       │   ├── employee_sidebar.php# Employee portal navigation
│       │   ├── alerts.php      # Flash message alert container
│       │   └── footer.php      # Footer metadata
│       ├── auth/
│       │   ├── login.php       # Sign-in form with one-click demo credentials
│       │   └── forgot_password.php # Password recovery simulation
│       ├── admin/
│       │   ├── dashboard.php
│       │   ├── employees/ (index.php, create.php, edit.php, show.php)
│       │   ├── departments/ (index.php)
│       │   ├── leave_types/ (index.php)
│       │   ├── leaves/ (index.php, show.php)
│       │   ├── attendance/ (index.php)
│       │   ├── reports/ (index.php, attendance.php, leaves.php, employees.php)
│       │   └── activity_logs/ (index.php)
│       ├── employee/
│       │   ├── dashboard.php
│       │   ├── profile.php
│       │   ├── apply_leave.php
│       │   ├── my_leaves.php
│       │   ├── my_attendance.php
│       │   └── notifications.php
│       └── errors/
│           ├── 403.php         # Forbidden Access
│           ├── 404.php         # Route Not Found
│           └── 500.php         # Server Error
│
├── config/
│   ├── config.php              # Global bootstrap, .env loader & PSR-4 autoloader
│   └── database.php            # MySQL PDO connection settings
│
├── public/
│   ├── index.php               # Front Controller
│   ├── .htaccess               # Apache URL rewrite engine rules
│   ├── assets/
│   │   ├── css/app.css         # Modern design tokens & Bootstrap extensions
│   │   └── js/
│   │       ├── app.js          # Fetch API helpers, live calculations & CSRF injection
│   │       └── charts.js       # Chart.js initializers
│   └── uploads/
│       ├── .htaccess           # Security: denies script execution in upload dir
│       └── avatars/            # Uploaded employee profile images
│
├── routes/
│   └── web.php                 # Web and REST API route mappings
│
├── database/
│   ├── schema.sql              # MySQL DDL table schemas, indexes & foreign keys
│   ├── seed.sql                # Realistic seed records (Admin, 10 Employees, Leaves)
│   └── setup.php               # Automated CLI/Web migration runner
│
├── storage/
│   └── logs/
│       └── app.log             # Application error & exception log
│
├── .env.example
├── .env
├── .gitignore
├── composer.json
└── README.md
```

---

## 6. Database Schema & ER Relationships

The database `elms_db` is normalized to Third Normal Form (3NF) across 9 tables:

### Entity Relationship Diagram (ERD)

```text
       +-------------------+                  +-------------------+
       |    departments    |                  |    leave_types    |
       +---------+---------+                  +---------+---------+
                 | 1                                    | 1
                 |                                      |
                 | has many                             | categorizes
                 |                                      |
                 v N                                    v N
       +-------------------+ 1              N +-------------------+
       |     employees     +------------------+   leave_balances  |
       +----+---------+----+                  +-------------------+
          1 |         | 1
            |         |
     linked |         | submits
         to |         |
            |         v N
            |  +-------------------+
            |  |   leave_requests  |
            |  +-------------------+
            |         | N
            |         | logs
            |         v 1
            |  +-------------------+
            |  |    attendance     |
            |  +-------------------+
            |
            v 1
       +-------------------+ 1              N +-------------------+
       |       users       +------------------+   notifications   |
       +---------+---------+                  +-------------------+
                 | 1
                 | triggers
                 v N
       +-------------------+
       |   activity_logs   |
       +-------------------+
```

### Table Definitions & Key Constraints
1. **`users`:** Holds credentials (`email` UNIQUE, Bcrypt `password`), role (`admin`, `employee`), and account status.
2. **`departments`:** Categorizes staff (`name` UNIQUE).
3. **`employees`:** 1-to-1 with `users` (`user_id` UNIQUE FK). Foreign key to `departments` (`department_id` FK).
4. **`leave_types`:** Annual entitlement categories (`annual_limit`).
5. **`leave_balances`:** Compound Unique Constraint `UNIQUE(employee_id, leave_type_id, year)` ensures exactly one ledger row per type per calendar year.
6. **`leave_requests`:** Contains start/end dates, computed `total_days`, approval foreign key link `approved_by` referencing `users(id)`.
7. **`attendance`:** Compound Unique Constraint `UNIQUE(employee_id, attendance_date)` guarantees impossible double check-ins for the same day.
8. **`notifications`:** User-targeted alerts with indexed `is_read` boolean flag.
9. **`activity_logs`:** System audit trail tracking action, description, user, and client IP.

---

## 7. Installation & Setup

### Prerequisites
- **PHP 8.2 or higher** with `pdo` and `pdo_mysql` extensions enabled.
- **MySQL 8.0+** or **MariaDB 10.4+** (InnoDB storage engine).
- Apache / Nginx / or PHP Built-in Server.

### Setup Instructions

1. **Clone or navigate to the project directory:**
   ```bash
   cd elms
   ```

2. **Configure Environment Variables:**
   Copy `.env.example` to `.env` (if not already created):
   ```bash
   cp .env.example .env
   ```
   Open `.env` and verify your MySQL credentials:
   ```env
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_NAME=elms_db
   DB_USER=root
   DB_PASSWORD=
   APP_URL=http://localhost:8000
   ```

3. **Initialize & Seed the Database:**
   Run the turnkey setup script from your terminal:
   ```bash
   php database/setup.php
   ```
   *Alternatively:* Import `database/schema.sql` followed by `database/seed.sql` in phpMyAdmin, MySQL Workbench, or via MySQL CLI:
   ```bash
   mysql -u root -p < database/schema.sql
   mysql -u root -p < database/seed.sql
   ```

4. **Launch Local Development Server:**
   Using PHP's built-in web server:
   ```bash
   php -S localhost:8000 -t public
   ```
   Or open your browser and navigate to:
   ```text
   http://localhost:8000
   ```

---

## 8. Environment Configuration

All configuration is managed through `.env`:

| Key | Default | Description |
| :--- | :--- | :--- |
| `APP_NAME` | `ELMS` | Application display title |
| `APP_ENV` | `development` | Set to `production` in live environments |
| `APP_DEBUG` | `true` | Enables detailed stack traces on 500 errors |
| `APP_URL` | `http://localhost:8000` | Root URL for generating asset and redirect paths |
| `DB_HOST` | `127.0.0.1` | MySQL server host |
| `DB_PORT` | `3306` | MySQL port |
| `DB_NAME` | `elms_db` | Application database name |
| `DB_USER` | `root` | Database username |
| `DB_PASSWORD` | `""` | Database password |
| `SESSION_LIFETIME`| `7200` | Session lifetime in seconds (2 hours) |
| `APP_TIMEZONE` | `UTC` | Default PHP application timezone |

---

## 9. Demo Credentials

The database seeder automatically provisions realistic sample accounts for demonstration and technical interviews:

### 1. Administrator Account
- **Email:** `admin@elms.test`
- **Password:** `Admin@123`
- **Role:** Administrator
- **Privileges:** Full administrative privileges across staff, leaves, attendance, departments, and reports.

### 2. Employee Accounts (10 Sample Employees)
All demo employee accounts use the password: `Employee@123`

| Name | Corporate Email | Department | Designation | Code |
| :--- | :--- | :--- | :--- | :--- |
| **Alex Turner** | `alex.turner@elms.test` | Engineering | Senior Backend Engineer | `EMP-1001` |
| **Sarah Jenkins** | `sarah.jenkins@elms.test` | Engineering | Frontend UI/UX Specialist | `EMP-1002` |
| **Michael Chen** | `michael.chen@elms.test` | Engineering | DevOps & Cloud Engineer | `EMP-1003` |
| **Emily Watson** | `emily.watson@elms.test` | Human Resources | HR Business Partner | `EMP-1004` |
| **David Miller** | `david.miller@elms.test` | Finance & Accounts| Chief Financial Analyst | `EMP-1005` |
| **Jessica Taylor**| `jessica.taylor@elms.test` | Marketing & Sales | Product Marketing Lead | `EMP-1006` |
| **Robert Wilson** | `robert.wilson@elms.test` | Operations | Operations Manager | `EMP-1007` |
| **Olivia Martinez**| `olivia.martinez@elms.test`| Engineering | QA Automation Engineer | `EMP-1008` |
| **James Anderson**| `james.anderson@elms.test`| Finance & Accounts| Senior Accountant | `EMP-1009` |
| **Sophia Thomas** | `sophia.thomas@elms.test` | Marketing & Sales | Digital Growth Specialist| `EMP-1010` |

> *Tip:* The login screen includes quick **Auto-fill buttons** to immediately populate credentials for both Admin and Employee with one click.

---

## 10. REST API Documentation

ELMS exposes private RESTful API endpoints formatted in JSON. Endpoints enforce session-based authorization and return standardized HTTP status codes.

| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/employees` | Paginated list of employees with search and department filtering | Admin |
| `GET` | `/api/employees/{id}` | Detailed employee dossier | Admin or Owner |
| `POST`| `/api/employees` | Create employee record, login account, and leave balances | Admin |
| `PUT` | `/api/employees/{id}` | Update employee profile fields | Admin |
| `DELETE`| `/api/employees/{id}` | Deactivate / soft delete employee | Admin |
| `GET` | `/api/leaves` | List leave requests (Filtered to user if employee) | Admin / Employee |
| `POST`| `/api/leaves` | Submit new leave application | Employee |
| `PUT` | `/api/leaves/{id}` | Approve (`action=approve`) or reject (`action=reject`) leave | Admin |
| `GET` | `/api/attendance/today`| Today's aggregate attendance summary metrics | Admin |

### Standard HTTP Status Codes Used:
- `200 OK`: Request succeeded.
- `201 Created`: Resource created successfully.
- `400 Bad Request`: Missing mandatory parameters or invalid format.
- `401 Unauthorized`: User is unauthenticated.
- `403 Forbidden`: Authenticated user lacks permission for target resource.
- `404 Not Found`: Target entity does not exist.
- `419 Authentication Timeout`: CSRF security token mismatch or expired.
- `422 Unprocessable Entity`: Business logic failure (e.g. insufficient leave days or date overlap).
- `500 Internal Server Error`: Unhandled database or system exception.

---

## 11. Security Architecture

### 1. SQL Injection Protection
Raw user inputs are **never** concatenated into SQL queries. The application exclusively uses **PDO prepared statements** with parameter binding (`:email`, `:employee_id`). Emulated prepares are explicitly disabled (`PDO::ATTR_EMULATE_PREPARES => false`), forcing the MySQL database engine to parse SQL structures separately from data parameters.

### 2. Cross-Site Scripting (XSS) Prevention
All dynamic view outputs pass through the `ViewHelper::e()` escaping wrapper, which calls:
```php
htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
```
This converts special characters (`<`, `>`, `"`, `'`, `&`) into inert HTML entities before outputting to the browser.

### 3. Cross-Site Request Forgery (CSRF) Tokens
Every state-altering request (`POST`, `PUT`, `DELETE`) is guarded by a cryptographically secure 256-bit token (`bin2hex(random_bytes(32))`). Forms automatically include `ViewHelper::csrf()`, and AJAX fetch requests transmit the token via the `X-CSRF-TOKEN` HTTP header.

### 4. Password Security & Brute-Force Throttling
- User passwords are encrypted using `password_hash($password, PASSWORD_DEFAULT)` and authenticated via constant-time `password_verify()`.
- Failed login attempts are tracked per session. Five consecutive failed attempts trigger an automated **5-minute security cooldown lockout**.

### 5. Session Security
- Session cookies are hardened: `httponly = true` (inaccessible to malicious JavaScript), `samesite = 'Lax'` (mitigates cross-site request leakage), and strict cookie mode enabled.
- Upon successful login, `session_regenerate_id(true)` is immediately invoked to neutralize **Session Fixation** vectors.

### 6. Secure File Uploads
Avatar uploads undergo multiple validation checkpoints in `FileUploader`:
- Strict allowlist of extensions: `jpg`, `jpeg`, `png`, `webp`.
- Deep binary MIME-type verification using PHP `finfo_file(FILEINFO_MIME_TYPE)`.
- File size capped at 2MB.
- Original client filenames are discarded and replaced with randomized UUID strings (`bin2hex(random_bytes(16)) . '.' . $extension`).
- An `.htaccess` configuration inside `public/uploads/` denies execution of PHP and CGI scripts.

---

## 12. ACID Database Transactions

When an administrator approves a leave application, multiple database tables must be updated simultaneously. If any step fails midway (e.g., database constraint failure or connection drop), updating the leave request without updating the balance would cause severe data corruption.

ELMS uses **InnoDB Transactions** inside `LeaveService::approveLeave()`:

```text
1. $pdo->beginTransaction();
2. SELECT ... FROM leave_requests WHERE id = :id FOR UPDATE (Row-level pessimistic lock)
3. SELECT ... FROM leave_balances WHERE employee_id = ... FOR UPDATE
4. Validate: remaining_days >= total_days
5. UPDATE leave_requests SET status = 'approved', approved_by = :admin, approved_at = NOW();
6. UPDATE leave_balances SET used_days = used_days + :days, remaining_days = remaining_days - :days;
7. INSERT INTO notifications (Notify employee);
8. INSERT INTO activity_logs (Audit trail);
9. $pdo->commit();
-- On any exception:
10. $pdo->rollBack();
```

---

## 13. Testing & Verification Guide

Verify the system using this structured manual testing checklist:

| Test Scenario | Steps | Expected Result |
| :--- | :--- | :--- |
| **Authentication: Valid** | Enter `admin@elms.test` / `Admin@123` | Redirects to `/admin/dashboard`, session regenerated. |
| **Authentication: Invalid** | Enter incorrect password 5 times | Throttle lock initiates for 300 seconds. |
| **Authorization Guard** | Log in as employee, navigate to `/admin/employees` | Blocked by `AdminMiddleware`, 403 Forbidden served. |
| **CSRF Defense** | Remove hidden `_csrf_token` and submit form | Request halts with 419 token mismatch error. |
| **Leave Date Validation** | Select end date earlier than start date | Rejected with clear server-side validation error. |
| **Leave Overlap** | Apply for dates already booked under another request | Blocked with "You already have an existing pending or approved leave". |
| **Leave Balance Limit** | Request 20 days when only 10 remain | Blocked with "Insufficient leave balance". |
| **Atomic Approval** | Admin approves leave via table button | Status switches to Approved, employee balance is deducted in database. |
| **Attendance Duplicate** | Mark attendance twice for employee on same date | Handled cleanly via `ON DUPLICATE KEY UPDATE` without fatal SQL crashes. |
| **Safe Profile Edits** | Employee edits phone and avatar | Profile updates; salary and role remain unchanged. |
| **CSV Export** | Click "Export to CSV" on Attendance Report | Browser downloads clean `.csv` spreadsheet. |

---

## 14. How to Pitch This Project in an Interview

When asked *"Can you walk me through a Core PHP project you built recently?"*, use this talking track:

> *"I engineered an Employee & Leave Management System called ELMS using Core PHP 8.2 and MySQL. Rather than relying on heavyweight third-party frameworks, I intentionally built it from scratch using Object-Oriented PHP and a custom MVC architecture to demonstrate my mastery of backend fundamentals.*
>
> *Key technical highlights include:*
> 1. *A clean Front Controller and regular-expression routing engine.*
> 2. *Strict PDO prepared statement database access with zero SQL injection risk.*
> 3. *ACID database transactions using InnoDB row locks (`FOR UPDATE`) to guarantee that leave status transitions and balance quota calculations are atomic.*
> 4. *A robust security baseline featuring CSRF protection tokens, XSS escaping, secure MIME-inspected file uploads, and session fixation defense.*
> 5. *A RESTful JSON API layer alongside an interactive Bootstrap 5 and Chart.js UI communicating via the Fetch API.*
>
> *The result is a clean, scalable application that proves I understand what frameworks do under the hood."*

---

## 15. 35 PHP Developer Interview Questions & Answers From This Project

### 1. Why use PHP for enterprise web applications?
PHP is a mature, high-performance, server-side language built specifically for web development. Modern PHP 8.2+ introduces strict typing, JIT compilation, match expressions, and typed properties. In ELMS, PHP powers our routing, session management, and business logic efficiently with minimal memory overhead.

### 2. What is MVC architecture and how is it used in ELMS?
Model-View-Controller separates an application into:
- **Models (`app/models/`):** Data logic and database queries (`Employee`, `LeaveRequest`).
- **Views (`app/views/`):** HTML rendering and presentation.
- **Controllers (`app/controllers/`):** Orchestrate request handling, validation, and responses.
In ELMS, no database queries exist in view files, and no HTML exists in models.

### 3. Why did you choose PDO over MySQLi?
PDO (PHP Data Objects) offers:
- Database driver abstraction (easily switched to PostgreSQL or SQLite).
- Named parameter binding (`:email`, `:id`), making complex queries significantly more readable.
- Default object mapping and clean exception handling (`PDO::ERRMODE_EXCEPTION`).
ELMS uses a PDO Singleton wrapper in `app/models/Database.php`.

### 4. What is a prepared statement and how does it prevent SQL Injection?
A prepared statement compiles SQL structure on the database server before parameters are inserted. The database treats parameters strictly as data literals, never executable SQL commands. In ELMS, 100% of queries use `$pdo->prepare()` with parameter arrays, making SQL injection impossible.

### 5. What are PHP Sessions and how do they work?
Sessions maintain state across stateless HTTP requests. When `session_start()` executes, PHP generates a unique `PHPSESSID`, sends it to the browser as a cookie, and stores session data server-side. ELMS uses sessions for storing authenticated user data and flash messages.

### 6. What is the difference between Cookies and Sessions?
- **Cookies:** Stored client-side in the browser. Can be inspected and tampered with by users.
- **Sessions:** Data stored server-side. The client only holds an opaque session identifier token.
In ELMS, sensitive authentication data is stored exclusively in server-side session memory.

### 7. Why use password_hash() instead of MD5 or SHA1?
MD5 and SHA1 are fast general-purpose checksum algorithms vulnerable to collision and rainbow table attacks. `password_hash()` uses adaptive, slow hashing algorithms (Bcrypt / Argon2) with automatic salting and configurable computational cost. In ELMS, user passwords are encrypted using `password_hash($pwd, PASSWORD_DEFAULT)`.

### 8. What is the difference between GET and POST HTTP methods?
- **GET:** Appends query parameters to the URL string. Should be idempotent and used strictly for data retrieval (e.g. `/admin/employees?search=Alex`).
- **POST:** Sends data inside the HTTP request body. Used for state mutations (e.g. creating employees, submitting leave).

### 9. What are the four core pillars of Object-Oriented Programming (OOP)?
1. **Encapsulation:** Bundling data and methods into classes with visibility controls (`private`, `protected`, `public`).
2. **Inheritance:** Deriving classes from parent classes (e.g. all models extend `Model`, all controllers extend `Controller`).
3. **Polymorphism:** Subclasses implementing shared interfaces or overriding methods differently.
4. **Abstraction:** Hiding low-level implementation details behind clean interfaces (e.g. `Database::getConnection()`).

### 10. How is Encapsulation demonstrated in ELMS?
In `Database.php`, the PDO connection `$connection` is marked `private`, preventing external scripts from corrupting the connection state. Callers can only access it via public methods (`getConnection()`).

### 11. What is an Abstract Class and where is it used here?
An abstract class cannot be instantiated directly and serves as a base blueprint. In ELMS, `Model.php` and `Controller.php` are abstract classes providing common query and rendering utilities to their concrete child classes.

### 12. What is an Interface and where is it used?
An interface defines a public method signature contract without implementation. In ELMS, `Middleware.php` defines `handle(Request $request, Response $response): void`, which all middleware classes (`AuthMiddleware`, `AdminMiddleware`) must implement.

### 13. What is a Constructor in PHP?
A constructor (`__construct()`) is a magic method executed automatically upon object instantiation. In ELMS, `Controller::__construct()` initializes the `Request` and `Response` objects on every dispatched controller.

### 14. What is the Singleton Pattern and why is it used for Database?
A Singleton guarantees that a class has only one instance throughout the request lifecycle. In ELMS, `Database::getInstance()` ensures the application opens only **one** MySQL database connection per request, preventing socket exhaustion.

### 15. What is the difference between INNER JOIN and LEFT JOIN?
- **INNER JOIN:** Returns records only when there is a match in both tables.
- **LEFT JOIN:** Returns all records from the left table, and matching records from the right table (or NULL if no match).
In ELMS, `Employee::getAllWithDetails()` uses a `LEFT JOIN` on `departments` so employees without an assigned department still appear in directory lists.

### 16. What is a Primary Key and Foreign Key?
- **Primary Key:** A column uniquely identifying each record in a table (e.g. `employees.id`).
- **Foreign Key:** A column referencing the primary key of another table, enforcing referential integrity (e.g. `leave_requests.employee_id` references `employees.id`).

### 17. What is Database Normalization and why is 3NF important?
Normalization organizes database tables to eliminate redundant data and avoid insertion, update, and deletion anomalies. In ELMS, departments and leave categories exist in dedicated lookup tables rather than repeating text names across thousands of employee rows.

### 18. What is a Composite Unique Key?
A constraint ensuring that the combination of multiple columns is unique across a table. In ELMS:
- `UNIQUE(employee_id, attendance_date)` prevents duplicate attendance records for the same employee on the same date.
- `UNIQUE(employee_id, leave_type_id, year)` guarantees exactly one leave quota ledger per employee per leave category per year.

### 19. What is Database Indexing and why is it used here?
An index is a database data structure (B-Tree) that accelerates data retrieval queries. In ELMS, indexes are applied to `status`, `created_at`, `attendance_date`, and foreign key columns, allowing dashboard counts to run in milliseconds.

### 20. What is an ACID Database Transaction?
A transaction groups multiple SQL operations into a single logical unit guaranteeing:
- **Atomicity:** All operations succeed, or all are rolled back.
- **Consistency:** Database transitions between valid states.
- **Isolation:** Concurrent transactions do not interfere with each other.
- **Durability:** Committed transactions persist even across server crashes.

### 21. What happens during a COMMIT and ROLLBACK?
- **`COMMIT`:** Permanently writes all pending changes within a transaction to the database.
- **`ROLLBACK`:** Aborts the transaction and reverts all changes back to the state before `beginTransaction()`.
In ELMS, `LeaveService::approveLeave()` rolls back all updates if balance deduction fails.

### 22. What is Pessimistic Locking (`FOR UPDATE`)?
`SELECT ... FOR UPDATE` locks selected rows until the surrounding transaction commits. In ELMS, we lock the `leave_requests` and `leave_balances` rows during approval to prevent concurrent administrators from approving duplicate requests simultaneously.

### 23. What is a REST API?
Representational State Transfer is an architectural style utilizing standard HTTP methods (`GET`, `POST`, `PUT`, `DELETE`) to manipulate stateless resources identified by URIs. In ELMS, `/api/employees` and `/api/leaves` return JSON responses with standard status codes.

### 24. What is the difference between Authentication and Authorization?
- **Authentication:** Verifying *who* a user is (e.g. logging in with email and password).
- **Authorization:** Verifying *what* an authenticated user is permitted to do (e.g. `AdminMiddleware` checking if `role === 'admin'`).

### 25. What is Cross-Site Request Forgery (CSRF) and how does ELMS stop it?
CSRF tricks an authenticated user's browser into executing unauthorized actions on a trusted site. ELMS generates a random 256-bit token stored in the session, verifies it on every POST/PUT/DELETE request, and rejects any submission lacking a matching token with HTTP 419.

### 26. What is Cross-Site Scripting (XSS) and how is it mitigated?
XSS occurs when malicious scripts are injected into web pages and executed in victim browsers. ELMS sanitizes all output before rendering using `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` via the `ViewHelper::e()` function.

### 27. What is Session Fixation and how does ELMS prevent it?
Session fixation occurs when an attacker forces a victim to use a known session ID, allowing the attacker to hijack the session post-login. ELMS calls `session_regenerate_id(true)` immediately after successful password verification.

### 28. How does ELMS harden file uploads against malicious exploits?
1. Checks file extension against an allowlist (`jpg`, `jpeg`, `png`, `webp`).
2. Validates genuine binary MIME-types using `finfo_file()`.
3. Discards original client filenames and generates random UUIDs.
4. Stores files in a directory configured with `.htaccess` to block PHP script execution.

### 29. What is AJAX and how is it used in this project?
Asynchronous JavaScript and XML (Fetch API) allows web pages to send and receive data in the background without reloading the page. In ELMS, leave approvals, rejections, and notification read toggles use `fetch()` with JSON responses to update the DOM seamlessly.

### 30. Why is Server-Side Validation mandatory even if Client-Side Validation exists?
Client-side JavaScript validation improves user experience, but can be easily bypassed using cURL, Postman, or disabling JavaScript. In ELMS, `Validator.php` strictly verifies all incoming data on the server before database interaction.

### 31. What is Method Spoofing and why is it needed?
Standard HTML forms only support `GET` and `POST` methods. For RESTful conventions (`PUT`, `DELETE`), ELMS inspects hidden inputs (`_method`) or `X-HTTP-Method-Override` headers in `Request::parseMethod()` to route requests properly.

### 32. What is Soft Deletion and why is it used for Employees?
Instead of executing hard `DELETE FROM employees`, soft deletion sets `status = 'inactive'`. This preserves foreign key relationships, historical payroll, audit logs, and attendance timesheets.

### 33. How does ELMS handle unhandled exceptions in production?
In `App::handleException()`, full error details and stack traces are written to `storage/logs/app.log`. Users are shown a friendly branded `500 Server Error` page. When `APP_DEBUG=false`, sensitive database credentials and queries are never exposed.

### 34. What is the Front Controller design pattern?
A centralized pattern where all HTTP requests are directed through a single file (`public/index.php`). This provides a single choke point for configuration loading, session startup, security headers, routing, and exception handling.

### 35. How does ELMS implement PSR-4 Autoloading without third-party libraries?
In `config/config.php`, `spl_autoload_register()` maps the `App\` namespace prefix to the `app/` filesystem directory, replacing namespace separators (`\`) with directory separators (`/`). When `new App\controllers\EmployeeController()` is called, PHP loads the file automatically on demand.

---

## 16. License

This project is open-source software licensed under the **MIT License**.
