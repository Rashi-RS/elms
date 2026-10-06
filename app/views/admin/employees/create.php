<div class="mb-4">
    <div class="d-flex align-items-center gap-2 mb-1">
        <a href="<?= \App\helpers\ViewHelper::url('admin/employees') ?>" class="text-secondary text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i> Back to Employee Directory
        </a>
    </div>
    <h3 class="fw-bold mb-0 text-dark">Onboard New Employee</h3>
    <p class="text-muted small">Register personnel details, configure login account, and auto-initialize annual leave quotas</p>
</div>

<form action="<?= \App\helpers\ViewHelper::url('admin/employees') ?>" method="POST" enctype="multipart/form-data">
    <?= \App\helpers\ViewHelper::csrf() ?>

    <div class="row g-4">
        <!-- Left Column: Personal Information & Credentials -->
        <div class="col-lg-6">
            <div class="card p-4 h-100">
                <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-person-lines-fill me-2"></i>Personal & Account Credentials</h5>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label small fw-semibold">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="first_name" name="first_name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="form-label small fw-semibold">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="last_name" name="last_name" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold">Corporate Email (Login Username) <span class="text-danger">*</span></label>
                    <input type="email" class="form-control form-control-sm" id="email" name="email" placeholder="employee@elms.test" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label small fw-semibold">Initial Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control form-control-sm" id="password" name="password" placeholder="At least 6 characters" required>
                    <div class="form-text" style="font-size: 0.75rem;">Password will be hashed using Bcrypt/Argon2.</div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="phone" class="form-label small fw-semibold">Contact Phone</label>
                        <input type="text" class="form-control form-control-sm" id="phone" name="phone" placeholder="+1-555-0100">
                    </div>
                    <div class="col-md-6">
                        <label for="gender" class="form-label small fw-semibold">Gender <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="gender" name="gender" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="date_of_birth" class="form-label small fw-semibold">Date of Birth</label>
                    <input type="date" class="form-control form-control-sm" id="date_of_birth" name="date_of_birth">
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label small fw-semibold">Residential Address</label>
                    <textarea class="form-control form-control-sm" id="address" name="address" rows="2" placeholder="Street, city, postal code..."></textarea>
                </div>

                <div class="mb-0">
                    <label for="profile_image" class="form-label small fw-semibold">Profile Photo (Avatar)</label>
                    <input type="file" class="form-control form-control-sm" id="profile_image" name="profile_image" accept="image/png,image/jpeg,image/webp">
                    <div class="form-text" style="font-size: 0.75rem;">Accepted formats: PNG, JPG, WebP. Max 2MB.</div>
                </div>
            </div>
        </div>

        <!-- Right Column: Employment & Compensation Details -->
        <div class="col-lg-6">
            <div class="card p-4 h-100">
                <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-briefcase-fill me-2"></i>Employment & Designation</h5>

                <div class="mb-3">
                    <label for="employee_code" class="form-label small fw-semibold">Employee ID / Code <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm font-monospace" id="employee_code" name="employee_code" value="<?= \App\helpers\ViewHelper::e($suggestedCode) ?>" required>
                    <div class="form-text" style="font-size: 0.75rem;">Unique personnel identifier.</div>
                </div>

                <div class="mb-3">
                    <label for="department_id" class="form-label small fw-semibold">Assigned Department <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="department_id" name="department_id" required>
                        <option value="">-- Choose Department --</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>"><?= \App\helpers\ViewHelper::e($dept['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="designation" class="form-label small fw-semibold">Job Designation / Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm" id="designation" name="designation" placeholder="e.g. Senior Software Engineer" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="joining_date" class="form-label small fw-semibold">Joining Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" id="joining_date" name="joining_date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="salary" class="form-label small fw-semibold">Annual Salary ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control form-control-sm" id="salary" name="salary" placeholder="75000.00" required>
                    </div>
                </div>

                <!-- Automated Provisioning Notice -->
                <div class="alert alert-info border-0 p-3 mt-4 small">
                    <h6 class="fw-bold mb-1"><i class="bi bi-gear-wide-connected me-1"></i> Automated Multi-Table Provisioning</h6>
                    Upon submission, a database transaction will automatically:
                    <ul class="mb-0 ps-3 mt-1">
                        <li>Register the core employee record.</li>
                        <li>Create a secure user login account with Bcrypt password.</li>
                        <li>Initialize all active annual leave balances for <?= date('Y') ?>.</li>
                        <li>Log an immutable audit trail entry.</li>
                    </ul>
                </div>

                <div class="mt-auto pt-3">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        <i class="bi bi-check-circle-fill me-1"></i> Register Employee & Create Account
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
