<div class="mb-4">
    <div class="d-flex align-items-center gap-2 mb-1">
        <a href="<?= \App\helpers\ViewHelper::url('employee/my-leaves') ?>" class="text-secondary text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i> Back to My Leave History
        </a>
    </div>
    <h3 class="fw-bold mb-0 text-dark">Submit Leave Application</h3>
    <p class="text-muted small">Request time off, select entitlement category, and notify HR management</p>
</div>

<div class="row g-4">
    <!-- Form Column -->
    <div class="col-lg-8">
        <div class="card p-4">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-calendar-plus-fill me-2"></i>Application Details</h5>

            <form action="<?= \App\helpers\ViewHelper::url('employee/apply-leave') ?>" method="POST" id="leaveApplicationForm">
                <?= \App\helpers\ViewHelper::csrf() ?>

                <!-- Leave Type Selection -->
                <div class="mb-3">
                    <label for="leave_type_id" class="form-label small fw-semibold">Leave Category <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="leave_type_id" name="leave_type_id" required>
                        <option value="">-- Select Leave Category --</option>
                        <?php foreach ($leaveTypes as $lt): ?>
                            <?php
                            // Find remaining balance for this type
                            $rem = 0;
                            foreach ($balances as $b) {
                                if ($b['leave_type_id'] == $lt['id']) {
                                    $rem = $b['remaining_days'];
                                    break;
                                }
                            }
                            ?>
                            <option value="<?= $lt['id'] ?>" data-remaining="<?= $rem ?>">
                                <?= \App\helpers\ViewHelper::e($lt['name']) ?> &mdash; (<?= $rem ?> Days Remaining)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Date Range Selection with Live Calculation -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label small fw-semibold">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="end_date" class="form-label small fw-semibold">End Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" min="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <!-- Live Days Calculation Preview -->
                <div class="p-3 bg-light rounded-3 border mb-3 d-flex align-items-center justify-content-between">
                    <span class="small fw-semibold text-secondary">Computed Leave Duration:</span>
                    <div id="calculated_days_preview">
                        <span class="text-muted small">Select dates to calculate</span>
                    </div>
                </div>

                <!-- Reason for Leave -->
                <div class="mb-4">
                    <label for="reason" class="form-label small fw-semibold">Reason / Purpose <span class="text-danger">*</span></label>
                    <textarea class="form-control form-control-sm" id="reason" name="reason" rows="4" placeholder="Detail reason for leave request (e.g. medical recuperation, family emergency, travel)..." required></textarea>
                    <div class="form-text" style="font-size: 0.75rem;">Minimum 5 characters. Clear descriptions expedite administrative approval.</div>
                </div>

                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                    <i class="bi bi-send-fill me-1"></i> Submit Leave Request
                </button>
            </form>
        </div>
    </div>

    <!-- Right Column: Current Balances Card -->
    <div class="col-lg-4">
        <div class="card p-3">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-wallet2 text-primary me-2"></i>My Leave Quotas (<?= date('Y') ?>)</h6>
            <div class="d-flex flex-column gap-2">
                <?php foreach ($balances as $b): ?>
                    <div class="p-2.5 rounded-3 border bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <strong class="text-dark small d-block"><?= \App\helpers\ViewHelper::e($b['leave_type_name']) ?></strong>
                            <small class="text-muted"><?= $b['used_days'] ?> used / <?= $b['allocated_days'] ?> quota</small>
                        </div>
                        <div class="text-end">
                            <span class="fs-5 fw-bold text-primary"><?= $b['remaining_days'] ?></span>
                            <small class="text-muted d-block" style="font-size: 0.7rem;">left</small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="alert alert-secondary border-0 p-3 mt-3 mb-0 small">
                <i class="bi bi-info-circle me-1"></i> <strong>Validation Rules:</strong>
                <ul class="mb-0 ps-3 mt-1" style="font-size: 0.78rem;">
                    <li>End date cannot be prior to start date.</li>
                    <li>Cannot request more days than remaining quota.</li>
                    <li>Overlapping leave dates are prevented.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
