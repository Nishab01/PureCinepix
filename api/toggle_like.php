<?php
session_start();
require_once '../config/config.php';
require_once '../config/constants.php';

header('Content-Type: application/json');

// 🔐 LOGIN CHECK
if (!isset($_SESSION[SESSION_USER]['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Login required']);
    exit;
}

$user_id = $_SESSION[SESSION_USER]['id'];
$comment_id = (int)($_POST['comment_id'] ?? 0);

if (!$comment_id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid comment']);
    exit;
}

// 🔍 CHECK EXISTING LIKE
$stmt = $conn->prepare("
    SELECT id FROM comment_likes 
    WHERE comment_id = ? AND user_id = ?
");
$stmt->bind_param("ii", $comment_id, $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    // ❌ UNLIKE
    $stmt = $conn->prepare("
        DELETE FROM comment_likes 
        WHERE comment_id = ? AND user_id = ?
    ");
    $stmt->bind_param("ii", $comment_id, $user_id);
    $stmt->execute();

    echo json_encode(['status' => 'unliked']);
} else {
    // ❤️ LIKE
    $stmt = $conn->prepare("
        INSERT INTO comment_likes (comment_id, user_id)
        VALUES (?, ?)
    ");
    $stmt->bind_param("ii", $comment_id, $user_id);
    $stmt->execute();

    echo json_encode(['status' => 'liked']);
}