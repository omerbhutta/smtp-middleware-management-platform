<!-- Hero Section with SVG wave -->
<div class="hero-section animate-fade-up">
    <div class="anim-wave" style="position:absolute;bottom:0;left:0;right:0;height:30px;z-index:0;opacity:0.3;">
        <svg viewBox="0 0 1440 40" preserveAspectRatio="none" style="width:200%;height:100%;">
            <defs>
                <linearGradient id="waveGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#3b82f6" stop-opacity="0"/>
                    <stop offset="50%" stop-color="#3b82f6" stop-opacity="0.4"/>
                    <stop offset="100%" stop-color="#06b6d4" stop-opacity="0"/>
                </linearGradient>
            </defs>
            <path class="anim-wave-fill" fill="url(#waveGrad)" d="M0,20 C240,0 480,40 720,20 C960,0 1200,40 1440,20 L1440,40 L0,40 Z"/>
            <path class="anim-wave-fill" fill="url(#waveGrad)" style="animation-delay:-2s;opacity:0.5;transform:translateX(-50%);" d="M0,25 C240,5 480,45 720,25 C960,5 1200,45 1440,25 L1440,40 L0,40 Z"/>
        </svg>
    </div>
    <div style="position:relative;z-index:1;">
        <h1 class="hero-title">
            <i class="fas fa-tachometer-alt" style="color:var(--blue-primary);margin-right:8px;"></i>
            SMMP Dashboard
            <span class="live-indicator ms-2" style="vertical-align:middle;"><span class="live-dot"></span> Live</span>
        </h1>
        <p class="hero-subtitle">SMTP Middleware Management Platform &mdash; <span id="currentDate"></span></p>
    </div>
    <div class="hero-stats" style="position:relative;z-index:1;">
        <div class="hero-stat">
            <div class="hero-stat-value"><span class="status-dot online"></span> Online</div>
            <div class="hero-stat-label">Server Status</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-value" style="color:var(--emerald);">98.6%</div>
            <div class="hero-stat-label">Delivery Rate</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-value" style="color:var(--blue-primary);">Stable</div>
            <div class="hero-stat-label">SMTP Health</div>
        </div>
    </div>
</div>

<!-- Premium Stats Cards -->
<div class="stats-grid">
    <div class="stat-card blue animate-fade-up stagger-1">
        <div class="stat-card-header">
            <div class="stat-card-icon" style="position:relative;">
                <div class="anim-rings" style="position:absolute;inset:-4px;">
                    <div class="anim-rings-ring"></div>
                    <div class="anim-rings-ring"></div>
                    <div class="anim-rings-ring"></div>
                </div>
                <i class="fas fa-paper-plane" style="position:relative;z-index:1;"></i>
            </div>
            <span class="stat-card-trend up"><i class="fas fa-arrow-up"></i> +12%</span>
        </div>
        <div class="stat-card-value"><span class="stat-counter" data-target="<?= $stats['today_count'] ?>">0</span></div>
        <div class="stat-card-label">Emails Sent Today</div>
        <div class="mini-chart" id="miniChart1"></div>
    </div>

    <div class="stat-card green animate-fade-up stagger-2">
        <div class="stat-card-header">
            <div class="stat-card-icon"><i class="fas fa-calendar-alt"></i></div>
            <span class="stat-card-trend up"><i class="fas fa-arrow-up"></i> +8%</span>
        </div>
        <div class="stat-card-value"><span class="stat-counter" data-target="<?= $stats['month_count'] ?>">0</span></div>
        <div class="stat-card-label">Emails Sent This Month</div>
        <div class="mini-chart" id="miniChart2"></div>
    </div>

    <div class="stat-card red animate-fade-up stagger-3">
        <div class="stat-card-header">
            <div class="stat-card-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <span class="stat-card-trend down"><i class="fas fa-arrow-down"></i> +2%</span>
        </div>
        <div class="stat-card-value"><span class="stat-counter" data-target="<?= $stats['failed_count'] ?>">0</span></div>
        <div class="stat-card-label">Failed Requests</div>
    </div>

    <div class="stat-card purple animate-fade-up stagger-4">
        <div class="stat-card-header">
            <div class="stat-card-icon"><i class="fas fa-ban"></i></div>
            <span class="stat-card-trend up"><i class="fas fa-shield"></i> Protected</span>
        </div>
        <div class="stat-card-value"><span class="stat-counter" data-target="<?= $stats['suppressed_count'] ?>">0</span></div>
        <div class="stat-card-label">Suppressed Emails Blocked</div>
    </div>

    <div class="stat-card cyan animate-fade-up stagger-5">
        <div class="stat-card-header">
            <div class="stat-card-icon"><i class="fas fa-building"></i></div>
        </div>
        <div class="stat-card-value"><span class="stat-counter" data-target="<?= $stats['active_departments'] ?>">0</span></div>
        <div class="stat-card-label">Active Departments</div>
    </div>

    <div class="stat-card amber animate-fade-up stagger-6">
        <div class="stat-card-header">
            <div class="stat-card-icon"><i class="fas fa-server"></i></div>
        </div>
        <div class="stat-card-value"><span class="stat-counter" data-target="<?= $stats['active_smtp'] ?>">0</span></div>
        <div class="stat-card-label">Active SMTP Accounts</div>
    </div>
