<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/constants.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION[SESSION_USER] ?? null;

if (!$user) {
    return;
}

$userId = (int)$user['id'];

$stmt = $conn->prepare("
    SELECT
        rn.id,
        c.id AS content_id,
        c.title,
        c.poster1
    FROM request_notifications rn
    INNER JOIN contents c
        ON c.id = rn.content_id
    WHERE rn.user_id = ?
    ORDER BY rn.created_at DESC
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (empty($notifications)) {
    return;
}
?>

<!-- OVERLAY -->
<div id="requestNotificationOverlay"
    class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[9998]">
</div>

<!-- MODAL -->
<div id="requestNotificationModal"
    class="fixed inset-0 flex items-center justify-center p-4 z-[9999]">

    <div class="w-full max-w-2xl rounded-2xl border border-blue-500/20 bg-[#0b0b0b] shadow-2xl overflow-hidden">

        <div class="p-6 border-b border-white/10">
            <h2 class="text-2xl font-bold text-white">
                Requested Content Available
            </h2>

            <p class="text-white/60 mt-2">
                Content(s) you requested are now available.
            </p>
        </div>

        <div class="p-6 max-h-[60vh] overflow-y-auto space-y-3">

            <?php foreach ($notifications as $item): ?>

                <!-- <a
                    href="../pages/content.php?slug=<?= $item['content_id'] ?>&title=<?= $item['title'] ?>"
                    class="flex items-center gap-4 p-3 rounded-xl bg-white/5 hover:bg-blue-500/20 border border-white/10 hover:border-blue-400/30 transition"
                > -->
                <a
                    href="../api/view_notification.php?id=<?= $item['id'] ?>"
                    class="flex items-center gap-4 p-3 rounded-xl bg-white/5 hover:bg-blue-500/20 border border-white/10 hover:border-blue-400/30 transition"
                >

                    <img
                        src="<?= htmlspecialchars($item['poster1'] ?: '../assets/no-image.png') ?>"
                        class="w-14 h-20 object-cover rounded-lg"
                    >

                    <div>
                        <div class="text-white font-medium">
                            <?= htmlspecialchars($item['title']) ?>
                        </div>

                        <div class="text-blue-400 text-sm mt-1">
                            Click to view
                        </div>
                    </div>

                </a>

            <?php endforeach; ?>

        </div>

        <div class="p-6 border-t border-white/10 flex justify-end">

            <button
                id="closeRequestNotifications"
                class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-white transition"
            >
                Close
            </button>

        </div>

    </div>

</div>

<script>
document
.getElementById('closeRequestNotifications')
.addEventListener('click', async () => {

    try {

        await fetch('../api/dismiss_notifications.php', {
            method: 'POST'
        });

    } catch (e) {
        console.error(e);
    }

    document
        .getElementById('requestNotificationModal')
        .remove();

    document
        .getElementById('requestNotificationOverlay')
        .remove();
});
</script>