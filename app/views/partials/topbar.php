<?php
$user = \App\helpers\Session::user();
$userName = ($user['first_name'] ?? 'User') . ' ' . ($user['last_name'] ?? '');
$userRole = ucfirst($user['role'] ?? 'Employee');
$userAvatar = !empty($user['profile_image']) ? \App\helpers\ViewHelper::url($user['profile_image']) : null;
$unreadCount = (new \App\models\Notification())->getUnreadCount((int)($user['id'] ?? 0));
?>
<header class="top-navbar d-flex align-items-center justify-content-between">
    <!-- Left: Sidebar Toggle & Title -->
    <div class="d-flex align-items-center gap-3">
        <button id="sidebar-toggle" class="btn btn-outline-secondary btn-sm d-lg-none" type="button">
            <i class="bi bi-list fs-5"></i>
        </button>
        <span class="fw-semibold text-secondary d-none d-sm-inline">
            <i class="bi bi-shield-check text-primary me-1"></i> <?= $userRole ?> Workspace
        </span>
    </div>

    <!-- Right: Notifications, Quick Actions, Profile Dropdown -->
    <div class="d-flex align-items-center gap-3">
        <!-- Notification Dropdown -->
        <div class="dropdown">
            <button class="btn btn-light position-relative rounded-circle p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell fs-5 text-secondary"></i>
                <span id="unread-notif-counter" class="badge bg-danger notif-badge" style="<?= $unreadCount > 0 ? '' : 'display: none;' ?>">
                    <?= $unreadCount ?>
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-0" style="width: 320px;">
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold">Notifications</h6>
                    <a href="<?= \App\helpers\ViewHelper::url('notifications') ?>" class="small text-decoration-none text-primary">View All</a>
                </div>
                <div class="p-2" style="max-height: 280px; overflow-y: auto;">
                    <?php
                    $quickNotifs = (new \App\models\Notification())->getAllForUser((int)($user['id'] ?? 0), 4);
                    if (empty($quickNotifs)):
                    ?>
                        <p class="text-muted small text-center p-3 mb-0">No new notifications</p>
                    <?php else: ?>
                        <?php foreach ($quickNotifs as $qn): ?>
                            <div class="p-2 border-bottom rounded-2 mb-1 <?= $qn['is_read'] ? 'opacity-75' : 'bg-light' ?>">
                                <div class="d-flex align-items-center justify-content-between">
                                    <strong class="small text-dark"><?= \App\helpers\ViewHelper::e($qn['title']) ?></strong>
                                    <small class="text-muted" style="font-size: 0.7rem;"><?= date('M d', strtotime($qn['created_at'])) ?></small>
                                </div>
                                <p class="small text-muted mb-0 mt-1" style="font-size: 0.8rem;"><?= \App\helpers\ViewHelper::e($qn['message']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </ul>
        </div>

        <!-- User Profile Dropdown -->
        <div class="dropdown">
            <button class="btn btn-light d-flex align-items-center gap-2 border-0 bg-transparent p-0" type="button" data-bs-toggle="dropdown">
                <?php if ($userAvatar): ?>
                    <img src="<?= $userAvatar ?>" alt="Avatar" class="avatar">
                <?php else: ?>
                    <span class="avatar bg-primary text-white"><?= strtoupper(substr($userName, 0, 1)) ?></span>
                <?php endif; ?>
                <div class="text-start d-none d-md-block">
                    <div class="fw-semibold small leading-tight"><?= \App\helpers\ViewHelper::e($userName) ?></div>
                    <div class="text-muted" style="font-size: 0.75rem;"><?= \App\helpers\ViewHelper::e($user['designation'] ?? $userRole) ?></div>
                </div>
                <i class="bi bi-chevron-down text-muted small ms-1"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                <li><h6 class="dropdown-header"><?= \App\helpers\ViewHelper::e($user['email'] ?? '') ?></h6></li>
                <?php if (!\App\helpers\Session::isAdmin()): ?>
                    <li><a class="dropdown-item" href="<?= \App\helpers\ViewHelper::url('employee/profile') ?>"><i class="bi bi-person me-2"></i> My Profile</a></li>
                    <li><a class="dropdown-item" href="<?= \App\helpers\ViewHelper::url('employee/my-leaves') ?>"><i class="bi bi-calendar-check me-2"></i> My Leaves</a></li>
                <?php endif; ?>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="<?= \App\helpers\ViewHelper::url('logout') ?>" method="POST" class="d-inline">
                        <?= \App\helpers\ViewHelper::csrf() ?>
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Sign Out</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
