<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND password=?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $row = $result->fetch_assoc();

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user'] = $row['name'];
        $_SESSION['role'] = $row['role'];

        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid Email or Password');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Course Enrollment</title>
<link rel="stylesheet" href="assets/style.css?v=1000">
</head>

<body>

<nav class="navbar">
    <div class="logo">MyEduConnect</div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="register.php">Register</a></li>
        <li><a href="login.php" class="active">Login</a></li>
    </ul>
</nav>

<!--  FIX: wrapper added -->
<main class="page-content">

<section class="form-section">
    <div class="form-container">
        <h2>Login</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</section>

</main>

<footer>
    <p> Course Enrollment System | MyEduConnect</p>
</footer>

</body>
</html>