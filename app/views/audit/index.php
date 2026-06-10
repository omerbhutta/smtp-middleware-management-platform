<div class="card-smm animate-fade-up">
    <div class="card-smm-header">
        <h3><i class="fas fa-clipboard-list me-2" style="color:var(--purple);"></i> Audit Logs</h3>
    </div>
    <div class="card-smm-body p-0">
        <div style="overflow-x:auto;">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>IP Address</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($audits['data'] as $audit): ?>
                    <tr>
                        <td><strong><?= escape($audit['username'] ?? 'System') ?></strong></td>
                        <td><span class="badge-smm badge-smm-info"><?= escape($audit['action']) ?></span></td>
                        <td><span style="font-size:0.85rem;color:var(--text-secondary);"><?= escape(truncate($audit['details'], 60)) ?></span></td>
                        <td><code style="background:rgba(0,0,0,0.3);"><?= escape($audit['ip_address']) ?></code></td>
                        <td><span style="font-size:0.82rem;color:var(--text-muted);"><?= date('M j, Y H:i', strtotime($audit['created_at'])) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($audits['data'])): ?>
                    <tr><td colspan="5"><div class="empty-state"><i class="fas fa-clipboard-list"></i><h4>No Audit Logs</h4><p>Administrative actions will be recorded here.</p></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($audits['total_pages'] > 1): ?>
    <div class="card-smm-footer">
        <ul class="pagination-smm mb-0">
            <li class="page-item <?= $audits['page'] <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="?route=audit&page=<?= $audits['page'] - 1 ?>"><i class="fas fa-chevron-left"></i></a></li>
            <?php for ($i = 1; $i <= $audits['total_pages']; $i++): ?>
                <li class="page-item <?= $i == $audits['page'] ? 'active' : '' ?>"><a class="page-link" href="?route=audit&page=<?= $i ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <li class="page-item <?= $audits['page'] >= $audits['total_pages'] ? 'disabled' : '' ?>"><a class="page-link" href="?route=audit&page=<?= $audits['page'] + 1 ?>"><i class="fas fa-chevron-right"></i></a></li>
        </ul>
    </div>
    <?php endif; ?>
</div>
