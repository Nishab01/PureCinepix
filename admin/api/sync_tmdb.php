<?php
require_once '../../config/config.php';
require_once '../../config/constants.php';

header('Content-Type: application/json');

/* =========================
   VALIDATE INPUT
========================= */
if (!isset($_POST['content_id'])) {
    echo json_encode(['status' => 'error', 'msg' => 'No ID']);
    exit;
}

$content_id = (int)$_POST['content_id'];

/* =========================
   FETCH CONTENT
========================= */
$stmt = $conn->prepare("
    SELECT tmdb_id, type, tmdb_last_updated
    FROM contents
    WHERE id = ?
");

$stmt->bind_param("i", $content_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Not found']);
    exit;
}

$content = $res->fetch_assoc();

/* =========================
   SKIP IF RECENT (24h)
========================= */
if (!empty($content['tmdb_last_updated']) &&
    (time() - strtotime($content['tmdb_last_updated'])) < 86400) {

    echo json_encode([
        'status' => 'skipped',
        'msg' => 'Recently updated'
    ]);
    exit;
}

/* =========================
   VALIDATE TMDB ID
========================= */
if (!$content['tmdb_id']) {
    echo json_encode(['status' => 'skipped', 'msg' => 'No TMDB ID']);
    exit;
}

$api_key = TMDB_API_KEY;

/* =========================
   SINGLE API CALL (OPTIMIZED)
========================= */
$url = ($content['type'] === 'movie')
    ? "https://api.themoviedb.org/3/movie/{$content['tmdb_id']}?api_key=$api_key&append_to_response=images,keywords,alternative_titles,videos&include_image_language=en,null"
    : "https://api.themoviedb.org/3/tv/{$content['tmdb_id']}?api_key=$api_key&append_to_response=images,keywords,alternative_titles,videos&include_image_language=en,null";

$response = @file_get_contents($url);

if (!$response) {
    echo json_encode(['status' => 'error', 'msg' => 'API failed']);
    exit;
}

$data = json_decode($response, true);

/* =========================
   RATING + TRAILER
========================= */
$rating = $data['vote_average'] ?? null;

$trailer = null;
if (!empty($data['videos']['results'])) {
    foreach ($data['videos']['results'] as $video) {
        if ($video['type'] === 'Trailer' && $video['site'] === 'YouTube') {
            $trailer = $video['key'];
            break;
        }
    }
}

/* =========================
   LAST AIRED DATE
========================= */
$last_date = ($content['type'] === 'tv')
    ? ($data['last_air_date'] ?? null)
    : null;


/* =========================
   IMAGE PROCESSING (OPTIMIZED)
========================= */
$posters_en = [];
$posters_all = [];
$backdrops_en = [];
$backdrops_null = [];

/* POSTERS */
if (!empty($data['images']['posters'])) {
    foreach ($data['images']['posters'] as $img) {

        $full = "https://image.tmdb.org/t/p/w500" . $img['file_path'];

        $posters_all[] = $full;

        if ($img['iso_639_1'] === 'en') {
            $posters_en[] = $full;
        }

        if (count($posters_all) >= 5 && count($posters_en) >= 5) break;
    }
}

/* SELECT POSTERS */
$poster1 = $posters_en[0] ?? $posters_all[0] ?? null;
$poster2 = $posters_en[1] ?? $posters_all[1] ?? null;
$poster3 = $posters_en[2] ?? $posters_all[2] ?? null;
$poster4 = $posters_en[3] ?? $posters_all[3] ?? null;

/* BACKDROPS */
if (!empty($data['images']['backdrops'])) {
    foreach ($data['images']['backdrops'] as $img) {

        $full = "https://image.tmdb.org/t/p/original" . $img['file_path'];

        if ($img['iso_639_1'] === 'en') {
            $backdrops_en[] = $full;
        } elseif ($img['iso_639_1'] === null) {
            $backdrops_null[] = $full;
        }

        if (count($backdrops_en) >= 5 && count($backdrops_null) >= 5) break;
    }
}

// /* SELECT BACKDROPS */
// $backdrop1 = $backdrops_en[0] ?? $backdrops_null[0] ?? null;
// $backdrop2 = $backdrops_null[0] ?? $backdrops_en[0] ?? null;
// $backdrop3 = $backdrops_null[2] ?? $backdrops_null[1] ?? $backdrops_null[0] ?? $backdrops_en[0] ?? null;
// $backdrop4 = $backdrops_null[3] ?? $backdrops_null[4] ?? $backdrops_null[4] ?? null;

/* =========================
   UPDATE DB
========================= */
$stmt = $conn->prepare("
    UPDATE contents
    SET
        tmdb_rating = ?,
        tmdb_trailer = ?,
        last_date = COALESCE(?, last_date),
        poster1 = COALESCE(?, poster1),
        poster2 = COALESCE(?, poster2),
        poster3 = COALESCE(?, poster3),
        poster4 = COALESCE(?, poster4),
        tmdb_last_updated = NOW()
    WHERE id = ?
");
// $stmt = $conn->prepare("
//     UPDATE contents
//     SET
//         tmdb_rating = ?,
//         tmdb_trailer = ?,
//         last_date = COALESCE(?, last_date),
//         poster1 = COALESCE(?, poster1),
//         poster2 = COALESCE(?, poster2),
//         poster3 = COALESCE(?, poster3),
//         poster4 = COALESCE(?, poster4),
//         backdrop1 = COALESCE(?, backdrop1),
//         backdrop2 = COALESCE(?, backdrop2),
//         backdrop3 = COALESCE(?, backdrop3),
//         backdrop4 = COALESCE(?, backdrop4),
//         tmdb_last_updated = NOW()
//     WHERE id = ?
// ");

$stmt->bind_param(
    "dssssssi",
    $rating,
    $trailer,
    $last_date,
    $poster1,
    $poster2,
    $poster3,
    $poster4,
    $content_id
);
// $stmt->bind_param(
//     "dssssssssssi",
//     $rating,
//     $trailer,
//     $last_date,
//     $poster1,
//     $poster2,
//     $poster3,
//     $poster4,
//     $backdrop1,
//     $backdrop2,
//     $backdrop3,
//     $backdrop4,
//     $content_id
// );

$stmt->execute();

/* =========================
   RESPONSE
========================= */
echo json_encode([
    'status' => 'success',
    'id' => $content_id
]);