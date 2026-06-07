<?php
$currentpage = "contents";
$head_name = "Manage Content";
$page_title = "Manage Content";

require_once '../partials/guard.php';
require_once '../../config/config.php';
require_once '../../config/constants.php';

$content_id = (int)($_GET['id'] ?? 0);
$type = $_GET['type'] ?? 'movie';

$message = "";

/* =========================
   FETCH DATA
========================= */
$content = $conn->query("SELECT * FROM contents WHERE id=$content_id")->fetch_assoc();

/* =========================
   FETCH TMDB LAST DATE (AUTO)
========================= */
$release_date = $content['release_date'];

if($content['last_date'] == NULL || $content['last_date'] == '0000-00-00'){
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

            if ($type === 'tv' && !empty($tmdb['last_air_date'])) {
                $last_date = $tmdb['last_air_date'];
            } elseif (!empty($content['last_date'])) {
                $last_date = $content['last_date']; // KEEP admin value
            } else {
                $last_date = $content['release_date'];
            }
        }
    }
}
else{
    $last_date = $content['last_date'];
}

/* =========================
   FETCH CATEGORIES
========================= */
$allCategories = [];
$catQ = $conn->query("SELECT * FROM categories ORDER BY is_locked DESC, name ASC");
while ($c = $catQ->fetch_assoc()) {
    $allCategories[] = $c;
}

$selectedCategories = [];
$selQ = $conn->query("SELECT category_id FROM content_categories WHERE content_id=$content_id");
while ($s = $selQ->fetch_assoc()) {
    $selectedCategories[] = $s['category_id'];
}

$seasonsData = [];
$downloadsData = [];

if ($type === 'tv') {

    $seasons = $conn->query("SELECT * FROM seasons WHERE content_id=$content_id");

    while ($s = $seasons->fetch_assoc()) {

        $season_id = $s['id'];

        $sd = $conn->query("SELECT * FROM downloads WHERE season_id=$season_id AND episode_id IS NULL");

        $seasonDownloads = [];
        while ($d = $sd->fetch_assoc()) {
            $seasonDownloads[] = $d;
        }

        $episodesQ = $conn->query("SELECT * FROM episodes WHERE season_id=$season_id");

        $episodes = [];
        while ($ep = $episodesQ->fetch_assoc()) {

            $episode_id = $ep['id'];

            $ed = $conn->query("SELECT * FROM downloads WHERE episode_id=$episode_id");

            $epDownloads = [];
            while ($d = $ed->fetch_assoc()) {
                $epDownloads[] = $d;
            }

            $episodes[] = [
                'episode_number' => $ep['episode_number'],
                'title' => $ep['title'],
                'downloads' => $epDownloads
            ];
        }

        $seasonsData[] = [
            'season_number' => $s['season_number'],
            'title' => $s['title'],
            'downloads' => $seasonDownloads,
            'episodes' => $episodes
        ];
    }

}
else {

    $d = $conn->query("SELECT * FROM downloads WHERE content_id=$content_id AND season_id IS NULL");

    while ($row = $d->fetch_assoc()) {
        $downloadsData[] = $row;
    }
}

