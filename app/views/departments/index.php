<div class="d-flex justify-content-between align-items-center mb-3 animate-fade-up">
    <h4 style="margin:0;"><i class="fas fa-building me-2" style="color:var(--blue-primary);"></i> Departments</h4>
    <a href="departments/create" class="btn-smm btn-smm-primary btn-smm-sm"><i class="fas fa-plus"></i> New Department</a>
</div>

<div class="tile-grid animate-fade-up">
    <?php foreach ($departments as $dept): ?>
    <div class="tile-card">
        <div class="tile-card-header">
            <div>
                <h5 class="tile-card-title"><?= escape($dept['name']) ?></h5>
                <p class="tile-card-subtitle"><?= escape(truncate($dept['description'], 50)) ?: 'No description' ?></p>
            </div>
            <span class="badge-smm badge-smm-<?= $dept['status'] === 'active' ? 'success' : 'danger' ?>"><?= $dept['status'] ?></span>
        </div>
        <div class="tile-card-body">
            <div class="tile-stat">
                <span class="tile-stat-label"><i class="fas fa-key me-1" style="color:var(--amber);"></i> Security Keys</span>
                <span class="tile-stat-value"><?= $dept['key_count'] ?></span>
            </div>
            <div class="tile-stat">
                <span class="tile-stat-label"><i class="fas fa-server me-1" style="color:var(--cyan);"></i> SMTP Accounts</span>
                <span class="tile-stat-value"><?= $dept['smtp_count'] ?></span>
            </div>
            <div class="tile-stat">
                <span class="tile-stat-label"><i class="far fa-calendar me-1" style="color:var(--text-muted);"></i> Created</span>
                <span class="tile-stat-value" style="font-size:0.8rem;color:var(--text-muted);"><?= date('M j, Y', strtotime($dept['created_at'])) ?></span>
            </div>
        </div>
        <div class="tile-card-actions">
            <a href="departments/edit?id=<?= $dept['id'] ?>" class="btn-smm btn-smm-secondary btn-smm-xs"><i class="fas fa-edit"></i> Edit</a>
            <a href="departments/delete?id=<?= $dept['id'] ?>" class="btn-smm btn-smm-danger btn-smm-xs" onclick="return confirm('Delete this department?')"><i class="fas fa-trash"></i> Delete</a>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($departments)): ?>
    <div class="tile-card">
        <div class="empty-state">
            <i class="fas fa-building"></i>
            <h4>No Departments</h4>
            <p>Create your first department to organize email activity.</p>
            <a href="departments/create" class="btn-smm btn-smm-primary btn-smm-sm">Create Department</a>
        </div>
    </div>
    <?php endif; ?>
</div>
