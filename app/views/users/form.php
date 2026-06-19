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

<?php if ($edit_mode): ?>
<div class="card-smm animate-fade-up mt-4">
    <div class="card-smm-header">
        <h3><i class="fab fa-microsoft me-2" style="color:var(--blue-primary);"></i> Microsoft Login Configuration</h3>
    </div>
    <div class="card-smm-body">
        <?php if (!empty($user['ms_id'])): ?>
        <div class="alert-smm" style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);color:var(--green);margin-bottom:16px;">
            <i class="fas fa-link"></i>
            <div>This account is linked to Microsoft ID: <code><?= escape($user['ms_id']) ?></code></div>
        </div>
        <?php endif; ?>

        <form method="POST" action="users/edit?id=<?= $user['id'] ?>">
            <input type="hidden" name="update_ms_settings" value="1">
            <p style="color:var(--text-muted);font-size:0.85rem;margin-bottom:16px;">
                Configure Microsoft OAuth 2.0 app credentials. Users will be able to sign in using their Microsoft work/school accounts.
            </p>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label-smm">Client ID <span style="color:var(--text-muted);font-weight:400;">(Application ID)</span></label>
                    <input type="text" name="ms_client_id" class="form-control-smm" value="<?= escape($_ENV['MS_CLIENT_ID'] ?? '') ?>" placeholder="e.g. 12345678-1234-1234-1234-123456789abc">
                </div>
                <div class="col-md-6">
                    <label class="form-label-smm">Client Secret</label>
                    <input type="password" name="ms_client_secret" class="form-control-smm" value="<?= escape($_ENV['MS_CLIENT_SECRET'] ?? '') ?>" placeholder="Enter a strong client secret">
                </div>
                <div class="col-md-6">
                    <label class="form-label-smm">Tenant ID</label>
                    <input type="text" name="ms_tenant_id" class="form-control-smm" value="<?= escape($_ENV['MS_TENANT_ID'] ?? 'common') ?>" placeholder="common or your tenant/directory ID">
                    <small style="color:var(--text-muted);font-size:0.7rem;">Use <code>common</code> for multi-tenant or your specific tenant ID.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label-smm">Redirect URI</label>
                    <input type="text" name="ms_redirect_uri" class="form-control-smm" value="<?= escape($_ENV['MS_REDIRECT_URI'] ?? (portalUrl() . 'auth/microsoft/callback')) ?>">
                    <small style="color:var(--text-muted);font-size:0.7rem;">Must match the redirect URI configured in your Azure app registration.</small>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn-smm btn-smm-primary"><i class="fas fa-save me-1"></i> Save MS Settings</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>