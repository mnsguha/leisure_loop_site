<?php
/**
 * Website Sync Health Endpoint
 * Responds to CRM health checks.
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Basic connectivity check
$status = [
    'status' => 'operational',
    'timestamp' => time(),
    'service' => 'Leisure Loop Marketing Site'
];

echo json_encode($status);
?>
