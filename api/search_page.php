<?php
require_once '../config/config.php';
require_once '../config/constants.php';

header('Content-Type: application/json');

$q = trim($_GET['q'] ?? '');

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = $paginationLimit;
$offset = ($page - 1) * $limit;

if (!$q) {
    echo json_encode([
        'html' => '',
        'related_html' => '',
        'totalPages' => 0,
        'totalResults' => 0
    ]);
    exit;
}

$search = "%{$q}%";
$starts = "{$q}%";

/* =========================
   TOTAL RESULTS
========================= */

$countStmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM contents
    WHERE
        title LIKE ?
        OR alt_titles LIKE ?
        OR keywords LIKE ?
");

$countStmt->bind_param(
    "sss",
    $search,
    $search,
    $search
);

$countStmt->execute();

$totalResults = $countStmt
    ->get_result()
    ->fetch_assoc()['total'];

$totalPages = max(1, ceil($totalResults / $limit));

/* =========================
   SEARCH RESULTS
========================= */

$stmt = $conn->prepare("
    SELECT *,
    (
        (LOWER(title) = LOWER(?)) * 100 +
        (title LIKE ?) * 90 +
        (title LIKE ?) * 80 +
        (alt_titles LIKE ?) * 60 +
        (keywords LIKE ?) * 40
    ) AS score
    FROM contents
    WHERE
        title LIKE ?
        OR alt_titles LIKE ?
        OR keywords LIKE ?
    ORDER BY
        score DESC,
        COALESCE(last_date, release_date) DESC
    LIMIT ? OFFSET ?
");

$stmt->bind_param(
    "ssssssssii",
    $q,
    $starts,
    $search,
    $search,
    $search,
    $search,
    $search,
    $search,
    $limit,
    $offset
);

$stmt->execute();

$result = $stmt->get_result();

/* =========================
   MAIN RESULTS HTML
========================= */

$searchIds = [];
$topContentId = null;

ob_start();

while ($item = $result->fetch_assoc()) {

    $searchIds[] = $item['id'];

    if (!$topContentId) {
        $topContentId = $item['id'];
    }

    $isGrid = true;

    include '../includes/poster-card.php';
}

$html = ob_get_clean();

/* =========================
   MORE LIKE THIS
========================= */

$relatedHtml = '';

if ($topContentId) {

    $genres = [];
    $categories = [];
    $keywords = [];

    /* GENRES */

    $genreRes = $conn->query("
        SELECT genre_id
        FROM content_genres
        WHERE content_id = {$topContentId}
    ");

    while ($row = $genreRes->fetch_assoc()) {
        $genres[] = (int)$row['genre_id'];
    }

    /* CATEGORIES */

    $catRes = $conn->query("
        SELECT category_id
        FROM content_categories
        WHERE content_id = {$topContentId}
    ");

    while ($row = $catRes->fetch_assoc()) {
        $categories[] = (int)$row['category_id'];
    }

    /* KEYWORDS */

    $keywordRes = $conn->query("
        SELECT keywords
        FROM contents
        WHERE id = {$topContentId}
        LIMIT 1
    ");

    if ($keywordRes->num_rows) {

        $kw = $keywordRes->fetch_assoc()['keywords'];

        if (!empty($kw)) {
            $keywords = array_map('trim', explode(',', $kw));
        }
    }

    $where = [];
    $exclude = '';

    if (!empty($searchIds)) {
        $exclude = "AND c.id NOT IN (" . implode(',', $searchIds) . ")";
    }

    if ($genres) {

        $where[] = "
            cg.genre_id IN (
                " . implode(',', $genres) . "
            )
        ";
    }

    if ($categories) {

        $where[] = "
            cc.category_id IN (
                " . implode(',', $categories) . "
            )
        ";
    }

    foreach ($keywords as $kw) {

        $kw = $conn->real_escape_string(trim($kw));

        if ($kw !== '') {
            $where[] = "c.keywords LIKE '%{$kw}%'";
        }
    }

    if ($where) {

        $relatedQuery = "
            SELECT DISTINCT c.*
            FROM contents c
            LEFT JOIN content_genres cg
                ON c.id = cg.content_id
            LEFT JOIN content_categories cc
                ON c.id = cc.content_id
            WHERE (
                " . implode(' OR ', $where) . "
            )
            {$exclude}
            ORDER BY
                COALESCE(c.last_date, c.release_date) DESC
            LIMIT 12
        ";

        $relatedRes = $conn->query($relatedQuery);

        ob_start();

        while ($item = $relatedRes->fetch_assoc()) {

            include '../includes/backdrop-card.php';
        }

        $relatedHtml = ob_get_clean();
    }
}

/* =========================
   RESPONSE
========================= */

echo json_encode([
    'html' => $html,
    'related_html' => $relatedHtml,
    'totalPages' => $totalPages,
    'totalResults' => $totalResults
]);