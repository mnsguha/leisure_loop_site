<?php
require_once __DIR__ . '/../config/db.php';
header('Content-Type: text/plain');

if (!$pdo) {
    die("Database connection failed.\n");
}

$tour_code = "VD-7908";
$tour_type = "Honeymoon, Alpine Lake, Adventure";
$original_price = 25000.00;
$rating_score = 4.9;
$rating_count = 340;
$highlights = "Breathtaking Gurudongmar Lake Curation\nPremium Wooden Alpine Cottages in Lachen\nPrivate 4x4 Luxury SUV Logistics Support\nElite Local Guide Access with Permit Priority";
$description_rich = "Destinations: Gangtok, Lachen, Lachung, Yumthang Valley, Gurudongmar\nPickup & Drop: Bagdogra Airport (IXB) / NJP Railway Station\nAccommodation: 5 Nights Premium Curation Alpine Stays";
$photos = "https://images.unsplash.com/photo-1605649487212-47bdab064df7?q=80&w=800, https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=800, https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=800";

try {
    $stmt = $pdo->prepare("UPDATE packages SET 
        tour_code = ?, 
        tour_type = ?, 
        original_price = ?, 
        rating_score = ?, 
        rating_count = ?, 
        highlights = ?, 
        description_rich = ?, 
        photos = ? 
        WHERE slug = 'north-sikkim-frozen-lake'");
        
    $stmt->execute([
        $tour_code,
        $tour_type,
        $original_price,
        $rating_score,
        $rating_count,
        $highlights,
        $description_rich,
        $photos
    ]);
    
    echo "SUCCESSFULLY populated North Sikkim package with premium Obsidian/Gold metadata!\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

unlink(__FILE__);
?>
