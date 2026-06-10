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
$user_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['user_id'];

// VULN: SQL Injection - Direct concatenation
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// VULN: No check if user exists or if current user has permission
if (!$user) {
    // VULN: Reveals that user doesn't exist
    die("User not found with ID: $user_id");
}


// Check if viewing own profile or someone else's
$is_own_profile = ($user_id == $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile</title>
<link rel="stylesheet" href="assets/style.css?v=999">

<style>
     .profile-container {
            max-width: 1000px;
            margin: 0 auto;
        }
      .profile-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #264adc 0%, #1a3a8a 100%);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
        }
        
        .profile-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .profile-header .role-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin-top: 10px;
        }
         .edit-badge {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        
        .edit-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .edit-btn:hover {
            background: rgba(255,255,255,0.3);
        }
      .profile-body {
            padding: 30px;
        }
        
        .info-section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #264adc;
            border-left: 4px solid #264adc;
            padding-left: 15px;
            margin-bottom: 20px;
        }
      .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            transition: transform 0.3s;
        }
        
        .info-item:hover {
            transform: translateY(-2px);
        }
.info-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            word-break: break-word;
        }
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

  <?php if(!$is_own_profile): ?>
    <div style="background: yellow; padding: 10px;">
        ⚠️ You are viewing another student's profile (IDOR Vulnerability)
    </div>
    <?php endif; ?>

   
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <?php if($is_own_profile): ?>
             
                
                <?php endif; ?>
                <h1><?php echo htmlspecialchars($user['name']); ?></h1>
              
                <div class="role-badge">
                    <?php echo ucfirst($user['role']); ?>
                </div>
            </div>
            
            <div class="profile-body">
                <?php if(!$is_own_profile): ?>
                <div class="warning-banner">
                    ⚠️ You are viewing <strong><?php echo htmlspecialchars($user['name']); ?></strong>'s profile.
                    <?php if($_SESSION['role'] != 'admin'): ?>
                    <br>You do not have permission to edit this profile.
                    <?php endif; ?>
                </div>
                 <?php endif; ?>
                
                <div class="info-section">
                    <div class="section-title">Personal Information</div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Full Name</div>
                            <div class="info-value"><?php echo htmlspecialchars($user['name']); ?></div>
                            <div class="info-item">
                            <div class="info-label">Email Address</div>
                            <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                             <div class="info-item">
                            <div class="info-label">Account Type</div>
                            <div class="info-value"><?php echo ucfirst($user['role']); ?></div>
                        </div>
                        </div>
                    </div>
                </div>

  
</body>
</html>

