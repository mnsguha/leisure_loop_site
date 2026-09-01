<?php
require '../config/db.php';
if (!$pdo) {
    die("PDO is null. Check db connection.");
}

$layers = [
    [
        "image" => "images/parallax/sunset_sky.webp",
        "class" => "p-layer-sky",
        "depth" => "0.10",
        "style" => "z-index: 1;"
    ],
    [
        "image" => "images/parallax/sunset_mountains.webp",
        "class" => "p-layer-back",
        "depth" => "0.15",
        "style" => "z-index: 2;"
    ],
    [
        "image" => "images/parallax/sunset_village.webp",
        "class" => "p-layer-mid",
        "depth" => "0.25",
        "style" => "z-index: 3;"
    ],
    [
        "image" => "images/parallax/sunset_flags.webp",
        "class" => "p-layer-foremost",
        "depth" => "0.40",
        "style" => "z-index: 8;"
    ]
];

$json = json_encode($layers);
$stmt = $pdo->prepare("UPDATE destinations SET parallax_layers = :layers WHERE slug = 'sikkim'");
$stmt->execute(['layers' => $json]);
echo "Updated Sikkim layers successfully.\n";
?>
