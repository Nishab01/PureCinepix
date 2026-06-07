<?php
$pagename = "PureCinepix | Browse";
require_once '../config/config.php';
require_once '../config/constants.php';

$genrelist = $conn->query("SELECT * FROM genres ORDER BY name ASC");
$categorylist = $conn->query("SELECT * FROM categories WHERE show_in_navbar = 1 OR show_in_home = 1 ORDER BY name ASC");

include '../includes/header.php';
?>

<div class="mt-10 max-w-[1400px] mx-auto px-3 md:px-6 flex gap-4">

    <!-- SIDEBAR -->
    <aside class="hidden md:flex flex-col w-fit space-y-6 sticky top-20 h-fit max-h-[calc(100vh-100px)] overflow-y-auto pr-2">

        <!-- TYPE -->
        <div>
            <h3 class="text-white font-semibold mb-2">Type</h3>
            <div class="flex flex-col gap-1 text-sm">
                <label><input type="checkbox" value="movie" class="filter-type mr-2">Movies</label>
                <label><input type="checkbox" value="tv" class="filter-type mr-2">TV Shows</label>
            </div>
        </div>

        <!-- GENRES -->
        <div>
            <h3 class="text-white font-semibold mb-2">Genres</h3>
            <div class="max-h-50 overflow-y-auto flex flex-col gap-1 text-sm custom-scroll pr-1">
                <?php while ($g = $genrelist->fetch_assoc()): ?>
                    <label>
                        <input type="checkbox" class="filter-genre mr-2" value="<?= $g['id']; ?>">
                        <?= htmlspecialchars($g['name']) ?>
                    </label>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- CATEGORIES -->
        <div>
            <h3 class="text-white font-semibold mb-2">Categories</h3>
            <div class="max-h-30 overflow-y-auto flex flex-col gap-1 text-sm custom-scroll pr-1">
                <?php while ($c = $categorylist->fetch_assoc()): ?>
                    <label>
                        <input type="checkbox" class="filter-category mr-2" value="<?= $c['id']; ?>">
                        <?= htmlspecialchars($c['name']) ?>
                    </label>
                <?php endwhile; ?>
            </div>
        </div>

        <button onclick="clearFilters()"
            class="text-red-500 py-2 rounded-lg text-sm border border-red-500 hover:bg-red-600 hover:text-white">
            Clear Filters
        </button>

    </aside>

    <!-- MAIN -->
    <div class="flex-1">
        <!-- MOBILE FILTER BUTTON -->
        <div class="flex justify-between items-center mb-4">
            <!-- LEFT: ACTIVE FILTERS -->
            <!-- <div id="activeFilters" class="flex flex-wrap gap-2"></div> -->
            <div class="md:hidden">
                <button onclick="openFilter()"
                    class="bg-white/10 text-white px-3 py-2 rounded-lg text-sm border border-white/10">
                    Filters
                </button>
            </div>

            <!-- RIGHT: SORT -->
            <div>
                <select id="sortSelect"
                    class="bg-white/10 text-white px-3 py-2 rounded-lg text-sm border border-white/10">
                    
                    <option class="text-black" value="latest">Newest</option>
                    <option class="text-black" value="views">Most Viewed</option>
                    <option class="text-black" value="az">A → Z</option>
                    <option class="text-black" value="rating">Top Rated</option>
                    <option class="text-black" value="updated">Recently Updated</option>
                </select>
            </div>
        </div>

        <div id="activeFilters" class="flex flex-wrap gap-2 mb-4"></div>

        <div id="content-grid"
            class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-6 lg:grid-cols-6 gap-2 md:gap-4">
        </div>

        <div id="pagination"
            class="flex justify-center gap-2 mt-8 flex-wrap">
        </div>

    </div>
</div>

