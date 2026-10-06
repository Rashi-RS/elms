<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-0 text-dark">Employee Management</h3>
        <p class="text-muted small mb-0">Total personnel: <?= $employees['total'] ?> &bull; Filter, inspect, or onboard new employees</p>
    </div>
    <a href="<?= \App\helpers\ViewHelper::url('admin/employees/create') ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-person-plus-fill me-1"></i> Add New Employee
    </a>
</div>

<!-- Search & Filtering Bar -->
<div class="card p-3 mb-4">
    <form method="GET" action="<?= \App\helpers\ViewHelper::url('admin/employees') ?>" class="row g-2">
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0" name="search" value="<?= \App\helpers\ViewHelper::e($filters['search']) ?>" placeholder="Search by name, employee code, or email...">
            </div>
        </div>

        <div class="col-md-3">
            <select class="form-select form-select-sm" name="department_id">
                <option value="">All Departments</option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= ($filters['department_id'] == $d['id']) ? 'selected' : '' ?>>
                        <?= \App\helpers\ViewHelper::e($d['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <select class="form-select form-select-sm" name="status">
                <option value="">All Statuses</option>
                <option value="active" <?= ($filters['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($filters['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                <option value="terminated" <?= ($filters['status'] === 'terminated') ? 'selected' : '' ?>>Terminated</option>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            <?php if (!empty($filters['search']) || !empty($filters['department_id']) || !empty($filters['status'])): ?>
                <a href="<?= \App\helpers\ViewHelper::url('admin/employees') ?>" class="btn btn-light btn-sm text-muted" title="Reset Filters"><i class="bi bi-x-lg"></i></a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Employees Directory Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Code</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Joining Date</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($employees['data'])): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 text-secondary opacity-50 d-block mb-2"></i>
                            No employee records match the given criteria.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($employees['data'] as $emp): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if (!empty($emp['profile_image'])): ?>
                                        <img src="<?= \App\helpers\ViewHelper::url($emp['profile_image']) ?>" class="avatar" alt="Avatar">
                                    <?php else: ?>
                                        <span class="avatar bg-primary text-white"><?= strtoupper(substr($emp['first_name'], 0, 1)) ?></span>
                                    <?php endif; ?>
                                    <div>
                                        <a href="<?= \App\helpers\ViewHelper::url("admin/employees/{$emp['id']}") ?>" class="fw-semibold text-dark text-decoration-none">
                                            <?= \App\helpers\ViewHelper::e($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                        </a>
                                        <div class="text-muted small"><?= \App\helpers\ViewHelper::e($emp['email'] ?? 'No user linked') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark font-monospace border"><?= \App\helpers\ViewHelper::e($emp['employee_code']) ?></span>
                            </td>
                            <td>
                                <span class="text-secondary"><?= \App\helpers\ViewHelper::e($emp['department_name'] ?? 'Unassigned') ?></span>
                            </td>
                            <td>
                                <span class="small text-dark fw-medium"><?= \App\helpers\ViewHelper::e($emp['designation']) ?></span>
                            </td>
                            <td>
                                <span class="small text-muted"><?= \App\helpers\ViewHelper::formatDate($emp['joining_date']) ?></span>
                            </td>
                            <td>
                                <?= \App\helpers\ViewHelper::statusBadge($emp['status']) ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= \App\helpers\ViewHelper::url("admin/employees/{$emp['id']}") ?>" class="btn btn-outline-secondary" title="View Profile & Personal Info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#empLeaveModal-<?= $emp['id'] ?>" title="Leave Balance & History">
                                        <i class="bi bi-calendar2-check"></i>
                                    </button>
                                    <a href="<?= \App\helpers\ViewHelper::url("admin/employees/{$emp['id']}/edit") ?>" class="btn btn-outline-primary" title="Edit Profile">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?= \App\helpers\ViewHelper::url("admin/employees/{$emp['id']}/toggle-status") ?>" method="POST" class="d-inline" onsubmit="return confirm('Toggle status for this employee?');">
                                        <?= \App\helpers\ViewHelper::csrf() ?>
                                        <button type="submit" class="btn btn-outline-warning" title="Toggle Active/Inactive">
                                            <i class="bi bi-power"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#empDeleteModal-<?= $emp['id'] ?>" title="Remove / Delete Employee">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Quick Leave Ledger & History Modal -->
                                <div class="modal fade" id="empLeaveModal-<?= $emp['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content text-start">
                                            <div class="modal-header bg-primary bg-opacity-10 py-3">
                                                <div>
                                                    <h5 class="modal-title fw-bold text-primary fs-6 mb-0">
                                                        <i class="bi bi-wallet2 me-2"></i>Leave Summary & Ledger — <?= \App\helpers\ViewHelper::e($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                                    </h5>
                                                    <small class="text-muted font-monospace"><?= \App\helpers\ViewHelper::e($emp['employee_code']) ?> &bull; <?= \App\helpers\ViewHelper::e($emp['designation']) ?> &bull; <?= \App\helpers\ViewHelper::e($emp['department_name'] ?? 'ELMS') ?></small>
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <!-- Section 1: Leave Quotas (Total, Taken, Remaining) -->
                                                <h6 class="fw-bold text-dark mb-1"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Annual Leave Quotas (<?= date('Y') ?>)</h6>
                                                <p class="text-muted small mb-3">Employee ne kitni leave li hai aur kitni bachi hai:</p>

                                                <div class="row g-2 mb-4">
                                                    <?php 
                                                    $empBals = $employeeBalances[$emp['id']] ?? [];
                                                    if (empty($empBals)): 
                                                    ?>
                                                        <div class="col-12"><p class="text-muted small mb-0">No leave quotas allocated for current year.</p></div>
                                                    <?php else: ?>
                                                        <?php foreach ($empBals as $b): 
                                                            $percent = $b['allocated_days'] > 0 ? min(100, round(($b['used_days'] / $b['allocated_days']) * 100)) : 0;
                                                        ?>
                                                            <div class="col-sm-6 col-md-4">
                                                                <div class="p-3 rounded-3 bg-light border h-100">
                                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                                        <strong class="text-dark small text-truncate"><?= \App\helpers\ViewHelper::e($b['leave_type_name']) ?></strong>
                                                                        <span class="badge bg-primary-subtle text-primary fw-bold" style="font-size: 0.65rem;"><?= $b['allocated_days'] ?>d Limit</span>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between align-items-baseline mt-2">
                                                                        <div>
                                                                            <small class="text-muted d-block" style="font-size: 0.7rem;">Li Hai (Taken)</small>
                                                                            <strong class="text-danger fs-6"><?= $b['used_days'] ?></strong> <small class="text-muted">days</small>
                                                                        </div>
                                                                        <div class="text-end">
                                                                            <small class="text-muted d-block" style="font-size: 0.7rem;">Bachi Hai (Left)</small>
                                                                            <strong class="text-success fs-5"><?= $b['remaining_days'] ?></strong> <small class="text-muted">days</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="progress mt-2" style="height: 5px;">
                                                                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $percent ?>%;"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Section 2: Recent Leaves Taken (Konsi leave li hai) -->
                                                <h6 class="fw-bold text-dark mb-1"><i class="bi bi-clock-history text-primary me-2"></i>Konsi Leave Li Hai (Recent Applications)</h6>
                                                <?php 
                                                $empLvs = $employeeLeaves[$emp['id']] ?? [];
                                                if (empty($empLvs)): 
                                                ?>
                                                    <div class="alert alert-light border small text-muted mb-0">
                                                        <i class="bi bi-info-circle me-1"></i> Is employee ne abhi tak koi leave apply nahi ki hai.
                                                    </div>
                                                <?php else: ?>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-hover align-middle mb-0">
                                                            <thead class="table-light">
                                                                <tr class="small text-muted">
                                                                    <th>Leave Category</th>
                                                                    <th>Dates</th>
                                                                    <th>Days</th>
                                                                    <th>Reason</th>
                                                                    <th>Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($empLvs as $lv): ?>
                                                                    <tr>
                                                                        <td><span class="badge bg-light text-dark border"><?= \App\helpers\ViewHelper::e($lv['leave_type_name']) ?></span></td>
                                                                        <td class="small"><?= date('d M', strtotime($lv['start_date'])) ?> &rarr; <?= date('d M Y', strtotime($lv['end_date'])) ?></td>
                                                                        <td><strong class="small"><?= $lv['total_days'] ?>d</strong></td>
                                                                        <td class="small text-muted text-truncate" style="max-width: 220px;" title="<?= \App\helpers\ViewHelper::e($lv['reason']) ?>">
                                                                            <?= \App\helpers\ViewHelper::e($lv['reason']) ?>
                                                                        </td>
                                                                        <td><?= \App\helpers\ViewHelper::statusBadge($lv['status']) ?></td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="modal-footer py-2 bg-light d-flex justify-content-between">
                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <a href="<?= \App\helpers\ViewHelper::url("admin/employees/{$emp['id']}") ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-person-badge me-1"></i> Full Personal Profile & Dossier &rarr;
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Employee Confirmation Modal -->
                                <div class="modal fade" id="empDeleteModal-<?= $emp['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content text-start">
                                            <form action="<?= \App\helpers\ViewHelper::url("admin/employees/{$emp['id']}/delete") ?>" method="POST">
                                                <?= \App\helpers\ViewHelper::csrf() ?>
                                                <div class="modal-header bg-danger bg-opacity-10 py-3">
                                                    <h5 class="modal-title fw-bold text-danger fs-6 mb-0">
                                                        <i class="bi bi-trash3-fill me-2"></i>Remove Employee
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <p class="mb-2">Are you sure you want to permanently remove this employee?</p>
                                                    <div class="p-3 bg-light rounded-3 small border mb-3">
                                                        <div><strong>Name:</strong> <?= \App\helpers\ViewHelper::e($emp['first_name'] . ' ' . $emp['last_name']) ?></div>
                                                        <div><strong>Employee ID:</strong> <span class="font-monospace"><?= \App\helpers\ViewHelper::e($emp['employee_code']) ?></span></div>
                                                        <div><strong>Email:</strong> <?= \App\helpers\ViewHelper::e($emp['email'] ?? 'N/A') ?></div>
                                                        <div><strong>Department:</strong> <?= \App\helpers\ViewHelper::e($emp['department_name'] ?? 'Unassigned') ?></div>
                                                    </div>
                                                    <div class="alert alert-danger bg-opacity-10 small mb-0">
                                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <strong>Warning:</strong> This will delete their profile, user login account, attendance timesheets, and leave records.
                                                    </div>
                                                </div>
                                                <div class="modal-footer py-2 bg-light">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-sm btn-danger fw-semibold px-3">Yes, Remove Employee</button>
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

    <!-- Server-side Pagination Bar -->
    <?php if ($employees['last_page'] > 1): ?>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
            <small class="text-muted">
                Showing Page <?= $employees['current_page'] ?> of <?= $employees['last_page'] ?> (Total <?= $employees['total'] ?> employees)
            </small>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= !$employees['has_prev'] ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $employees['current_page'] - 1 ?>&search=<?= urlencode($filters['search']) ?>&department_id=<?= $filters['department_id'] ?>&status=<?= $filters['status'] ?>">Previous</a>
                    </li>
                    <?php for ($p = 1; $p <= $employees['last_page']; $p++): ?>
                        <li class="page-item <?= $p === $employees['current_page'] ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $p ?>&search=<?= urlencode($filters['search']) ?>&department_id=<?= $filters['department_id'] ?>&status=<?= $filters['status'] ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= !$employees['has_next'] ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $employees['current_page'] + 1 ?>&search=<?= urlencode($filters['search']) ?>&department_id=<?= $filters['department_id'] ?>&status=<?= $filters['status'] ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>
