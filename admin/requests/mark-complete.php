<?php

require_once '../partials/guard.php';
require_once '../../config/config.php';
require_once '../../config/constants.php';

$requestId = (int)($_GET['id'] ?? 0);

if (!$requestId) {
    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Request Info
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT *
    FROM search_requests
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $requestId);
$stmt->execute();

$request = $stmt->get_result()->fetch_assoc();

if (!$request) {
    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Complete Request
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $contentId = (int)($_POST['content_id'] ?? 0);

    if ($contentId > 0) {

        // Prevent duplicate completion
        if ($request['status'] !== 'completed') {

            $usersStmt = $conn->prepare("
                SELECT user_id
                FROM request_users
                WHERE request_id = ?
            ");

            $usersStmt->bind_param("i", $requestId);
            $usersStmt->execute();

            $users = $usersStmt
                ->get_result()
                ->fetch_all(MYSQLI_ASSOC);

            foreach ($users as $user) {

                $userId = (int)$user['user_id'];

                $check = $conn->prepare("
                    SELECT id
                    FROM request_notifications
                    WHERE request_id = ?
                    AND content_id = ?
                    AND user_id = ?
                    LIMIT 1
                ");

                $check->bind_param(
                    "iii",
                    $requestId,
                    $contentId,
                    $userId
                );

                $check->execute();

                if ($check->get_result()->num_rows === 0) {

                    $insert = $conn->prepare("
                        INSERT INTO request_notifications
                        (
                            request_id,
                            content_id,
                            user_id
                        )
                        VALUES
                        (
                            ?,
                            ?,
                            ?
                        )
                    ");

                    $insert->bind_param(
                        "iii",
                        $requestId,
                        $contentId,
                        $userId
                    );

                    $insert->execute();
                }
            }

            $update = $conn->prepare("
                UPDATE search_requests
                SET status = 'completed'
                WHERE id = ?
                LIMIT 1
            ");

            $update->bind_param("i", $requestId);
            $update->execute();
        }

        header("Location: view-requests.php?id=" . $requestId);
        exit;
    }
}

$currentpage = "requests";
$head_name = "Complete Request";
$page_title = "Complete Request";

include '../partials/sidebar.php';
include '../partials/header.php';

?>

<div class="ml-64 mt-16 p-6">

    <div class="max-w-3xl mx-auto">

        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-white">
                Complete Request
            </h2>

            <p class="text-white/50 mt-1">
                <?= htmlspecialchars($request['search_text']) ?>
            </p>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-xl p-6">

            <form method="POST">

                <!-- SEARCH INPUT -->

                <div class="relative">

                    <label class="block mb-2 text-sm text-white/70">
                        Search Content
                    </label>

                    <input
                        id="searchInput"
                        type="text"
                        placeholder="Type movie or TV show title..."
                        autocomplete="off"
                        class="w-full px-4 py-3 rounded-lg
                        bg-black/40 border border-white/10
                        focus:outline-none focus:border-blue-500"
                    >

                    <div
                        id="searchResults"
                        class="hidden absolute left-0 right-0 mt-2 z-50
                        bg-black border border-white/10 rounded-xl
                        max-h-[450px] overflow-y-auto">
                    </div>

                </div>

                <!-- HIDDEN CONTENT ID -->

                <input
                    type="hidden"
                    name="content_id"
                    id="contentId"
                    required
                >

                <!-- SELECTED CONTENT -->

                <div
                    id="selectedContent"
                    class="hidden mt-4">
                </div>

                <!-- ACTIONS -->

                <div class="mt-6 flex gap-3">

                    <button
                        type="submit"
                        class="px-5 py-3 rounded-lg
                        bg-green-500 hover:bg-green-600
                        text-white font-medium"
                    >
                        Mark Completed
                    </button>

                    <a
                        href="view-requests.php?id=<?= $requestId ?>"
                        class="px-5 py-3 rounded-lg
                        bg-white/10 hover:bg-white/20"
                    >
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

<script>

const input = document.getElementById('searchInput');
const resultsBox = document.getElementById('searchResults');
const selectedContent = document.getElementById('selectedContent');
const contentIdInput = document.getElementById('contentId');

let debounce;
let lastQuery = '';

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

    if (type === 'movie') {
        return start;
    }

    if (type === 'tv') {

        if (last && last !== release) {
            return `${start} - ${formatDate(last)}`;
        }

        return start;
    }

    return start;
}

input.addEventListener('input', () => {

    const query = input.value.trim();

    clearTimeout(debounce);

    if (query === lastQuery) {
        return;
    }

    if (query.length < 2) {

        resultsBox.classList.add('hidden');
        resultsBox.innerHTML = '';

        return;
    }

    debounce = setTimeout(async () => {

        lastQuery = query;

        resultsBox.innerHTML = `
            <div class="p-3 text-white/60">
                Searching...
            </div>
        `;

        resultsBox.classList.remove('hidden');

        try {

            const res = await fetch(
                `../api/search-content.php?q=${encodeURIComponent(query)}`
            );

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
            <div class="p-3 text-white/60">
                No results found
            </div>
        `;

        return;
    }

    let html = '';

    data.forEach(item => {

        const poster =
            item.poster1 || '../../assets/no-image.png';

        html += `
            <div
                class="content-result cursor-pointer
                flex gap-3 p-3 hover:bg-blue-500/10 transition"
                data-id="${item.id}"
                data-title="${item.title}">

                <img
                    src="${poster}"
                    class="w-14 h-20 object-cover rounded-lg">

                <div>

                    <div class="font-medium">
                        ${item.title}
                    </div>

                    <div class="text-xs text-white/50 mt-1">
                        ${item.type === 'tv'
                            ? 'TV Show'
                            : 'Movie'}
                    </div>

                    <div class="text-xs text-white/40">
                        ${formatDateRange(
                            item.release_date,
                            item.last_date,
                            item.type
                        )}
                    </div>

                </div>

            </div>
        `;
    });

    resultsBox.innerHTML = html;
}

resultsBox.addEventListener('click', (e) => {

    const item =
        e.target.closest('.content-result');

    if (!item) {
        return;
    }

    const id = item.dataset.id;
    const title = item.dataset.title;

    contentIdInput.value = id;

    input.value = title;

    selectedContent.classList.remove('hidden');

    selectedContent.innerHTML = `
        <div class="p-4 rounded-lg
            bg-green-500/10
            border border-green-500/20
            text-green-400">
            Selected: ${title}
        </div>
    `;

    resultsBox.classList.add('hidden');
});

document.addEventListener('click', (e) => {

    if (
        !e.target.closest('#searchInput') &&
        !e.target.closest('#searchResults')
    ) {
        resultsBox.classList.add('hidden');
    }

});

</script>