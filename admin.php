<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    
}

$admin_id = $_SESSION['user_id'];

$error = "";
$instructor_error = "";
$amount_error = "";

// ================= ADD COURSE =================
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $course_name = $_POST['course_name'];
    $day = $_POST['day'];
    $time = trim($_POST['time']);
    $description = $_POST['description'];
    $instructor_name = $_POST['instructor_name'];
    $amount = $_POST['amount'];  // VULN: No validation - can be any string

      if (!preg_match('/^(0?[1-9]|1[0-2]):[0-5][0-9]-(0?[1-9]|1[0-2]):[0-5][0-9](AM|PM)$/', $time)) {
    $error = " Invalid format. Use HH:MM-HH:MMAM/PM (Example: 11:00-12:00PM)";
}

 // VULN: Amount validation missing - allows SQL injection
    if (!is_numeric($amount) && $amount != "") {
        $amount_error = "Amount must be a number";
    }

    // ================= ONLY CONTINUE IF NO ERROR =================
    if ($error == "" && $instructor_error == ""&& $amount_error == "") {

        $amount_clean = floatval($amount); // Weak conversion

        $stmt = $conn->prepare("SELECT * FROM users WHERE name=? AND role='instructor'");
        $stmt->bind_param("s", $instructor_name);
        $stmt->execute();
        $res = $stmt->get_result();

       if($res->num_rows == 0){
    $instructor_error = " Instructor not found";
} else {

    $instructor = $res->fetch_assoc();

    if(strtolower($instructor['specialization']) != strtolower($course_name)){
        $instructor_error = "This instructor does not teach this course";
    }
}

        $instructor_id = $instructor['id'];

        
        $stmt = $conn->prepare("
            INSERT INTO courses (course_name, day, time, description, amount, instructor_id, admin_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("ssssiid", $course_name, $day, $time, $description, $amount_clean, $instructor_id, $admin_id);
        $stmt->execute();
    }
}

// ================= ACTIVE COURSES =================
$active = $conn->query("
SELECT c.*,
u.name AS instructor_name,
a.name AS admin_name,
(SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) AS total_students
FROM courses c
LEFT JOIN users u ON c.instructor_id = u.id
LEFT JOIN users a ON c.admin_id = a.id
WHERE c.id NOT IN (SELECT course_id FROM deleted_courses)
ORDER BY c.id DESC
");

// VULN: No error checking on query execution
if (!$active) {
    // VULN: Exposes database error
    die("Query failed: " . $conn->error);
}


?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Panel</title>
<link rel="stylesheet" href="assets/style.css?v=1000">
</head>

<body>

<nav class="navbar">
<div class="logo">MyEduConnect</div>
<ul class="nav-links">
<li><a href="admin.php">Admin Dashboard</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
</nav>
<div class="admin-actions">
    <a href="view_users.php" class="view-deleted-btn">
        View Users
    </a>

    <a href="deleted_courses.php" class="view-deleted-btn">
        View Deleted Courses
    </a>
</div>
<div class="page-wrapper">

    <section class="dashboard page-content">
        <h2>Add Course</h2>

        <form method="POST" class="add-course-form">

        <input type="text" name="course_name" placeholder="Course Name" required>
        <label for="day">Choose a day:</label>
        <select name="day" id="day" required>
            <option value="Monday">Monday</option>
            <option value="Tuesday">Tuesday</option>
            <option value="Wednesday">Wednesday</option>
            <option value="Thrusday">Thursday</option>
            <option value="Friday">Friday</option>
        </select>
        

        <input type="text" name="time" placeholder="Time AM/PM" required>

        <?php if($error != ""): ?>
            <div style="color:red; font-size:14px; margin-top:5px;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <input type="text" name="amount" placeholder="RM0.00" required>

        <textarea name="description" placeholder="Description"></textarea>

        <input type="text" name="instructor_name" placeholder="Search Instructor Name" required>
<?php if($instructor_error != ""): ?>
    <div style="color:red; font-size:14px; margin-top:5px;">
        <?= $instructor_error ?>
    </div>
<?php endif; ?>
        <button type="submit" class="add-btn">Add Course</button>

        </form>
    </section>

    <section class="dashboard page-content">
        <h2>Active Courses</h2>

        <table>
        <tr>
        <th>Course</th>
        <th>Day</th>
        <th>Time</th>
        <th>Instructor</th>
        <th>Students</th>
        <th>Amount</th>
        <th>Action</th>
        </tr>

        <?php while($c = $active->fetch_assoc()): ?>
        <tr>
        <td><?= $c['course_name'] ?></td>
        <td><?= $c['day'] ?></td>
        <td><?= $c['time'] ?></td>
        <td><?= $c['instructor_name'] ?></td>
        <td><?= $c['total_students'] ?></td>
        <td>RM <?= $c['amount'] ?></td>
        <td>
        <a href="view_students.php?course_id=<?= $c['id'] ?>">View</a> |
        <a href="delete_course.php?id=<?= $c['id'] ?>">Delete</a>
        </td>
        </tr>
        <?php endwhile; ?>

        </table>
    </section>

</div>

<footer>
<p>Course Enrollment System | MyEduConnect</p>


</footer>

</body>
</html>