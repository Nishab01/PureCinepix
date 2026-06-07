<?php
$currentpage = "categories";
$head_name = 'Manage Categories';
$page_title = 'Manage Categories';

require_once '../partials/guard.php';
require_once '../../config/config.php';
require_once '../../config/constants.php';

$message = "";

/* =========================
   ADD CATEGORY
========================= */
if (isset($_POST['add_category'])) {

    $name = trim($_POST['name']);
    $limit = (int)$_POST['max_items'];
    $navbar = isset($_POST['show_in_navbar']) ? 1 : 0;
    $home = isset($_POST['show_in_home']) ? 1 : 0;

    if ($name) {
        $stmt = $conn->prepare("
            INSERT INTO categories (name, max_items, show_in_navbar, show_in_home)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("siii", $name, $limit, $navbar, $home);
        $stmt->execute();

        $message = "Category added!";
    }
}

/* =========================
   TRIM OVERFLOW
========================= */
if (isset($_POST['trim_categories'])) {

    // Only categories with LIMIT > 0
    $cats = $conn->query("SELECT id, max_items FROM categories WHERE max_items > 0");

    while ($c = $cats->fetch_assoc()) {

        $cat_id = (int)$c['id'];
        $limit  = (int)$c['max_items'];

        // Count items
        $countRes = $conn->query("
            SELECT COUNT(*) as c
            FROM content_categories
            WHERE category_id = $cat_id
        ");
        $count = (int)$countRes->fetch_assoc()['c'];

        if ($count > $limit) {

            $extra = $count - $limit;

            // SAFE DELETE (oldest first)
            $conn->query("
                DELETE FROM content_categories
                WHERE content_id IN (
                    SELECT content_id FROM (
                        SELECT content_id FROM content_categories
                        WHERE category_id = $cat_id
                        ORDER BY added_at ASC
                        LIMIT $extra
                    ) as temp
                )
                AND category_id = $cat_id
            ");
        }
    }

    $message = "Overflow trimmed successfully!";
}

/* =========================
   DELETE CATEGORY
========================= */
if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    $check = $conn->query("SELECT is_locked FROM categories WHERE id = $id")->fetch_assoc();

    if ($check && !$check['is_locked']) {
        $conn->query("DELETE FROM categories WHERE id = $id");
        $message = "Category deleted!";
    } else {
        $message = "This category is locked!";
    }
}

/* =========================
   UPDATE CATEGORY
========================= */
if (isset($_POST['update_category'])) {

    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $limit = (int)$_POST['max_items'];
    $navbar = isset($_POST['show_in_navbar']) ? 1 : 0;
    $home = isset($_POST['show_in_home']) ? 1 : 0;

    $check = $conn->query("SELECT is_locked FROM categories WHERE id = $id")->fetch_assoc();

    if ($check && !$check['is_locked']) {

        $stmt = $conn->prepare("
            UPDATE categories 
            SET name=?, max_items=?, show_in_navbar=?, show_in_home=?
            WHERE id=?
        ");
        $stmt->bind_param("siiii", $name, $limit, $navbar, $home, $id);
        $stmt->execute();

        $message = "Category updated!";
    } else {
        $message = "Locked category cannot be edited!";
    }
}

/* =========================
   FETCH DATA
========================= */
// $categories = $conn->query("SELECT * FROM categories ORDER BY id ASC");
$categories = $conn->query("
    SELECT c.*, COUNT(cc.content_id) AS content_count
    FROM categories c
    LEFT JOIN content_categories cc 
        ON c.id = cc.category_id
    GROUP BY c.id
    ORDER BY c.is_locked DESC, c.name ASC
");
$total = $conn->query("SELECT COUNT(*) as c FROM categories")->fetch_assoc()['c'];
?>

<?php include '../partials/sidebar.php'; ?>
<?php include '../partials/header.php'; ?>

<!-- MAIN -->
<div class="ml-64 mt-16 p-6 space-y-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center">

        <div>
            <h2 class="text-2xl font-semibold text-white/90">Categories</h2>
            <p class="text-sm text-white/50">Manage content categories</p>
        </div>

        <div class="text-right">
            <p class="text-sm text-white/50">Total</p>
            <p class="text-xl font-bold text-yellow-400"><?= $total ?></p>
        </div>

    </div>

    <!-- MESSAGE -->
    <?php if ($message): ?>
        <div class="p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <!-- ACTION CARD -->
    <div class="p-6 rounded-xl bg-white/5 border border-white/10 backdrop-blur-lg space-y-4">

        <!-- ADD CATEGORY -->
        <form method="POST" class="flex flex-wrap gap-4 items-center">

            <input type="text" name="name" placeholder="Category name"
                class="px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-sm">

            <input type="number" name="max_items" placeholder="Limit"
                class="px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-sm w-32">

            <label class="flex items-center gap-2 text-sm">
                <input class="h-4 w-4" type="checkbox" name="show_in_navbar">
                Show in Navbar
            </label>

            <label class="flex items-center gap-2 text-sm">
                <input class="h-4 w-4" type="checkbox" name="show_in_home">
                Show in Homepage
            </label>

            <button name="add_category"
                class="px-5 py-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-sm transition
                       hover:shadow-[0_0_10px_rgba(59,130,246,0.5)]">
                Add Category
            </button>

        </form>

        <?php if($is_superadmin === true): ?>
        <!-- TRIM BUTTON -->
        <form method="POST">
            <button name="trim_categories"
                class="px-5 py-2 rounded-lg bg-red-500/80 hover:bg-red-600
                       text-sm font-medium transition
                       hover:shadow-[0_0_10px_rgba(239,68,68,0.5)]">
                Trim Overflow
            </button>
        </form>
        <?php endif ?>

    </div>

    <!-- CATEGORY LIST -->
    <div class="p-6 rounded-xl bg-white/5 border border-white/10 backdrop-blur-lg overflow-x-auto">

        <table class="w-full text-sm text-left text-white/80">

            <thead class="text-white/50 border-b border-white/10">
                <tr>
                    <th class="py-2">Name</th>
                    <th>Limit</th>
                    <th>Contents</th>
                    <th>Navbar</th>
                    <th>Homepage</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                <?php while ($c = $categories->fetch_assoc()): ?>
                    <tr class="border-b border-white/5 hover:bg-white/5">

                        <form method="POST">

                            <!-- NAME -->
                            <td class="py-3">
                                <?php if (!$c['is_locked']): ?>
                                    <input type="text" name="name"
                                        value="<?= htmlspecialchars($c['name']) ?>"
                                        class="bg-transparent border border-white/10 px-2 py-1 rounded">
                                <?php else: ?>
                                    <?= htmlspecialchars($c['name']) ?>
                                <?php endif; ?>
                            </td>

                            <!-- LIMIT -->
                            <td>
                                <?php if (!$c['is_locked']): ?>
                                    <input type="number" name="max_items"
                                        value="<?= $c['max_items'] ?>"
                                        class="bg-transparent border border-white/10 px-2 py-1 rounded w-20">
                                <?php else: ?>
                                    <?= $c['max_items'] ?>
                                <?php endif; ?>
                            </td>

                            <!-- CONTENT COUNT -->
                            <td>
                                <?= $c['content_count'] ?>
                            </td>

                            <!-- NAVBAR -->
                            <td>
                                <?php if (!$c['is_locked']): ?>
                                    <input class="h-4 w-4" type="checkbox" name="show_in_navbar" <?= $c['show_in_navbar'] ? 'checked' : '' ?>>
                                <?php else: ?>
                                    <?= $c['show_in_navbar'] ? 'Yes' : 'No' ?>
                                <?php endif; ?>
                            </td>

                            <!-- HOMEPAGE -->
                            <td>
                                <?php if (!$c['is_locked']): ?>
                                    <input class="h-4 w-4" type="checkbox" name="show_in_home" <?= $c['show_in_home'] ? 'checked' : '' ?>>
                                <?php else: ?>
                                    <?= $c['show_in_home'] ? 'Yes' : 'No' ?>
                                <?php endif; ?>
                            </td>

                            <!-- STATUS -->
                            <td>
                                <?php if ($c['is_locked']): ?>
                                    <span class="text-red-400">Locked</span>
                                <?php else: ?>
                                    <span class="text-green-400">Editable</span>
                                <?php endif; ?>
                            </td>

                            <!-- ACTION -->
                            <td class="space-x-2">

                                <?php if (!$c['is_locked']): ?>

                                    <input type="hidden" name="id" value="<?= $c['id'] ?>">

                                    <button name="update_category"
                                        class="text-blue-400 hover:underline">
                                        Save
                                    </button>

                                    <a href="?delete=<?= $c['id'] ?>" onclick="return confirm('Are you sure you want to delete this category?');"
                                        class="text-red-400 hover:underline">
                                        Delete
                                    </a>

                                <?php else: ?>
                                    <span class="text-white/30">—</span>
                                <?php endif; ?>

                            </td>

                        </form>

                    </tr>
                <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>