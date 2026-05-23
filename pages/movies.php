<?php
$pagename = "Movies";
require_once '../config/config.php';
require_once '../config/constants.php';

include '../includes/header.php';
?>

<div class="mt-20 max-w-[1400px] mx-auto px-6">

    <h1 class="text-2xl md:text-3xl font-bold mb-4">
        Movies
    </h1>

    <!-- GRID -->
    <div id="movies-grid">
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-6 lg:grid-cols-6 gap-2 md:gap-4">
            <!-- 🔥 Data will load here -->
        </div>
    </div>

    <!-- PAGINATION -->
    <div id="pagination"
        class="flex flex-wrap justify-center items-center gap-2 mt-6">
    </div>

</div>

<script>
const grid = document.querySelector('#movies-grid .grid');
const pagination = document.getElementById('pagination');

let currentPage = 1;

/* =========================
   LOAD DATA
========================= */
async function load(page = 1) {

    try {
        grid.innerHTML = `<div class="text-white/60 col-span-full text-center py-10">Loading...</div>`;

        const res = await fetch(`../api/list.php?type=movies&page=${page}`);
        const data = await res.json();

        if (data.status !== 'success') {
            grid.innerHTML = `<div class="text-red-400 col-span-full text-center py-10">Error loading</div>`;
            return;
        }

        grid.innerHTML = data.html;

        // 🔥 KEEP YOUR ANIMATION
        animateCards(grid, 40);

        renderPagination(data.totalPages, page);

        currentPage = page;

    } catch (err) {
        console.error(err);
        grid.innerHTML = `<div class="text-red-400 col-span-full text-center py-10">Failed</div>`;
    }
}

/* =========================
   PAGINATION UI
========================= */
function renderPagination(totalPages, current) {

    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let html = '';

    // Prev
    if (current > 1) {
        html += `<button onclick="load(${current - 1})"
            class="px-3 py-1 rounded bg-white/10 hover:bg-blue-500/40 transition">
            Prev
        </button>`;
    }

    // Numbers
    for (let i = 1; i <= totalPages; i++) {

        if (i === current) {
            html += `<button class="px-3 py-1 rounded bg-blue-500 text-white">${i}</button>`;
        } else {
            html += `<button onclick="load(${i})"
                class="px-3 py-1 rounded bg-white/10 hover:bg-blue-500/40 transition">
                ${i}
            </button>`;
        }
    }

    // Next
    if (current < totalPages) {
        html += `<button onclick="load(${current + 1})"
            class="px-3 py-1 rounded bg-white/10 hover:bg-blue-500/40 transition">
            Next
        </button>`;
    }

    pagination.innerHTML = html;
}

/* =========================
   INIT
========================= */
load(1);
</script>