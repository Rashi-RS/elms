<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <a href="<?= \App\helpers\ViewHelper::url('admin/reports') ?>" class="text-secondary text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports Hub
        </a>
        <h3 class="fw-bold mb-0 text-dark">Employee Headcount Census Report</h3>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print Report
        </button>
        <?php
        $csvUrl = '?export=csv&department_id=' . ($filters['department_id'] ?? '') .
                  '&status=' . ($filters['status'] ?? '');
        ?>
        <a href="<?= $csvUrl ?>" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export to CSV
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card p-3 mb-4 d-print-none">
    <form method="GET" action="<?= \App\helpers\ViewHelper::url('admin/reports/employees') ?>" class="row g-2">
        <div class="col-md-5">
            <select class="form-select form-select-sm" name="department_id">
                <option value="">All Departments</option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= ($filters['department_id'] == $d['id']) ? 'selected' : '' ?>>
                        <?= \App\helpers\ViewHelper::e($d['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <select class="form-select form-select-sm" name="status">
                <option value="">All Employment Statuses</option>
                <option value="active" <?= ($filters['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($filters['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                <option value="terminated" <?= ($filters['status'] === 'terminated') ? 'selected' : '' ?>>Terminated</option>
            </select>
        </div>

        <div class="col-md-3">
            <button type="submit" class="btn btn-primary btn-sm w-100">Filter Census</button>
        </div>
    </form>
</div>

<!-- Census Table -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <span class="fw-bold text-dark">Total Census: <?= count($records) ?> Personnel</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Employee Name</th>
                    <th>Corporate Email</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Tenure Joining</th>
                    <th>Compensation</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr><td colspan="8" class="text-center py-5 text-muted">No records match criteria.</td></tr>
                <?php else: ?>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td class="font-monospace small"><?= \App\helpers\ViewHelper::e($r['employee_code']) ?></td>
                            <td><strong><?= \App\helpers\ViewHelper::e($r['first_name'] . ' ' . $r['last_name']) ?></strong></td>
                            <td class="small text-muted"><?= \App\helpers\ViewHelper::e($r['email'] ?? '—') ?></td>
                            <td><?= \App\helpers\ViewHelper::e($r['department_name'] ?? '—') ?></td>
                            <td class="small"><?= \App\helpers\ViewHelper::e($r['designation']) ?></td>
                            <td class="small"><?= \App\helpers\ViewHelper::formatDate($r['joining_date']) ?></td>
                            <td class="small fw-semibold"><?= \App\helpers\ViewHelper::formatCurrency($r['salary']) ?></td>
                            <td><?= \App\helpers\ViewHelper::statusBadge($r['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
