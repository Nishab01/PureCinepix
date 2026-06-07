<?php

require_once '../partials/guard.php';
require_once '../../config/config.php';
require_once '../../config/constants.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: addmanual.php");
    exit;
}

/* =========================
   GET DATA
========================= */

$title         = trim($_POST['title'] ?? '');
$type          = $_POST['type'] ?? 'movie';
$description   = trim($_POST['description'] ?? '');

$release_date  = $_POST['release_date'] ?? null;
$last_date     = $_POST['last_date'] ?? null;

$rating        = $_POST['rating'] ?? null;
$trailer       = trim($_POST['trailer'] ?? '');

$keywords      = trim($_POST['keywords'] ?? '');
$alt_titles    = trim($_POST['alt_titles'] ?? '');

$poster1       = trim($_POST['poster1'] ?? '');
$poster2       = trim($_POST['poster2'] ?? '');
$poster3       = trim($_POST['poster3'] ?? '');
$poster4       = trim($_POST['poster4'] ?? '');

$backdrop1     = trim($_POST['backdrop1'] ?? '');
$backdrop2     = trim($_POST['backdrop2'] ?? '');
$backdrop3     = trim($_POST['backdrop3'] ?? '');
$backdrop4     = trim($_POST['backdrop4'] ?? '');

$genres        = $_POST['genres'] ?? [];
$categories    = $_POST['categories'] ?? [];

/* =========================
   VALIDATION
========================= */

if (
    empty($title) ||
    empty($type) ||
    empty($release_date) ||
    empty($poster1)
) {
    die("Required fields missing.");
}

/* =========================
   DUPLICATE CHECK
========================= */

$stmt = $conn->prepare("
    SELECT id
    FROM contents
    WHERE LOWER(title) = LOWER(?)
    LIMIT 1
");

$stmt->bind_param("s", $title);
$stmt->execute();

$existing = $stmt->get_result()->fetch_assoc();

if ($existing) {
    die("Content with this title already exists.");
}

/* =========================
   INSERT CONTENT
========================= */

$stmt = $conn->prepare("
    INSERT INTO contents
    (
        title,
        description,

        poster1,
        poster2,
        poster3,
        poster4,

        backdrop1,
        backdrop2,
        backdrop3,
        backdrop4,

        release_date,
        last_date,

        type,

        tmdb_rating,
        tmdb_trailer,

        keywords,
        alt_titles
    )
    VALUES
    (
        ?, ?,

        ?, ?, ?, ?,

        ?, ?, ?, ?,

        ?, ?,

        ?,

        ?, ?,

        ?, ?
    )
");

$stmt->bind_param(
    "sssssssssssssdsss",

    $title,
    $description,

    $poster1,
    $poster2,
    $poster3,
    $poster4,

    $backdrop1,
    $backdrop2,
    $backdrop3,
    $backdrop4,

    $release_date,
    $last_date,

    $type,

    $rating,
    $trailer,

    $keywords,
    $alt_titles
);

$stmt->execute();

$content_id = $stmt->insert_id;

/* =========================
   SAVE GENRES
========================= */

if (!empty($genres)) {

    $stmtGenre = $conn->prepare("
        INSERT INTO content_genres
        (
            content_id,
            genre_id
        )
        VALUES (?, ?)
    ");

    foreach ($genres as $genre_id) {

        $genre_id = (int)$genre_id;

        $stmtGenre->bind_param(
            "ii",
            $content_id,
            $genre_id
        );

        $stmtGenre->execute();
    }
}

/* =========================
   SAVE CATEGORIES
========================= */

if (!empty($categories)) {

    $stmtCat = $conn->prepare("
        INSERT INTO content_categories
        (
            content_id,
            category_id
        )
        VALUES (?, ?)
    ");

    foreach ($categories as $category_id) {

        $category_id = (int)$category_id;

        $stmtCat->bind_param(
            "ii",
            $content_id,
            $category_id
        );

        $stmtCat->execute();
    }
}

/* =========================
   REDIRECT TO MANAGE
========================= */

header(
    "Location: manage.php?id=" .
    $content_id .
    "&type=" .
    urlencode($type)
);

exit;