/* =========================
   FINAL SAVE
========================= */
if (isset($_POST['payload'])) {

    $data = json_decode($_POST['payload'], true);

    // echo '<pre>';
    // print_r($data);
    // echo '</pre>';
    // exit;

    /* UPDATE LAST DATE */
    $last_date_post = $_POST['last_date'] ?? null;

    $stmt = $conn->prepare("
        UPDATE contents
        SET last_date = ?
        WHERE id = ?
    ");
    $stmt->bind_param("si", $last_date_post, $content_id);
    $stmt->execute();

    // UPDATE IMAGES
    $imgsql = $conn->prepare("
        UPDATE contents SET
        poster1 = ?,
        poster2 = ?,
        poster3 = ?,
        poster4 = ?,
        backdrop1 = ?,
        backdrop2 = ?,
        backdrop3 = ?,
        backdrop4 = ?
        WHERE id = ?
    ");
    $imgsql->bind_param("ssssssssi", $_POST['poster1'], $_POST['poster2'], $_POST['poster3'], $_POST['poster4'], $_POST['backdrop1'], $_POST['backdrop2'], $_POST['backdrop3'], $_POST['backdrop4'], $content_id);
    $imgsql->execute();

    // Capture Old Download Links
    $oldDownloads = [];
    $newDownloads = [];
    $downloadsChanged = false;

    $res = $conn->query("
        SELECT quality, url
        FROM downloads
        WHERE content_id = $content_id
        ORDER BY quality, url
    ");

    while ($row = $res->fetch_assoc()) {
        $oldDownloads[] = $row;
    }

    // Build New Download Links
    if ($data['type'] === 'movie') {
        foreach ($data['downloads'] as $d) {

            if (empty($d['quality']) || empty($d['url'])) {
                continue;
            }

            $newDownloads[] = [
                'quality' => $d['quality'],
                'url' => $d['url']
            ];
        }
    }
    else {
        foreach ($data['seasons'] as $season) {
            foreach ($season['downloads'] as $d) {

                if (empty($d['quality']) || empty($d['url'])) {
                    continue;
                }

                $newDownloads[] = [
                    'quality' => $d['quality'],
                    'url' => $d['url']
                ];
            }

            foreach ($season['episodes'] as $ep) {
                foreach ($ep['downloads'] as $d) {

                    if (empty($d['quality']) || empty($d['url'])) {
                        continue;
                    }

                    $newDownloads[] = [
                        'quality' => $d['quality'],
                        'url' => $d['url']
                    ];
                }
            }
        }
    }

    sort($oldDownloads);
    sort($newDownloads);

    $downloadsChanged = ($oldDownloads != $newDownloads);
   





    /* =========================
       CLEAN OLD DATA
    ========================= */
    $seasonIds = [];
    $res = $conn->query("SELECT id FROM seasons WHERE content_id=$content_id");
    while ($r = $res->fetch_assoc()) {
        $seasonIds[] = $r['id'];
    }

    if (!empty($seasonIds)) {
        $ids = implode(',', $seasonIds);

        $conn->query("DELETE FROM downloads WHERE season_id IN ($ids)");
        $conn->query("DELETE FROM episodes WHERE season_id IN ($ids)");
    }

    $conn->query("DELETE FROM seasons WHERE content_id=$content_id");
    $conn->query("DELETE FROM downloads WHERE content_id=$content_id");
    $conn->query("DELETE FROM content_categories WHERE content_id=$content_id");


    /* =========================
       SAVE CATEGORIES
    ========================= */
    if (!empty($_POST['categories'])) {
        foreach ($_POST['categories'] as $cat_id) {
            $stmt = $conn->prepare("
                INSERT INTO content_categories (content_id, category_id)
                VALUES (?, ?)
            ");
            $stmt->bind_param("ii", $content_id, $cat_id);
            $stmt->execute();
        }
    }


    /* =========================
       RE-INSERT DATA
    ========================= */
    if ($data['type'] === 'movie') {

        foreach ($data['downloads'] as $d) {
            if (!$d['quality'] || !$d['url']) continue;

            $stmt = $conn->prepare("
                INSERT INTO downloads (content_id, quality, url)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("iss", $content_id, $d['quality'], $d['url']);
            $stmt->execute();
        }

    }
    else {

        foreach ($data['seasons'] as $season) {

            $stmt = $conn->prepare("
                INSERT INTO seasons (content_id, season_number, title)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("iis", $content_id, $season['season_number'], $season['title']);
            $stmt->execute();

            $season_id = $stmt->insert_id;

            foreach ($season['downloads'] as $d) {
                if (!$d['quality'] || !$d['url']) continue;

                $stmt = $conn->prepare("
                    INSERT INTO downloads (content_id, season_id, quality, url)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->bind_param("iiss", $content_id, $season_id, $d['quality'], $d['url']);
                $stmt->execute();
            }

            foreach ($season['episodes'] as $ep) {

                $stmt = $conn->prepare("
                    INSERT INTO episodes (season_id, episode_number, title)
                    VALUES (?, ?, ?)
                ");
                $stmt->bind_param("iis", $season_id, $ep['episode_number'], $ep['title']);
                $stmt->execute();

                $episode_id = $stmt->insert_id;

                foreach ($ep['downloads'] as $d) {
                    if (!$d['quality'] || !$d['url']) continue;

                    $stmt = $conn->prepare("
                        INSERT INTO downloads (content_id, season_id, episode_id, quality, url)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt->bind_param("iiiss", $content_id, $season_id, $episode_id, $d['quality'], $d['url']);
                    $stmt->execute();
                }
            }
        }
    }

    if ($downloadsChanged) {
        $stmt = $conn->prepare("
            UPDATE contents
            SET downloads_updated_at = NOW()
            WHERE id = ?
        ");

        $stmt->bind_param("i", $content_id);
        $stmt->execute();
    }

    header("Location: addnew.php?success=1");
    exit;
}
?>

<?php include '../partials/sidebar.php'; ?>
<?php include '../partials/header.php'; ?>

<div class="ml-64 mt-16 p-6 space-y-6">

    <div>
        <p class="text-sm text-white/50">Manage Content</p>
        <h2 class="text-2xl font-semibold text-white/90"><?= $content['title']; ?></h2>
    </div>

    <form method="POST" onsubmit="prepareData()">

        <input type="hidden" name="payload" id="payload">

        <div class="flex flex-col gap-2 mb-2">
            <!-- DATES -->
            <div class="w-full p-5 rounded-xl bg-white/5 border border-white/10">
                <h3 class="text-white font-semibold mb-3">Dates</h3>

                <div class="flex flex-col md:flex-row gap-2">
                    <div class="w-1/2 flex flex-row items-center justify-start">
                        <p class="pr-2 w-36">Release Date:</p>
                        <div class="flex flex-row items-center justify-center">
                            <input class="px-2 py-1 rounded-lg bg-white/10" type="date" name="release_date" value="<?= $release_date ?>">
                        </div>
                    </div>

                    <div class="w-1/2 flex flex-row items-center justify-start">
                        <p class="pr-2 w-36">Last Aired Date:</p>
                        <div class="flex flex-row items-center justify-center gap-2">
                            <input class="px-2 py-1 rounded-lg bg-white/10 border" type="date" name="last_date" id="last_date" value="<?= $last_date ?>">
                            <button 
                                type="button"
                                onclick="syncLastDate()"
                                class="px-2 py-1 text-white rounded-full bg-blue-500 border-2 border-blue-500 text-sm hover:bg-blue-600 hover:border-blue-600">
                                
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-wifi-sync-icon lucide-wifi-sync"><path d="M11.965 10.105v4L13.5 12.5a5 5 0 0 1 8 1.5"/><path d="M11.965 14.105h4"/><path d="M17.965 18.105h4L20.43 19.71a5 5 0 0 1-8-1.5"/><path d="M2 8.82a15 15 0 0 1 20 0"/><path d="M21.965 22.105v-4"/><path d="M5 12.86a10 10 0 0 1 3-2.032"/><path d="M8.5 16.429h.01"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CATEGORY SECTION -->
            <div class="w-full p-5 rounded-xl bg-white/5 border border-white/10">

                <h3 class="text-white font-semibold mb-3">Categories</h3>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">

                    <?php foreach ($allCategories as $cat): ?>
                        <label class="flex items-center gap-2 text-sm text-white/80">

                            <input type="checkbox"
                                name="categories[]"
                                value="<?= $cat['id'] ?>"
                                <?= in_array($cat['id'], $selectedCategories) ? 'checked' : '' ?>
                                class="accent-blue-500">

                            <?= htmlspecialchars($cat['name']) ?>

                        </label>
                    <?php endforeach; ?>

                </div>

            </div>
        </div>

        <!-- POSTERS & BACKDROPS -->

            <!-- POSTERS -->
            <div class="flex flex-col md:flex-row gap-2">
                <div class="w-1/2 p-5 rounded-xl bg-white/5 border border-white/10 mb-4 flex flex-col gap-2">
                    <h3 class="text-white font-semibold mb-1">Posters</h3>

                    <div class="flex flex-col items-center tect-left gap-1">
                        <p class="w-full">Poster 1:</p>
                        <input type="text" name="poster1" id="poster1" value="<?= $content['poster1'] ?>" class="w-full px-2 py-2 bg-white/10 border rounded-lg">
                    </div>
                    <div class="flex flex-col items-center tect-left gap-1">
                        <p class="w-full">Poster 2:</p>
                        <input type="text" name="poster2" id="poster2" value="<?= $content['poster2'] ?>" class="w-full px-2 py-2 bg-white/10 border rounded-lg">
                    </div>
                    <div class="flex flex-col items-center tect-left gap-1">
                        <p class="w-full">Poster 3:</p>
                        <input type="text" name="poster3" id="poster3" value="<?= $content['poster3'] ?>" class="w-full px-2 py-2 bg-white/10 border rounded-lg">
                    </div>
                    <div class="flex flex-col items-center tect-left gap-1">
                        <p class="w-full">Poster 4:</p>
                        <input type="text" name="poster4" id="poster4" value="<?= $content['poster4'] ?>" class="w-full px-2 py-2 bg-white/10 border rounded-lg">
                    </div>
                </div>

                <div class="w-1/2 p-5 rounded-xl bg-white/5 border border-white/10 mb-4 flex flex-col gap-2">
                    <h3 class="text-white font-semibold mb-1">Backdrops</h3>

                    <div class="flex flex-col items-center tect-left gap-1">
                        <p class="w-full">Backdrop 1: <span class="italic text-xs text-gray-400">(For Backdrop-card View)</span></p>
                        <input type="text" name="backdrop1" id="backdrop1" value="<?= $content['backdrop1'] ?>" class="w-full px-2 py-2 bg-white/10 border rounded-lg">
                    </div>
                    <div class="flex flex-col items-center tect-left gap-1">
                        <p class="w-full">Backdrop 2: <span class="italic text-xs text-gray-400">(For Hero-slider)</span></p>
                        <input type="text" name="backdrop2" id="backdrop2" value="<?= $content['backdrop2'] ?>" class="w-full px-2 py-2 bg-white/10 border rounded-lg">
                    </div>
                    <div class="flex flex-col items-center tect-left gap-1">
                        <p class="w-full">Backdrop 3: <span class="italic text-xs text-gray-400">(For Content's Details Page)</span></p>
                        <input type="text" name="backdrop3" id="backdrop3" value="<?= $content['backdrop3'] ?>" class="w-full px-2 py-2 bg-white/10 border rounded-lg">
                    </div>
                    <div class="flex flex-col items-center tect-left gap-1">
                        <p class="w-full">Backdrop 4: <span class="italic text-xs text-gray-400">(For Download Processing Page)</span></p>
                        <input type="text" name="backdrop4" id="backdrop4" value="<?= $content['backdrop4'] ?>" class="w-full px-2 py-2 bg-white/10 border rounded-lg">
                    </div>
                </div>
            </div>
    

        <!-- MOVIE -->
        <div id="movieSection"></div>

        <!-- TV -->
        <div id="tvSection"></div>

        <button type="submit"
            class="mt-6 px-6 py-2 rounded-lg bg-green-600 hover:bg-green-700
            transition hover:shadow-[0_0_10px_rgba(34,197,94,0.5)]">
            Save
        </button>

    </form>
</div>

<script>
const contentType = "<?= $type ?>";

let movieDownloads = <?= json_encode($downloadsData) ?>;
let seasons = <?= json_encode($seasonsData) ?>;

function addMovieDownload() {
    movieDownloads.push({ quality: '', url: '' });
    render();
}

function addSeason() {
    seasons.push({
        season_number: seasons.length + 1,
        title: '',
        downloads: [],
        episodes: []
    });
    render();
}

function addEpisode(sIndex) {
    seasons[sIndex].episodes.push({
        episode_number: seasons[sIndex].episodes.length + 1,
        title: '',
        downloads: []
    });
    render();
}

function addSeasonDownload(sIndex) {
    seasons[sIndex].downloads.push({ quality: '', url: '' });
    render();
}

function addEpisodeDownload(sIndex, eIndex) {
    seasons[sIndex].episodes[eIndex].downloads.push({ quality: '', url: '' });
    render();
}

function render() {

    if (contentType === 'movie') {

        let html = `
        <div class="p-5 rounded-xl bg-white/5 border border-white/10">

            <h3 class="text-white font-semibold mb-3">Movie Downloads</h3>

            <button type="button" onclick="addMovieDownload()"
                class="mb-3 px-3 py-1 rounded bg-blue-500 hover:bg-blue-600 text-sm">
                + Add Download
            </button>
        `;

        movieDownloads.forEach((d, i) => {
            html += `
            <div class="flex gap-2 mb-2">

                <input placeholder="Quality"
                    value="${d.quality ?? ''}"
                    onchange="movieDownloads[${i}].quality=this.value"
                    class="px-2 py-1 rounded bg-white/10 border border-white/50 text-sm w-1/4">

                <input placeholder="Download URL"
                    value="${d.url ?? ''}"
                    onchange="movieDownloads[${i}].url=this.value"
                    class="flex-1 px-2 py-1 rounded bg-white/10 border border-white/50 text-sm">

            </div>
            `;
        });

        html += `</div>`;

        document.getElementById('movieSection').innerHTML = html;
        document.getElementById('tvSection').innerHTML = '';
    }

    if (contentType === 'tv') {

        let html = `
        <button type="button" onclick="addSeason()"
            class="px-4 py-2 rounded bg-blue-500 hover:bg-blue-600 text-sm mb-4">
            + Add Season
        </button>
        `;

        seasons.forEach((s, sIndex) => {

            html += `
            <div class="p-5 rounded-xl bg-white/5 border border-white/10 mb-2">

                <div class="flex justify-between mb-2">
                    <h3 class="text-white font-semibold">Season ${sIndex + 1}</h3>

                    <button type="button" onclick="addEpisode(${sIndex})"
                        class="text-xs px-2 py-1 bg-purple-500 hover:bg-purple-600 rounded">
                        + Episode
                    </button>
                </div>

                <input placeholder="Season Title" value="${s.title ?? ''}"
                    onchange="seasons[${sIndex}].title=this.value"
                    class="mb-3 w-full px-2 py-1 rounded bg-white/10 border border-white/50 text-sm">

                <button type="button" onclick="addSeasonDownload(${sIndex})"
                    class="text-xs mb-3 px-2 py-1 bg-blue-500 rounded">
                    + Season Download
                </button>
            `;

            s.downloads.forEach((d, dIndex) => {
                html += `
                <div class="flex gap-2 mb-2">
                    <input placeholder="Quality" value="${d.quality ?? ''}"
                        onchange="seasons[${sIndex}].downloads[${dIndex}].quality=this.value"
                        class="px-2 py-1 rounded bg-white/10 border border-white/50 text-sm w-1/4">

                    <input placeholder="URL" value="${d.url ?? ''}"
                        onchange="seasons[${sIndex}].downloads[${dIndex}].url=this.value"
                        class="flex-1 px-2 py-1 rounded bg-white/10 border border-white/50 text-sm">
                </div>
                `;
            });

            s.episodes.forEach((ep, eIndex) => {

                html += `
                <div class="mt-3 p-3 rounded bg-white/5 border border-white/50">

                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-semibold">Episode ${eIndex + 1}</span>

                        <button type="button"
                            onclick="addEpisodeDownload(${sIndex}, ${eIndex})"
                            class="text-xs px-2 py-1 bg-blue-500 rounded">
                            + Download
                        </button>
                    </div>

                    <input placeholder="Episode Title" value="${ep.title ?? ''}"
                        onchange="seasons[${sIndex}].episodes[${eIndex}].title=this.value"
                        class="mb-2 w-full px-2 py-1 rounded bg-white/10 border border-white/50 text-sm">
                `;

                ep.downloads.forEach((d, dIndex) => {
                    html += `
                    <div class="flex gap-2 mb-2">
                        <input placeholder="Quality" value="${d.quality ?? ''}"
                            onchange="seasons[${sIndex}].episodes[${eIndex}].downloads[${dIndex}].quality=this.value"
                            class="px-2 py-1 rounded bg-white/10 border border-white/50 text-sm w-1/4">

                        <input placeholder="URL" value="${d.url ?? ''}"
                            onchange="seasons[${sIndex}].episodes[${eIndex}].downloads[${dIndex}].url=this.value"
                            class="flex-1 px-2 py-1 rounded bg-white/10 border border-white/50 text-sm">
                    </div>
                    `;
                });

                html += `</div>`;
            });

            html += `</div>`;
        });

        document.getElementById('tvSection').innerHTML = html;
        document.getElementById('movieSection').innerHTML = '';
    }
}

function prepareData() {
    if (contentType === 'movie') {
        document.getElementById('payload').value = JSON.stringify({
            type: 'movie',
            downloads: movieDownloads
        });
    } else {
        document.getElementById('payload').value = JSON.stringify({
            type: 'tv',
            seasons: seasons
        });
    }
}

render();

function syncLastDate() {
    const tmdbId = "<?= $content['tmdb_id'] ?>";
    const type = "<?= $type ?>";

    if (!tmdbId) {
        alert("No TMDB ID found");
        return;
    }

    let url = '';

    if (type === 'movie') {
        url = `https://api.themoviedb.org/3/movie/${tmdbId}?api_key=<?= TMDB_API_KEY ?>`;
    } else {
        url = `https://api.themoviedb.org/3/tv/${tmdbId}?api_key=<?= TMDB_API_KEY ?>`;
    }

    fetch(url)
        .then(res => res.json())
        .then(data => {
            console.log(data.last_air_date);
            document.getElementById('last_date').value = data.last_air_date;
        })
        .catch(() => {
            alert("Sync failed");
        });
}
</script>