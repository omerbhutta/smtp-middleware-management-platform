<div class="hero-section animate-fade-up" style="min-height:auto;padding:28px 32px;">
    <div class="anim-wave" style="position:absolute;bottom:0;left:0;right:0;height:30px;z-index:0;opacity:0.3;">
        <svg viewBox="0 0 1440 40" preserveAspectRatio="none" style="width:200%;height:100%;">
            <defs>
                <linearGradient id="waveGrad3" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#3b82f6" stop-opacity="0"/>
                    <stop offset="50%" stop-color="#3b82f6" stop-opacity="0.4"/>
                    <stop offset="100%" stop-color="#06b6d4" stop-opacity="0"/>
                </linearGradient>
            </defs>
            <path class="anim-wave-fill" fill="url(#waveGrad3)" d="M0,20 C240,0 480,40 720,20 C960,0 1200,40 1440,20 L1440,40 L0,40 Z"/>
        </svg>
    </div>
    <div style="position:relative;z-index:1;">
        <div>
            <h1 class="hero-title" style="font-size:1.5rem;">
                <i class="fas fa-envelope-open-text" style="color:var(--blue-primary);margin-right:8px;"></i>
                Email Activity
            </h1>
            <p class="hero-subtitle" style="font-size:0.85rem;">Track all email requests sent through the platform &mdash; <strong><?= $totalCount ?> total</strong></p>
        </div>
    </div>
    <?php
    $successRate = $totalCount > 0 ? round(($stats['sent'] / max($totalCount, 1)) * 100, 1) : 100;
    ?>
    <div class="hero-stats" style="position:relative;z-index:1;margin-top:16px;">
        <div class="hero-stat">
            <div class="hero-stat-value" style="color:var(--blue-primary);font-size:1.1rem;"><?= $totalCount ?></div>
            <div class="hero-stat-label">Total Requests</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-value" style="color:var(--emerald);font-size:1.1rem;"><?= $todayCount ?></div>
            <div class="hero-stat-label">Sent Today</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-value" style="color:var(--red);font-size:1.1rem;"><?= $failedCount ?></div>
            <div class="hero-stat-label">Failed</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-value" style="color:var(--amber);font-size:1.1rem;"><?= $stats['skipped'] ?></div>
            <div class="hero-stat-label">Skipped</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-value" style="font-size:1.1rem;"><?= $successRate ?>%</div>
            <div class="hero-stat-label">Success Rate</div>
        </div>
    </div>
</div>

