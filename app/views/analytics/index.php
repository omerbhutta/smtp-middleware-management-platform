<div class="animate-fade-up">
    <h4 class="mb-4"><i class="fas fa-chart-bar me-2" style="color:var(--blue-primary);"></i> Analytics & Insights</h4>

    <!-- Summary Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card orange animate-fade-up stagger-1" style="padding:16px;">
                <div class="stat-card-header" style="margin-bottom:4px;">
                    <div class="stat-card-icon" style="width:36px;height:36px;font-size:0.9rem;"><i class="fas fa-filter"></i></div>
                </div>
                <div class="stat-card-value" style="font-size:1.5rem;"><?= $skipped_breakdown['total'] ?? 0 ?></div>
                <div class="stat-card-label" style="font-size:0.72rem;">Total Skipped Requests</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card red animate-fade-up stagger-2" style="padding:16px;">
                <div class="stat-card-header" style="margin-bottom:4px;">
                    <div class="stat-card-icon" style="width:36px;height:36px;font-size:0.9rem;"><i class="fas fa-ban"></i></div>
                </div>
                <div class="stat-card-value" style="font-size:1.5rem;"><?= $skipped_breakdown['forbidden'] ?? 0 ?></div>
                <div class="stat-card-label" style="font-size:0.72rem;">Forbidden Emails Blocked</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card amber animate-fade-up stagger-3" style="padding:16px;">
                <div class="stat-card-header" style="margin-bottom:4px;">
                    <div class="stat-card-icon" style="width:36px;height:36px;font-size:0.9rem;"><i class="fas fa-shield"></i></div>
                </div>
                <div class="stat-card-value" style="font-size:1.5rem;"><?= $skipped_breakdown['suppressed'] ?? 0 ?></div>
                <div class="stat-card-label" style="font-size:0.72rem;">Suppressed Bounces Blocked</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card blue animate-fade-up stagger-4" style="padding:16px;">
                <div class="stat-card-header" style="margin-bottom:4px;">
                    <div class="stat-card-icon" style="width:36px;height:36px;font-size:0.9rem;"><i class="fas fa-percentage"></i></div>
                </div>
                <div class="stat-card-value" style="font-size:1.5rem;"><?= $success_rate ?>%</div>
                <div class="stat-card-label" style="font-size:0.72rem;">Overall Success Rate</div>
            </div>
        </div>
    </div>

    <!-- Row 1: Daily Traffic + Monthly Trends -->
    <div class="chart-grid">
        <div class="chart-card">
            <div class="chart-card-title"><i class="fas fa-chart-area" style="color:var(--blue-primary);"></i> Daily Email Traffic (30d)</div>
            <div style="position:relative;height:240px;"><canvas id="dailyTrafficChart"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="chart-card-title"><i class="fas fa-chart-line" style="color:var(--purple);"></i> Monthly Email Trends</div>
            <div style="position:relative;height:240px;"><canvas id="monthlyChart"></canvas></div>
        </div>
    </div>

    <!-- Row 2: Day of Week + Hourly Distribution -->
    <div class="chart-grid">
        <div class="chart-card">
            <div class="chart-card-title"><i class="fas fa-calendar-week" style="color:var(--emerald);"></i> Day of Week Distribution</div>
            <div style="position:relative;height:240px;"><canvas id="dayOfWeekChart"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="chart-card-title"><i class="fas fa-clock" style="color:var(--cyan);"></i> Hourly Distribution (30d)</div>
            <div style="position:relative;height:240px;"><canvas id="hourlyChart"></canvas></div>
        </div>
    </div>

    <!-- Row 3: Dept Usage + Provider Usage -->
    <div class="chart-grid">
        <div class="chart-card">
            <div class="chart-card-title"><i class="fas fa-building" style="color:var(--blue-primary);"></i> Department Usage</div>
            <div style="position:relative;height:240px;"><canvas id="deptChart"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="chart-card-title"><i class="fas fa-server" style="color:var(--purple);"></i> SMTP Provider Usage</div>
            <div style="position:relative;height:240px;"><canvas id="providerChart"></canvas></div>
        </div>
    </div>

    <!-- Row 4: Success Breakdown + Blocked 7d -->
    <div class="chart-grid">
        <div class="chart-card">
            <div class="chart-card-title"><i class="fas fa-chart-pie" style="color:var(--emerald);"></i> Success Breakdown (30d)</div>
            <div style="position:relative;height:240px;"><canvas id="successBreakdownChart"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="chart-card-title"><i class="fas fa-shield" style="color:var(--amber);"></i> Blocked Requests (Last 7 Days)</div>
            <div style="position:relative;height:240px;"><canvas id="blocked7Chart"></canvas></div>
        </div>
    </div>

    <!-- Row 5: Skipped Trend + Top Departments Bars -->
    <div class="chart-grid">
        <div class="chart-card">
            <div class="chart-card-title"><i class="fas fa-chart-area" style="color:var(--orange);"></i> Skipped Recipients Trend (30d)</div>
            <div style="position:relative;height:240px;"><canvas id="skippedChart"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="chart-card-title"><i class="fas fa-building" style="color:var(--emerald);"></i> Top Departments</div>
            <div style="overflow-y:auto;max-height:260px;">
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

    <!-- Row 6: SMTP Performance + Failure Analysis -->
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
// 1. Daily Traffic Chart (30d) - Sent / Failed / Skipped
var dailyData = <?= json_encode($daily_volume) ?>;
var skippedDailyData = <?= json_encode($skipped_recipient_daily) ?>;
var hasDaily = dailyData.length > 0;
var dtLabels = hasDaily ? dailyData.map(function(d) { return d.date ? d.date.substring(5) : ''; }) : ['No Data'];
var dtSent = hasDaily ? dailyData.map(function(d) { return parseInt(d.sent); }) : [0];
var dtFailed = hasDaily ? dailyData.map(function(d) { return parseInt(d.failed); }) : [0];
var dtSkipped = hasDaily ? dailyData.map(function(d) {
    var m = skippedDailyData.find(function(s) { return s.date === d.date; });
    return m ? parseInt(m.skipped) : 0;
}) : [0];

