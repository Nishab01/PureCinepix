<?php
    require_once '../config/config.php';
    require_once '../config/constants.php';

    $id = $_GET['slug'] ?? 0;

    /* ==========================
    VIEW COUNTER
    ========================== */
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

    if (isset($_SESSION[SESSION_USER])) {
        $visitorHash = hash(
            'sha256',
            'user_' . $_SESSION[SESSION_USER]['id']
        );
    } else {
        $visitorHash = hash(
            'sha256',
            $ip . '|' . $ua
        );
    }

    /*
        Recount after 24 hours
    */
    $viewStmt = $conn->prepare("
        SELECT id
        FROM content_views
        WHERE content_id = ?
        AND visitor_hash = ?
        AND viewed_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        LIMIT 1
    ");

    $viewStmt->bind_param(
        "is",
        $id,
        $visitorHash
    );

    $viewStmt->execute();

    $alreadyViewed = $viewStmt
        ->get_result()
        ->num_rows > 0;

    if (!$alreadyViewed) {

        $insert = $conn->prepare("
            INSERT INTO content_views (content_id, visitor_hash)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE viewed_at = NOW();
        ");

        $insert->bind_param(
            "is",
            $id,
            $visitorHash
        );

        $insert->execute();

        $update = $conn->prepare("
            UPDATE contents
            SET total_views = total_views + (FLOOR(RAND() * 9) + 1)
            WHERE id = ?
        ");

        $update->bind_param("i", $id);
        $update->execute();
    }

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
    $contentgenres = $conn->query("
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

    $related = $conn->query("
        SELECT c.*, COUNT(cg.genre_id) as match_score
        FROM contents c
        JOIN content_genres cg ON c.id = cg.content_id
        WHERE c.id != $id
        AND cg.genre_id IN (
            SELECT genre_id FROM content_genres WHERE content_id = $id
        )
        GROUP BY c.id
        ORDER BY match_score DESC, COALESCE(c.last_date, c.release_date) DESC
        LIMIT 50
    ");

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    $pagename = $title;
    include '../includes/header.php';
?>



<!-- BACKDROP -->
<div class="w-full h-[50vh] md:h-[96vh] relative">
    <img src="<?= $backdrop ?: 'https://images.pexels.com/photos/29508640/pexels-photo-29508640.jpeg' ?>"
         class="w-full h-full object-cover">

    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/70 to-transparent"></div>
</div>

<!-- MAIN -->
<div class="max-w-[1400px] mx-auto px-4 -mt-[350px] md:-mt-[450px] relative z-10">

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
            <div class="flex-1 space-y-2">
                <h1 class="text-2xl md:text-4xl font-bold">
                    <?= $title ?>
                </h1>

                <!-- TYPE + RATING -->
                <div class="flex flex-wrap items-center gap-2 mt-4">
                    <!-- TYPE -->
                    <span class="flex items-center gap-2 bg-white/10 text-white/60 px-2 py-1 rounded-md text-sm">
                        <?= ($type == 'movie') 
                        ? '<svg class="text-blue-500" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-film-icon lucide-film"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 3v18"/><path d="M3 7.5h4"/><path d="M3 12h18"/><path d="M3 16.5h4"/><path d="M17 3v18"/><path d="M17 7.5h4"/><path d="M17 16.5h4"/></svg> Movie'
                        : '<svg class="text-blue-500" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tv-icon lucide-tv"><path d="m17 2-5 5-5-5"/><rect width="20" height="15" x="2" y="7" rx="2"/></svg> TV Show' 
                        ?>
                    </span>

                    <!-- TMDB RATING -->
                    <span class="flex items-center gap-2 bg-white/10 text-white/60 px-2 py-1 rounded-md text-sm">
                        <svg class="text-yellow-500" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
                        <?= ($rating && $rating > 0) ? number_format($rating, 1) : 'N/A' ?>
                    </span>

                    <div class="hidden md:flex flex-row items-center gap-2 w-fit bg-white/10 text-white/60 px-2 py-1 rounded-md text-sm">
                        <?= ($release > date('Y-m-d')) ? '<svg class="text-orange-500" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar1-icon lucide-calendar-1"><path d="M11 14h1v4"/><path d="M16 2v4"/><path d="M3 10h18"/><path d="M8 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/></svg> '.$date.' (Unreleased)' : '<svg class="text-orange-500" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar1-icon lucide-calendar-1"><path d="M11 14h1v4"/><path d="M16 2v4"/><path d="M3 10h18"/><path d="M8 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/></svg> '.$date ?>
                    </div>

                    <div class="flex flex-row items-center gap-2 w-fit bg-white/10 text-white/60 px-2 py-1 rounded-md text-sm ">
                        <svg class="text-purple-500" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-icon lucide-eye"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                        
                        <span><?= $content['total_views'] ?? 0 ?></span>
                    </div>
                </div>

                <!-- DATE -->
                <div class="md:hidden flex flex-row items-center gap-2 w-fit bg-white/10 text-white/60 px-2 py-1 rounded-md text-sm">
                    <?= ($release > date('Y-m-d')) ?  '<svg class="text-orange-500" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar1-icon lucide-calendar-1"><path d="M11 14h1v4"/><path d="M16 2v4"/><path d="M3 10h18"/><path d="M8 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/></svg> '.$date.' (Unreleased)' : '<svg class="text-orange-500" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar1-icon lucide-calendar-1"><path d="M11 14h1v4"/><path d="M16 2v4"/><path d="M3 10h18"/><path d="M8 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/></svg> '.$date ?>
                </div>

                <!-- GENRES -->
                <div class="flex flex-wrap gap-2">
                    <?php while($g = $contentgenres->fetch_assoc()): ?>
                        <span class="text-xs bg-white/10 px-3 py-1 rounded-full border border-white/10">
                            <?= $g['name'] ?>
                        </span>
                    <?php endwhile; ?>
                </div>

                <!-- DESCRIPTION -->
                <p class="text-white/70 text-sm md:text-base leading-relaxed mb-5 text-justify mt-4">
                    <?= $description ?>
                </p>

                <!-- WATCHLIST -->
                <button id="watchlistBtn"
                    data-id="<?= $id ?>"
                    data-state="<?= $isInWatchlist ? 'added' : 'not-added' ?>"
                    class="px-5 py-2 rounded-lg font-semibold border border-white/10 backdrop-blur-md text-sm transition-all duration-150
                    <?= $user
                        ? $isInWatchlist
                            ? 'bg-green-500/20 text-green-300 hover:shadow-lg hover:shadow-green-500/20 active:scale-95' 
                            : 'bg-white/10 text-white hover:bg-white/20 hover:shadow-lg hover:shadow-blue-500/20 active:scale-95'
                        : 'cursor-not-allowed bg-white/5 text-white/50'
                    ?>">

                    <span id="watchlistText">
                        <?= $isInWatchlist ? '✓ In Watchlist' : '+ Add to Watchlist' ?>
                    </span>

                </button>

                <hr class="my-5 border-white/10">

                <!-- DOWNLOADS -->
                <h3 class="text-xl font-semibold mb-4">Available Downloads</h3>
                <?php if ($type == 'movie'): ?>

                    <div class="grid gap-2 grid-cols-1 md:grid-cols-2">
                        <?php
                        $links = $conn->query("SELECT * FROM downloads WHERE content_id = $id");
                        while($l = $links->fetch_assoc()):
                        ?>
                            <!-- <a href="<?= $l['url'] ?>" target="_blank" -->
                            <a href="download.php?download=<?= $l['id'] ?>&title=<?= urlencode($title) ?>" target="_blank"
                            class="flex items-center justify-between text-sm font-semibold px-4 py-2.5 rounded-lg bg-gradient-to-r from-blue-600/50 to-blue-500/80 hover:from-blue-500 hover:to-blue-600 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-300 group">
                                <span class="text-white group-hover:text-white transition-all duration-300"><?= $l['quality'] ?></span>
                                <span class="transform transition-all duration-300"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-download-icon lucide-download"><path d="M12 15V3"/><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/></svg></span>
                            </a>
                        <?php endwhile; ?>
                    </div>

                <?php else: ?>

                <div class="grid grid-cols-1 gap-2">
                    <?php
                    $seasons = $conn->query("SELECT * FROM seasons WHERE content_id = $id");
                    while($season = $seasons->fetch_assoc()):
                    ?>
                        <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                            <h3 class="text-lg font-semibold mb-3">
                                <!-- Season <?= $season['season_number'] ?><?= $season['title'] ? ': '.$season['title'] : '' ?> -->
                                
                                <?= $season['title'] ? $season['title'] : 'Season '.$season['season_number'] ?>
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
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
                                    <?php while($l = $season_links->fetch_assoc()): ?>
                                        <!-- <a href="<?= $l['url'] ?>" target="_blank" -->
                                        <a href="download.php?download=<?= $l['id'] ?>&title=<?= urlencode($title) ?>" target="_blank"
                                        class="flex flex-row items-center justify-between text-sm font-semibold px-4 py-2.5 rounded-lg bg-gradient-to-r from-blue-600/50 to-blue-500/80 hover:from-blue-500 hover:to-blue-600 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-300 group">
                                            <div class="flex flex-col items-start justify-start">
                                                <span class="text-white group-hover:text-white transition-all duration-300 text-xs font-semibold">Season Pack</span>
                                                <span class="text-white group-hover:text-white transition-all duration-300"><?= $l['quality'] ?></span>
                                            </div>
                                            
                                            <span class="flex flex-col items-start justify-end transform transition-all duration-300"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-download-icon lucide-download"><path d="M12 15V3"/><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/></svg></span>
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
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
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

                                            <div class="grid grid-cols-1 gap-2">
                                            <?php while($l = $ep_links->fetch_assoc()): ?>
                                                <!-- <a href="<?= $l['url'] ?>" target="_blank" -->
                                                <a href="download.php?download=<?= $l['id'] ?>&title=<?= urlencode($title) ?>" target="_blank"
                                                class="flex items-center justify-between text-sm font-semibold px-4 py-2.5 rounded-lg bg-gradient-to-r from-blue-600/50 to-blue-500/80 hover:from-blue-500 hover:to-blue-600 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-300 group">
                                                    <span class="text-white group-hover:text-white transition-all duration-300"><?= $l['quality'] ?></span>
                                                    <span class="transform transition-all duration-300"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-download-icon lucide-download"><path d="M12 15V3"/><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/></svg></span>
                                                </a>
                                            <?php endwhile; ?>
                                            </div>

                                        </div>
                                    <?php endwhile; ?>
                                </div>
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

    <!-- GALLERY -->
    <?php
        $images = [
            $content['backdrop1'] ?? '',
            $content['backdrop2'] ?? '',

            $content['poster1'] ?? '',
            $content['poster2'] ?? '',

            $content['backdrop3'] ?? '',
            $content['backdrop4'] ?? '',

            $content['poster3'] ?? '',
            $content['poster4'] ?? ''
        ];

        // remove empty
        $images = array_values(array_filter($images));
    ?>

    <?php if (!empty($images)): ?>
        <div class="max-w-[1400px] mx-auto px-4 mt-4 bg-black/60 backdrop-blur-xl border border-white/10 rounded-2xl p-4 md:p-6">
            <h3 class="text-xl font-semibold mb-4">Gallery</h3>

            <div class="grid grid-cols-2 gap-2 md:flex md:flex-row">
                <?php foreach ($images as $index => $img): ?>
                    <div class="col-span-1 md:h-[150px] md:w-auto h-auto group cursor-pointer overflow-hidden rounded-xl border border-white/10 animate-item card-hidden" onclick="openGallery(<?= $index ?>)">
                        <img src="<?= $img ?>" class="w-full h-full group-hover:scale-105 transition duration-300">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- GALLERY MODAL -->
        <div id="galleryModal" class="mt-20 fixed inset-0 bg-black/90 backdrop-blur-sm hidden items-center justify-center z-50">
            <button onclick="closeGallery()" class="absolute top-5 right-5 text-white text-3xl">&times;</button>
            <img id="galleryImage" src="" class="max-w-[80%] max-h-[80%] rounded-xl shadow-lg border border-white/50">
        </div>
    <?php endif; ?>

    <!-- KEYWORDS -->
     <?php
        $keywords = [];

        if (!empty($content['keywords'])) {
            $keywords = array_map('trim', explode(',', $content['keywords']));
        }
    ?>

    <?php if (!empty($keywords)): ?>
        <div class="max-w-[1400px] mx-auto px-4 mt-4 bg-black/60 backdrop-blur-xl border border-white/10 rounded-2xl p-4 md:p-6">

            <h3 class="text-xl font-semibold mb-3">Keywords</h3>

            <div class="flex flex-wrap gap-2">
                <?php foreach ($keywords as $kw): ?>
                    <span class="px-3 py-1 text-sm rounded-full bg-white/10 border border-white/10 text-white/70 transition">
                        #<?= htmlspecialchars($kw) ?>
                    </span>
                <?php endforeach; ?>
            </div>

        </div>
    <?php endif; ?>

    <!-- RELATED CONTENTS -->
    <?php if ($related && $related->num_rows > 0): ?>
        <div id="relatedBody" class="max-w-[1400px] mx-auto px-4 mt-4 bg-black/60 backdrop-blur-xl border border-white/10 rounded-2xl p-4 md:p-6">

            <!-- HEADER -->
            <div class="flex justify-between items-center">
                <h3 class="text-xl md:text-2xl font-semibold text-white">
                    More Like This
                </h3>
            </div>

            <!-- SCROLLER -->
            <div id="relatedWrapper" class="relative overflow-hidden">

                <!-- LEFT -->
                <button onclick="scrollRelated(-1)"
                    class="absolute left-1 top-1/2 -translate-y-1/2 z-20
                    bg-black/40 hover:bg-black/70 backdrop-blur-md
                    md:p-3 p-1 rounded-full transition hover:scale-105">
                    ‹
                </button>

                <!-- RIGHT -->
                <button onclick="scrollRelated(1)"
                    class="absolute right-1 top-1/2 -translate-y-1/2 z-20
                    bg-black/40 hover:bg-black/70 backdrop-blur-md
                    md:p-3 p-1 rounded-full transition hover:scale-105">
                    ›
                </button>

                <!-- SLIDER -->
                <div id="relatedSlider"
                    class="flex gap-2 md:gap-4 overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory py-4">

                    <?php while ($item = $related->fetch_assoc()): ?>
                        <?php include '../includes/poster-card.php'; ?>
                    <?php endwhile; ?>

                </div>

            </div>

        </div>
    <?php endif; ?>

    <!-- COMMENTS -->

    <?php
        $total_comments = $conn->query("SELECT COUNT(*) AS total FROM comments WHERE content_id = $id")->fetch_assoc()['total'];
    ?>

    <div class="max-w-[1400px] mx-auto px-4 mt-4 bg-black/60 backdrop-blur-xl border border-white/10 rounded-2xl p-4 md:p-6">
        <h3 class="text-xl font-semibold mb-3">Comments <span class="text-sm text-white/50 font-normal">(<?= $total_comments?>)</span></h3>

        <!-- ADD COMMENT -->
        <input id="commentInput"
            class="w-full p-3 rounded-lg bg-white/10 text-white border border-white/10"
            placeholder="Write a comment...">

        <button id="postCommentBtn"
            class="mt-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 rounded-lg text-white text-sm">
            Post
        </button>

        <!-- LIST -->
        <div id="commentsList" class="mt-4 space-y-3"></div>

        <!-- LOAD MORE -->
        <button id="loadMoreBtn"
            class="mt-4 px-4 py-2 bg-white/10 rounded-lg text-white text-sm">
            View More
        </button>
    </div>

    <div id="likeModal"
        class="hidden fixed inset-0 z-50 items-center justify-center">

        <!-- 🌫️ BACKDROP -->
        <div id="likeModalBackdrop"
            class="absolute inset-0 bg-black/60 backdrop-blur-sm opacity-0 transition-opacity duration-300">
        </div>

        <!-- 📦 MODAL BOX -->
        <div id="likeModalBox"
            class="relative w-[90%] max-w-sm bg-[#111] rounded-2xl shadow-2xl
            border border-white/10
            transform scale-95 opacity-0 translate-y-4
            transition-all duration-300">

            <!-- HEADER -->
            <div class="flex items-center justify-between px-4 py-2 border-b border-white/10">
                <span class="text-white/50 text-sm font-medium">Liked by</span>
                <button id="closeLikeModal"
                    class="text-white/40 hover:text-white transition text-lg">
                    ✕
                </button>
            </div>

            <!-- LIST -->
            <div id="likeList"
                class="max-h-60 overflow-y-auto px-4 py-3 space-y-2 text-sm text-white/90">
            </div>

        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* .replies .comment-item {
        border-left: 2px solid rgba(255,255,255,0.1);
        padding-left: 10px;
    } */
</style>

<script>
    // Admin check
    const isAdmin = <?= $_SESSION['is_admin'] ?? 0 ?>;

    // Watchlist Button
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

    // Gallery JS
    const galleryImages = <?= json_encode($images) ?>;
    let currentIndex = 0;

    function openGallery(index) {
        currentIndex = index;

        const modal = document.getElementById('galleryModal');
        const img = document.getElementById('galleryImage');

        img.src = galleryImages[currentIndex];

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeGallery() {
        const modal = document.getElementById('galleryModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    // 🔥 close on outside click
    document.getElementById('galleryModal').addEventListener('click', function(e) {
        if (e.target.id === 'galleryModal') {
            closeGallery();
        }
    });

    // Related Contents
    const relatedBody = document.getElementById('relatedBody');
    const relatedSlider = document.getElementById('relatedSlider');

    let relatedAutoScroll;

    // ✅ MANUAL SCROLL (CARD BASED)
    function scrollRelated(direction) {
        const card = relatedSlider.querySelector('a');
        const cardWidth = card ? card.offsetWidth + 16 : 300;

        relatedSlider.scrollBy({
            left: direction * cardWidth * 2,
            behavior: 'smooth'
        });
    }
    // ✅ AUTO SCROLL (LIKE TRENDING)
    function startRelatedAutoScroll() {
        relatedAutoScroll = setInterval(() => {

            const maxScroll = relatedSlider.scrollWidth - relatedSlider.clientWidth;

            if (relatedSlider.scrollLeft >= maxScroll - 5) {
                relatedSlider.scrollTo({
                    left: 0,
                    behavior: 'smooth'
                });
            } else {
                scrollRelated(1);
            }

        }, 4000); // slightly faster than trending
    }
    function stopRelatedAutoScroll() {
        clearInterval(relatedAutoScroll);
    }

    // INIT
    startRelatedAutoScroll();

    // PAUSE ON HOVER (IMPORTANT UX)
    relatedBody.addEventListener('mouseenter', stopRelatedAutoScroll);
    relatedBody.addEventListener('mouseleave', startRelatedAutoScroll);

</script>

<script>
    const currentUserId = <?= $_SESSION[SESSION_USER]['id'] ?? 0 ?>;

    let offset = 0;
    let loading = false;

    // ==========================
    // 📝 POST COMMENT
    document.getElementById('postCommentBtn').addEventListener('click', async () => {
        const input = document.getElementById('commentInput');
        const text = input.value.trim();
        if (!text) return;

        const btn = document.getElementById('postCommentBtn');
        btn.disabled = true;

        try {
            const res = await fetch('../api/add_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `content_id=<?= $id ?>&comment=${encodeURIComponent(text)}`
            });

            const data = await res.json();

            if (data.status === 'success') {
                input.value = "";

                // ✅ instant render at top
                renderComment(data.comment, true);

            } else {
                alert(data.message || "Error");
            }

        } catch (err) {
            console.error(err);
        }

        btn.disabled = false;
    });

    document.getElementById('commentInput').addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            document.getElementById('postCommentBtn').click();
        }
    });

    // ==========================
    // 📥 LOAD COMMENTS
    async function loadComments() {
        if (loading) return;

        loading = true;

        const res = await fetch(`../api/fetch_comments.php?content_id=<?= $id ?>&offset=${offset}`);
        const data = await res.json();

        if (!data.comments.length) {
            const btn = document.getElementById('loadMoreBtn');
            btn.innerText = "No more comments";
            btn.disabled = true;
            loading = false;
            return;
        }

        // append older comments
        data.comments.forEach(c => renderComment(c, false));

        offset += data.comments.length;
        loading = false;
    }

    // ==========================
    // 🔄 RESET
    function resetComments() {
        document.getElementById('commentsList').innerHTML = "";
        offset = 0;
        loadComments();
    }

    // ==========================
    // 💬 RENDER COMMENT
    function renderComment(c, prepend = false) {
        const list = document.getElementById('commentsList');
        const el = document.createElement('div');

        el.className = "comment-item bg-white/5 p-3 rounded-lg";
        el.dataset.id = c.id;

        let isEdited = c.updated_at && c.updated_at !== c.timestamp;
        let timeText = timeAgo(c.updated_at || c.timestamp);

        if (isEdited) {
            timeText = "Edited " + timeText;
        }

        el.innerHTML = `
            <div class="flex justify-between text-sm text-white/50">
                <span>${c.name} ${isAdmin ? `<span class="text-[12px] text-white/40 italic">(${c.email})</span>` : ''}</span>
                <span>${timeText}</span>
            </div>

            <div class="comment-text mt-1 text-sm text-white">
                ${c.comment}
            </div>

            <div class="edit-box hidden mt-2">
                <input class="editInput w-full bg-black/30 p-2 rounded">
                <div class="flex gap-1 mt-2 text-white/80">
                    <button class="saveEditBtn text-xs rounded-lg px-2 py-1 bg-white/10">Save</button>
                    <button class="cancelEditBtn text-xs rounded-lg px-2 py-1 bg-white/10">Cancel</button>
                </div>
            </div>

            <div class="flex gap-3 mt-2 text-xs">
                <button class="likeBtn" data-id="${c.id}">👍 ${c.likes || 0}</button>
                <button class="viewLikesBtn text-white/50 hover:text-white" data-id="${c.id}">View</button>
                <button class="replyBtn text-white/50 hover:text-white" data-id="${c.id}">Reply</button>

                ${c.user_id == currentUserId ? `
                    <button class="editBtn">Edit</button>
                    <button class="deleteBtn" data-id="${c.id}">Delete</button>
                ` : ''}

                ${isAdmin && c.user_id != currentUserId ? `
                    <button class="deleteBtn text-red-400 hover:text-red-600" data-id="${c.id}">Delete as Admin</button>
                ` : ''}
                
            </div>

            <div class="replies mt-3 ml-4 space-y-2"></div>

            <div class="reply-box hidden mt-2">
                <input class="replyInput w-full bg-black/30 p-2 rounded text-sm"
                    placeholder="Write a reply...">
                <div class="flex gap-2 mt-2 text-xs">
                    <button class="sendReplyBtn px-2 py-1 bg-white/10 rounded">Reply</button>
                    <button class="cancelReplyBtn px-2 py-1 bg-white/10 rounded">Cancel</button>
                </div>
            </div>
        `;

        // ✅ correct insertion logic
        if (prepend) {
            list.prepend(el);
        } else {
            list.appendChild(el);
        }

        const repliesBox = el.querySelector('.replies');

        if (c.replies && Array.isArray(c.replies)) {
            c.replies.forEach(r => renderReply(repliesBox, r));
        }
    }

    // ==========================
    // 🔁 REPLY
    function renderReply(container, r) {
        const el = document.createElement('div');
        el.className = "comment-item bg-white/5 p-2 rounded";
        el.dataset.id = r.id;

        let timeText = timeAgo(r.updated_at || r.timestamp);

        el.innerHTML = `
            <div class="flex justify-between text-xs text-white/50">
                <span>${r.name}</span>
                <span>${timeText}</span>
            </div>

            <div class="comment-text mt-1 text-sm text-white">
                ${r.comment}
            </div>

            <div class="edit-box hidden mt-2">
                <input class="editInput w-full bg-black/30 p-2 rounded">
                <div class="flex gap-1 mt-2 text-white/80">
                    <button class="saveEditBtn text-xs px-2 py-1 bg-white/10 rounded">Save</button>
                    <button class="cancelEditBtn text-xs px-2 py-1 bg-white/10 rounded">Cancel</button>
                </div>
            </div>

            <div class="flex gap-3 mt-2 text-xs">
                <button class="likeBtn" data-id="${r.id}">👍 ${r.likes || 0}</button>

                ${r.user_id == currentUserId ? `
                    <button class="editBtn">Edit</button>
                    <button class="deleteBtn" data-id="${r.id}">Delete</button>
                ` : ''}

                ${window.isAdmin ? `
                    <button class="deleteBtn text-red-400" data-id="${r.id}">
                        Delete as Admin
                    </button>
                ` : ''}
            </div>
        `;

        container.appendChild(el);
    }

    // ==========================
    // 🧠 GLOBAL HANDLER
    document.addEventListener('click', async (e) => {

        // ❤️ LIKE (ONLY toggle)
        const likeBtn = e.target.closest('.likeBtn');
        if (likeBtn) {
            const id = likeBtn.dataset.id;

            await fetch('../api/toggle_like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `comment_id=${id}`
            });

            resetComments();
            return;
        }

        // 👀 VIEW LIKES (OPEN MODAL)
        const viewBtn = e.target.closest('.viewLikesBtn');
        if (viewBtn) {
            const id = viewBtn.dataset.id;
            openLikeModal(id);
            return;
        }

        // 🗑️ DELETE
        const deleteBtn = e.target.closest('.deleteBtn');
        if (deleteBtn) {
            if (!confirm("Delete?")) return;

            const id = deleteBtn.dataset.id;

            await fetch('../api/comment_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `comment_id=${id}&action=delete`
            });

            resetComments();
            return;
        }

        // ✏️ EDIT
        const editBtn = e.target.closest('.editBtn');
        if (editBtn) {
            const box = editBtn.closest('.comment-item');
            const textEl = box.querySelector('.comment-text');
            const editBox = box.querySelector('.edit-box');
            const input = box.querySelector('.editInput');

            input.value = textEl.innerText;
            textEl.style.display = 'none';
            editBox.style.display = 'block';

            // ✅ ADD THIS
            input.addEventListener('keydown', function handler(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    box.querySelector('.saveEditBtn').click();
                }
            }, { once: true });

            return;
        }

        // 💾 SAVE
        const saveBtn = e.target.closest('.saveEditBtn');
        if (saveBtn) {
            const box = saveBtn.closest('.comment-item');
            const id = box.dataset.id;

            const textEl = box.querySelector('.comment-text');
            const editBox = box.querySelector('.edit-box');
            const input = box.querySelector('.editInput');

            const newText = input.value.trim();
            if (!newText) return;

            const res = await fetch('../api/comment_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `comment_id=${id}&action=edit&comment=${encodeURIComponent(newText)}`
            });

            const data = await res.json();

            if (data.status === 'success') {
                textEl.innerText = newText;

                const timeEl = box.querySelector('.flex span:last-child');
                if (timeEl) {
                    timeEl.innerText = "Edited just now";
                }

                editBox.style.display = 'none';
                textEl.style.display = 'block';
            } else {
                alert(data.message);
            }

            return;
        }

        // ❌ CANCEL
        const cancelBtn = e.target.closest('.cancelEditBtn');
        if (cancelBtn) {
            const box = cancelBtn.closest('.comment-item');

            const textEl = box.querySelector('.comment-text');
            const editBox = box.querySelector('.edit-box');

            editBox.style.display = 'none';
            textEl.style.display = 'block';
            return;
        }

        // 💬 OPEN REPLY BOX
        const replyBtn = e.target.closest('.replyBtn');
        if (replyBtn) {
            const box = replyBtn.closest('.comment-item');
            const replyBox = box.querySelector('.reply-box');
            const input = box.querySelector('.replyInput');

            replyBox.style.display = 'block';
            input.focus();

            // ✅ ADD THIS
            input.addEventListener('keydown', function handler(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    box.querySelector('.sendReplyBtn').click();
                }
            }, { once: true });

            return;
        }

        // 📤 SEND REPLY
        const sendReplyBtn = e.target.closest('.sendReplyBtn');
        if (sendReplyBtn) {
            const box = sendReplyBtn.closest('.comment-item');
            const commentId = box.dataset.id;

            const input = box.querySelector('.replyInput');
            const text = input.value.trim();

            if (!text) return;

            const res = await fetch('../api/add_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `content_id=<?= $id ?>&comment=${encodeURIComponent(text)}&parent_id=${commentId}`
            });

            const data = await res.json();

            if (data.status === 'success') {

                const repliesBox = box.querySelector('.replies');

                // ✅ instant render reply
                renderReply(repliesBox, data.comment);

                input.value = "";
                box.querySelector('.reply-box').style.display = 'none';

            } else {
                alert(data.message);
            }

            return;
        }

        // ❌ CANCEL REPLY
        const cancelReplyBtn = e.target.closest('.cancelReplyBtn');
        if (cancelReplyBtn) {
            const box = cancelReplyBtn.closest('.comment-item');

            box.querySelector('.reply-box').style.display = 'none';
            return;
        }
    });

    // ==========================
    // ⏱️ TIME
    function timeAgo(ts) {
        if (!ts) return "";

        const now = Math.floor(Date.now() / 1000);
        const diff = now - ts;

        if (diff < 60) return "Just now";
        if (diff < 3600) return Math.floor(diff / 60) + "m ago";
        if (diff < 86400) return Math.floor(diff / 3600) + "h ago";
        if (diff < 604800) return Math.floor(diff / 86400) + "d ago";

        return "";
    }

    // ❤️ OPEN LIKE MODAL
    async function openLikeModal(commentId) {

        const modal = document.getElementById('likeModal');
        const backdrop = document.getElementById('likeModalBackdrop');
        const box = document.getElementById('likeModalBox');
        const list = document.getElementById('likeList');

        list.innerHTML = "Loading...";

        // show modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // animation (enter)
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            box.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
        }, 10);

        try {
            const res = await fetch(`../api/get_likes.php?comment_id=${commentId}`);
            const data = await res.json();

            if (!data.likes.length) {
                list.innerHTML = "<div class='text-white/50'>No likes yet</div>";
                return;
            }

            list.innerHTML = "";

            data.likes.forEach(u => {
                const el = document.createElement('div');

                el.className = "flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-white/5 transition";

                el.innerHTML = `
                    <div class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center text-xs">
                        ${u.name.charAt(0).toUpperCase()}
                    </div>
                    <span>${u.name}</span>
                `;

                list.appendChild(el);
            });

        } catch (err) {
            list.innerHTML = "Error loading";
        }
    }

    // Close Modal
    function closeLikeModal() {

        const modal = document.getElementById('likeModal');
        const backdrop = document.getElementById('likeModalBackdrop');
        const box = document.getElementById('likeModalBox');

        // animate out
        backdrop.classList.add('opacity-0');
        box.classList.add('opacity-0', 'scale-95', 'translate-y-4');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
    document.getElementById('likeModal').addEventListener('click', (e) => {
        if (e.target.id === 'likeModalBackdrop') {
            closeLikeModal();
        }
    });

    // close button
    document.getElementById('closeLikeModal')
        .addEventListener('click', closeLikeModal);

    // ==========================
    // 📄 LOAD MORE
    document.getElementById('loadMoreBtn').addEventListener('click', () => {
        loadComments();
    });


    // ==========================
    loadComments();
</script>