<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-0 text-dark">Leave Management & Approvals</h3>
        <p class="text-muted small mb-0">Review employee leave applications, execute approval transactions, and audit balances</p>
    </div>
</div>

<!-- Status Filter Tabs -->
<ul class="nav nav-pills mb-3 gap-1">
    <li class="nav-item">
        <a class="nav-link <?= empty($filters['status']) ? 'active' : '' ?>" href="<?= \App\helpers\ViewHelper::url('admin/leaves') ?>">
            All (<?= array_sum($statusCounts) ?>)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($filters['status'] === 'pending') ? 'active bg-warning text-dark' : 'text-dark' ?>" href="<?= \App\helpers\ViewHelper::url('admin/leaves?status=pending') ?>">
            <i class="bi bi-hourglass-split me-1"></i> Pending (<?= $statusCounts['pending'] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($filters['status'] === 'approved') ? 'active bg-success' : 'text-dark' ?>" href="<?= \App\helpers\ViewHelper::url('admin/leaves?status=approved') ?>">
            <i class="bi bi-check-circle me-1"></i> Approved (<?= $statusCounts['approved'] ?>)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($filters['status'] === 'rejected') ? 'active bg-danger' : 'text-dark' ?>" href="<?= \App\helpers\ViewHelper::url('admin/leaves?status=rejected') ?>">
            <i class="bi bi-x-circle me-1"></i> Rejected (<?= $statusCounts['rejected'] ?>)
        </a>
    </li>
</ul>

<!-- Filter Bar -->
<div class="card p-3 mb-4">
    <form method="GET" action="<?= \App\helpers\ViewHelper::url('admin/leaves') ?>" class="row g-2">
        <input type="hidden" name="status" value="<?= \App\helpers\ViewHelper::e($filters['status']) ?>">

        <div class="col-md-4">
            <select class="form-select form-select-sm" name="leave_type_id">
                <option value="">All Leave Categories</option>
                <?php foreach ($leaveTypes as $lt): ?>
                    <option value="<?= $lt['id'] ?>" <?= ($filters['leave_type_id'] == $lt['id']) ? 'selected' : '' ?>>
                        <?= \App\helpers\ViewHelper::e($lt['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <input type="date" class="form-control form-control-sm" name="start_date" value="<?= \App\helpers\ViewHelper::e($filters['start_date']) ?>" placeholder="Start Date">
        </div>

        <div class="col-md-3">
            <input type="date" class="form-control form-control-sm" name="end_date" value="<?= \App\helpers\ViewHelper::e($filters['end_date']) ?>" placeholder="End Date">
        </div>

        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            <?php if (!empty($filters['leave_type_id']) || !empty($filters['start_date']) || !empty($filters['end_date'])): ?>
                <a href="<?= \App\helpers\ViewHelper::url('admin/leaves') ?>" class="btn btn-light btn-sm text-muted" title="Reset Filters"><i class="bi bi-x-lg"></i></a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Leave Requests Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Employee</th>
                    <th>Category</th>
                    <th>Leave Dates</th>
                    <th>Days</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leaves['data'])): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-check fs-1 text-secondary opacity-50 d-block mb-2"></i>
                            No leave applications found matching these parameters.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($leaves['data'] as $lr): ?>
                        <tr id="leave-row-<?= $lr['id'] ?>">
                            <td class="font-monospace small text-muted">#<?= $lr['id'] ?></td>
                            <td>
                                <a href="<?= \App\helpers\ViewHelper::url("admin/employees/{$lr['employee_id']}") ?>" class="fw-semibold text-dark text-decoration-none">
                                    <?= \App\helpers\ViewHelper::e($lr['first_name'] . ' ' . $lr['last_name']) ?>
                                </a>
                                <div class="text-muted small font-monospace"><?= \App\helpers\ViewHelper::e($lr['employee_code']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= \App\helpers\ViewHelper::e($lr['leave_type_name']) ?></span>
                            </td>
                            <td>
                                <div class="small fw-semibold"><?= date('d M Y', strtotime($lr['start_date'])) ?> &rarr; <?= date('d M Y', strtotime($lr['end_date'])) ?></div>
                                <small class="text-muted">Applied on <?= date('d M, H:i', strtotime($lr['created_at'])) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-primary fs-6"><?= $lr['total_days'] ?>d</span>
                            </td>
                            <td>
                                <div class="small text-muted text-truncate" style="max-width: 220px;" title="<?= \App\helpers\ViewHelper::e($lr['reason']) ?>">
                                    <?= \App\helpers\ViewHelper::e($lr['reason']) ?>
                                </div>
                                <?php if (!empty($lr['rejection_reason'])): ?>
                                    <small class="text-danger d-block mt-0.5">Note: <?= \App\helpers\ViewHelper::e($lr['rejection_reason']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="leave-status-cell">
                                <?= \App\helpers\ViewHelper::statusBadge($lr['status']) ?>
                            </td>
                            <td class="text-end leave-actions-cell">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= \App\helpers\ViewHelper::url("admin/leaves/{$lr['id']}") ?>" class="btn btn-outline-secondary" title="View Full Review">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <?php if ($lr['status'] === 'pending'): ?>
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-approve-<?= $lr['id'] ?>" title="Approve Request">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modal-reject-<?= $lr['id'] ?>" title="Reject Request">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <?php if ($lr['status'] === 'pending'): ?>
                                    <!-- Approve Modal for #<?= $lr['id'] ?> -->
                                    <div class="modal fade" id="modal-approve-<?= $lr['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-start">
                                                <form action="<?= \App\helpers\ViewHelper::url("admin/leaves/{$lr['id']}/approve") ?>" method="POST">
                                                    <?= \App\helpers\ViewHelper::csrf() ?>
                                                    <input type="hidden" name="redirect_to" value="<?= \App\helpers\ViewHelper::url('admin/leaves') ?>">
                                                    <div class="modal-header bg-success bg-opacity-10 py-3">
                                                        <h5 class="modal-title fw-bold text-success fs-6"><i class="bi bi-check-circle-fill me-2"></i>Approve Leave Request #<?= $lr['id'] ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <p class="mb-2">Are you sure you want to approve this leave request?</p>
                                                        <div class="p-3 bg-light rounded-3 small border mb-3">
                                                            <div class="mb-1"><strong>Employee:</strong> <?= \App\helpers\ViewHelper::e($lr['first_name'] . ' ' . $lr['last_name']) ?> (<?= \App\helpers\ViewHelper::e($lr['employee_code']) ?>)</div>
                                                            <div class="mb-1"><strong>Category:</strong> <?= \App\helpers\ViewHelper::e($lr['leave_type_name']) ?></div>
                                                            <div class="mb-1"><strong>Duration:</strong> <?= date('d M Y', strtotime($lr['start_date'])) ?> &rarr; <?= date('d M Y', strtotime($lr['end_date'])) ?> (<?= $lr['total_days'] ?> days)</div>
                                                            <div><strong>Reason:</strong> <?= \App\helpers\ViewHelper::e($lr['reason']) ?></div>
                                                        </div>
                                                        <div class="alert alert-success bg-opacity-10 small mb-0">
                                                            <i class="bi bi-info-circle me-1"></i> Approving will automatically deduct <strong><?= $lr['total_days'] ?> day(s)</strong> from their annual quota balance.
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer py-2 bg-light">
                                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-sm btn-success fw-semibold px-3">Confirm & Approve</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Reject Modal for #<?= $lr['id'] ?> -->
                                    <div class="modal fade" id="modal-reject-<?= $lr['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-start">
                                                <form action="<?= \App\helpers\ViewHelper::url("admin/leaves/{$lr['id']}/reject") ?>" method="POST">
                                                    <?= \App\helpers\ViewHelper::csrf() ?>
                                                    <input type="hidden" name="redirect_to" value="<?= \App\helpers\ViewHelper::url('admin/leaves') ?>">
                                                    <div class="modal-header bg-danger bg-opacity-10 py-3">
                                                        <h5 class="modal-title fw-bold text-danger fs-6"><i class="bi bi-x-circle-fill me-2"></i>Reject Leave Request #<?= $lr['id'] ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <p class="mb-2">Rejecting leave application for <strong><?= \App\helpers\ViewHelper::e($lr['first_name'] . ' ' . $lr['last_name']) ?></strong>.</p>
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                                                            <textarea class="form-control" name="rejection_reason" rows="3" placeholder="Explain clearly why this leave cannot be approved..." required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer py-2 bg-light">
                                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-sm btn-danger fw-semibold px-3">Confirm Rejection</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
            <small class="text-muted">
                Showing Page <?= $leaves['current_page'] ?> of <?= $leaves['last_page'] ?> (Total <?= $leaves['total'] ?> requests)
            </small>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= !$leaves['has_prev'] ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $leaves['current_page'] - 1 ?>&status=<?= urlencode($filters['status']) ?>&leave_type_id=<?= $filters['leave_type_id'] ?>&start_date=<?= $filters['start_date'] ?>&end_date=<?= $filters['end_date'] ?>">Previous</a>
                    </li>
                    <?php for ($p = 1; $p <= $leaves['last_page']; $p++): ?>
                        <li class="page-item <?= $p === $leaves['current_page'] ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $p ?>&status=<?= urlencode($filters['status']) ?>&leave_type_id=<?= $filters['leave_type_id'] ?>&start_date=<?= $filters['start_date'] ?>&end_date=<?= $filters['end_date'] ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= !$leaves['has_next'] ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $leaves['current_page'] + 1 ?>&status=<?= urlencode($filters['status']) ?>&leave_type_id=<?= $filters['leave_type_id'] ?>&start_date=<?= $filters['start_date'] ?>&end_date=<?= $filters['end_date'] ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>
