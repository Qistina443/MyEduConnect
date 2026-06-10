<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}


$deleted = $conn->query("
SELECT d.*,
u.name AS admin_name
FROM deleted_courses d
LEFT JOIN users u ON d.deleted_by = u.id
ORDER BY d.deleted_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Deleted Courses</title>
<link rel="stylesheet" href="assets/style.css?v=1000">
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar">
<div class="logo">MyEduConnect</div>
<ul class="nav-links">
<li><a href="admin.php">Admin Dashboard</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
</nav>

<!-- CONTENT -->
<section class="dashboard">
<h2>Deleted Courses</h2>

<?php if($deleted->num_rows > 0): ?>

<table>
<tr>
<th>Course</th>
<th>Day</th>
<th>Time</th>
<th>Deleted By</th>
<th>Date</th>
</tr>

<?php while($d = $deleted->fetch_assoc()): ?>
<tr>
<td><?= $d['course_name'] ?></td>
<td><?= $d['day'] ?></td>
<td><?= $d['time'] ?></td>


<td><?= $d['admin_name'] ?></td>

<td><?= $d['deleted_at'] ?></td>
</tr>
<?php endwhile; ?>

</table>

<?php else: ?>

<p class="no-data">No deleted courses</p>

<?php endif; ?>

</section>

<!-- FOOTER -->
<footer>
<p>Course Enrollment System | MyEduConnect</p>

</footer>

</body>
</html>