<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'leisure_loop_db');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

$stmt = $pdo->query("SELECT slug, parallax_layers_json FROM destinations WHERE slug IN ('sikkim', 'darjeeling')");
$dests = [];
while ($row = $stmt->fetch()) {
    $dests[$row['slug']] = json_decode($row['parallax_layers_json'], true);
}

// Fix Sikkim: Remove custom_cloud_1, 2, 3 completely
$new_sikkim = [];
foreach ($dests['sikkim'] as $layer) {
    if (strpos($layer['image'], 'custom_cloud') === false) {
        $new_sikkim[] = $layer;
    }
}

// Fix Darjeeling: If it has custom_cloud_*, change height: 120% to height: auto; and adjust positioning
$new_darj = [];
foreach ($dests['darjeeling'] as $layer) {
    if (strpos($layer['image'], 'custom_cloud') !== false) {
        $layer['style'] = str_replace(['height: 120%;', 'inset: -10%;'], ['height: auto;', 'left: -10%; bottom: -5%;'], $layer['style']);
        $layer['style'] = preg_replace('/bottom:\s*-[0-9]+%;/', 'bottom: 5%;', $layer['style']);
    }
    if (strpos($layer['image'], 'darj_snow_mountain') !== false || strpos($layer['image'], 'darj_distant_mountain') !== false) {
        $layer['style'] = str_replace('height: 120%;', 'height: auto;', $layer['style']);
    }
    $new_darj[] = $layer;
}

$stmt_up = $pdo->prepare("UPDATE destinations SET parallax_layers_json = ? WHERE slug = ?");
$stmt_up->execute([json_encode($new_sikkim), 'sikkim']);
$stmt_up->execute([json_encode($new_darj), 'darjeeling']);

echo "Fixed DB layers for Sikkim and Darjeeling!\n";
?>
