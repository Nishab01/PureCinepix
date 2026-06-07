<?php

function tmdbFetch($endpoint) {
    $api = TMDB_API_KEY;
    $url = "https://api.themoviedb.org/3/$endpoint?api_key=$api";

    $res = @file_get_contents($url);
    return $res ? json_decode($res, true) : null;
}