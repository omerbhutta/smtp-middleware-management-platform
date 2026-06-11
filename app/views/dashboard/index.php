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
    <?php
    $today = date('Y-m-d');
    $monthStart = date('Y-m-01');
    $totalAll = $stats['today_count'] + $stats['failed_count'];
    $deliveryRate = $totalAll > 0 ? round(($stats['today_count'] / $totalAll) * 100, 1) : 100;
    ?>
    <div class="hero-stats" style="position:relative;z-index:1;">
        <div class="hero-stat">
            <div class="hero-stat-value"><span class="status-dot online"></span> Online</div>
            <div class="hero-stat-label">Server Status</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-value" style="color:var(--emerald);"><?= $deliveryRate ?>%</div>
            <div class="hero-stat-label">Delivery Rate (Today)</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-value" style="color:var(--blue-primary);">Stable</div>
            <div class="hero-stat-label">SMTP Health</div>
        </div>
    </div>
</div>

<div class="stats-grid">
    <a href="email_logs?date_from=<?= $today ?>&date_to=<?= $today ?>" class="stat-card-link">
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
                <span class="stat-card-trend up"><i class="fas fa-arrow-up"></i> +<?= $week_pct ?>%</span>
            </div>
            <div class="stat-card-value"><span class="stat-counter" data-target="<?= $stats['today_count'] ?>">0</span></div>
            <div class="stat-card-label">Emails Sent Today</div>
            <div class="mini-chart" id="miniChart1"></div>
        </div>
    </a>

    <a href="email_logs?date_from=<?= $monthStart ?>" class="stat-card-link">
        <div class="stat-card green animate-fade-up stagger-2">
            <div class="stat-card-header">
                <div class="stat-card-icon"><i class="fas fa-calendar-alt"></i></div>
                <span class="stat-card-trend up"><i class="fas fa-arrow-up"></i> +<?= $month_pct ?>%</span>
            </div>
            <div class="stat-card-value"><span class="stat-counter" data-target="<?= $stats['month_count'] ?>">0</span></div>
            <div class="stat-card-label">Emails Sent This Month</div>
            <div class="mini-chart" id="miniChart2"></div>
        </div>
    </a>

    <a href="email_logs?status=failed" class="stat-card-link">
        <div class="stat-card red animate-fade-up stagger-3">
            <div class="stat-card-header">
                <div class="stat-card-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <span class="stat-card-trend down"><i class="fas fa-arrow-<?= $failed_pct >= 0 ? 'up' : 'down' ?>"></i> <?= abs($failed_pct) ?>%</span>
            </div>
            <div class="stat-card-value"><span class="stat-counter" data-target="<?= $stats['failed_count'] ?>">0</span></div>
            <div class="stat-card-label">Failed Requests</div>
        </div>
    </a>

    <a href="email_logs?search=Skipped" class="stat-card-link">
        <div class="stat-card orange animate-fade-up stagger-4">
            <div class="stat-card-header">
                <div class="stat-card-icon"><i class="fas fa-filter"></i></div>
                <span class="stat-card-trend up"><i class="fas fa-shield"></i> Blocked</span>
            </div>
            <div class="stat-card-value"><span class="stat-counter" data-target="<?= $stats['skipped_count'] ?>">0</span></div>
            <div class="stat-card-label">Requests With Skipped Recipients</div>
            <div style="margin-top:4px;display:flex;gap:10px;font-size:0.65rem;color:var(--text-muted);">
                <span><span style="color:var(--rose);">●</span> Forbidden: <strong><?= $stats['skipped_breakdown']['forbidden'] ?? 0 ?></strong></span>
                <span><span style="color:var(--amber);">●</span> Suppressed: <strong><?= $stats['skipped_breakdown']['suppressed'] ?? 0 ?></strong></span>
            </div>
        </div>
    </a>

    <a href="suppression" class="stat-card-link">
        <div class="stat-card purple animate-fade-up stagger-5">
            <div class="stat-card-header">
                <div class="stat-card-icon"><i class="fas fa-ban"></i></div>
                <span class="stat-card-trend up"><i class="fas fa-shield"></i> Protected</span>
            </div>
            <div class="stat-card-value"><span class="stat-counter" data-target="<?= $stats['suppressed_count'] ?>">0</span></div>
            <div class="stat-card-label">Suppressed Addresses</div>
        </div>
    </a>

    <a href="departments" class="stat-card-link">
        <div class="stat-card cyan animate-fade-up stagger-6">
            <div class="stat-card-header">
                <div class="stat-card-icon"><i class="fas fa-building"></i></div>
            </div>
            <div class="stat-card-value"><span class="stat-counter" data-target="<?= $stats['active_departments'] ?>">0</span></div>
            <div class="stat-card-label">Active Departments</div>
        </div>
    </a>

    <a href="smtp_accounts" class="stat-card-link">
        <div class="stat-card amber animate-fade-up stagger-7">
            <div class="stat-card-header">
                <div class="stat-card-icon"><i class="fas fa-server"></i></div>
            </div>
            <div class="stat-card-value"><span class="stat-counter" data-target="<?= $stats['active_smtp'] ?>">0</span></div>
            <div class="stat-card-label">Active SMTP Accounts</div>
        </div>
    </a>
