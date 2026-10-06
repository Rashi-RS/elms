<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <a href="<?= \App\helpers\ViewHelper::url('admin/reports') ?>" class="text-secondary text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports Hub
        </a>
        <h3 class="fw-bold mb-0 text-dark">Leave Utilization Report</h3>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print Report
        </button>
        <?php
        $csvUrl = '?export=csv&employee_id=' . ($filters['employee_id'] ?? '') .
                  '&leave_type_id=' . ($filters['leave_type_id'] ?? '') .
                  '&status=' . ($filters['status'] ?? '') .
                  '&start_date=' . ($filters['start_date'] ?? '') .
                  '&end_date=' . ($filters['end_date'] ?? '');
        ?>
        <a href="<?= $csvUrl ?>" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export to CSV
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card p-3 mb-4 d-print-none">
    <form method="GET" action="<?= \App\helpers\ViewHelper::url('admin/reports/leaves') ?>" class="row g-2">
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

        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Leave Category</label>
            <select class="form-select form-select-sm" name="leave_type_id">
                <option value="">All Categories</option>
                <?php foreach ($leaveTypes as $lt): ?>
                    <option value="<?= $lt['id'] ?>" <?= ($filters['leave_type_id'] == $lt['id']) ? 'selected' : '' ?>>
                        <?= \App\helpers\ViewHelper::e($lt['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Status</label>
            <select class="form-select form-select-sm" name="status">
                <option value="">All Statuses</option>
                <option value="approved" <?= ($filters['status'] === 'approved') ? 'selected' : '' ?>>Approved</option>
                <option value="pending" <?= ($filters['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                <option value="rejected" <?= ($filters['status'] === 'rejected') ? 'selected' : '' ?>>Rejected</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">From Date</label>
            <input type="date" class="form-control form-control-sm" name="start_date" value="<?= \App\helpers\ViewHelper::e($filters['start_date']) ?>">
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary btn-sm w-100">Apply Filter</button>
        </div>
    </form>
</div>

<!-- Report Table -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <span class="fw-bold text-dark">Results: <?= count($records) ?> Leave Application(s)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Category</th>
                    <th>Duration</th>
                    <th>Days</th>
                    <th>Status</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr><td colspan="8" class="text-center py-5 text-muted">No leave applications match the selected filters.</td></tr>
                <?php else: ?>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td class="font-monospace text-muted small">#<?= $r['id'] ?></td>
                            <td>
                                <strong><?= \App\helpers\ViewHelper::e($r['first_name'] . ' ' . $r['last_name']) ?></strong>
                                <small class="text-muted font-monospace d-block"><?= \App\helpers\ViewHelper::e($r['employee_code']) ?></small>
                            </td>
                            <td><?= \App\helpers\ViewHelper::e($r['department_name'] ?? '—') ?></td>
                            <td><span class="badge bg-light text-dark border"><?= \App\helpers\ViewHelper::e($r['leave_type_name']) ?></span></td>
                            <td class="small"><?= \App\helpers\ViewHelper::formatDate($r['start_date']) ?> &rarr; <?= \App\helpers\ViewHelper::formatDate($r['end_date']) ?></td>
                            <td><span class="badge bg-primary"><?= $r['total_days'] ?>d</span></td>
                            <td><?= \App\helpers\ViewHelper::statusBadge($r['status']) ?></td>
                            <td class="small text-muted text-truncate" style="max-width: 200px;" title="<?= \App\helpers\ViewHelper::e($r['reason']) ?>">
                                <?= \App\helpers\ViewHelper::e($r['reason']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
