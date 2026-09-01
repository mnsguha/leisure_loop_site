<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'leisure_loop_db');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM destinations LIKE 'narrative_watermark'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE destinations ADD COLUMN narrative_watermark VARCHAR(255) DEFAULT NULL AFTER story_narrative_image_3");
        echo "Column narrative_watermark added successfully.";
    } else {
        echo "Column already exists.";
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