</div>

<!-- Charts -->
<div class="chart-grid">
    <div class="chart-card">
        <div class="chart-card-title"><i class="fas fa-chart-area" style="color:var(--blue-primary);"></i> Daily Email Traffic</div>
        <div style="position:relative;height:240px;"><canvas id="dailyChart"></canvas></div>
    </div>
    <div class="chart-card">
        <div class="chart-card-title"><i class="fas fa-chart-pie" style="color:var(--purple);"></i> SMTP Provider Usage</div>
        <div style="position:relative;height:240px;"><canvas id="providerChart"></canvas></div>
    </div>
</div>

<div class="chart-grid">
    <div class="chart-card">
        <div class="chart-card-title"><i class="fas fa-chart-bar" style="color:var(--emerald);"></i> Department Usage</div>
        <div style="position:relative;height:240px;"><canvas id="deptChart"></canvas></div>
    </div>
    <div class="chart-card">
        <div class="chart-card-title"><i class="fas fa-chart-doughnut" style="color:var(--cyan);"></i> Success vs Failure</div>
        <div style="position:relative;height:240px;"><canvas id="successChart"></canvas></div>
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
var skippedDailyData = <?= json_encode($skipped_recipient_daily) ?>;
var hasDailyData = dailyData.length > 0;
var labels = hasDailyData ? dailyData.map(function(d) { return d.date ? d.date.substring(5) : ''; }) : ['No Data'];
var sent = hasDailyData ? dailyData.map(function(d) { return parseInt(d.sent); }) : [0];
var failed = hasDailyData ? dailyData.map(function(d) { return parseInt(d.failed); }) : [0];
var skipped = hasDailyData ? dailyData.map(function(d) {
    var match = skippedDailyData.find(function(s) { return s.date === d.date; });
    return match ? parseInt(match.skipped) : 0;
}) : [0];
var dailySentColor = hasDailyData ? '#3b82f6' : '#2a3040';
var dailyFailedColor = hasDailyData ? '#ef4444' : 'transparent';
var dailySkippedColor = hasDailyData ? '#f59e0b' : 'transparent';

