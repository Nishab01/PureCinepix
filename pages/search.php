<?php
$pagename = "Search Results";
require_once '../config/config.php';
require_once '../config/constants.php';

$q = trim($_GET['q'] ?? '');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION[SESSION_USER] ?? null;

include '../includes/header.php';
?>

<div class="mt-10 max-w-[1400px] mx-auto px-3 md:px-6">

    <!-- SEARCH HEADER -->
    <div class="mb-6">
        <form action="search.php" method="GET" class="w-full flex flex-row items-center justify-center ">
            <input
                type="text"
                name="q"
                value="<?= htmlspecialchars($q) ?>"
                placeholder="Search Movies, TV Shows..."
                class="w-full md:w-1/2 px-4 h-12 rounded-l-xl bg-white/10 border-2 border-white/10
                text-white placeholder-white/40
                focus:outline-none focus:border-blue-500
                flex items-center justify-center"
            >

            <button
                type="submit"
                class="
                px-4 h-12 rounded-r-lg border-2 border-blue-500 hover:border-blue-600
                bg-blue-500 hover:bg-blue-600 font-bold
                text-white text-sm
                flex items-center justify-center">
                Search
            </button>
        </form>
    </div>

    <!-- PAGE TITLE -->
    <div class="mb-6">
        <?php if ($q): ?>
            <h1 class="text-xl md:text-2xl font-bold">
                Search Results
            </h1>

            <p class="text-white/60 mt-1">
                Showing results for
                <span class="text-blue-400">
                    "<?= htmlspecialchars($q) ?>"
                </span>
            </p>
        <?php else: ?>
            <h1 class="text-2xl md:text-3xl font-bold">
                Search
            </h1>

            <p class="text-white/60 mt-1">
                Search movies and TV shows.
            </p>
        <?php endif; ?>
    </div>

    <!-- RESULTS COUNT -->
    <div id="resultCount"
        class="mb-4 text-sm text-white/60">
    </div>

    <div id="requestBox" class="hidden mt-6"></div>

    <!-- SEARCH RESULTS -->
    <div id="searchGrid"
        class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-6 lg:grid-cols-6 gap-2 md:gap-4">
    </div>

    <!-- PAGINATION -->
    <div id="pagination"
        class="flex justify-center gap-2 mt-8 flex-wrap">
    </div>

    <!-- MORE LIKE THIS -->
    <div id="relatedSection" class="hidden mt-20">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg md:text-xl font-bold">
                    More Like This
                </h2>
                <p class="text-sm text-white/50">
                    Based on your search results
                </p>
            </div>
        </div>

        <div id="relatedWrapper" class="relative overflow-hidden">
            
            <!-- LEFT BUTTON -->
            <button onclick="scrollRelated(-1)"
                class="absolute left-1 top-1/2 -translate-y-1/2 z-20
                bg-black/40 hover:bg-black/70 backdrop-blur-md
                md:p-3 p-1 rounded-full transition hover:scale-105">
                ‹
            </button>

            <!-- RIGHT BUTTON -->
            <button onclick="scrollRelated(1)"
                class="absolute right-1 top-1/2 -translate-y-1/2 z-20
                bg-black/40 hover:bg-black/70 backdrop-blur-md
                md:p-3 p-1 rounded-full transition hover:scale-105">
                ›
            </button>

            <!-- SLIDER -->
            <div id="relatedContainer"
                class="flex gap-2 md:gap-4 overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory py-4">
            </div>

        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>

