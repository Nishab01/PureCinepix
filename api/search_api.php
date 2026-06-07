<?php


require_once '../config/config.php';

header('Content-Type: application/json');

$q = $_GET['q'] ?? '';

if (!$q) {
    echo json_encode([]);
    exit;
}

$search = "%$q%";
$starts = "$q%";

// $stmt = $conn->prepare("
//     SELECT id, title, poster1, type, release_date, last_date,

//     (
//         CASE
//             WHEN title = ? THEN 100
//             WHEN title LIKE ? THEN 80
//             WHEN title LIKE ? THEN 60
//             WHEN alt_titles LIKE ? THEN 40
//             WHEN keywords LIKE ? THEN 20
//             ELSE 0
//         END
//     ) AS score

//     FROM contents

//     WHERE
//         title LIKE ?
//         OR alt_titles LIKE ?
//         OR keywords LIKE ?

//     ORDER BY 
//         score DESC,
//         COALESCE(last_date, release_date) DESC

//     LIMIT 10
// ");

// $stmt->bind_param(
//     "ssssssss",
//     $q,
//     $starts,
//     $search,
//     $search,
//     $search,
//     $search,
//     $search,
//     $search
// );

$stmt = $conn->prepare("
    SELECT id, title, poster1, type, release_date, last_date,
    (
        (title = ?) * 100 +
        (title LIKE ?) * 80 +
        (title LIKE ?) * 60 +
        (alt_titles LIKE ?) * 40 +
        (keywords LIKE ?) * 20
    ) AS score
    FROM contents
    WHERE
        title LIKE ?
        OR alt_titles LIKE ?
        OR keywords LIKE ?
    ORDER BY 
        score DESC,
        COALESCE(last_date, release_date) DESC
    LIMIT 10
");

$stmt->bind_param(
    "ssssssss",
    $q,        // title exact
    $starts,   // title starts with
    $search,   // title contains
    $search,   // alt_titles
    $search,   // keywords
    $search,   // WHERE title
    $search,   // WHERE alt_titles
    $search    // WHERE keywords
);

$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);