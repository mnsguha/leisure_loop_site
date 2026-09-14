<?php
declare(strict_types=1);

require_once '../includes/functions.php';
require_once '../config/db.php';

// Ensure database connection
if (!$pdo) {
    http_response_code(500);
    exit('Database connection failed.');
}

// Retrieve Meta credentials from settings
$stmt = $pdo->query("SELECT meta_verify_token, meta_access_token FROM settings WHERE id = 1");
$settings = $stmt->fetch();
$verifyToken = $settings['meta_verify_token'] ?? '';
$accessToken = $settings['meta_access_token'] ?? '';

// ==========================================
// 1. WEBHOOK VERIFICATION (GET)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $hubMode = $_GET['hub_mode'] ?? '';
    $hubVerifyToken = $_GET['hub_verify_token'] ?? '';
    $hubChallenge = $_GET['hub_challenge'] ?? '';

    if ($hubMode === 'subscribe' && $hubVerifyToken === $verifyToken) {
        http_response_code(200);
        echo $hubChallenge;
        exit;
    } else {
        http_response_code(403);
        exit('Verification failed.');
    }
}

// ==========================================
// 2. RECEIVE LEAD DATA (POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $payload = json_decode($input, true);

    if (!$payload) {
        http_response_code(400);
        exit('Invalid JSON.');
    }

    if (isset($payload['object']) && $payload['object'] === 'page') {
        foreach ($payload['entry'] as $entry) {
            foreach ($entry['changes'] as $change) {
                if ($change['value']['item'] === 'leadgen') {
                    $leadgenId = $change['value']['leadgen_id'] ?? null;
                    
                    if ($leadgenId && $accessToken) {
                        // Fetch Lead Details from Meta Graph API
                        $graphUrl = "https://graph.facebook.com/v19.0/{$leadgenId}?access_token={$accessToken}";
                        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $graphUrl);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                        $response = curl_exec($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);

                        if ($httpCode === 200 && $response) {
                            $leadData = json_decode($response, true);
                            $fieldData = $leadData['field_data'] ?? [];
                            
                            $name = '';
                            $email = '';
                            $phone = '';

                            foreach ($fieldData as $field) {
                                if (in_array($field['name'], ['full_name', 'first_name'])) {
                                    $name = $field['values'][0] ?? '';
                                } elseif ($field['name'] === 'email') {
                                    $email = $field['values'][0] ?? '';
                                } elseif ($field['name'] === 'phone_number') {
                                    $phone = $field['values'][0] ?? '';
                                }
                            }

                            // Insert Lead into database
                            $stmt = $pdo->prepare("INSERT INTO leads (customer_name, customer_email, customer_phone, destination, message, source, crm_synced, created_at) VALUES (?, ?, ?, ?, ?, ?, 0, NOW())");
                            $stmt->execute([
                                $name,
                                $email,
                                $phone,
                                'General Inquiry',
                                "Lead retrieved from Meta Webhook (Leadgen ID: {$leadgenId})",
                                'Meta Lead Ad'
                            ]);
                        }
                    }
                }
            }
        }
        
        // Always return 200 OK to Meta to prevent retries
        http_response_code(200);
        echo 'EVENT_RECEIVED';
        exit;
    }

    http_response_code(404);
    exit;
}
