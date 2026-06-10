<div class="card-smm animate-fade-up">
    <div class="card-smm-header">
        <h3><i class="fas fa-<?= $edit_mode ? 'edit' : 'plus-circle' ?> me-2" style="color:var(--blue-primary);"></i> <?= $edit_mode ? 'Edit' : 'Create' ?> Department</h3>
    </div>
    <div class="card-smm-body">
        <form method="POST" action="departments/<?= $edit_mode ? 'edit?id=' . $dept['id'] : 'create' ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label-smm">Name *</label>
                    <input type="text" name="name" class="form-control-smm" value="<?= escape($dept['name'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-smm">Status</label>
                    <select name="status" class="form-control-smm">
                        <option value="active" <?= ($dept['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($dept['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label-smm">Description</label>
                    <textarea name="description" class="form-control-smm" rows="3"><?= escape($dept['description'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn-smm btn-smm-primary"><?= $edit_mode ? 'Update' : 'Create' ?> Department</button>
                <a href="departments" class="btn-smm btn-smm-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
