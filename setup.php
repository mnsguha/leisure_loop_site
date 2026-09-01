<?php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'leisure_loop_db');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
$sql = "CREATE TABLE IF NOT EXISTS hotel_partners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    logo_url VARCHAR(255) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    display_order INT DEFAULT 0
)";
$pdo->exec($sql);

$pdo->exec("TRUNCATE TABLE hotel_partners");
$partners = [
    ['Taj Hotels', 'taj.svg', 1],
    ['Ramada', 'ramada.svg', 2],
    ['Udaan Hotels', 'udaan.svg', 3],
    ['Sinclairs', 'sinclairs.svg', 4],
    ['Summit', 'summit.svg', 5],
    ['The Deltin', 'deltin.svg', 6],
    ['Lemon Tree', 'lemontree.svg', 7],
    ['Divine', 'divine.svg', 8],
    ['Q Saina', 'qsaina.svg', 9]
];
$stmt = $pdo->prepare("INSERT INTO hotel_partners (name, logo_url, display_order) VALUES (?, ?, ?)");
foreach($partners as $p) {
    $stmt->execute($p);
}
echo "Database setup complete.\n";

$dir = __DIR__ . '/public/assets/img/partners/';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}
foreach($partners as $p) {
    $file = $dir . $p[1];
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="100" viewBox="0 0 200 100"><rect width="200" height="100" fill="transparent"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Arial, sans-serif" font-size="20" fill="#ffffff">'.$p[0].'</text></svg>';
    file_put_contents($file, $svg);
}
echo "SVGs created.\n";
?>
