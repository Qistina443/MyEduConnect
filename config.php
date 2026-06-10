<?php
$db_host = 'db';           // ← MUST be 'db' (Docker service name)
$db_user = 'root';         // ← MySQL username
$db_pass = '';             // ← Empty password (vulnerability)
$db_name = 'myeduconnect_db';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>