<?php
class SettingsController
{
    public function suppression_api()
    {
        $auth = new Auth();
        $auth->requireAuth();

        $syncService = new SuppressionSyncService();
        $settings = new SystemSetting();

        // Ensure a cron key exists
        $cronKey = $settings->get('cron_key');
        if (!$cronKey) {
            $cronKey = bin2hex(random_bytes(16));
            $settings->set('cron_key', $cronKey);
        }

        // Regenerate key if requested
        if (isset($_GET['regenerate_key'])) {
            $cronKey = bin2hex(random_bytes(16));
            $settings->set('cron_key', $cronKey);
            flash('success', 'Cron key regenerated.');
            header('Location: ' . BASE_URL . 'settings/suppression_api');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'save') {
                $syncService->saveConfig(
                    $_POST['endpoint'] ?? '',
                    $_POST['api_key'] ?? '',
                    $_POST['method'] ?? 'GET'
                );
                flash('success', 'Suppression API configuration saved.');
            } elseif ($action === 'sync') {
                $result = $syncService->sync();
                if ($result['status']) {
                    flash('success', $result['message']);
                } else {
                    flash('error', $result['message']);
                }
            }
            header('Location: ' . BASE_URL . 'settings/suppression_api');
            exit;
        }

        $config = $syncService->getConfig();
        $cronUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
                 . '://' . ($_SERVER['SERVER_NAME'] ?? 'localhost')
                 . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\')
                 . '/cron/suppression_sync?key=' . urlencode($cronKey);
        $title = 'Suppression API Settings';
        $active_menu = 'settings';
        $app_name = 'SMTP Management Platform';
        $app_version = '1.0.0';

        ob_start();
        include VIEW_PATH . 'settings/suppression_api.php';
        $content = ob_get_clean();
        include VIEW_PATH . 'layouts/main.php';
    }
}
