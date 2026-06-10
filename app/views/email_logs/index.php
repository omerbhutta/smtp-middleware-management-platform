<div class="card-smm animate-fade-up">
    <div class="card-smm-header">
        <h3><i class="fas fa-envelope-open-text me-2" style="color:var(--blue-primary);"></i> Email Activity</h3>
    </div>
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
                        <th>Recipients</th>
                        <th>Subject</th>
                        <th>Department</th>
                        <th>Sender</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs['data'] as $log): ?>
                    <tr>
                        <td><strong><?= escape(truncate($log['recipients'] ?? 'N/A', 30)) ?></strong></td>
                        <td><span style="color:var(--text-muted);font-size:0.85rem;"><?= escape(truncate($log['subject'] ?? 'N/A', 30)) ?></span></td>
                        <td><?= escape($log['department_name'] ?? '-') ?></td>
                        <td><span style="font-size:0.82rem;"><?= escape($log['sender_email'] ?? '-') ?></span></td>
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
                    <tr><td colspan="7"><div class="empty-state"><i class="fas fa-inbox"></i><h4>No Email Activity</h4><p>Logs will appear once emails are sent through the platform.</p></div></td></tr>
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
