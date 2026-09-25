<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Anti-bot guard: honeypot -> CSRF -> time-trap -> rate limits.
lead_guard_json($_POST);

function cleanInput($key) {
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
}

$first_name = cleanInput('guest_name');
$last_name = cleanInput('guest_last_name');
$name = $first_name . ' ' . $last_name;

$phone = cleanInput('phone');
$email = filter_var(cleanInput('email'), FILTER_SANITIZE_EMAIL);
$special_request = cleanInput('special_request');
$travel_date = cleanInput('travel_date');
$adults = cleanInput('adults');
$package_id = cleanInput('package_id');
$package_title = cleanInput('package_title');
$tour_code = cleanInput('tour_code');
$selected_hotel = cleanInput('selected_hotel');
$selected_cab = cleanInput('selected_cab');

if (!$name || !$phone || !$package_id) {
    echo json_encode(['success' => false, 'message' => 'Name, Phone, and Package details are required.']);
    exit;
}

// Prepare JSON notes for the PDF slip and CRM
$booking_details = [
    'package_id' => $package_id,
    'package_title' => $package_title,
    'tour_code' => $tour_code,
    'selected_hotel' => $selected_hotel,
    'selected_cab' => $selected_cab,
    'special_request' => $special_request,
    'travel_date' => $travel_date,
    'adults' => $adults
];
$json_notes = json_encode($booking_details);
$local_id = null;
$status_column = null;

if ($pdo) {
    try {
        $hasLegacyLeadSchema = tableHasColumn($pdo, 'leads', 'customer_name');
        
        if ($hasLegacyLeadSchema) {
            $status_column = 'status';
            $stmt = $pdo->prepare(
                "INSERT INTO leads (package_id, customer_name, customer_phone, customer_email, message, destination, travel_date, adults)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $package_id,
                $name,
                $phone,
                $email ?: null,
                $json_notes,
                $package_title,
                $travel_date,
                $adults
            ]);
        } else {
            $status_column = 'status';
            $stmt = $pdo->prepare(
                "INSERT INTO leads (name, phone, email, destination, travel_date, adults, notes)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $name,
                $phone,
                $email ?: null,
                $package_title,
                $travel_date,
                $adults,
                $json_notes
            ]);
        }

        $local_id = $pdo->lastInsertId();
        pdf_grant_access('package', (string) $local_id);
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// 2. Fetch CRM Settings from Database
$is_local = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']) || (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false);
$crm_url = $settings['crm_url'] ?? 'https://crm.leisurelooptrip.in/api/leads/create/';
$crm_api_key = $settings['crm_api_key'] ?? 'LL-CRM-SECURE-8918-7908';

if ($is_local) {
    $crm_url = 'http://127.0.0.1:8080/api/leads/create/';
}

$crm_data = [
    'source' => 'Website Package Checkout',
    'customer_name' => $name,
    'phone' => $phone,
    'email' => $email,
    'notes' => $json_notes,
    'destination' => $package_title,
    'travel_date' => $travel_date,
    'adults' => $adults,
    'package_id' => $package_id
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
        'X-API-KEY: ' . $crm_api_key
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
    'booking_id' => $local_id,
    'message' => 'Booking submitted successfully!',
    'sync_status' => $http_code === 201 ? 'synced' : 'queued'
]);
?>
