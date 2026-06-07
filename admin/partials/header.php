<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="google-site-verification" content=""/>

    <title><?= $page_title ?></title>

    <!-- Tailwind -->
    <script src="../../assets/js/tw.js"></script>

    <!-- Fonts (optional but clean) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        button{
            cursor: pointer;
        }
    </style>

</head>

<body class="bg-[#070B1A] text-white">

<!-- TOP BAR -->
<header class="fixed top-0 left-64 right-0 h-16 z-30
               bg-black/50 backdrop-blur-xl
               border-b border-blue-500/10
               flex items-center justify-between px-4">

    <!-- LEFT: PAGE TITLE -->
    <div>
        <h1 class="text-lg font-semibold tracking-wide text-white/90">
            <?= $head_name ?>
        </h1>
    </div>

    <!-- RIGHT: ACTIONS -->
    <div class="flex items-center gap-5">

        <!-- USER -->
        <?php if (isset($_SESSION[SESSION_USER])): ?>
            <div class="text-sm text-white/70">
                <?= htmlspecialchars($_SESSION[SESSION_USER]['email']) ?>
            </div>

            <a href="../../auth/logout.php"
                class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-500/20 text-red-400 hover:bg-red-500/30 transition">
                Logout
            </a>
        <?php endif; ?>

    </div>

</header>
