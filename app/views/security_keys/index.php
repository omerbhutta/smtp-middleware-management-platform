<div class="d-flex justify-content-between align-items-center mb-3 animate-fade-up">
    <h4 style="margin:0;"><i class="fas fa-key me-2" style="color:var(--amber);"></i> Security Keys</h4>
    <a href="security_keys/create" class="btn-smm btn-smm-primary btn-smm-sm"><i class="fas fa-plus"></i> Generate Key</a>
</div>

<div class="card-smm animate-fade-up">
    <div class="card-smm-body p-0">
        <div style="overflow-x:auto;">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>API Key</th>
                        <th>Status</th>
                        <th>Usage</th>
                        <th>Last Used</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($keys as $key): ?>
                    <tr>
                        <td><strong><?= escape($key['department_name'] ?? 'N/A') ?></strong></td>
                        <td>
                            <div class="key-display" style="max-width:260px;">
                                <span class="key-value"><?= escape(substr($key['api_key'], 0, 20)) ?>...<?= escape(substr($key['api_key'], -4)) ?></span>
                                <button class="btn-smm btn-smm-secondary btn-smm-xs" data-copy="<?= escape($key['api_key']) ?>" title="Copy"><i class="fas fa-copy"></i></button>
                            </div>
                        </td>
                        <td>
                            <span class="badge-smm badge-smm-<?= $key['status'] === 'active' ? 'success' : 'danger' ?>">
                                <?= $key['status'] ?>
                            </span>
                        </td>
                        <td><span class="badge-smm badge-smm-info"><?= $key['usage_count'] ?></span></td>
                        <td><span style="color:var(--text-muted);font-size:0.82rem;"><?= timeAgo($key['last_usage']) ?></span></td>
                        <td style="text-align:right;">
                            <a href="security_keys/edit?id=<?= $key['id'] ?>" class="btn-smm btn-smm-secondary btn-smm-xs"><i class="fas fa-edit"></i></a>
                            <a href="security_keys/delete?id=<?= $key['id'] ?>" class="btn-smm btn-smm-danger btn-smm-xs" onclick="return confirm('Delete this key?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($keys)): ?>
                    <tr><td colspan="6"><div class="empty-state"><i class="fas fa-key"></i><h4>No Security Keys</h4><p>Generate your first API key to enable portal integrations.</p></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