new Chart(document.getElementById('dailyTrafficChart'), {
    type: 'line',
    data: { labels: dtLabels, datasets: [
        { label: 'Sent', data: dtSent, borderColor: '#3b82f6', backgroundColor: function(c) { var g=c.chart.ctx.createLinearGradient(0,0,0,240); g.addColorStop(0,'rgba(59,130,246,0.25)'); g.addColorStop(1,'rgba(59,130,246,0)'); return g; }, fill: true, tension: 0.35, borderWidth: 2.5, pointRadius: 0, pointHoverRadius: 5, pointHoverBackgroundColor: '#fff', pointHoverBorderColor: '#3b82f6', pointHoverBorderWidth: 2.5 },
        { label: 'Failed', data: dtFailed, borderColor: '#ef4444', backgroundColor: function(c) { var g=c.chart.ctx.createLinearGradient(0,0,0,240); g.addColorStop(0,'rgba(239,68,68,0.18)'); g.addColorStop(1,'rgba(239,68,68,0)'); return g; }, fill: true, tension: 0.35, borderWidth: 2, borderDash: [6,3], pointRadius: 0, pointHoverRadius: 5, pointHoverBackgroundColor: '#fff', pointHoverBorderColor: '#ef4444', pointHoverBorderWidth: 2.5 },
        { label: 'Skipped', data: dtSkipped, borderColor: '#f59e0b', backgroundColor: function(c) { var g=c.chart.ctx.createLinearGradient(0,0,0,240); g.addColorStop(0,'rgba(245,158,11,0.15)'); g.addColorStop(1,'rgba(245,158,11,0)'); return g; }, fill: true, tension: 0.35, borderWidth: 2, borderDash: [3,3], pointRadius: 0, pointHoverRadius: 5, pointHoverBackgroundColor: '#fff', pointHoverBorderColor: '#f59e0b', pointHoverBorderWidth: 2.5 }
    ]},
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'top', labels: { boxWidth: 10, boxHeight: 10, borderRadius: 3, padding: 14, usePointStyle: true, pointStyle: 'circle', font: {size:10} } }, tooltip: { backgroundColor: 'rgba(15,23,42,0.9)', padding: 10, cornerRadius: 6 } },
        scales: { y: { beginAtZero: true, grid: { color: 'rgba(42,48,64,0.3)', drawBorder: false, lineWidth: 0.5 }, ticks: { font: {size:10}, color: '#64748b', maxTicksLimit: 5, padding: 8 } }, x: { grid: { display: false }, ticks: { font: {size:10}, color: '#64748b', maxTicksLimit: 10, maxRotation: 0 } } },
        interaction: { intersect: false, mode: 'nearest' }
    }
});

