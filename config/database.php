<?php
/**
 * Database Configuration
 *
 * Reads from:
 * 1. $_ENV (populated from .env by index.php / api/send.php)
 * 2. Legacy config/database.local.php (upgrade compatibility)
 * 3. Default fallback
 */

if (!empty($_ENV['DB_HOST'])) {
    return [
        'host'     => $_ENV['DB_HOST'],
        'port'     => $_ENV['DB_PORT'] ?? '3306',
        'username' => $_ENV['DB_USERNAME'],
        'password' => $_ENV['DB_PASSWORD'] ?? '',
        'database' => $_ENV['DB_DATABASE'],
        'charset'  => 'utf8mb4',
        'timezone' => $_ENV['APP_TIMEZONE'] ?? 'America/Los_Angeles',
    ];
}

$legacyFile = __DIR__ . '/database.local.php';
if (file_exists($legacyFile)) {
    return require $legacyFile;
}

return [
    'host'     => 'localhost',
    'port'     => '3306',
    'username' => '',
    'password' => '',
    'database' => 'smmp_smtp',
    'charset'  => 'utf8mb4',
    'timezone' => 'America/Los_Angeles',
];