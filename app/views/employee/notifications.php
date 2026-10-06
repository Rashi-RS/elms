<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-0 text-dark">Notifications Center</h3>
        <p class="text-muted small mb-0">System communications, leave approvals, policy updates, and attendance alerts</p>
    </div>
    <?php if (!empty($notifications)): ?>
        <form action="<?= \App\helpers\ViewHelper::url('notifications/read-all') ?>" method="POST">
            <?= \App\helpers\ViewHelper::csrf() ?>
            <button type="submit" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-check-all me-1"></i> Mark All as Read
            </button>
        </form>
    <?php endif; ?>
</div>

<div class="card">
    <div class="p-3">
        <?php if (empty($notifications)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bell-slash fs-1 text-secondary opacity-50 d-block mb-2"></i>
                You have no notifications in your inbox.
            </div>
        <?php else: ?>
            <div class="d-flex flex-column gap-2">
                <?php foreach ($notifications as $n): ?>
                    <div id="notif-item-<?= $n['id'] ?>" class="p-3 rounded-3 border d-flex justify-content-between align-items-start gap-3 <?= $n['is_read'] ? 'bg-white opacity-75' : 'bg-light fw-semibold border-primary border-opacity-25' ?>">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="kpi-icon <?= $n['is_read'] ? 'bg-secondary bg-opacity-10 text-secondary' : 'bg-primary bg-opacity-10 text-primary' ?>" style="width: 40px; height: 40px; font-size: 1.1rem;">
                                <i class="bi <?= ($n['type'] === 'leave') ? 'bi-calendar-check' : 'bi-info-circle' ?>"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-dark"><?= \App\helpers\ViewHelper::e($n['title']) ?></h6>
                                <p class="mb-1 text-muted small fw-normal"><?= \App\helpers\ViewHelper::e($n['message']) ?></p>
                                <small class="text-muted font-monospace" style="font-size: 0.72rem;"><?= \App\helpers\ViewHelper::formatDate($n['created_at'], 'd M Y, h:i A') ?></small>
                            </div>
                        </div>

                        <?php if (!$n['is_read']): ?>
                            <button type="button" class="btn btn-sm btn-light ajax-mark-notif-read text-primary" data-id="<?= $n['id'] ?>" title="Mark as read">
                                <i class="bi bi-check2"></i> Mark read
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
