<?php
$trending = $conn->query("
    SELECT id, title, backdrop1, backdrop2, backdrop3, release_date, last_date, type, tmdb_rating
    FROM contents
    WHERE id IN (
        SELECT content_id FROM content_categories
        WHERE category_id = (SELECT id FROM categories WHERE name='Trending')
    )
    ORDER BY COALESCE(last_date, release_date) DESC
    LIMIT 50
");
?>

<div id="trendingbody" class="max-w-[1400px] mx-auto px-4 mb-10 md:mb-20">

    <!-- HEADER -->
    <div class="flex justify-between items-center">
        <h2 class="text-xl md:text-2xl font-semibold text-white">
            Trending
        </h2>
    </div>

    <!-- SCROLLER -->
    <div id="trendingWrapper" class="relative overflow-hidden">

        <!-- BUTTONS -->
        <button onclick="scrollTrending(-1)"
            class="absolute left-1 top-1/2 -translate-y-1/2 z-20
            bg-black/40 hover:bg-black/70 backdrop-blur-md
            md:p-3 p-1 rounded-full transition hover:scale-105">
            ‹
        </button>

        <button onclick="scrollTrending(1)"
            class="absolute right-1 top-1/2 -translate-y-1/2 z-20
            bg-black/40 hover:bg-black/70 backdrop-blur-md
            md:p-3 p-1 rounded-full transition hover:scale-105">
            ›
        </button>

        <!-- SLIDER -->
        <div id="trendingSlider"
            class="flex gap-2 md:gap-4 overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory py-4">

            <?php while ($item = $trending->fetch_assoc()): ?>
                <?php include '../includes/backdrop-card.php'; ?>
            <?php endwhile; ?>

        </div>

    </div>

</div>

<script>
const trendingbody = document.getElementById('trendingbody');
const trendingSlider = document.getElementById('trendingSlider');

let autoScroll;

// ✅ MANUAL SCROLL (CARD BASED)
function scrollTrending(direction) {
    const card = trendingSlider.querySelector('a');
    const cardWidth = card ? card.offsetWidth + 16 : 300;

    trendingSlider.scrollBy({
        left: direction * cardWidth * 2,
        behavior: 'smooth'
    });
}

// ✅ AUTO SCROLL
function startAutoScroll() {
    autoScroll = setInterval(() => {

        const maxScroll = trendingSlider.scrollWidth - trendingSlider.clientWidth;

        if (trendingSlider.scrollLeft >= maxScroll - 5) {
            trendingSlider.scrollTo({
                left: 0,
                behavior: 'smooth'
            });
        } else {
            scrollTrending(1);
        }

    }, 5000);
}

function stopAutoScroll() {
    clearInterval(autoScroll);
}

// INIT
startAutoScroll();

// PAUSE ON HOVER
trendingbody.addEventListener('mouseenter', stopAutoScroll);
trendingbody.addEventListener('mouseleave', startAutoScroll);

</script>