<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-0 text-dark">My Attendance Timesheet</h3>
        <p class="text-muted small mb-0">Review your monthly check-ins, recorded shift hours, and punctuality performance</p>
    </div>
    <!-- Month Selector -->
    <form method="GET" action="<?= \App\helpers\ViewHelper::url('employee/my-attendance') ?>" class="d-flex align-items-center gap-2">
        <label for="month" class="small text-muted fw-semibold mb-0">Period:</label>
        <input type="month" class="form-control form-control-sm" id="month" name="month" value="<?= \App\helpers\ViewHelper::e($selectedMonth) ?>" onchange="this.form.submit()">
    </form>
</div>

<!-- Attendance Performance KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
        <div class="card p-3 text-center bg-success bg-opacity-10 border-0">
            <h3 class="fw-bold text-success mb-0"><?= $stats['present'] ?></h3>
            <small class="text-success fw-medium">Present Days</small>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card p-3 text-center bg-info bg-opacity-10 border-0">
            <h3 class="fw-bold text-info mb-0"><?= $stats['half_day'] ?></h3>
            <small class="text-info fw-medium">Half Days</small>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card p-3 text-center bg-secondary bg-opacity-10 border-0">
            <h3 class="fw-bold text-secondary mb-0"><?= $stats['leave'] ?></h3>
            <small class="text-secondary fw-medium">Approved Leaves</small>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card p-3 text-center bg-danger bg-opacity-10 border-0">
            <h3 class="fw-bold text-danger mb-0"><?= $stats['absent'] ?></h3>
            <small class="text-danger fw-medium">Absences</small>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card p-3 text-center border">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small text-muted fw-semibold">Attendance Ratio</span>
                <span class="fs-5 fw-bold text-dark"><?= $stats['percentage'] ?>%</span>
            </div>
            <div class="progress" style="height: 10px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $stats['percentage'] ?>%;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Attendance Timesheet Table -->
<div class="card">
    <div class="card-header bg-white py-3">
        <span class="fw-bold text-dark">Timesheet for <?= date('F Y', strtotime($selectedMonth . '-01')) ?></span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Day</th>
                    <th>Status</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Logged Hours</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr><td colspan="7" class="text-center py-5 text-muted">No attendance entries recorded for this month.</td></tr>
                <?php else: ?>
                    <?php foreach ($records as $att): ?>
                        <tr>
                            <td><strong><?= \App\helpers\ViewHelper::formatDate($att['attendance_date']) ?></strong></td>
                            <td class="text-muted small"><?= date('l', strtotime($att['attendance_date'])) ?></td>
                            <td><?= \App\helpers\ViewHelper::statusBadge($att['status']) ?></td>
                            <td class="small font-monospace"><?= !empty($att['check_in']) ? \App\helpers\ViewHelper::formatTime($att['check_in']) : '—' ?></td>
                            <td class="small font-monospace"><?= !empty($att['check_out']) ? \App\helpers\ViewHelper::formatTime($att['check_out']) : '—' ?></td>
                            <td class="small fw-semibold"><?= ($att['working_hours'] > 0) ? "{$att['working_hours']} hrs" : '—' ?></td>
                            <td class="small text-muted"><?= \App\helpers\ViewHelper::e($att['remarks'] ?? '—') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
