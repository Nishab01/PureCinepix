<?php
$recentUpdated = $conn->query("
    SELECT
        c.id,
        c.title,
        c.poster1,
        c.poster2,
        c.poster3,
        c.release_date,
        c.last_date,
        c.type,
        c.tmdb_rating,
        c.total_views,
        c.downloads_updated_at
    FROM contents c
    WHERE c.downloads_updated_at IS NOT NULL
    ORDER BY c.downloads_updated_at DESC
    LIMIT 20;
");
?>

<div id="recentUpdatedBody" class="max-w-[1400px] mx-auto px-4 mb-10 md:mb-20">

    <!-- HEADER -->
    <div class="flex justify-between items-center">
        <h2 class="text-xl md:text-2xl font-semibold text-white">
            Recently Updated
        </h2>

        <a href="../pages/browse.php?sort=updated"
           class="text-sm text-blue-400 hover:text-blue-300 transition">
            View All →
        </a>
    </div>

    <!-- SCROLLER -->
    <div id="recentUpdatedWrapper" class="relative overflow-hidden">

        <!-- LEFT BUTTON -->
        <button onclick="scrollRecentUpdated(-1)"
            class="absolute left-1 top-1/2 -translate-y-1/2 z-20
            bg-black/40 hover:bg-black/70 backdrop-blur-md
            md:p-3 p-1 rounded-full transition hover:scale-105">
            ‹
        </button>

        <!-- RIGHT BUTTON -->
        <button onclick="scrollRecentUpdated(1)"
            class="absolute right-1 top-1/2 -translate-y-1/2 z-20
            bg-black/40 hover:bg-black/70 backdrop-blur-md
            md:p-3 p-1 rounded-full transition hover:scale-105">
            ›
        </button>

        <!-- SLIDER -->
        <div id="recentUpdatedSlider"
            class="flex gap-2 md:gap-4 overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory py-4">

            <?php while ($item = $recentUpdated->fetch_assoc()): ?>
                <?php
                    $content = $item;
                    include '../includes/poster-card.php';
                ?>
            <?php endwhile; ?>

        </div>
    </div>
</div>

<script>
const recentUpdatedSlider =
    document.getElementById('recentUpdatedSlider');

function scrollRecentUpdated(direction) {
    const amount = recentUpdatedSlider.clientWidth * 0.8;

    recentUpdatedSlider.scrollBy({
        left: direction * amount,
        behavior: 'smooth'
    });
}
</script>