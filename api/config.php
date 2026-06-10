<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');  // VULN: Allow any domain (CORS misconfiguration)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include main database config (adjust path as needed)
include_once '../config.php';

// API version
define('API_VERSION', '1.0');

// VULN: No rate limiting
// VULN: No API key authentication
// VULN: No request logging (or insecure logging if added)

// Simple error handler
function send_json($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit();
}

function send_error($message, $code = 400, $details = null) {
    $error = [
        'success' => false,
        'error' => $message,
        'code' => $code
    ];
    if ($details) {
        $error['details'] = $details;
    }
    send_json($error, $code);
}

function send_success($data, $message = "Success") {
    send_json([
        'success' => true,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ], 200);
}
?>