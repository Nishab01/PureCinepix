<?php
$currentpage = "requests";
$head_name = "Content Requests";
$page_title = "Content Requests";

require_once '../partials/guard.php';
require_once '../../config/config.php';
require_once '../../config/constants.php';

$message = "";

// CURRENT ADMIN ID
$current_admin_id = $_SESSION[SESSION_USER]['id'] ?? 0;
$current_admin_email = $_SESSION[SESSION_USER]['email'] ?? "";

$requests = [];

$stmt = $conn->prepare("
    SELECT
        sr.id,
        sr.search_text,
        sr.normalized_text,
        sr.status,
        COUNT(ru.id) AS total_requests
    FROM search_requests sr
    LEFT JOIN request_users ru
        ON ru.request_id = sr.id
    GROUP BY sr.id
    ORDER BY sr.id DESC
");

$stmt->execute();

$requests = $stmt
    ->get_result()
    ->fetch_all(MYSQLI_ASSOC);

?>

<?php include '../partials/sidebar.php'; ?>
<?php include '../partials/header.php'; ?>

<div class="ml-64 mt-16 p-6 space-y-6">

    <!-- HEADER -->
    <div>
        <h2 class="text-2xl font-semibold text-white/90">Content Requests</h2>
        <p class="text-sm text-white/50">Manage requested contents</p>
    </div>

    <button
        id="deleteSelectedBtn"
        class="px-5 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-sm transition cursor-pointer">
        Delete Selected
    </button>

    <!-- MESSAGE -->
    <?php if ($message): ?>
        <div class="p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <div class="">

        <div class="rounded-xl bg-white/5 border border-white/10 backdrop-blur-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-white/5">
                    <tr>
                        <th class="text-center p-2">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th class="text-left p-4">Search Text</th>
                        <th class="text-left p-4">Normalized Text</th>
                        <th class="text-center p-4">Requests</th>
                        <th class="text-center p-4">Status</th>
                        <th class="text-center p-4">Action</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (empty($requests)): ?>

                    <tr>
                        <td colspan="4" class="p-6 text-center text-white/50">
                            No requests found.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($requests as $request): ?>

                        <tr class="border-t border-white/10">
                            <td class="text-center p-2">
                                <input
                                    type="checkbox"
                                    class="rowCheckbox"
                                    value="<?= $request['id'] ?>"
                                >
                            </td>

                            <td class="p-4">
                                <?= htmlspecialchars($request['search_text']) ?>
                            </td>
                            
                            <td class="p-4">
                                <?= htmlspecialchars($request['normalized_text']) ?>
                            </td>

                            <td class="p-4 text-center">
                                <?= (int)$request['total_requests'] ?>
                            </td>

                            <td class="p-4 text-center">

                                <?php if ($request['status'] === 'completed'): ?>

                                    <span class="px-3 py-1 rounded-full text-xs bg-green-500/20 text-green-400">
                                        Completed
                                    </span>

                                <?php else: ?>

                                    <span class="px-3 py-1 rounded-full text-xs bg-yellow-500/20 text-yellow-400">
                                        Pending
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td class="p-4 text-center">

                                <a href="view-requests.php?id=<?= $request['id'] ?>"
                                class="px-3 py-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-sm">
                                    View
                                </a>

                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>
            </table>
        </div>

    </div>
</div>



<script>
document.getElementById('selectAll')
.addEventListener('change', function () {

    document.querySelectorAll('.rowCheckbox')
    .forEach(cb => {
        cb.checked = this.checked;
    });

});

document.getElementById('deleteSelectedBtn')
.addEventListener('click', async () => {

    const selected = [];

    document.querySelectorAll('.rowCheckbox:checked')
    .forEach(cb => {
        selected.push(cb.value);
    });

    if (selected.length === 0) {
        alert('No requests selected');
        return;
    }

    if (!confirm(`Delete ${selected.length} selected requests?`)) {
        return;
    }

    try {

        const response = await fetch(
            '../api/delete_requests.php',
            {
                method: 'POST',
                headers: {
                    'Content-Type':
                        'application/json'
                },
                body: JSON.stringify({
                    ids: selected
                })
            }
        );

        const data = await response.json();

        if (data.success) {
            location.reload();
        } else {
            alert('Delete failed');
        }

    } catch (e) {
        console.error(e);
        alert('Delete failed');
    }

});
</script>