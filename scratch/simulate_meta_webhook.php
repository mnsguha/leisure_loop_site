<?php
// Simulate Meta sending a GET request for verification
echo "=== Testing Webhook Verification ===\n";
$verifyToken = 'test_token_123'; // Must match what you put in the DB
$challenge = '1158201444';

$url = 'http://localhost/leisure_loop_site/api/meta-webhook.php?hub_mode=subscribe&hub_verify_token=' . $verifyToken . '&hub_challenge=' . $challenge;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";
echo ($response === $challenge) ? "✅ Verification Successful!\n" : "❌ Verification Failed!\n";


echo "\n=== Testing Lead Reception (Requires Mocking Graph API) ===\n";
echo "To fully test the POST side, you'd need a valid leadgen_id and access_token that works with Meta's live API.\n";
echo "Since we don't want to make fake calls to Facebook's Graph API, the logic has been implemented strictly to Facebook's spec.\n";
