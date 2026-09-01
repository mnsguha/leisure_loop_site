<?php
// 1. Inject CSS into destination-details.php
$file = 'destination-details.php';
$content = file_get_contents($file);

$css = <<<EOT
/* 3D Roaming Clouds Animation */
@keyframes roamCloudLeftToRight {
    0% { transform: translateX(-20vw); opacity: 0; }
    10% { opacity: 0.9; }
    90% { opacity: 0.9; }
    100% { transform: translateX(120vw); opacity: 0; }
}

@keyframes roamCloudRightToLeft {
    0% { transform: translateX(120vw); opacity: 0; }
    10% { opacity: 0.9; }
    90% { opacity: 0.9; }
    100% { transform: translateX(-20vw); opacity: 0; }
}

.cloud-img-1, .cloud-img-2, .cloud-img-3, .darj-cloud-1, .darj-cloud-2, .darj-cloud-3 {
    position: absolute;
    object-fit: contain;
    opacity: 0; /* Controlled by animation */
    pointer-events: none;
    filter: drop-shadow(0 15px 15px rgba(0,0,0,0.1));
}

.darj-cloud-1 {
    bottom: 45%; width: 25vw;
    animation: roamCloudLeftToRight 30s linear infinite; animation-delay: -10s;
}
.darj-cloud-2 {
    bottom: 35%; width: 35vw;
    animation: roamCloudRightToLeft 25s linear infinite; animation-delay: -5s; animation-fill-mode: both;
}
.darj-cloud-3 {
    bottom: 40%; width: 20vw;
    animation: roamCloudRightToLeft 35s linear infinite; animation-delay: -15s; animation-fill-mode: both;
}

.cloud-img-1 {
    bottom: 25%; width: 25vw;
    animation: roamCloudLeftToRight 25s linear infinite; animation-delay: -5s;
}
.cloud-img-2 {
    bottom: 18%; width: 35vw;
    animation: roamCloudLeftToRight 20s linear infinite; animation-delay: -12s; animation-fill-mode: both;
}
.cloud-img-3 {
    bottom: 30%; width: 20vw;
    animation: roamCloudLeftToRight 35s linear infinite; animation-delay: -2s; animation-fill-mode: both;
}
EOT;

if (strpos($content, 'roamCloudLeftToRight') === false) {
    $content = str_replace('</style>', "\n" . $css . "\n</style>", $content);
    file_put_contents($file, $content);
    echo "CSS Injected! ";
} else {
    echo "CSS already present! ";
}

// 2. Clean Database Inline Styles
require '../config/db.php';

$stmt = $pdo->query("SELECT slug, parallax_layers_json FROM destinations WHERE slug IN ('sikkim', 'darjeeling')");
while ($row = $stmt->fetch()) {
    $layers = json_decode($row['parallax_layers_json'], true);
    $new_layers = [];
    foreach ($layers as $layer) {
        if (strpos($layer['image'], 'custom_cloud') !== false) {
            $layer['style'] = "z-index: 5; pointer-events: none;";
            
            if ($row['slug'] === 'darjeeling') {
                if (strpos($layer['image'], 'custom_cloud_1') !== false) $layer['class'] = 'p-layer darj-cloud-1';
                if (strpos($layer['image'], 'custom_cloud_2') !== false) $layer['class'] = 'p-layer darj-cloud-2';
                if (strpos($layer['image'], 'custom_cloud_3') !== false) $layer['class'] = 'p-layer darj-cloud-3';
            } else {
                if (strpos($layer['image'], 'custom_cloud_1') !== false) $layer['class'] = 'p-layer cloud-img-1';
                if (strpos($layer['image'], 'custom_cloud_2') !== false) $layer['class'] = 'p-layer cloud-img-2';
                if (strpos($layer['image'], 'custom_cloud_3') !== false) $layer['class'] = 'p-layer cloud-img-3';
            }
        }
        $new_layers[] = $layer;
    }
    $stmt_up = $pdo->prepare("UPDATE destinations SET parallax_layers_json = ? WHERE slug = ?");
    $stmt_up->execute([json_encode($new_layers), $row['slug']]);
}

echo "Database cleaned!";
?>