<div class="card-smm animate-fade-up" style="margin-top:12px;">
    <div class="card-smm-body">
        <!-- Filters -->
        <form method="GET" action="index.php" class="filter-bar">
            <input type="hidden" name="route" value="email_logs">
            <input type="date" name="date_from" class="form-control-smm" value="<?= escape($_GET['date_from'] ?? '') ?>" style="min-width:140px;">
            <input type="date" name="date_to" class="form-control-smm" value="<?= escape($_GET['date_to'] ?? '') ?>" style="min-width:140px;">
            <select name="department_id" class="form-control-smm" style="min-width:150px;">
                <option value="">All Departments</option>
                <?php foreach ($departments as $dept): ?>
                    <option value="<?= $dept['id'] ?>" <?= (($_GET['department_id'] ?? '') == $dept['id']) ? 'selected' : '' ?>><?= escape($dept['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="status" class="form-control-smm" style="min-width:120px;">
                <option value="">All Status</option>
                <option value="sent" <?= ($_GET['status'] ?? '') === 'sent' ? 'selected' : '' ?>>Sent</option>
                <option value="failed" <?= ($_GET['status'] ?? '') === 'failed' ? 'selected' : '' ?>>Failed</option>
            </select>
            <input type="text" name="search" class="form-control-smm" placeholder="Search..." value="<?= escape($_GET['search'] ?? '') ?>" style="min-width:200px;">
            <button type="submit" class="btn-smm btn-smm-primary btn-smm-sm"><i class="fas fa-search"></i></button>
        </form>

        <div style="overflow-x:auto;">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th><a href="<?= sortUrl('recipients', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Recipients <?= sortIcon('recipients', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th><a href="<?= sortUrl('subject', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Subject <?= sortIcon('subject', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th>Priority</th>
                        <th><a href="<?= sortUrl('department_name', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Department <?= sortIcon('department_name', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th><a href="<?= sortUrl('sender_email', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Sender <?= sortIcon('sender_email', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th>API Key</th>
                        <th><a href="<?= sortUrl('status', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Status <?= sortIcon('status', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th><a href="<?= sortUrl('created_at', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Date <?= sortIcon('created_at', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs['data'] as $log): ?>
                    <tr>
                        <td>
                            <?php
                            $hasRecipients = !empty(trim($log['recipients'] ?? ''));
                            $totalReq = $log['total_recipients'] ?? $log['recipient_count'];
                            $delivered = $log['recipient_count'];
                            $skippedCt = $totalReq - $delivered;
                            ?>
                            <div style="font-size:0.82rem;color:var(--text-primary);font-weight:600;">
                                <?php if ($hasRecipients): ?>
                                    <?= $totalReq ?> recipient<?= $totalReq != 1 ? 's' : '' ?>
                                <?php else: ?>
                                    No recipients
                                <?php endif; ?>
                            </div>
                            <?php if ($hasRecipients): ?>
                            <div style="display:flex;gap:8px;margin-top:2px;">
                                <span style="font-size:0.7rem;color:var(--emerald);"><i class="fas fa-check-circle" style="font-size:0.6rem;"></i> <?= $delivered ?> delivered</span>
                                <?php if ($skippedCt > 0): ?>
                                <span style="font-size:0.7rem;color:var(--red);"><i class="fas fa-times-circle" style="font-size:0.6rem;"></i> <?= $skippedCt ?> skipped</span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <div style="margin-top:4px;"><?= renderRecipientsHtml($log['recipients'] ?? '', $log['error_message'] ?? '') ?></div>
                        </td>
                        <td><span style="color:var(--text-muted);font-size:0.85rem;"><?= escape(truncate($log['subject'] ?? 'N/A', 30)) ?></span></td>
                        <td>
                            <?php if (isset($log['priority']) && $log['priority'] === 1): ?>
                                <span class="badge-smm badge-smm-danger" style="font-size:0.6rem;">High</span>
                            <?php elseif (isset($log['priority']) && $log['priority'] === 5): ?>
                                <span class="badge-smm badge-smm-neutral" style="font-size:0.6rem;">Low</span>
                            <?php else: ?>
                                <span style="color:var(--text-muted);font-size:0.7rem;">Normal</span>
                            <?php endif; ?>
                            <?php if (!empty($log['has_attachment'])): ?>
                                <span style="margin-left:4px;color:var(--blue-primary);font-size:0.65rem;"><i class="fas fa-paperclip"></i></span>
                            <?php endif; ?>
                            <?php if (!empty($log['reply_to'])): ?>
                                <span style="margin-left:2px;color:var(--cyan);font-size:0.65rem;" title="Reply-To: <?= escape($log['reply_to']) ?>"><i class="fas fa-reply"></i></span>
                            <?php endif; ?>
                        </td>
                        <td><?= escape($log['department_name'] ?? '-') ?></td>
                        <td><span style="font-size:0.82rem;"><?= escape($log['sender_email'] ?? '-') ?></span></td>
                        <td><code style="background:rgba(59,130,246,0.15);color:var(--blue-primary);font-size:0.75rem;padding:2px 6px;border-radius:4px;"><?php if ($log['api_key']): ?><?= escape(substr($log['api_key'], 0, 4)) ?>...<?= escape(substr($log['api_key'], -4)) ?><?php else: ?>-<?php endif; ?></code></td>
                        <td>
                            <span class="badge-smm badge-smm-<?= $log['status'] === 'sent' ? 'success' : 'danger' ?>">
                                <?= $log['status'] === 'sent' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-times-circle"></i>' ?> <?= $log['status'] ?>
                            </span>
                        </td>
                        <td><span style="font-size:0.82rem;color:var(--text-muted);"><?= date('M j, H:i', strtotime($log['created_at'])) ?></span></td>
                        <td style="text-align:right;">
                            <a href="email_logs/view?id=<?= $log['id'] ?>" class="btn-smm btn-smm-secondary btn-smm-xs"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($logs['data'])): ?>
                    <tr><td colspan="9"><div class="empty-state"><i class="fas fa-inbox"></i><h4>No Email Activity</h4><p>Logs will appear once emails are sent through the platform.</p></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($logs['total_pages'] > 1): ?>
        <ul class="pagination-smm mt-3">
            <li class="page-item <?= $logs['page'] <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?route=email_logs&page=<?= $logs['page'] - 1 ?>&<?= http_build_query(array_diff_key($_GET, ['route' => '', 'page' => ''])) ?>"><i class="fas fa-chevron-left"></i></a>
            </li>
            <?php for ($i = 1; $i <= $logs['total_pages']; $i++): ?>
                <li class="page-item <?= $i == $logs['page'] ? 'active' : '' ?>">
                    <a class="page-link" href="?route=email_logs&page=<?= $i ?>&<?= http_build_query(array_diff_key($_GET, ['route' => '', 'page' => ''])) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= $logs['page'] >= $logs['total_pages'] ? 'disabled' : '' ?>">
                <a class="page-link" href="?route=email_logs&page=<?= $logs['page'] + 1 ?>&<?= http_build_query(array_diff_key($_GET, ['route' => '', 'page' => ''])) ?>"><i class="fas fa-chevron-right"></i></a>
            </li>
        </ul>
        <?php endif; ?>
    </div>
</div>
