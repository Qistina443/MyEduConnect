<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$course_id = $_POST['course_id'] ?? null;

if(!$course_id){
    header("Location: student.php");
    exit();
}

// delete from enrollments
$stmt = $conn->prepare("DELETE FROM enrollments WHERE student_id=? AND course_id=?");
$stmt->bind_param("ii", $student_id, $course_id);

if($stmt->execute()){
    header("Location: student.php?msg=unenrolled");
} else {
    header("Location: student.php?msg=error");
}

$stmt->close();
?>