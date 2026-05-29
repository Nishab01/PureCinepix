<?php
require_once '../config/config.php';

header('Content-Type: application/json');

$comment_id = (int)($_GET['comment_id'] ?? 0);

if (!$comment_id) {
    echo json_encode(['likes' => []]);
    exit;
}

$res = $conn->query("
    SELECT u.name
    FROM comment_likes cl
    JOIN users u ON cl.user_id = u.id
    WHERE cl.comment_id = $comment_id
");

$likes = [];

while ($row = $res->fetch_assoc()) {
    $likes[] = [
        'name' => htmlspecialchars($row['name'])
    ];
}

echo json_encode(['likes' => $likes]);