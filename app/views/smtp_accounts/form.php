<div class="card-smm animate-fade-up">
    <div class="card-smm-header">
        <h3><i class="fas fa-<?= $edit_mode ? 'edit' : 'plus-circle' ?> me-2" style="color:var(--cyan);"></i> <?= $edit_mode ? 'Edit' : 'Create' ?> SMTP Account</h3>
    </div>
    <div class="card-smm-body">
        <form method="POST" action="smtp_accounts/<?= $edit_mode ? 'edit?id=' . $acc['id'] : 'create' ?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label-smm">Provider Type *</label>
                    <select name="provider_type" class="form-control-smm" id="providerType" required>
                        <option value="microsoft365" <?= ($acc['provider_type'] ?? '') === 'microsoft365' ? 'selected' : '' ?>>Microsoft 365</option>
                        <option value="gmail" <?= ($acc['provider_type'] ?? '') === 'gmail' ? 'selected' : '' ?>>Gmail</option>
                        <option value="custom" <?= ($acc['provider_type'] ?? '') === 'custom' ? 'selected' : '' ?>>Custom SMTP</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label-smm">Department <span style="color:var(--text-muted);font-weight:400;">(optional)</span></label>
                    <select name="department_id" class="form-control-smm">
                        <option value="">All Departments (Shared)</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>" <?= (($acc['department_id'] ?? '') == $dept['id']) ? 'selected' : '' ?>><?= escape($dept['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="is_portal_smtp" value="1" class="form-check-input" id="portalSmtp" style="border-color:var(--border-color);" <?= ($acc['is_portal_smtp'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="portalSmtp" style="font-size:0.85rem;color:var(--text-secondary);">Use as Portal SMTP (MFA & system emails)</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label-smm">SMTP Host *</label>
                    <input type="text" name="smtp_host" class="form-control-smm" value="<?= escape($acc['smtp_host'] ?? '') ?>" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label-smm">Port *</label>
                    <input type="number" name="smtp_port" class="form-control-smm" value="<?= $acc['smtp_port'] ?? '587' ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label-smm">Encryption</label>
                    <select name="encryption" class="form-control-smm">
                        <option value="tls" <?= ($acc['encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>STARTTLS (587)</option>
                        <option value="ssl" <?= ($acc['encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL (465)</option>
                        <option value="none" <?= ($acc['encryption'] ?? '') === 'none' ? 'selected' : '' ?>>None (25)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label-smm">Status</label>
                    <select name="status" class="form-control-smm">
                        <option value="active" <?= ($acc['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($acc['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label-smm">SMTP Username *</label>
                    <input type="text" name="smtp_username" class="form-control-smm" value="<?= escape($acc['smtp_username'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-smm">SMTP Password <?= $edit_mode ? '<span style="color:var(--text-muted);font-weight:400;">(leave blank to keep)</span>' : '' ?></label>
                    <input type="password" name="smtp_password" class="form-control-smm" <?= $edit_mode ? '' : 'required' ?>>
                </div>
                <div class="col-md-6">
                    <label class="form-label-smm">Sender Email *</label>
                    <input type="email" name="sender_email" class="form-control-smm" value="<?= escape($acc['sender_email'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-smm">Sender Name <span style="color:var(--text-muted);font-weight:400;">(optional)</span></label>
                    <input type="text" name="sender_name" class="form-control-smm" value="<?= escape($acc['sender_name'] ?? '') ?>">
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn-smm btn-smm-primary"><?= $edit_mode ? 'Update' : 'Create' ?> SMTP Account</button>
                <a href="smtp_accounts" class="btn-smm btn-smm-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php if ($edit_mode): ?>
<div class="card-smm animate-fade-up mt-4">
    <div class="card-smm-header">
        <h3><i class="fas fa-paper-plane me-2" style="color:var(--blue-primary);"></i> Test SMTP Connection</h3>
    </div>
    <div class="card-smm-body">
        <p style="font-size:0.82rem;color:var(--text-secondary);margin-bottom:14px;">
            Send a test email to verify the SMTP credentials are working.
        </p>
        <form id="testSmtpForm">
            <input type="hidden" name="id" value="<?= $acc['id'] ?>">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label-smm">Recipient Email *</label>
                    <input type="email" name="recipient" class="form-control-smm" placeholder="you@example.com" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn-smm btn-smm-primary" id="testSmtpBtn">
                        <i class="fas fa-paper-plane"></i> Send Test
                    </button>
                </div>
                <div class="col-md-3">
                    <span id="testSmtpResult" style="font-size:0.82rem;"></span>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('testSmtpForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = document.getElementById('testSmtpBtn');
    var result = document.getElementById('testSmtpResult');
    var formData = new FormData(this);
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    result.innerHTML = '';
    fetch('smtp_accounts/test_smtp?id=<?= $acc['id'] ?>', {
        method: 'POST',
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.status) {
            result.innerHTML = '<span style="color:var(--emerald);"><i class="fas fa-check-circle"></i> ' + data.message + '</span>';
        } else {
            result.innerHTML = '<span style="color:var(--rose);"><i class="fas fa-exclamation-circle"></i> ' + data.message + '</span>';
        }
    })
    .catch(function() {
        result.innerHTML = '<span style="color:var(--rose);"><i class="fas fa-exclamation-circle"></i> Request failed</span>';
    })
    .finally(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Test';
    });
});
</script>
<?php endif; ?>

<script>
document.getElementById('providerType').addEventListener('change', function() {
    var host = document.querySelector('input[name="smtp_host"]');
    var port = document.querySelector('input[name="smtp_port"]');
    var enc = document.querySelector('select[name="encryption"]');
    if (this.value === 'microsoft365') { host.value = 'smtp.office365.com'; port.value = '587'; enc.value = 'tls'; }
    else if (this.value === 'gmail') { host.value = 'smtp.gmail.com'; port.value = '587'; enc.value = 'tls'; }
});
</script>
