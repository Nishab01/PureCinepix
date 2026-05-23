<?php
$pagename = "My Watchlist";
require_once '../config/config.php';
require_once '../config/constants.php';

if (!isset($_SESSION['user'])) {
    die("Login required");
}

include '../includes/header.php';
?>

<div class="mt-10 max-w-[1400px] mx-auto px-6">

    <h1 class="text-2xl md:text-3xl font-bold mb-4">
        My Watchlist
    </h1>

    <div id="watchlist-grid">
        <div id="gridInner"
            class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-6 lg:grid-cols-6 gap-2 md:gap-4">
        </div>
    </div>

    <div id="pagination"
        class="flex justify-center gap-2 mt-8 flex-wrap">
    </div>

    <div id="empty-state"
        class="hidden flex flex-col items-center justify-center py-20 text-center">
        <!-- keep your same empty UI -->
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {

        const grid = document.getElementById("gridInner");
        const pagination = document.getElementById("pagination");
        const emptyState = document.getElementById("empty-state");

        let currentPage = 1;
        let totalPages = 1;

        async function load(page = 1) {

            const res = await fetch(`../api/watchlist_ajax.php?page=${page}`);
            const json = await res.json();

            grid.innerHTML = json.html;
            totalPages = json.totalPages;
            currentPage = page;

            // EMPTY STATE
            if (!json.html.trim()) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }

            renderPagination();

            // 🔥 YOUR ORIGINAL ANIMATION BACK
            if (typeof animateCards !== 'undefined') {
                animateCards(grid, 30);
            }
        }

        function renderPagination() {

            let html = '';

            html += `<button onclick="goPage(${currentPage - 1})"
                class="px-3 py-1 bg-white/10 rounded ${currentPage === 1 ? 'opacity-30' : ''}">
                Prev
            </button>`;

            for (let i = 1; i <= totalPages; i++) {

                html += `<button onclick="goPage(${i})"
                    class="px-3 py-1 rounded ${i === currentPage ? 'bg-blue-500' : 'bg-white/10'}">
                    ${i}
                </button>`;
            }

            html += `<button onclick="goPage(${currentPage + 1})"
                class="px-3 py-1 bg-white/10 rounded ${currentPage === totalPages ? 'opacity-30' : ''}">
                Next
            </button>`;

            pagination.innerHTML = html;
        }

        window.goPage = function(page) {
            if (page < 1 || page > totalPages) return;
            load(page);
        }

        load();

    });
</script>