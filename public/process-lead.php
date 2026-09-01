<?php
require_once '../config/db.php';
require_once '../config/recaptcha.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$enforce_recaptcha = !empty($_POST['enforce_recaptcha']);
$recaptcha_token = $_POST['g-recaptcha-response'] ?? '';

if ($enforce_recaptcha && recaptchaIsConfigured()) {
    $is_valid = false;
    if ($recaptcha_token) {
        $payload = http_build_query([
            'secret' => recaptchaSecretKey(),
            'response' => $recaptcha_token,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ]);
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => $payload,
                'timeout' => 10
            ]
        ]);
        $responseBody = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        if ($responseBody) {
            $result = json_decode($responseBody, true);
            if (!empty($result['success'])) {
                $is_valid = true;
            }
        }
    }
    
    if (!$is_valid) {
        $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
        $redirect .= (strpos($redirect, '?') !== false ? '&' : '?') . 'lead_error=1';
        header("Location: $redirect");
        exit;
    }
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
