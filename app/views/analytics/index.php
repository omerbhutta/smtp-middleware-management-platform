<div class="animate-fade-up">
    <h4 class="mb-4"><i class="fas fa-chart-bar me-2" style="color:var(--blue-primary);"></i> Analytics & Insights</h4>

    <div class="chart-grid">
        <div class="chart-card">
            <div class="chart-card-title"><i class="fas fa-chart-line" style="color:var(--blue-primary);"></i> Monthly Email Trends</div>
            <canvas id="monthlyChart" height="250"></canvas>
        </div>
        <div class="chart-card">
            <div class="chart-card-title"><i class="fas fa-building" style="color:var(--emerald);"></i> Top Departments</div>
            <div style="overflow-y:auto;max-height:280px;">
                <?php $idx = 0; $maxDept = $top_departments ? max(array_column($top_departments, 'email_count')) : 0; ?>
                <?php foreach ($top_departments as $d): ?>
                <?php $pct = $maxDept > 0 ? round(($d['email_count'] / $maxDept) * 100) : 0; ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size:0.85rem;color:var(--text-secondary);"><?= escape($d['name'] ?? 'N/A') ?></span>
                        <span style="font-size:0.85rem;font-weight:600;"><?= $d['email_count'] ?></span>
                    </div>
                    <div style="height:6px;background:var(--border-color);border-radius:3px;overflow:hidden;">
                        <div style="height:100%;width:<?= $pct ?>%;background:linear-gradient(90deg,#3b82f6,#06b6d4);border-radius:3px;transition:width 1s ease;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($top_departments)): ?>
                <div class="empty-state"><i class="fas fa-building"></i><h4>No Data</h4></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="chart-grid">
        <div class="chart-card">
            <div class="chart-card-title"><i class="fas fa-server" style="color:var(--cyan);"></i> SMTP Performance</div>
            <div style="overflow-x:auto;">
                <table class="table-modern">
                    <thead>
                        <tr><th>Sender</th><th>Host</th><th>Emails</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_smtp as $s): ?>
                        <tr>
                            <td><?= escape($s['sender_email']) ?></td>
                            <td><code style="background:rgba(0,0,0,0.3);"><?= escape($s['smtp_host']) ?></code></td>
                            <td><span class="badge-smm badge-smm-info"><?= $s['email_count'] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($top_smtp)): ?>
                        <tr><td colspan="3"><div class="empty-state"><i class="fas fa-server"></i><h4>No Data</h4></div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="chart-card">
            <div class="chart-card-title"><i class="fas fa-exclamation-triangle" style="color:var(--red);"></i> Failure Analysis</div>
            <div style="overflow-x:auto;">
                <table class="table-modern">
                    <thead>
                        <tr><th>Error</th><th>Count</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($failure_analysis as $f): ?>
                        <tr>
                            <td><span style="font-size:0.82rem;color:var(--text-secondary);"><?= escape(truncate($f['error_message'] ?? 'Unknown', 50)) ?></span></td>
                            <td><span class="badge-smm badge-smm-danger"><?= $f['count'] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($failure_analysis)): ?>
                        <tr><td colspan="2"><div class="empty-state"><i class="fas fa-check-circle" style="color:var(--emerald);"></i><h4>No Failures</h4></div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
var monthlyData = <?= json_encode($monthly_trend) ?>;
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: monthlyData.map(function(d) { return d.month; }),
        datasets: [{
            label: 'Emails',
            data: monthlyData.map(function(d) { return parseInt(d.count); }),
            backgroundColor: function(ctx) {
                var g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 300);
                g.addColorStop(0, 'rgba(59, 130, 246, 0.6)');
                g.addColorStop(1, 'rgba(59, 130, 246, 0.1)');
                return g;
            },
            borderRadius: 4,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(42,48,64,0.5)' } },
            x: { grid: { display: false } }
        }
    }
});
</script>
