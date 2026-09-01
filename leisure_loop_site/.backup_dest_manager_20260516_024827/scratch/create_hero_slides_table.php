<?php
require_once 'config/db.php';

try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=leisure_loop_db", "root", "");

    // 1. Create the table
    $sql = "CREATE TABLE IF NOT EXISTS hero_slides (
        id INT AUTO_INCREMENT PRIMARY KEY,
        type ENUM('image', 'video') NOT NULL,
        url VARCHAR(255) NOT NULL,
        display_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $pdo->exec($sql);
    echo "Table 'hero_slides' created or already exists.\n";

    // 2. Seed with current hero if empty
    $count = $pdo->query("SELECT COUNT(*) FROM hero_slides")->fetchColumn();
    if ($count == 0) {
        // Fetch current settings to seed
        $stmt = $pdo->query("SELECT hero_type, hero_url FROM settings WHERE id = 1");
        $curr = $stmt->fetch();
        if ($curr) {
            $type = ($curr['hero_type'] === 'video') ? 'video' : 'image';
            $url = $curr['hero_url'] ?: ($type === 'video' ? 'https://cdn.pixabay.com/video/2025/05/06/277097_large.mp4' : 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=2000');
            
            $stmt = $pdo->prepare("INSERT INTO hero_slides (type, url, display_order) VALUES (?, ?, 1)");
            $stmt->execute([$type, $url]);
            echo "Seeded with current hero content.\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
