<aside id="sidebar" class="d-flex flex-column flex-shrink-0">
    <div class="brand d-flex align-items-center gap-2">
        <i class="bi bi-person-badge-fill text-primary fs-4"></i>
        <span>ELMS Portal</span>
    </div>

    <ul class="nav nav-pills flex-column mb-auto py-3">
        <li class="nav-item">
            <a href="<?= \App\helpers\ViewHelper::url('employee/dashboard') ?>" class="nav-link <?= \App\helpers\ViewHelper::activeNav('/employee/dashboard') ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <div class="section-heading">My Workplace</div>

        <li class="nav-item">
            <a href="<?= \App\helpers\ViewHelper::url('employee/profile') ?>" class="nav-link <?= \App\helpers\ViewHelper::activeNav('/employee/profile') ?>">
                <i class="bi bi-person-circle"></i>
                <span>My Profile</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="<?= \App\helpers\ViewHelper::url('employee/my-attendance') ?>" class="nav-link <?= \App\helpers\ViewHelper::activeNav('/employee/my-attendance') ?>">
                <i class="bi bi-clock-history"></i>
                <span>My Attendance</span>
            </a>
        </li>

        <div class="section-heading">Time Off Management</div>

        <li class="nav-item">
            <a href="<?= \App\helpers\ViewHelper::url('employee/apply-leave') ?>" class="nav-link <?= \App\helpers\ViewHelper::activeNav('/employee/apply-leave') ?>">
                <i class="bi bi-plus-circle-fill text-primary"></i>
                <span>Apply for Leave</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="<?= \App\helpers\ViewHelper::url('employee/my-leaves') ?>" class="nav-link <?= \App\helpers\ViewHelper::activeNav('/employee/my-leaves') ?>">
                <i class="bi bi-calendar2-check-fill"></i>
                <span>My Leave History</span>
            </a>
        </li>

        <div class="section-heading">Communications</div>

        <li class="nav-item">
            <a href="<?= \App\helpers\ViewHelper::url('notifications') ?>" class="nav-link <?= \App\helpers\ViewHelper::activeNav('/notifications') ?>">
                <i class="bi bi-bell-fill"></i>
                <span>Notifications</span>
            </a>
        </li>
    </ul>

    <div class="p-3 border-top border-secondary border-opacity-25">
        <form action="<?= \App\helpers\ViewHelper::url('logout') ?>" method="POST">
            <?= \App\helpers\ViewHelper::csrf() ?>
            <button type="submit" class="btn btn-outline-danger w-100 btn-sm d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
            </button>
        </form>
    </div>
</aside>
