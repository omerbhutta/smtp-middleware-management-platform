<div class="d-flex justify-content-between align-items-center mb-3 animate-fade-up">
    <h4 style="margin:0;"><i class="fas fa-server me-2" style="color:var(--cyan);"></i> SMTP Accounts</h4>
    <a href="smtp_accounts/create" class="btn-smm btn-smm-primary btn-smm-sm"><i class="fas fa-plus"></i> Add SMTP</a>
</div>

<div class="tile-grid animate-fade-up">
    <?php foreach ($accounts as $acc): ?>
    <div class="tile-card">
        <div class="tile-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="smtp-provider-icon <?= $acc['provider_type'] ?>">
                    <i class="fas fa-<?= $acc['provider_type'] === 'microsoft365' ? 'microsoft' : ($acc['provider_type'] === 'gmail' ? 'google' : 'envelope') ?>"></i>
                </div>
                <div>
                    <h5 class="tile-card-title" style="font-size:0.9rem;"><?= escape($acc['sender_email']) ?></h5>
                    <p class="tile-card-subtitle"><?= escape($acc['smtp_host']) ?>:<?= $acc['smtp_port'] ?></p>
                </div>
            </div>
            <div class="d-flex flex-column align-items-end gap-1">
                <span class="badge-smm badge-smm-<?= $acc['status'] === 'active' ? 'success' : 'danger' ?>"><?= $acc['status'] ?></span>
                <?php if ($acc['is_portal_smtp']): ?>
                    <span class="badge-smm badge-smm-warning" style="font-size:0.7rem;"><i class="fas fa-check"></i> Portal</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="tile-card-body">
            <div class="tile-stat">
                <span class="tile-stat-label">Provider</span>
                <span class="tile-stat-value" style="text-transform:capitalize;"><?= escape($acc['provider_type']) ?></span>
            </div>
            <div class="tile-stat">
                <span class="tile-stat-label">Encryption</span>
                <span class="tile-stat-value"><?= strtoupper(escape($acc['encryption'])) ?></span>
            </div>
            <div class="tile-stat">
                <span class="tile-stat-label">Department</span>
                <span class="tile-stat-value"><?= escape($acc['department_name'] ?? '<em>Shared</em>') ?></span>
            </div>
        </div>
        <div class="tile-card-actions">
            <a href="smtp_accounts/edit?id=<?= $acc['id'] ?>" class="btn-smm btn-smm-secondary btn-smm-xs"><i class="fas fa-edit"></i> Edit</a>
            <a href="smtp_accounts/delete?id=<?= $acc['id'] ?>" class="btn-smm btn-smm-danger btn-smm-xs" onclick="return confirm('Delete this SMTP account?')"><i class="fas fa-trash"></i> Delete</a>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($accounts)): ?>
    <div class="tile-card">
        <div class="empty-state">
            <i class="fas fa-server"></i>
            <h4>No SMTP Accounts</h4>
            <p>Add your first SMTP provider to start sending emails.</p>
            <a href="smtp_accounts/create" class="btn-smm btn-smm-primary btn-smm-sm">Add SMTP Account</a>
        </div>
    </div>
    <?php endif; ?>
</div>