<!-- MOBILE FILTER MODAL -->
<div id="filterModal"
    class="fixed inset-0 bg-black/80 z-50 hidden p-4 overflow-y-auto flex items-center justify-center">

    <div class="bg-[#111] rounded-xl border border-white/20 p-4 space-y-4 w-full max-h-100 mx-auto">

        <div class="flex justify-between items-center">
            <h2 class="text-white text-lg">Filters</h2>
            <button onclick="closeFilter()" class="text-white text-xl">✕</button>
        </div>

        <!-- TYPE -->
        <div>
            <h3 class="text-white mb-1">Type</h3>
            <div class="flex flex-col gap-1 text-sm">
                <label><input type="checkbox" value="movie" class="filter-type mr-2">Movies</label>
                <label><input type="checkbox" value="tv" class="filter-type mr-2">TV Shows</label>
            </div>
        </div>

        <!-- GENRES -->
        <div>
            <h3 class="text-white mb-1">Genres</h3>
            <div class="max-h-40 overflow-y-auto flex flex-col gap-1 text-sm custom-scroll">
                <?php
                $genrelist->data_seek(0);
                while ($g = $genrelist->fetch_assoc()):
                ?>
                    <label>
                        <input type="checkbox" class="filter-genre mr-2" value="<?= $g['id']; ?>">
                        <?= htmlspecialchars($g['name']) ?>
                    </label>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- CATEGORIES -->
        <div>
            <h3 class="text-white mb-1">Categories</h3>
            <div class="max-h-40 overflow-y-auto flex flex-col gap-1 text-sm">
                <?php
                $categories->data_seek(0);
                while ($c = $categories->fetch_assoc()):
                ?>
                    <label>
                        <input type="checkbox" class="filter-category mr-2" value="<?= $c['id']; ?>">
                        <?= htmlspecialchars($c['name']) ?>
                    </label>
                <?php endwhile; ?>
            </div>
        </div>

        <button onclick="clearFilters()"
            class="w-full bg-red-500 py-2 rounded-lg text-white">
            Clear Filters
        </button>

    </div>
</div>


<script>
const grid = document.getElementById('content-grid');
const pagination = document.getElementById('pagination');
const activeFilters = document.getElementById('activeFilters');

const params = new URLSearchParams(window.location.search);

// =========================
// STATE (FROM URL)
// =========================
let page = parseInt(params.get('page')) || 1;

let types = (params.get('types') || '').split(',').filter(Boolean);
let genres = (params.get('genres') || '').split(',').filter(Boolean);
let categories = (params.get('categories') || '').split(',').filter(Boolean);

let sort = params.get('sort') || 'latest';

// ✅ SUPPORT SINGLE NAV LINKS
if (params.get('genre')) genres = [params.get('genre')];
if (params.get('category')) categories = [params.get('category')];
if (params.get('type')) types = [params.get('type')];

// =========================
// MAPS (ID → NAME)
// =========================
const GENRE_MAP = {
<?php
$genrelist->data_seek(0);
while ($g = $genrelist->fetch_assoc()) {
    echo "{$g['id']}: \"" . addslashes($g['name']) . "\",";
}
?>
};

const CATEGORY_MAP = {
<?php
$categories->data_seek(0);
while ($c = $categories->fetch_assoc()) {
    echo "{$c['id']}: \"" . addslashes($c['name']) . "\",";
}
?>
};

// =========================
// BUILD QUERY
// =========================
function buildQuery() {
    const url = new URLSearchParams();

    if (sort) url.set('sort', sort);
    if (types.length) url.set('types', types.join(','));
    if (genres.length) url.set('genres', genres.join(','));
    if (categories.length) url.set('categories', categories.join(','));

    url.set('page', page);

    return url.toString();
}

// =========================
// UPDATE URL (IMPORTANT)
// =========================
function updateURL() {
    const url = new URL(window.location);

    url.search = buildQuery();

    window.history.replaceState({}, '', url);
}

// =========================
// LOAD DATA
// =========================
async function loadData() {

    updateURL();

    grid.innerHTML = `<div class="text-white/60 col-span-full">Loading...</div>`;

    const res = await fetch(`../api/list.php?${buildQuery()}`);
    const data = await res.json();

    grid.innerHTML = data.html;

    renderPagination(data.totalPages);
    renderChips();
    syncCheckboxes();

    if (typeof animateCards === "function") {
        animateCards(grid, 30);
    }

    closeFilter();
}

