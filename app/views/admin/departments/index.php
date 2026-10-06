<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0 text-dark">Department Management</h3>
        <p class="text-muted small mb-0">Organize organizational branches and monitor personnel distribution</p>
    </div>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createDeptModal">
        <i class="bi bi-plus-lg me-1"></i> Add Department
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Department Name</th>
                    <th>Description</th>
                    <th>Personnel Count</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($departments)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">No departments configured.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($departments as $dept): ?>
                        <tr>
                            <td>
                                <strong class="text-dark"><?= \App\helpers\ViewHelper::e($dept['name']) ?></strong>
                            </td>
                            <td>
                                <span class="text-muted small"><?= \App\helpers\ViewHelper::e($dept['description'] ?? '—') ?></span>
                            </td>
                            <td>
                                <a href="<?= \App\helpers\ViewHelper::url("admin/employees?department_id={$dept['id']}") ?>" class="badge bg-primary-subtle text-primary text-decoration-none px-2.5 py-1.5">
                                    <i class="bi bi-people-fill me-1"></i> <?= $dept['employee_count'] ?> Employees
                                </a>
                            </td>
                            <td>
                                <?= \App\helpers\ViewHelper::statusBadge($dept['status']) ?>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editDeptModal-<?= $dept['id'] ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </td>
                        </tr>

                        <!-- Edit Department Modal -->
                        <div class="modal fade" id="editDeptModal-<?= $dept['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="<?= \App\helpers\ViewHelper::url("admin/departments/{$dept['id']}/edit") ?>" method="POST">
                                        <?= \App\helpers\ViewHelper::csrf() ?>
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Department</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Department Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm" name="name" value="<?= \App\helpers\ViewHelper::e($dept['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Description</label>
                                                <textarea class="form-control form-control-sm" name="description" rows="3"><?= \App\helpers\ViewHelper::e($dept['description'] ?? '') ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Status <span class="text-danger">*</span></label>
                                                <select class="form-select form-select-sm" name="status" required>
                                                    <option value="active" <?= ($dept['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                                                    <option value="inactive" <?= ($dept['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                                </select>
                                                <div class="form-text" style="font-size: 0.75rem;">Cannot be deactivated if employees are actively assigned.</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-sm btn-primary">Save Changes</button>
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

<!-- Create Department Modal -->
<div class="modal fade" id="createDeptModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= \App\helpers\ViewHelper::url('admin/departments') ?>" method="POST">
                <?= \App\helpers\ViewHelper::csrf() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Department Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="name" placeholder="e.g. Quality Assurance" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Description</label>
                        <textarea class="form-control form-control-sm" name="description" rows="3" placeholder="Core functions and domain responsibility..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">Create Department</button>
                </div>
            </form>
        </div>
    </div>
</div>
