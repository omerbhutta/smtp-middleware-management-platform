<div class="hero-section animate-fade-up" style="min-height:auto;padding:28px 32px;">
    <div class="anim-wave" style="position:absolute;bottom:0;left:0;right:0;height:30px;z-index:0;opacity:0.3;">
        <svg viewBox="0 0 1440 40" preserveAspectRatio="none" style="width:200%;height:100%;">
            <defs>
                <linearGradient id="waveGrad2" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#3b82f6" stop-opacity="0"/>
                    <stop offset="50%" stop-color="#3b82f6" stop-opacity="0.4"/>
                    <stop offset="100%" stop-color="#06b6d4" stop-opacity="0"/>
                </linearGradient>
            </defs>
            <path class="anim-wave-fill" fill="url(#waveGrad2)" d="M0,20 C240,0 480,40 720,20 C960,0 1200,40 1440,20 L1440,40 L0,40 Z"/>
        </svg>
    </div>
    <div style="position:relative;z-index:1;">
        <div>
            <h1 class="hero-title" style="font-size:1.5rem;">
                <i class="fas fa-envelope-open-text" style="color:var(--blue-primary);margin-right:8px;"></i>
                Email Detail #<?= $log['id'] ?>
            </h1>
            <p class="hero-subtitle" style="font-size:0.85rem;">Sent from <strong><?= escape($log['department_name'] ?? 'N/A') ?></strong> &mdash; <?= date('M j, Y H:i:s', strtotime($log['created_at'])) ?></p>
        </div>
    </div>
    <?php
    $hasRecipients = !empty(trim($log['recipients'] ?? ''));
    $totalReq = $log['total_recipients'] ?? $log['recipient_count'];
    $delivered = $log['recipient_count'];
    $skippedCt = $totalReq - $delivered;
    $ccCount = $log['cc'] ? count(splitRecipients($log['cc'])) : 0;
    $bccCount = $log['bcc'] ? count(splitRecipients($log['bcc'])) : 0;
    ?>
    <div class="hero-stats" style="position:relative;z-index:1;margin-top:16px;">
        <div class="hero-stat">
            <div class="hero-stat-value"><span class="badge-smm badge-smm-<?= $log['status'] === 'sent' ? 'success' : 'danger' ?>" style="font-size:0.75rem;padding:4px 12px;"><?= $log['status'] === 'sent' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-times-circle"></i>' ?> <?= ucfirst($log['status']) ?></span></div>
            <div class="hero-stat-label">Status</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-value" style="color:var(--text-primary);font-size:1.1rem;"><?= $totalReq ?> total</div>
            <div class="hero-stat-label">Recipients</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-value" style="color:var(--emerald);font-size:1.1rem;"><?= $delivered ?> delivered</div>
            <div class="hero-stat-label">Successfully Sent</div>
        </div>
        <?php if ($skippedCt > 0): ?>
        <div class="hero-stat">
            <div class="hero-stat-value" style="color:var(--red);font-size:1.1rem;"><?= $skippedCt ?> skipped</div>
            <div class="hero-stat-label">Skipped</div>
        </div>
        <?php endif; ?>
        <?php if ($ccCount > 0): ?>
        <div class="hero-stat">
            <div class="hero-stat-value" style="color:var(--blue-primary);font-size:1.1rem;"><?= $ccCount ?></div>
            <div class="hero-stat-label">CC</div>
        </div>
        <?php endif; ?>
        <?php if ($bccCount > 0): ?>
        <div class="hero-stat">
            <div class="hero-stat-value" style="color:var(--purple);font-size:1.1rem;"><?= $bccCount ?></div>
            <div class="hero-stat-label">BCC</div>
        </div>
        <?php endif; ?>
        <div class="hero-stat">
            <div class="hero-stat-value" style="font-size:1.1rem;">
                <?php if (!empty($log['has_attachment'])): ?>
                    <span style="color:var(--blue-primary);"><i class="fas fa-paperclip"></i> <?= (int)($log['attachment_count'] ?? 1) ?> file<?= ($log['attachment_count'] ?? 1) != 1 ? 's' : '' ?></span>
                <?php else: ?>
                    <span style="color:var(--text-muted);">No</span>
                <?php endif; ?>
            </div>
            <div class="hero-stat-label">Attachments</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-value" style="font-size:1.1rem;">
                <?php if (isset($log['priority']) && $log['priority'] === 1): ?>
                    <span style="color:var(--red);">High</span>
                <?php elseif (isset($log['priority']) && $log['priority'] === 5): ?>
                    <span style="color:var(--text-muted);">Low</span>
                <?php else: ?>
                    <span style="color:var(--text-muted);">Normal</span>
                <?php endif; ?>
            </div>
            <div class="hero-stat-label">Priority</div>
        </div>
        <?php if (!empty($log['reply_to'])): ?>
        <div class="hero-stat">
            <div class="hero-stat-value" style="color:var(--cyan);font-size:1.1rem;"><?= escape($log['reply_to']) ?></div>
            <div class="hero-stat-label">Reply-To</div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="row g-3" style="margin-top:4px;">
    <div class="col-md-6">
        <div class="card-smm animate-fade-up" style="height:100%;">
            <div class="card-smm-header">
                <h3 style="font-size:0.95rem;"><i class="fas fa-envelope" style="color:var(--blue-primary);margin-right:6px;"></i> Recipients</h3>
            </div>
            <div class="card-smm-body">
                <?php if ($hasRecipients): ?>
                <div style="display:flex;flex-direction:column;gap:6px;">
                    <?php
                    $recipientEmails = splitRecipients($log['recipients']);
                    $parsed = parseRecipientsWithStatus($log['recipients'] ?? '', $log['error_message'] ?? '');
                    $statusMap = [];
                    foreach ($parsed as $p) {
                        $statusMap[strtolower($p['email'])] = $p['status'];
                    }
                    foreach ($recipientEmails as $email):
                        $status = $statusMap[strtolower($email)] ?? 'sent';
                        $isSkipped = $status !== 'sent';
                    ?>
                    <div style="display:flex;align-items:center;gap:10px;padding:6px 10px;background:var(--bg-subtle);border-radius:6px;border-left:3px solid <?= $isSkipped ? 'var(--red)' : 'var(--emerald)' ?>;">
                        <i class="fas fa-user" style="font-size:0.7rem;color:var(--text-muted);flex-shrink:0;"></i>
                        <span style="color:<?= $isSkipped ? 'var(--red)' : 'var(--text-primary)' ?>;font-size:0.82rem;<?= $isSkipped ? 'text-decoration:line-through;' : '' ?>"><?= escape($email) ?></span>
                        <?php if ($isSkipped): ?>
                        <span class="badge-smm badge-smm-danger" style="font-size:0.6rem;margin-left:auto;flex-shrink:0;"><?= escape($status) ?></span>
                        <?php else: ?>
                        <span class="badge-smm badge-smm-success" style="font-size:0.6rem;margin-left:auto;flex-shrink:0;">sent</span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div style="text-align:center;padding:20px;color:var(--text-muted);font-size:0.85rem;">No recipients</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-smm animate-fade-up" style="height:100%;">
            <div class="card-smm-header">
                <h3 style="font-size:0.95rem;"><i class="fas fa-info-circle" style="color:var(--cyan);margin-right:6px;"></i> Details</h3>
            </div>
            <div class="card-smm-body">
                <div class="tile-stat mb-2"><span class="tile-stat-label">Subject</span><span class="tile-stat-value"><?= escape($log['subject'] ?? 'N/A') ?></span></div>
                <div class="tile-stat mb-2"><span class="tile-stat-label">Department</span><span class="tile-stat-value"><?= escape($log['department_name'] ?? 'N/A') ?></span></div>
                <div class="tile-stat mb-2"><span class="tile-stat-label">Sender Email</span><span class="tile-stat-value"><?= escape($log['sender_email'] ?? 'N/A') ?></span></div>
                <div class="tile-stat mb-2"><span class="tile-stat-label">Source IP</span><span class="tile-stat-value"><code style="background:rgba(0,0,0,0.3);padding:2px 8px;border-radius:4px;font-size:0.78rem;"><?= escape($log['source_ip'] ?? 'N/A') ?></code></span></div>
                <div class="tile-stat mb-2"><span class="tile-stat-label">API Key</span><span class="tile-stat-value"><code style="background:rgba(59,130,246,0.15);color:var(--blue-primary);padding:2px 8px;border-radius:4px;font-size:0.78rem;"><?php if ($log['api_key']): ?><?= escape(substr($log['api_key'], 0, 4)) ?>...<?= escape(substr($log['api_key'], -4)) ?><?php else: ?>-<?php endif; ?></code></span></div>
                <div class="tile-stat mb-2"><span class="tile-stat-label">Priority</span><span class="tile-stat-value"><?php if (isset($log['priority']) && $log['priority'] === 1): ?><span style="color:var(--red);">High</span><?php elseif (isset($log['priority']) && $log['priority'] === 5): ?><span style="color:var(--text-muted);">Low</span><?php else: ?><span style="color:var(--text-muted);">Normal</span><?php endif; ?></span></div>
                <?php if (!empty($log['reply_to'])): ?>
                <div class="tile-stat mb-2"><span class="tile-stat-label">Reply-To</span><span class="tile-stat-value" style="color:var(--cyan);"><?= escape($log['reply_to']) ?></span></div>
                <?php endif; ?>
                <div class="tile-stat"><span class="tile-stat-label">Created</span><span class="tile-stat-value"><?= date('M j, Y H:i:s', strtotime($log['created_at'])) ?></span></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3" style="margin-top:8px;">
    <?php if (!empty(trim($log['cc'] ?? ''))): ?>
    <div class="col-md-6">
        <div class="card-smm animate-fade-up">
            <div class="card-smm-header">
                <h3 style="font-size:0.95rem;"><i class="fas fa-cc" style="color:var(--blue-primary);margin-right:6px;"></i> CC <span class="badge-smm badge-smm-neutral" style="font-size:0.6rem;"><?= $ccCount ?></span></h3>
            </div>
            <div class="card-smm-body">
                <div style="display:flex;flex-direction:column;gap:6px;">
                    <?php foreach (splitRecipients($log['cc']) as $email): ?>
                    <div style="display:flex;align-items:center;gap:10px;padding:6px 10px;background:var(--bg-subtle);border-radius:6px;border-left:3px solid var(--blue-primary);">
                        <i class="fas fa-user" style="font-size:0.7rem;color:var(--text-muted);flex-shrink:0;"></i>
                        <span style="color:var(--text-primary);font-size:0.82rem;"><?= escape($email) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty(trim($log['bcc'] ?? ''))): ?>
    <div class="col-md-6">
        <div class="card-smm animate-fade-up">
            <div class="card-smm-header">
                <h3 style="font-size:0.95rem;"><i class="fas fa-eye-slash" style="color:var(--purple);margin-right:6px;"></i> BCC <span class="badge-smm badge-smm-neutral" style="font-size:0.6rem;"><?= $bccCount ?></span></h3>
            </div>
            <div class="card-smm-body">
                <div style="display:flex;flex-direction:column;gap:6px;">
                    <?php foreach (splitRecipients($log['bcc']) as $email): ?>
                    <div style="display:flex;align-items:center;gap:10px;padding:6px 10px;background:var(--bg-subtle);border-radius:6px;border-left:3px solid var(--purple);">
                        <i class="fas fa-user" style="font-size:0.7rem;color:var(--text-muted);flex-shrink:0;"></i>
                        <span style="color:var(--text-primary);font-size:0.82rem;"><?= escape($email) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($log['error_message']): ?>
    <div class="col-12">
        <div class="card-smm animate-fade-up" style="border-color:rgba(239,68,68,0.3);">
            <div class="card-smm-header" style="border-bottom-color:rgba(239,68,68,0.15);">
                <h3 style="font-size:0.95rem;color:var(--red);"><i class="fas fa-exclamation-circle" style="margin-right:6px;"></i> Error</h3>
            </div>
            <div class="card-smm-body">
                <div style="color:var(--red);font-size:0.85rem;"><?= escape($log['error_message']) ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div style="margin-top:16px;text-align:center;">
    <a href="email_logs" class="btn-smm btn-smm-secondary"><i class="fas fa-arrow-left"></i> Back to Email Activity</a>
</div>
