<?php
define('INSTALL_MODE', true);
session_start();

require_once __DIR__ . '/../config/constants.php';
require_once HELPER_PATH . 'functions.php';

$step = $_POST['step'] ?? $_GET['step'] ?? 1;
$error = null;
$success = null;
$app_name = 'SMTP Management Platform';

define('DEFAULT_FORBIDDEN_EMAILS', json_encode([]));

// Determine the app's base URL (installer is at /install/ or /mailer/install/)
$installDir = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = rtrim(dirname($installDir), '/\\');

function checkRequirements()
{
    return [
        'PHP Version >= 8.0' => version_compare(PHP_VERSION, '8.0.0', '>='),
        'PDO Extension'       => extension_loaded('pdo'),
        'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
        'cURL Extension'      => extension_loaded('curl'),
        'JSON Extension'      => extension_loaded('json'),
        'MB String Extension' => extension_loaded('mbstring'),
        'OpenSSL Extension'   => extension_loaded('openssl'),
    ];
}

function testDbConnection($host, $port, $user, $pass, $dbname)
{
    try {
        $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$dbname}`");
        return ['success' => true, 'pdo' => $pdo];
    } catch (PDOException $e) {
        $msg = $e->getMessage();
        if (str_contains($msg, 'Connection refused')) {
            $msg = "Connection refused. Verify MySQL is running and the host:port is correct.";
        } elseif (str_contains($msg, 'Access denied')) {
            $msg = "Access denied. Check username and password.";
        } elseif (str_contains($msg, 'Unknown database')) {
            $msg = "Database '{$dbname}' does not exist and could not be created. Check permissions.";
        }
        return ['success' => false, 'error' => $msg];
    }
}

function runSchema($pdo)
{
    $sql = "
    CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(100) NOT NULL UNIQUE,
        `email` VARCHAR(255) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `mfa_enabled` TINYINT(1) DEFAULT 1,
        `role` ENUM('admin','manager') DEFAULT 'admin',
        `status` ENUM('active','inactive') DEFAULT 'active',
        `last_login` DATETIME NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `departments` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `description` TEXT NULL,
        `status` ENUM('active','inactive') DEFAULT 'active',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `security_keys` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `department_id` INT NOT NULL,
        `api_key` VARCHAR(64) NOT NULL UNIQUE,
        `secret_key` VARCHAR(64) NOT NULL,
        `last_usage` DATETIME NULL,
        `usage_count` INT DEFAULT 0,
        `status` ENUM('active','inactive') DEFAULT 'active',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `smtp_accounts` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `department_id` INT NULL,
        `provider_type` ENUM('microsoft365','gmail','custom') NOT NULL,
        `smtp_host` VARCHAR(255) NOT NULL,
        `smtp_port` INT NOT NULL,
        `smtp_username` VARCHAR(255) NOT NULL,
        `smtp_password` VARCHAR(255) NOT NULL,
        `encryption` ENUM('tls','ssl','none') DEFAULT 'tls',
        `sender_email` VARCHAR(255) NOT NULL,
        `sender_name` VARCHAR(255) NULL,
        `is_portal_smtp` TINYINT(1) DEFAULT 0,
        `status` ENUM('active','inactive') DEFAULT 'active',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `email_logs` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `department_id` INT NULL,
        `security_key_id` INT NULL,
        `smtp_account_id` INT NULL,
        `sender_email` VARCHAR(255) NULL,
        `recipients` TEXT NULL,
        `recipient_count` INT DEFAULT 0,
        `subject` VARCHAR(255) NULL,
        `source_ip` VARCHAR(45) NULL,
        `request_path` VARCHAR(255) NULL,
        `status` ENUM('sent','failed') DEFAULT 'sent',
        `error_message` TEXT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE SET NULL,
        FOREIGN KEY (`security_key_id`) REFERENCES `security_keys`(`id`) ON DELETE SET NULL,
        FOREIGN KEY (`smtp_account_id`) REFERENCES `smtp_accounts`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `system_settings` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `setting_key` VARCHAR(100) NOT NULL UNIQUE,
        `setting_value` TEXT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `audit_logs` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NULL,
        `action` VARCHAR(255) NOT NULL,
        `details` TEXT NULL,
        `ip_address` VARCHAR(45) NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `login_logs` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NULL,
        `ip_address` VARCHAR(45) NULL,
        `status` ENUM('success','failed') DEFAULT 'failed',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `suppression_cache` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `email` VARCHAR(255) NOT NULL,
        `reason` VARCHAR(255) NULL,
        `source` VARCHAR(100) DEFAULT 'manual',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `unique_email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `mfa_codes` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `code` VARCHAR(6) NOT NULL,
        `expires_at` DATETIME NOT NULL,
        `used` TINYINT(1) DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement . ';');
        }
    }
}

