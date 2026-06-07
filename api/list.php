<?php


session_start();
require_once '../config/config.php';

header('Content-Type: application/json');

// INPUTS
$page = max(1, (int)($_GET['page'] ?? 1));

$typesParam = $_GET['types'] ?? '';
$genres = $_GET['genres'] ?? '';
$categories = $_GET['categories'] ?? '';

$sort = $_GET['sort'] ?? 'latest';

// $limit = 18;
$limit = $paginationLimit;
$offset = ($page - 1) * $limit;

// BASE
$query = "FROM contents c
LEFT JOIN content_genres cg ON c.id = cg.content_id
LEFT JOIN content_categories cc ON c.id = cc.content_id";

$where = [];
$params = [];
$types = "";

// =========================
// TYPE FILTER (MULTIPLE)
// =========================
if (!empty($typesParam)) {

    $arr = explode(',', $typesParam);
    $placeholders = implode(',', array_fill(0, count($arr), '?'));

    $where[] = "c.type IN ($placeholders)";

    foreach ($arr as $t) {
        $params[] = $t;
        $types .= "s";
    }
}

// =========================
// GENRE FILTER
// =========================
if (!empty($genres)) {

    $arr = explode(',', $genres);
    $placeholders = implode(',', array_fill(0, count($arr), '?'));

    $where[] = "cg.genre_id IN ($placeholders)";

    foreach ($arr as $g) {
        $params[] = (int)$g;
        $types .= "i";
    }
}

// =========================
// CATEGORY FILTER
// =========================
if (!empty($categories)) {

    $arr = explode(',', $categories);
    $placeholders = implode(',', array_fill(0, count($arr), '?'));

    $where[] = "cc.category_id IN ($placeholders)";

    foreach ($arr as $c) {
        $params[] = (int)$c;
        $types .= "i";
    }
}

// FINAL WHERE
$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

// ORDER
if ($sort === 'az') {

    $order = "ORDER BY c.title ASC";

}
elseif ($sort === 'rating') {

    $order = "ORDER BY 
        CASE
            WHEN c.tmdb_rating IS NULL OR c.tmdb_rating = 0 THEN 1
            ELSE 0
        END,
        c.tmdb_rating DESC";

}
elseif ($sort === 'views') {

    $order = "ORDER BY c.total_views DESC";

}
elseif ($sort === 'updated') {

    $order = "ORDER BY COALESCE(c.downloads_updated_at, c.updated_at) DESC";

}
else {

    $order = "ORDER BY COALESCE(c.last_date, c.release_date) DESC";

}

// COUNT
$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT c.id) as total
    $query
    $whereSQL
");

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);

// DATA
$stmt = $conn->prepare("
    SELECT c.*
    $query
    $whereSQL
    GROUP BY c.id
    $order
    LIMIT ? OFFSET ?
");

$types2 = $types . "ii";
$params2 = array_merge($params, [$limit, $offset]);

$stmt->bind_param($types2, ...$params2);
$stmt->execute();
$result = $stmt->get_result();

// HTML
ob_start();

if ($result->num_rows > 0):
    while ($item = $result->fetch_assoc()):
        $content = $item;
        $isGrid = true;
        include '../includes/poster-card.php';
    endwhile;
endif;

$html = ob_get_clean();

// RESPONSE
echo json_encode([
    'status' => 'success',
    'html' => $html,
    'totalPages' => $totalPages
]);