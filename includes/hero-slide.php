<?php
$settings = [];
$res = $conn->query("SELECT `key`, `value` FROM settings");
while ($row = $res->fetch_assoc()) {
    $value = is_numeric($row['value']) ? (int)$row['value'] : $row['value'];
    $settings[$row['key']] = $value;
}

$sliderSpeed = (int)($settings['hero_slide_speed'] ?? 5000);
$trailerDelay = (int)($settings['hero_trailer_delay'] ?? 5000);

$featured = $conn->query("
    SELECT id, title, description, backdrop1, backdrop2, backdrop3, tmdb_rating, tmdb_trailer
    FROM contents
    WHERE id IN (
        SELECT content_id FROM content_categories
        WHERE category_id = (SELECT id FROM categories WHERE name='Featured')
    )
    ORDER BY COALESCE(last_date, release_date) DESC
");
?>

<div class="hero-container relative h-[40vh] md:h-[92vh] w-full overflow-hidden mb-10 md:mb-20">

    <div id="heroSlides" class="h-full w-full relative">

        <!-- LEFT -->
        <button onclick="prevSlide()"
            class="absolute left-2 top-1/2 -translate-y-1/2 z-20
            bg-black/30 hover:bg-black/60 backdrop-blur-xl
            md:p-3 p-1 rounded-full transition duration-300
            hover:scale-110 active:scale-95">
            <svg class="md:w-6 w-4 md:h-6 h-4 text-white"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <!-- RIGHT -->
        <button onclick="nextSlide()"
            class="absolute right-2 top-1/2 -translate-y-1/2 z-20
            bg-black/30 hover:bg-black/60 backdrop-blur-xl
            md:p-3 p-1 rounded-full transition duration-300
            hover:scale-110 active:scale-95">
            <svg class="md:w-6 w-4 md:h-6 h-4 text-white"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <?php $i = 0; while ($item = $featured->fetch_assoc()): ?>

            <?php
                $isInWatchlist = false;

                if (isset($_SESSION[SESSION_USER])) {
                    $user_id = $_SESSION[SESSION_USER]['id'];

                    $stmt = $conn->prepare("
                        SELECT 1 FROM watchlist 
                        WHERE user_id = ? AND content_id = ?
                    ");
                    $stmt->bind_param("ii", $user_id, $item['id']);
                    $stmt->execute();
                    $res = $stmt->get_result();

                    $isInWatchlist = $res->num_rows > 0;
                }
                
            ?>

            <div class="hero-slide absolute inset-0 transition-opacity duration-700 <?= $i === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' ?>"
                data-trailer="<?= htmlspecialchars($item['tmdb_trailer']) ?>">

                <!-- BACKDROP -->
                <div class="hero-bg absolute inset-0 z-0"
                    style="background-image:url('<?= $item['backdrop2'] ?>'); background-size:cover; background-position:center;"
                    data-backdrops='<?= json_encode(array_values(array_filter([
                        $item['backdrop2'],
                        $item['backdrop3']
                    ]))) ?>'>
                </div>

                <!-- TRAILER -->
                <div class="hero-trailer absolute inset-0 z-0 opacity-0 transition-opacity duration-700 pointer-events-none"></div> 

                <!-- OVERLAY -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent z-10"></div>

                <!-- CONTENT -->
                <div class="absolute bottom-4 md:bottom-32 w-full z-20">
                    <div class="max-w-[1400px] mx-auto px-4">

                        <div class="max-w-xl space-y-2 md:space-y-4">

                            <h1 class="text-xl md:text-5xl font-bold text-white drop-shadow-[0_2px_10px_rgba(0,0,0,0.8)]">
                                <?= htmlspecialchars($item['title']) ?>
                            </h1>

                            <p class="text-xs md:text-base text-white/80 line-clamp-3 drop-shadow">
                                <?= htmlspecialchars($item['description']) ?>
                            </p>

                            <div class="flex gap-3 mt-4">

                                <a href="../pages/content.php?slug=<?= $item['id'] ?>&title=<?= urlencode($item['title']) ?>"
                                   class="px-4 py-2 md:px-6 md:py-3 bg-blue-500 hover:bg-blue-600 rounded-lg md:text-sm text-xs font-semibold transition"
                                   title="<?= $title ?>"
                                   >
                                    View Details
                                </a>

                                <button class="watchlistBtn
                                    px-4 py-2 md:px-6 rounded-lg md:text-sm text-xs font-semibold border border-white/10 backdrop-blur-md transition-all duration-150 bg-white/10 text-white hover:bg-white/20"
                                    
                                    data-id="<?= $item['id'] ?>"
                                    data-state="<?= $isInWatchlist ? 'added' : 'not-added' ?>">

                                    <span class="watchlistText">
                                        <?= $isInWatchlist ? '✓ In Watchlist' : '+ Add to Watchlist' ?>
                                    </span>
                                </button>

                            </div>

                        </div>

                    </div>
                </div>

            </div>
        <?php $i++; endwhile; ?>

    </div>

