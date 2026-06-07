<?php

require_once '../config/config.php';
require_once '../config/constants.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$user = $_SESSION[SESSION_USER] ?? null;

/*
|--------------------------------------------------------------------------
| Login Required
|--------------------------------------------------------------------------
*/
if (!$user) {
    echo json_encode([
        'success' => false,
        'message' => 'Login required'
    ]);
    exit;
}

$requestId = (int)($_POST['request_id'] ?? 0);

if ($requestId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
    exit;
}

$userId = (int)$user['id'];

/*
|--------------------------------------------------------------------------
| Prevent Duplicate Request
|--------------------------------------------------------------------------
*/
$check = $conn->prepare("
    SELECT id
    FROM request_users
    WHERE request_id = ?
    AND user_id = ?
    LIMIT 1
");

$check->bind_param(
    "ii",
    $requestId,
    $userId
);

$check->execute();

if ($check->get_result()->num_rows > 0) {

    echo json_encode([
        'success' => false,
        'already_requested' => true
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Insert Request
|--------------------------------------------------------------------------
*/
$insert = $conn->prepare("
    INSERT INTO request_users
    (
        request_id,
        user_id
    )
    VALUES
    (
        ?,
        ?
    )
");

$insert->bind_param(
    "ii",
    $requestId,
    $userId
);

if (!$insert->execute()) {

    echo json_encode([
        'success' => false,
        'message' => 'Failed to save request'
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Success
|--------------------------------------------------------------------------
*/
echo json_encode([
    'success' => true,
    'already_requested' => false
]);