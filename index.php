<?php
// Load .env file if it exists
$envFile = __DIR__ . '/.env';
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

session_start();

require_once __DIR__ . '/config/constants.php';
require_once HELPER_PATH . 'functions.php';

spl_autoload_register(function ($class) {
    $paths = [
        CONTROLLER_PATH,
        MODEL_PATH,
        SERVICE_PATH,
    ];
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

if (!file_exists(INSTALLER_LOCK_FILE)) {
    if (!isset($_GET['route']) || strpos($_GET['route'], 'install') !== 0) {
        header('Location: install/index.php');
        exit;
    }
    require_once INSTALL_PATH . 'index.php';
    exit;
}

// Maintenance mode check
$maintenanceFile = STORAGE_PATH . '.maintenance';
if (file_exists($maintenanceFile)) {
    $isUpdateRoute = isset($_GET['route']) && strpos($_GET['route'], 'self_update') === 0;
    if (!$isUpdateRoute) {
        $maintenanceMsg = @file_get_contents($maintenanceFile) ?: 'The system is currently undergoing maintenance. Please check back shortly.';
        http_response_code(503);
        ?>
        <!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Maintenance - SMMP</title>
        <style>body{background:#0f172a;color:#e2e8f0;font-family:system-ui,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;text-align:center;}
        .maint-box{max-width:480px;padding:40px;}
        .maint-icon{font-size:3rem;margin-bottom:16px;display:block;}
        h1{font-size:1.5rem;margin:0 0 8px;font-weight:600;}
        p{color:#94a3b8;font-size:0.9rem;line-height:1.5;margin:0;}</style></head>
        <body><div class="maint-box"><span class="maint-icon">&#9881;</span><h1>Under Maintenance</h1><p><?= htmlspecialchars($maintenanceMsg, ENT_QUOTES) ?></p></div></body></html>
        <?php
        exit;
    }
}

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    $migrationFile = STORAGE_PATH . '.migrations_applied';
    if (!file_exists($migrationFile)) {
        $migrations = [
            "ALTER TABLE `users` ADD COLUMN `theme` VARCHAR(10) DEFAULT 'dark' AFTER `last_login`",
            "ALTER TABLE `users` ADD COLUMN `sidebar_state` VARCHAR(10) DEFAULT 'collapsed' AFTER `theme`",
            "ALTER TABLE `users` ADD COLUMN `department_id` INT NULL AFTER `role`",
            "ALTER TABLE `users` ADD COLUMN `full_name` VARCHAR(255) NULL AFTER `username`",
            "ALTER TABLE `email_logs` ADD COLUMN `total_recipients` INT NULL DEFAULT NULL AFTER `recipient_count`",
            "ALTER TABLE `users` ADD COLUMN `ms_id` VARCHAR(255) NULL DEFAULT NULL AFTER `id`",
            "UPDATE `users` SET `role` = 'admin' WHERE `role` NOT IN ('admin','user')",
            "ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin','user') NOT NULL DEFAULT 'admin'",
            "CREATE TABLE IF NOT EXISTS `deploy_logs` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT NULL,
                `action` VARCHAR(100) NOT NULL,
                `branch` VARCHAR(255) NULL,
                `previous_commit` VARCHAR(255) NULL,
                `output` LONGTEXT NULL,
                `status` VARCHAR(50) NOT NULL DEFAULT 'running',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        ];
        foreach ($migrations as $sql) {
            try { $pdo->exec($sql); } catch (PDOException $e) {}
        }
        @file_put_contents($migrationFile, date('Y-m-d H:i:s'));
    }

    $migrationFile2 = STORAGE_PATH . '.migrations_applied_v2';
    if (!file_exists($migrationFile2)) {
        $migrationsV2 = [
            "ALTER TABLE `email_logs` ADD COLUMN `cc` TEXT NULL AFTER `recipients`",
            "ALTER TABLE `email_logs` ADD COLUMN `bcc` TEXT NULL AFTER `cc`",
            "ALTER TABLE `email_logs` ADD COLUMN `has_attachment` TINYINT(1) NOT NULL DEFAULT 0 AFTER `bcc`",
        ];
        foreach ($migrationsV2 as $sql) {
            try { $pdo->exec($sql); } catch (PDOException $e) {}
        }
        @file_put_contents($migrationFile2, date('Y-m-d H:i:s'));
    }

    $migrationFile3 = STORAGE_PATH . '.migrations_applied_v3';
    if (!file_exists($migrationFile3)) {
        $migrationsV3 = [
            "ALTER TABLE `email_logs` ADD COLUMN `priority` TINYINT DEFAULT NULL AFTER `has_attachment`",
            "ALTER TABLE `email_logs` ADD COLUMN `reply_to` VARCHAR(255) DEFAULT NULL AFTER `priority`",
            "ALTER TABLE `email_logs` ADD COLUMN `attachment_count` INT NOT NULL DEFAULT 0 AFTER `reply_to`",
        ];
        foreach ($migrationsV3 as $sql) {
            try { $pdo->exec($sql); } catch (PDOException $e) {}
        }
        @file_put_contents($migrationFile3, date('Y-m-d H:i:s'));
    }

    $settings = new SystemSetting();
    $appSettings = $settings->getAllAsArray();
    $tz = $_ENV['APP_TIMEZONE'] ?? ($appSettings['app_timezone'] ?? 'UTC');
    date_default_timezone_set($tz);

    // Restore user preferences from DB if missing from session (fresh login / new device)
    if (!empty($_SESSION['logged_in']) && empty($_SESSION['theme'])) {
        $userRow = $db->fetchOne("SELECT theme, sidebar_state, role, department_id FROM users WHERE id = :id", ['id' => $_SESSION['user_id'] ?? 0]);
        if ($userRow) {
            $_SESSION['theme']         = $userRow['theme'] ?? 'dark';
            $_SESSION['sidebar_state'] = $userRow['sidebar_state'] ?? 'collapsed';
            $_SESSION['role']          = $userRow['role'] ?? 'super_admin';
            $_SESSION['department_id'] = $userRow['department_id'];
        }
    }
} catch (Exception $e) {
    if (!isset($_GET['route']) || strpos($_GET['route'], 'install') !== 0) {
        header('Location: install/index.php');
        exit;
    }
}

$app_name    = $appSettings['app_name'] ?? 'SMTP Management Platform';
$app_version = '1.0.0';

// Compute base URL for absolute paths
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
define('BASE_URL', rtrim($scriptDir ?: '/', '/') . '/');

$route = $_GET['route'] ?? 'dashboard';
$routeParts = explode('/', $route);

$controllerRoute = $routeParts[0];
if (isset($routeParts[1])) {
    $action = $routeParts[1];
    for ($i = 2; $i < count($routeParts); $i++) {
        $action .= ucfirst($routeParts[$i]);
    }
} else {
    $action = 'index';
}

$controllerName = implode('', array_map('ucfirst', explode('_', $controllerRoute))) . 'Controller';

$controllerFile = CONTROLLER_PATH . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName();
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        http_response_code(404);
        echo "404 - Action not found: {$action}";
    }
} else {
    http_response_code(404);
    echo "404 - Controller not found: {$controllerName}";
}