// 2. Monthly Trends
var monthlyData = <?= json_encode($monthly_trend) ?>;
var hasMonthly = monthlyData.length > 0;
if (!hasMonthly) { monthlyData = [{month: 'No Data', count: 0}]; }
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: { labels: monthlyData.map(function(d) { return d.month; }), datasets: [{ label: 'Emails', data: monthlyData.map(function(d) { return parseInt(d.count); }), backgroundColor: hasMonthly ? function(c) { var g=c.chart.ctx.createLinearGradient(0,0,0,240); g.addColorStop(0,'rgba(59,130,246,0.7)'); g.addColorStop(0.6,'rgba(59,130,246,0.3)'); g.addColorStop(1,'rgba(59,130,246,0.05)'); return g; } : 'rgba(42,48,64,0.3)', borderRadius: 6, borderSkipped: false, borderWidth: 0, barPercentage: 0.6 }] },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(15,23,42,0.9)', padding: 10, cornerRadius: 6 } },
        scales: { y: { beginAtZero: true, grid: { color: 'rgba(42,48,64,0.3)', drawBorder: false, lineWidth: 0.5 }, ticks: { font: {size:10}, color: '#64748b', maxTicksLimit: 5, padding: 8 } }, x: { grid: { display: false }, ticks: { font: {size:10}, color: '#64748b', maxRotation: 45 } } }
    }
});