</div>

<!-- Charts -->
<div class="chart-grid">
    <div class="chart-card">
        <div class="chart-card-title"><i class="fas fa-chart-area" style="color:var(--blue-primary);"></i> Daily Email Traffic</div>
        <canvas id="dailyChart"></canvas>
    </div>
    <div class="chart-card">
        <div class="chart-card-title"><i class="fas fa-chart-pie" style="color:var(--purple);"></i> SMTP Provider Usage</div>
        <canvas id="providerChart"></canvas>
    </div>
</div>

<div class="chart-grid">
    <div class="chart-card">
        <div class="chart-card-title"><i class="fas fa-chart-bar" style="color:var(--emerald);"></i> Department Usage</div>
        <canvas id="deptChart"></canvas>
    </div>
    <div class="chart-card">
        <div class="chart-card-title"><i class="fas fa-chart-doughnut" style="color:var(--cyan);"></i> Success vs Failure</div>
        <canvas id="successChart"></canvas>
    </div>
</div>

<!-- Activity Feed -->
<div class="activity-feed">
    <div class="section-header">
        <div class="d-flex align-items-center gap-2">
            <h4 class="section-title mb-0"><i class="fas fa-bolt"></i> Live Activity Feed</h4>
            <span class="live-indicator" style="font-size:0.65rem;"><span class="live-dot"></span> Streaming</span>
        </div>
        <div class="data-flow-container">
            <div class="data-dot"></div>
            <div class="data-dot"></div>
            <div class="data-dot"></div>
            <div class="data-dot"></div>
            <div class="data-dot"></div>
        </div>
    </div>
    <div class="activity-timeline">
        <?php if (!empty($recent_activities)): ?>
            <?php foreach ($recent_activities as $activity): ?>
                <div class="activity-item animate-fade-up">
                    <div class="activity-dot <?= $activity['status'] === 'sent' ? 'success' : 'failed' ?>"></div>
                    <div class="activity-content">
                        <p class="activity-text">
                            <?php if ($activity['status'] === 'sent'): ?>
                                <i class="fas fa-check-circle" style="color:var(--emerald);"></i>
                            <?php else: ?>
                                <i class="fas fa-times-circle" style="color:var(--red);"></i>
                            <?php endif; ?>
                            Email <?= $activity['status'] === 'sent' ? 'sent' : 'failed' ?> to
                            <strong><?= escape(truncate($activity['recipients'] ?? 'N/A', 35)) ?></strong>
                            <?php if ($activity['department_name']): ?>
                                via <span style="color:var(--blue-primary);"><?= escape($activity['department_name']) ?></span>
                            <?php endif; ?>
                        </p>
                        <div class="activity-time">
                            <i class="far fa-clock me-1"></i> <?= timeAgo($activity['created_at']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h4>No Activity Yet</h4>
                <p>Activity will appear here once emails are sent through the platform.</p>
                <a href="smtp_accounts/create" class="btn-smm btn-smm-primary">Configure SMTP Account</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Date
document.getElementById('currentDate').textContent = new Date().toLocaleDateString('en-US', {
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
});

// Mini sparkline
function drawMiniSparkline(id, color) {
    var canvas = document.createElement('canvas');
    canvas.width = 200; canvas.height = 40;
    document.getElementById(id).appendChild(canvas);
    var ctx = canvas.getContext('2d');
    var points = Array.from({length: 20}, function() { return Math.random() * 30 + 5; });
    var max = Math.max.apply(null, points);
    var w = canvas.width, h = canvas.height;
    ctx.beginPath();
    ctx.moveTo(0, h - (points[0] / max * h));
    for (var i = 1; i < points.length; i++) {
        var x = (i / (points.length - 1)) * w;
        var y = h - (points[i] / max * h);
        ctx.lineTo(x, y);
    }
    ctx.strokeStyle = color;
    ctx.lineWidth = 1.5;
    ctx.stroke();
    // Fill
    ctx.lineTo(w, h);
    ctx.lineTo(0, h);
    ctx.closePath();
    ctx.fillStyle = color.replace(')', ', 0.08)').replace('rgb', 'rgba');
    ctx.fill();
}
drawMiniSparkline('miniChart1', 'rgb(59, 130, 246)');
drawMiniSparkline('miniChart2', 'rgb(16, 185, 129)');

// Daily Chart
var dailyData = <?= json_encode($daily_volume) ?>;
var labels = dailyData.map(function(d) { return d.date ? d.date.substring(5) : ''; });
var sent = dailyData.map(function(d) { return parseInt(d.sent); });
var failed = dailyData.map(function(d) { return parseInt(d.failed); });

new Chart(document.getElementById('dailyChart'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Sent',
                data: sent,
                borderColor: '#3b82f6',
                backgroundColor: function(ctx) {
                    var g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 280);
                    g.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
                    g.addColorStop(1, 'rgba(59, 130, 246, 0)');
                    return g;
                },
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: '#3b82f6'
            },
            {
                label: 'Failed',
                data: failed,
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0)',
                fill: false,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: '#ef4444',
                borderDash: [5, 5]
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top', labels: { boxWidth: 12, padding: 16 } }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(42,48,64,0.5)' } },
            x: { grid: { display: false } }
        },
        interaction: { intersect: false, mode: 'index' }
    }
});

