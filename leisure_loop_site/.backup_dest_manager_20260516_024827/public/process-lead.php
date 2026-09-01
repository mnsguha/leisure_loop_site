<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Sanitize inputs
$name        = trim(htmlspecialchars($_POST['name'] ?? ''));
$phone       = trim(htmlspecialchars($_POST['phone'] ?? ''));
$destination = trim(htmlspecialchars($_POST['destination'] ?? ''));
$date        = trim(htmlspecialchars($_POST['date'] ?? ''));
$adults      = trim(htmlspecialchars($_POST['adults'] ?? '1'));
$children    = trim(htmlspecialchars($_POST['children'] ?? '0'));

$notes = "Destination: $destination | Date: $date | Adults: $adults | Children: $children";
$source = 'Website Hero Form';

$saved = false;

// Try saving to DB (production)
if ($pdo) {
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO leads (name, phone, notes, source, status, created_at)
             VALUES (?, ?, ?, ?, 'new', NOW())"
        );
        $stmt->execute([$name, $phone, $notes, $source]);
        $saved = true;
    } catch (Exception $e) {
        // DB failed — fallback to log file
    }
}

// Fallback: Write to a log file for local testing
if (!$saved) {
    $log_dir = '../storage/leads/';
    if (!is_dir($log_dir)) mkdir($log_dir, 0755, true);
    $log_line = date('Y-m-d H:i:s') . " | $name | $phone | $notes\n";
    file_put_contents($log_dir . 'leads_log.txt', $log_line, FILE_APPEND);
}

// Redirect back with success message
header('Location: index.php?lead_sent=1');
exit;
?>
