<?php

$currentpage = "requests";
$head_name = "Complete Request";
$page_title = "Complete Request";

require_once '../partials/guard.php';
require_once '../../config/config.php';
require_once '../../config/constants.php';

$requestId = (int)($_GET['id'] ?? 0);

if (!$requestId) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $contentId = (int)($_POST['content_id'] ?? 0);

    if ($contentId > 0) {

        /*
        |--------------------------------------------------------------------------
        | Get Request Users
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Create Notifications
        |--------------------------------------------------------------------------
        */

        foreach ($users as $user) {

            $userId = (int)$user['user_id'];

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

        /*
        |--------------------------------------------------------------------------
        | Mark Request Completed
        |--------------------------------------------------------------------------
        */

        $update = $conn->prepare("
            UPDATE search_requests
            SET status = 'completed'
            WHERE id = ?
            LIMIT 1
        ");

        $update->bind_param("i", $requestId);
        $update->execute();

        header("Location: view-requests.php?id=" . $requestId);
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| Request
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

$request = $stmt
    ->get_result()
    ->fetch_assoc();

if (!$request) {
    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Contents
|--------------------------------------------------------------------------
*/

$contents = $conn
    ->query("
        SELECT id, title
        FROM contents
        ORDER BY title ASC
    ")
    ->fetch_all(MYSQLI_ASSOC);

include '../partials/sidebar.php';
include '../partials/header.php';

?>

<div class="ml-64 mt-16 p-6">

    <div class="max-w-2xl">

        <h2 class="text-2xl font-semibold mb-2">
            Complete Request
        </h2>

        <p class="text-white/50 mb-6">
            <?= htmlspecialchars($request['search_text']) ?>
        </p>

        <form method="POST" class="space-y-4">

            <select
                name="content_id"
                required
                class="w-full p-3 rounded-lg bg-white/5 border border-white/10"
            >
                <option value="">
                    Select Content
                </option>

                <?php foreach ($contents as $content): ?>

                    <option value="<?= $content['id'] ?>">
                        <?= htmlspecialchars($content['title']) ?>
                    </option>

                <?php endforeach; ?>

            </select>

            <button
                type="submit"
                class="px-5 py-3 rounded-lg bg-green-500 hover:bg-green-600"
            >
                Mark Completed
            </button>

        </form>

    </div>

</div>