<?php
require_once 'config.php';

// VULN: Only POST method allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_error("Method not allowed. Use POST", 405);
}

// Get POST data (VULN: No input validation)
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    // Try form-data as fallback
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
} else {
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
}

if (empty($email) || empty($password)) {
    send_error("Email and password required", 400);
}

// VULN: SQL Injection
// VULN: MD5 password hashing (weak)
$hashed_password = md5($password);
$query = "SELECT id, username, email, fullname, role FROM users 
          WHERE email = '$email' AND password = '$hashed_password'";

$result = $conn->query($query);

if (!$result) {
    send_error("Login error: " . $conn->error, 500);
}

if ($result->num_rows == 0) {
    // VULN: Reveals that credentials are wrong (no timing attack protection)
    send_error("Invalid email or password", 401);
}

$user = $result->fetch_assoc();

// VULN: Generate simple token (not secure)
$token = base64_encode($user['id'] . ':' . time() . ':' . md5($user['email']));

// VULN: Store token in session (insecure)
session_start();
$_SESSION['api_token'] = $token;
$_SESSION['api_user_id'] = $user['id'];

send_success([
    'user' => $user,
    'token' => $token,
    'expires_in' => 86400  // 24 hours - VULN: No actual expiration check
], "Login successful");
?>