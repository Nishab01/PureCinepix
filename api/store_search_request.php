<?php
require_once '../config/config.php';
require_once '../config/constants.php';

header('Content-Type: application/json');

$q = trim($_POST['search_text'] ?? '');

if ($q === '') {
    echo json_encode([
        'success' => false
    ]);
    exit;
}

$normalized = strtolower($q);
$normalized = preg_replace('/[^a-z0-9]+/', ' ', $normalized);
$normalized = trim($normalized);

$stmt = $conn->prepare("
    SELECT id
    FROM search_requests
    WHERE normalized_text = ?
    LIMIT 1
");

$stmt->bind_param("s", $normalized);
$stmt->execute();

$res = $stmt->get_result();

if ($res->num_rows === 0) {

    $insert = $conn->prepare("
        INSERT INTO search_requests
        (
            search_text,
            normalized_text,
            status
        )
        VALUES
        (
            ?,
            ?,
            'pending'
        )
    ");

    $insert->bind_param(
        "ss",
        $q,
        $normalized
    );

    $insert->execute();
}

// echo json_encode([
//     'success' => true
// ]);

$requestId = 0;

$get = $conn->prepare("
    SELECT id
    FROM search_requests
    WHERE normalized_text = ?
    LIMIT 1
");

$get->bind_param("s", $normalized);
$get->execute();

$row = $get
    ->get_result()
    ->fetch_assoc();

if ($row) {
    $requestId = (int)$row['id'];
}

echo json_encode([
    'success' => true,
    'request_id' => $requestId
]);