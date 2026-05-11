<?php include '../includes/header.php'; ?>
<?php include '../includes/config.php'; ?>

<?php 
    $api_key = $TMDB_API_KEY;
    $results = [];

    if (isset($_GET['query'])) {
        $query = urlencode($_GET['query']);

        $url = "https://api.themoviedb.org/3/search/movie?api_key=$api_key&query=$query";

        $response = file_get_contents($url);
        $data = json_decode($response, true);

        $results = $data['results'] ?? [];
    }
?>

<?php include '../includes/admin_sidebar.php'; ?>

<div class="ml-64 p-6">

    <h1 class="text-2xl font-semibold mb-6">
        Add Movie (TMDB)
    </h1>

    <form method="GET" class="mb-6">
        <input type="text" name="query" placeholder="Search movie..."
            class="px-4 py-2 bg-white/10 rounded-lg w-80">

        <button class="bg-blue-500 px-4 py-2 rounded-lg ml-2">
            Search
        </button>
    </form>

    <!-- Results -->
    <div class="grid grid-cols-5 gap-6">

        <?php foreach ($results as $movie): ?>

            <div class="bg-white/5 p-3 rounded-lg hover:bg-white/10 transition">

                <img src="https://image.tmdb.org/t/p/w500<?= $movie['poster_path'] ?>"
                     class="rounded-lg mb-2">

                <h3 class="text-sm font-semibold">
                    <?= $movie['title'] ?>
                </h3>

                <p class="text-xs text-white/60 mb-2">
                    <?= $movie['release_date'] ?? 'N/A' ?>
                </p>

                <!-- Add Button -->
                <button class="w-full bg-blue-500 py-1 rounded-lg text-sm hover:bg-blue-600" onclick='openModal(
                    "<?= addslashes($movie['title']) ?>",
                    "<?= addslashes($movie['overview']) ?>",
                    "<?= $movie['poster_path'] ?>",
                    "<?= $movie['backdrop_path'] ?>",
                    "<?= $movie['release_date'] ?>",
                    <?= json_encode($movie['genre_ids']) ?>,
                    <?= $movie['id'] ?>
                )'
                >
                Add
                </button>

            </div>

        <?php endforeach; ?>

    </div>

    <div id="movieModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50">

        <div class="bg-[#0B0F29] p-6 rounded-xl w-[400px]">

            <h2 class="text-lg font-semibold mb-4">Add Movie</h2>

            <form method="POST" action="save_movie.php">

                <input type="hidden" name="title" id="m_title">
                <input type="hidden" name="description" id="m_desc">
                <input type="hidden" name="poster" id="m_poster">
                <input type="hidden" name="backdrop" id="m_backdrop">
                <input type="hidden" name="release_date" id="m_date">
                <input type="hidden" name="genres" id="m_genres">
                <input type="hidden" name="tmdb_id" id="m_tmdb_id">

                <!-- Links -->
                <div id="linksContainer">
                    <div class="p-2 border border-white link-item mb-3">
                        <input type="text" name="quality[]" placeholder="Quality"
                            class="w-full mb-2 px-3 py-2 bg-white/10 rounded">

                        <input type="text" name="url[]" placeholder="URL"
                            class="w-full mb-2 px-3 py-2 bg-white/10 rounded">
                    </div>
                </div>

                <button type="button" onclick="addLink()" class="mb-4 text-blue-400 text-sm">+ Add Another Link</button>

                <label class="block mb-2 text-sm">Select Categories</label>

                <div class="mb-4 space-y-2">
                    <?php
                    $cats = $conn->query("SELECT * FROM categories");
                    while($c = $cats->fetch_assoc()):
                    ?>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="categories[]" value="<?= $c['id'] ?>">
                            <span><?= $c['name'] ?></span>
                        </label>
                    <?php endwhile; ?>
                </div>

                <button class="w-full bg-blue-500 py-2 rounded">
                    Save Movie
                </button>

            </form>

        </div>

    </div>

</div>

<script>
    function openModal(title, desc, poster, backdrop, date, genres, tmdb_id) {
        document.getElementById('movieModal').classList.remove('hidden');
        document.getElementById('movieModal').classList.add('flex');
        document.getElementById('m_title').value = title;
        document.getElementById('m_desc').value = desc;
        document.getElementById('m_poster').value = poster;
        document.getElementById('m_backdrop').value = backdrop;
        document.getElementById('m_date').value = date;
        document.getElementById('m_genres').value = JSON.stringify(genres);
        document.getElementById('m_tmdb_id').value = tmdb_id;
    }
</script>

<script>
    function addLink() {
        let container = document.getElementById('linksContainer');

        let html = `
        <div class="p-2 border border-white link-item mb-3">
            <input type="text" name="quality[]" placeholder="Quality"
                class="w-full mb-2 px-3 py-2 bg-white/10 rounded">

            <input type="text" name="url[]" placeholder="URL"
                class="w-full mb-2 px-3 py-2 bg-white/10 rounded">
        </div>
        `;

        container.insertAdjacentHTML('beforeend', html);
    }
</script>

<?php include '../includes/footer.php'; ?>