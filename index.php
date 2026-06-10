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

try {
    $db = Database::getInstance();

    // Auto-migrate: add theme / sidebar_state columns if missing
    $pdo = $db->getConnection();
    try {
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `theme` VARCHAR(10) DEFAULT 'dark' AFTER `last_login`");
    } catch (PDOException $e) {
        // column already exists, ignore
    }
    try {
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `sidebar_state` VARCHAR(10) DEFAULT 'collapsed' AFTER `theme`");
    } catch (PDOException $e) {
        // column already exists, ignore
    }

    // Auto-migration: add department_id column for user-department association
    try {
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `department_id` INT NULL AFTER `role`");
    } catch (PDOException $e) {
        // column already exists
    }

    // Auto-migration: add full_name column
    try {
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `full_name` VARCHAR(255) NULL AFTER `username`");
    } catch (PDOException $e) {
        // column already exists
    }

    $settings = new SystemSetting();
    $appSettings = $settings->getAllAsArray();
    date_default_timezone_set($appSettings['app_timezone'] ?? 'UTC');

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
$action = $routeParts[1] ?? 'index';

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
