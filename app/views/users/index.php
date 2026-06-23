<?php include VIEW_PATH . 'partials/hero_header.php'; ?>

<div class="card-smm animate-fade-up" style="margin-top:12px;">
    <div class="card-smm-body">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:12px;">
            <div style="font-size:0.9rem;color:var(--text-muted);">All registered platform users</div>
            <a href="users/create" class="btn-smm btn-smm-primary btn-smm-sm"><i class="fas fa-plus"></i> New User</a>
        </div>
        <form method="GET" action="index.php" class="filter-bar mb-3">
            <input type="hidden" name="route" value="users">
            <input type="text" name="search" class="form-control-smm" placeholder="Search users..." value="<?= escape($_GET['search'] ?? '') ?>" style="min-width:200px;">
            <button type="submit" class="btn-smm btn-smm-primary btn-smm-sm"><i class="fas fa-search"></i></button>
            <?php if (!empty($_GET['search']) || !empty($_GET['sort'])): ?>
            <a href="?route=users" class="btn-smm btn-smm-secondary btn-smm-sm"><i class="fas fa-times"></i></a>
            <?php endif; ?>
        </form>
        <div style="overflow-x:auto;">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th><a href="<?= sortUrl('full_name', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Name <?= sortIcon('full_name', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th><a href="<?= sortUrl('username', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Username <?= sortIcon('username', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th><a href="<?= sortUrl('email', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Email <?= sortIcon('email', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th><a href="<?= sortUrl('role', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Role <?= sortIcon('role', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th><a href="<?= sortUrl('status', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Status <?= sortIcon('status', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th><a href="<?= sortUrl('mfa_enabled', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">MFA <?= sortIcon('mfa_enabled', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th><a href="<?= sortUrl('last_login', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Last Login <?= sortIcon('last_login', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#3b82f6,#06b6d4);display:flex;align-items:center;justify-content:center;font-size:0.75rem;color:#fff;font-weight:600;flex-shrink:0;">
                                    <?= strtoupper(substr($user['full_name'] ?? $user['username'], 0, 1)) ?>
                                </div>
                                <strong><?= escape($user['full_name'] ?? $user['username']) ?></strong>
                            </div>
                        </td>
                        <td><span style="color:var(--text-muted);font-size:0.82rem;"><?= escape($user['username']) ?></span></td>
                        <td><?= escape($user['email']) ?></td>
                        <td>
                            <span class="badge-smm badge-smm-<?= $user['role'] === 'admin' ? 'warning' : 'info' ?>">
                                <?= escape($user['role']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge-smm badge-smm-<?= $user['status'] === 'active' ? 'success' : 'danger' ?>">
                                <?= $user['status'] ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge-smm badge-smm-<?= $user['mfa_enabled'] ? 'success' : 'neutral' ?>">
                                <?= $user['mfa_enabled'] ? 'Enabled' : 'Disabled' ?>
                            </span>
                        </td>
                        <td><span style="color:var(--text-muted);font-size:0.82rem;"><?= timeAgo($user['last_login']) ?></span></td>
                        <td style="text-align:right;">
                            <a href="users/edit?id=<?= $user['id'] ?>" class="btn-smm btn-smm-secondary btn-smm-xs"><i class="fas fa-edit"></i></a>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <a href="users/delete?id=<?= $user['id'] ?>" class="btn-smm btn-smm-danger btn-smm-xs" onclick="return confirm('Delete this user?')"><i class="fas fa-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                    <tr><td colspan="9"><div class="empty-state"><i class="fas fa-users"></i><h4>No Users Found</h4><p>Create your first user to get started.</p></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>