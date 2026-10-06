<div class="mb-4">
    <div class="d-flex align-items-center gap-2 mb-1">
        <a href="<?= \App\helpers\ViewHelper::url('admin/leaves') ?>" class="text-secondary text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i> Back to Leave Applications
        </a>
    </div>
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Leave Application #<?= $leave['id'] ?></h3>
            <p class="text-muted small mb-0">Submitted by <?= \App\helpers\ViewHelper::e($leave['first_name'] . ' ' . $leave['last_name']) ?> (<?= \App\helpers\ViewHelper::e($leave['employee_code']) ?>)</p>
        </div>
        <div>
            <?= \App\helpers\ViewHelper::statusBadge($leave['status']) ?>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Application Details Card -->
    <div class="col-lg-7">
        <div class="card p-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-file-earmark-text me-2"></i>Application Dossier</h5>

            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <label class="text-muted small d-block">Employee Name</label>
                    <strong class="text-dark"><?= \App\helpers\ViewHelper::e($leave['first_name'] . ' ' . $leave['last_name']) ?></strong>
                </div>
                <div class="col-sm-6">
                    <label class="text-muted small d-block">Department</label>
                    <strong class="text-dark"><?= \App\helpers\ViewHelper::e($leave['department_name'] ?? 'N/A') ?></strong>
                </div>
                <div class="col-sm-6">
                    <label class="text-muted small d-block">Leave Category</label>
                    <span class="badge bg-light text-dark border fs-6"><?= \App\helpers\ViewHelper::e($leave['leave_type_name']) ?></span>
                </div>
                <div class="col-sm-6">
                    <label class="text-muted small d-block">Requested Duration</label>
                    <strong class="text-primary fs-5"><?= $leave['total_days'] ?> Day(s)</strong>
                </div>
                <div class="col-sm-6">
                    <label class="text-muted small d-block">Date Window</label>
                    <span><?= \App\helpers\ViewHelper::formatDate($leave['start_date']) ?> &rarr; <?= \App\helpers\ViewHelper::formatDate($leave['end_date']) ?></span>
                </div>
                <div class="col-sm-6">
                    <label class="text-muted small d-block">Submitted At</label>
                    <span><?= \App\helpers\ViewHelper::formatDate($leave['created_at'], 'd M Y, h:i A') ?></span>
                </div>
            </div>

            <div class="mb-4">
                <label class="text-muted small d-block mb-1">Reason / Purpose of Leave</label>
                <div class="p-3 bg-light rounded-3 text-dark border">
                    <?= nl2br(\App\helpers\ViewHelper::e($leave['reason'])) ?>
                </div>
            </div>

            <?php if (!empty($leave['rejection_reason'])): ?>
                <div class="alert alert-danger mb-4">
                    <strong class="d-block mb-1"><i class="bi bi-x-circle me-1"></i> Rejection Reason</strong>
                    <?= \App\helpers\ViewHelper::e($leave['rejection_reason']) ?>
                </div>
            <?php endif; ?>

            <?php if ($leave['status'] === 'approved'): ?>
                <div class="alert alert-success mb-4">
                    <i class="bi bi-check-circle me-1"></i> Approved by <?= \App\helpers\ViewHelper::e($leave['approver_email'] ?? 'Administrator') ?> on <?= \App\helpers\ViewHelper::formatDate($leave['approved_at'], 'd M Y, h:i A') ?>
                </div>
            <?php endif; ?>

            <!-- Action Buttons for Pending Status -->
            <?php if ($leave['status'] === 'pending'): ?>
                <hr>
                <div class="d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-success px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#approveModal">
                        <i class="bi bi-check-lg me-1"></i> Approve Application
                    </button>

                    <button type="button" class="btn btn-outline-danger px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-lg me-1"></i> Reject Application
                    </button>
                </div>

                <!-- Approval Modal -->
                <div class="modal fade" id="approveModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="<?= \App\helpers\ViewHelper::url("admin/leaves/{$leave['id']}/approve") ?>" method="POST">
                                <?= \App\helpers\ViewHelper::csrf() ?>
                                <input type="hidden" name="redirect_to" value="<?= \App\helpers\ViewHelper::url("admin/leaves/{$leave['id']}") ?>">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i> Approve Leave Request</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-start">
                                    <p class="mb-2">Are you sure you want to approve this leave application?</p>
                                    <div class="alert alert-success bg-opacity-10 mb-0 small">
                                        <strong>Impact:</strong> This will deduct <strong><?= $leave['total_days'] ?> day(s)</strong> from <strong><?= \App\helpers\ViewHelper::e($leave['first_name'] . ' ' . $leave['last_name']) ?></strong>'s annual <?= \App\helpers\ViewHelper::e($leave['leave_type_name']) ?> balance quota.
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-sm btn-success fw-semibold px-3">Confirm Approval</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Rejection Modal -->
                <div class="modal fade" id="rejectModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="<?= \App\helpers\ViewHelper::url("admin/leaves/{$leave['id']}/reject") ?>" method="POST">
                                <?= \App\helpers\ViewHelper::csrf() ?>
                                <input type="hidden" name="redirect_to" value="<?= \App\helpers\ViewHelper::url("admin/leaves/{$leave['id']}") ?>">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold text-danger"><i class="bi bi-x-circle-fill me-1"></i> Reject Leave Application</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-start">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="rejection_reason" rows="3" placeholder="Provide clear feedback to the employee regarding why this leave cannot be granted..." required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-sm btn-danger fw-semibold px-3">Confirm Rejection</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column: Employee Balance Ledger -->
    <div class="col-lg-5">
        <div class="card p-3">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-wallet2 text-primary me-2"></i>Employee Annual Balance Ledger</h6>
            <div class="d-flex flex-column gap-2">
                <?php foreach ($balances as $b): ?>
                    <div class="p-2.5 rounded-3 border bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <strong class="text-dark small d-block"><?= \App\helpers\ViewHelper::e($b['leave_type_name']) ?></strong>
                            <small class="text-muted"><?= $b['used_days'] ?> used / <?= $b['allocated_days'] ?> limit</small>
                        </div>
                        <div class="text-end">
                            <span class="fs-5 fw-bold text-primary"><?= $b['remaining_days'] ?></span>
                            <small class="text-muted d-block" style="font-size: 0.7rem;">remaining</small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
