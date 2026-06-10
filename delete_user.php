<?php
include 'config.php';

$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    die("No user selected");
}

$user_id = (int)$user_id;

/* =========================
   1.  enrollments 
========================= */
$conn->query("
DELETE e FROM enrollments e
JOIN courses c ON e.course_id = c.id
WHERE c.instructor_id = $user_id
");

/* delete user enrollments */
$conn->query("DELETE FROM enrollments WHERE student_id = $user_id");


/* =========================
   2. delete courses for instructor 
========================= */
$conn->query("DELETE FROM courses WHERE instructor_id = $user_id");


/* =========================
   3. deletes user
========================= */
$conn->query("DELETE FROM users WHERE id = $user_id");


header("Location: view_users.php");
exit();
?>