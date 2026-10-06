<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-0 text-dark">Daily Attendance Roster</h3>
        <p class="text-muted small mb-0">Record check-ins, verify shift hours, and manage attendance status for all personnel</p>
    </div>
</div>

<!-- Date & Department Selector Card -->
<div class="card p-3 mb-4">
    <form method="GET" action="<?= \App\helpers\ViewHelper::url('admin/attendance') ?>" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light text-muted"><i class="bi bi-calendar3"></i> Date:</span>
                <input type="date" class="form-control" name="date" value="<?= \App\helpers\ViewHelper::e($date) ?>" onchange="this.form.submit()">
            </div>
        </div>

        <div class="col-md-4">
            <select class="form-select form-select-sm" name="department_id" onchange="this.form.submit()">
                <option value="">All Departments</option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= ($deptId == $d['id']) ? 'selected' : '' ?>>
                        <?= \App\helpers\ViewHelper::e($d['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4 d-flex justify-content-md-end gap-2">
            <a href="<?= \App\helpers\ViewHelper::url('admin/attendance?date=' . date('Y-m-d')) ?>" class="btn btn-outline-secondary btn-sm">
                Today
            </a>
            <a href="<?= \App\helpers\ViewHelper::url('admin/reports/attendance') ?>" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Full Report
            </a>
        </div>
    </form>
</div>

<!-- Daily Roster Table -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <span class="fw-bold text-dark">Roster for <?= \App\helpers\ViewHelper::formatDate($date) ?></span>
        <span class="badge bg-light text-muted border"><?= count($roster) ?> Active Staff</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Working Hours</th>
                    <th>Remarks</th>
                    <th class="text-end">Record</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($roster)): ?>
                    <tr><td colspan="8" class="text-center py-5 text-muted">No active staff found.</td></tr>
                <?php else: ?>
                    <?php foreach ($roster as $r): ?>
                        <tr>
                            <td>
                                <strong class="text-dark"><?= \App\helpers\ViewHelper::e($r['first_name'] . ' ' . $r['last_name']) ?></strong>
                                <div class="text-muted small font-monospace"><?= \App\helpers\ViewHelper::e($r['employee_code']) ?></div>
                            </td>
                            <td>
                                <span class="small text-secondary"><?= \App\helpers\ViewHelper::e($r['department_name'] ?? '—') ?></span>
                            </td>
                            <td>
                                <?= !empty($r['attendance_status']) ? \App\helpers\ViewHelper::statusBadge($r['attendance_status']) : '<span class="badge bg-light text-muted border">Not Logged</span>' ?>
                            </td>
                            <td class="small font-monospace">
                                <?= !empty($r['check_in']) ? \App\helpers\ViewHelper::formatTime($r['check_in']) : '—' ?>
                            </td>
                            <td class="small font-monospace">
                                <?= !empty($r['check_out']) ? \App\helpers\ViewHelper::formatTime($r['check_out']) : '—' ?>
                            </td>
                            <td class="small fw-semibold">
                                <?= ($r['working_hours'] > 0) ? "{$r['working_hours']} hrs" : '—' ?>
                            </td>
                            <td class="small text-muted text-truncate" style="max-width: 150px;">
                                <?= \App\helpers\ViewHelper::e($r['remarks'] ?? '—') ?>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#markModal-<?= $r['employee_id'] ?>">
                                    <i class="bi bi-pencil-square"></i> Mark
                                </button>
                            </td>
                        </tr>

                        <!-- Mark Attendance Modal for this Employee -->
                        <div class="modal fade" id="markModal-<?= $r['employee_id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content text-start">
                                    <form action="<?= \App\helpers\ViewHelper::url('admin/attendance/mark') ?>" method="POST">
                                        <?= \App\helpers\ViewHelper::csrf() ?>
                                        <input type="hidden" name="employee_id" value="<?= $r['employee_id'] ?>">
                                        <input type="hidden" name="attendance_date" value="<?= $date ?>">

                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Update Attendance: <?= \App\helpers\ViewHelper::e($r['first_name'] . ' ' . $r['last_name']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Date</label>
                                                <input type="text" class="form-control form-control-sm bg-light" value="<?= \App\helpers\ViewHelper::formatDate($date) ?>" readonly>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Attendance Status <span class="text-danger">*</span></label>
                                                <select class="form-select form-select-sm" name="status" required>
                                                    <option value="present" <?= ($r['attendance_status'] === 'present') ? 'selected' : '' ?>>Present</option>
                                                    <option value="half_day" <?= ($r['attendance_status'] === 'half_day') ? 'selected' : '' ?>>Half Day</option>
                                                    <option value="absent" <?= ($r['attendance_status'] === 'absent') ? 'selected' : '' ?>>Absent</option>
                                                    <option value="leave" <?= ($r['attendance_status'] === 'leave') ? 'selected' : '' ?>>On Leave</option>
                                                </select>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <label class="form-label small fw-semibold">Check In Time</label>
                                                    <input type="time" class="form-control form-control-sm" name="check_in" value="<?= $r['check_in'] ?? '09:00' ?>">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small fw-semibold">Check Out Time</label>
                                                    <input type="time" class="form-control form-control-sm" name="check_out" value="<?= $r['check_out'] ?? '17:30' ?>">
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Remarks / Notes</label>
                                                <input type="text" class="form-control form-control-sm" name="remarks" value="<?= \App\helpers\ViewHelper::e($r['remarks'] ?? '') ?>" placeholder="e.g. Approved half-day shift">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-sm btn-primary">Save Record</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
