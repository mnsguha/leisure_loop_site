<?php
require '../config/db.php';
$stmt = $pdo->query("SELECT parallax_layers_json FROM destinations WHERE slug='sikkim'");
$sikkim_layers = json_decode($stmt->fetchColumn(), true);

// Remove custom clouds first
$new_layers = [];
foreach ($sikkim_layers as $l) {
    if (strpos($l['image'], 'custom_cloud') === false) {
        $new_layers[] = $l;
    }
}

// Clouds
$clouds = [
    [
        "image" => "images/parallax/custom_cloud_1.png",
        "class" => "p-layer cloud-img-1",
        "style" => "position:absolute; top:40%; left:10%; width:auto; height:auto; opacity:0.6; filter:blur(2px); z-index: 5; pointer-events: none;",
        "depth" => "0.5",
        "z_index" => 5
    ],
    [
        "image" => "images/parallax/custom_cloud_2.png",
        "class" => "p-layer cloud-img-2",
        "style" => "position:absolute; top:60%; right:15%; width:auto; height:auto; opacity:0.8; filter:blur(4px); z-index: 6; pointer-events: none;",
        "depth" => "0.7",
        "z_index" => 6
    ],
    [
        "image" => "images/parallax/custom_cloud_3.png",
        "class" => "p-layer cloud-img-3",
        "style" => "position:absolute; top:20%; left:50%; width:auto; height:auto; opacity:0.5; filter:blur(1px); z-index: 5; pointer-events: none;",
        "depth" => "0.6",
        "z_index" => 5
    ]
];

// Combine
$final = [];
foreach ($new_layers as $l) {
    if (strpos($l['image'], 'sunset_flags') !== false) {
        foreach ($clouds as $c) $final[] = $c;
    }
    $final[] = $l;
}

// If sunset_flags wasn't found, just append
if (count($final) == count($new_layers)) {
    $final = array_merge($new_layers, $clouds);
}

$stmt_up = $pdo->prepare("UPDATE destinations SET parallax_layers_json = ? WHERE slug = 'sikkim'");
$stmt_up->execute([json_encode($final)]);
echo "Forced restore success!";
?>