// Provider Chart
new Chart(document.getElementById('providerChart'), {
    type: 'doughnut',
    data: {
        labels: ['Microsoft 365', 'Gmail', 'Custom SMTP'],
        datasets: [{
            data: [65, 20, 15],
            backgroundColor: ['#0078d4', '#ea4335', '#3b82f6'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12 } }
        },
        cutout: '65%'
    }
});

// Department Chart
var deptData = <?= json_encode($top_departments) ?>;
new Chart(document.getElementById('deptChart'), {
    type: 'bar',
    data: {
        labels: deptData.map(function(d) { return d.name || 'N/A'; }),
        datasets: [{
            label: 'Emails',
            data: deptData.map(function(d) { return parseInt(d.email_count); }),
            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#ef4444'],
            borderRadius: 4,
            borderSkipped: false
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, grid: { color: 'rgba(42,48,64,0.5)' } },
            y: { grid: { display: false } }
        }
    }
});

// Success vs Failure
var totalSent = parseInt(<?= $stats['today_count'] ?>);
var totalFailed = parseInt(<?= $stats['failed_count'] ?>);
new Chart(document.getElementById('successChart'), {
    type: 'doughnut',
    data: {
        labels: ['Successful', 'Failed'],
        datasets: [{
            data: [totalSent + totalFailed > 0 ? totalSent : 100, totalFailed > 0 ? totalFailed : 0],
            backgroundColor: ['#10b981', '#ef4444'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12 } }
        },
        cutout: '65%'
    }
});
</script>
