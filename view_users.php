<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}


$result = $conn->query("SELECT id, name, email, role FROM users ORDER BY role, id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>All Users</title>
<link rel="stylesheet" href="assets/style.css?v=1000">

<style>
/* container */
.page-wrapper{
    flex: 1;
    display: flex;
    flex-direction: column;
}

/* table wrapper */
.users-container{
    width: 90%;
    margin: 30px auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
}

/* title */
.users-title{
    text-align: center;
    margin-top: 20px;
    font-size: 28px;
    color: #1e3a8a;
}

/* delete button */
.delete-btn{
    background: #dc2626;
    color: white;
    padding: 6px 10px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
}

.delete-btn:hover{
    background: #b91c1c;
}
</style>

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

<div class="page-wrapper">

<h2 class="users-title">All Users</h2>

<div class="users-container">

<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Role</th>
<th>Action</th>
</tr>

<?php while($u = $result->fetch_assoc()): ?>
<tr>
<td><?= $u['id'] ?></td>
<td><?= $u['name'] ?></td>
<td><?= $u['email'] ?></td>
<td><?= $u['role'] ?></td>

<td>
<a class="delete-btn"
   href="delete_user.php?id=<?= $u['id'] ?>"
   onclick="return confirm('Delete this user?')">
   Delete
</a>
</td>

</tr>
<?php endwhile; ?>

</table>

</div>

</div>

<!-- FOOTER -->
<footer>
<p> Course Enrollment System | MyEduConnect</p>
</footer>

</body>
</html>