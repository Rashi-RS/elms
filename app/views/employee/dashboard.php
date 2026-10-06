<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-0 text-dark">Welcome back, <?= \App\helpers\ViewHelper::e($employee['first_name']) ?>!</h3>
        <p class="text-muted small mb-0"><?= \App\helpers\ViewHelper::e($employee['designation']) ?> &bull; <?= \App\helpers\ViewHelper::e($employee['department_name'] ?? 'ELMS') ?> &bull; ID: <span class="font-monospace fw-semibold"><?= \App\helpers\ViewHelper::e($employee['employee_code']) ?></span></p>
    </div>
    <a href="<?= \App\helpers\ViewHelper::url('employee/apply-leave') ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle-fill me-1"></i> Apply for Leave
    </a>
</div>

<!-- Row 1: Today's Shift & Monthly Attendance Stats -->
<div class="row g-3 mb-4">
    <!-- Today's Clock In/Out Action Card -->
    <div class="col-lg-6">
        <div class="card p-4 h-100 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold text-dark"><i class="bi bi-clock-fill text-primary me-2"></i>Today's Attendance (<?= date('d M Y') ?>)</span>
                <?php if ($todayAttendance): ?>
                    <?= \App\helpers\ViewHelper::statusBadge($todayAttendance['status']) ?>
                <?php else: ?>
                    <span class="badge bg-secondary-subtle text-secondary">Not Checked In</span>
                <?php endif; ?>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="p-2.5 rounded bg-light border text-center">
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Check In Time</small>
                        <strong class="fs-5 text-dark"><?= !empty($todayAttendance['check_in']) ? \App\helpers\ViewHelper::formatTime($todayAttendance['check_in']) : '—' ?></strong>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2.5 rounded bg-light border text-center">
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Check Out Time</small>
                        <strong class="fs-5 text-dark"><?= !empty($todayAttendance['check_out']) ? \App\helpers\ViewHelper::formatTime($todayAttendance['check_out']) : '—' ?></strong>
                    </div>
                </div>
            </div>

            <div class="mt-auto d-flex gap-2">
                <form action="<?= \App\helpers\ViewHelper::url('employee/clock') ?>" method="POST" class="flex-grow-1">
                    <?= \App\helpers\ViewHelper::csrf() ?>
                    <input type="hidden" name="action" value="in">
                    <button type="submit" class="btn btn-outline-success w-100 btn-sm py-2" <?= (!empty($todayAttendance['check_in'])) ? 'disabled' : '' ?>>
                        <i class="bi bi-box-arrow-in-right me-1"></i> Clock In
                    </button>
                </form>

                <form action="<?= \App\helpers\ViewHelper::url('employee/clock') ?>" method="POST" class="flex-grow-1">
                    <?= \App\helpers\ViewHelper::csrf() ?>
                    <input type="hidden" name="action" value="out">
                    <button type="submit" class="btn btn-outline-danger w-100 btn-sm py-2" <?= empty($todayAttendance['check_in']) ? 'disabled' : '' ?>>
                        <i class="bi bi-box-arrow-right me-1"></i> <?= !empty($todayAttendance['check_out']) ? 'Update Clock Out' : 'Clock Out' ?>
                    </button>
                </form>

                <?php if ($todayAttendance): ?>
                <form action="<?= \App\helpers\ViewHelper::url('employee/clock') ?>" method="POST">
                    <?= \App\helpers\ViewHelper::csrf() ?>
                    <input type="hidden" name="action" value="reset">
                    <button type="submit" class="btn btn-outline-secondary btn-sm py-2 px-2.5" title="Reset Today's Shift (For Testing)" onclick="return confirm('Reset attendance for today so you can test clocking in again?');">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Monthly Attendance Overview Card -->
    <div class="col-lg-6">
        <div class="card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold text-dark"><i class="bi bi-calendar-check-fill text-success me-2"></i>Attendance Summary (<?= date('F Y') ?>)</span>
                <a href="<?= \App\helpers\ViewHelper::url('employee/my-attendance') ?>" class="small text-decoration-none">Full Sheet</a>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-3 text-center">
                    <div class="p-2 rounded bg-success bg-opacity-10 text-success">
                        <h4 class="fw-bold mb-0"><?= $monthlyStats['present'] ?></h4>
                        <small style="font-size: 0.7rem;">Present</small>
                    </div>
                </div>
                <div class="col-3 text-center">
                    <div class="p-2 rounded bg-info bg-opacity-10 text-info">
                        <h4 class="fw-bold mb-0"><?= $monthlyStats['half_day'] ?></h4>
                        <small style="font-size: 0.7rem;">Half-Day</small>
                    </div>
                </div>
                <div class="col-3 text-center">
                    <div class="p-2 rounded bg-secondary bg-opacity-10 text-secondary">
                        <h4 class="fw-bold mb-0"><?= $monthlyStats['leave'] ?></h4>
                        <small style="font-size: 0.7rem;">On Leave</small>
                    </div>
                </div>
                <div class="col-3 text-center">
                    <div class="p-2 rounded bg-danger bg-opacity-10 text-danger">
                        <h4 class="fw-bold mb-0"><?= $monthlyStats['absent'] ?></h4>
                        <small style="font-size: 0.7rem;">Absent</small>
                    </div>
                </div>
            </div>

            <div class="mt-auto">
                <div class="d-flex justify-content-between align-items-center small mb-1">
                    <span class="text-muted">Monthly Attendance Ratio</span>
                    <strong class="text-dark"><?= $monthlyStats['percentage'] ?>%</strong>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $monthlyStats['percentage'] ?>%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Annual Leave Quota Balances -->
