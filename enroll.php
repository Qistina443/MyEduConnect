<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: login.php");
    
}

$student_id = $_SESSION['user_id'];
$course_id = $_POST['course_id'];

/* 1. select course from course table
$course = $conn->query("SELECT * FROM courses WHERE id = $course_id")->fetch_assoc();

if(!$course){
    die("Course not found");
}

/* 2. check for conflict*/
$conflict = $conn->query("
SELECT c.*
FROM enrollments e
JOIN courses c ON e.course_id = c.id
WHERE e.student_id = $student_id
AND c.day = '{$course['day']}'
AND c.time = '{$course['time']}'
");

/* 3.if student schedule already taken*/
if($conflict->num_rows > 0){
    echo "<script>
        alert(' You cannot enroll: Time conflict with another course');
        window.location='student.php';
    </script>";
    exit();
}

/* 4. if no conflict, enroll successful*/
$conn->query("
INSERT INTO enrollments (student_id, course_id)
VALUES ($student_id, $course_id)
");

echo "<script>
    alert(' Enrolled successfully');
    window.location='student.php';
</script>";
?>