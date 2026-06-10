<?php
// This file shows API documentation when accessed
header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html>
<head>
    <title>MyEduConnect REST API</title>
    <style>
        body { font-family: monospace; max-width: 900px; margin: 50px auto; padding: 20px; }
        h1 { color: #264adc; }
        .endpoint { background: #f0f0f0; padding: 15px; margin: 15px 0; border-left: 4px solid #264adc; }
        .method { display: inline-block; padding: 3px 8px; border-radius: 4px; font-weight: bold; }
        .get { background: #28a745; color: white; }
        .post { background: #dc3545; color: white; }
        code { background: #e0e0e0; padding: 2px 5px; border-radius: 3px; }
        .vuln-warning { background: #ffc107; padding: 10px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>🔌 MyEduConnect REST API v1.0</h1>
    
    <div class="vuln-warning">
        <strong>⚠️ SECURITY WARNING (Deliberate Vulnerabilities):</strong>
        <ul>
            <li>No authentication required for GET endpoints</li>
            <li>SQL Injection vulnerabilities present</li>
            <li>No rate limiting</li>
            <li>CORS misconfiguration (allows any domain)</li>
        </ul>
    </div>
    
    <h2>Available Endpoints</h2>
    
    <div class="endpoint">
        <span class="method get">GET</span>
        <strong><code>/api/get_courses.php</code></strong>
        <p>Get all courses with optional search and pagination</p>
        <strong>Parameters:</strong>
        <ul>
            <li><code>search</code> (optional) - Search term (VULN: SQL Injection)</li>
            <li><code>limit</code> (optional) - Results per page (default: 100)</li>
            <li><code>page</code> (optional) - Page number (default: 1)</li>
        </ul>
        <strong>Example:</strong> <code>GET /api/get_courses.php?search=math&limit=10</code>
    </div>
    
    <div class="endpoint">
        <span class="method get">GET</span>
        <strong><code>/api/get_course.php</code></strong>
        <p>Get a single course by ID</p>
        <strong>Parameters:</strong>
        <ul>
            <li><code>id</code> (required) - Course ID (VULN: SQL Injection)</li>
        </ul>
        <strong>Example:</strong> <code>GET /api/get_course.php?id=1</code>
    </div>
    
    <div class="endpoint">
        <span class="method get">GET</span>
        <strong><code>/api/get_enrollments.php</code></strong>
        <p>Get student enrollments (VULN: IDOR - any student ID)</p>
        <strong>Parameters:</strong>
        <ul>
            <li><code>student_id</code> (required) - Student ID to fetch enrollments for</li>
        </ul>
        <strong>Example:</strong> <code>GET /api/get_enrollments.php?student_id=1</code>
    </div>
    
    <div class="endpoint">
        <span class="method post">POST</span>
        <strong><code>/api/login.php</code></strong>
        <p>Authenticate user and get token</p>
        <strong>Body (JSON):</strong>
        <ul>
            <li><code>email</code> - User email</li>
            <li><code>password</code> - User password</li>
        </ul>
        <strong>Example:</strong>
        <pre>POST /api/login.php
Content-Type: application/json
{"email": "student@example.com", "password": "password123"}</pre>
    </div>
    
    <div class="endpoint">
        <span class="method post">POST</span>
        <strong><code>/api/enroll.php</code></strong>
        <p>Enroll student in course</p>
        <strong>Body (JSON):</strong>
        <ul>
            <li><code>student_id</code> - Student ID</li>
            <li><code>course_id</code> - Course ID</li>
        </ul>
    </div>
    
    <hr>
    
    <h2>Testing the API</h2>
    <p>Use these commands to test:</p>
    <pre>
# Get all courses
curl http://localhost/CourseEnrollmentSystem/api/get_courses.php

# SQL Injection demo
curl "http://localhost/CourseEnrollmentSystem/api/get_courses.php?search=math%27%20OR%20%271%27=%271"

# Get specific course
curl http://localhost/CourseEnrollmentSystem/api/get_course.php?id=1

# IDOR - View another student's enrollments
curl http://localhost/CourseEnrollmentSystem/api/get_enrollments.php?student_id=1

# Login
curl -X POST http://localhost/CourseEnrollmentSystem/api/login.php \
  -H "Content-Type: application/json" \
  -d '{"email":"student@example.com","password":"password123"}'
    </pre>
</body>
</html>