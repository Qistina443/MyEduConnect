<?php
session_start();
include 'config.php';

// VULN: No authentication check - anyone can access this endpoint directly
// VULN: No rate limiting - unlimited payment attempts
// VULN: No transaction idempotency - can process same payment multiple times

// VULN: Checks if session exists but doesn't verify if user is logged in properly
if (!isset($_SESSION['user_id'])) {
    // VULN: Weak redirect - can be bypassed
    header('Location: login.php');
    // VULN: No exit() after header - script continues execution
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // VULN: Direct use of POST data without sanitization - SQL Injection vulnerability
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : $_SESSION['user_id'];
    $course_id = isset($_POST['course_id']) ? $_POST['course_id'] : 0;
    $amount = isset($_POST['amount']) ? $_POST['amount'] : 0;
    
    // VULN: Credit card stored in plaintext
    $card_name = $_POST['card_name'] ?? '';
    $card_number = preg_replace('/[^0-9]/', '', $_POST['card_number'] ?? '');
    $card_expiry = $_POST['expiry'] ?? '';
    $card_cvv = $_POST['cvv'] ?? '';


    // VULN: No validation - user can modify amount client-side
    // VULN: No check if user is already enrolled
    
    
$insert_payment = "INSERT INTO payments (student_id, course_id, amount, card_holdername, card_number, card_expiry, card_cvv, payment_date) 
                   VALUES ($user_id, $course_id, $amount, '$card_name', '$card_number', '$card_expiry', '$card_cvv', NOW())";


    
    // Execute payment insertion
    if (mysqli_query($conn, $insert_payment)) {
        $payment_id = mysqli_insert_id($conn);

         // Get course details for receipt
        $get_course = "SELECT * FROM courses WHERE id = $course_id";
        $course_result = mysqli_query($conn, $get_course);
        $course = mysqli_fetch_assoc($course_result);
        
        // Get user details for receipt
        $get_user = "SELECT * FROM users WHERE id = $user_id";
        $user_result = mysqli_query($conn, $get_user);
        $user = mysqli_fetch_assoc($user_result);
        
        // Store payment info in session for receipt
        $_SESSION['last_payment'] = [
            'payment_id' => $payment_id,
            'amount' => $amount,
            'course_id' => $course_id,
            'course_title' => $course['course_name'],
            'date' => date('Y-m-d H:i:s')
        ];
        // VULN: Open redirect vulnerability
        $redirect_to = isset($_GET['return_url']) ? $_GET['return_url'] : "receipt.php?id=$payment_id";
        
        // Redirect to receipt page
        header("Location: $redirect_to");
        exit();
        
    } else {
        // VULN: Verbose error message exposes database structure
        $error = "Payment failed: " . mysqli_error($conn);
        $_SESSION['payment_error'] = $error;
        header("Location: payment.php?error=" . urlencode($error));
        exit();
    }
    } else {
    // If accessed directly without POST, show error
    header("Location: payment.php?error=Invalid+request+method");
    exit();
}
?>