<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-0 text-dark">My Leave Applications</h3>
        <p class="text-muted small mb-0">Track application status, review administrative responses, and manage time off</p>
    </div>
    <a href="<?= \App\helpers\ViewHelper::url('employee/apply-leave') ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle-fill me-1"></i> Apply for Leave
    </a>
</div>

<!-- Status Tabs -->
<ul class="nav nav-pills mb-3 gap-1">
    <li class="nav-item">
        <a class="nav-link <?= empty($currentStatus) ? 'active' : '' ?>" href="<?= \App\helpers\ViewHelper::url('employee/my-leaves') ?>">
            All Applications
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($currentStatus === 'pending') ? 'active bg-warning text-dark' : 'text-dark' ?>" href="<?= \App\helpers\ViewHelper::url('employee/my-leaves?status=pending') ?>">
            Pending Review
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($currentStatus === 'approved') ? 'active bg-success' : 'text-dark' ?>" href="<?= \App\helpers\ViewHelper::url('employee/my-leaves?status=approved') ?>">
            Approved
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($currentStatus === 'rejected') ? 'active bg-danger' : 'text-dark' ?>" href="<?= \App\helpers\ViewHelper::url('employee/my-leaves?status=rejected') ?>">
            Rejected
        </a>
    </li>
</ul>

<!-- Leaves Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Leave Category</th>
                    <th>Requested Dates</th>
                    <th>Total Days</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Admin Feedback</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leaves['data'])): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-event fs-1 text-secondary opacity-50 d-block mb-2"></i>
                            No leave applications found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($leaves['data'] as $l): ?>
                        <tr>
                            <td class="small font-monospace text-muted">#<?= $l['id'] ?></td>
                            <td>
                                <strong><?= \App\helpers\ViewHelper::e($l['leave_type_name']) ?></strong>
                            </td>
                            <td>
                                <div class="small fw-semibold"><?= \App\helpers\ViewHelper::formatDate($l['start_date']) ?> &rarr; <?= \App\helpers\ViewHelper::formatDate($l['end_date']) ?></div>
                                <small class="text-muted">Applied on <?= date('d M Y', strtotime($l['created_at'])) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-primary fs-6"><?= $l['total_days'] ?>d</span>
                            </td>
                            <td>
                                <span class="small text-muted text-truncate d-inline-block" style="max-width: 200px;" title="<?= \App\helpers\ViewHelper::e($l['reason']) ?>">
                                    <?= \App\helpers\ViewHelper::e($l['reason']) ?>
                                </span>
                            </td>
                            <td>
                                <?= \App\helpers\ViewHelper::statusBadge($l['status']) ?>
                            </td>
                            <td>
                                <?php if (!empty($l['rejection_reason'])): ?>
                                    <span class="small text-danger" title="<?= \App\helpers\ViewHelper::e($l['rejection_reason']) ?>">
                                        <i class="bi bi-info-circle me-1"></i><?= \App\helpers\ViewHelper::e($l['rejection_reason']) ?>
                                    </span>
                                <?php elseif ($l['status'] === 'approved'): ?>
                                    <span class="small text-success"><i class="bi bi-check2"></i> Approved</span>
                                <?php else: ?>
                                    <span class="small text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if ($l['status'] === 'pending'): ?>
                                    <form action="<?= \App\helpers\ViewHelper::url("employee/leaves/{$l['id']}/cancel") ?>" method="POST" class="d-inline" onsubmit="return confirm('Cancel this pending leave request?');">
                                        <?= \App\helpers\ViewHelper::csrf() ?>
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            Cancel
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($leaves['last_page'] > 1): ?>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
            <small class="text-muted">Page <?= $leaves['current_page'] ?> of <?= $leaves['last_page'] ?></small>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= !$leaves['has_prev'] ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $leaves['current_page'] - 1 ?>&status=<?= urlencode($currentStatus) ?>">Previous</a>
                    </li>
                    <li class="page-item <?= !$leaves['has_next'] ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $leaves['current_page'] + 1 ?>&status=<?= urlencode($currentStatus) ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>