new Chart(document.getElementById('dailyChart'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Sent',
                data: sent,
                borderColor: '#3b82f6',
                backgroundColor: hasDailyData ? function(ctx) {
                    var g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 260);
                    g.addColorStop(0, 'rgba(59, 130, 246, 0.25)');
                    g.addColorStop(0.5, 'rgba(59, 130, 246, 0.10)');
                    g.addColorStop(1, 'rgba(59, 130, 246, 0)');
                    return g;
                } : 'rgba(42,48,64,0.2)',
                fill: true,
                tension: 0.35,
                borderWidth: 2.5,
                pointRadius: 0,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#3b82f6',
                pointHoverBorderWidth: 2.5
            },
            {
                label: 'Failed',
                data: failed,
                borderColor: '#ef4444',
                backgroundColor: hasDailyData ? function(ctx) {
                    var g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 260);
                    g.addColorStop(0, 'rgba(239, 68, 68, 0.18)');
                    g.addColorStop(0.5, 'rgba(239, 68, 68, 0.06)');
                    g.addColorStop(1, 'rgba(239, 68, 68, 0)');
                    return g;
                } : 'transparent',
                fill: true,
                tension: 0.35,
                borderWidth: 2,
                borderDash: [6, 3],
                pointRadius: 0,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#ef4444',
                pointHoverBorderWidth: 2.5
            },
            {
                label: 'Skipped',
                data: skipped,
                borderColor: '#f59e0b',
                backgroundColor: hasDailyData ? function(ctx) {
                    var g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 260);
                    g.addColorStop(0, 'rgba(245, 158, 11, 0.15)');
                    g.addColorStop(0.5, 'rgba(245, 158, 11, 0.05)');
                    g.addColorStop(1, 'rgba(245, 158, 11, 0)');
                    return g;
                } : 'transparent',
                fill: true,
                tension: 0.35,
                borderWidth: 2,
                borderDash: [3, 3],
                pointRadius: 0,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#f59e0b',
                pointHoverBorderWidth: 2.5
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    boxWidth: 10,
                    boxHeight: 10,
                    borderRadius: 3,
                    padding: 16,
                    usePointStyle: true,
                    pointStyle: 'circle',
                    font: { size: 11 }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(15,23,42,0.9)',
                titleFont: { size: 11 },
                bodyFont: { size: 12 },
                padding: 10,
                cornerRadius: 6,
                displayColors: true,
                boxPadding: 4
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(42,48,64,0.3)', drawBorder: false, lineWidth: 0.5 },
                ticks: { font: { size: 10 }, color: '#64748b', maxTicksLimit: 6, padding: 8 }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 10 }, color: '#64748b', maxTicksLimit: 10, maxRotation: 0 }
            }
        },
        interaction: { intersect: false, mode: 'nearest' },
        hover: { mode: 'nearest', intersect: false }
    }
});

// Provider Chart
var providerData = <?= json_encode($provider_usage) ?>;
var provLabels, provValues;
if (providerData.length) {
    provLabels = providerData.map(function(d) { return d.smtp_host || d.sender_email || 'Unknown'; });
    provValues = providerData.map(function(d) { return parseInt(d.email_count); });
} else {
    provLabels = ['No Activity Yet'];
    provValues = [1];
}
var provColors = ['#0078d4', '#ea4335', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#ef4444'];
new Chart(document.getElementById('providerChart'), {
    type: 'doughnut',
    data: {
        labels: provLabels,
        datasets: [{
            data: provValues,
            backgroundColor: provColors.slice(0, provLabels.length),
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
if (!deptData.length) { deptData = [{name: 'No Activity Yet', email_count: 1}]; }
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
var dailyDataSF = <?= json_encode($daily_volume) ?>;
var totalSent30 = dailyDataSF.reduce(function(sum, d) { return sum + parseInt(d.sent || 0); }, 0);
var totalFailed30 = dailyDataSF.reduce(function(sum, d) { return sum + parseInt(d.failed || 0); }, 0);
var totalSkipped30 = <?= $skipped30 ?>;
var hasData = totalSent30 + totalFailed30 + totalSkipped30 > 0;
new Chart(document.getElementById('successChart'), {
    type: 'doughnut',
    data: {
        labels: hasData ? ['Sent', 'Failed', 'Skipped'] : ['Awaiting Data'],
        datasets: [{
            data: hasData ? [totalSent30, totalFailed30, totalSkipped30] : [1],
            backgroundColor: hasData ? ['#10b981', '#ef4444', '#f59e0b'] : ['#2a3040'],
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
