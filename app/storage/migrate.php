<?php
/**
 * Database migration helper.
 * Run manually: php app/storage/migrate.php
 */

require_once __DIR__ . '/../../config/constants.php';
require_once SERVICE_PATH . 'Database.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Add theme column to users table
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN theme VARCHAR(10) DEFAULT 'dark'");
        echo "Theme column added.\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "Theme column already exists.\n";
        } else {
            throw $e;
        }
    }

    // Add sidebar_state column to users table
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN sidebar_state VARCHAR(10) DEFAULT 'collapsed'");
        echo "sidebar_state column added.\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "sidebar_state column already exists.\n";
        } else {
            throw $e;
        }
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}