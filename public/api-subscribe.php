<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ll_json_response('error', 'METHOD_NOT_ALLOWED', 'Invalid request method');
}

// Anti-bot guard: honeypot -> CSRF -> time-trap -> rate limits.
lead_guard_json($_POST);

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    ll_json_response('error', 'VALIDATION_ERROR', 'Please enter a valid email address');
}

try {
    // Create the table on first use — driver-aware DDL (MySQL vs SQLite).
    if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') {
        $pdo->exec("CREATE TABLE IF NOT EXISTS subscribers (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            subscribed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    } else {
        $pdo->exec("CREATE TABLE IF NOT EXISTS subscribers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE NOT NULL,
            subscribed_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
    }

    $stmt = $pdo->prepare("INSERT INTO subscribers (email) VALUES (?)");
    $stmt->execute([$email]);
    ll_json_response('success', 'OK', 'Welcome to the Inner Circle!');
} catch (PDOException $e) {
    if ($e->getCode() == 23000 || $e->getCode() == 19) { // Integrity constraint violation (UNIQUE)
        ll_json_response('success', 'OK', 'You are already subscribed to the Inner Circle.');
    } else {
        error_log("Subscribe Error: " . $e->getMessage());
        ll_json_response('error', 'INTERNAL_ERROR', 'An error occurred. Please try again later.');
    }
}
