<?php
class AnalyticsController
{
    public function index()
    {
        $auth = new Auth();
        $auth->requireAuth();

        $emailLog = new EmailLog();
        $department = new Department();
        $smtpAccount = new SmtpAccount();
        $suppression = new SuppressionCache();

        $monthly_trend = $emailLog->getMonthlyTrend(12);
        $top_departments = $department->getUsageStats();
        $top_smtp = $smtpAccount->getUsageStats();
        $failure_analysis = $emailLog->getFailureAnalysis();
        $suppression_stats = $suppression->getStats();
        $skipped_breakdown = $emailLog->getSkippedBreakdown();
        $skipped_daily_volume = $emailLog->getSkippedDailyVolume(30);

        // New chart data
        $daily_volume = $emailLog->getDailyVolume(30);
        $dept_usage = $emailLog->getTopDepartments();
        $provider_usage = $smtpAccount->getUsageStats();
        $day_of_week = $emailLog->getDayOfWeekDistribution();
        $hourly = $emailLog->getHourlyDistribution();
        $totals = $emailLog->getSentFailedSkippedTotals();
        $success_rate = $emailLog->getSuccessRate();
        $skipped_recipient_daily = $emailLog->getSkippedRecipientDailyVolume(30);

        $title = 'Analytics';
        $active_menu = 'analytics';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'analytics/index.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }
}
