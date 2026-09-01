<?php
require_once '../config/db.php';
require_once '../config/recaptcha.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
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
        echo json_encode(['success' => false, 'message' => 'Please verify that you are not a robot.']);
        exit;
    }
}

try {
    // Check if table exists, create if not
    $pdo->exec("CREATE TABLE IF NOT EXISTS subscribers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT UNIQUE NOT NULL,
        subscribed_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $stmt = $pdo->prepare("INSERT INTO subscribers (email) VALUES (?)");
    $stmt->execute([$email]);
    echo json_encode(['success' => true, 'message' => 'Welcome to the Inner Circle!']);
} catch (PDOException $e) {
    if ($e->getCode() == 23000 || $e->getCode() == 19) { // Integrity constraint violation (UNIQUE)
        echo json_encode(['success' => true, 'message' => 'You are already subscribed to the Inner Circle.']);
    } else {
        error_log("Subscribe Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
    }
}
