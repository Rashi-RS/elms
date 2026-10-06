<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0 text-dark">Leave Categories & Policies</h3>
        <p class="text-muted small mb-0">Define annual quotas, policy entitlements, and categories for employee time off</p>
    </div>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createLeaveTypeModal">
        <i class="bi bi-plus-lg me-1"></i> Add Leave Type
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Leave Category</th>
                    <th>Annual Quota</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leaveTypes)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">No leave categories configured.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($leaveTypes as $lt): ?>
                        <tr>
                            <td>
                                <strong class="text-dark"><?= \App\helpers\ViewHelper::e($lt['name']) ?></strong>
                            </td>
                            <td>
                                <span class="badge bg-primary fs-6 px-2.5 py-1.5"><?= $lt['annual_limit'] ?> Days/Year</span>
                            </td>
                            <td>
                                <span class="text-muted small"><?= \App\helpers\ViewHelper::e($lt['description'] ?? '—') ?></span>
                            </td>
                            <td>
                                <?= \App\helpers\ViewHelper::statusBadge($lt['status']) ?>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editLeaveTypeModal-<?= $lt['id'] ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </td>
                        </tr>

                        <!-- Edit Leave Type Modal -->
                        <div class="modal fade" id="editLeaveTypeModal-<?= $lt['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="<?= \App\helpers\ViewHelper::url("admin/leave-types/{$lt['id']}/edit") ?>" method="POST">
                                        <?= \App\helpers\ViewHelper::csrf() ?>
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Leave Policy</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Leave Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm" name="name" value="<?= \App\helpers\ViewHelper::e($lt['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Annual Limit (Days) <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control form-control-sm" name="annual_limit" value="<?= $lt['annual_limit'] ?>" min="1" max="365" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Description</label>
                                                <textarea class="form-control form-control-sm" name="description" rows="3"><?= \App\helpers\ViewHelper::e($lt['description'] ?? '') ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Status <span class="text-danger">*</span></label>
                                                <select class="form-select form-select-sm" name="status" required>
                                                    <option value="active" <?= ($lt['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                                                    <option value="inactive" <?= ($lt['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                                </select>
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

<!-- Create Leave Type Modal -->
<div class="modal fade" id="createLeaveTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= \App\helpers\ViewHelper::url('admin/leave-types') ?>" method="POST">
                <?= \App\helpers\ViewHelper::csrf() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Configure Leave Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="name" placeholder="e.g. Bereavement Leave" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Annual Limit (Days) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-sm" name="annual_limit" value="10" min="1" max="365" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Description</label>
                        <textarea class="form-control form-control-sm" name="description" rows="3" placeholder="Eligibility conditions and guidelines..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">Create Policy</button>
                </div>
            </form>
        </div>
    </div>
</div>
