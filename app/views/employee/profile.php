<div class="mb-4">
    <h3 class="fw-bold mb-0 text-dark">My Profile & Security Settings</h3>
    <p class="text-muted small">Manage contact details, profile picture, and account credentials</p>
</div>

<div class="row g-4">
    <!-- Left Column: Read-Only Official Employment Information -->
    <div class="col-lg-4">
        <div class="card p-4 text-center">
            <?php if (!empty($employee['profile_image'])): ?>
                <img src="<?= \App\helpers\ViewHelper::url($employee['profile_image']) ?>" class="avatar-lg rounded-circle mx-auto mb-3 shadow-sm" alt="Avatar">
            <?php else: ?>
                <div class="avatar-lg bg-primary text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm">
                    <?= strtoupper(substr($employee['first_name'], 0, 1)) ?>
                </div>
            <?php endif; ?>

            <h5 class="fw-bold text-dark mb-0"><?= \App\helpers\ViewHelper::e($employee['first_name'] . ' ' . $employee['last_name']) ?></h5>
            <span class="text-primary small fw-semibold"><?= \App\helpers\ViewHelper::e($employee['designation']) ?></span>
            <div class="mt-2 mb-3">
                <span class="badge bg-light text-dark font-monospace border"><?= \App\helpers\ViewHelper::e($employee['employee_code']) ?></span>
            </div>

            <div class="alert alert-light border text-start small mb-0">
                <div class="d-flex align-items-center gap-1 text-muted fw-semibold mb-2" style="font-size: 0.72rem;">
                    <i class="bi bi-lock-fill text-warning"></i> RESTRICTED CORPORATE RECORDS
                </div>
                <div class="py-1 d-flex justify-content-between border-bottom">
                    <span class="text-muted">Department:</span>
                    <strong><?= \App\helpers\ViewHelper::e($employee['department_name'] ?? '—') ?></strong>
                </div>
                <div class="py-1 d-flex justify-content-between border-bottom">
                    <span class="text-muted">Work Email:</span>
                    <strong><?= \App\helpers\ViewHelper::e($employee['email'] ?? '—') ?></strong>
                </div>
                <div class="py-1 d-flex justify-content-between border-bottom">
                    <span class="text-muted">Compensation:</span>
                    <strong><?= \App\helpers\ViewHelper::formatCurrency($employee['salary']) ?></strong>
                </div>
                <div class="py-1 d-flex justify-content-between">
                    <span class="text-muted">Joining Date:</span>
                    <strong><?= \App\helpers\ViewHelper::formatDate($employee['joining_date']) ?></strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Editable Contact Information & Password Change -->
    <div class="col-lg-8">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-3" id="profileTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-semibold" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-pane" type="button">
                    <i class="bi bi-person-gear me-1"></i> Update Contact Details
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="security-tab" data-bs-toggle="tab" data-bs-target="#security-pane" type="button">
                    <i class="bi bi-shield-lock me-1"></i> Change Password
                </button>
            </li>
        </ul>

        <div class="tab-content" id="profileTabsContent">
            <!-- Tab 1: Editable Safe Fields -->
            <div class="tab-pane fade show active" id="contact-pane">
                <div class="card p-4">
                    <h5 class="fw-bold text-primary mb-3">Self-Service Profile Information</h5>
                    <p class="text-muted small mb-4">You may update your contact phone, residential address, and profile photo. Official company credentials cannot be modified without HR approval.</p>

                    <form action="<?= \App\helpers\ViewHelper::url('employee/profile') ?>" method="POST" enctype="multipart/form-data">
                        <?= \App\helpers\ViewHelper::csrf() ?>

                        <div class="mb-3">
                            <label for="phone" class="form-label small fw-semibold">Phone Number</label>
                            <input type="text" class="form-control form-control-sm" id="phone" name="phone" value="<?= \App\helpers\ViewHelper::e($employee['phone'] ?? '') ?>" placeholder="+1-555-0100">
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label small fw-semibold">Home / Mailing Address</label>
                            <textarea class="form-control form-control-sm" id="address" name="address" rows="3"><?= \App\helpers\ViewHelper::e($employee['address'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="profile_image" class="form-label small fw-semibold">Upload New Profile Photo</label>
                            <input type="file" class="form-control form-control-sm" id="profile_image" name="profile_image" accept="image/png,image/jpeg,image/webp">
                            <div class="form-text" style="font-size: 0.75rem;">Supported file formats: PNG, JPG, WebP. Maximum size: 2MB.</div>
                        </div>

                        <button type="submit" class="btn btn-primary px-4 btn-sm fw-semibold">
                            <i class="bi bi-save2 me-1"></i> Save Profile Details
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tab 2: Change Password -->
            <div class="tab-pane fade" id="security-pane">
                <div class="card p-4">
                    <h5 class="fw-bold text-primary mb-3">Account Security & Credentials</h5>
                    <p class="text-muted small mb-4">Ensure your password is at least 6 characters long and is not shared with anyone.</p>

                    <form action="<?= \App\helpers\ViewHelper::url('employee/change-password') ?>" method="POST">
                        <?= \App\helpers\ViewHelper::csrf() ?>

                        <div class="mb-3">
                            <label for="current_password" class="form-label small fw-semibold">Current Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control form-control-sm" id="current_password" name="current_password" required>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label small fw-semibold">New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control form-control-sm" id="new_password" name="new_password" minlength="6" required>
                        </div>

                        <div class="mb-4">
                            <label for="confirm_password" class="form-label small fw-semibold">Confirm New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control form-control-sm" id="confirm_password" name="confirm_password" minlength="6" required>
                        </div>

                        <button type="submit" class="btn btn-warning px-4 btn-sm fw-semibold">
                            <i class="bi bi-key-fill me-1"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
