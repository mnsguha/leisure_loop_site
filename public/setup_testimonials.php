<?php
require_once __DIR__ . '/../config/db.php';

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

    // 1. Create Table
    $sql = "CREATE TABLE IF NOT EXISTS testimonials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        client_name VARCHAR(255) NOT NULL,
        tour_name VARCHAR(255) NOT NULL,
        quote_text TEXT NOT NULL,
        image_url VARCHAR(500) NOT NULL,
        rotation_angle INT DEFAULT 0,
        status ENUM('active', 'inactive') DEFAULT 'active',
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql);
    echo "Table 'testimonials' created successfully.\n";

    // 2. Check if data exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM testimonials");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        // Seed default data
        $seedSql = "INSERT INTO testimonials (client_name, tour_name, quote_text, image_url, rotation_angle, sort_order) VALUES 
        ('Michael & Sarah T.', 'Bespoke Ladakh Expedition', 'An impeccably orchestrated journey. The attention to detail in Ladakh was nothing short of miraculous. We never felt like tourists, only honored guests.', 'https://images.unsplash.com/photo-1596781226767-17b01777d13f?q=80&w=600', 0, 1),
        ('Elena Rodriguez', 'Himalayan Retreat', 'From private tea estates in Darjeeling to remote monasteries, Leisure Loop curated an experience that felt utterly exclusive and authentic.', 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=600', 3, 2),
        ('David & Emma C.', 'Kashmir Honeymoon Loop', 'We rejected standard tours for our honeymoon. The secluded stays and private guides in Kashmir redefined luxury travel for us.', 'https://images.unsplash.com/photo-1627894483216-2138af692e32?q=80&w=600', -2, 3)";
        
        $pdo->exec($seedSql);
        echo "Table 'testimonials' seeded with default data.\n";
    } else {
        echo "Table already has data, skipping seed.\n";
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
