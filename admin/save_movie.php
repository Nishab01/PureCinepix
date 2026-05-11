<?php

include '../includes/config.php';

$title = $conn->real_escape_string($_POST['title']);
$description = $conn->real_escape_string($_POST['description']);
$date = $conn->real_escape_string($_POST['release_date']);
$tmdb_id = (int)$_POST['tmdb_id'];

$poster = "https://image.tmdb.org/t/p/w500" . $conn->real_escape_string($_POST['poster']);
$backdrop = "https://image.tmdb.org/t/p/original" . $conn->real_escape_string($_POST['backdrop']);
$date = $_POST['release_date'];

$conn->query("INSERT INTO movies (title, description, poster, backdrop, release_date, tmdb_id)
VALUES ('$title', '$description', '$poster', '$backdrop', '$date', $tmdb_id)");

$movie_id = $conn->insert_id;

/* LINKS */
$qualities = $_POST['quality'];
$urls = $_POST['url'];

for ($i = 0; $i < count($urls); $i++) {

    $q = $conn->real_escape_string($qualities[$i]);
    $u = $conn->real_escape_string($urls[$i]);

    if (!empty($u)) {
        $conn->query("INSERT INTO movie_links (movie_id, quality, url)
        VALUES ($movie_id, '$q', '$u')");
    }
}


/* CATEGORIES */
$categories = $_POST['categories'] ?? [];

foreach ($categories as $cid) {
    $conn->query("INSERT INTO movie_categories (movie_id, category_id)
    VALUES ($movie_id, $cid)");
}

/* GENRES */
$genres = $_POST['genres'];

// Convert string "[12,28,878]" → array
$genres = str_replace(['[', ']'], '', $genres);
$genres = explode(',', $genres);

if (!empty($genres)) {
    foreach ($genres as $gid) {
        $conn->query("INSERT INTO movie_genres (movie_id, genre_id)
        VALUES ($movie_id, $gid)");
    }
}

header("Location: search.php");