// AJAX connection test handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'test') {
    header('Content-Type: application/json');
    $result = testDbConnection(
        $_POST['db_host'] ?? 'localhost',
        $_POST['db_port'] ?? '3306',
        $_POST['db_user'] ?? 'root',
        $_POST['db_pass'] ?? '',
        $_POST['db_name'] ?? 'smmp_smtp'
    );
    echo json_encode([
        'status' => $result['success'],
        'message' => $result['success'] ? 'Connection successful!' : $result['error'],
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 1) {
        $dbHost = $_POST['db_host'] ?? 'localhost';
        $dbPort = $_POST['db_port'] ?? '3306';
        $dbUser = $_POST['db_user'] ?? 'root';
        $dbPass = $_POST['db_pass'] ?? '';
        $dbName = $_POST['db_name'] ?? 'smmp_smtp';
        $result = testDbConnection($dbHost, $dbPort, $dbUser, $dbPass, $dbName);
        if ($result['success']) {
            $_SESSION['install_db'] = ['host' => $dbHost, 'port' => $dbPort, 'user' => $dbUser, 'pass' => $dbPass, 'name' => $dbName];
            runSchema($result['pdo']);
            $success = 'Database connection successful and tables created!';
            $step = 2;
        } else {
            $error = 'Database connection failed: ' . $result['error'];
        }
    } elseif ($step == 2) {
        $username = $_POST['username'] ?? 'admin';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $appName = $_POST['app_name'] ?? 'SMTP Management Platform';
        $timezone = $_POST['timezone'] ?? 'UTC';

        if (empty($email) || empty($password)) {
            $error = 'Please fill in all required fields.';
        } else {
            $dbConfig = $_SESSION['install_db'];
            try {
                $pdo = new PDO("mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['name']};charset=utf8mb4", $dbConfig['user'], $dbConfig['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, mfa_enabled, role, status) VALUES (?, ?, ?, 1, 'admin', 'active')");
                $stmt->execute([$username, $email, $hashedPassword]);

                $stmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES ('app_name', ?), ('app_timezone', ?), ('forbidden_emails', ?), ('installer_locked', '1')");
                $stmt->execute([$appName, $timezone, DEFAULT_FORBIDDEN_EMAILS]);

                // Write .env file
                $envContent = "# Database Configuration\n"
                    . "DB_HOST={$dbConfig['host']}\n"
                    . "DB_PORT={$dbConfig['port']}\n"
                    . "DB_DATABASE={$dbConfig['name']}\n"
                    . "DB_USERNAME={$dbConfig['user']}\n"
                    . "DB_PASSWORD={$dbConfig['pass']}\n"
                    . "\n# Application Settings\n"
                    . "APP_NAME={$appName}\n"
                    . "APP_TIMEZONE={$timezone}\n"
                    . "APP_DEBUG=false\n"
                    . "\n# Session\n"
                    . "SESSION_NAME=SMMP_SESSION\n";
                file_put_contents(BASE_PATH . '.env', $envContent);

                file_put_contents(INSTALLER_LOCK_FILE, date('Y-m-d H:i:s'));

                $_SESSION['install_db'] = null;
                $success = 'Installation complete! Redirecting to login...';
                echo "<script>setTimeout(function(){ window.location.href='{$baseUrl}/auth/login'; }, 2000);</script>";
                $step = 3;
            } catch (Exception $e) {
                $error = 'Installation error: ' . $e->getMessage();
            }
        }
    }
}

$checks = checkRequirements();
$allPassed = !in_array(false, $checks, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install - <?= escape($app_name) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .install-card { background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); padding: 2.5rem; width: 100%; max-width: 700px; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 0.75rem; font-weight: 600; }
        .btn-primary:hover { background: linear-gradient(135deg, #5a6fd6 0%, #6a4192 100%); }
        .step-indicator { display: flex; justify-content: center; gap: 2rem; margin-bottom: 2rem; }
        .step-item { text-align: center; color: #aaa; }
        .step-item.active { color: #667eea; font-weight: bold; }
        .step-item i { font-size: 1.5rem; display: block; margin-bottom: 0.25rem; }
    </style>
</head>
<body>
    <div class="install-card">
        <div class="text-center mb-4">
            <i class="fas fa-envelope-circle-check" style="font-size:3rem;color:#667eea"></i>
            <h3><?= escape($app_name) ?> Installation</h3>
        </div>

        <div class="step-indicator">
            <div class="step-item <?= $step >= 1 ? 'active' : '' ?>"><i class="fas fa-server"></i>Database</div>
            <div class="step-item <?= $step >= 2 ? 'active' : '' ?>"><i class="fas fa-user-cog"></i>Admin</div>
            <div class="step-item <?= $step >= 3 ? 'active' : '' ?>"><i class="fas fa-check-circle"></i>Done</div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= escape($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= escape($success) ?></div>
        <?php endif; ?>

        <?php if ($step == 1): ?>
            <h5>System Requirements</h5>
            <table class="table table-sm mb-4">
                <?php foreach ($checks as $check => $passed): ?>
                <tr>
                    <td><?= escape($check) ?></td>
                    <td class="text-end"><?= $passed ? '<span class="badge bg-success">Passed</span>' : '<span class="badge bg-danger">Failed</span>' ?></td>
                </tr>
                <?php endforeach; ?>
            </table>

            <?php if ($allPassed): ?>
            <h5>Database Configuration</h5>
            <form method="POST" id="dbForm">
                <input type="hidden" name="step" value="1">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Database Host</label>
                        <input type="text" name="db_host" class="form-control" value="<?= escape(old('db_host', 'localhost')) ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Port</label>
                        <input type="number" name="db_port" class="form-control" value="<?= escape(old('db_port', '3306')) ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Database Name</label>
                    <input type="text" name="db_name" class="form-control" value="<?= escape(old('db_name', 'smmp_smtp')) ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="db_user" class="form-control" value="<?= escape(old('db_user', 'root')) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="db_pass" class="form-control" id="dbPass" value="<?= escape(old('db_pass')) ?>" placeholder="Database password">
                        <div class="form-check mt-1">
                            <input type="checkbox" class="form-check-input" id="showDbPass" onchange="document.getElementById('dbPass').type=this.checked?'text':'password'">
                            <label class="form-check-label" for="showDbPass">Show</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary w-50" id="testBtn" onclick="testConnection()">
                        <i class="fas fa-plug"></i> Test Connection
                    </button>
                    <button type="submit" class="btn btn-primary w-50" id="installBtn">
                        <i class="fas fa-server"></i> Install Database
                    </button>
                </div>
                <div id="testResult" class="mt-3"></div>
            </form>

            <script>
            function testConnection() {
                var btn = document.getElementById('testBtn');
                var result = document.getElementById('testResult');
                var host = document.querySelector('input[name="db_host"]').value;
                var port = document.querySelector('input[name="db_port"]').value;
                var user = document.querySelector('input[name="db_user"]').value;
                var pass = document.querySelector('input[name="db_pass"]').value;
                var dbname = document.querySelector('input[name="db_name"]').value;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
                result.innerHTML = '<div class="alert alert-info mb-0">Connecting...</div>';
                var formData = new FormData();
                formData.append('action', 'test');
                formData.append('db_host', host);
                formData.append('db_port', port);
                formData.append('db_user', user);
                formData.append('db_pass', pass);
                formData.append('db_name', dbname);
                fetch('', { method: 'POST', body: formData })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.status) {
                        result.innerHTML = '<div class="alert alert-success mb-0"><i class="fas fa-check-circle"></i> ' + data.message + '</div>';
                    } else {
                        result.innerHTML = '<div class="alert alert-danger mb-0"><i class="fas fa-exclamation-circle"></i> ' + data.message + '</div>';
                    }
                })
                .catch(function() {
                    result.innerHTML = '<div class="alert alert-danger mb-0">Request failed - check browser console</div>';
                })
                .finally(function() {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-plug"></i> Test Connection';
                });
            }
            </script>
            <?php endif; ?>

        <?php elseif ($step == 2): ?>
            <h5>Create Admin User</h5>
            <form method="POST">
                <input type="hidden" name="step" value="2">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="admin" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="admin@example.com" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Application Name</label>
                        <input type="text" name="app_name" class="form-control" value="SMTP Management Platform">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Timezone</label>
                        <select name="timezone" class="form-control">
                            <option value="UTC">UTC</option>
                            <option value="America/New_York">America/New_York</option>
                            <option value="America/Chicago">America/Chicago</option>
                            <option value="America/Denver">America/Denver</option>
                            <option value="America/Los_Angeles">America/Los_Angeles</option>
                            <option value="Asia/Karachi" selected>Asia/Karachi</option>
                            <option value="Asia/Dubai">Asia/Dubai</option>
                            <option value="Asia/Kolkata">Asia/Kolkata</option>
                            <option value="Asia/Singapore">Asia/Singapore</option>
                            <option value="Europe/London">Europe/London</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Complete Installation</button>
            </form>

        <?php elseif ($step == 3): ?>
            <div class="text-center">
                <i class="fas fa-check-circle text-success" style="font-size:4rem"></i>
                <h4 class="mt-3">Installation Complete!</h4>
                <p>Redirecting to login page...</p>
                <a href="<?= $baseUrl ?>/auth/login" class="btn btn-primary">Go to Login</a>
            </div>

        <?php endif; ?>
    </div>
</body>
</html>
