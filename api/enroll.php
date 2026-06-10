<?php
require_once 'config.php';

// VULN: No authentication check

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_error("Method not allowed. Use POST", 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$student_id = $input['student_id'] ?? $_POST['student_id'] ?? 0;
$course_id = $input['course_id'] ?? $_POST['course_id'] ?? 0;

if (!$student_id || !$course_id) {
    send_error("student_id and course_id required", 400);
}

// VULN: Check if already enrolled (SQL Injection)
$check_query = "SELECT * FROM enrollments WHERE student_id = '$student_id' AND course_id = '$course_id'";
$check = $conn->query($check_query);

if ($check && $check->num_rows > 0) {
    send_error("Already enrolled in this course", 409);
}

// VULN: SQL Injection in INSERT
$insert_query = "INSERT INTO enrollments (student_id, course_id, enrollment_date, status) 
                 VALUES ('$student_id', '$course_id', NOW(), 'enrolled')";

if ($conn->query($insert_query)) {
    send_success([
        'student_id' => $student_id,
        'course_id' => $course_id,
        'enrollment_date' => date('Y-m-d H:i:s')
    ], "Successfully enrolled in course");
} else {
    send_error("Enrollment failed: " . $conn->error, 500);
}
?>