// 3. Day of Week Distribution
var dowData = <?= json_encode($day_of_week) ?>;
var hasDow = dowData.length > 0;
var dowOrder = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
if (!hasDow) { dowData = [{dayname: 'No Data', count: 0}]; }
var dowLabels = hasDow ? dowData.map(function(d) { return d.dayname; }) : ['No Data'];
var dowValues = hasDow ? dowData.map(function(d) { return parseInt(d.count); }) : [0];
new Chart(document.getElementById('dayOfWeekChart'), {
    type: 'bar',
    data: { labels: dowLabels, datasets: [{ label: 'Emails', data: dowValues, backgroundColor: ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#06b6d4','#ef4444','#ec4899'], borderRadius: 4, borderSkipped: false, borderWidth: 0, barPercentage: 0.65 }] },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(15,23,42,0.9)', padding: 10, cornerRadius: 6 } },
        scales: { y: { beginAtZero: true, grid: { color: 'rgba(42,48,64,0.3)', drawBorder: false, lineWidth: 0.5 }, ticks: { font: {size:10}, color: '#64748b', maxTicksLimit: 5, padding: 8 } }, x: { grid: { display: false }, ticks: { font: {size:10}, color: '#94a3b8' } } }
    }
});

// 4. Hourly Distribution
var hourlyData = <?= json_encode($hourly) ?>;
var hasHourly = hourlyData.length > 0;
if (!hasHourly) { hourlyData = [{hour: 0, count: 0}]; }
var hrLabels = hasHourly ? hourlyData.map(function(d) { var h = parseInt(d.hour); return h + ':00'; }) : ['No Data'];
var hrValues = hasHourly ? hourlyData.map(function(d) { return parseInt(d.count); }) : [0];
new Chart(document.getElementById('hourlyChart'), {
    type: 'bar',
    data: { labels: hrLabels, datasets: [{ label: 'Emails', data: hrValues, backgroundColor: function(c) { var g=c.chart.ctx.createLinearGradient(0,0,0,240); g.addColorStop(0,'rgba(6,182,212,0.7)'); g.addColorStop(0.6,'rgba(6,182,212,0.3)'); g.addColorStop(1,'rgba(6,182,212,0.05)'); return g; }, borderRadius: 4, borderSkipped: false, borderWidth: 0, barPercentage: 0.8 }] },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(15,23,42,0.9)', padding: 10, cornerRadius: 6, callbacks: { title: function(i) { return i[0].label; }, label: function(c) { return c.raw + ' email' + (c.raw !== 1 ? 's' : ''); } } } },
        scales: { y: { beginAtZero: true, grid: { color: 'rgba(42,48,64,0.3)', drawBorder: false, lineWidth: 0.5 }, ticks: { font: {size:9}, color: '#64748b', maxTicksLimit: 5, padding: 8 } }, x: { grid: { display: false }, ticks: { font: {size:9}, color: '#94a3b8', maxTicksLimit: 12, maxRotation: 45 } } }
    }
});

// 5. Department Usage (bar)
var deptData = <?= json_encode($dept_usage) ?>;
var hasDept = deptData.length > 0;
if (!hasDept) { deptData = [{name: 'No Data', email_count: 0}]; }
new Chart(document.getElementById('deptChart'), {
    type: 'bar',
    data: { labels: deptData.map(function(d) { return d.name || 'N/A'; }), datasets: [{ label: 'Emails', data: deptData.map(function(d) { return parseInt(d.email_count); }), backgroundColor: ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#06b6d4','#ef4444'], borderRadius: 4, borderSkipped: false }] },
    options: {
        indexAxis: 'y', responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(15,23,42,0.9)', padding: 8, cornerRadius: 6 } },
        scales: { x: { beginAtZero: true, grid: { color: 'rgba(42,48,64,0.3)', drawBorder: false, lineWidth: 0.5 }, ticks: { font: {size:10}, color: '#64748b' } }, y: { grid: { display: false }, ticks: { font: {size:10}, color: '#94a3b8' } } }
    }
});

// 6. SMTP Provider Usage (donut)
var providerData = <?= json_encode($provider_usage) ?>;
var provLabels = providerData.length ? providerData.map(function(d) { return d.smtp_host || d.sender_email || 'Unknown'; }) : ['No Activity Yet'];
var provValues = providerData.length ? providerData.map(function(d) { return parseInt(d.email_count); }) : [1];
var provColors = ['#0078d4', '#ea4335', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#ef4444'];
new Chart(document.getElementById('providerChart'), {
    type: 'doughnut',
    data: { labels: provLabels, datasets: [{ data: provValues, backgroundColor: provColors.slice(0, provLabels.length), borderWidth: 0 }] },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 10, font: {size:10} } }, tooltip: { backgroundColor: 'rgba(15,23,42,0.9)', padding: 8, cornerRadius: 6 } },
        cutout: '65%'
    }
});

// 7. Success Breakdown (30d)
var totals = <?= json_encode($totals) ?>;
var hasTotals = (totals.sent + totals.failed + totals.skipped) > 0;
new Chart(document.getElementById('successBreakdownChart'), {
    type: 'doughnut',
    data: { labels: hasTotals ? ['Sent', 'Failed', 'Skipped'] : ['Awaiting Data'], datasets: [{ data: hasTotals ? [totals.sent, totals.failed, totals.skipped] : [1], backgroundColor: hasTotals ? ['#10b981', '#ef4444', '#f59e0b'] : ['#2a3040'], borderWidth: 0 }] },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12, font: {size:10} } }, tooltip: { backgroundColor: 'rgba(15,23,42,0.9)', padding: 8, cornerRadius: 6 } },
        cutout: '65%'
    }
});

