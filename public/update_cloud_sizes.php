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

foreach (['sikkim', 'darjeeling'] as $slug) {
    if (!isset($dests[$slug])) continue;
    
    $layers = $dests[$slug];
    $new_layers = [];
    foreach ($layers as $layer) {
        if (strpos($layer['image'], 'custom_cloud_1') !== false) {
            $layer['style'] = preg_replace('/width:\s*[^;]+;/', 'width: 25vw;', $layer['style']);
        } elseif (strpos($layer['image'], 'custom_cloud_2') !== false) {
            $layer['style'] = preg_replace('/width:\s*[^;]+;/', 'width: 35vw;', $layer['style']);
        } elseif (strpos($layer['image'], 'custom_cloud_3') !== false) {
            $layer['style'] = preg_replace('/width:\s*[^;]+;/', 'width: 20vw;', $layer['style']);
        }
        $new_layers[] = $layer;
    }
    
    $stmt_up = $pdo->prepare("UPDATE destinations SET parallax_layers_json = ? WHERE slug = ?");
    $stmt_up->execute([json_encode($new_layers), $slug]);
}

echo "Clouds fixed!";
?>
