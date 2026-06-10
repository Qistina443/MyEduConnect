<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';


$course_id = $_GET['course_id'] ?? null;

if (!$course_id) {
    die("No course selected");
}

$course_id = (int)$course_id;


if (!$conn) {
    die("DB Connection failed");
}

$result = $conn->query("
SELECT u.name, u.email
FROM enrollments e
JOIN users u ON e.student_id = u.id
WHERE e.course_id = $course_id
");

if (!$result) {
    die("SQL Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Students List</title>
<link rel="stylesheet" href="assets/style.css">
</head>

<body>

<h2 style="text-align:center; margin-top:20px;">Students List</h2>

<div style="width:60%; margin:20px auto; background:white; padding:20px; border-radius:10px;">

<?php if ($result->num_rows > 0): ?>

    <?php while($r = $result->fetch_assoc()): ?>
        <p>
             <?= $r['name'] ?> -  <?= $r['email'] ?>
        </p>
    <?php endwhile; ?>

<?php else: ?>
    <p>No students enrolled</p>
<?php endif; ?>

</div>

</body>
</html>