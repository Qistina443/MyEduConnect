<?php
include 'config.php';

$id = $_GET['id'];

$res = $conn->query("
SELECT u.name, u.email
FROM enrollments e
JOIN users u ON e.student_id = u.id
WHERE e.course_id = $id
");
?>

<h2>Students</h2>

<table border="1">
<tr>
<th>Name</th>
<th>Email</th>
</tr>

<?php while($s = $res->fetch_assoc()): ?>
<tr>
<td><?= $s['name'] ?></td>
<td><?= $s['email'] ?></td>
</tr>
<?php endwhile; ?>
</table>