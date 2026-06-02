<?php
$categories = $conn->query("
    SELECT * FROM categories WHERE show_in_home = 1 ORDER BY name ASC
");
?>

<?php while($category = $categories->fetch_assoc()): ?>
    <?php
    $catId = (int)$category['id'];

    $categorycontents = $conn->query("
        SELECT c.id, c.title, c.poster1, c.poster2, c.poster3, c.release_date, c.last_date, c.type, c.tmdb_rating
        FROM contents c
        JOIN content_categories cc ON c.id = cc.content_id
        WHERE cc.category_id = {$catId}
        ORDER BY COALESCE(c.last_date, c.release_date) DESC
        LIMIT 50
    ");
    ?>

    <div id="categorybody" class="max-w-[1400px] mx-auto px-4 mb-10 md:mb-20">

        <!-- HEADER -->
        <div class="flex justify-between items-center">
            <h2 class="text-xl md:text-2xl font-semibold text-white">
                <?= htmlspecialchars($category['name']); ?>
            </h2>

            <a href="../pages/browse.php?category=<?= $category['id'] ?>"
               class="text-sm text-blue-400 hover:text-blue-300 transition">
                View All →
            </a>
        </div>

        <!-- SCROLLER -->
        <div class="relative overflow-hidden">

            <!-- LEFT BUTTON -->
            <button onclick="scrollCategory(-1, <?= $catId ?>)"
                class="absolute left-1 top-1/2 -translate-y-1/2 z-20
                bg-black/40 hover:bg-black/70 backdrop-blur-md
                md:p-3 p-1 rounded-full transition hover:scale-105">
                ‹
            </button>

            <!-- RIGHT BUTTON -->
            <button onclick="scrollCategory(1, <?= $catId ?>)"
                class="absolute right-1 top-1/2 -translate-y-1/2 z-20
                bg-black/40 hover:bg-black/70 backdrop-blur-md
                md:p-3 p-1 rounded-full transition hover:scale-105">
                ›
            </button>

            <!-- SLIDER -->
            <div id="categorySlider<?= $catId ?>"
                class="flex gap-2 md:gap-4 overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory py-4">

                <?php while ($item = $categorycontents->fetch_assoc()): ?>
                    <?php 
                        $content = $item;
                        include '../includes/poster-card.php'; 
                    ?>
                <?php endwhile; ?>

            </div>

        </div>

    </div>
<?php endwhile; ?>

<script>
// 👉 MANUAL SCROLL (PER CATEGORY)
function scrollCategory(direction, id) {
    const slider = document.getElementById('categorySlider' + id);

    if (!slider) return;

    const amount = slider.clientWidth * 0.8;

    slider.scrollBy({
        left: direction * amount,
        behavior: 'smooth'
    });
}


// 👉 AUTO RESET (PER CATEGORY)
function startCategoryAutoReset() {

    const sliders = document.querySelectorAll('[id^="categorySlider"]');

    sliders.forEach(slider => {

        setInterval(() => {

            const maxScroll = slider.scrollWidth - slider.clientWidth;

            if (slider.scrollLeft >= maxScroll - 5) {
                slider.scrollTo({
                    left: 0,
                    behavior: 'smooth'
                });
            }

        }, 5000);

    });

}

// START
startCategoryAutoReset();
</script>