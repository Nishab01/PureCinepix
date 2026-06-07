<?php
$currentpage = "users";
$head_name = "Manage Users";
$page_title = "Manage Users";

require_once '../partials/guard.php';
require_once '../../config/config.php';
require_once '../../config/constants.php';

$message = "";

// CURRENT ADMIN ID
$current_admin_id = $_SESSION[SESSION_USER]['id'] ?? 0;
$current_admin_email = $_SESSION[SESSION_USER]['email'] ?? "";

// HANDLE ACTIONS
if (isset($_GET['action'], $_GET['id'])) {

    $id = (int)$_GET['id'];

    // prevent self-action
    if ($id === $current_admin_id) {
        $message = "You cannot perform this action on yourself!";
    } else {

        $user = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();

        if ($user) {

            switch ($_GET['action']) {

                case 'reset':
                    $new_pass = password_hash($user['email'], PASSWORD_DEFAULT);
                    $conn->query("UPDATE users SET password='$new_pass' WHERE id=$id");
                    $message = "Password reset";
                    break;

                case 'toggle_role':
                    $new_role = $user['role'] === 'admin' ? 'user' : 'admin';
                    $conn->query("UPDATE users SET role='$new_role' WHERE id=$id");
                    $message = "User role updated!";
                    break;

                case 'ban':
                    $conn->query("UPDATE users SET is_banned=1 WHERE id=$id");
                    $message = "User banned!";
                    break;

                case 'unban':
                    $conn->query("UPDATE users SET is_banned=0 WHERE id=$id");
                    $message = "User unbanned!";
                    break;

                case 'delete':
                    $conn->query("DELETE FROM users WHERE id=$id");
                    $message = "User deleted!";
                    break;
            }
        }
    }
}

// SEARCH + FILTER
$search = $_GET['search'] ?? '';
$role_filter = $_GET['role'] ?? '';

$where = "WHERE 1";

if ($search) {
    $search = $conn->real_escape_string($search);
    $where .= " AND (name LIKE '%$search%' OR email LIKE '%$search%')";
}

if ($role_filter) {
    $role_filter = $conn->real_escape_string($role_filter);
    $where .= " AND role='$role_filter'";
}

// FETCH USERS
$users = $conn->query("SELECT * FROM users $where ORDER BY id DESC");

// COUNT
$total = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
?>

<?php include '../partials/sidebar.php'; ?>
<?php include '../partials/header.php'; ?>

<div class="ml-64 mt-16 p-6 space-y-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center">

        <div>
            <h2 class="text-2xl font-semibold text-white/90">Users</h2>
            <p class="text-sm text-white/50">Manage platform users</p>
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

    <!-- SEARCH + FILTER -->
    <div class="p-6 rounded-xl bg-white/5 border border-white/10 backdrop-blur-lg flex flex-wrap gap-4 items-center">

        <form method="GET" class="flex gap-4 flex-wrap">

            <input type="text" name="search" placeholder="Search name or email"
                value="<?= htmlspecialchars($search) ?>"
                class="px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-sm w-64">

            <select name="role"
                class="px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-sm">
                <option class="text-black" value="">All Roles</option>
                <option class="text-black" value="admin" <?= $role_filter === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option class="text-black" value="user" <?= $role_filter === 'user' ? 'selected' : '' ?>>User</option>
            </select>

            <button
                class="px-5 py-2 rounded-lg bg-blue-500 hover:bg-blue-600
                text-sm font-medium transition
                hover:shadow-[0_0_10px_rgba(59,130,246,0.5)]">
                Apply
            </button>

        </form>

    </div>

    <!-- USERS TABLE -->
    <div class="p-6 rounded-xl bg-white/5 border border-white/10 backdrop-blur-lg overflow-x-auto">

        <table class="w-full text-sm text-left text-white/80">

            <thead class="text-white/50 border-b border-white/10">
                <tr>
                    <th class="py-2">Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php while ($u = $users->fetch_assoc()): ?>
                    <tr class="border-b border-white/5 hover:bg-white/5">

                        <td class="py-3">
                            <?= $u['name'] ?>
                            <?php if ($u['email'] == $current_admin_email): ?>
                                <span class="text-white/50">(You)</span>
                            <?php endif ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($u['email']) ?>
                        </td>

                        <td>
                            <span class="<?= $u['role'] === 'superadmin' ? 'text-blue-400' : ($u['role'] === 'admin' ? 'text-purple-400' : 'text-white/60') ?>">
                                <?= ucfirst($u['role']) ?>
                            </span>
                        </td>

                        <td>
                            <?php if (!empty($u['is_banned'])): ?>
                                <span class="text-red-400">Banned</span>
                            <?php else: ?>
                                <span class="text-green-400">Active</span>
                            <?php endif; ?>
                        </td>

                        <td class="text-white/50 text-xs">
                            <?= date('d M Y', strtotime($u['created_at'])) ?>
                        </td>

                        <td class="flex flex-wrap gap-2 py-2">

                            <?php if ($u['email'] !== $current_admin_email): ?>

                                <!-- RESET -->
                                <a href="?action=reset&id=<?= $u['id'] ?>"
                                    class="px-3 py-1 rounded-md bg-yellow-500/20 text-yellow-300
                                    hover:bg-yellow-500/40 hover:text-white transition text-xs">
                                    Reset Password
                                </a>

                                <!-- ROLE -->
                                <?php if($u['role'] !== 'superadmin'): ?>
                                    <a href="?action=toggle_role&id=<?= $u['id'] ?>"
                                        class="px-3 py-1 rounded-md bg-purple-500/20 text-purple-300
                                        hover:bg-purple-500/40 hover:text-white transition text-xs">
                                        <?php
                                            if ($u['role'] == "user"){
                                                echo "Change to Admin";
                                            }
                                            if ($u['role'] == "admin"){
                                                echo "Change to User";
                                            }
                                        ?> 
                                    </a>
                                    

                                    <!-- BAN / UNBAN -->
                                    <?php if (!empty($u['is_banned'])): ?>
                                        <a href="?action=unban&id=<?= $u['id'] ?>"
                                            class="px-3 py-1 rounded-md bg-green-500/20 text-green-300
                                            hover:bg-green-500/40 hover:text-white transition text-xs">
                                            Unban User
                                        </a>
                                    <?php else: ?>
                                        <a href="?action=ban&id=<?= $u['id'] ?>"
                                            class="px-3 py-1 rounded-md bg-red-500/20 text-red-300
                                            hover:bg-red-500/40 hover:text-white transition text-xs">
                                            Ban User
                                        </a>
                                    <?php endif; ?>

                                    <!-- DELETE -->
                                    <a href="?action=delete&id=<?= $u['id'] ?>"
                                        onclick="return confirm('Delete this user?')"
                                        class="px-3 py-1 rounded-md bg-red-600/20 text-red-400
                                        hover:bg-red-600/40 hover:text-white transition text-xs">
                                        Delete User
                                    </a>

                                <?php endif ?>

                            <?php else: ?>
                                <a href="?action=reset&id=<?= $u['id'] ?>"
                                    class="px-3 py-1 rounded-md bg-yellow-500/20 text-yellow-300
                                    hover:bg-yellow-500/40 hover:text-white transition text-xs">
                                    Reset Password
                                </a>

                                <!-- <span class="text-white/30">You</span> -->
                            <?php endif; ?>

                        </td>

                    </tr>
                <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>