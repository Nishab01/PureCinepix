<?php
$currentpage = "contents";
$head_name = "All Contents";
$page_title = "All Contents";

require_once '../partials/guard.php';
require_once '../../config/config.php';
require_once '../../config/constants.php';

/* =========================
   FILTERS
========================= */
$categoriesList = $conn->query("SELECT * FROM categories ORDER BY id ASC");

$search = $_GET['search'] ?? '';
$type   = $_GET['type'] ?? '';
$selectedCategory = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'added_latest';

$where = "WHERE 1";

if ($search) {
    $s = $conn->real_escape_string($search);
    $where .= " AND c.title LIKE '%$s%'";
}

if ($type && in_array($type, ['movie', 'tv'])) {
    $where .= " AND c.type='$type'";
}

if ($selectedCategory) {
    $catId = (int)$selectedCategory;
    $where .= " AND c.id IN (
        SELECT content_id FROM content_categories WHERE category_id = $catId
    )";
}

/* =========================
   PAGINATION
========================= */
$limit = 20;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$totalQuery = $conn->query("
    SELECT COUNT(DISTINCT c.id) as c 
    FROM contents c
    LEFT JOIN content_categories cc ON c.id = cc.content_id
    $where
");

$totalRows = $totalQuery->fetch_assoc()['c'];
$totalPages = ceil($totalRows / $limit);

/* =========================
   FETCH DATA
========================= */
$query = $conn->query("
    SELECT
        c.*,
        GROUP_CONCAT(cat.name SEPARATOR ', ') AS categories
    FROM contents c
    LEFT JOIN content_categories cc ON c.id = cc.content_id
    LEFT JOIN categories cat ON cc.category_id = cat.id
    $where
    GROUP BY c.id
    ORDER BY

    -- NAME
    CASE 
        WHEN '$sort' = 'name_asc' THEN c.title
    END ASC,

    CASE 
        WHEN '$sort' = 'name_desc' THEN c.title
    END DESC,

    -- RELEASE DATE
    CASE 
        WHEN '$sort' = 'latest' THEN c.release_date
    END DESC,

    CASE 
        WHEN '$sort' = 'oldest' THEN c.release_date
    END ASC,

    -- ADDED DATE (ID BASED)
    CASE 
        WHEN '$sort' = 'added_latest' THEN c.id
    END DESC,

    CASE 
        WHEN '$sort' = 'added_oldest' THEN c.id
    END ASC

    LIMIT $limit OFFSET $offset
");

/* =========================
   DELETE
========================= */
if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    if ($id > 0) {

        $conn->begin_transaction();

        try {

            $conn->query("DELETE FROM downloads WHERE content_id = $id");

            $conn->query("
                DELETE e FROM episodes e
                JOIN seasons s ON e.season_id = s.id
                WHERE s.content_id = $id
            ");

            $conn->query("DELETE FROM seasons WHERE content_id = $id");

            $conn->query("DELETE FROM content_categories WHERE content_id = $id");
            $conn->query("DELETE FROM content_genres WHERE content_id = $id");

            $conn->query("DELETE FROM contents WHERE id = $id");

            $conn->commit();

            header("Location: index.php?deleted=1");
            exit;

        } catch (Exception $e) {
            $conn->rollback();
            $message = "Delete failed!";
        }
    }
}

if (isset($_POST['trim_views'])) {

    $stmt = $conn->prepare("
        DELETE FROM content_views
        WHERE viewed_at < DATE_SUB(NOW(), INTERVAL 90 DAY)
    ");

    if ($stmt->execute()) {

        $deleted = $stmt->affected_rows;

        header(
            "Location: index.php?trimmed=1&deleted=" . $deleted
        );
        exit;

    } else {

        header("Location: index.php?trim_error=1");
        exit;
    }
}
?>

<?php include '../partials/sidebar.php'; ?>
<?php include '../partials/header.php'; ?>

<div class="ml-64 mt-16 p-6 space-y-6">

    <!-- HEADER -->
    <div>
        <h2 class="text-2xl font-semibold text-white/90">Contents</h2>
        <p class="text-sm text-white/50">Manage your content library</p>
    </div>

    <!-- ACTION BAR -->
    <div class="flex justify-between items-center">
        <div class="flex flex-row gap-2">
            <a href="addnew.php"
                class="px-5 py-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-sm transition
                hover:shadow-[0_0_10px_rgba(59,130,246,0.5)]">
                Add New Content
            </a>

            <button id="syncBtn"
                class="px-5 py-2 rounded-lg bg-purple-500 hover:bg-purple-600 text-sm transition cursor-pointer">
                Sync Selected
            </button>

            <form method="POST">
                <button
                    name="trim_views"
                    class="px-5 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-sm transition cursor-pointer">
                    Trim View Cache
                </button>
            </form>
        </div>

        <div class="text-right">
            <p class="text-sm text-white/50">Total</p>
            <p class="text-xl font-bold text-yellow-400">
                <?= $totalRows ?>
            </p>
        </div>

    </div>

    <?php if (isset($_GET['trimmed'])): ?>
        <div class="p-4 rounded-lg border border-green-500/30 bg-green-500/10 text-green-300">
            View cache trimmed successfully.
            Removed <?= (int)($_GET['deleted'] ?? 0) ?> old records.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['trim_error'])): ?>
        <div class="p-4 rounded-lg border border-red-500/30 bg-red-500/10 text-red-300">
            Trimming failed.
        </div>
    <?php endif; ?>

    <!-- FILTERS -->
    <form method="GET"
        class="p-4 rounded-xl bg-white/5 border border-white/10 backdrop-blur-lg flex flex-wrap gap-4 items-center">

        <input type="text" name="search" placeholder="Search title..."
            value="<?= htmlspecialchars($search) ?>"
            class="px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-sm">

        <select name="type"
            class="px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-sm">

            <option class="text-black" value="">All Types</option>
            <option class="text-black" value="movie" <?= $type === 'movie' ? 'selected' : '' ?>>Movie</option>
            <option class="text-black" value="tv" <?= $type === 'tv' ? 'selected' : '' ?>>TV Show</option>

        </select>

        <select name="category"
            class="px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-sm">

            <option class="text-black" value="">All Categories</option>

            <?php
            $categoriesList->data_seek(0);
            while ($cat = $categoriesList->fetch_assoc()):
            ?>
                <option class="text-black" value="<?= $cat['id'] ?>"
                    <?= $selectedCategory == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endwhile; ?>

        </select>

        <select name="sort"
            class="px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-sm">
            
            <option class="text-black" value="added_latest" <?= $sort === 'added_latest' ? 'selected' : '' ?>>
                Latest Added
            </option>

            <option class="text-black" value="added_oldest" <?= $sort === 'added_oldest' ? 'selected' : '' ?>>
                Oldest Added
            </option>

            <option class="text-black" value="latest" <?= $sort === 'latest' ? 'selected' : '' ?>>
                Newest Release
            </option>

            <option class="text-black" value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>
                Oldest Release
            </option>

            <option class="text-black" value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>
                Name (A → Z)
            </option>

            <option class="text-black" value="name_desc" <?= $sort === 'name_desc' ? 'selected' : '' ?>>
                Name (Z → A)
            </option>

        </select>

        <button
            class="px-4 py-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-sm transition">
            Filter
        </button>

        <!-- RESET BUTTON -->
        <a href="index.php"
            class="px-4 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-sm transition">
            Reset
        </a>

    </form>

    <!-- ACTIVE FILTER CHIPS -->
    <div class="flex gap-2 flex-wrap">

        <?php if ($search): ?>
            <span class="px-3 py-1 text-xs rounded bg-blue-500/20 text-blue-300">
                Search: <?= htmlspecialchars($search) ?>
            </span>
        <?php endif; ?>

        <?php if ($type): ?>
            <span class="px-3 py-1 text-xs rounded bg-purple-500/20 text-purple-300">
                Type: <?= ucfirst($type) ?>
            </span>
        <?php endif; ?>

        <?php if ($selectedCategory): ?>
            <?php
            $catName = $conn->query("SELECT name FROM categories WHERE id=".(int)$selectedCategory)->fetch_assoc()['name'];
            ?>
            <span class="px-3 py-1 text-xs rounded bg-green-500/20 text-green-300">
                Category: <?= htmlspecialchars($catName) ?>
            </span>
        <?php endif; ?>

    </div>

    <!-- Progress Bar -->
    <div id="syncProgress"
        class="hidden p-4 rounded-lg bg-black/40 border border-white/10 text-sm">

        <div class="flex justify-between mb-2">
            <span id="progressText">Syncing...</span>
            <span id="progressCount">0 / 0</span>
        </div>

        <div class="w-full bg-white/10 rounded-full h-2">
            <div id="progressBar"
                class="h-2 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full transition-all duration-300"
                style="width: 0%">
            </div>
        </div>

    </div>

    <!-- TABLE -->
    <div class="rounded-xl bg-white/5 border border-white/10 overflow-x-auto">

        <table class="w-full text-sm text-white/80">

            <thead class="text-white/50 border-b border-white/10 text-left">
                <tr>
                    <th class="p-3">
                        <input class="h-4 w-4" type="checkbox" id="selectAll">
                    </th>
                    <th class="p-3">Poster</th>
                    <th class="p-3">Title</th>
                    <th class="p-3">Type</th>
                    <th class="p-3">Categories</th>
                    <th class="p-3">Release</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php while ($row = $query->fetch_assoc()): ?>

                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="p-3">
                            <input class="h-4 w-4 rowCheckbox" type="checkbox" value="<?= $row['id'] ?>">
                        </td>

                        <td class="p-3">
                            <img src="<?= $row['poster1'] ?>"
                                class="w-12 h-16 object-cover rounded">
                        </td>

                        <td class="p-3">
                            <?= htmlspecialchars($row['title']) ?>
                        </td>

                        <td class="p-3">
                            <?= $row['type'] === 'movie'
                                ? '<span class="text-blue-400">Movie</span>'
                                : '<span class="text-purple-400">TV</span>' ?>
                        </td>

                        <td class="p-3 text-xs">
                            <?= $row['categories'] ?: '<span class="text-white/40">None</span>' ?>
                        </td>

                        <td class="p-3 text-white/60">
                            <?= date("j M Y", strtotime($row['release_date'])) ?>
                        </td>

                        <td class="p-3 flex flex-wrap iems-center justify-centter gap-2">
                            <a href="manage.php?id=<?= $row['id'] ?>&type=<?= $row['type'] ?>"
                                class="px-3 py-1 text-xs bg-green-600 rounded">
                                Manage
                            </a>

                            <a href="?delete=<?= $row['id'] ?>"
                                onclick="return confirm('Delete this content?')"
                                class="px-3 py-1 text-xs bg-red-500 rounded">
                                Delete
                            </a>
                        </td>

                    </tr>

                <?php endwhile; ?>

            </tbody>

        </table>

    </div>

    <!-- PAGINATION -->
    <div class="flex justify-center gap-2">

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>

            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&type=<?= $type ?>&category=<?= $selectedCategory ?>&sort=<?= $sort ?>&search=<?= urlencode($search) ?>&type=<?= $type ?>&category=<?= $selectedCategory ?>&sort=<?= $sort ?>"
                class="px-3 py-1 rounded
                <?= $i == $page ? 'bg-blue-500' : 'bg-white/10' ?>">
                <?= $i ?>
            </a>

        <?php endfor; ?>

    </div>

</div>

<script>
    document.getElementById('selectAll').addEventListener('change', function () {

        const checked = this.checked;

        document.querySelectorAll('.rowCheckbox').forEach(cb => {
            cb.checked = checked;
        });

    });

    const checkboxes = document.querySelectorAll('.rowCheckbox');
    const counter = document.getElementById('selectedCount');

    function updateCount() {
        const count = document.querySelectorAll('.rowCheckbox:checked').length;
        counter.innerText = `${count} selected`;
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateCount);
    });

    document.getElementById('selectAll').addEventListener('change', function () {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateCount();
    });

    document.getElementById('syncBtn').addEventListener('click', async () => {

        const selected = [];

        document.querySelectorAll('.rowCheckbox:checked').forEach(cb => {
            selected.push(cb.value);
        });
        

        if (selected.length === 0) {
            alert('No content selected');
            return;
        }

        const progressBox = document.getElementById('syncProgress');
        const progressText = document.getElementById('progressText');
        const progressCount = document.getElementById('progressCount');
        const progressBar = document.getElementById('progressBar');

        progressBox.classList.remove('hidden');

        let completed = 0;
        const total = selected.length;

        for (let i = 0; i < selected.length; i++) {

            const id = selected[i];

            // progressText.innerText = `Syncing ID: ${id}`;
            progressCount.innerText = `${completed} / ${total}`;

            try {
                const res = await fetch('../api/sync_tmdb.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `content_id=${id}`
                });

                const data = await res.json();

                if (data.status === 'success') {
                    console.log(`✅ Synced ${id}`);
                } else if (data.status === 'skipped') {
                    console.log(`⏭ Skipped ${id}`);
                } else {
                    console.log(`❌ Error ${id}`, data);
                }

            } catch (err) {
                console.error("Error:", err);
            }

            completed++;

            // update progress UI
            progressCount.innerText = `${completed} / ${total}`;
            progressBar.style.width = `${(completed / total) * 100}%`;

            await new Promise(resolve => setTimeout(resolve, 500));
        }

        progressText.innerText = "Sync Completed";
    });
</script>