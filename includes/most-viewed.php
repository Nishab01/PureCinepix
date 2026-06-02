<?php
$mostViewed = $conn->query("
    SELECT
        id,
        title,
        poster1,
        poster2,
        poster3,
        release_date,
        last_date,
        type,
        tmdb_rating,
        total_views
    FROM contents
    ORDER BY total_views DESC, COALESCE(last_date, release_date) DESC
    LIMIT 50
");
?>

<div id="mostViewedBody" class="max-w-[1400px] mx-auto px-4 mb-10 md:mb-20">
    <!-- HEADER -->
    <div class="flex justify-between items-center">
        <h2 class="text-xl md:text-2xl font-semibold text-white">
            Most Viewed
        </h2>

        <a href="../pages/browse.php?sort=views"
           class="text-sm text-blue-400 hover:text-blue-300 transition">
            View All →
        </a>
    </div>

    <!-- SCROLLER -->
    <div id="mostViewedWrapper" class="relative overflow-hidden">

        <!-- LEFT BUTTON -->
        <button onclick="scrollMostViewed(-1)"
            class="absolute left-1 top-1/2 -translate-y-1/2 z-20
            bg-black/40 hover:bg-black/70 backdrop-blur-md
            md:p-3 p-1 rounded-full transition hover:scale-105">
            ‹
        </button>

        <!-- RIGHT BUTTON -->
        <button onclick="scrollMostViewed(1)"
            class="absolute right-1 top-1/2 -translate-y-1/2 z-20
            bg-black/40 hover:bg-black/70 backdrop-blur-md
            md:p-3 p-1 rounded-full transition hover:scale-105">
            ›
        </button>

        <!-- SLIDER -->
        <div id="mostViewedSlider"
            class="flex gap-2 md:gap-4 overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory py-4">

            <?php while ($item = $mostViewed->fetch_assoc()): ?>
                <?php
                    $content = $item;
                    include '../includes/poster-card.php';
                ?>
            <?php endwhile; ?>

        </div>
    </div>
</div>

<script>
const mostViewedSlider = document.getElementById('mostViewedSlider');

let mostViewedAutoScroll;

// 👉 MANUAL SCROLL
function scrollMostViewed(direction) {
    const amount = mostViewedSlider.clientWidth * 0.8;

    mostViewedSlider.scrollBy({
        left: direction * amount,
        behavior: 'smooth'
    });
}

// 👉 AUTO RESET
function startMostViewedAutoReset() {
    mostViewedAutoScroll = setInterval(() => {

        const maxScroll =
            mostViewedSlider.scrollWidth -
            mostViewedSlider.clientWidth;

        if (mostViewedSlider.scrollLeft >= maxScroll - 5) {

            mostViewedSlider.scrollTo({
                left: 0,
                behavior: 'smooth'
            });

        }

    }, 5000);
}

startMostViewedAutoReset();
</script>