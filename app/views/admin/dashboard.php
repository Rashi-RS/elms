<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0 text-dark">Executive Dashboard</h3>
        <p class="text-muted small mb-0">Real-time organizational metrics, attendance pulse, and leave governance</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= \App\helpers\ViewHelper::url('admin/employees/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus-fill me-1"></i> New Employee
        </a>
        <a href="<?= \App\helpers\ViewHelper::url('admin/reports') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-download me-1"></i> Reports
        </a>
    </div>
</div>

<!-- KPI Cards Row 1: Employee & Department Pulse -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card kpi-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Staff</div>
                    <h3 class="fw-bold mb-0 text-dark mt-1"><?= $stats['total_employees'] ?></h3>
                    <small class="text-success fw-medium"><i class="bi bi-check-circle"></i> <?= $stats['active_employees'] ?> Active</small>
                </div>
                <div class="kpi-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card kpi-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Present Today</div>
                    <h3 class="fw-bold mb-0 text-dark mt-1"><?= $stats['present_today'] ?></h3>
                    <small class="text-info fw-medium"><i class="bi bi-clock"></i> <?= $stats['half_day_today'] ?> Half-day</small>
                </div>
                <div class="kpi-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-person-check-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card kpi-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Absent Today</div>
                    <h3 class="fw-bold mb-0 text-dark mt-1"><?= $stats['absent_today'] ?></h3>
                    <small class="text-secondary fw-medium"><i class="bi bi-calendar-event"></i> <?= $stats['leave_today'] ?> On Leave</small>
                </div>
                <div class="kpi-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-person-x-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card kpi-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Pending Leaves</div>
                    <h3 class="fw-bold mb-0 text-dark mt-1"><?= $stats['pending_leaves'] ?></h3>
                    <small class="text-warning fw-medium"><i class="bi bi-hourglass-split"></i> Awaiting Action</small>
                </div>
                <div class="kpi-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-calendar-range-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row: Department Distribution, Today's Attendance, Leave Status -->
<div class="row g-3 mb-4">
    <!-- Chart 1: Department Distribution -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Staff by Department</span>
                <span class="badge bg-light text-muted border"><?= $stats['total_departments'] ?> depts</span>
            </div>
            <div class="card-body d-flex flex-column justify-content-center p-3">
                <div style="height: 240px; position: relative;">
                    <canvas id="deptDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart 2: Today's Attendance Pulse -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-bar-chart-fill text-success me-2"></i>Today's Attendance</span>
                <span class="badge bg-success-subtle text-success"><?= date('d M Y') ?></span>
            </div>
            <div class="card-body d-flex flex-column justify-content-center p-3">
                <div style="height: 240px; position: relative;">
                    <canvas id="attendanceBreakdownChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart 3: Leave Requests Overview -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-pie-chart text-warning me-2"></i>Leave Status Ratio</span>
                <a href="<?= \App\helpers\ViewHelper::url('admin/leaves') ?>" class="small text-decoration-none">Review All</a>
            </div>
            <div class="card-body d-flex flex-column justify-content-center p-3">
                <div style="height: 240px; position: relative;">
                    <canvas id="leaveStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tables Row: Pending Leave Requests & Recent System Activity -->
<div class="row g-3">
    <!-- Pending Leaves Table -->
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-clock-history text-warning me-2"></i>Pending Leave Requests</span>
                <a href="<?= \App\helpers\ViewHelper::url('admin/leaves?status=pending') ?>" class="small text-decoration-none text-primary">View All (<?= $stats['pending_leaves'] ?>)</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Leave Type</th>
                                <th>Duration</th>
                                <th>Days</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pendingLeaves)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-check2-circle fs-3 text-success d-block mb-1"></i>
                                        No pending leave applications awaiting review!
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pendingLeaves as $pl): ?>
                                    <tr id="leave-row-<?= $pl['id'] ?>">
                                        <td>
                                            <div class="fw-semibold text-dark"><?= \App\helpers\ViewHelper::e($pl['first_name'] . ' ' . $pl['last_name']) ?></div>
                                            <small class="text-muted font-monospace"><?= \App\helpers\ViewHelper::e($pl['employee_code']) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border"><?= \App\helpers\ViewHelper::e($pl['leave_type_name']) ?></span>
                                        </td>
                                        <td>
                                            <div class="small"><?= date('M d', strtotime($pl['start_date'])) ?> &rarr; <?= date('M d', strtotime($pl['end_date'])) ?></div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary"><?= $pl['total_days'] ?>d</span>
                                        </td>
                                        <td class="text-end leave-actions-cell">
                                            <button type="button" class="btn btn-sm btn-success px-2 py-1" data-bs-toggle="modal" data-bs-target="#dash-approve-<?= $pl['id'] ?>">
                                                Approve
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1" data-bs-toggle="modal" data-bs-target="#dash-reject-<?= $pl['id'] ?>">
                                                Reject
                                            </button>

                                            <!-- Dashboard Approve Modal -->
                                            <div class="modal fade" id="dash-approve-<?= $pl['id'] ?>" tabindex="-1">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content text-start">
                                                        <form action="<?= \App\helpers\ViewHelper::url("admin/leaves/{$pl['id']}/approve") ?>" method="POST">
                                                            <?= \App\helpers\ViewHelper::csrf() ?>
                                                            <input type="hidden" name="redirect_to" value="<?= \App\helpers\ViewHelper::url('admin/dashboard') ?>">
                                                            <div class="modal-header bg-success bg-opacity-10 py-2">
                                                                <h6 class="modal-title fw-bold text-success mb-0"><i class="bi bi-check-circle-fill me-1"></i>Approve Leave #<?= $pl['id'] ?></h6>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body p-3">
                                                                <p class="small mb-1">Approve leave request for <strong><?= \App\helpers\ViewHelper::e($pl['first_name'] . ' ' . $pl['last_name']) ?></strong>?</p>
                                                                <small class="text-muted d-block"><?= date('d M', strtotime($pl['start_date'])) ?> &rarr; <?= date('d M Y', strtotime($pl['end_date'])) ?> (<?= $pl['total_days'] ?> days)</small>
                                                            </div>
                                                            <div class="modal-footer py-1 bg-light">
                                                                <button type="button" class="btn btn-xs btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-xs btn-success fw-semibold">Confirm Approve</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Dashboard Reject Modal -->
                                            <div class="modal fade" id="dash-reject-<?= $pl['id'] ?>" tabindex="-1">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content text-start">
                                                        <form action="<?= \App\helpers\ViewHelper::url("admin/leaves/{$pl['id']}/reject") ?>" method="POST">
                                                            <?= \App\helpers\ViewHelper::csrf() ?>
                                                            <input type="hidden" name="redirect_to" value="<?= \App\helpers\ViewHelper::url('admin/dashboard') ?>">
                                                            <div class="modal-header bg-danger bg-opacity-10 py-2">
                                                                <h6 class="modal-title fw-bold text-danger mb-0"><i class="bi bi-x-circle-fill me-1"></i>Reject Leave #<?= $pl['id'] ?></h6>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body p-3">
                                                                <label class="form-label small fw-semibold">Rejection Reason *</label>
                                                                <textarea class="form-control form-control-sm" name="rejection_reason" rows="2" placeholder="Reason..." required></textarea>
                                                            </div>
                                                            <div class="modal-footer py-1 bg-light">
                                                                <button type="button" class="btn btn-xs btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-xs btn-danger fw-semibold">Confirm Reject</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent System Activities Feed -->
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-activity text-primary me-2"></i>Audit Trail & Activity</span>
                <a href="<?= \App\helpers\ViewHelper::url('admin/activity-logs') ?>" class="small text-decoration-none">Full Audit</a>
            </div>
            <div class="card-body p-3">
                <div class="d-flex flex-column gap-3">
                    <?php if (empty($recentActivities)): ?>
                        <p class="text-muted small text-center mb-0">No system activities recorded yet.</p>
                    <?php else: ?>
                        <?php foreach ($recentActivities as $act): ?>
                            <div class="d-flex align-items-start gap-2 border-bottom pb-2">
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill mt-1" style="font-size: 0.65rem;">
                                    <i class="bi bi-clock"></i> <?= date('H:i', strtotime($act['created_at'])) ?>
                                </span>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold small text-dark text-truncate"><?= \App\helpers\ViewHelper::e($act['action']) ?></div>
                                    <div class="text-muted text-break" style="font-size: 0.78rem;"><?= \App\helpers\ViewHelper::e($act['description']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inline Chart Data Payloads for Chart.js Handlers -->
<script>
    window.deptChartData = <?= json_encode($deptChart) ?>;
    window.attendanceChartData = <?= json_encode($attendanceChart) ?>;
    window.leaveChartData = <?= json_encode($leaveChart) ?>;
</script>
