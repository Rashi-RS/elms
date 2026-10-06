<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <a href="<?= \App\helpers\ViewHelper::url('admin/reports') ?>" class="text-secondary text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports Hub
        </a>
        <h3 class="fw-bold mb-0 text-dark">Attendance Audit Report</h3>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print Report
        </button>
        <?php
        $csvUrl = '?export=csv&department_id=' . ($filters['department_id'] ?? '') .
                  '&employee_id=' . ($filters['employee_id'] ?? '') .
                  '&start_date=' . ($filters['start_date'] ?? '') .
                  '&end_date=' . ($filters['end_date'] ?? '') .
                  '&status=' . ($filters['status'] ?? '');
        ?>
        <a href="<?= $csvUrl ?>" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export to CSV
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card p-3 mb-4 d-print-none">
    <form method="GET" action="<?= \App\helpers\ViewHelper::url('admin/reports/attendance') ?>" class="row g-2">
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Department</label>
            <select class="form-select form-select-sm" name="department_id">
                <option value="">All Departments</option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= ($filters['department_id'] == $d['id']) ? 'selected' : '' ?>>
                        <?= \App\helpers\ViewHelper::e($d['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Employee</label>
            <select class="form-select form-select-sm" name="employee_id">
                <option value="">All Employees</option>
                <?php foreach ($employees as $e): ?>
                    <option value="<?= $e['id'] ?>" <?= ($filters['employee_id'] == $e['id']) ? 'selected' : '' ?>>
                        <?= \App\helpers\ViewHelper::e($e['first_name'] . ' ' . $e['last_name']) ?> (<?= $e['employee_code'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Start Date</label>
            <input type="date" class="form-control form-control-sm" name="start_date" value="<?= \App\helpers\ViewHelper::e($filters['start_date']) ?>">
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">End Date</label>
            <input type="date" class="form-control form-control-sm" name="end_date" value="<?= \App\helpers\ViewHelper::e($filters['end_date']) ?>">
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary btn-sm w-100">Apply Filter</button>
        </div>
    </form>
</div>

<!-- Report Table -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <span class="fw-bold text-dark">Results: <?= count($records) ?> Attendance Log(s)</span>
        <span class="small text-muted">Range: <?= \App\helpers\ViewHelper::formatDate($filters['start_date']) ?> &rarr; <?= \App\helpers\ViewHelper::formatDate($filters['end_date']) ?></span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Hours</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr><td colspan="8" class="text-center py-5 text-muted">No attendance records found for this period.</td></tr>
                <?php else: ?>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td><?= \App\helpers\ViewHelper::formatDate($r['attendance_date']) ?></td>
                            <td>
                                <strong><?= \App\helpers\ViewHelper::e($r['first_name'] . ' ' . $r['last_name']) ?></strong>
                                <small class="text-muted font-monospace d-block"><?= \App\helpers\ViewHelper::e($r['employee_code']) ?></small>
                            </td>
                            <td><?= \App\helpers\ViewHelper::e($r['department_name'] ?? '—') ?></td>
                            <td><?= \App\helpers\ViewHelper::statusBadge($r['status']) ?></td>
                            <td class="small font-monospace"><?= !empty($r['check_in']) ? \App\helpers\ViewHelper::formatTime($r['check_in']) : '—' ?></td>
                            <td class="small font-monospace"><?= !empty($r['check_out']) ? \App\helpers\ViewHelper::formatTime($r['check_out']) : '—' ?></td>
                            <td class="small fw-semibold"><?= ($r['working_hours'] > 0) ? "{$r['working_hours']} hrs" : '—' ?></td>
                            <td class="small text-muted"><?= \App\helpers\ViewHelper::e($r['remarks'] ?? '—') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
