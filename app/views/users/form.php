<div class="card-smm animate-fade-up">
    <div class="card-smm-header">
        <h3><i class="fas fa-<?= $edit_mode ? 'edit' : 'plus-circle' ?> me-2" style="color:var(--blue-primary);"></i> <?= $edit_mode ? 'Edit' : 'Create' ?> User</h3>
    </div>
    <div class="card-smm-body">
        <form method="POST" action="users/<?= $edit_mode ? 'edit?id=' . $user['id'] : 'create' ?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label-smm">Full Name</label>
                    <input type="text" name="full_name" class="form-control-smm" value="<?= escape($user['full_name'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label-smm">Username</label>
                    <input type="text" name="username" class="form-control-smm" value="<?= escape($user['username'] ?? '') ?>" <?= $edit_mode ? 'readonly' : 'required' ?>>
                </div>
                <div class="col-md-4">
                    <label class="form-label-smm">Email</label>
                    <input type="email" name="email" class="form-control-smm" value="<?= escape($user['email'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-smm">Password <?= $edit_mode ? '<span style="color:var(--text-muted);font-weight:400;">(leave blank to keep)</span>' : '' ?></label>
                    <input type="password" name="password" class="form-control-smm" <?= $edit_mode ? '' : 'required' ?>>
                </div>
                <div class="col-md-3">
                    <label class="form-label-smm">Status</label>
                    <select name="status" class="form-control-smm">
                        <option value="active" <?= ($user['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($user['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label-smm">MFA</label>
                    <select name="mfa_enabled" class="form-control-smm">
                        <option value="1" <?= ($user['mfa_enabled'] ?? 1) ? 'selected' : '' ?>>Enabled</option>
                        <option value="0" <?= !($user['mfa_enabled'] ?? 1) ? 'selected' : '' ?>>Disabled</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn-smm btn-smm-primary"><?= $edit_mode ? 'Update' : 'Create' ?> User</button>
                <a href="users" class="btn-smm btn-smm-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
