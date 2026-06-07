<?php
$pagename = "Profile Settings";
require_once '../config/config.php';
require_once '../config/constants.php';

if (!isset($_SESSION[SESSION_USER])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION[SESSION_USER]['id'];

// =========================
// FETCH USER
// =========================
$stmt = $conn->prepare("SELECT name, email, password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$hasPassword = !empty(trim($user['password'] ?? ''));

// =========================
// HANDLE POST
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    // ---------- NAME ----------
    if ($action === 'update_name') {

        $name = trim($_POST['name'] ?? '');

        if ($name === '') {
            $_SESSION['flash_error'] = "Name cannot be empty.";
        } else if ($name !== $user['name']) {

            $stmt = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $name, $user_id);
            $stmt->execute();

            $_SESSION[SESSION_USER]['name'] = $name;
            $_SESSION['flash_success'] = "Name updated successfully.";
        }

        header("Location: profile.php");
        exit;
    }

    // ---------- PASSWORD ----------
    if ($action === 'update_password') {

        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (strlen($new) < 6) {
            $_SESSION['flash_error'] = "Minimum 6 characters required.";
            header("Location: profile.php");
            exit;
        }

        if ($new !== $confirm) {
            $_SESSION['flash_error'] = "Passwords do not match.";
            header("Location: profile.php");
            exit;
        }

        if (!$hasPassword) {
            $hash = password_hash($new, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hash, $user_id);
            $stmt->execute();

            $_SESSION['flash_success'] = "Password set successfully.";
            header("Location: profile.php");
            exit;
        }

        if (!password_verify($current, $user['password'])) {
            $_SESSION['flash_error'] = "Current password is incorrect.";
            header("Location: profile.php");
            exit;
        }

        $hash = password_hash($new, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hash, $user_id);
        $stmt->execute();

        session_destroy();
        header("Location: login.php?msg=" . urlencode("Password changed. Login again."));
        exit;
    }

    // ---------- DELETE ACCOUNT ----------
    if ($action === 'delete_account') {

        $email = trim($_POST['del_email'] ?? '');
        $password = $_POST['del_password'] ?? '';

        if ($email !== $user['email']) {
            $_SESSION['flash_error'] = "Email does not match.";
            header("Location: profile.php");
            exit;
        }

        if ($hasPassword && !password_verify($password, $user['password'])) {
            $_SESSION['flash_error'] = "Wrong password.";
            header("Location: profile.php");
            exit;
        }

        // delete watchlist
        $stmt = $conn->prepare("DELETE FROM watchlist WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        // delete user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        session_destroy();

        header("Location: ../index.php");
        exit;
    }

    header("Location: profile.php");
    exit;
}

// =========================
// FLASH
// =========================
$success = $_SESSION['flash_success'] ?? '';
$error = $_SESSION['flash_error'] ?? '';

unset($_SESSION['flash_success'], $_SESSION['flash_error']);

include '../includes/header.php';
?>

<div class="max-w-[1400px] mx-auto px-4 py-10 text-white">

    <h1 class="text-2xl md:text-3xl font-bold mb-6">Profile Settings</h1>

    <?php if ($success): ?>
        <script>alert("<?= htmlspecialchars($success) ?>");</script>
    <?php endif; ?>

    <?php if ($error): ?>
        <script>alert("<?= htmlspecialchars($error) ?>");</script>
    <?php endif; ?>

    <div class="grid md:grid-cols-2 gap-6">

        <!-- NAME -->
        <form method="POST" class="bg-white/5 border border-white/10 p-6 rounded-xl backdrop-blur">
            <h2 class="text-lg font-semibold mb-4">Account Info</h2>

            <div class="space-y-4">
                <div>
                    <label for="name" class="mb-1 text-white/60">Name:</label>
                    <input type="text" name="name"
                        value="<?= htmlspecialchars($user['name']) ?>"
                        class="w-full px-3 py-2 bg-black/30 border border-white/10 rounded">
                </div>

                <div>
                    <label for="email" class="mb-1 text-white/60">Email:</label>
                    <input type="email"
                        value="<?= htmlspecialchars($user['email']) ?>"
                        readonly
                        class="w-full px-3 py-2 bg-black/20 border border-white/10 rounded text-white/50">
                </div>
            </div>

            <input type="hidden" name="action" value="update_name">

            <button class="mt-5 px-6 py-2.5 bg-blue-500 hover:bg-blue-600 rounded-lg">
                Update Name
            </button>
        </form>

        <!-- PASSWORD -->
        <form method="POST" class="bg-white/5 border border-white/10 p-6 rounded-xl backdrop-blur">
            <h2 class="text-lg font-semibold mb-4">Password</h2>

            <div class="space-y-4">

                <?php if ($hasPassword): ?>
                    <input type="password" name="current_password" placeholder="Current Password"
                        class="w-full px-3 py-2 bg-black/30 border border-white/10 rounded">
                <?php endif; ?>

                <input type="password" name="new_password" placeholder="New Password"
                    class="w-full px-3 py-2 bg-black/30 border border-white/10 rounded">

                <input type="password" name="confirm_password" placeholder="Confirm Password"
                    class="w-full px-3 py-2 bg-black/30 border border-white/10 rounded">
            </div>

            <input type="hidden" name="action" value="update_password">

            <button class="mt-5 px-6 py-2.5 bg-blue-500 hover:bg-blue-600 rounded-lg">
                <?= $hasPassword ? 'Change Password' : 'Set Password' ?>
            </button>
        </form>

    </div>

    <!-- DELETE SECTION -->
    <div class="mt-10 bg-red-500/10 border border-red-500/30 p-6 rounded-xl">

        <h2 class="text-lg font-semibold text-red-400 mb-3">Delete Account</h2>
        <p class="text-white/60 mb-4">This action is permanent.</p>

        <button onclick="openModal('confirmModal')"
            class="px-6 py-2 bg-red-500 hover:bg-red-600 rounded-lg">
            Delete Account
        </button>

    </div>

</div>

<!-- MODAL 1 -->
<div id="confirmModal" class="modal fixed inset-0 bg-black/70 flex items-center justify-center z-50">

    <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-2xl w-full max-w-md">

        <h3 class="text-lg font-semibold mb-2">Delete Account</h3>
        <p class="text-white/60 mb-6">This action is permanent and cannot be undone.</p>

        <div class="flex justify-end gap-3">
            <button onclick="closeModal('confirmModal')"
                class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg">
                Cancel
            </button>

            <button onclick="nextModal()"
                class="px-4 py-2 bg-red-500 hover:bg-red-600 rounded-lg">
                Continue
            </button>
        </div>

    </div>

</div>

<!-- MODAL 2 -->
<div id="verifyModal" class="modal fixed inset-0 bg-black/70 flex items-center justify-center z-50">

    <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-2xl w-full max-w-md">

        <h3 class="text-lg font-semibold mb-4">Confirm Identity</h3>

        <form method="POST">

            <input type="hidden" name="action" value="delete_account">

            <input type="email" name="del_email" placeholder="Enter your email" required
                class="w-full mb-3 px-3 py-2 bg-black/30 border border-white/10 rounded-lg focus:outline-none focus:border-blue-500">

            <?php if ($hasPassword): ?>
                <input type="password" name="del_password" placeholder="Enter your password" required
                    class="w-full mb-4 px-3 py-2 bg-black/30 border border-white/10 rounded-lg focus:outline-none focus:border-blue-500">
            <?php endif; ?>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal('verifyModal')"
                    class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg">
                    Cancel
                </button>

                <button class="px-4 py-2 bg-red-500 hover:bg-red-600 rounded-lg">
                    Delete Account
                </button>
            </div>

        </form>

    </div>

</div>

<?php include '../includes/footer.php'; ?>

<style>
.modal {
    opacity: 0;
    pointer-events: none;
    transform: scale(0.95);
    transition: all 0.25s ease;
}
.modal.show {
    opacity: 1;
    pointer-events: auto;
    transform: scale(1);
}
</style>

<script>
function openModal(id){
    const el = document.getElementById(id);
    el.classList.add('show');
}

function closeModal(id){
    const el = document.getElementById(id);
    el.classList.remove('show');
}

function nextModal(){
    closeModal('confirmModal');
    setTimeout(() => {
        openModal('verifyModal');
    }, 150);
}
</script>


