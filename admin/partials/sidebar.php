<?php
$current = basename($_SERVER['PHP_SELF']);

require_once '../partials/guard.php';
require_once '../../config/config.php';
require_once '../../config/constants.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION[SESSION_USER] ?? null;
$is_admin = false;
$is_superadmin = false;

if ($user) {
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $dbUser = $res->fetch_assoc();
        if ($dbUser['role'] === 'superadmin') {
            $is_superadmin = true;
            $is_admin = true;
        }
        else if ($dbUser['role'] === 'admin') {
            $is_superadmin = false;
            $is_admin = true;
        }
        else{
            $is_superadmin = false;
            $is_admin = false;
        }
    }
}

?>

<aside class="fixed top-0 left-0 h-full w-64 z-40
    bg-black/60 backdrop-blur-xl
    border-r border-blue-500/10
    shadow-[0_0_30px_rgba(0,0,0,0.6)]
    flex flex-col">

    <!-- TOP -->
    <div class="px-6 flex items-center justify-start border-b border-blue-500/10 h-16">

        <a href="../"
           class="flex items-center gap-2 group">

            <img src="../../assets/admin-logo.svg"
                class="w-8 h-8 object-contain">

            <span class="text-lg font-bold text-blue-500 group-hover:drop-shadow-[0_0_6px_rgba(59,130,246,0.6)] transition">
                PureCinepix
            </span>

        </a>

    </div>

    <!-- MENU -->
    <nav class="flex-1 p-4 space-y-2">

        <a href="../"
           class="flex items-center gap-2 px-4 py-2 rounded-lg
           transition duration-300
           <?= $currentpage == 'index'
                ? 'bg-blue-500/10 text-blue-400 shadow-[0_0_10px_rgba(59,130,246,0.3)]'
                : 'hover:bg-white/5 hover:text-blue-400' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard-icon lucide-layout-dashboard"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
            <span>Dashboard</span>
        </a>

        <a href="../contents/"
           class="flex items-center gap-2 px-4 py-2 rounded-lg
           transition duration-300
           <?= $currentpage == 'contents'
                ? 'bg-blue-500/10 text-blue-400 shadow-[0_0_10px_rgba(59,130,246,0.3)]'
                : 'hover:bg-white/5 hover:text-blue-400' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clapperboard-icon lucide-clapperboard"><path d="m12.296 3.464 3.02 3.956"/><path d="M20.2 6 3 11l-.9-2.4c-.3-1.1.3-2.2 1.3-2.5l13.5-4c1.1-.3 2.2.3 2.5 1.3z"/><path d="M3 11h18v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="m6.18 5.276 3.1 3.899"/></svg>
            <span>Contents</span>
        </a>

        <a href="../categories/"
           class="flex items-center gap-2 px-4 py-2 rounded-lg
           transition duration-300
           <?= $currentpage == 'categories'
                ? 'bg-blue-500/10 text-blue-400 shadow-[0_0_10px_rgba(59,130,246,0.3)]'
                : 'hover:bg-white/5 hover:text-blue-400' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chart-bar-stacked-icon lucide-chart-bar-stacked"><path d="M11 13v4"/><path d="M15 5v4"/><path d="M3 3v16a2 2 0 0 0 2 2h16"/><rect x="7" y="13" width="9" height="4" rx="1"/><rect x="7" y="5" width="12" height="4" rx="1"/></svg>
            <span>Categories</span>
        </a>

        <a href="../genres/"
           class="flex items-center gap-3 px-4 py-2 rounded-lg
           transition duration-300
           <?= $currentpage == 'genres'
                ? 'bg-blue-500/10 text-blue-400 shadow-[0_0_10px_rgba(59,130,246,0.3)]'
                : 'hover:bg-white/5 hover:text-blue-400' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list-icon lucide-list"><path d="M3 5h.01"/><path d="M3 12h.01"/><path d="M3 19h.01"/><path d="M8 5h13"/><path d="M8 12h13"/><path d="M8 19h13"/></svg>
            <span>Genres</span>
        </a>

        <a href="../requests/"
           class="flex items-center gap-3 px-4 py-2 rounded-lg
           transition duration-300
           <?= $currentpage == 'requests'
                ? 'bg-blue-500/10 text-blue-400 shadow-[0_0_10px_rgba(59,130,246,0.3)]'
                : 'hover:bg-white/5 hover:text-blue-400' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-circle-question-mark-icon lucide-message-circle-question-mark"><path d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
            <span>Requests</span>
        </a>

        <a href="../users/"
           class="flex items-center gap-2 px-4 py-2 rounded-lg
           transition duration-300
           <?= $currentpage == 'users'
                ? 'bg-blue-500/10 text-blue-400 shadow-[0_0_10px_rgba(59,130,246,0.3)]'
                : 'hover:bg-white/5 hover:text-blue-400' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users-round-icon lucide-users-round"><path d="M18 21a8 8 0 0 0-16 0"/><circle cx="10" cy="8" r="5"/><path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3"/></svg>
            <span>Users</span>
        </a>

        <a href="../settings/"
           class="flex items-center gap-2 px-4 py-2 rounded-lg
           transition duration-300
           <?= $currentpage == 'settings'
                ? 'bg-blue-500/10 text-blue-400 shadow-[0_0_10px_rgba(59,130,246,0.3)]'
                : 'hover:bg-white/5 hover:text-blue-400' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings-icon lucide-settings"><path d="M9.671 4.136a2.34 2.34 0 0 1 4.659 0 2.34 2.34 0 0 0 3.319 1.915 2.34 2.34 0 0 1 2.33 4.033 2.34 2.34 0 0 0 0 3.831 2.34 2.34 0 0 1-2.33 4.033 2.34 2.34 0 0 0-3.319 1.915 2.34 2.34 0 0 1-4.659 0 2.34 2.34 0 0 0-3.32-1.915 2.34 2.34 0 0 1-2.33-4.033 2.34 2.34 0 0 0 0-3.831A2.34 2.34 0 0 1 6.35 6.051a2.34 2.34 0 0 0 3.319-1.915"/><circle cx="12" cy="12" r="3"/></svg>
            <span>Settings</span>
        </a>

        <br>

        <a href="../../" target="_blank"
        class="flex items-center gap-3 px-4 py-2 rounded-lg text-white/50
            hover:bg-white/5 hover:text-white transition">
            Back to Home
        </a>
        
        <a href="../../auth/logout.php"
        class="flex items-center gap-3 px-4 py-2 rounded-lg text-white/50
            hover:bg-red-500/5 hover:text-red-500 transition">
            Logout
        </a>


    </nav>

</aside>