<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/constants.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION[SESSION_USER] ?? null;
$is_admin = false;
$is_superadmin = false;

if ($user) {
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $dbUser = $res->fetch_assoc();
        if ($dbUser['role'] === 'admin') {
            $is_admin = true;
            $is_superadmin = false;

            if ($dbUser['role'] === 'admin') {
                $_SESSION['is_admin'] = 1;
            } else {
                $_SESSION['is_admin'] = 0;
            }
        }
        if($dbUser['role'] === 'superadmin'){
            $is_admin = true;
            $is_superadmin = true;

            if ($dbUser['role'] === 'superadmin') {
                $_SESSION['is_superadmin'] = 1;
            } else {
                $_SESSION['is_superadmin'] = 0;
            }
        }
    }
}

/* FETCH DATA */
$categories = $conn->query("SELECT * FROM categories WHERE show_in_navbar = 1 ORDER BY name ASC");
$genres = $conn->query("SELECT * FROM genres ORDER BY name ASC");
?>

<!-- NAVBAR -->
<header class="fixed top-0 left-0 w-full z-50 bg-black/60 backdrop-blur-xl shadow shadow-blue-400/10">

    <div class="max-w-[1400px] mx-auto px-4 h-20 flex items-center justify-between">

        <!-- LEFT -->
        <div class="flex items-center gap-4">

            <!-- MOBILE BTN -->
            <button id="menuBtn" class="md:hidden text-white text-lg">☰</button>

            <!-- LOGO -->
            <a href="../pages/" class="flex items-center gap-1">
                <img src="../assets/logo.svg" class="h-8">
                <span class="text-lg md:text-xl font-semibold tracking-wide">
                    Pure<span class="text-blue-500">Cinepix</span>
                </span>
            </a>

            <!-- DESKTOP MENU -->
            <nav class="ml-6 hidden md:flex items-center gap-0 text-sm text-white/90">

                <!-- HOME -->
                <a href="../pages/" class="px-4 py-2 relative hover:text-blue-400 transition rounded-xl hover:bg-blue-500/20 hover:text-shadow text-sm">
                    Home
                </a>

                <!-- GENRES -->
                <div class="relative" data-dropdown>

                    <button class="px-4 py-2 flex items-center gap-1 hover:text-blue-400 rounded-xl hover:bg-blue-500/20 hover:text-shadow text-sm">
                        Genres
                        <svg class="w-4 h-4 transition-transform duration-200" data-arrow viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-width="2" fill="none" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div class="absolute left-1/2 -translate-x-1/2 mt-6 w-[700px]
                        bg-black border border-white/10 shadow-xl border border-white/10 rounded-xl shadow-xl p-4
                        opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 text-white/80"
                        data-menu>

                        <div class="grid grid-cols-3 gap-2 text-sm">
                            <?php while ($g = $genres->fetch_assoc()): ?>
                                <a href="../pages/browse.php?genres=<?= $g['id'] ?>"
                                   class="px-3 py-2 rounded-lg bg-white/5 hover:bg-blue-500/20
                                   hover:text-blue-400 text-center border border-transparent hover:border-blue-400/50 text-sm">
                                    <?= htmlspecialchars($g['name']) ?>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>

                <!-- CATEGORIES -->
                <div class="relative" data-dropdown>

                    <button class="px-4 py-2 flex items-center gap-1 hover:text-blue-400 rounded-xl hover:bg-blue-500/20 hover:text-shadow text-sm">
                        Categories
                        <svg class="w-4 h-4 transition-transform duration-200" data-arrow viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-width="2" fill="none" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div class="absolute mt-6 w-[700px]
                        bg-black border border-white/10 rounded-xl shadow-xl p-4
                        opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 text-white/80"
                        data-menu>

                        <div class="grid grid-cols-3 gap-2 text-sm">

                            <?php while ($c = $categories->fetch_assoc()): ?>
                                <a href="../pages/browse.php?categories=<?= $c['id'] ?>"
                                class="px-3 py-2 rounded-lg bg-white/5
                                border border-transparent text-center
                                hover:bg-blue-500/20 hover:text-blue-400 hover:border-blue-400/50 transition text-sm">
                                    <?= htmlspecialchars($c['name']) ?>
                                </a>
                            <?php endwhile; ?>

                        </div>

                    </div>
                </div>

                <a href="../pages/browse.php?type=movie" class="px-4 py-2 hover:text-blue-400 rounded-xl hover:bg-blue-500/20 hover:text-shadow text-sm">Movies</a>
                <a href="../pages/browse.php?type=tv" class="px-4 py-2 hover:text-blue-400 rounded-xl hover:bg-blue-500/20 hover:text-shadow text-sm">TV Shows</a>

                <?php if ($user): ?>
                    <a href="../pages/watchlist.php" class="px-4 py-2 hover:text-blue-400 rounded-xl hover:bg-blue-500/20 hover:text-shadow text-sm">
                        Watchlist
                    </a>
                <?php endif; ?>

            </nav>
        </div>

        <!-- RIGHT -->
        <div class="flex items-center gap-2">

            <!-- SEARCH -->
            <div class="hidden md:block relative w-fit min-w-[100px]">

                <!-- <input
                    id="searchInput"
                    type="text"
                    placeholder="Search Movies, TV shows..."
                    class="w-full px-3 py-2 bg-black/60 border border-white/20 rounded-lg text-sm focus:outline-none focus:border-blue-400"
                >

                <div id="searchResults"
                    class="absolute left-0 mt-2 w-[360px] bg-black border border-white/10 rounded-xl shadow-xl p-2 hidden z-50 max-h-[400px] overflow-y-auto custom-scroll">
                </div> -->


                <form action="../pages/search.php" method="GET"
                    class="hidden md:block relative w-fit min-w-[100px]">

                    <input
                        id="searchInput"
                        name="q"
                        type="text"
                        placeholder="Search Movies, TV Shows..."
                        class="w-full px-3 py-2 bg-black/60 border border-white/20 rounded-lg text-sm focus:outline-none focus:border-blue-400"
                    >

                    <div id="searchResults"
                        class="absolute left-0 mt-2 w-[360px] bg-black border border-white/10 rounded-xl shadow-xl p-2 hidden z-50 max-h-[400px] overflow-y-auto custom-scroll">
                    </div>
                </form>

            </div>

            <!-- USER -->
            <?php if ($user): ?>
                <div class="relative" data-dropdown>

                    <button class="flex items-center gap-2 px-3 py-2 border border-white/20 bg-white/10 rounded-lg text-[15px] hover:bg-blue-500/20 hover:text-blue-400 hover:border-blue-400/50 text-sm">
                        <span class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-icon lucide-user"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>

                        <span class="hidden md:block line-clamp-1 text-sm">
                            <?php 
                            // htmlspecialchars($user['name'] ?? 'User') ;
                            $fullname = htmlspecialchars($user['name'] ?? 'User');
                            $shortname = preg_split('/\s+/', trim($fullname));
                            echo $shortname[0];
                            ?>
                        </span>

                        <svg class="w-4 h-4 transition-transform duration-200" data-arrow viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-width="2" fill="none" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div class="absolute right-0 mt-4 w-52 space-y-1
                        bg-black border border-white/10 shadow-xl border border-white/10 rounded-xl shadow-xl p-2
                        opacity-0 scale-95 pointer-events-none transition-all duration-300"
                        data-menu>

                        <?php if ($is_admin): ?>
                            <a href="../admin/"
                               class="block px-3 py-2 text-sm text-blue-400 rounded-lg hover:bg-blue-500/20 border border-transparent hover:border-blue-400/50">
                                Admin Panel
                            </a>
                        <?php endif; ?>
                        
                        <a href="../pages/watchlist.php"
                           class="block px-3 py-2 text-sm text-white/80 rounded-lg hover:bg-blue-500/20 hover:text-blue-400 border border-transparent hover:border-blue-400/50">
                            My Watchlist
                        </a>

                        <a href="../auth/profile.php"
                           class="block px-3 py-2 text-sm text-white/80 rounded-lg hover:bg-blue-500/20 hover:text-blue-400 border border-transparent hover:border-blue-400/50">
                            Profile Settings
                        </a>

                        <a href="../auth/logout.php"
                           class="block px-3 py-2 text-sm text-red-400 rounded-lg hover:bg-red-500/20 border border-transparent hover:border-red-400/50">
                            Logout
                        </a>

                    </div>
                </div>

            <?php else: ?>
                <a href="../auth/login.php"
                   class="px-4 py-2 text-sm border border-blue-500/20 bg-blue-500/20 text-blue-400 rounded-lg">
                    Login
                </a>
            <?php endif; ?>

        </div>
    </div>

    <div class="h-[1px] w-full bg-gradient-to-r from-black via-blue-500/80 to-black"></div>
