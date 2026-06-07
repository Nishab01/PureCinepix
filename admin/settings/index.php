<?php
$currentpage = "settings";
$head_name = "Settings";
$page_title = "Settings";

require_once '../partials/guard.php';
require_once '../../config/config.php';
require_once '../../config/constants.php';

function getSetting($key) {
    global $conn;

    $stmt = $conn->prepare("SELECT value FROM settings WHERE `key`=? LIMIT 1");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        return $row['value'];
    }
}

include '../partials/sidebar.php';
include '../partials/header.php';

// if($is_superadmin === false){
//     header("Location: ../../auth/login.php");
//     exit;
// }
?>

<div class="ml-64 mt-16 p-6 space-y-6">
    <?php
        $activeTab = $_GET['tab'] ?? 'frontend';
    ?>

    <div class="flex gap-2 border-b border-white/10 mb-4">
        <a href="?tab=frontend"
            class="px-4 py-2 rounded-t-lg text-sm
            <?= $activeTab === 'frontend'
                ? 'bg-blue-500/20 text-blue-400'
                : 'text-white/60 hover:text-white' ?>">
            Frontend UI
        </a>

        <?php if($is_superadmin === true): ?>
        <a href="?tab=content"
            class="px-4 py-2 rounded-t-lg text-sm
            <?= $activeTab === 'content'
                ? 'bg-blue-500/20 text-blue-400'
                : 'text-white/60 hover:text-white' ?>">
            Content Behavior
        </a>
        <?php endif ?>
    </div>

    <?php if ($activeTab === 'frontend'): ?>
        <div id="frontendTab" class="p-6 rounded-xl bg-white/5 border border-white/10">
            <h3 class="text-lg font-semibold mb-4">Frontend UI Settings</h3>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-white/60">Hero Slider Speed (ms)</label>
                    <input type="number" id="hero_slide_speed"
                        value="<?= getSetting('hero_slide_speed') ?>"
                        class="w-full mt-1 p-2 rounded-lg bg-white/10 border border-white/20 text-sm">
                </div>

                <div>
                    <label class="text-sm text-white/60">Trailer Delay (ms)</label>
                    <input type="number" id="hero_trailer_delay"
                        value="<?= getSetting('hero_trailer_delay') ?>"
                        class="w-full mt-1 p-2 rounded-lg bg-white/10 border border-white/20 text-sm">
                </div>

                <div>
                    <label class="text-sm text-white/60">Max Items Per Page</label>
                    <input type="number" id="pagination_limit"
                        value="<?= getSetting('pagination_limit') ?>"
                        class="w-full mt-1 p-2 rounded-lg bg-white/10 border border-white/20 text-sm">
                </div>

                <button onclick="saveFrontend()"
                    class="px-5 py-2 bg-blue-500 hover:bg-blue-600 rounded-lg text-sm">
                    Save Settings
                </button>
            </div>
        </div>
    <?php endif; ?>


    <?php if($is_superadmin === true): ?>
        <?php if ($activeTab === 'content'): ?>
            <div id="contentTab" class="p-6 rounded-xl bg-white/5 border border-white/10">
                <h3 class="text-lg font-semibold mb-4">Content Behavior</h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-white/60">Comment Cooldown (seconds)</label>
                        <input type="number" id="comment_cooldown"
                            value="<?= getSetting('comment_cooldown') ?>"
                            class="w-full mt-1 p-2 rounded-lg bg-white/10 border border-white/20 text-sm">
                    </div>

                    <div>
                        <label class="text-sm text-white/60">Offensive Words</label>
                        <textarea id="offensive_words"
                            class="w-full mt-1 p-2 rounded-lg bg-white/10 border border-white/20 text-sm"
                            rows="4"><?= getSetting('offensive_words') ?></textarea>
                    </div>

                    <button onclick="saveContent()"
                        class="px-5 py-2 bg-blue-500 hover:bg-blue-600 rounded-lg text-sm">
                        Save Settings
                    </button>
                </div>
            </div>
        <?php endif; ?>
    <?php endif ?>
</div>

<script>
    async function saveFrontend() {

        const data = {
            hero_slide_speed: document.getElementById('hero_slide_speed').value,
            hero_trailer_delay: document.getElementById('hero_trailer_delay').value,
            pagination_limit: document.getElementById('pagination_limit').value
        };

        await sendSettings(data);
    }

    async function saveContent() {

        let banned = document.getElementById('offensive_words').value;

        // normalize
        banned = banned.toLowerCase().replace(/\s+/g, '');

        const data = {
            comment_cooldown: document.getElementById('comment_cooldown').value,
            offensive_words: banned
        };

        await sendSettings(data);
    }

    async function sendSettings(data) {

        const res = await fetch('update.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await res.json();

        if (result.status === 'success') {
            alert('Settings saved successfully'); // later we replace with toast
        }
    }
</script>