<?php
/**
 * Lead Submission API
 * Saves a local copy of the lead and optionally pushes it to the CRM.
 */

require_once '../config/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ll_json_response('error', 'METHOD_NOT_ALLOWED', 'Invalid request method.');
}

// Anti-bot guard: honeypot -> CSRF -> time-trap -> rate limits.
lead_guard_json($_POST);

function cleanInput($key) {
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
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

$company = cleanInput('company');
$size = cleanInput('size');
$requirements = cleanInput('requirements');

if ($company || $size || $requirements) {
    $parts = [];
    if ($company) $parts[] = "Company Name: $company";
    if ($size) $parts[] = "Group Size: $size pax";
    if ($requirements) $parts[] = "Requirements:\n$requirements";
    $message = implode("\n\n", $parts);
    if ($size) {
        $adults = $size;
    }
}

if ($message === '' && $destination !== '') {
    $message = $destination;
}

if (!$name || !$phone) {
    ll_json_response('error', 'VALIDATION_ERROR', 'Name and phone are required.');
}

$local_id = null;
$status_column = null;

if ($pdo) {
    try {
        $hasLegacyLeadSchema = tableHasColumn($pdo, 'leads', 'customer_name');

        if ($hasLegacyLeadSchema) {
            $status_column = 'status';
            
            // Check for modern columns in legacy table
            $hasDest = tableHasColumn($pdo, 'leads', 'destination');
            $hasDate = tableHasColumn($pdo, 'leads', 'travel_date');
            
            if ($hasDest && $hasDate) {
                $stmt = $pdo->prepare(
                    "INSERT INTO leads (package_id, customer_name, customer_phone, customer_email, message, destination, travel_date, adults, children)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute([
                    $package_id ?: null,
                    $name,
                    $phone,
                    $email ?: null,
                    $message ?: null,
                    $destination ?: null,
                    $travel_date ?: null,
                    $adults ?: 2,
                    $children ?: 0
                ]);
            } else {
                // Fallback: Append trip details to the message field if columns don't exist
                $fullMessage = $message;
                if ($destination || $travel_date) {
                    $details = "\n\n--- Trip Details ---\n";
                    if ($destination) $details .= "Destination: $destination\n";
                    if ($travel_date) $details .= "Date: $travel_date\n";
                    if ($adults || $children) $details .= "Pax: $adults Adults, $children Children\n";
                    $fullMessage .= $details;
                }
                
                $stmt = $pdo->prepare(
                    "INSERT INTO leads (package_id, customer_name, customer_phone, customer_email, message)
                     VALUES (?, ?, ?, ?, ?)"
                );
                $stmt->execute([
                    $package_id ?: null,
                    $name,
                    $phone,
                    $email ?: null,
                    $fullMessage ?: null
                ]);
            }
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

$is_local = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']) || (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false);

// 2. Fetch CRM Settings from Database
$crm_url = $settings['crm_url'] ?? 'https://crm.leisurelooptrip.in/api/leads/create/';
$crm_api_key = $settings['crm_api_key'] ?? 'LL-CRM-SECURE-8918-7908';

// Local Override for testing
if ($is_local) {
    $crm_url = 'http://127.0.0.1:8080/api/leads/create/';
}

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

ll_json_response('success', 'OK', 'Thank you! Our travel expert will contact you shortly.', [
    'sync_status' => $http_code === 201 ? 'synced' : 'queued'
]);
?>
