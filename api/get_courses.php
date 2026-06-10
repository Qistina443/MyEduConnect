<?php
require_once 'config.php';

// VULN: No authentication required - anyone can access
// VULN: SQL Injection vulnerability

$search = isset($_GET['search']) ? $_GET['search'] : '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// VULN: Direct concatenation - SQL Injection
$where_clause = "";
if ($search) {
    // DANGEROUS: Search parameter directly concatenated
    $where_clause = "WHERE course_name LIKE '%$search%' OR description LIKE '%$search%'";
}

$query = "SELECT id, course_name, description, day, time, amount
          FROM courses 
          $where_clause 
          ORDER BY id DESC 
          LIMIT $limit OFFSET $offset";

$result = $conn->query($query);

if (!$result) {
    // VULN: Exposes database error
    send_error("Database error: " . $conn->error, 500);
}

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

// Get total count (for pagination)
$count_query = "SELECT COUNT(*) as total FROM courses $where_clause";
$count_result = $conn->query($count_query);
$total = $count_result->fetch_assoc()['total'];

send_success([
    'courses' => $courses,
    'pagination' => [
        'current_page' => $page,
        'per_page' => $limit,
        'total' => (int)$total,
        'total_pages' => ceil($total / $limit)
    ]
]);
?>