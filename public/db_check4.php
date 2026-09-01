<?php
require_once '../config/db.php';
$slug = 'sikkim';
$stmt = $pdo->prepare("SELECT * FROM destinations WHERE slug = :slug AND is_active = 1");
$stmt->execute(['slug' => $slug]);
$destination = $stmt->fetch();

$stmt2 = $pdo->prepare("SELECT * FROM packages WHERE (destination = :name OR title LIKE :like_name) AND is_active = 1 GROUP BY title LIMIT 6");
$stmt2->execute(['name' => $destination['name'], 'like_name' => '%' . $destination['name'] . '%']);
$related_packages = $stmt2->fetchAll(PDO::FETCH_ASSOC);

echo "Destination Name: " . $destination['name'] . "\n";
echo "Related packages count: " . count($related_packages) . "\n";
foreach ($related_packages as $pkg) {
    echo $pkg['id'] . " - " . $pkg['title'] . "\n";
}
