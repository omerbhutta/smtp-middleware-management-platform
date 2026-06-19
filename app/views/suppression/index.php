<div class="card-smm animate-fade-up">
    <div class="card-smm-header">
        <h3><i class="fas fa-ban me-2" style="color:var(--red);"></i> Suppression Logs</h3>
        <form method="POST" action="suppression/add" class="d-flex gap-2">
            <input type="email" name="email" class="form-control-smm" placeholder="Add email to suppression" required style="min-width:200px;">
            <input type="text" name="reason" class="form-control-smm" placeholder="Reason" style="min-width:150px;">
            <button type="submit" class="btn-smm btn-smm-danger btn-smm-sm"><i class="fas fa-plus"></i> Block</button>
        </form>
    </div>
    <div class="card-smm-body">
        <form method="GET" action="index.php" class="filter-bar mb-3">
            <input type="hidden" name="route" value="suppression">
            <input type="text" name="search" class="form-control-smm" placeholder="Search suppressed emails..." value="<?= escape($_GET['search'] ?? '') ?>" style="min-width:200px;">
            <button type="submit" class="btn-smm btn-smm-primary btn-smm-sm"><i class="fas fa-search"></i></button>
            <?php if (!empty($_GET['search']) || !empty($_GET['sort'])): ?>
            <a href="?route=suppression" class="btn-smm btn-smm-secondary btn-smm-sm"><i class="fas fa-times"></i></a>
            <?php endif; ?>
        </form>
        <div style="overflow-x:auto;">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th><a href="<?= sortUrl('email', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Email <?= sortIcon('email', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th><a href="<?= sortUrl('reason', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Reason <?= sortIcon('reason', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th><a href="<?= sortUrl('source', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Source <?= sortIcon('source', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th><a href="<?= sortUrl('created_at', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Added <?= sortIcon('created_at', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                        <th style="text-align:right;">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($suppressions['data'] as $s): ?>
                    <tr>
                        <td><strong><?= escape($s['email']) ?></strong></td>
                        <td><span style="color:var(--text-muted);font-size:0.85rem;"><?= escape($s['reason'] ?? '-') ?></span></td>
                        <td><span class="badge-smm badge-smm-neutral"><?= escape($s['source']) ?></span></td>
                        <td><span style="color:var(--text-muted);font-size:0.82rem;"><?= date('M j, Y', strtotime($s['created_at'])) ?></span></td>
                        <td style="text-align:right;">
                            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                            <a href="suppression/remove&email=<?= urlencode($s['email']) ?>" class="btn-smm btn-smm-success btn-smm-xs" onclick="return confirm('Remove from suppression?')"><i class="fas fa-check"></i> Remove</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($suppressions['data'])): ?>
                    <tr><td colspan="<?= (($_SESSION['role'] ?? '') === 'admin') ? 5 : 4 ?>"><div class="empty-state"><i class="fas fa-shield"></i><h4>No Suppressed Emails</h4><p>All clear. No email addresses are currently blocked.</p></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($suppressions['total_pages'] > 1): ?>
    <div class="card-smm-footer">
        <ul class="pagination-smm mb-0">
            <li class="page-item <?= $suppressions['page'] <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="?route=suppression&page=<?= $suppressions['page'] - 1 ?>&sort=<?= urlencode($_GET['sort'] ?? '') ?>&order=<?= urlencode($_GET['order'] ?? '') ?>&search=<?= urlencode($_GET['search'] ?? '') ?>"><i class="fas fa-chevron-left"></i></a></li>
            <?php for ($i = 1; $i <= $suppressions['total_pages']; $i++): ?>
                <li class="page-item <?= $i == $suppressions['page'] ? 'active' : '' ?>"><a class="page-link" href="?route=suppression&page=<?= $i ?>&sort=<?= urlencode($_GET['sort'] ?? '') ?>&order=<?= urlencode($_GET['order'] ?? '') ?>&search=<?= urlencode($_GET['search'] ?? '') ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <li class="page-item <?= $suppressions['page'] >= $suppressions['total_pages'] ? 'disabled' : '' ?>"><a class="page-link" href="?route=suppression&page=<?= $suppressions['page'] + 1 ?>&sort=<?= urlencode($_GET['sort'] ?? '') ?>&order=<?= urlencode($_GET['order'] ?? '') ?>&search=<?= urlencode($_GET['search'] ?? '') ?>"><i class="fas fa-chevron-right"></i></a></li>
        </ul>
    </div>
    <?php endif; ?>
</div>