<?php

require_once '../../config/config.php';
require_once '../../config/constants.php';

header('Content-Type: application/json');

$q = trim($_GET['q'] ?? '');

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$search = '%' . $q . '%';

$stmt = $conn->prepare("
    SELECT
        id,
        title,
        type,
        poster1,
        release_date,
        last_date
    FROM contents
    WHERE title LIKE ?
    ORDER BY title ASC
    LIMIT 20
");

$stmt->bind_param("s", $search);
$stmt->execute();

$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {

    $data[] = [
        'id'           => (int)$row['id'],
        'title'        => $row['title'],
        'type'         => $row['type'],
        'poster1'      => $row['poster1'],
        'release_date' => $row['release_date'],
        'last_date'    => $row['last_date']
    ];
}

echo json_encode($data);