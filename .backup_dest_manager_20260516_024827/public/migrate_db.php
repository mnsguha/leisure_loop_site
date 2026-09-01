<?php
require_once '../config/db.php';

if (!$pdo) die("DB connection failed");

try {
    // 1. Add destination column to packages if it doesn't exist
    $cols = $pdo->query("SHOW COLUMNS FROM packages LIKE 'destination'")->fetchAll();
    if (empty($cols)) {
        $pdo->exec("ALTER TABLE packages ADD COLUMN destination VARCHAR(255) DEFAULT NULL AFTER title");
        echo "<p>✅ Column 'destination' added to packages table.</p>";
    } else {
        echo "<p>ℹ️ Column 'destination' already exists.</p>";
    }

    // 2. Ensure destinations table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS destinations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        tagline VARCHAR(255),
        description_long TEXT,
        sightseeing_json JSON,
        cover_image VARCHAR(255),
        category ENUM('domestic','international') DEFAULT 'domestic',
        is_active TINYINT(1) DEFAULT 1,
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $count = $pdo->query("SELECT COUNT(*) FROM destinations")->fetchColumn();
    if ($count == 0) {
        $stmt = $pdo->prepare("INSERT INTO destinations (name,slug,tagline,category,cover_image,description_long,sightseeing_json,display_order) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute(['Sikkim','sikkim','Himalayan Majesty','domestic','https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?q=80&w=2000','Nestled in the lap of the Himalayas, Sikkim is a sanctuary of serene lakes, ancient monasteries, and the towering Kanchenjunga. Experience a culture as deep as its valleys and as pure as its mountain air.',json_encode([['title'=>'Tsomgo Lake','desc'=>'A sacred alpine lake that changes colors with the seasons.','image'=>'https://images.unsplash.com/photo-1589982840456-a2281881b28b?q=80&w=800'],['title'=>'Rumtek Monastery','desc'=>'The seat of the Karmapa and a masterpiece of Tibetan architecture.','image'=>'https://images.unsplash.com/photo-1570731648057-074479998242?q=80&w=800'],['title'=>'Nathula Pass','desc'=>'The historic Silk Road pass on the Indo-China border.','image'=>'https://images.unsplash.com/photo-1544216717-3bbf52512659?q=80&w=800']]),1]);
        $stmt->execute(['Ladakh','ladakh','The Land of High Passes','domestic','https://images.unsplash.com/photo-1589982840456-a2281881b28b?q=80&w=2000','A stark, moon-like landscape punctuated by vibrant blue lakes and spiritual silence. Ladakh is where the earth meets the heavens in an eternal embrace of light and shadow.',json_encode([['title'=>'Pangong Lake','desc'=>"The world's highest saltwater lake, extending from India to Tibet.",'image'=>'https://images.unsplash.com/photo-1590050752117-238cb0fb12b1?q=80&w=800'],['title'=>'Nubra Valley','desc'=>'The high-altitude cold desert with double-humped camels.','image'=>'https://images.unsplash.com/photo-1581430873933-05b81a8ca93b?q=80&w=800'],['title'=>'Leh Palace','desc'=>'A former royal palace overlooking the Ladakhi town of Leh.','image'=>'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?q=80&w=800']]),2]);
        $stmt->execute(['Kashmir','kashmir','Paradise on Earth','domestic','https://images.unsplash.com/photo-1540960578613-2df67215f91e?q=80&w=2000','From the shikaras of Dal Lake to the snow-covered slopes of Gulmarg, Kashmir is a dream painted in the colors of saffron and emerald.',json_encode([['title'=>'Dal Lake','desc'=>'The jewel in the crown of Kashmir, famous for its houseboats.','image'=>'https://images.unsplash.com/photo-1598325178942-882069f53e20?q=80&w=800'],['title'=>'Gulmarg','desc'=>'The meadow of flowers and a world-class skiing destination.','image'=>'https://images.unsplash.com/photo-1597233539235-56af97458197?q=80&w=800'],['title'=>'Pahalgam','desc'=>'A serene valley known for its lush meadows and pristine waters.','image'=>'https://images.unsplash.com/photo-1506461883276-594a12b11cf3?q=80&w=800']]),3]);
        echo "<p>✅ Destinations table seeded with 3 destinations.</p>";
    } else {
        echo "<p>ℹ️ Destinations table already has $count rows.</p>";
    }

    echo "<p><strong>✅ Migration complete! <a href='index.php'>Go to site</a> | <a href='admin/packages.php'>Go to Admin Packages</a></strong></p>";
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
