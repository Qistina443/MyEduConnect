<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];
    $specialization = $_POST['specialization'] ?? NULL;

    $check = $conn->prepare("SELECT * FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $res = $check->get_result();

    if($res->num_rows > 0){
        echo "<script>alert('Email already exists');</script>";
    } else {

        $stmt = $conn->prepare("
        INSERT INTO users (name, email, password, role, specialization)
        VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("sssss", $name, $email, $password, $role, $specialization);
        $stmt->execute();

        echo "<script>alert('Registered Successfully'); window.location='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register</title>
<link rel="stylesheet" href="assets/style.css?v=1000">
</head>

<body>

<nav class="navbar">
    <div class="logo">MyEduConnect</div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="register.php" class="active">Register</a></li>
        <li><a href="login.php">Login</a></li>
    </ul>
</nav>

<section class="form-section">
    <div class="form-container">
        <h2>Create an Account</h2>

        <form method="POST">

            <input type="text" name="name" placeholder="Full Name" required>

            <input type="email" name="email" placeholder="Email" required>

            <input type="password" name="password" placeholder="Password" required>

            <select name="role" id="role" onchange="toggleSpec()" required>
                <option value="">Select Role</option>
                <option value="student">Student</option>
                <option value="instructor">Instructor</option>
                <option value="admin">Admin</option>
            </select>

            <input type="text" name="specialization" id="spec"
                   placeholder="Instructor Specialization"
                   style="display:none;">

            <button type="submit">Register</button>

        </form>
    </div>
</section>

<footer>
    <p> Course Enrollment System | MyEduConnect</p>
</footer>

<script>
function toggleSpec(){
    let role = document.getElementById("role").value;
    let spec = document.getElementById("spec");

    if(role === "instructor"){
        spec.style.display = "block";
        spec.required = true;
    } else {
        spec.style.display = "none";
        spec.required = false;
    }
}
</script>

</body>
</html>