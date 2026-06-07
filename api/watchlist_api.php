<?php
session_start();

require_once '../config/config.php';
require_once '../config/constants.php';

header('Content-Type: application/json');

// ✅ LOGIN CHECK
if (!isset($_SESSION[SESSION_USER])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Login required'
    ]);
    exit();
}

$user_id = $_SESSION[SESSION_USER]['id'];

// ✅ INPUT
$content_id = (int)($_POST['content_id'] ?? 0);
$action = $_POST['action'] ?? '';

if (!$content_id || !$action) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request'
    ]);
    exit();
}

// =========================
// ✅ ADD
// =========================
if ($action === 'add') {

    $stmt = $conn->prepare("
        INSERT IGNORE INTO watchlist (user_id, content_id)
        VALUES (?, ?)
    ");

    if (!$stmt) {
        echo json_encode(['status' => 'error']);
        exit();
    }

    $stmt->bind_param("ii", $user_id, $content_id);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'added'
        ]);
    } else {
        echo json_encode([
            'status' => 'error'
        ]);
    }
}

// =========================
// ✅ REMOVE
// =========================
elseif ($action === 'remove') {

    $stmt = $conn->prepare("
        DELETE FROM watchlist
        WHERE user_id = ? AND content_id = ?
    ");

    if (!$stmt) {
        echo json_encode(['status' => 'error']);
        exit();
    }

    $stmt->bind_param("ii", $user_id, $content_id);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'removed'
        ]);
    } else {
        echo json_encode([
            'status' => 'error'
        ]);
    }
}

// =========================
// ❌ INVALID
// =========================
else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid action'
    ]);
}