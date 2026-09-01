<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'leisure_loop_db');
define('DB_USER', 'root');
define('DB_PASS', '');

$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);

$stmt = $pdo->query("SELECT slug, parallax_layers_json FROM destinations WHERE slug IN ('sikkim', 'darjeeling')");
while ($row = $stmt->fetch()) {
    $layers = json_decode($row['parallax_layers_json'], true);
    $new_layers = [];
    
    foreach ($layers as $layer) {
        $img = basename($layer['image']);
        
        // Sky (Layer 1)
        if (strpos($img, 'sky.png') !== false) {
            $layer['style'] = preg_replace('/z-index:\s*\d+;/', 'z-index: 10;', $layer['style']);
        }
        // Mountains (Layer 2)
        elseif (strpos($img, 'mountains.png') !== false || strpos($img, 'snow_mountain.png') !== false) {
            $layer['style'] = preg_replace('/z-index:\s*\d+;/', 'z-index: 20;', $layer['style']);
        }
        // Midground (Layer 3)
        elseif (strpos($img, 'village.png') !== false || strpos($img, 'distant_mountain.png') !== false) {
            $layer['style'] = preg_replace('/z-index:\s*\d+;/', 'z-index: 40;', $layer['style']);
        }
        // Foreground (Layer 4)
        elseif (strpos($img, 'flags.png') !== false || strpos($img, 'teagarden.png') !== false) {
            $layer['style'] = preg_replace('/z-index:\s*\d+;/', 'z-index: 50;', $layer['style']);
        }
        // Cloud 1 (Between Layer 1 and 2)
        elseif (strpos($img, 'custom_cloud_1') !== false) {
            $layer['style'] = "z-index: 15; pointer-events: none;";
        }
        // Cloud 2 & 3 (Between Layer 2 and 3, behind text)
        elseif (strpos($img, 'custom_cloud_2') !== false || strpos($img, 'custom_cloud_3') !== false) {
            $layer['style'] = "z-index: 25; pointer-events: none;";
        }
        
        $new_layers[] = $layer;
    }
    
    $stmt_up = $pdo->prepare("UPDATE destinations SET parallax_layers_json = ? WHERE slug = ?");
    $stmt_up->execute([json_encode($new_layers), $row['slug']]);
}

echo "Z-Indexes updated successfully!";
?>
