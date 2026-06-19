<div class="card-smm animate-fade-up">
    <div class="card-smm-header">
        <h3><i class="fas fa-server me-2" style="color:var(--cyan);"></i> SMTP Accounts</h3>
        <a href="smtp_accounts/create" class="btn-smm btn-smm-primary btn-smm-sm"><i class="fas fa-plus"></i> Add SMTP</a>
    </div>
    <div class="card-smm-body">
        <form method="GET" action="index.php" class="filter-bar mb-3">
            <input type="hidden" name="route" value="smtp_accounts">
            <input type="text" name="search" class="form-control-smm" placeholder="Search SMTP accounts..." value="<?= escape($_GET['search'] ?? '') ?>" style="min-width:200px;">
            <button type="submit" class="btn-smm btn-smm-primary btn-smm-sm"><i class="fas fa-search"></i></button>
            <?php if (!empty($_GET['search']) || !empty($_GET['sort'])): ?>
            <a href="?route=smtp_accounts" class="btn-smm btn-smm-secondary btn-smm-sm"><i class="fas fa-times"></i></a>
            <?php endif; ?>
        </form>
        <div style="overflow-x:auto;">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th><a href="<?= sortUrl('sender_email', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Sender Email <?= sortIcon('sender_email', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th><a href="<?= sortUrl('smtp_host', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Host <?= sortIcon('smtp_host', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th><a href="<?= sortUrl('smtp_port', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Port <?= sortIcon('smtp_port', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th><a href="<?= sortUrl('provider_type', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Provider <?= sortIcon('provider_type', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th><a href="<?= sortUrl('encryption', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Encryption <?= sortIcon('encryption', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th><a href="<?= sortUrl('department_name', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Department <?= sortIcon('department_name', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th><a href="<?= sortUrl('status', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Status <?= sortIcon('status', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th>Portal</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($accounts as $acc): ?>
                    <tr>
                        <td><strong><?= escape($acc['sender_email']) ?></strong></td>
                        <td><span style="color:var(--text-muted);font-size:0.82rem;"><?= escape($acc['smtp_host']) ?></span></td>
                        <td><span style="color:var(--text-muted);font-size:0.82rem;"><?= $acc['smtp_port'] ?></span></td>
                        <td><span class="badge-smm badge-smm-info" style="text-transform:capitalize;"><?= escape($acc['provider_type']) ?></span></td>
                        <td><span style="color:var(--text-muted);font-size:0.82rem;"><?= strtoupper(escape($acc['encryption'])) ?></span></td>
                        <td><span style="color:var(--text-muted);font-size:0.82rem;"><?= escape($acc['department_name'] ?? '<em>Shared</em>') ?></span></td>
                        <td>
                            <span class="badge-smm badge-smm-<?= $acc['status'] === 'active' ? 'success' : 'danger' ?>"><?= $acc['status'] ?></span>
                        </td>
                        <td>
                            <?php if ($acc['is_portal_smtp']): ?>
                            <span class="badge-smm badge-smm-warning" style="font-size:0.7rem;"><i class="fas fa-check"></i> Portal</span>
                            <?php else: ?>
                            <span style="color:var(--text-muted);font-size:0.75rem;">-</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:right;">
                            <a href="smtp_accounts/edit?id=<?= $acc['id'] ?>" class="btn-smm btn-smm-secondary btn-smm-xs"><i class="fas fa-edit"></i></a>
                            <a href="smtp_accounts/delete?id=<?= $acc['id'] ?>" class="btn-smm btn-smm-danger btn-smm-xs" onclick="return confirm('Delete this SMTP account?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($accounts)): ?>
                    <tr><td colspan="9"><div class="empty-state"><i class="fas fa-server"></i><h4>No SMTP Accounts</h4><p>Add your first SMTP provider to start sending emails.</p><a href="smtp_accounts/create" class="btn-smm btn-smm-primary btn-smm-sm">Add SMTP Account</a></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>