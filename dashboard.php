<?php
session_start();

//for all users
if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

// for role=admin
if($_SESSION['role'] == 'admin'){
    header("Location: admin.php");
    exit();
}
elseif($_SESSION['role'] == 'student'){
    header("Location: student.php");
    exit();
}
elseif($_SESSION['role'] == 'instructor'){
    header("Location: instructor.php");
    exit();
}
else{
    
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
// ===== Student View =====
if($role == 'student'){
    echo "<h2>Student Dashboard</h2>";
    
    $result = $conn->query("SELECT * FROM courses");
    while($row = $result->fetch_assoc()){
        echo "<div class='course-card'>";
        echo "<h3>".$row['course_name']."</h3>";
        echo "<p>".$row['description']."</p>";
        echo "<p><strong>Schedule:</strong> ".$row['appointment']."</p>";
        
        echo "<form method='POST' action='enroll.php'>
                <input type='hidden' name='course_id' value='".$row['id']."'>
                <button type='submit'>Enroll</button>
              </form>";
        echo "</div>";
    }
}

// ===== Admin View =====
elseif($role == 'admin'){
    echo "<h2>Admin Dashboard</h2>";
    echo "<form method='POST' action='add_course.php'>
            <input type='text' name='course_name' placeholder='Course Name' required>
            <textarea name='description' placeholder='Description'></textarea>
            <input type='text' name='appointment' placeholder='Appointment'>
            <button type='submit'>Add Course</button>
          </form>";
   
    $result = $conn->query("SELECT * FROM courses");
    while($row = $result->fetch_assoc()){
        echo "<div class='course-card'>";
        echo "<h3>".$row['course_name']."</h3>";
        echo "<p>".$row['description']."</p>";
        echo "<p><strong>Schedule:</strong> ".$row['appointment']."</p>";
        echo "<a href='edit_course.php?id=".$row['id']."'>Edit</a> | ";
        echo "<a href='delete_course.php?id=".$row['id']."'>Delete</a>";
        echo "</div>";
    }
}

// ===== Instructor View =====
elseif($role == 'instructor'){
    echo "<h2>Instructor Dashboard</h2>";
    $result = $conn->query("
        SELECT courses.course_name, COUNT(enrollments.id) as total
        FROM courses
        LEFT JOIN enrollments ON courses.id = enrollments.course_id
        GROUP BY courses.id
    ");
    while($row = $result->fetch_assoc()){
        echo "<p>".$row['course_name']." - Registered Students: ".$row['total']."</p>";
    }
}
?>
</section>

<script src="assets/script.js"></script>
</body>
</html>
