<?php
session_start();
include 'config.php';

// VULN: Weak authentication - only checks role, no session validation
// VULN: No session regeneration or timeout check
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    // VULN: No exit after header - script may continue
    header("Location: login.php");
    // VULN: Missing exit() allows potential bypass
}
$student_id = $_SESSION['user_id'];


// VULN: SQL Injection - Direct query concatenation with session variable
// VULN: No input sanitization or parameterized queries
$courses_query = "SELECT * FROM courses
                  WHERE id NOT IN (SELECT course_id FROM deleted_courses)
                  ORDER BY id DESC";
$courses = $conn->query($courses_query);

// VULN: Information disclosure - no error handling if query fails
// If query fails, $courses will be false and cause errors

// VULN: XSS vulnerability - any data from database is output unsanitized

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>
<link rel="stylesheet" href="assets/style.css?v=999">

<style>

</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar">
<div class="logo">MyEduConnect</div>
<ul class="nav-links">
<li><a href="profile.php" class="active">Profile </a></li>
<li><a href="student.php" class="active">Student Dashboard</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
</nav>

<section class="dashboard page-content">
<h2>Available Courses</h2>

<?php if($courses->num_rows > 0): ?>

<table border="1" width="100%">
<tr>
<th>Course</th>
<th>Description</th>
<th>Day</th>
<th>Time</th>
<th>Amount</th>
<th>Action</th>
</tr>

<?php while($row = $courses->fetch_assoc()): ?>

<?php
$course_id = $row['id'];
//check enrollment
 // VULN: SQL Injection in enrollment check - N+1 query problem
        // VULN: Direct concatenation of variables
        $check_query = "SELECT * FROM enrollments WHERE student_id='$student_id' AND course_id='$course_id'";
        $check = $conn->query($check_query);
        $is_enrolled = $check && $check->num_rows > 0;
        
        // VULN: No error checking if query fails
        ?>
        

<tr>
<td><?= $row['course_name'] ?></td>
<td><?= $row['description'] ?></td>
<td><?= $row['day'] ?></td>
<td><?= $row['time'] ?></td>
<!-- VULN: Price manipulation possible client-side, but amount displayed raw -->
<td>RM <?= number_format($row['amount'], 2) ?></td>

<td>
<?php if(!$is_enrolled): ?>
<form method="POST" action="enroll.php">
<input type="hidden" name="course_id" value="<?= $course_id ?>">
<button type="submit" class="enroll-btn">Enroll</button>
</form>
<?php else: ?>
<form method="POST" action="unenroll.php">
<input type="hidden" name="course_id" value="<?= $course_id ?>">
<button type="submit" class="unenroll-btn">Unenroll</button></form>
<?php endif; ?>

</td>



</tr>


<?php endwhile; ?>

</table>
  <!-- VULN: Payment button submits to payment.php without course selection -->
    <!-- VULN: User can pay without selecting a course -->

<form method="POST" action="payment.php">
<input type="hidden" name="student_id" value="<?= $student_id ?>">
<button type="submit" class="payment-btn">Payment</button></form>


<?php else: ?>

<p style="text-align:center; font-size:18px; margin-top:20px;">
    No courses available
</p>

<?php endif; ?>

</section>

<footer>
<p> Course Enrollment System | MyEduConnect</p>

</footer>

</body>
</html>