</div>

<script>
    const SLIDE_INTERVAL = <?= $sliderSpeed ?>;
    const TRAILER_DELAY = <?= $trailerDelay ?>;

    let current = 0;
    let autoSlide;

    const slides = document.querySelectorAll('.hero-slide');
    const heroSlides = document.getElementById('heroSlides');

    const isMobile = window.innerWidth < 768;

    // =========================
    // 🎬 SLIDER SYSTEM
    // =========================
    if (slides.length > 0) {

        function showSlide(index) {
            slides.forEach((s, i) => {
                s.classList.toggle('opacity-0', i !== index);
                s.classList.toggle('opacity-100', i === index);
                s.classList.toggle('z-10', i === index);
                s.classList.toggle('z-0', i !== index);
            });
        }

        function nextSlide() {
            current = (current + 1) % slides.length;
            showSlide(current);
        }

        function prevSlide() {
            current = (current - 1 + slides.length) % slides.length;
            showSlide(current);
        }

        function startAutoSlide() {
            stopAutoSlide();
            autoSlide = setInterval(nextSlide, SLIDE_INTERVAL);
        }

        function stopAutoSlide() {
            clearInterval(autoSlide);
        }

        startAutoSlide();
        showSlide(current);

        heroSlides.addEventListener('mouseenter', stopAutoSlide);
        heroSlides.addEventListener('mouseleave', startAutoSlide);
    }

    // =========================
    // 🎬 TRAILER SYSTEM
    // =========================

    // extract YouTube key safely
    function extractYouTubeKey(url) {
        if (!url) return null;

        const match = url.match(/(youtu\.be\/|v=)([^&]+)/);
        return match ? match[2] : url; // fallback if already key
    }

    slides.forEach((slide) => {

        const bg = slide.querySelector('.hero-bg');
        const trailerBox = slide.querySelector('.hero-trailer');

        let hoverTimer = null;
        let iframeLoaded = false;

        slide.addEventListener('mouseenter', () => {

            if (isMobile) return;

            stopAutoSlide();

            hoverTimer = setTimeout(() => {

                const raw = slide.dataset.trailer;
                if (!raw || iframeLoaded) return;

                const key = extractYouTubeKey(raw);
                if (!key) return;

                const iframe = document.createElement('iframe');

                iframe.src = `https://www.youtube.com/embed/${key}?autoplay=1&mute=1&controls=0&modestbranding=1&rel=0&loop=1&playlist=${key}&playsinline=1&iv_load_policy=3&disablekb=1&fs=0`;

                iframe.style.position = "absolute";
                iframe.style.top = "50%";
                iframe.style.left = "50%";
                iframe.style.transform = "translate(-50%, -50%)";
                iframe.style.width = "120vw";
                iframe.style.height = "120vh";
                iframe.style.pointerEvents = "none";
                iframe.style.border = "none";

                iframe.allow = "autoplay; encrypted-media";

                trailerBox.innerHTML = '';
                trailerBox.appendChild(iframe);

                // smooth transition
                bg.style.transition = "opacity 0.5s ease";
                trailerBox.style.transition = "opacity 0.5s ease";

                bg.style.opacity = "0";
                trailerBox.style.opacity = "1";

                iframeLoaded = true;

            }, TRAILER_DELAY); //Trailer after this time (ms)
        });

        slide.addEventListener('mouseleave', () => {

            if (isMobile) return;

            clearTimeout(hoverTimer);

            trailerBox.style.opacity = "0";
            bg.style.opacity = "1";

            setTimeout(() => {
                trailerBox.innerHTML = '';
                iframeLoaded = false;
            }, 400);

            startAutoSlide();
        });

    });

    document.addEventListener('click', async function(e) {

        let btn = e.target;

        // walk up manually (more reliable)
        while (btn && !btn.classList.contains('watchlistBtn')) {
            btn = btn.parentElement;
        }

        if (!btn) return;

        const contentId = btn.dataset.id;
        let state = btn.dataset.state;

        if (!contentId) {
            console.log("No content ID");
            return;
        }

        const action = state === 'added' ? 'remove' : 'add';

        btn.disabled = true;
        btn.classList.add('opacity-60');

        try {
            const res = await fetch('../api/watchlist_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `content_id=${contentId}&action=${action}`
            });

            const data = await res.json();
            console.log("API:", data);

            const textEl = btn.querySelector('.watchlistText');

            if (data.status === 'added') {

                btn.dataset.state = 'added';
                textEl.innerText = '✓ In Watchlist';
            }

            else if (data.status === 'removed') {

                btn.dataset.state = 'not-added';
                textEl.innerText = '+ Add to Watchlist';
            }

        } catch (err) {
            console.error("Fetch error:", err);
        }

        btn.disabled = false;
        btn.classList.remove('opacity-60');
    });
</script>