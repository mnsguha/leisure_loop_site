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
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
    echo "Connected\n";
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
} catch (PDOException $e) {
    echo "PDO Error: " . $e->getMessage() . "\n";
}
?>
