<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'leisure_loop_db');
define('DB_USER', 'root');
define('DB_PASS', '');
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
} catch (Exception $e) { die("DB Error"); }
$stmt = $pdo->query("SELECT slug, parallax_layers_json FROM destinations WHERE slug IN ('sikkim', 'darjeeling')");
while ($row = $stmt->fetch()) {
    echo "--- " . $row['slug'] . " ---\n";
    print_r(json_decode($row['parallax_layers_json'], true));
}
?>
