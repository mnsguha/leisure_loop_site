<?php
/**
 * Database Configuration
 * Local: leisure_loop_db (XAMPP)
 * Production: Update credentials for Hostinger
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'leisure_loop_db');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

    // Load site settings
    $stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
    $settings = $stmt->fetch() ?: [];

} catch (PDOException $e) {
    $pdo = null;
    $settings = [];
}
?>
