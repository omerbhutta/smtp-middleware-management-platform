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

// Load .env
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $_ENV[trim($parts[0])] = trim(trim($parts[1]), '"\'');
        }
    }
}

require_once __DIR__ . '/../config/constants.php';
require_once HELPER_PATH . 'functions.php';
require_once SERVICE_PATH . 'Database.php';
require_once SERVICE_PATH . 'SuppressionSyncService.php';
require_once MODEL_PATH . 'SystemSetting.php';
require_once MODEL_PATH . 'SuppressionCache.php';

if (!empty($_ENV['APP_TIMEZONE'])) {
    date_default_timezone_set($_ENV['APP_TIMEZONE']);
}

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