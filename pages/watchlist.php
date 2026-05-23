<?php
$pagename = "My Watchlist";
require_once '../config/config.php';
require_once '../config/constants.php';

// ✅ LOGIN CHECK
if (!isset($_SESSION['user'])) {
    die("Login required");
}

$user_id = $_SESSION['user']['id'];

// ✅ FETCH WATCHLIST
$stmt = $conn->prepare("
    SELECT c.*
    FROM watchlist w
    JOIN contents c ON w.content_id = c.id
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

include '../includes/header.php';
?>

<div class="mt-20 max-w-[1400px] mx-auto px-6">

    <h1 class="text-2xl md:text-3xl font-bold mb-4">
        My Watchlist
    </h1>

    <?php if ($result->num_rows > 0): ?>

        <div id="watchlist-grid">

            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-6 lg:grid-cols-6 gap-2 md:gap-4">
                
                <?php while($item = $result->fetch_assoc()): ?>

                    <?php
                        $content = $item;
                        $forwatchlist = true;
                        include '../includes/poster-card.php';
                    ?>

                <?php endwhile; ?>

            </div>

        </div>

        <div id="empty-state"
            class="hidden flex flex-col items-center justify-center py-20 text-center">

            <div class="text-5xl mb-4">🎬</div>

            <h2 class="text-2xl font-semibold mb-2 text-white">
                Your Watchlist is Empty
            </h2>

            <p class="text-white/60 mb-6">
                Start adding movies you want to watch
            </p>

            <a href="../pages/movies.php"
                class="bg-blue-500 hover:bg-blue-600 px-6 py-2 rounded-lg font-semibold transition">
                Browse Movies →
            </a>

        </div>

    <?php else: ?>

        <div class="flex flex-col items-center justify-center py-20 text-center">

            <div class="text-5xl mb-4">🎬</div>

            <h2 class="text-2xl font-semibold mb-2 text-white">
                Your Watchlist is Empty
            </h2>

            <p class="text-white/60 mb-6">
                Start adding movies you want to watch
            </p>

            <a href="../pages/movies.php"
                class="bg-blue-500 hover:bg-blue-600 px-6 py-2 rounded-lg font-semibold transition">
                Browse Movies →
            </a>

        </div>

    <?php endif; ?>

</div>