// =========================
// PAGINATION
// =========================
function renderPagination(totalPages) {

    let html = '';

    for (let i = 1; i <= totalPages; i++) {
        html += `
        <button onclick="goToPage(${i})"
        class="px-3 py-1 rounded ${
            i === page ? 'bg-blue-500' : 'bg-white/10'
        }">${i}</button>`;
    }

    pagination.innerHTML = html;
}

function goToPage(p) {
    page = p;
    loadData();
}

// =========================
// FILTER EVENTS
// =========================
document.addEventListener('change', (e) => {

    if (e.target.classList.contains('filter-type')) {
        const val = e.target.value;

        if (e.target.checked) {
            if (!types.includes(val)) types.push(val);
        } else {
            types = types.filter(t => t !== val);
        }
    }

    if (e.target.classList.contains('filter-genre')) {
        const val = e.target.value;

        if (e.target.checked) {
            if (!genres.includes(val)) genres.push(val);
        } else {
            genres = genres.filter(g => g !== val);
        }
    }

    if (e.target.classList.contains('filter-category')) {
        const val = e.target.value;

        if (e.target.checked) {
            if (!categories.includes(val)) categories.push(val);
        } else {
            categories = categories.filter(c => c !== val);
        }
    }

    page = 1;
    loadData();
});

// =========================
// CLEAR
// =========================
function clearFilters() {
    types = [];
    genres = [];
    categories = [];
    sort = 'latest';

    document.querySelectorAll('input[type=checkbox]').forEach(c => c.checked = false);
    document.getElementById('sortSelect').value = 'latest';

    loadData();
}

// =========================
// CHIPS
// =========================
function renderChips() {

    let html = '';

    types.forEach(t => {
        html += `
        <span class="flex items-center gap-1 bg-blue-800 px-2 py-1 text-xs rounded">
            ${t === 'movie' ? 'Movies' : 'TV Shows'}
            <button onclick="removeType('${t}')">✕</button>
        </span>`;
    });

    genres.forEach(g => {
        html += `
        <span class="flex items-center gap-1 bg-cyan-800 px-2 py-1 text-xs rounded">
            ${GENRE_MAP[g] || g}
            <button onclick="removeGenre('${g}')">✕</button>
        </span>`;
    });

    categories.forEach(c => {
        html += `
        <span class="flex items-center gap-1 bg-purple-800 px-2 py-1 text-xs rounded">
            ${CATEGORY_MAP[c] || c}
            <button onclick="removeCategory('${c}')">✕</button>
        </span>`;
    });

    activeFilters.innerHTML = html;
}

// =========================
// REMOVE FILTERS
// =========================
function removeType(val) {
    types = types.filter(t => t !== val);
    loadData();
}

function removeGenre(val) {
    genres = genres.filter(g => g !== val);
    loadData();
}

function removeCategory(val) {
    categories = categories.filter(c => c !== val);
    loadData();
}

// =========================
// SYNC CHECKBOXES
// =========================
function syncCheckboxes() {

    document.querySelectorAll('.filter-type').forEach(cb => {
        cb.checked = types.includes(cb.value);
    });

    document.querySelectorAll('.filter-genre').forEach(cb => {
        cb.checked = genres.includes(cb.value);
    });

    document.querySelectorAll('.filter-category').forEach(cb => {
        cb.checked = categories.includes(cb.value);
    });

    document.getElementById('sortSelect').value = sort;
}

// =========================
// SORT
// =========================
document.getElementById('sortSelect').addEventListener('change', (e) => {
    sort = e.target.value;
    page = 1;
    loadData();
});

// =========================
// MOBILE MODAL
// =========================
function openFilter() {
    document.getElementById('filterModal').classList.remove('hidden');
}

function closeFilter() {
    document.getElementById('filterModal').classList.add('hidden');
}

// =========================
// INIT
// =========================
syncCheckboxes();
loadData();
</script>

<?php include '../includes/footer.php'; ?>