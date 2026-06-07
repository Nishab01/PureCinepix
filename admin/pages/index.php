<?php
$currentpage = "index";
$head_name = "Admin Dashboard";
$page_title = "Admin Dashboard";

require_once '../partials/guard.php';
require_once '../../config/config.php';
require_once '../../config/constants.php';

// STATS (basic for now)
$total_movies = $conn->query("SELECT COUNT(*) as c FROM contents WHERE type='movie'")->fetch_assoc()['c'] ?? 0;

$total_tv = $conn->query("SELECT COUNT(*) as c FROM contents WHERE type='tv'")->fetch_assoc()['c'] ?? 0;

$total_genres = $conn->query("SELECT COUNT(*) as c FROM genres")->fetch_assoc()['c'] ?? 0;

$total_categories = $conn->query("SELECT COUNT(*) as c FROM categories")->fetch_assoc()['c'] ?? 0;

$total_users = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'] ?? 0;

$total_links = $conn->query("SELECT COUNT(*) as c FROM downloads")->fetch_assoc()['c'] ?? 0;
?>

<?php include '../partials/sidebar.php'; ?>
<?php include '../partials/header.php'; ?>

<!-- MAIN -->
<div class="ml-64 mt-16 p-6 space-y-6">

    <!-- TITLE -->
    <div>
        <h2 class="text-2xl font-semibold text-white/90">
            Dashboard Overview
        </h2>
        <p class="text-sm text-white/50">
            Quick insights of your platform
        </p>
    </div>

    <div class="grid md:grid-cols-3 gap-6">

        <div class="p-6 rounded-xl bg-gradient-to-br from-blue-500/10 to-blue-500/5
                    border border-blue-500/20 backdrop-blur-lg">

            <h3 class="text-lg font-semibold text-blue-400 mb-2">
                Add New Content
            </h3>

            <p class="text-sm text-white/60 mb-4">
                Upload Movies or TV Shows to your platform
            </p>

            <a href="../contents/addnew.php"
               class="inline-block px-5 py-2 rounded-lg bg-blue-500 hover:bg-blue-600
                    text-sm font-medium transition
                    hover:shadow-[0_0_10px_rgba(59,130,246,0.5)]">
                Add Content
            </a>

        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-purple-500/10 to-purple-500/5
                    border border-purple-500/20 backdrop-blur-lg">

            <h3 class="text-lg font-semibold text-purple-400 mb-2">
                Edit Category
            </h3>

            <p class="text-sm text-white/60 mb-4">
                Edit categories for all kinds of Movies & TV Shows
            </p>

            <a href="../categories/"
               class="inline-block px-5 py-2 rounded-lg bg-purple-500 hover:bg-purple-600
                      text-sm font-medium transition
                      hover:shadow-[0_0_10px_rgba(168,85,247,0.5)]">
                All Categories
            </a>

        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-green-600/10 to-green-600/5
                    border border-green-600/20 backdrop-blur-lg">

            <h3 class="text-lg font-semibold text-green-600 mb-2">
                Manage Users
            </h3>

            <p class="text-sm text-white/60 mb-4">
                Manage registered users
            </p>

            <a href="../users/"
               class="inline-block px-5 py-2 rounded-lg bg-green-600 hover:bg-green-700
                      text-sm font-medium transition
                      hover:shadow-[0_0_10px_rgba(22,163,74,0.5)]">
                Manage Users
            </a>

        </div>

    </div>

    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

        <div class="p-5 rounded-xl bg-white/5 border border-white/10
                    backdrop-blur-lg hover:bg-white/10 transition
                    shadow-[0_0_20px_rgba(0,0,0,0.4)]">

            <p class="text-sm text-white/60">Movies</p>
            <h3 class="text-3xl font-bold text-blue-400 mt-2">
                <?= $total_movies ?>
            </h3>
        </div>

        <div class="p-5 rounded-xl bg-white/5 border border-white/10
                    backdrop-blur-lg hover:bg-white/10 transition
                    shadow-[0_0_20px_rgba(0,0,0,0.4)]">

            <p class="text-sm text-white/60">TV Shows</p>
            <h3 class="text-3xl font-bold text-blue-400 mt-2">
                <?= $total_tv ?>
            </h3>
        </div>

        <div class="p-5 rounded-xl bg-white/5 border border-white/10
                    backdrop-blur-lg hover:bg-white/10 transition
                    shadow-[0_0_20px_rgba(0,0,0,0.4)]">

            <p class="text-sm text-white/60">Links</p>
            <h3 class="text-3xl font-bold text-blue-400 mt-2">
                <?= $total_links ?>
            </h3>
        </div>

        <div class="p-5 rounded-xl bg-white/5 border border-white/10
                    backdrop-blur-lg hover:bg-white/10 transition">

            <p class="text-sm text-white/60">Genres</p>
            <h3 class="text-3xl font-bold text-purple-400 mt-2">
                <?= $total_genres ?>
            </h3>
        </div>

        <div class="p-5 rounded-xl bg-white/5 border border-white/10
                    backdrop-blur-lg hover:bg-white/10 transition">

            <p class="text-sm text-white/60">Categories</p>
            <h3 class="text-3xl font-bold text-purple-400 mt-2">
                <?= $total_categories ?>
            </h3>
        </div>

        <div class="p-5 rounded-xl bg-white/5 border border-white/10
                    backdrop-blur-lg hover:bg-white/10 transition">

            <p class="text-sm text-white/60">Users</p>
            <h3 class="text-3xl font-bold text-green-400 mt-2">
                <?= $total_users ?>
            </h3>
        </div>

        <!-- LINKS -->
        

    </div>

</div>