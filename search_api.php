<?php
include './includes/config.php';

header('Content-Type: application/json');

$q = $_GET['q'] ?? '';
$q = $conn->real_escape_string($q);

if (empty($q)) {
    echo json_encode([]);
    exit;
}

$res = $conn->query("
    SELECT id, title, poster, release_date 
    FROM movies 
    WHERE title LIKE '%$q%' 
    LIMIT 6
");

$data = [];

while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);