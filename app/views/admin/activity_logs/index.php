<div class="mb-4">
    <h3 class="fw-bold mb-0 text-dark">System Activity Audit Trail</h3>
    <p class="text-muted small">Immutable record of authentication events, leave actions, administrative updates, and security logs</p>
</div>

<!-- Search Bar -->
<div class="card p-3 mb-4">
    <form method="GET" action="<?= \App\helpers\ViewHelper::url('admin/activity-logs') ?>" class="row g-2">
        <div class="col-md-10">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0" name="search" value="<?= \App\helpers\ViewHelper::e($search) ?>" placeholder="Filter audit trail by action keyword, description text, email address, or client IP...">
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-sm w-100">Search Logs</button>
        </div>
    </form>
</div>

<!-- Logs Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>User / Origin</th>
                    <th>Client IP</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs['data'])): ?>
                    <tr><td colspan="5" class="text-center py-5 text-muted">No audit trail entries found.</td></tr>
                <?php else: ?>
                    <?php foreach ($logs['data'] as $log): ?>
                        <tr>
                            <td class="small text-muted font-monospace" style="white-space: nowrap;">
                                <?= \App\helpers\ViewHelper::formatDate($log['created_at'], 'd M Y, H:i:s') ?>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary font-monospace">
                                    <?= \App\helpers\ViewHelper::e($log['action']) ?>
                                </span>
                            </td>
                            <td class="small text-dark">
                                <?= \App\helpers\ViewHelper::e($log['description']) ?>
                            </td>
                            <td class="small">
                                <?php if (!empty($log['email'])): ?>
                                    <div class="fw-semibold text-dark"><?= \App\helpers\ViewHelper::e($log['email']) ?></div>
                                    <small class="text-muted"><?= ucfirst($log['role'] ?? 'user') ?></small>
                                <?php else: ?>
                                    <span class="text-muted">System / Anonymous</span>
                                <?php endif; ?>
                            </td>
                            <td class="small font-monospace text-muted">
                                <?= \App\helpers\ViewHelper::e($log['ip_address'] ?? '127.0.0.1') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($logs['last_page'] > 1): ?>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
            <small class="text-muted">
                Page <?= $logs['current_page'] ?> of <?= $logs['last_page'] ?> (Total <?= $logs['total'] ?> entries)
            </small>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= !$logs['has_prev'] ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $logs['current_page'] - 1 ?>&search=<?= urlencode($search) ?>">Previous</a>
                    </li>
                    <?php for ($p = 1; $p <= $logs['last_page']; $p++): ?>
                        <li class="page-item <?= $p === $logs['current_page'] ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $p ?>&search=<?= urlencode($search) ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= !$logs['has_next'] ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $logs['current_page'] + 1 ?>&search=<?= urlencode($search) ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>
