<?php
$currentpage = "genres";
$head_name = "Manage Genres";
$page_title = "Manage Genres";

require_once '../partials/guard.php';
require_once '../../config/config.php';
require_once '../../config/constants.php';

$message = "";

/* =========================
   SYNC GENRES
========================= */
if (isset($_POST['sync_genres'])) {

    $api_key = TMDB_API_KEY;

    function syncGenres($conn, $api_key) {

        $endpoints = [
            "https://api.themoviedb.org/3/genre/movie/list?api_key=$api_key",
            "https://api.themoviedb.org/3/genre/tv/list?api_key=$api_key"
        ];

        foreach ($endpoints as $url) {

            $response = @file_get_contents($url);
            if (!$response) continue;

            $data = json_decode($response, true);

            foreach ($data['genres'] as $g) {

                $id = (int)$g['id'];
                $name = $conn->real_escape_string($g['name']);

                $conn->query("
                    INSERT INTO genres (id, name)
                    VALUES ($id, '$name')
                    ON DUPLICATE KEY UPDATE name='$name'
                ");
            }
        }

        return "Genres synced successfully!";
    }

    $message = syncGenres($conn, $api_key);
}

/* =========================
   FETCH GENRES
========================= */
$genres = $conn->query("SELECT * FROM genres ORDER BY name ASC");
$total = $conn->query("SELECT COUNT(*) as g FROM genres")->fetch_assoc()['g'];
?>

<?php include '../partials/sidebar.php'; ?>
<?php include '../partials/header.php'; ?>

<!-- MAIN -->
<div class="ml-64 mt-16 p-6 space-y-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center">

        <div>
            <h2 class="text-2xl font-semibold text-white/90">Genres</h2>
            <p class="text-sm text-white/50">Manage content genres</p>
        </div>

        <div class="text-right">
            <p class="text-sm text-white/50">Total</p>
            <p class="text-xl font-bold text-yellow-400"><?= $total ?></p>
        </div>

    </div>

    <!-- MESSAGE -->
    <?php if ($message): ?>
        <div class="p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <!-- ACTION CARD -->
    <div class="p-6 rounded-xl bg-white/5 border border-white/10 backdrop-blur-lg">

        <form method="POST">
            <button name="sync_genres"
                class="px-5 py-2 rounded-lg bg-blue-500 hover:bg-blue-600
                       text-sm font-medium transition
                       hover:shadow-[0_0_10px_rgba(59,130,246,0.5)]">
                Sync with TMDB
            </button>
        </form>

    </div>

    <!-- GENRES LIST -->
    <div class="p-6 rounded-xl bg-white/5 border border-white/10 backdrop-blur-lg">

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

            <?php while ($g = $genres->fetch_assoc()): ?>
                <div class="px-4 py-2 rounded-lg bg-white/10 text-sm text-white/80
                            hover:bg-white/20 transition
                            border border-white/5">
                    <?= htmlspecialchars($g['name']) ?>
                </div>
            <?php endwhile; ?>

        </div>

    </div>

</div>