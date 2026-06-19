<div class="card-smm animate-fade-up">
    <div class="card-smm-header">
        <h3><i class="fas fa-building me-2" style="color:var(--blue-primary);"></i> Departments</h3>
        <a href="departments/create" class="btn-smm btn-smm-primary btn-smm-sm"><i class="fas fa-plus"></i> New Department</a>
    </div>
    <div class="card-smm-body">
        <form method="GET" action="index.php" class="filter-bar mb-3">
            <input type="hidden" name="route" value="departments">
            <input type="text" name="search" class="form-control-smm" placeholder="Search departments..." value="<?= escape($_GET['search'] ?? '') ?>" style="min-width:200px;">
            <button type="submit" class="btn-smm btn-smm-primary btn-smm-sm"><i class="fas fa-search"></i></button>
            <?php if (!empty($_GET['search']) || !empty($_GET['sort'])): ?>
            <a href="?route=departments" class="btn-smm btn-smm-secondary btn-smm-sm"><i class="fas fa-times"></i></a>
            <?php endif; ?>
        </form>
        <div style="overflow-x:auto;">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th><a href="<?= sortUrl('name', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Name <?= sortIcon('name', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th>Description</th>
                        <th><a href="<?= sortUrl('key_count', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Security Keys <?= sortIcon('key_count', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th><a href="<?= sortUrl('smtp_count', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">SMTP Accounts <?= sortIcon('smtp_count', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th><a href="<?= sortUrl('status', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?>" class="sort-link">Status <?= sortIcon('status', $_GET['sort'] ?? '', $_GET['order'] ?? '') ?></a></th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($departments as $dept): ?>
                    <tr>
                        <td><strong><?= escape($dept['name']) ?></strong></td>
                        <td><span style="color:var(--text-muted);font-size:0.82rem;"><?= escape(truncate($dept['description'], 50)) ?: 'No description' ?></span></td>
                        <td><span class="badge-smm badge-smm-info"><?= $dept['key_count'] ?></span></td>
                        <td><span class="badge-smm badge-smm-info"><?= $dept['smtp_count'] ?></span></td>
                        <td>
                            <span class="badge-smm badge-smm-<?= $dept['status'] === 'active' ? 'success' : 'danger' ?>"><?= $dept['status'] ?></span>
                        </td>
                        <td style="text-align:right;">
                            <a href="departments/edit?id=<?= $dept['id'] ?>" class="btn-smm btn-smm-secondary btn-smm-xs"><i class="fas fa-edit"></i></a>
                            <a href="departments/delete?id=<?= $dept['id'] ?>" class="btn-smm btn-smm-danger btn-smm-xs" onclick="return confirm('Delete this department?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($departments)): ?>
                    <tr><td colspan="6"><div class="empty-state"><i class="fas fa-building"></i><h4>No Departments</h4><p>Create your first department to organize email activity.</p><a href="departments/create" class="btn-smm btn-smm-primary btn-smm-sm">Create Department</a></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>