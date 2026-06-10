<?php
require_once 'config.php';

// VULN: No authentication - anyone can see enrollments
// VULN: IDOR - Can view ANY student's enrollments

$student_id = isset($_GET['student_id']) ? $_GET['student_id'] : 0;
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : $student_id;

if (!$user_id) {
    send_error("student_id or user_id parameter required", 400);
}

// VULN: SQL Injection
$query = "SELECT e.*, c.course_name, c.description, c.amount, c.day, c.time 
          FROM enrollments e 
          JOIN courses c ON e.course_id = c.id 
          WHERE e.student_id = '$user_id' 
          ORDER BY e.enrollment_date DESC";

$result = $conn->query($query);

if (!$result) {
    send_error("Database error: " . $conn->error, 500);
}

$enrollments = [];
while ($row = $result->fetch_assoc()) {
    $enrollments[] = $row;
}

send_success([
    'student_id' => $user_id,
    'total_enrolled' => count($enrollments),
    'enrollments' => $enrollments
]);
?>