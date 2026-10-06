<aside id="sidebar" class="d-flex flex-column flex-shrink-0">
    <div class="brand d-flex align-items-center gap-2">
        <i class="bi bi-building-fill-gear text-primary fs-4"></i>
        <span>ELMS Admin</span>
    </div>

    <ul class="nav nav-pills flex-column mb-auto py-3">
        <li class="nav-item">
            <a href="<?= \App\helpers\ViewHelper::url('admin/dashboard') ?>" class="nav-link <?= \App\helpers\ViewHelper::activeNav('/admin/dashboard') ?>">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <div class="section-heading">Personnel & Organization</div>

        <li class="nav-item">
            <a href="<?= \App\helpers\ViewHelper::url('admin/employees') ?>" class="nav-link <?= \App\helpers\ViewHelper::activeNav('/admin/employees') ?>">
                <i class="bi bi-people-fill"></i>
                <span>Employees</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="<?= \App\helpers\ViewHelper::url('admin/departments') ?>" class="nav-link <?= \App\helpers\ViewHelper::activeNav('/admin/departments') ?>">
                <i class="bi bi-diagram-3-fill"></i>
                <span>Departments</span>
            </a>
        </li>

        <div class="section-heading">Time & Attendance</div>

        <li class="nav-item">
            <a href="<?= \App\helpers\ViewHelper::url('admin/leaves') ?>" class="nav-link <?= \App\helpers\ViewHelper::activeNav('/admin/leaves') ?>">
                <i class="bi bi-calendar-event-fill"></i>
                <span>Leave Requests</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="<?= \App\helpers\ViewHelper::url('admin/leave-types') ?>" class="nav-link <?= \App\helpers\ViewHelper::activeNav('/admin/leave-types') ?>">
                <i class="bi bi-tags-fill"></i>
                <span>Leave Types</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="<?= \App\helpers\ViewHelper::url('admin/attendance') ?>" class="nav-link <?= \App\helpers\ViewHelper::activeNav('/admin/attendance') ?>">
                <i class="bi bi-clock-history"></i>
                <span>Attendance</span>
            </a>
        </li>

        <div class="section-heading">Insights & Governance</div>

        <li class="nav-item">
            <a href="<?= \App\helpers\ViewHelper::url('admin/reports') ?>" class="nav-link <?= \App\helpers\ViewHelper::activeNav('/admin/reports') ?>">
                <i class="bi bi-file-earmark-bar-graph-fill"></i>
                <span>Reports</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="<?= \App\helpers\ViewHelper::url('notifications') ?>" class="nav-link <?= \App\helpers\ViewHelper::activeNav('/notifications') ?>">
                <i class="bi bi-bell-fill"></i>
                <span>Notifications</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="<?= \App\helpers\ViewHelper::url('admin/activity-logs') ?>" class="nav-link <?= \App\helpers\ViewHelper::activeNav('/admin/activity-logs') ?>">
                <i class="bi bi-journal-text"></i>
                <span>Activity Logs</span>
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