<div class="card p-3 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-wallet-fill text-primary me-2"></i>My Leave Balance Quota (<?= date('Y') ?>)</h6>
        <a href="<?= \App\helpers\ViewHelper::url('employee/apply-leave') ?>" class="small text-decoration-none text-primary">Apply Leave &rarr;</a>
    </div>
    <div class="row g-2">
        <?php foreach ($balances as $bal): ?>
            <div class="col-6 col-md-4 col-xl">
                <div class="p-3 rounded bg-light border text-center h-100">
                    <span class="fw-semibold small text-dark d-block text-truncate"><?= \App\helpers\ViewHelper::e($bal['leave_type_name']) ?></span>
                    <h3 class="fw-bold text-primary mb-0 mt-1"><?= $bal['remaining_days'] ?></h3>
                    <small class="text-muted" style="font-size: 0.72rem;">Days Left (<?= $bal['used_days'] ?>/<?= $bal['allocated_days'] ?> used)</small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Row 3: Recent Leave Requests & Notifications -->
<div class="row g-3">
    <!-- Recent Leaves -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-clock-history text-primary me-2"></i>Recent Leave Applications</span>
                <a href="<?= \App\helpers\ViewHelper::url('employee/my-leaves') ?>" class="small text-decoration-none">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Duration</th>
                                <th>Days</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentLeaves)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No leave requests submitted yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentLeaves as $rl): ?>
                                    <tr>
                                        <td><strong><?= \App\helpers\ViewHelper::e($rl['leave_type_name']) ?></strong></td>
                                        <td class="small"><?= date('M d', strtotime($rl['start_date'])) ?> &rarr; <?= date('M d', strtotime($rl['end_date'])) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= $rl['total_days'] ?>d</span></td>
                                        <td><?= \App\helpers\ViewHelper::statusBadge($rl['status']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent In-App Notifications -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-bell-fill text-warning me-2"></i>Notifications</span>
                <a href="<?= \App\helpers\ViewHelper::url('notifications') ?>" class="small text-decoration-none">Center (<?= $unreadNotifCount ?>)</a>
            </div>
            <div class="card-body p-3">
                <div class="d-flex flex-column gap-2">
                    <?php if (empty($notifications)): ?>
                        <p class="text-muted small text-center mb-0">No notifications.</p>
                    <?php else: ?>
                        <?php foreach ($notifications as $n): ?>
                            <div class="p-2 rounded border <?= $n['is_read'] ? 'bg-light opacity-75' : 'bg-primary-subtle' ?>">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="small text-dark"><?= \App\helpers\ViewHelper::e($n['title']) ?></strong>
                                    <small class="text-muted" style="font-size: 0.68rem;"><?= date('M d', strtotime($n['created_at'])) ?></small>
                                </div>
                                <p class="small text-muted mb-0" style="font-size: 0.78rem;"><?= \App\helpers\ViewHelper::e($n['message']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
