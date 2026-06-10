<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'instructor'){
    header("Location: login.php");
    
}

$instructor_id = $_SESSION['user_id'];

$courses = $conn->query("
SELECT c.*,
(SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) AS total_students
FROM courses c
WHERE c.instructor_id = $instructor_id
AND c.id NOT IN (SELECT course_id FROM deleted_courses)
ORDER BY c.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Instructor Dashboard</title>
<link rel="stylesheet" href="assets/style.css?v=1000">
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar">
<div class="logo">MyEduConnect</div>
<ul class="nav-links">
<li><a href="instructor.php" class="active">Instructor Dashboard</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
</nav>

<!-- DASHBOARD -->
<section class="dashboard">
<h2>My Courses</h2>

<?php if($courses->num_rows > 0): ?>

<table>
<tr>
<th>Course</th>
<th>Description</th>
<th>Day</th>
<th>Time</th>
<th>Students</th>
<th>Action</th>
</tr>

<?php while($row = $courses->fetch_assoc()): ?>

<tr>
<td><?= $row['course_name'] ?></td>
<td><?= $row['description'] ?></td>
<td><?= $row['day'] ?></td>
<td><?= $row['time'] ?></td>
<td><?= $row['total_students'] ?></td>

<td>
<a href="view_students.php?course_id=<?= $row['id'] ?>">
    View Students
</a>
</td>
</tr>

<?php endwhile; ?>

</table>

<?php else: ?>


<p style="text-align:center; font-size:20px; color:gray; margin-top:30px;">
    No courses available
</p>


<?php endif; ?>

</section>

<!-- FOOTER -->
<footer>
<p>Course Enrollment System | MyEduConnect</p>
</footer>

</body>
</html>
