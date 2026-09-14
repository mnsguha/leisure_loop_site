<?php
$_GET['view'] = 'mobile';
require 'g:/Antigravity/leisure_loop_site/config/db.php';

$stmt_mob = $pdo->prepare("
    SELECT id, title, slug, destination, tour_type, days, nights, price, original_price, 
           card_image, cover_image, is_featured, is_trending
    FROM packages 
    WHERE is_active = 1 
    ORDER BY is_featured DESC, created_at DESC
");
$stmt_mob->execute();
$packages_mobile = $stmt_mob->fetchAll(PDO::FETCH_ASSOC);

echo "Total mobile packages: " . count($packages_mobile) . "\n";

$trending_mobile = array_filter($packages_mobile, fn($p) => !empty($p['is_trending']) || !empty($p['is_featured']));
echo "Trending packages: " . count($trending_mobile) . "\n";

$offer_packages = array_filter($packages_mobile ?? [], fn($p) => !empty($p['original_price']) && floatval($p['original_price']) > floatval($p['price']));
if (empty($offer_packages)) $offer_packages = array_slice($packages_mobile ?? [], 0, 5);
echo "Offer packages: " . count($offer_packages) . "\n";