<script>
    const searchGrid = document.getElementById('searchGrid');
    const pagination = document.getElementById('pagination');
    const resultCount = document.getElementById('resultCount');
    const relatedSection = document.getElementById('relatedSection');
    const relatedContainer = document.getElementById('relatedContainer');

    const isLoggedIn = <?= $user ? 'true' : 'false' ?>;
    let currentRequestId = 0;

    const params = new URLSearchParams(window.location.search);

    const query = params.get('q') || '';

    let page = parseInt(params.get('page')) || 1;
    let totalPages = 1;

    async function loadSearch(pageNumber = 1) {

        if (!query.trim()) {
            searchGrid.innerHTML = `
                <div class="col-span-full text-center text-white/50 py-20">
                    Start typing something to search.
                </div>
            `;
            return;
        }

        searchGrid.innerHTML = `
            <div class="col-span-full text-white/60">
                Loading...
            </div>
        `;

        try {

            const res = await fetch(
                `../api/search_page.php?q=${encodeURIComponent(query)}&page=${pageNumber}`
            );

            const data = await res.json();

            totalPages = data.totalPages;

            searchGrid.innerHTML = data.html;

            resultCount.innerHTML =
                `${data.totalResults} result(s) found`;


            if (data.totalResults === 0) {

                const saveRes = await fetch(
                    '../api/store_search_request.php',
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type':
                            'application/x-www-form-urlencoded'
                        },
                        body:
                            'search_text=' +
                            encodeURIComponent(query)
                    }
                );

                const saveData = await saveRes.json();
                currentRequestId = saveData.request_id;

                const requestBox =
                    document.getElementById('requestBox');

                requestBox.classList.remove('hidden');

                if (isLoggedIn) {

                    requestBox.innerHTML = `
                        <div class="">
                            <p class="pt-4 text-sm text-white/70 mb-2 border-t border-white/10">
                                Can't find this content?
                            </p>

                            <button
                                id="requestContentBtn"
                                class="text-sm px-4 py-2 rounded-lg bg-blue-500 hover:bg-blue-600">
                                Click to Request
                            </button>
                        </div>
                    `;

                    document.getElementById('requestContentBtn').addEventListener('click', async () => {

                        const res = await fetch(
                            '../api/request_content.php',
                            {
                                method: 'POST',
                                headers: {
                                    'Content-Type':
                                    'application/x-www-form-urlencoded'
                                },
                                body:
                                    'request_id=' +
                                    currentRequestId
                            }
                        );

                        const data = await res.json();

                        if (data.already_requested) {

                            alert(
                                'You already requested this content.'
                            );

                            return;
                        }

                        if (data.success) {

                            document
                            .getElementById('requestContentBtn')
                            .innerHTML = 'Requested ✓';

                            document
                            .getElementById('requestContentBtn')
                            .disabled = true;
                        }
                    });

                } else {

                    requestBox.innerHTML = `
                        <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                            <p class="text-white/70">
                                Login to request this content.
                            </p>
                        </div>
                    `;
                }

            } else {

                document
                    .getElementById('requestBox')
                    .classList
                    .add('hidden');
            }

            renderPagination(totalPages, pageNumber);

            if (data.related_html && data.related_html.trim() !== '') {

                relatedSection.classList.remove('hidden');

                relatedContainer.innerHTML =
                    data.related_html;

            } else {

                relatedSection.classList.add('hidden');

                relatedContainer.innerHTML = '';
            }

            if (typeof animateCards === "function") {
                animateCards(searchGrid, 30);
                animateCards(relatedContainer, 30);
            }

            if (pageNumber > 1) {
                window.scrollTo({
                    top: 0,
                    behavior: "smooth"
                });
            }

        } catch (err) {

            console.error(err);

            searchGrid.innerHTML = `
                <div class="col-span-full text-red-400">
                    Failed to load search results.
                </div>
            `;
        }
    }

    function renderPagination(totalPages, currentPage) {

        let html = '';

        html += `
            <button
                onclick="goPage(${currentPage - 1})"
                class="px-3 py-1 rounded bg-white/10
                ${currentPage === 1
                    ? 'opacity-30 pointer-events-none'
                    : ''}">
                Prev
            </button>
        `;

        for (let i = 1; i <= totalPages; i++) {

            html += `
                <button
                    onclick="goPage(${i})"
                    class="px-3 py-1 rounded
                    ${i === currentPage
                        ? 'bg-blue-500'
                        : 'bg-white/10'}">
                    ${i}
                </button>
            `;
        }

        html += `
            <button
                onclick="goPage(${currentPage + 1})"
                class="px-3 py-1 rounded bg-white/10
                ${currentPage === totalPages
                    ? 'opacity-30 pointer-events-none'
                    : ''}">
                Next
            </button>
        `;

        pagination.innerHTML = html;
    }

    window.goPage = function(nextPage) {

        if (nextPage < 1 || nextPage > totalPages) {
            return;
        }

        page = nextPage;

        const url = new URL(window.location);

        url.searchParams.set('q', query);
        url.searchParams.set('page', page);

        window.history.replaceState({}, '', url);

        loadSearch(page);
    };

    /* =========================
    RELATED SLIDER
    ========================= */
    window.scrollRelated = function(direction) {

        const card = relatedContainer.querySelector('a');

        const cardWidth =
            card
            ? card.offsetWidth + 16
            : 300;

        relatedContainer.scrollBy({
            left: direction * cardWidth * 2,
            behavior: 'smooth'
        });
    };

    loadSearch(page);
</script>