</header>

<!-- MOBILE SIDEBAR -->
<div id="sidebar"
    class="fixed top-0 left-0 min-w-1/2 w-fit h-full
    bg-black/80 backdrop-blur-xl border-r border-white/10
    z-50 -translate-x-full transition duration-300">

    <div class="mt-2 p-4 flex flex-col gap-2">

        <!-- LOGO -->
        <a href="../" class="flex items-center gap-2">
            <img src="../assets/logo.svg" class="h-8">
            <span class="text-lg md:text-xl font-semibold tracking-wide">
                Pure<span class="text-blue-500">Cinepix</span>
            </span>
        </a>

        <!-- MOBILE SEARCH -->
        <!-- <div class="my-4">

            <input
                id="mobileSearchInput"
                type="text"
                placeholder="Search movies, TV shows..."
                class="w-full px-4 py-2 rounded-lg 
                bg-white/5 border border-white/10 
                text-sm text-white placeholder-white/40
                focus:outline-none focus:border-blue-400
                focus:bg-white/10 transition"
            >

            <div id="mobileSearchResults"
                class="mt-2 bg-black border border-white/10 rounded-xl shadow-xl p-2 hidden">
            </div>

        </div> -->

        <form action="../pages/search.php" method="GET" class="my-4">

            <input
                id="mobileSearchInput"
                name="q"
                type="text"
                placeholder="Search Movies, TV Shows..."
                class="w-full px-4 py-2 rounded-lg 
                bg-white/10 border border-white/10 
                text-sm text-white placeholder-white/40
                focus:outline-none focus:border-blue-400
                focus:bg-white/10 transition"
            >

            <div id="mobileSearchResults"
                class="mt-2 bg-black border border-white/10 rounded-xl shadow-xl p-2 hidden">
            </div>

        </form>
        
        <!-- HOME -->
        <a href="../" class="block px-3 py-2 bg-white/5 rounded-lg hover:bg-blue-500/20 hover:text-blue-400">Home</a>

        <!-- GENRES -->
        <div>
            <button onclick="toggleMobile('genres')" 
                class="w-full text-left px-3 py-2 bg-white/5 rounded-lg hover:bg-blue-500/20 hover:text-blue-400">
                Genres
            </button>

            <div id="mobile-genres" 
                class="max-h-0 overflow-y-auto transition-all duration-300 custom-scroll pr-1">
                <?php $genres->data_seek(0); while ($g = $genres->fetch_assoc()): ?>
                    <a href="../pages/browse.php?genres=<?= $g['id'] ?>"
                    class="mt-1 block rounded-lg px-3 py-2 text-sm text-white/70 hover:bg-blue-500/20 hover:text-blue-400">
                        <?= $g['name'] ?>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- CATEGORIES -->
        <div>
            <button onclick="toggleMobile('cats')" 
                class="w-full text-left px-3 py-2 bg-white/5 rounded-lg hover:bg-blue-500/20 hover:text-blue-400">
                Categories
            </button>

            <div id="mobile-cats" class="max-h-0 overflow-y-auto transition-all duration-300 custom-scroll pr-1">
                <?php $categories->data_seek(0); while ($c = $categories->fetch_assoc()): ?>
                    <a href="../pages/browse.php?categories=<?= $c['id'] ?>"
                    class="mt-1 block rounded-lg px-3 py-2 text-sm text-white/70 hover:bg-blue-500/20 hover:text-blue-400">
                        <?= $c['name'] ?>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- MOVIES -->
        <a href="../pages/browse.php?type=movie" class="block px-3 py-2 bg-white/5 rounded-lg hover:bg-blue-500/20 hover:text-blue-400">
            Movies
        </a>

        <!-- TV SHOWS -->
        <a href="../pages/browse.php?type=tv" class="block px-3 py-2 bg-white/5 rounded-lg hover:bg-blue-500/20 hover:text-blue-400">
            TV Shows
        </a>

        <!-- Watchlist -->
        <a href="../pages/watchlist.php" class="block px-3 py-2 bg-white/5 rounded-lg hover:bg-blue-500/20 hover:text-blue-400">
            Watchlist
        </a>

        <!-- <a href="../pages/browse.php" class="mt-4 block px-3 py-2 bg-white/5 rounded-lg hover:bg-blue-500/20 hover:text-blue-400">
            Browse
        </a>
        <a href="../pages/search.php" class="block px-3 py-2 bg-white/5 rounded-lg hover:bg-blue-500/20 hover:text-blue-400">
            Search
        </a> -->
    </div>
