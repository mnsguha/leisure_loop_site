<?php
require_once '../config/db.php';
$nav_domestic = $pdo->query("SELECT name, slug FROM destinations WHERE category = 'domestic' AND is_active = 1 ORDER BY display_order ASC, name ASC")->fetchAll();
$all_pkgs = $pdo->query("SELECT title, slug, destination FROM packages WHERE is_active = 1 ORDER BY title ASC")->fetchAll();
$grouped_packages = [];
foreach ($all_pkgs as $nav_pkg) {
    $destKey = strtolower(trim($nav_pkg['destination']));
    $grouped_packages[$destKey][] = $nav_pkg;
}

echo "Grouped Packages Keys:\n";
print_r(array_keys($grouped_packages));

echo "\nDomestic Destinations:\n";
foreach ($nav_domestic as $dest) {
    $destKey = strtolower(trim($dest['name']));
    $tours = $grouped_packages[$destKey] ?? [];
    echo "Dest: $destKey, Tours count: " . count($tours) . "\n";
}
?>
