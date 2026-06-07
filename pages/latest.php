<?php
$latest = $conn->query("
    SELECT id, title, poster1, poster2, poster3, release_date, last_date, type, tmdb_rating
    FROM contents
    ORDER BY COALESCE(last_date, release_date) DESC
    LIMIT 50
");
?>

<div id="latestbody" class="max-w-[1400px] mx-auto px-4 mb-10 md:mb-20">

    <!-- HEADER -->
    <div class="flex justify-between items-center">
        <h2 class="text-xl md:text-2xl font-semibold text-white">
            Latest Releases
        </h2>

        <a href="../pages/browse.php?sort=latest"
           class="text-sm text-blue-400 hover:text-blue-300 transition">
            View All →
        </a>
    </div>

    <!-- SCROLLER -->
    <div id="latestWrapper" class="relative overflow-hidden">

        <!-- LEFT BUTTON -->
        <button onclick="scrollLatest(-1)"
            class="absolute left-1 top-1/2 -translate-y-1/2 z-20
            bg-black/40 hover:bg-black/70 backdrop-blur-md
            md:p-3 p-1 rounded-full transition hover:scale-105">
            ‹
        </button>

        <!-- RIGHT BUTTON -->
        <button onclick="scrollLatest(1)"
            class="absolute right-1 top-1/2 -translate-y-1/2 z-20
            bg-black/40 hover:bg-black/70 backdrop-blur-md
            md:p-3 p-1 rounded-full transition hover:scale-105">
            ›
        </button>

        <!-- SLIDER -->
        <div id="latestSlider"
            class="flex gap-2 md:gap-4 overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory py-4">

            <?php while ($item = $latest->fetch_assoc()): ?>
                <?php 
                    $content = $item;
                    include '../includes/poster-card.php'; 
                ?>
            <?php endwhile; ?>

        </div>

    </div>

</div>

<script>
const latestSlider = document.getElementById('latestSlider');
let latestAutoScroll;

// 👉 MANUAL SCROLL
function scrollLatest(direction) {
    const amount = latestSlider.clientWidth * 0.8;

    latestSlider.scrollBy({
        left: direction * amount,
        behavior: 'smooth'
    });
}

// 👉 AUTO RESET ONLY (NOT CONTINUOUS AUTO SCROLL)
function startLatestAutoReset() {
    latestAutoScroll = setInterval(() => {

        const maxScroll = latestSlider.scrollWidth - latestSlider.clientWidth;

        if (latestSlider.scrollLeft >= maxScroll - 5) {
            latestSlider.scrollTo({
                left: 0,
                behavior: 'smooth'
            });
        }

    }, 5000); // wait 5s before reset
}

startLatestAutoReset();
</script>