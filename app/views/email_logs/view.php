<div class="card-smm animate-fade-up">
    <div class="card-smm-header">
        <h3><i class="fas fa-envelope-open-text me-2" style="color:var(--blue-primary);"></i> Email Detail #<?= $log['id'] ?></h3>
        <a href="email_logs" class="btn-smm btn-smm-secondary btn-smm-sm"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-smm-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:var(--radius-sm);padding:16px;">
                    <div class="tile-stat mb-2"><span class="tile-stat-label">Recipients</span><span class="tile-stat-value"><?= escape($log['recipients'] ?? 'N/A') ?></span></div>
                    <div class="tile-stat mb-2"><span class="tile-stat-label">Subject</span><span class="tile-stat-value"><?= escape($log['subject'] ?? 'N/A') ?></span></div>
                    <div class="tile-stat mb-2"><span class="tile-stat-label">Department</span><span class="tile-stat-value"><?= escape($log['department_name'] ?? 'N/A') ?></span></div>
                    <div class="tile-stat mb-2"><span class="tile-stat-label">Sender Email</span><span class="tile-stat-value"><?= escape($log['sender_email'] ?? 'N/A') ?></span></div>
                </div>
            </div>
            <div class="col-md-6">
                <div style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:var(--radius-sm);padding:16px;">
                    <div class="tile-stat mb-2"><span class="tile-stat-label">Recipient Count</span><span class="tile-stat-value"><?= $log['recipient_count'] ?></span></div>
                    <div class="tile-stat mb-2"><span class="tile-stat-label">Source IP</span><span class="tile-stat-value"><code style="background:rgba(0,0,0,0.3);"><?= escape($log['source_ip'] ?? 'N/A') ?></code></span></div>
                    <div class="tile-stat mb-2"><span class="tile-stat-label">Status</span><span class="tile-stat-value"><span class="badge-smm badge-smm-<?= $log['status'] === 'sent' ? 'success' : 'danger' ?>"><?= $log['status'] ?></span></span></div>
                    <div class="tile-stat mb-2"><span class="tile-stat-label">Created</span><span class="tile-stat-value"><?= date('M j, Y H:i:s', strtotime($log['created_at'])) ?></span></div>
                </div>
            </div>
            <?php if ($log['error_message']): ?>
            <div class="col-12">
                <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);border-radius:var(--radius-sm);padding:16px;">
                    <div class="tile-stat"><span class="tile-stat-label" style="color:var(--red);"><i class="fas fa-exclamation-circle"></i> Error</span><span class="tile-stat-value" style="color:var(--red);"><?= escape($log['error_message']) ?></span></div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
