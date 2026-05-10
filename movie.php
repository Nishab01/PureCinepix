<?php
include './includes/config.php';
include './includes/header.php';
include './includes/navbar.php';

$id = $_GET['id'] ?? 0;

$movie = $conn->query("SELECT * FROM movies WHERE id = $id")->fetch_assoc();

$links = $conn->query("SELECT * FROM movie_links WHERE movie_id = $id");

$genres = $conn->query("
    SELECT genres.name FROM genres
    JOIN movie_genres ON genres.id = movie_genres.genre_id
    WHERE movie_genres.movie_id = $id
");

$related = $conn->query("
SELECT movies.*, COUNT(*) as match_count
FROM movies
JOIN movie_genres ON movies.id = movie_genres.movie_id
WHERE movie_genres.genre_id IN (
    SELECT genre_id FROM movie_genres WHERE movie_id = $id
)
AND movies.id != $id
GROUP BY movies.id
ORDER BY match_count DESC
LIMIT 6
");

$tmdb_id = $movie['tmdb_id'] ?? 0;

$trailer = null;

if ($tmdb_id) {
    $api_key = $TMDB_API_KEY;

    $url = "https://api.themoviedb.org/3/movie/$tmdb_id/videos?api_key=$api_key";
    $res = file_get_contents($url);
    $data = json_decode($res, true);

    foreach ($data['results'] as $v) {
        if ($v['type'] === 'Trailer' && $v['site'] === 'YouTube') {
            $trailer = $v['key'];
            break;
        }
    }
}

?>

<div class="mt-20">

    <!-- BACKDROP -->
    <div class="w-full h-[60vh] relative">
        <img src="<?= $movie['backdrop'] ?>" class="w-full h-full object-cover">

        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/70 to-transparent"></div>
    </div>

    <!-- CONTENT -->
    <div class="max-w-6xl mx-auto px-6 -mt-40 relative z-10 flex gap-8">

        <!-- Poster -->
        <img src="<?= $movie['poster'] ?>"
             class="w-[250px] rounded-xl shadow-lg object-contain ">

        <!-- Info -->
        <div>

            <h1 class="text-3xl font-bold mb-3">
                <?= $movie['title'] ?>
            </h1>

            <!-- Genres -->
            <div class="flex gap-2 mb-3 flex-wrap">
                <?php while($g = $genres->fetch_assoc()): ?>
                    <span class="text-sm bg-white/10 px-2 py-1 rounded">
                        <?= $g['name'] ?>
                    </span>
                <?php endwhile; ?>
            </div>

            <p class="text-white/80 mb-6 text-lg">
                <?= $movie['description'] ?>
            </p>

            <!-- LINKS -->
            <h3 class="text-lg font-semibold mb-2">Downlaod Links</h3>

            <div class="space-y-2">
                <?php while($l = $links->fetch_assoc()): ?>
                    <a href="<?= $l['url'] ?>" target="_blank"
                       class="block bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded text-lg font-semibold">
                        <?= $l['quality'] ?>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>

    </div>

    <div class="max-w-6xl mx-auto px-6">
        <?php if($trailer): ?>

            <div class="mt-6">

                <h3 class="text-lg font-semibold mb-2">Trailer</h3>

                <div class="w-full aspect-video">
                    <iframe 
                        class="w-full h-full rounded-lg"
                        src="https://www.youtube.com/embed/<?= $trailer ?>"
                        frameborder="0"
                        allowfullscreen>
                    </iframe>
                </div>

            </div>

        <?php endif; ?>
    </div>
    

</div>

<div class="max-w-6xl mx-auto px-6 mt-16">

    <h2 class="text-xl font-semibold mb-4">
        Related Movies
    </h2>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-5">

        <?php while($r = $related->fetch_assoc()): ?>

            <?php $m = $r; // important: reuse card.php ?>

            <?php include './includes/card.php'; ?>

        <?php endwhile; ?>

    </div>

</div>

<?php include './includes/footer.php'; ?>