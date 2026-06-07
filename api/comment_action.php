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
$is_admin = $_SESSION['is_admin'] ?? 0;

// 📥 INPUT
$comment_id = (int)($_POST['comment_id'] ?? 0);
$action = $_POST['action'] ?? '';
$comment_text = trim($_POST['comment'] ?? '');

if (!$comment_id || !$action) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

// 🔍 GET OWNER
$stmt = $conn->prepare("SELECT user_id FROM comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Comment not found']);
    exit;
}

$row = $res->fetch_assoc();
$owner_id = (int)$row['user_id'];

// 🔐 PERMISSION
$is_owner = ($user_id === $owner_id);

// ==========================
// 🔥 ACTIONS
switch ($action) {

    // 🗑️ DELETE
    // case 'delete':
    //     if (!$is_owner) {
    //         echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    //         exit;
    //     }

    //     // delete comment + replies
    //     $stmt = $conn->prepare("DELETE FROM comments WHERE id = ? OR parent_id = ?");
    //     $stmt->bind_param("ii", $comment_id, $comment_id);
    //     break;
    case 'delete':
        if (!$is_owner && !$is_admin) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
        // delete comment + replies
        $stmt = $conn->prepare("DELETE FROM comments WHERE id = ? OR parent_id = ?");
        $stmt->bind_param("ii", $comment_id, $comment_id);
        break;

    // ✏️ EDIT
    case 'edit':
        if (!$is_owner) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        if (!$comment_text) {
            echo json_encode(['status' => 'error', 'message' => 'Empty comment']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE comments SET comment = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $comment_text, $comment_id);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit;
}

// ==========================
// ✅ EXECUTE
if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => $stmt->error
    ]);
}