<?php
require_once __DIR__ . '/../config/db.php';
if (!$pdo) {
    die("Database connection failed\n");
}

echo "--- TABLES ---\n";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $t) {
    echo "Table: $t\n";
}

echo "\n--- COLUMNS of destinations ---\n";
try {
    $columns = $pdo->query("SHOW COLUMNS FROM destinations")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $c) {
        echo "{$c['Field']} ({$c['Type']})\n";
    }
} catch (Exception $e) {
    echo "Error columns: " . $e->getMessage() . "\n";
}

echo "\n--- DATA from destinations ---\n";
try {
    $data = $pdo->query("SELECT id, name, slug, tagline, category, cover_image FROM destinations")->fetchAll(PDO::FETCH_ASSOC);
    print_r($data);
} catch (Exception $e) {
    echo "Error data: " . $e->getMessage() . "\n";
}
