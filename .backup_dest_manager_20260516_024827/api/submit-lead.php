<?php
/**
 * Lead Submission API
 * Saves a local copy of the lead and optionally pushes it to the CRM.
 */

require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../config/recaptcha.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

function cleanInput($key) {
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
}

function verifyRecaptchaResponse($token) {
    if (!recaptchaIsConfigured()) {
        return true;
    }

    if (!$token) {
        return false;
    }

    $payload = http_build_query([
        'secret' => recaptchaSecretKey(),
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ]);

    $responseBody = false;

    if (function_exists('curl_init')) {
        $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $responseBody = curl_exec($ch);
        curl_close($ch);
    }

    if ($responseBody === false) {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => $payload,
                'timeout' => 10
            ]
        ]);
        $responseBody = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
    }

    if ($responseBody === false) {
        return false;
    }

    $decoded = json_decode($responseBody, true);
    return !empty($decoded['success']);
}

$name = cleanInput('name');
$phone = cleanInput('phone');
$email = filter_var(cleanInput('email'), FILTER_SANITIZE_EMAIL);
$message = cleanInput('message');
$destination = cleanInput('destination');
$travel_date = cleanInput('date');
$adults = cleanInput('adults');
$children = cleanInput('children');
$package_id = (int) cleanInput('package_id');
$enforce_recaptcha = cleanInput('enforce_recaptcha') === '1';
$recaptcha_token = cleanInput('g-recaptcha-response');

if ($message === '' && $destination !== '') {
    $message = $destination;
}

if (!$name || !$phone) {
    echo json_encode(['success' => false, 'message' => 'Name and phone are required.']);
    exit;
}

if ($enforce_recaptcha && !verifyRecaptchaResponse($recaptcha_token)) {
    echo json_encode(['success' => false, 'message' => 'Please confirm that you are not a robot.']);
    exit;
}

$local_id = null;
$status_column = null;

if ($pdo) {
    try {
        $hasLegacyLeadSchema = tableHasColumn($pdo, 'leads', 'customer_name');

        if ($hasLegacyLeadSchema) {
            $status_column = 'status';
            $stmt = $pdo->prepare(
                "INSERT INTO leads (package_id, customer_name, customer_phone, customer_email, message)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $package_id ?: null,
                $name,
                $phone,
                $email ?: null,
                $message ?: null
            ]);
        } else {
            $status_column = 'status';
            $notes = $message;
            $stmt = $pdo->prepare(
                "INSERT INTO leads (name, phone, email, destination, travel_date, adults, children, notes)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $name,
                $phone,
                $email ?: null,
                $destination ?: null,
                $travel_date ?: null,
                $adults ?: null,
                $children ?: null,
                $notes ?: null
            ]);
        }

        $local_id = $pdo->lastInsertId();
    } catch (Throwable $e) {
        $local_id = null;
    }
}

$crm_url = 'https://crm.leisurelooptrip.in/api/leads/create/';
$crm_data = [
    'source' => 'Website Marketing',
    'customer_name' => $name,
    'phone' => $phone,
    'email' => $email,
    'notes' => $message,
    'destination' => $destination,
    'travel_date' => $travel_date,
    'adults' => $adults,
    'children' => $children,
    'package_id' => $package_id ?: null
];

$http_code = 0;
if (function_exists('curl_init')) {
    $ch = curl_init($crm_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($crm_data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-API-KEY: YOUR_CRM_API_KEY'
    ]);

    curl_exec($ch);
    $http_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
}

if ($local_id && $pdo && $status_column) {
    $hasLegacyLeadSchema = tableHasColumn($pdo, 'leads', 'customer_name');
    $nextStatus = $http_code === 201
        ? ($hasLegacyLeadSchema ? 'synced' : 'contacted')
        : ($hasLegacyLeadSchema ? 'failed' : 'new');

    try {
        $pdo->prepare("UPDATE leads SET {$status_column} = ? WHERE id = ?")->execute([$nextStatus, $local_id]);
    } catch (Throwable $e) {
        // Keep the lead even if status update fails.
    }
}

echo json_encode([
    'success' => true,
    'message' => 'Thank you! Our travel expert will contact you shortly.',
    'sync_status' => $http_code === 201 ? 'synced' : 'queued'
]);
?>
