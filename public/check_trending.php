<?php
require_once __DIR__ . '/../config/db.php';
$trending_count = $pdo->query("SELECT COUNT(*) FROM packages WHERE is_active = 1 AND is_trending = 1")->fetchColumn();
$featured_count = $pdo->query("SELECT COUNT(*) FROM packages WHERE is_active = 1 AND is_featured = 1")->fetchColumn();
$total_count = $pdo->query("SELECT COUNT(*) FROM packages WHERE is_active = 1")->fetchColumn();
echo "Trending: $trending_count, Featured: $featured_count, Total: $total_count\n";
