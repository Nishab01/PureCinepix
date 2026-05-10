<?php include './includes/config.php'; ?>
<?php include './includes/header.php'; ?>
<?php include './includes/navbar.php'; ?>

<?php
$genre = $_GET['genre'] ?? '';
$category = $_GET['category'] ?? '';

$genre = intval($genre);
$category = intval($category);

/*
|--------------------------------------------------------------------------
| Build Query (Clean + Scalable)
|--------------------------------------------------------------------------
*/

$query = "
SELECT DISTINCT movies.* 
FROM movies
LEFT JOIN movie_genres ON movies.id = movie_genres.movie_id
LEFT JOIN movie_categories ON movies.id = movie_categories.movie_id
WHERE 1
";

if ($genre) {
    $query .= " AND movie_genres.genre_id = $genre";
}

if ($category) {
    $query .= " AND movie_categories.category_id = $category";
}

$query .= " ORDER BY movies.id DESC";

$movies = $conn->query($query);

/*
|--------------------------------------------------------------------------
| Fetch Filters
|--------------------------------------------------------------------------
*/

$genres = $conn->query("SELECT * FROM genres");
$cats = $conn->query("SELECT * FROM categories");
?>

<div class="pt-24 px-6 max-w-[1400px] mx-auto">

    <h1 class="text-2xl font-semibold mb-6">Browse Movies</h1>

    <!-- FILTERS -->
    <div class="mb-8 space-y-4">

        <!-- GENRES -->
        <div class="flex flex-wrap gap-2 items-center">

            <span class="text-sm text-white/60 mr-2">Genres:</span>

            <?php while($g = $genres->fetch_assoc()): ?>
                <a href="movies.php?genre=<?= $g['id'] ?><?= $category ? '&category='.$category : '' ?>"
                   class="px-3 py-1 rounded text-sm
                   <?= ($genre == $g['id']) ? 'bg-blue-500' : 'bg-white/10 hover:bg-blue-500' ?>">
                   
                   <?= $g['name'] ?>
                </a>
            <?php endwhile; ?>

        </div>

        <!-- CATEGORIES -->
        <div class="flex flex-wrap gap-2 items-center">

            <span class="text-sm text-white/60 mr-2">Categories:</span>

            <?php while($c = $cats->fetch_assoc()): ?>
                <a href="movies.php?category=<?= $c['id'] ?><?= $genre ? '&genre='.$genre : '' ?>"
                   class="px-3 py-1 rounded text-sm
                   <?= ($category == $c['id']) ? 'bg-blue-500' : 'bg-white/10 hover:bg-blue-500' ?>">
                   
                   <?= $c['name'] ?>
                </a>
            <?php endwhile; ?>
        </div>
        
        <div class="flex flex-wrap gap-2 items-center justify-end">
            <a href="movies.php" class="text-sm text-red-400 px-3 py-1 rounded text-sm bg-white/10 hover:bg-red-500 hover:text-white">Clear</a>
        </div>

        

    </div>

    <!-- MOVIES GRID -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-5">

        <?php if ($movies && $movies->num_rows > 0): ?>

            <?php while($m = $movies->fetch_assoc()): ?>
                <?php include './includes/card.php'; ?>
            <?php endwhile; ?>

        <?php else: ?>

            <p class="text-white/60 col-span-full">
                No movies found for selected filters.
            </p>

        <?php endif; ?>

    </div>

</div>

<?php include './includes/footer.php'; ?>