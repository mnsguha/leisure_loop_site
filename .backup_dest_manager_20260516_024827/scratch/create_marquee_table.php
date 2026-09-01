<?php
require_once 'config/db.php';

try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=leisure_loop_db", "root", "");

    // 1. Create the table
    $sql = "CREATE TABLE IF NOT EXISTS marquee_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        image_url VARCHAR(255) NOT NULL,
        label VARCHAR(100) NOT NULL,
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $pdo->exec($sql);
    echo "Table 'marquee_items' created or already exists.\n";

    // 2. Seed with current items if empty
    $count = $pdo->query("SELECT COUNT(*) FROM marquee_items")->fetchColumn();
    
    if ($count == 0) {
        $items = [
            ['https://images.unsplash.com/photo-1581430873933-05b81a8ca93b?q=80&w=600', 'Bespoke Journeys', 1],
            ['https://images.unsplash.com/photo-1540960578613-2df67215f91e?q=80&w=600', 'Luxury Reimagined', 2],
            ['https://images.unsplash.com/photo-1597233539235-56af97458197?q=80&w=600', 'Elite Concierge', 3],
            ['https://images.unsplash.com/photo-1506461883276-594a12b11cf3?q=80&w=600', 'Exotic Escapes', 4],
            ['https://images.unsplash.com/photo-1589982840456-a2281881b28b?q=80&w=600', 'Unforgettable Memories', 5]
        ];

        $stmt = $pdo->prepare("INSERT INTO marquee_items (image_url, label, display_order) VALUES (?, ?, ?)");
        foreach ($items as $item) {
            $stmt->execute($item);
        }
        echo "Table seeded with default items.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
