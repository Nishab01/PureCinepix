<?php
    require_once '../config/config.php';

    $id = $_GET['id'] ?? 0;

    $stmt = $conn->prepare("SELECT * FROM contents WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 0) {
        die("Content not found");
    }

    $content = $res->fetch_assoc();

    $title = htmlspecialchars($content['title']);
    $description = $content['description'] ?? '';
    $type = $content['type'] ?? '';

    $release = $content['release_date'] ?? '';
    $last = $content['last_date'] ?? '';

    $poster = $content['poster1'] ?? '';
    $backdrop = $content['backdrop1'] ?? '';

    $releaseFormatted = $release ? date('j M Y', strtotime($release)) : '';
    $lastFormatted = $last ? date('j M Y', strtotime($last)) : '';

    $date = ($type == 'movie') ? $releaseFormatted : $releaseFormatted . " - " . $lastFormatted;

    $genres = $conn->query("
        SELECT g.name FROM genres g
        JOIN content_genres cg ON g.id = cg.genre_id
        WHERE cg.content_id = $id ORDER BY name ASC
    ");

    $rating = null;

    if (!empty($content['tmdb_id'])) {

        $api_key = TMDB_API_KEY;

        if ($type === 'movie') {
            $url = "https://api.themoviedb.org/3/movie/{$content['tmdb_id']}?api_key=$api_key";
        } else {
            $url = "https://api.themoviedb.org/3/tv/{$content['tmdb_id']}?api_key=$api_key";
        }

        $response = @file_get_contents($url);

        if ($response) {
            $tmdb = json_decode($response, true);

            if (!empty($tmdb['vote_average'])) {
                $rating = $tmdb['vote_average'];
            }
        }
    }

    $isInWatchlist = null;

    include '../includes/header.php';
?>







<?php if ($type == 'movie'){
    $links = $conn->query("SELECT * FROM downloads WHERE content_id = $id");
}
?>









<div class="w-full h-[20vh] md:h-[60vh] relative">

    <img src="<?= $backdrop ?: 'https://images.pexels.com/photos/29508640/pexels-photo-29508640.jpeg' ?>"
         class="w-full h-full object-cover">

    <div class="backdrop-blur md:backdrop-blur-0 absolute inset-0 bg-gradient-to-t from-black via-black/70 to-transparent"></div>

</div>

<div class="max-w-[1400px] mx-auto px-4 -mt-20 md:-mt-40 relative z-10">

    <div class="bg-black/60 backdrop-blur-xl border border-white/10 rounded-2xl p-4 md:p-6">

        <div class="flex flex-col md:flex-row gap-4">

            <!-- POSTER (FIXED RATIO) -->
            <div class="w-[300px] flex-shrink-0 mx-auto md:mx-0">

                <div class="aspect-[2/3] w-full">
                    <img src="<?= $poster ?>"
                        class="w-full h-full object-cover rounded-xl shadow-lg">
                </div>

            </div>

            <!-- INFO -->
            <div class="flex-1">

                <!-- TITLE -->
                <h1 class="text-2xl md:text-4xl font-bold mb-2">
                    <?= $title ?>
                </h1>

                <!-- TYPE + RATING -->
                <div class="flex items-center gap-2 mb-2">

                    <!-- TYPE -->
                    <span class="flex items-center gap-1 bg-white/10 text-white/60 px-2 py-1 rounded-md text-sm">
                        <?= ($type == 'movie') ? 'Movie' : 'TV Show' ?>
                    </span>

                    <span class="text-white/60">•</span>

                    <!-- TMDB RATING -->
                    <span class="flex items-center gap-1 bg-white/10 text-white/60 px-2 py-1 rounded-md text-sm">
                        <span class="text-yellow-400">★</span>
                        <?= $rating ? number_format($rating, 1) : 'N/A' ?>
                    </span>

                    <span class="text-white/60">•</span>

                    <div class="flex items-center gap-1 bg-white/10 text-white/60 px-2 py-1 rounded-md text-sm">
                        <?= $date ?>
                    </div>
                </div>

                <!-- GENRES -->
                <div class="flex flex-wrap gap-2 mb-4">
                    <?php while($g = $genres->fetch_assoc()): ?>
                        <span class="text-sm bg-white/10 px-3 py-1 rounded-full border border-white/10">
                            <?= $g['name'] ?>
                        </span>
                    <?php endwhile; ?>
                </div>

                <!-- DESCRIPTION -->
                <p class="text-white/70 text-sm md:text-base leading-relaxed mb-5 text-justify">
                    <?= $description ?>
                </p>

                <!-- WATCHLIST BUTTON -->
                <button id="watchlistBtn"
                    data-movie-id="<?= $id ?>"
                    data-state="<?= $isInWatchlist ? 'added' : 'not-added' ?>"
                    class="bg-white/10 hover:bg-white/20 px-5 py-2 rounded-lg text-sm font-medium transition">

                    <?= $isInWatchlist ? '✓ In Watchlist' : '+ Add to Watchlist' ?>
                </button>

                <hr class="my-4 border-b border-white/40">

                <div>
                    <h3 class="text-xl font-semibold mb-4">Available Downloads</h3>

                    <div class="grid gap-3 sm:grid-cols-2">

                        <?php while($l = $links->fetch_assoc()): ?>

                            <a href="<?= $l['url'] ?>" target="_blank"
                            class="flex items-center justify-between bg-white/5 hover:bg-white/10 border border-white/10 px-4 py-3 rounded-xl transition backdrop-blur-md group">

                                <span class="font-medium text-white">
                                    <?= $l['quality'] ?>
                                </span>

                                <span class="text-sm text-blue-400 group-hover:translate-x-1 transition">
                                    Download →
                                </span>

                            </a>

                        <?php endwhile; ?>

                    </div>
                </div>

            </div>
        </div>

    </div>

</div>



<?php if(!empty($content['trailer'])): ?>
<div class="max-w-[1300px] mx-auto px-4 mt-6">

    <h3 class="text-xl font-semibold mb-3">Trailer</h3>

    <div class="aspect-video w-full rounded-xl overflow-hidden border border-white/10">
        <iframe
            class="w-full h-full"
            src="https://www.youtube.com/embed/<?= $content['trailer'] ?>"
            allowfullscreen>
        </iframe>
    </div>

</div>
<?php endif; ?>



