<?php
require_once 'config/db.php';
try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=leisure_loop_db", "root", "");
    $slides = $pdo->query("SELECT * FROM hero_slides")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($slides, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
