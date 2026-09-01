<?php
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
    echo "Connected successfully to " . DB_NAME . "\n";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Recent Leads in Marketing Database:\n";
try {
    $stmt = $pdo->query("SELECT id, name, phone, status, created_at FROM leads ORDER BY id DESC LIMIT 5");
    while ($row = $stmt->fetch()) {
        echo "ID: {$row['id']} | Name: {$row['name']} | Phone: {$row['phone']} | Status: {$row['status']} | Created: {$row['created_at']}\n";
    }
} catch (PDOException $e) {
    echo "Error querying leads: " . $e->getMessage() . "\n";
}
?>
