<?php

require_once '../config/config.php';
require_once '../config/constants.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION[SESSION_USER] ?? null;

if (!$user) {
    header("Location: ../auth/login.php");
    exit;
}

$notificationId = (int)($_GET['id'] ?? 0);

if (!$notificationId) {
    header("Location: ../pages/");
    exit;
}

$userId = (int)$user['id'];

/*
|--------------------------------------------------------------------------
| Find Notification
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        rn.id,
        rn.request_id,
        rn.content_id,
        c.title
    FROM request_notifications rn
    INNER JOIN contents c
        ON c.id = rn.content_id
    WHERE rn.id = ?
    AND rn.user_id = ?
    LIMIT 1
");

$stmt->bind_param(
    "ii",
    $notificationId,
    $userId
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../pages/");
    exit;
}

$row = $result->fetch_assoc();

$requestId = (int)$row['request_id'];
$contentId = (int)$row['content_id'];
$title = urlencode($row['title']);

/*
|--------------------------------------------------------------------------
| Delete Notification
|--------------------------------------------------------------------------
*/

$deleteNotification = $conn->prepare("
    DELETE FROM request_notifications
    WHERE id = ?
    AND user_id = ?
");

$deleteNotification->bind_param(
    "ii",
    $notificationId,
    $userId
);

$deleteNotification->execute();

/*
|--------------------------------------------------------------------------
| Delete User Request
|--------------------------------------------------------------------------
*/

$deleteUserRequest = $conn->prepare("
    DELETE FROM request_users
    WHERE request_id = ?
    AND user_id = ?
");

$deleteUserRequest->bind_param(
    "ii",
    $requestId,
    $userId
);

$deleteUserRequest->execute();

/*
|--------------------------------------------------------------------------
| Check Remaining Users
|--------------------------------------------------------------------------
*/

$check = $conn->prepare("
    SELECT id
    FROM request_users
    WHERE request_id = ?
    LIMIT 1
");

$check->bind_param(
    "i",
    $requestId
);

$check->execute();

$remainingUsers = $check->get_result();

/*
|--------------------------------------------------------------------------
| Delete Request If Nobody Left
|--------------------------------------------------------------------------
*/

if ($remainingUsers->num_rows === 0) {

    $deleteRequest = $conn->prepare("
        DELETE FROM search_requests
        WHERE id = ?
    ");

    $deleteRequest->bind_param(
        "i",
        $requestId
    );

    $deleteRequest->execute();
}

/*
|--------------------------------------------------------------------------
| Redirect To Content
|--------------------------------------------------------------------------
*/

header(
    "Location: ../pages/content.php?slug={$contentId}&title={$title}"
);

exit;