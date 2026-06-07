<?php
$currentpage = "contents";
$head_name = "Add New Content";
$page_title = "Add New Content";

require_once '../partials/guard.php';
require_once '../../config/config.php';
require_once '../../config/constants.php';

$message = "";
$results = [];

// FORMAT DATE FUNCTION
function formatDate($date) {
    if (!$date) return '';
    return date("j F Y", strtotime($date)); // 7 August 1998
}

// SEARCH TMDB
if (isset($_GET['q'])) {

    $query = urlencode($_GET['q']);
    $api_key = TMDB_API_KEY;

    $url = "https://api.themoviedb.org/3/search/multi?api_key=$api_key&query=$query";

    $response = @file_get_contents($url);

    if ($response) {
        $data = json_decode($response, true);

        $count = 0;

        foreach ($data['results'] as $item) {
            if ($count >= 10) break;

            // Skip people
            if ($item['media_type'] === 'person') continue;

            $tmdb_id = (int)$item['id'];

            // CHECK DUPLICATE
            $check = $conn->prepare("SELECT id FROM contents WHERE tmdb_id = ?");
            $check->bind_param("i", $tmdb_id);
            $check->execute();
            $res = $check->get_result();

            $exists = $res->num_rows > 0;

            $results[] = [
                'id' => $tmdb_id,
                'title' => $item['title'] ?? $item['name'],
                'backdrop' => $item['backdrop_path']
                    ? "https://image.tmdb.org/t/p/w780" . $item['backdrop_path']
                    : '',
                'release' => $item['release_date'] ?? $item['first_air_date'] ?? '',
                'type' => $item['media_type'],
                'exists' => $exists
            ];

            $count++; 
        }
    }
}
?>

<?php include '../partials/sidebar.php'; ?>
<?php include '../partials/header.php'; ?>

<div class="ml-64 mt-16 p-6 space-y-6">

    <!-- HEADER -->
    <div class="flex flex-row items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-white/90">Contents</h2>
            <p class="text-sm text-white/50">Search and add content from TMDB</p>
        </div>
        <div>
            <!-- Manually content adding button -->
            <a href="addmanual.php" class="px-5 py-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-sm transition
                hover:shadow-[0_0_10px_rgba(59,130,246,0.5)]">Add Manually</a>
        </div>
    </div>

    <!-- SEARCH -->
    <form method="GET" class="flex gap-4">

        <input type="text" name="q" placeholder="Search movies or TV shows..."
            value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
            class="flex-1 px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-sm text-white focus:outline-none">

        <button class="px-5 py-2 rounded-lg bg-blue-500 hover:bg-blue-600
            text-sm font-medium transition
            hover:shadow-[0_0_10px_rgba(59,130,246,0.5)]">
            Search
        </button>

    </form>

    <!-- MESSAGE -->
    <?php if ($message): ?>
        <div class="p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400">
            Content saved successfully!
        </div>
    <?php endif; ?>

    <!-- RESULTS -->
    <?php if (!empty($results)): ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">

            <?php foreach ($results as $r): ?>

                <div class="group bg-white/5 border border-white/10 rounded-xl p-3
                            hover:bg-white/10 transition backdrop-blur-lg">

                    <!-- POSTER -->
                    <div class="aspect-video overflow-hidden rounded-lg mb-3">
                        <img src="<?= $r['backdrop'] ?>"
                            class="w-full h-full object-cover rounded-lg group-hover:scale-105 transition duration-300">
                    </div>

                    <!-- TYPE BADGE -->
                    <div class="mb-1">
                        <?php if ($r['type'] === 'movie'): ?>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/30 transition group-hover:scale-105">
                                Movie
                            </span>
                        <?php elseif ($r['type'] === 'tv'): ?>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/30 transition group-hover:scale-105">
                                TV Show
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- TITLE -->
                    <h3 class="text-sm text-white font-medium line-clamp-1">
                        <?= htmlspecialchars($r['title']) ?>
                    </h3>

                    <!-- DATE -->
                    <p class="text-xs text-white/50 mb-2">
                        <?= formatDate($r['release']) ?>
                    </p>

                    <!-- ACTION -->
                    <?php if ($r['exists']): ?>

                        <div class="text-green-400 text-xs font-medium">
                            ✓ Already Added
                        </div>

                    <?php else: ?>

                        <a href="./add.php?tmdb_id=<?= $r['id'] ?>&type=<?= $r['type'] ?>"
                            class="block text-center mt-2 px-3 py-1.5 rounded-lg
                                   bg-blue-500 hover:bg-blue-600 text-xs
                                   transition hover:shadow-[0_0_10px_rgba(59,130,246,0.5)]">
                            Add
                        </a>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>