</div>

<!-- OVERLAY -->
<div id="overlay" class="fixed inset-0 bg-black/50 hidden z-40"></div>

<!-- SCRIPT -->
<script>
    /* MOBILE */
    const menuBtn = document.getElementById('menuBtn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    menuBtn.onclick = () => {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    };

    overlay.onclick = () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    };

    function toggleMobile(id) {
        const current = document.getElementById('mobile-' + id);

        // CLOSE ALL FIRST
        document.querySelectorAll('[id^="mobile-"]').forEach(el => {
            if (el !== current) {
                el.classList.add('max-h-0');
                el.classList.remove('max-h-[40vh]');
            }
        });

        // TOGGLE CURRENT
        if (current.classList.contains('max-h-0')) {
            current.classList.remove('max-h-0');
            current.classList.add('max-h-[40vh]');
        } else {
            current.classList.add('max-h-0');
            current.classList.remove('max-h-[40vh]');
        }
    }

    /* UNIVERSAL DROPDOWN */
    document.querySelectorAll('[data-dropdown]').forEach(drop => {

        const btn = drop.querySelector('button');
        const menu = drop.querySelector('[data-menu]');
        const arrow = drop.querySelector('[data-arrow]');

        btn.addEventListener('click', (e) => {
            e.stopPropagation();

            const isOpen = !menu.classList.contains('opacity-0');

            // CLOSE ALL
            document.querySelectorAll('[data-menu]').forEach(m => {
                m.classList.add('opacity-0','scale-95','pointer-events-none');
            });

            document.querySelectorAll('[data-arrow]').forEach(a => {
                a.classList.remove('rotate-180');
            });

            // OPEN ONLY IF IT WAS CLOSED
            if (!isOpen) {
                menu.classList.remove('opacity-0','scale-95','pointer-events-none');
                arrow.classList.add('rotate-180');
            }
        });
    });

    /* OUTSIDE CLICK */
    document.addEventListener('click', () => {
        document.querySelectorAll('[data-menu]').forEach(m => {
            m.classList.add('opacity-0','scale-95','pointer-events-none');
        });

        document.querySelectorAll('[data-arrow]').forEach(a => {
            a.classList.remove('rotate-180');
        });
    });


    // SEARCH SYSTEM
    const input = document.getElementById('searchInput');
    const resultsBox = document.getElementById('searchResults');

    let debounce;
    let lastQuery = "";

    // FORMAT DATE
    function formatDate(dateStr) {
        if (!dateStr) return '';

        const date = new Date(dateStr);
        return date.toLocaleDateString('en-GB', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });
    }

    function formatDateRange(release, last, type) {

        if (!release) return '';

        const start = formatDate(release);

        // 🎬 MOVIE → only one date
        if (type === 'movie') {
            return start;
        }

        // 📺 TV → range
        if (type === 'tv') {
            if (last && last !== release) {
                return `${start} - ${formatDate(last)}`;
            }
            return start;
        }

        return start;
    }

    input?.addEventListener('input', () => {

        const query = input.value.trim();

        clearTimeout(debounce);

        if (query === lastQuery) return;

        if (query.length < 2) {
            resultsBox.classList.add('hidden');
            resultsBox.innerHTML = '';
            return;
        }

        debounce = setTimeout(async () => {

            lastQuery = query;

            // loading state
            resultsBox.innerHTML = `
                <div class="px-3 py-2 text-sm text-white/60">
                    Searching...
                </div>
            `;
            resultsBox.classList.remove('hidden');

            try {
                const res = await fetch(`../api/search_api.php?q=${encodeURIComponent(query)}`);
                const data = await res.json();

                renderResults(data);

            } catch (err) {
                console.error(err);
            }

        }, 300);
    });

    function renderResults(data) {

        if (!data.length) {
            resultsBox.innerHTML = `
                <div class="px-3 py-2 text-sm text-white/60">
                    No results found
                </div>
            `;
            return;
        }

        let html = '';

        data.forEach(item => {

            const poster = item.poster1 || '../assets/no-image.png';
            
            html += `
            <a href="../pages/content.php?slug=${item.id}&title=${item.title}"
            class="card-item flex items-start gap-3 p-2 rounded-lg hover:bg-blue-500/20 transition">

                <img src="${poster}"
                    class="w-auto h-24 object-cover rounded-lg">

                <div class="flex flex-col gap-1">
                    <div class="text-base text-white">${item.title}</div>

                    <div class="w-fit text-xs capitalize rounded-xl px-2 border border-blue-400/60 bg-blue-400/20 text-blue-400/60 font-semibold">
                        ${item.type === 'tv' ? 'TV Show' : item.type === 'movie' ? 'Movie' : item.type}
                    </div>

                    <div class="text-xs text-white/80">
                        ${formatDateRange(item.release_date, item.last_date, item.type)}
                    </div>
                </div>

            </a>
            `;
        });

        resultsBox.innerHTML = html;

        // your global animation
        if (typeof animateCards === "function") {
            animateCards(resultsBox, 30);
        }
    }

    // CLOSE ON OUTSIDE CLICK
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#searchInput') && !e.target.closest('#searchResults')) {
            resultsBox.classList.add('hidden');
        }
    });


    // MOBILE SEARCH INIT
    const mobileInput = document.getElementById('mobileSearchInput');
    const mobileResults = document.getElementById('mobileSearchResults');

    if (mobileInput && mobileResults) {

        let debounce;
        let lastQuery = "";

        mobileInput.addEventListener('input', () => {

            const query = mobileInput.value.trim();

            clearTimeout(debounce);

            // prevent same query repeat
            if (query === lastQuery) return;

            if (query.length < 2) {
                mobileResults.classList.add('hidden');
                mobileResults.innerHTML = '';
                return;
            }

            debounce = setTimeout(async () => {

                lastQuery = query;

                // loading state
                mobileResults.innerHTML = `
                    <div class="px-3 py-2 text-sm text-white/60">
                        Searching...
                    </div>
                `;
                mobileResults.classList.remove('hidden');

                try {
                    const res = await fetch(`../api/search_api.php?q=${encodeURIComponent(query)}`);
                    const data = await res.json();

                    renderMobileResults(data);

                } catch (err) {
                    console.error(err);
                }

            }, 300);
        });
    }


    // RENDER RESULTS
    function renderMobileResults(data) {

        if (!data.length) {
            mobileResults.innerHTML = `
                <div class="px-3 py-2 text-sm text-white/60">
                    No results found
                </div>
            `;
            return;
        }

        let html = '';

        data.forEach(item => {

            const poster = item.poster1 || '../assets/no-image.png';

            html += `
            <a href="../pages/content.php?id=${item.id}"
            class="card-item flex items-start gap-3 p-2 rounded-lg hover:bg-blue-500/20 transition">

                <img src="${poster}"
                    class="w-auto h-20 object-cover rounded-lg">

                <div class="flex flex-col gap-1">
                    <div class="text-sm text-white">${item.title}</div>

                    <div class="text-[10px] text-blue-400/70 capitalize">
                        ${item.type}
                    </div>

                    <div class="text-[11px] text-white/70">
                        ${formatDateRange(item.release_date, item.last_date, item.type)}
                    </div>
                </div>

            </a>
            `;
        });

        mobileResults.innerHTML = html;

        // your global animation
        animateCards(mobileResults, 30);
    }


    // DATE FORMAT (shared)
    function formatDateRange(release, last, type) {

        if (!release) return '';

        const format = (d) => {
            const date = new Date(d);
            return date.toLocaleDateString('en-GB', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        };

        if (type === 'tv' && last) {
            return `${format(release)} - ${format(last)}`;
        }

        return format(release);
    }


    // CLICK OUTSIDE CLOSE
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#mobileSearchInput') && !e.target.closest('#mobileSearchResults')) {
            mobileResults?.classList.add('hidden');
        }
    });
</script>