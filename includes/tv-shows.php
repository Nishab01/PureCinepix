<?php
$tvshows = $conn->query("
    SELECT id, title, poster1, poster2, poster3, release_date, last_date, type, tmdb_rating
    FROM contents
    WHERE type = 'tv'
    ORDER BY COALESCE(last_date, release_date) DESC
    LIMIT 50
");
?>

<div id="tvshowsbody" class="max-w-[1400px] mx-auto px-4 mb-10 md:mb-20">

    <!-- HEADER -->
    <div class="flex justify-between items-center">
        <h2 class="text-xl md:text-2xl font-semibold text-white">
            TV Shows
        </h2>

        <a href="../pages/browse.php?types=tv"
           class="text-sm text-blue-400 hover:text-blue-300 transition">
            View All →
        </a>
    </div>

    <!-- SCROLLER -->
    <div id="tvshowsWrapper" class="relative overflow-hidden">

        <!-- LEFT BUTTON -->
        <button onclick="scrollTV(-1)"
            class="absolute left-1 top-1/2 -translate-y-1/2 z-20
            bg-black/40 hover:bg-black/70 backdrop-blur-md
            md:p-3 p-1 rounded-full transition hover:scale-105">
            ‹
        </button>

        <!-- RIGHT BUTTON -->
        <button onclick="scrollTV(1)"
            class="absolute right-1 top-1/2 -translate-y-1/2 z-20
            bg-black/40 hover:bg-black/70 backdrop-blur-md
            md:p-3 p-1 rounded-full transition hover:scale-105">
            ›
        </button>

        <!-- SLIDER -->
        <div id="tvshowsSlider"
            class="flex gap-2 md:gap-4 overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory py-4">

            <?php while ($item = $tvshows->fetch_assoc()): ?>
                <?php 
                    $content = $item;
                    include '../includes/poster-card.php'; 
                ?>
            <?php endwhile; ?>

        </div>

    </div>

</div>

<script>
const tvSlider = document.getElementById('tvshowsSlider');
let tvAutoScroll;

// 👉 MANUAL SCROLL
function scrollTV(direction) {
    const amount = tvSlider.clientWidth * 0.8;

    tvSlider.scrollBy({
        left: direction * amount,
        behavior: 'smooth'
    });
}

// 👉 AUTO RESET ONLY (NOT CONTINUOUS AUTO SCROLL)
function startTVAutoReset() {
    tvAutoScroll = setInterval(() => {

        const maxScroll = tvSlider.scrollWidth - tvSlider.clientWidth;

        if (tvSlider.scrollLeft >= maxScroll - 5) {
            tvSlider.scrollTo({
                left: 0,
                behavior: 'smooth'
            });
        }

    }, 5000); // wait 5s before reset
}

startTVAutoReset();
</script>