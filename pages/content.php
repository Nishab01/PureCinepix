<?php

require_once '../config/config.php';
require_once '../config/constants.php';

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

$rating = $content['tmdb_rating'] ?? null;
$trailer = $content['tmdb_trailer'] ?? null;

$release = $content['release_date'] ?? '';
$last = $content['last_date'] ?? '';

$poster = $content['poster1'] ?? '';
$backdrop = $content['backdrop3'] ?? '';

$releaseFormatted = $release ? date('j M Y', strtotime($release)) : '';
$lastFormatted = $last ? date('j M Y', strtotime($last)) : '';

$date = ($type == 'movie') ? $releaseFormatted : $releaseFormatted . " - " . $lastFormatted;

// GENRES
$genres = $conn->query("
    SELECT g.name FROM genres g
    JOIN content_genres cg ON g.id = cg.genre_id
    WHERE cg.content_id = $id ORDER BY name ASC
");

$user_id = $_SESSION['user_id'] ?? 0;

$isInWatchlist = false;

if (isset($_SESSION[SESSION_USER])) {

    $user_id = $_SESSION[SESSION_USER]['id'];

    $stmt = $conn->prepare("
        SELECT 1 FROM watchlist 
        WHERE user_id = ? AND content_id = ?
    ");

    $stmt->bind_param("ii", $user_id, $id);
    $stmt->execute();
    $res = $stmt->get_result();

    $isInWatchlist = $res->num_rows > 0;
}

$pagename = $title;
include '../includes/header.php';
?>

<!-- BACKDROP -->
<div class="w-full h-[50vh] md:h-[96vh] relative">
    <img src="<?= $backdrop ?: 'https://images.pexels.com/photos/29508640/pexels-photo-29508640.jpeg' ?>"
         class="w-full h-full object-cover">

    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/70 to-transparent backdrop-blur md:backdrop-blur-0"></div>
</div>

<!-- MAIN -->
<div class="max-w-[1400px] mx-auto px-4 -mt-[350px] md:-mt-[500px] relative z-10">

<div class="bg-black/60 backdrop-blur-xl border border-white/10 rounded-2xl p-4 md:p-6">

<div class="flex flex-col md:flex-row gap-4">

<!-- POSTER -->
<div class="w-[300px] mx-auto md:mx-0 flex-shrink-0">
    <div class="aspect-[2/3] w-full">
        <img src="<?= $poster ?>"
             class="w-full h-full object-cover rounded-xl shadow-lg">
    </div>
</div>

<!-- INFO -->
<div class="flex-1">

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
        <?= ($rating && $rating > 0) ? number_format($rating, 1) : 'N/A' ?>
    </span>

    <span class="hidden md:block text-white/60">•</span>

    <div class="hidden md:block flex items-center gap-1 bg-white/10 text-white/60 px-2 py-1 rounded-md text-sm">
        <?= ($release > date('Y-m-d')) ? $date.' (Unreleased)' : $date ?>
    </div>
</div>

<!-- DATE -->
<div class="md:hidden w-fit bg-white/10 text-white/60 px-2 py-1 rounded-md text-sm mb-3">
    <?= ($release > date('Y-m-d')) ? $date.' (Unreleased)' : $date ?>
</div>

<!-- GENRES -->
<div class="flex flex-wrap gap-2 mb-4">
    <?php while($g = $genres->fetch_assoc()): ?>
        <span class="text-xs bg-white/10 px-3 py-1 rounded-full border border-white/10">
            <?= $g['name'] ?>
        </span>
    <?php endwhile; ?>
</div>

<!-- DESCRIPTION -->
<p class="text-white/70 text-sm md:text-base leading-relaxed mb-5 text-justify">
    <?= $description ?>
</p>

<!-- WATCHLIST -->
<!-- <button id="watchlistBtn"
    data-id="<?= $id ?>"
    data-state="<?= $isInWatchlist ? 'added' : 'not-added' ?>"
    class="bg-white/10 hover:bg-white/20 active:scale-95 px-5 py-2 rounded-lg font-semibold border border-white/10 backdrop-blur-md transition-all duration-150 hover:shadow-lg hover:shadow-blue-500/20 text-sm transition">

    <?= $isInWatchlist ? '✓ In Watchlist' : '+ Add to Watchlist' ?>

</button> -->
<button id="watchlistBtn"
    data-id="<?= $id ?>"
    data-state="<?= $isInWatchlist ? 'added' : 'not-added' ?>"
    class="px-5 py-2 rounded-lg font-semibold border border-white/10 backdrop-blur-md text-sm transition-all duration-150
    <?= $isInWatchlist 
        ? 'bg-green-500/20 text-green-300 hover:shadow-lg hover:shadow-green-500/20 active:scale-95' 
        : 'bg-white/10 text-white hover:bg-white/20 hover:shadow-lg hover:shadow-blue-500/20 active:scale-95' ?>">

    <span id="watchlistText">
        <?= $isInWatchlist ? '✓ In Watchlist' : '+ Add to Watchlist' ?>
    </span>

</button>

<hr class="my-5 border-white/10">

<!-- DOWNLOADS -->
<h3 class="text-xl font-semibold mb-4">Available Downloads</h3>

<?php if ($type == 'movie'): ?>

    <div class="grid gap-3 sm:grid-cols-2">
    <?php
    $links = $conn->query("SELECT * FROM downloads WHERE content_id = $id");
    

    while($l = $links->fetch_assoc()):
    ?>
        <a href="<?= $l['url'] ?>" target="_blank"
        class="flex items-center justify-between text-sm font-semibold px-4 py-2.5 rounded-lg bg-gradient-to-r from-blue-500/20 to-blue-600/10 hover:from-blue-500 hover:to-blue-600 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-300 group">
            <span class="text-white group-hover:text-white transition-all duration-300"><?= $l['quality'] ?></span>
            <span class="transform transition-all duration-300"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-download-icon lucide-download"><path d="M12 15V3"/><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/></svg></span>
        </a>
    <?php endwhile; ?>
    </div>

    <?php else: ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <?php
    $seasons = $conn->query("SELECT * FROM seasons WHERE content_id = $id");
    ?>

    <?php while($season = $seasons->fetch_assoc()): ?>

        <div class="bg-white/5 border border-white/10 rounded-xl p-4">

            <h3 class="text-lg font-semibold mb-3">
                Season <?= $season['season_number'] ?><?= $season['title'] ? ': '.$season['title'] : '' ?>
            </h3>

            <!-- SEASON PACK -->
            <?php
            $season_links = $conn->query("
                SELECT * FROM downloads
                WHERE season_id = ".$season['id']."
                AND (episode_id IS NULL OR episode_id = 0)
            ");
            ?>

            <?php if ($season_links->num_rows > 0): ?>
                <div class="grid gap-2 mb-4">
                    <?php while($l = $season_links->fetch_assoc()): ?>
                        <a href="<?= $l['url'] ?>" target="_blank"
                        class="flex items-center justify-between text-sm font-semibold px-4 py-2.5 rounded-lg bg-gradient-to-r from-blue-500/20 to-blue-600/10 hover:from-blue-500 hover:to-blue-600 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-300 group">
                            <span class="text-white group-hover:text-white transition-all duration-300">Season Pack (<?= $l['quality'] ?>)</span>
                            <span class="transform transition-all duration-300"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-download-icon lucide-download"><path d="M12 15V3"/><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/></svg></span>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

            <!-- EPISODES -->
            <?php
            $episodes = $conn->query("
                SELECT * FROM episodes
                WHERE season_id = ".$season['id']."
                ORDER BY episode_number ASC
            ");
            ?>

            <?php if ($episodes && $episodes->num_rows > 0): ?>
                <?php while($ep = $episodes->fetch_assoc()): ?>
                    <div class="border border-white/10 rounded-lg p-2 mb-2">

                        <div class="mb-2 font-medium">
                            <!-- EP <?= $ep['episode_number'] ?> - <?= $ep['title'] ?> -->
                            EP <?= $ep['episode_number'] ?><?= $ep['title'] ? ': '.$ep['title'] : '' ?>
                        </div>

                        <?php
                        $ep_links = $conn->query("
                            SELECT * FROM downloads
                            WHERE episode_id = ".$ep['id']."
                        ");
                        ?>

                        <div class="grid gap-2">
                        <?php while($l = $ep_links->fetch_assoc()): ?>
                            <a href="<?= $l['url'] ?>" target="_blank"
                            class="flex items-center justify-between text-sm font-semibold px-4 py-2.5 rounded-lg bg-gradient-to-r from-blue-500/20 to-blue-600/10 hover:from-blue-500 hover:to-blue-600 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-300 group">
                                <span class="text-white group-hover:text-white transition-all duration-300"><?= $l['quality'] ?></span>
                                <span class="transform transition-all duration-300"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-download-icon lucide-download"><path d="M12 15V3"/><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/></svg></span>
                            </a>
                        <?php endwhile; ?>
                        </div>

                    </div>
                <?php endwhile; ?>
            <?php endif; ?>

        </div>

    <?php endwhile; ?>

    </div>

<?php endif; ?>

</div>
</div>
</div>

<!-- TRAILER -->
<?php if (!empty($trailer) && strlen($trailer) > 5): ?>
<div class="max-w-[1400px] mx-auto px-4 mt-4 bg-black/60 backdrop-blur-xl border border-white/10 rounded-2xl p-4 md:p-6">
        <h3 class="text-xl font-semibold mb-3">Trailer</h3>

        <div class="aspect-video h-[60vh] w-full rounded-xl overflow-hidden border border-white/10">
            <iframe
                class="w-full h-full"
                src="https://www.youtube.com/embed/<?= $trailer ?>"
                allowfullscreen>
            </iframe>
        </div>
</div>
<?php else: ?>
    <div class="max-w-[1400px] mx-auto px-4 mt-4 bg-black/60 border border-white/10 rounded-2xl p-6 text-center text-white/50">
        No trailer available
    </div>
<?php endif; ?>

<script>
    // function showToast(message, type = 'success') {
    //     const toast = document.createElement('div');

    //     toast.className = `
    //         fixed bottom-5 right-5 px-4 py-2 rounded-lg text-white text-sm
    //         ${type === 'error' ? 'bg-red-500' : 'bg-green-500'}
    //         shadow-lg z-50 animate-fade-in
    //     `;

    //     toast.innerText = message;
    //     document.body.appendChild(toast);

    //     setTimeout(() => toast.remove(), 2500);
    // }

    const btn = document.getElementById('watchlistBtn');

    btn.addEventListener('click', async () => {

        const contentId = btn.dataset.id;
        let state = btn.dataset.state;

        const action = state === 'added' ? 'remove' : 'add';

        // 🔄 loading state
        btn.disabled = true;
        btn.classList.add('opacity-60');

        try {
            const res = await fetch('../api/watchlist_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `content_id=${contentId}&action=${action}`
            });

            // const data = await res.json();
            const text = await res.text();

            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error("JSON ERROR:", e);
            }

            // ✅ ADD SUCCESS
            if (data.status === 'added') {

                btn.dataset.state = 'added';

                btn.classList.remove('bg-white/10', 'text-white', 'hover:bg-white/20', 'hover:shadow-blue-500/20');
                btn.classList.add('bg-green-500/20', 'text-green-300', 'hover:shadow-green-500/20');

                document.getElementById('watchlistText').innerText = '✓ In Watchlist';

                // showToast("Added to Watchlist");
            }

            // ✅ REMOVE SUCCESS
            else if (data.status === 'removed') {

                btn.dataset.state = 'not-added';

                btn.classList.remove('bg-green-500/20', 'text-green-300', 'hover:shadow-green-500/20');
                btn.classList.add('bg-white/10', 'text-white', 'hover:bg-white/20', 'hover:shadow-blue-500/20');

                document.getElementById('watchlistText').innerText = '+ Add to Watchlist';

                // showToast("Removed from Watchlist", "error");
            }

            else {
                showToast("Something went wrong", "error");
            }

        } catch (err) {
            console.error(err);
            showToast("Network error", "error");
        }

        btn.disabled = false;
        btn.classList.remove('opacity-60');
    });
</script>