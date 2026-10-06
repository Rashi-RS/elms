<div class="mb-4">
    <div class="d-flex align-items-center gap-2 mb-1">
        <a href="<?= \App\helpers\ViewHelper::url('admin/employees') ?>" class="text-secondary text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i> Back to Employee Directory
        </a>
    </div>
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
        <h3 class="fw-bold mb-0 text-dark"><?= \App\helpers\ViewHelper::e($employee['first_name'] . ' ' . $employee['last_name']) ?></h3>
        <div class="d-flex gap-2">
            <a href="<?= \App\helpers\ViewHelper::url("admin/employees/{$employee['id']}/edit") ?>" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-pencil me-1"></i> Edit Details
            </a>
            <form action="<?= \App\helpers\ViewHelper::url("admin/employees/{$employee['id']}/toggle-status") ?>" method="POST" class="d-inline" onsubmit="return confirm('Change status for this employee?');">
                <?= \App\helpers\ViewHelper::csrf() ?>
                <button type="submit" class="btn btn-outline-warning btn-sm">
                    <i class="bi bi-power me-1"></i> Toggle Status
                </button>
            </form>
            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteEmployeeModal">
                <i class="bi bi-trash3-fill me-1"></i> Remove Employee
            </button>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Dossier Card & Personal Information -->
    <div class="col-lg-4">
        <!-- Main Profile Summary Card -->
        <div class="card p-4 text-center mb-4">
            <?php if (!empty($employee['profile_image'])): ?>
                <img src="<?= \App\helpers\ViewHelper::url($employee['profile_image']) ?>" class="avatar-lg rounded-circle mx-auto mb-3 shadow-sm" alt="Avatar">
            <?php else: ?>
                <div class="avatar-lg bg-primary text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm">
                    <?= strtoupper(substr($employee['first_name'], 0, 1)) ?>
                </div>
            <?php endif; ?>

            <h5 class="fw-bold text-dark mb-0"><?= \App\helpers\ViewHelper::e($employee['first_name'] . ' ' . $employee['last_name']) ?></h5>
            <span class="text-primary fw-medium small mb-2"><?= \App\helpers\ViewHelper::e($employee['designation']) ?></span>
            <div class="mb-3">
                <span class="badge bg-light text-dark font-monospace border me-1"><?= \App\helpers\ViewHelper::e($employee['employee_code']) ?></span>
                <?= \App\helpers\ViewHelper::statusBadge($employee['status']) ?>
            </div>

            <hr class="my-2">

            <!-- Work & Employment Info -->
            <div class="text-start small">
                <div class="py-2 d-flex justify-content-between border-bottom">
                    <span class="text-muted"><i class="bi bi-diagram-3 me-1"></i>Department:</span>
                    <strong class="text-dark"><?= \App\helpers\ViewHelper::e($employee['department_name'] ?? 'Unassigned') ?></strong>
                </div>
                <div class="py-2 d-flex justify-content-between border-bottom">
                    <span class="text-muted"><i class="bi bi-envelope me-1"></i>Corporate Email:</span>
                    <strong class="text-dark"><?= \App\helpers\ViewHelper::e($employee['email'] ?? 'N/A') ?></strong>
                </div>
                <div class="py-2 d-flex justify-content-between border-bottom">
                    <span class="text-muted"><i class="bi bi-telephone me-1"></i>Phone:</span>
                    <strong class="text-dark"><?= \App\helpers\ViewHelper::e($employee['phone'] ?? '—') ?></strong>
                </div>
                <div class="py-2 d-flex justify-content-between border-bottom">
                    <span class="text-muted"><i class="bi bi-calendar-event me-1"></i>Joining Date:</span>
                    <strong class="text-dark"><?= \App\helpers\ViewHelper::formatDate($employee['joining_date']) ?></strong>
                </div>
                <div class="py-2 d-flex justify-content-between border-bottom">
                    <span class="text-muted"><i class="bi bi-cash-stack me-1"></i>Salary:</span>
                    <strong class="text-dark"><?= \App\helpers\ViewHelper::formatCurrency($employee['salary']) ?></strong>
                </div>
                <div class="py-2 d-flex justify-content-between">
                    <span class="text-muted"><i class="bi bi-clock-history me-1"></i>Last Active:</span>
                    <span class="text-muted"><?= \App\helpers\ViewHelper::formatDate($employee['last_login'], 'd M Y, h:i A') ?></span>
                </div>
            </div>
        </div>

        <!-- Personal Info Card -->
        <div class="card p-3 mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-lines-fill text-primary me-2"></i>Personal Information</h6>
            <div class="small">
                <div class="py-2 d-flex justify-content-between border-bottom">
                    <span class="text-muted"><i class="bi bi-gender-ambiguous me-1"></i>Gender:</span>
                    <span class="text-dark fw-semibold text-capitalize"><?= \App\helpers\ViewHelper::e($employee['gender'] ?? 'Not Specified') ?></span>
                </div>
                <div class="py-2 d-flex justify-content-between border-bottom">
                    <span class="text-muted"><i class="bi bi-cake2 me-1"></i>Date of Birth:</span>
                    <span class="text-dark fw-semibold">
                        <?php if (!empty($employee['date_of_birth'])): ?>
                            <?= \App\helpers\ViewHelper::formatDate($employee['date_of_birth']) ?>
                            <?php 
                                $dob = new \DateTime($employee['date_of_birth']);
                                $now = new \DateTime();
                                $age = $now->diff($dob)->y;
                            ?>
                            <small class="text-muted">(<?= $age ?> yrs)</small>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </span>
                </div>
                <div class="pt-2">
                    <span class="text-muted d-block mb-1"><i class="bi bi-geo-alt me-1"></i>Residential Address:</span>
                    <div class="p-2 bg-light rounded text-dark border">
                        <?= !empty($employee['address']) ? nl2br(\App\helpers\ViewHelper::e($employee['address'])) : '<span class="text-muted font-italic">No address provided.</span>' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Leave Balances Ledger & Recent Activities -->
    <div class="col-lg-8">
        <!-- Annual Leave Balances (Kitni li hai & Kitni bachi hai) -->
        <div class="card p-3 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-wallet2 text-primary me-2"></i>Annual Leave Balances (<?= date('Y') ?>)</h6>
                    <small class="text-muted">Total leave limit, kitni li hai (used) aur kitni bachi hai (remaining):</small>
                </div>
                <span class="badge bg-primary-subtle text-primary fw-medium px-2 py-1">Year <?= date('Y') ?></span>
            </div>

            <div class="row g-3">
                <?php if (empty($balances)): ?>
                    <div class="col-12"><p class="text-muted small mb-0">No leave balance records found for current year.</p></div>
                <?php else: ?>
                    <?php foreach ($balances as $b): 
                        $percent = $b['allocated_days'] > 0 ? min(100, round(($b['used_days'] / $b['allocated_days']) * 100)) : 0;
                    ?>
                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 rounded-3 bg-light border h-100">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-dark text-truncate small"><?= \App\helpers\ViewHelper::e($b['leave_type_name']) ?></span>
                                    <span class="badge bg-secondary-subtle text-secondary fw-bold" style="font-size: 0.68rem;"><?= $b['allocated_days'] ?>d Total</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-baseline mb-2">
                                    <div>
                                        <small class="text-muted d-block" style="font-size: 0.72rem;">Li Hai (Used)</small>
                                        <span class="fw-bold text-danger fs-5"><?= $b['used_days'] ?></span> <small class="text-muted">days</small>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block" style="font-size: 0.72rem;">Bachi Hai (Left)</small>
                                        <span class="fw-bold text-success fs-5"><?= $b['remaining_days'] ?></span> <small class="text-muted">days</small>
                                    </div>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar <?= $percent > 80 ? 'bg-danger' : ($percent > 50 ? 'bg-warning' : 'bg-primary') ?>" role="progressbar" style="width: <?= $percent ?>%;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted" style="font-size: 0.68rem;"><?= $percent ?>% utilized</small>
                                    <small class="text-muted" style="font-size: 0.68rem;"><?= $b['remaining_days'] ?> days remaining</small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Leave Requests (Konsi leave li hai) -->
        <div class="card p-3 mb-4">
            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-calendar2-range text-primary me-2"></i>Konsi Leave Li Hai (Leave History)</h6>
            <p class="text-muted small mb-3">Recent leave applications, reasons, dates and approval statuses:</p>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted">
                            <th>Leave Category</th>
                            <th>Dates Applied</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($leaves)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">No leave requests logged for this employee.</td></tr>
                        <?php else: ?>
                            <?php foreach ($leaves as $l): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= \App\helpers\ViewHelper::e($l['leave_type_name']) ?></span>
                                    </td>
                                    <td class="small">
                                        <?= date('d M Y', strtotime($l['start_date'])) ?> &rarr; <?= date('d M Y', strtotime($l['end_date'])) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary"><?= $l['total_days'] ?> day<?= $l['total_days'] > 1 ? 's' : '' ?></span>
                                    </td>
                                    <td class="small text-muted" style="max-width: 200px;">
                                        <?= \App\helpers\ViewHelper::e($l['reason'] ?? '—') ?>
                                    </td>
                                    <td><?= \App\helpers\ViewHelper::statusBadge($l['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Current Month Attendance Records -->
        <div class="card p-3">
            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-clock-history text-primary me-2"></i>Current Month Attendance</h6>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($attendance)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">No attendance records this month.</td></tr>
                        <?php else: ?>
                            <?php foreach ($attendance as $att): ?>
                                <tr>
                                    <td><?= \App\helpers\ViewHelper::formatDate($att['attendance_date']) ?></td>
                                    <td><?= \App\helpers\ViewHelper::statusBadge($att['status']) ?></td>
                                    <td class="small"><?= \App\helpers\ViewHelper::formatTime($att['check_in']) ?></td>
                                    <td class="small"><?= \App\helpers\ViewHelper::formatTime($att['check_out']) ?></td>
                                    <td class="small fw-semibold"><?= $att['working_hours'] ?> hrs</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Delete Employee Confirmation -->
<div class="modal fade" id="deleteEmployeeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= \App\helpers\ViewHelper::url("admin/employees/{$employee['id']}/delete") ?>" method="POST">
                <?= \App\helpers\ViewHelper::csrf() ?>
                <div class="modal-header bg-danger bg-opacity-10 py-3">
                    <h5 class="modal-title fw-bold text-danger fs-6 mb-0">
                        <i class="bi bi-trash3-fill me-2"></i>Permanently Remove Employee
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-2">Are you sure you want to permanently remove this employee from the system?</p>
                    <div class="p-3 bg-light rounded-3 small border mb-3">
                        <div><strong>Name:</strong> <?= \App\helpers\ViewHelper::e($employee['first_name'] . ' ' . $employee['last_name']) ?></div>
                        <div><strong>Employee ID:</strong> <span class="font-monospace"><?= \App\helpers\ViewHelper::e($employee['employee_code']) ?></span></div>
                        <div><strong>Email:</strong> <?= \App\helpers\ViewHelper::e($employee['email'] ?? 'N/A') ?></div>
                        <div><strong>Department:</strong> <?= \App\helpers\ViewHelper::e($employee['department_name'] ?? 'Unassigned') ?></div>
                    </div>
                    <div class="alert alert-danger bg-opacity-10 small mb-0">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <strong>Warning:</strong> This will delete their login credentials, leave ledger, attendance history, and profile records. This action cannot be undone.
                    </div>
                </div>
                <div class="modal-footer py-2 bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-danger fw-semibold px-3">
                        <i class="bi bi-trash3 me-1"></i> Yes, Remove Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