// 8. Blocked 7 Days
var blocked7Data = <?= json_encode($skipped_daily_volume) ?>;
var last7 = blocked7Data.slice(-7);
var hasBlocked7 = last7.length > 0;
var b7Labels = hasBlocked7 ? last7.map(function(d) { var p = d.date.split('-'); return p[1]+'/'+p[2]; }) : ['No Data'];
var b7Values = hasBlocked7 ? last7.map(function(d) { return parseInt(d.count); }) : [0];
new Chart(document.getElementById('blocked7Chart'), {
    type: 'bar',
    data: { labels: b7Labels, datasets: [{ label: 'Blocked', data: b7Values, backgroundColor: hasBlocked7 ? function(c) { var g=c.chart.ctx.createLinearGradient(0,0,0,240); g.addColorStop(0,'rgba(245,158,11,0.7)'); g.addColorStop(0.6,'rgba(245,158,11,0.3)'); g.addColorStop(1,'rgba(245,158,11,0.05)'); return g; } : 'rgba(42,48,64,0.3)', borderRadius: 6, borderSkipped: false, borderWidth: 0, barPercentage: 0.55 }] },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(15,23,42,0.9)', padding: 10, cornerRadius: 6, callbacks: { label: function(c) { return c.raw + ' request' + (c.raw !== 1 ? 's' : '') + ' blocked'; } } } },
        scales: { y: { beginAtZero: true, grid: { color: 'rgba(42,48,64,0.3)', drawBorder: false, lineWidth: 0.5 }, ticks: { font: {size:10}, color: '#64748b', maxTicksLimit: 5, padding: 8, stepSize: 1 } }, x: { grid: { display: false }, ticks: { font: {size:11, weight:'600'}, color: '#94a3b8' } } }
    }
});

// 9. Skipped Trend (30d)
var skippedData = <?= json_encode($skipped_daily_volume) ?>;
var hasSkip = skippedData.length > 0;
var skLabels = hasSkip ? skippedData.map(function(d) { var p = d.date.split('-'); return p[1]+'/'+p[2]; }) : ['No Data'];
var skValues = hasSkip ? skippedData.map(function(d) { return parseInt(d.count); }) : [0];
new Chart(document.getElementById('skippedChart'), {
    type: 'line',
    data: { labels: skLabels, datasets: [{ label: 'Skipped', data: skValues, borderColor: hasSkip ? '#f59e0b' : '#2a3040', backgroundColor: hasSkip ? function(c) { var g=c.chart.ctx.createLinearGradient(0,0,0,240); g.addColorStop(0,'rgba(245,158,11,0.2)'); g.addColorStop(0.5,'rgba(245,158,11,0.06)'); g.addColorStop(1,'rgba(245,158,11,0)'); return g; } : 'rgba(42,48,64,0.2)', fill: true, tension: 0.35, borderWidth: 2.5, pointRadius: 0, pointHoverRadius: 5, pointHoverBackgroundColor: '#fff', pointHoverBorderColor: '#f59e0b', pointHoverBorderWidth: 2.5 }] },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(15,23,42,0.9)', padding: 10, cornerRadius: 6 } },
        scales: { y: { beginAtZero: true, grid: { color: 'rgba(42,48,64,0.3)', drawBorder: false, lineWidth: 0.5 }, ticks: { font: {size:10}, color: '#64748b', maxTicksLimit: 5, padding: 8 } }, x: { grid: { display: false }, ticks: { font: {size:9}, color: '#64748b', maxTicksLimit: 10, maxRotation: 45 } } },
        interaction: { intersect: false, mode: 'nearest' }
    }
});
</script>