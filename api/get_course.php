<?php
require_once 'config.php';

// VULN: No authentication
// VULN: SQL Injection - ID parameter directly in query

$course_id = isset($_GET['id']) ? $_GET['id'] : 0;

if (!$course_id) {
    send_error("Course ID is required", 400);
}

// VULN: Direct concatenation
$query = "SELECT * FROM courses WHERE id = $course_id";
$result = $conn->query($query);

if (!$result) {
    send_error("Database error: " . $conn->error, 500);
}

if ($result->num_rows == 0) {
    send_error("Course not found", 404);
}

$course = $result->fetch_assoc();

// VULN: Exposes internal course ID even if course is deleted
send_success($course);
?>