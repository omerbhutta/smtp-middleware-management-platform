<?php
/**
 * CLI fallback for suppression sync cron job.
 *
 * Usage:
 *   */5 * * * * php /path/to/cron/suppression_sync.php
 *
 * The web-based cron endpoint is preferred:
 *   https://yourdomain.com/cron/suppression_sync?key=YOUR_CRON_KEY
 */

require_once __DIR__ . '/../config/constants.php';
require_once HELPER_PATH . 'functions.php';
require_once SERVICE_PATH . 'Database.php';
require_once SERVICE_PATH . 'SuppressionSyncService.php';
require_once MODEL_PATH . 'SystemSetting.php';
require_once MODEL_PATH . 'SuppressionCache.php';

$logFile = __DIR__ . '/suppression_sync.log';
$timestamp = date('Y-m-d H:i:s');

try {
    $sync = new SuppressionSyncService();
    $result = $sync->sync();

    $line = "[{$timestamp}] {$result['message']}" . PHP_EOL;
    file_put_contents($logFile, $line, FILE_APPEND);
    exit($result['status'] ? 0 : 1);
} catch (Exception $e) {
    $line = "[{$timestamp}] ERROR: " . $e->getMessage() . PHP_EOL;
    file_put_contents($logFile, $line, FILE_APPEND);
    exit(1);
}