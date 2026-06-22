<?php
class DashboardController
{
    public function index()
    {
        $auth = new Auth();
        $auth->requireAuth();

        $emailLog = new EmailLog();
        $department = new Department();
        $securityKey = new SecurityKey();
        $smtpAccount = new SmtpAccount();
        $suppression = new SuppressionCache();

        $dashboardCounts = $emailLog->getDashboardCounts();
        $stats = $dashboardCounts + [
            'active_departments' => $department->countActive(),
            'active_keys'        => $securityKey->countActive(),
            'active_smtp'        => $smtpAccount->countActive(),
            'suppressed_count'   => $suppression->count(),
            'skipped_breakdown'  => $emailLog->getSkippedBreakdown(),
        ];

        $pct = $emailLog->getDashboardPercentChanges();
        $daily_volume = $emailLog->getDailyVolume(30);
        $skipped_daily_volume = $emailLog->getSkippedDailyVolume(30);
        $skipped_recipient_daily = $emailLog->getSkippedRecipientDailyVolume(30);
        $top_departments = $emailLog->getTopDepartments();
        $provider_usage = $smtpAccount->getUsageStats();
        $week_pct = $pct['week_pct'];
        $month_pct = $pct['month_pct'];
        $failed_pct = $pct['failed_pct'];
        $skipped30 = $emailLog->getSkippedRecipientCount(30);
        $recent_activities = $emailLog->getAll([], 1, 10)['data'];

        $title = 'Dashboard';
        $active_menu = 'dashboard';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'dashboard/index.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }
}
