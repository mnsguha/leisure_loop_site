<?php
require_once '../config/db.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $pdo->exec("UPDATE packages SET is_international = 0 WHERE destination IN ('Sikkim', 'Ladakh', 'Kashmir', 'Darjeeling', 'Meghalaya', 'Kalimpong')");
    $pdo->exec("UPDATE packages SET is_international = 1 WHERE destination = 'Bhutan'");

    $new_packages = [
        [
            'title' => 'Soul of Southeast Asia',
            'slug' => 'soul-of-southeast-asia-thailand',
            'destination' => 'Thailand',
            'price' => '45999',
            'original_price' => '55999',
            'image_url' => 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?q=80&w=800&auto=format&fit=crop',
            'is_active' => 1,
            'is_international' => 1,
            'days' => 5,
            'nights' => 4,
            'description_rich' => 'Experience the vibrant culture, stunning beaches, and delicious street food of Thailand.',
            'tour_code' => 'INT-TH-001',
            'tour_type' => 'Group Tour'
        ],
        [
            'title' => 'Emerald Isles & Ancient Whispers',
            'slug' => 'emerald-isles-srilanka',
            'destination' => 'Srilanka',
            'price' => '39999',
            'original_price' => '49999',
            'image_url' => 'https://images.unsplash.com/photo-1546708973-14e366119561?q=80&w=800&auto=format&fit=crop',
            'is_active' => 1,
            'is_international' => 1,
            'days' => 6,
            'nights' => 5,
            'description_rich' => 'Explore the ancient ruins, lush tea plantations, and pristine beaches of Srilanka.',
            'tour_code' => 'INT-SL-001',
            'tour_type' => 'Private Tour'
        ],
        [
            'title' => 'Bespoke Escapes from City to Sea',
            'slug' => 'bespoke-escapes-malaysia',
            'destination' => 'Malaysia',
            'price' => '42999',
            'original_price' => '52999',
            'image_url' => 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?q=80&w=800&auto=format&fit=crop',
            'is_active' => 1,
            'is_international' => 1,
            'days' => 4,
            'nights' => 3,
            'description_rich' => 'Discover the modern skyline of Kuala Lumpur and the beautiful islands of Malaysia.',
            'tour_code' => 'INT-MY-001',
            'tour_type' => 'Couples Tour'
        ],
        [
            'title' => 'Emerald of the Equator',
            'slug' => 'emerald-equator-indonesia',
            'destination' => 'Indonesia',
            'price' => '55999',
            'original_price' => '65999',
            'image_url' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?q=80&w=800&auto=format&fit=crop',
            'is_active' => 1,
            'is_international' => 1,
            'days' => 7,
            'nights' => 6,
            'description_rich' => 'Journey through the cultural heart of Bali and the stunning volcanic landscapes of Indonesia.',
            'tour_code' => 'INT-ID-001',
            'tour_type' => 'Adventure Tour'
        ]
    ];

    $stmt = $pdo->prepare("INSERT INTO packages (title, slug, destination, price, original_price, image_url, is_active, is_international, days, nights, description_rich, tour_code, tour_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $inserted = 0;
    foreach ($new_packages as $pkg) {
        $check = $pdo->prepare("SELECT COUNT(*) FROM packages WHERE slug = ?");
        $check->execute([$pkg['slug']]);
        if ($check->fetchColumn() == 0) {
            $stmt->execute([
                $pkg['title'],
                $pkg['slug'],
                $pkg['destination'],
                $pkg['price'],
                $pkg['original_price'],
                $pkg['image_url'],
                $pkg['is_active'],
                $pkg['is_international'],
                $pkg['days'],
                $pkg['nights'],
                $pkg['description_rich'],
                $pkg['tour_code'],
                $pkg['tour_type']
            ]);
            $inserted++;
            echo "Inserted {$pkg['title']}<br>";
        } else {
            echo "Skipped {$pkg['title']} (already exists)<br>";
        }
    }

    echo "Successfully inserted $inserted international packages.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
