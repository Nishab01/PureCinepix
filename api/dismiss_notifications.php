<?php

require_once '../config/config.php';
require_once '../config/constants.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$user = $_SESSION[SESSION_USER] ?? null;

if (!$user) {

    echo json_encode([
        'success' => false
    ]);

    exit;
}

$userId = (int)$user['id'];

$stmt = $conn->prepare("
    DELETE FROM request_notifications
    WHERE user_id = ?
");

$stmt->bind_param("i", $userId);
$stmt->execute();

echo json_encode([
    'success' => true
]);