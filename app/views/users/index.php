<div class="card-smm animate-fade-up">
    <div class="card-smm-header">
        <h3><i class="fas fa-users me-2" style="color:var(--blue-primary);"></i> All Users</h3>
        <a href="users/create" class="btn-smm btn-smm-primary btn-smm-sm"><i class="fas fa-plus"></i> New User</a>
    </div>
    <div class="card-smm-body p-0">
        <div style="overflow-x:auto;">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>MFA</th>
                        <th>Last Login</th>
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
                    <tr><td colspan="8"><div class="empty-state"><i class="fas fa-users"></i><h4>No Users Found</h4><p>Create your first user to get started.</p></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
