<div class="mb-4">
    <div class="d-flex align-items-center gap-2 mb-1">
        <a href="<?= \App\helpers\ViewHelper::url("admin/employees/{$employee['id']}") ?>" class="text-secondary text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i> Back to Employee Profile
        </a>
    </div>
    <h3 class="fw-bold mb-0 text-dark">Edit Employee Details</h3>
    <p class="text-muted small">Update personnel info, compensation, department assignment, or credentials for <?= \App\helpers\ViewHelper::e($employee['first_name'] . ' ' . $employee['last_name']) ?></p>
</div>

<form action="<?= \App\helpers\ViewHelper::url("admin/employees/{$employee['id']}/edit") ?>" method="POST" enctype="multipart/form-data">
    <?= \App\helpers\ViewHelper::csrf() ?>

    <div class="row g-4">
        <!-- Personal Column -->
        <div class="col-lg-6">
            <div class="card p-4 h-100">
                <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-person-lines-fill me-2"></i>Personal Info</h5>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label small fw-semibold">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="first_name" name="first_name" value="<?= \App\helpers\ViewHelper::e($employee['first_name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="form-label small fw-semibold">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="last_name" name="last_name" value="<?= \App\helpers\ViewHelper::e($employee['last_name']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold">Email (User Login) <span class="text-danger">*</span></label>
                    <input type="email" class="form-control form-control-sm" id="email" name="email" value="<?= \App\helpers\ViewHelper::e($employee['email'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label small fw-semibold">Reset Password (leave empty to keep current)</label>
                    <input type="password" class="form-control form-control-sm" id="password" name="password" placeholder="••••••••">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="phone" class="form-label small fw-semibold">Phone Number</label>
                        <input type="text" class="form-control form-control-sm" id="phone" name="phone" value="<?= \App\helpers\ViewHelper::e($employee['phone'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="gender" class="form-label small fw-semibold">Gender <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="gender" name="gender" required>
                            <option value="male" <?= ($employee['gender'] === 'male') ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= ($employee['gender'] === 'female') ? 'selected' : '' ?>>Female</option>
                            <option value="other" <?= ($employee['gender'] === 'other') ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="date_of_birth" class="form-label small fw-semibold">Date of Birth</label>
                    <input type="date" class="form-control form-control-sm" id="date_of_birth" name="date_of_birth" value="<?= $employee['date_of_birth'] ?? '' ?>">
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label small fw-semibold">Residential Address</label>
                    <textarea class="form-control form-control-sm" id="address" name="address" rows="2"><?= \App\helpers\ViewHelper::e($employee['address'] ?? '') ?></textarea>
                </div>

                <div class="mb-0">
                    <label for="profile_image" class="form-label small fw-semibold">Update Profile Image</label>
                    <input type="file" class="form-control form-control-sm" id="profile_image" name="profile_image" accept="image/png,image/jpeg,image/webp">
                </div>
            </div>
        </div>

        <!-- Employment Column -->
        <div class="col-lg-6">
            <div class="card p-4 h-100">
                <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-briefcase-fill me-2"></i>Employment Details</h5>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Employee ID / Code</label>
                    <input type="text" class="form-control form-control-sm font-monospace bg-light" value="<?= \App\helpers\ViewHelper::e($employee['employee_code']) ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="department_id" class="form-label small fw-semibold">Department <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="department_id" name="department_id" required>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>" <?= ($employee['department_id'] == $dept['id']) ? 'selected' : '' ?>>
                                <?= \App\helpers\ViewHelper::e($dept['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="designation" class="form-label small fw-semibold">Designation <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm" id="designation" name="designation" value="<?= \App\helpers\ViewHelper::e($employee['designation']) ?>" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="joining_date" class="form-label small fw-semibold">Joining Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" id="joining_date" name="joining_date" value="<?= $employee['joining_date'] ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="salary" class="form-label small fw-semibold">Salary ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control form-control-sm" id="salary" name="salary" value="<?= $employee['salary'] ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="status" class="form-label small fw-semibold">Account Status <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="status" name="status" required>
                        <option value="active" <?= ($employee['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($employee['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                        <option value="terminated" <?= ($employee['status'] === 'terminated') ? 'selected' : '' ?>>Terminated</option>
                    </select>
                </div>

                <div class="mt-auto">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        <i class="bi bi-save2 me-1"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
