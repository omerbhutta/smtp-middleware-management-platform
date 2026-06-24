<?php include VIEW_PATH . 'partials/hero_header.php'; ?>

<div class="card-smm animate-fade-up" style="margin-top:12px;">
    <div class="card-smm-body">
        <form method="POST" action="suppression/add" class="filter-bar mb-3">
            <input type="email" name="email" class="form-control-smm" placeholder="Add email to suppression" required style="min-width:200px;">
            <input type="text" name="reason" class="form-control-smm" placeholder="Reason" style="min-width:150px;">
            <button type="submit" class="btn-smm btn-smm-danger btn-smm-sm"><i class="fas fa-plus"></i> Block</button>
        </form>
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
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
            <span style="font-size:0.78rem;color:var(--text-muted);">Page <?= $suppressions['page'] ?> of <?= $suppressions['total_pages'] ?> (<?= $suppressions['total'] ?> records)</span>
            <?php
                $url = '?route=suppression&sort=' . urlencode($_GET['sort'] ?? '') . '&order=' . urlencode($_GET['order'] ?? '') . '&search=' . urlencode($_GET['search'] ?? '');
                echo renderPagination($suppressions['page'], $suppressions['total_pages'], $url);
            ?>
        </div>
    </div>
    <?php endif; ?>
</div>
