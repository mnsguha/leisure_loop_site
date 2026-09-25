<?php
declare(strict_types=1);

require_once '../includes/functions.php';
require_once '../config/db.php';
requireAdmin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ll_json_response('error', 'METHOD_NOT_ALLOWED', 'Invalid request method.');
}

if (!$pdo) {
    ll_json_response('error', 'INTERNAL_ERROR', 'Database connection failed.');
}

// Ensure columns exist
try {
    $pdo->exec("ALTER TABLE hotel_bookings ADD COLUMN crm_synced TINYINT(1) DEFAULT 0");
} catch (PDOException $e) { /* column exists */ }
try {
    $pdo->exec("ALTER TABLE leads ADD COLUMN crm_synced TINYINT(1) DEFAULT 0");
} catch (PDOException $e) { /* column exists */ }

// Get settings
$stmt = $pdo->query("SELECT crm_api_key, crm_url FROM settings WHERE id = 1");
$settings = $stmt->fetch();

$apiUrl = $settings['crm_url'] ?? null;
$apiKey = $settings['crm_api_key'] ?? null;

if (empty($apiUrl) || empty($apiKey)) {
    ll_json_response('error', 'VALIDATION_ERROR', 'CRM Webhook not configured. Please set the API Key and URL in settings.');
}

// Ensure the raw request body is read for type
$input = json_decode(file_get_contents('php://input'), true) ?? [];
$type = $input['type'] ?? 'hotel';

$syncedCount = 0;
$failedCount = 0;

if ($type === 'hotel') {
    $stmt = $pdo->query("SELECT * FROM hotel_bookings WHERE crm_synced = 0 LIMIT 50");
    $records = $stmt->fetchAll();

    foreach ($records as $record) {
        // Fetch hotel name for destination
        $hotelName = 'Unknown Hotel';
        if (!empty($record['hotel_id'])) {
            $hStmt = $pdo->prepare("SELECT name FROM hotels WHERE id = ?");
            $hStmt->execute([$record['hotel_id']]);
            $hotel = $hStmt->fetch();
            if ($hotel) {
                $hotelName = $hotel['name'];
            }
        }

        $payload = [
            'name' => $record['guest_name'],
            'phone' => $record['phone'],
            'email' => $record['email'] ?? '',
            'destination' => $hotelName,
            'travel_date' => $record['check_in'] ?? '',
            'adults' => $record['adults'] ?? 0,
            'children' => 0,
            'source' => 'Website Hotel Inquiry',
            'notes' => 'Rooms: ' . ($record['rooms'] ?? 1)
        ];

        if (pushToCRM($apiUrl, $apiKey, $payload)) {
            $uStmt = $pdo->prepare("UPDATE hotel_bookings SET crm_synced = 1 WHERE id = ?");
            $uStmt->execute([$record['id']]);
            $syncedCount++;
        } else {
            $failedCount++;
        }
    }
} elseif ($type === 'lead') {
    $stmt = $pdo->query("SELECT * FROM leads WHERE crm_synced = 0 LIMIT 50");
    $records = $stmt->fetchAll();

    foreach ($records as $record) {
        $destName = $record['destination'] ?? '';
        if (empty($destName) && !empty($record['package_id'])) {
            $pStmt = $pdo->prepare("SELECT title FROM packages WHERE id = ?");
            $pStmt->execute([$record['package_id']]);
            $pkg = $pStmt->fetch();
            if ($pkg) {
                $destName = $pkg['title'];
            }
        }

        $payload = [
            'name' => $record['name'] ?? $record['customer_name'] ?? 'Unknown',
            'phone' => $record['phone'] ?? $record['customer_phone'] ?? '',
            'email' => $record['email'] ?? '',
            'destination' => $destName,
            'travel_date' => $record['travel_date'] ?? '',
            'adults' => $record['adults'] ?? 0,
            'children' => $record['children'] ?? 0,
            'source' => 'Website General Inquiry',
            'notes' => $record['message'] ?? $record['notes'] ?? ''
        ];

        if (pushToCRM($apiUrl, $apiKey, $payload)) {
            $uStmt = $pdo->prepare("UPDATE leads SET crm_synced = 1 WHERE id = ?");
            $uStmt->execute([$record['id']]);
            $syncedCount++;
        } else {
            $failedCount++;
        }
    }
}

function pushToCRM(string $url, string $key, array $payload): bool {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'X-API-KEY: ' . $key
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($httpCode === 201 || $httpCode === 200);
}

ll_json_response('success', 'OK', "Successfully synced $syncedCount records.", [
    'synced' => $syncedCount,
    'failed' => $failedCount
]);
