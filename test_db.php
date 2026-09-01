<?php $pdo = new PDO('mysql:host=localhost;dbname=leisure_loop_db;charset=utf8mb4', 'root', ''); print_r($pdo->query('DESCRIBE destinations')->fetchAll(PDO::FETCH_ASSOC)); ?>
