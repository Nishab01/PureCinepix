<?php
require_once '../partials/guard.php';
require_once '../../config/config.php';
require_once '../../config/constants.php';

$message = "";

// GET PARAMS
$tmdb_id = $_GET['tmdb_id'] ?? null;
$type = $_GET['type'] ?? 'movie';

if (!$tmdb_id) {
    die("Invalid request");
}

// CHECK DUPLICATE
$stmt = $conn->prepare("SELECT id FROM contents WHERE tmdb_id = ? AND type = ?");
$stmt->bind_param("is", $tmdb_id, $type);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $existing = $res->fetch_assoc();
    header("Location: manage.php?id=" . $existing['id'] . "&type=" . $type);
    exit;
}

// FETCH FROM TMDB (OPTIMIZED)
$api_key = TMDB_API_KEY;

if ($type === 'movie') {
    $url = "https://api.themoviedb.org/3/movie/$tmdb_id?api_key=$api_key&append_to_response=images,keywords,alternative_titles,videos&include_image_language=en,null";
} else {
    $url = "https://api.themoviedb.org/3/tv/$tmdb_id?api_key=$api_key&append_to_response=images,keywords,alternative_titles,videos&include_image_language=en,null";
}

$response = @file_get_contents($url);

if (!$response) {
    die("Failed to fetch TMDB data");
}

$data = json_decode($response, true);

/* =========================
   BASIC DATA
========================= */
$title = $type === 'movie' ? $data['title'] : $data['name'];
$description = $data['overview'] ?? '';
$release_date = $type === 'movie' ? $data['release_date'] : $data['first_air_date'];

/* =========================
   IMAGES
========================= */
$posters_en = [];
$posters_null = [];

$backdrops_en = [];
$backdrops_null = [];

if (!empty($data['images']['posters'])) {
    foreach ($data['images']['posters'] as $img) {

        $fullPath = "https://image.tmdb.org/t/p/w500" . $img['file_path'];

        if ($img['iso_639_1'] === 'en') {
            $posters_en[] = $fullPath;
        }

        if ($img['iso_639_1'] === null) {
            $posters_null[] = $fullPath;
        }
    }
}

if (!empty($data['images']['backdrops'])) {
    foreach ($data['images']['backdrops'] as $img) {

        $fullPath = "https://image.tmdb.org/t/p/original" . $img['file_path'];

        if ($img['iso_639_1'] === 'en') {
            $backdrops_en[] = $fullPath;
        }

        if ($img['iso_639_1'] === null) {
            $backdrops_null[] = $fullPath;
        }
    }
}

/* =========================
   FINAL IMAGE SELECTION
========================= */
$poster1 = $posters_en[0] ?? $posters_null[3] ?? '';
$poster2 = $posters_null[0] ?? '';
$poster3 = $posters_null[1] ?? '';
$poster4 = $posters_null[2] ?? '';

$backdrop1 = $backdrops_en[0] ?? $backdrops_null[0] ?? '';
$backdrop2 = $backdrops_null[0] ?? $backdrops_en[0] ?? '';
$backdrop3 = $backdrops_null[2] ?? $backdrops_null[1] ?? $backdrops_null[0] ?? $backdrops_en[0] ?? '';
$backdrop4 = $backdrops_null[3] ?? $backdrops_null[4];

// RATING
$rating = $data['vote_average'] ?? null;

// TRAILER
$trailer = '';

if (!empty($data['videos']['results'])) {
    foreach ($data['videos']['results'] as $v) {

        if ($v['type'] === 'Trailer' && $v['site'] === 'YouTube') {
            $trailer = "https://www.youtube.com/watch?v=" . $v['key'];
            break;
        }
    }
}

/* =========================
   KEYWORDS
========================= */
$keywords_list = [];

if (!empty($data['keywords']['keywords'])) {
    foreach ($data['keywords']['keywords'] as $k) {
        $keywords_list[] = strtolower($k['name']);
    }
} elseif (!empty($data['keywords']['results'])) { // TV fallback
    foreach ($data['keywords']['results'] as $k) {
        $keywords_list[] = strtolower($k['name']);
    }
}

$keywords_string = implode(', ', $keywords_list);

/* =========================
   ALTERNATIVE TITLES
========================= */
$alt_list = [];

// 🔥 ADD CLEAN MAIN TITLE FIRST
$clean_title = strtolower(preg_replace('/[^a-zA-Z0-9 ]/', '', $title));
$alt_list[] = $clean_title;

if (!empty($data['alternative_titles']['titles'])) {
    foreach ($data['alternative_titles']['titles'] as $t) {
        $alt_list[] = strtolower($t['title']);
    }
} elseif (!empty($data['alternative_titles']['results'])) { // TV fallback
    foreach ($data['alternative_titles']['results'] as $t) {
        $alt_list[] = strtolower($t['title']);
    }
}

$alt_list = array_unique($alt_list);
$alt_string = implode(', ', $alt_list);


$stmt = $conn->prepare("
        INSERT INTO contents
        (
            title, description,
            poster1, poster2, poster3, poster4,
            backdrop1, backdrop2, backdrop3, backdrop4,
            release_date, type, tmdb_id,
            keywords, alt_titles,
            tmdb_rating, tmdb_trailer
        )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "ssssssssssssissds",
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
    $type,
    $tmdb_id,
    $keywords_string,
    $alt_string,
    $rating,
    $trailer
);

$stmt->execute();
$content_id = $stmt->insert_id;

/* =========================
   INSERT GENRES
========================= */
if (!empty($data['genres'])) {

    foreach ($data['genres'] as $g) {

        $gname = trim($g['name']);

        // Check if genre exists (case-insensitive)
        $stmt = $conn->prepare("SELECT id FROM genres WHERE LOWER(name) = LOWER(?)");
        $stmt->bind_param("s", $gname);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($row = $res->fetch_assoc()) {

            $genre_id = $row['id'];

        } else {

            // Insert new genre
            $stmt = $conn->prepare("INSERT INTO genres (name) VALUES (?)");
            $stmt->bind_param("s", $gname);
            $stmt->execute();

            $genre_id = $stmt->insert_id;
        }

        // Insert into pivot table
        $stmt = $conn->prepare("
            INSERT INTO content_genres (content_id, genre_id)
            VALUES (?, ?)
        ");
        $stmt->bind_param("ii", $content_id, $genre_id);
        $stmt->execute();
    }
}
// REDIRECT
header("Location: manage.php?id=$content_id&type=$type");
exit;
?>