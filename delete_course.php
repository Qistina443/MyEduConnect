<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

$course_id = $_GET['id'] ?? null;

if(!$course_id){
    header("Location: admin.php");
    exit();
}

$admin_id = $_SESSION['user_id'];


// ============================
// GET COURSE DATA FIRST
// ============================
$stmt = $conn->prepare("SELECT * FROM courses WHERE id=?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

if(!$course){
    header("Location: admin.php");
    exit();
}


// ============================
// SAVE TO deleted_courses
// ============================
$conn->query("
INSERT INTO deleted_courses (course_id, course_name, deleted_by, day, time)
VALUES (
    $course_id,
    '{$course['course_name']}',
    $admin_id,
    '{$course['day']}',
    '{$course['time']}'
)
");


// ============================
// DELETE ENROLLMENTS FIRST
// ============================
$conn->query("DELETE FROM enrollments WHERE course_id=$course_id");


// ============================
// DELETE COURSE
// ============================
$conn->query("DELETE FROM courses WHERE id=$course_id");


// ============================
// REDIRECT BACK
// ============================
header("Location: admin.php");
exit();
?>