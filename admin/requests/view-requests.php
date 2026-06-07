<?php

$currentpage = "requests";
$head_name = "View Request";
$page_title = "View Request";

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
    SELECT
        sr.*
    FROM search_requests sr
    WHERE sr.id = ?
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
| Users Who Requested
|--------------------------------------------------------------------------
*/

$usersStmt = $conn->prepare("
    SELECT
        u.id,
        u.name,
        u.email,
        ru.created_at
    FROM request_users ru
    INNER JOIN users u
        ON u.id = ru.user_id
    WHERE ru.request_id = ?
    ORDER BY ru.created_at DESC
");

$usersStmt->bind_param("i", $requestId);
$usersStmt->execute();

$users = $usersStmt
    ->get_result()
    ->fetch_all(MYSQLI_ASSOC);

include '../partials/sidebar.php';
include '../partials/header.php';

?>

<div class="ml-64 mt-16 p-6 space-y-6">

    <div>
        <h2 class="text-2xl font-semibold">
            <?= htmlspecialchars($request['search_text']) ?>
        </h2>

        <p class="text-white/50 mt-1">
            Request ID #<?= $request['id'] ?>
        </p>
    </div>

    <!-- REQUEST INFO -->

    <div class="p-6 rounded-xl bg-white/5 border border-white/10">

        <div class="grid md:grid-cols-3 gap-4">

            <div>
                <p class="text-white/50 text-sm">
                    Status
                </p>

                <p class="mt-1">
                    <?php if ($request['status'] === 'completed'): ?>
                        <span class="text-green-400">
                            Completed
                        </span>
                    <?php else: ?>
                        <span class="text-yellow-400">
                            Pending
                        </span>
                    <?php endif; ?>
                </p>
            </div>

            <div>
                <p class="text-white/50 text-sm">
                    Created
                </p>

                <p class="mt-1">
                    <?= $request['created_at'] ?>
                </p>
            </div>

            <div>
                <p class="text-white/50 text-sm">
                    Total Requesters
                </p>

                <p class="mt-1">
                    <?= count($users) ?>
                </p>
            </div>

        </div>

    </div>

    <!-- ACTIONS -->

    <div class="flex gap-3">

        <?php if ($request['status'] === 'pending'): ?>

            <a href="mark-complete.php?id=<?= $request['id'] ?>"
               class="px-4 py-2 rounded-lg bg-green-500 hover:bg-green-600">
                Mark Completed
            </a>

        <?php endif; ?>

        <a href="index.php"
           class="px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20">
            Back
        </a>

    </div>

    <!-- REQUESTERS -->

    <div class="rounded-xl bg-white/5 border border-white/10 overflow-hidden">

        <table class="w-full">

            <thead class="bg-white/5">
                <tr>
                    <th class="text-left p-4">
                        Name
                    </th>

                    <th class="text-left p-4">
                        Email
                    </th>

                    <th class="text-left p-4">
                        Requested At
                    </th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($users)): ?>

                    <tr>
                        <td colspan="3"
                            class="p-6 text-center text-white/50">
                            No users found.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($users as $user): ?>

                        <tr class="border-t border-white/10">

                            <td class="p-4">
                                <?= htmlspecialchars($user['name']) ?>
                            </td>

                            <td class="p-4">
                                <?= htmlspecialchars($user['email']) ?>
                            </td>

                            <td class="p-4">
                                <?= $user['created_at'] ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>