<?php
session_start();

if(isset($_SESSION['role'])){
    if($_SESSION['role'] == 'admin'){
        header("Location: admin.php");
    }
    elseif($_SESSION['role'] == 'instructor'){
        header("Location: instructor.php");
    }
    else{
        header("Location: student.php");
    }
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Home - MyEduConnect</title>
<link rel="stylesheet" href="assets/style.css?v=1000">

<style>
/* ===== HERO SECTION ===== */
.hero {
    height: 80vh;
    display: flex;
    justify-content: center;  
    align-items: center;       
    text-align: center;        
}

.hero-text {
    max-width: 600px;
}

.hero-text h1 {
    font-size: 42px;
    color: #1e3a8a;
    margin-bottom: 15px;
}

.hero-text p {
    font-size: 18px;
    color: #555;
}
</style>

</head>

<body>

<!-- NAVBAR -->
<nav class="navbar">
<div class="logo">MyEduConnect</div>
<ul class="nav-links">
<li><a href="index.php" class="active">Home</a></li>
<li><a href="register.php">Register</a></li>
<li><a href="login.php">Login</a></li>
</ul>
</nav>

<!-- HERO -->
<section class="hero">
<div class="hero-text">
    <h1>MyEduConnect</h1>
    <p>Manage courses, students, and instructors in a simple and efficient way.</p>
</div>
</section>

<!-- FOOTER -->
<footer>
<p Course Enrollment System | MyEduConnect</p>

</footer>

</body>
</html>