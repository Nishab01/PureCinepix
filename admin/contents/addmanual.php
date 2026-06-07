<?php
$currentpage = "contents";
$head_name = "Add Manual Content";
$page_title = "Add Manual Content";

require_once '../partials/guard.php';
require_once '../../config/config.php';
require_once '../../config/constants.php';

/* =========================
   FETCH GENRES
========================= */
$genres = [];
$res = $conn->query("
    SELECT *
    FROM genres
    ORDER BY name ASC
");
while ($row = $res->fetch_assoc()) {
    $genres[] = $row;
}

/* =========================
   FETCH CATEGORIES
========================= */
$categories = [];
$res = $conn->query("
    SELECT *
    FROM categories
    ORDER BY name ASC
");
while ($row = $res->fetch_assoc()) {
    $categories[] = $row;
}

include '../partials/sidebar.php';
include '../partials/header.php';
?>

<div class="ml-64 mt-16 p-6 space-y-6">

    <div>
        <h2 class="text-2xl font-semibold text-white/90">
            Add Manual Content
        </h2>

        <p class="text-sm text-white/50">
            Create content without TMDB
        </p>
    </div>

    <form method="POST" action="manual_save.php" class="space-y-2">

    
        <h3 class="text-lg text-white font-semibold mb-2">
            Basic Information
        </h3>

        <!-- BASIC INFO -->
        <div class="p-4 rounded-xl bg-white/5 border border-white/10">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">

                <div>
                    <label class="block text-sm mb-1 font-semibold">
                        Title <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        name="title"
                        required
                        class="w-full p-2 rounded-lg bg-white/10 border border-white/20 text-sm">
                </div>

                <div>
                    <label class="block text-sm mb-1 font-semibold">
                        Type <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="type"
                        required
                        class="w-full p-2 rounded-lg bg-white/10 border border-white/20 text-sm">

                        <option class="text-black" value="" selected hidden none>
                            Select content type
                        </option>

                        <option class="text-black" value="movie">
                            Movie
                        </option>

                        <option class="text-black" value="tv">
                            TV Show
                        </option>

                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-1 font-semibold">
                        Release Date <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="date"
                        name="release_date"
                        required
                        class="w-full p-2 rounded-lg bg-white/10 border border-white/20 text-sm">
                </div>

                <div>
                    <label class="block text-sm mb-1 font-semibold">
                        Last Air Date
                    </label>

                    <input
                        type="date"
                        name="last_date"
                        class="w-full p-2 rounded-lg bg-white/10 border border-white/20 text-sm">
                </div>

                <div>
                    <label class="block text-sm mb-1 font-semibold">
                        Rating
                    </label>

                    <input
                        type="number"
                        step="0.1"
                        min="0"
                        max="10"
                        name="rating"
                        placeholder="8.5"
                        class="w-full p-2 rounded-lg bg-white/10 border border-white/20 text-sm">
                </div>

                <div>
                    <label class="block text-sm mb-1 font-semibold">
                        Trailer
                    </label>

                    <input
                        type="text"
                        name="trailer"
                        placeholder="dQw4w9WgXcQ"
                        class="w-full p-2 rounded-lg bg-white/10 border border-white/20 text-sm">
                </div>

            </div>

        </div>

        <!-- DESCRIPTION -->
        <div class="p-5 rounded-xl bg-white/5 border border-white/10">

            <h3 class="text-white font-semibold mb-3">
                Description
            </h3>

            <textarea
                name="description"
                rows="2"
                class="w-full p-2 rounded-lg bg-white/10 border border-white/20 text-sm"></textarea>

        </div>

        <!-- KEYWORDS & ALT TITLES -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">

            <div class="p-5 rounded-xl bg-white/5 border border-white/10">

                <h3 class="text-white font-semibold mb-3">
                    Keywords
                </h3>

                <textarea
                    name="keywords"
                    rows="2"
                    placeholder="action, thriller, detective"
                    class="w-full p-2 rounded-lg bg-white/10 border border-white/20 text-sm"></textarea>

            </div>

            <div class="p-5 rounded-xl bg-white/5 border border-white/10">

                <h3 class="text-white font-semibold mb-3">
                    Alternative Titles
                </h3>

                <textarea
                    name="alt_titles"
                    rows="2"
                    placeholder="title 1, title 2, title 3"
                    class="w-full p-2 rounded-lg bg-white/10 border border-white/20 text-sm"></textarea>

            </div>

        </div>

        <!-- GENRES -->
        <div class="p-5 rounded-xl bg-white/5 border border-white/10">

            <h3 class="text-white font-semibold mb-3">
                Genres
            </h3>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 text-sm">

                <?php foreach ($genres as $genre): ?>

                    <label class="flex items-center gap-2 text-sm">

                        <input
                            type="checkbox"
                            name="genres[]"
                            value="<?= $genre['id'] ?>"
                            class="accent-blue-500">

                        <?= htmlspecialchars($genre['name']) ?>

                    </label>

                <?php endforeach; ?>

            </div>

        </div>

        <!-- CATEGORIES -->
        <div class="p-5 rounded-xl bg-white/5 border border-white/10">

            <h3 class="text-white font-semibold mb-3">
                Categories
            </h3>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 text-sm">

                <?php foreach ($categories as $category): ?>

                    <label class="flex items-center gap-2 text-sm">

                        <input
                            type="checkbox"
                            name="categories[]"
                            value="<?= $category['id'] ?>"
                            class="accent-blue-500">

                        <?= htmlspecialchars($category['name']) ?>

                    </label>

                <?php endforeach; ?>

            </div>

        </div>
                
        <!-- IMAGES -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">

            <!-- POSTERS -->
            <div class="p-5 rounded-xl bg-white/5 border border-white/10">
                <h3 class="text-white font-semibold mb-3">
                    Posters
                </h3>

                <div class="flex flex-col gap-3 text-sm">

                    <div>
                        <p class="mb-1">
                            Poster 1:
                            <span class="italic text-xs text-gray-400">
                                (Required / Main Poster)
                            </span>
                        </p>

                        <input
                            type="text"
                            name="poster1"
                            required
                            class="w-full p-2 rounded-lg bg-white/10 border">
                    </div>

                    <div>
                        <p class="mb-1">
                            Poster 2:
                            <span class="italic text-xs text-gray-400">
                                (Alternative Poster)
                            </span>
                        </p>

                        <input
                            type="text"
                            name="poster2"
                            class="w-full p-2 rounded-lg bg-white/10 border">
                    </div>

                    <div>
                        <p class="mb-1">
                            Poster 3:
                            <span class="italic text-xs text-gray-400">
                                (Alternative Poster)
                            </span>
                        </p>

                        <input
                            type="text"
                            name="poster3"
                            class="w-full p-2 rounded-lg bg-white/10 border">
                    </div>

                    <div>
                        <p class="mb-1">
                            Poster 4:
                            <span class="italic text-xs text-gray-400">
                                (Alternative Poster)
                            </span>
                        </p>

                        <input
                            type="text"
                            name="poster4"
                            class="w-full p-2 rounded-lg bg-white/10 border">
                    </div>

                </div>
            </div>

            <!-- BACKDROPS -->
            <div class="p-5 rounded-xl bg-white/5 border border-white/10">
                <h3 class="text-white font-semibold mb-3">
                    Backdrops
                </h3>

                <div class="flex flex-col gap-3 text-sm">

                    <div>
                        <p class="mb-1">
                            Backdrop 1:
                            <span class="italic text-xs text-gray-400">
                                (For Backdrop-card View)
                            </span>
                        </p>

                        <input
                            type="text"
                            name="backdrop1"
                            class="w-full p-2 rounded-lg bg-white/10 border">
                    </div>

                    <div>
                        <p class="mb-1">
                            Backdrop 2:
                            <span class="italic text-xs text-gray-400">
                                (For Hero-slider)
                            </span>
                        </p>

                        <input
                            type="text"
                            name="backdrop2"
                            class="w-full p-2 rounded-lg bg-white/10 border">
                    </div>

                    <div>
                        <p class="mb-1">
                            Backdrop 3:
                            <span class="italic text-xs text-gray-400">
                                (For Content's Details Page)
                            </span>
                        </p>

                        <input
                            type="text"
                            name="backdrop3"
                            class="w-full p-2 rounded-lg bg-white/10 border">
                    </div>

                    <div>
                        <p class="mb-1">
                            Backdrop 4:
                            <span class="italic text-xs text-gray-400">
                                (Extra)
                            </span>
                        </p>

                        <input
                            type="text"
                            name="backdrop4"
                            class="w-full p-2 rounded-lg bg-white/10 border">
                    </div>

                </div>
            </div>

        </div>

        <!-- SAVE -->
        <button
            type="submit"
            class="px-6 py-2 rounded-lg bg-green-600 hover:bg-green-700 transition hover:shadow-[0_0_10px_rgba(34,197,94,0.5)]">

            Save & Continue

        </button>

    </form>

</div>