<?php
require_once '../config/db.php';
$destinations_list = $pdo->query("SELECT d.*, (SELECT COUNT(*) FROM packages p WHERE p.destination = d.name AND p.is_active = 1) as tour_count FROM destinations d WHERE d.is_active = 1 ORDER BY d.display_order ASC, d.created_at DESC")->fetchAll();
echo "Found " . count($destinations_list) . " destinations.\n";
foreach ($destinations_list as $dest) {
    echo $dest['name'] . " - card_image: " . $dest['card_image'] . "\n";
}
?>
