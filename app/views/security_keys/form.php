<div class="card-smm animate-fade-up">
    <div class="card-smm-header">
        <h3><i class="fas fa-<?= $edit_mode ? 'edit' : 'key' ?> me-2" style="color:var(--amber);"></i> <?= $edit_mode ? 'Edit' : 'Generate' ?> Security Key</h3>
    </div>
    <div class="card-smm-body">
        <form method="POST" action="security_keys/<?= $edit_mode ? 'edit?id=' . $key['id'] : 'create' ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label-smm">Department *</label>
                    <select name="department_id" class="form-control-smm" required>
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>" <?= (($key['department_id'] ?? '') == $dept['id']) ? 'selected' : '' ?>>
                                <?= escape($dept['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($edit_mode): ?>
                <div class="col-md-6">
                    <label class="form-label-smm">Status</label>
                    <select name="status" class="form-control-smm">
                        <option value="active" <?= ($key['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($key['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <?php endif; ?>
                <?php if (!$edit_mode): ?>
                <div class="col-12">
                    <div style="background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.2);border-radius:12px;padding:16px;display:flex;align-items:center;gap:12px;">
                        <i class="fas fa-info-circle" style="color:var(--blue-primary);font-size:1.2rem;"></i>
                        <span style="font-size:0.85rem;color:var(--text-secondary);">A new API key and secret key will be auto-generated for the selected department.</span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn-smm btn-smm-primary"><?= $edit_mode ? 'Update' : 'Generate Key' ?></button>
                <a href="security_keys" class="btn-smm btn-smm-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
