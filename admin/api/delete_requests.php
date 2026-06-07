<?php

require_once '../../config/config.php';
require_once '../../config/constants.php';

header('Content-Type: application/json');

$payload = json_decode(
    file_get_contents('php://input'),
    true
);

$ids = $payload['ids'] ?? [];

if (
    !is_array($ids)
    || empty($ids)
) {
    echo json_encode([
        'success' => false,
        'message' => 'No requests selected.'
    ]);
    exit;
}

$ids = array_map(
    'intval',
    $ids
);

$placeholders = implode(
    ',',
    array_fill(
        0,
        count($ids),
        '?'
    )
);

$types = str_repeat(
    'i',
    count($ids)
);

$conn->begin_transaction();

try {

    /*
    |--------------------------------------------------------------------------
    | Delete Notifications
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        DELETE FROM request_notifications
        WHERE request_id IN ($placeholders)
    ");

    $stmt->bind_param(
        $types,
        ...$ids
    );

    $stmt->execute();

    /*
    |--------------------------------------------------------------------------
    | Delete Request Users
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        DELETE FROM request_users
        WHERE request_id IN ($placeholders)
    ");

    $stmt->bind_param(
        $types,
        ...$ids
    );

    $stmt->execute();

    /*
    |--------------------------------------------------------------------------
    | Delete Requests
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        DELETE FROM search_requests
        WHERE id IN ($placeholders)
    ");

    $stmt->bind_param(
        $types,
        ...$ids
    );

    $stmt->execute();

    $conn->commit();

    echo json_encode([
        'success' => true
    ]);

} catch (Exception $e) {

    $conn->rollback();

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}