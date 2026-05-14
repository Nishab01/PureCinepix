<?php
session_start();
require_once './includes/config.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? 0;

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

$movie_id = $_POST['movie_id'] ?? 0;
$action = $_POST['action'] ?? '';

if (!$movie_id) {
    echo json_encode(['status' => 'error']);
    exit();
}

// ADD
if ($action === 'add') {

    $stmt = $conn->prepare("
        INSERT IGNORE INTO watchlist (user_id, movie_id)
        VALUES (?, ?)
    ");
    $stmt->bind_param("ii", $user_id, $movie_id);
    $stmt->execute();

    echo json_encode(['status' => 'added']);
    exit();
}

// REMOVE
if ($action === 'remove') {

    $stmt = $conn->prepare("
        DELETE FROM watchlist
        WHERE user_id = ? AND movie_id = ?
    ");
    $stmt->bind_param("ii", $user_id, $movie_id);
    $stmt->execute();

    echo json_encode(['status' => 'removed']);
    exit();
}

echo json_encode(['status' => 'error']);