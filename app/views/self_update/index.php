<div class="animate-fade-up">
    <h4 class="mb-4"><i class="fas fa-arrow-up-circle me-2" style="color:var(--blue-primary);"></i> Self Update</h4>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card blue animate-fade-up stagger-1" style="padding:16px;">
                <div class="stat-card-header" style="margin-bottom:4px;">
                    <div class="stat-card-icon" style="width:36px;height:36px;font-size:0.9rem;"><i class="fas fa-code-branch"></i></div>
                </div>
                <div class="stat-card-value" style="font-size:1.2rem;"><code style="background:transparent;color:inherit;font-size:0.9rem;"><?= escape($currentBranch) ?></code></div>
                <div class="stat-card-label" style="font-size:0.72rem;">Current Branch</div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="stat-card blue animate-fade-up stagger-2" style="padding:16px;">
                <div class="stat-card-header" style="margin-bottom:4px;">
                    <div class="stat-card-icon" style="width:36px;height:36px;font-size:0.9rem;"><i class="fas fa-git-commit"></i></div>
                </div>
                <div class="stat-card-value" style="font-size:0.9rem;"><code style="background:transparent;color:inherit;font-weight:400;"><?= escape($currentCommit) ?></code></div>
                <div class="stat-card-label" style="font-size:0.72rem;">Last Commit</div>
            </div>
        </div>
    </div>

    <?php if ($gitStatus): ?>
    <div class="alert-smm" style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.2);color:var(--amber);margin-bottom:20px;">
        <i class="fas fa-exclamation-triangle"></i>
        <div>
            <strong>Uncommitted changes detected:</strong>
            <pre style="margin:4px 0 0;font-size:0.75rem;color:var(--text-muted);"><?= escape($gitStatus) ?></pre>
        </div>
    </div>
    <?php endif; ?>

    <div class="card-smm animate-fade-up mb-4">
        <div class="card-smm-body text-center py-4">
            <p class="mb-3" style="color:var(--text-muted);">Update the platform with one click — pull code and run migrations.</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <button class="btn-smm btn-smm-primary btn-smm-lg" onclick="selfUpdate()" id="update-btn">
                    <i class="fas fa-arrow-down"></i> Pull Latest
                </button>
                <button class="btn-smm btn-smm-secondary btn-smm-lg" onclick="fullUpdate()" id="full-update-btn">
                    <i class="fas fa-rocket"></i> Full Update
                </button>
            </div>
        </div>
    </div>

    <div id="update-output" class="card-smm animate-fade-up mb-4 d-none">
        <div class="card-smm-header">
            <h6><i class="fas fa-terminal me-2"></i> Output</h6>
        </div>
        <div class="card-smm-body p-0">
            <pre id="update-output-text" class="mb-0" style="border-radius:0;background:var(--bg-primary);padding:16px;font-size:0.78rem;max-height:400px;overflow-y:auto;color:var(--text-secondary);"></pre>
        </div>
    </div>

    <?php if (count($logs) > 0): ?>
    <div class="card-smm animate-fade-up">
        <div class="card-smm-header">
            <h6><i class="fas fa-clock-history me-2"></i> Recent Updates</h6>
        </div>
        <div class="card-smm-body p-0">
            <table class="table-modern">
                <thead>
                    <tr><th>Time</th><th>User</th><th>Action</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td style="font-size:0.8rem;color:var(--text-muted);"><?= date('M j, H:i', strtotime($log['created_at'])) ?></td>
                        <td style="font-size:0.85rem;"><?= escape($log['full_name'] ?: $log['username'] ?: '-') ?></td>
                        <td style="font-size:0.82rem;">
                            <?= $log['action'] === 'self_full_update' ? 'Full Update' : 'Pull Latest' ?>
                        </td>
                        <td>
                            <?php if ($log['status'] === 'success'): ?>
                                <span class="badge-smm badge-smm-success">Success</span>
                            <?php elseif ($log['status'] === 'failed'): ?>
                                <span class="badge-smm badge-smm-danger">Failed</span>
                            <?php else: ?>
                                <span class="badge-smm badge-smm-warning">Running</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function selfUpdate() {
    var btn = document.getElementById('update-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Pulling...';
    document.getElementById('update-output').classList.remove('d-none');
    document.getElementById('update-output-text').textContent = '';

    fetch('self_update/pull', { method: 'POST' })
        .then(function(r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function(res) {
            var text = '';
            if (res.output) text += res.output;
            if (res.new_commit) text += '\nNew commit: ' + res.new_commit;
            if (res.error) text += '\n\nERROR: ' + res.error;
            document.getElementById('update-output-text').textContent = text || JSON.stringify(res, null, 2);
            if (res.success) setTimeout(function() { location.reload(); }, 2000);
        })
        .catch(function(err) {
            document.getElementById('update-output-text').textContent = 'Request failed: ' + err.message;
        })
        .finally(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-arrow-down"></i> Pull Latest';
        });
}

function fullUpdate() {
    var btn = document.getElementById('full-update-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Running full update...';
    document.getElementById('update-output').classList.remove('d-none');
    document.getElementById('update-output-text').textContent = '';
    document.getElementById('update-output-text').textContent = '';

    fetch('self_update/fullUpdate', { method: 'POST' })
        .then(function(r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function(res) {
            var text = '';
            if (res.output) text += res.output;
            if (res.new_commit) text += '\nNew commit: ' + res.new_commit;
            if (res.error) text += '\n\nERROR: ' + res.error;
            document.getElementById('update-output-text').textContent = text || JSON.stringify(res, null, 2);
            if (res.success) setTimeout(function() { location.reload(); }, 3000);
        })
        .catch(function(err) {
            document.getElementById('update-output-text').textContent = 'Request failed: ' + err.message;
        })
        .finally(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-rocket"></i> Full Update';
        });
}
</script>