<?php
class CronController
{
    public function suppression_sync()
    {
        $settings = new SystemSetting();
        $expectedKey = $settings->get('cron_key');

        if (!$expectedKey) {
            $expectedKey = bin2hex(random_bytes(16));
            $settings->set('cron_key', $expectedKey);
        }

        $providedKey = $_GET['key'] ?? '';
        if ($providedKey !== $expectedKey) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Invalid or missing cron key']);
            exit;
        }

        $sync = new SuppressionSyncService();
        $result = $sync->sync();

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
}
