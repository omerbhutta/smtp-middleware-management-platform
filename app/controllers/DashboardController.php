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

        $stats = [
            'today_count'        => $emailLog->getTodayCount(),
            'week_count'         => $emailLog->getWeekCount(),
            'month_count'        => $emailLog->getMonthCount(),
            'failed_count'       => $emailLog->getFailedCount(),
            'active_departments' => $department->countActive(),
            'active_keys'        => $securityKey->countActive(),
            'active_smtp'        => $smtpAccount->countActive(),
            'suppressed_count'   => $suppression->count(),
            'skipped_count'      => $emailLog->getSkippedCount(),
            'skipped_breakdown'  => $emailLog->getSkippedBreakdown(),
        ];

        $daily_volume = $emailLog->getDailyVolume(30);
        $skipped_daily_volume = $emailLog->getSkippedDailyVolume(30);
        $skipped_recipient_daily = $emailLog->getSkippedRecipientDailyVolume(30);
        $top_departments = $emailLog->getTopDepartments();
        $provider_usage = $smtpAccount->getUsageStats();
        $week_pct = $emailLog->getWeeklyPercentChange();
        $month_pct = $emailLog->getMonthlyPercentChange();
        $failed_pct = $emailLog->getFailedPercentChange();
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
