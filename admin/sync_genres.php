<?php
include '../includes/config.php';

$api_key = $TMDB_API_KEY;

$url = "https://api.themoviedb.org/3/genre/movie/list?api_key=$api_key";

$response = file_get_contents($url);
$data = json_decode($response, true);

foreach ($data['genres'] as $g) {

    $id = (int)$g['id']; // 🔥 IMPORTANT
    $name = $conn->real_escape_string($g['name']);

    $conn->query("
        INSERT INTO genres (id, name)
        VALUES ($id, '$name')
        ON DUPLICATE KEY UPDATE name='$name'
    ");
}

echo "Genres synced!";