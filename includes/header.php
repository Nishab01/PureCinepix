<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <!-- MOBILE VIEW -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- TITLE (dynamic later) -->
    <title><?= isset($pagename) ? $pagename : 'PureCinepix' ?></title>

    <!-- FAVICON -->
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/favicon-32.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/favicon-16.ico">

    <!-- TAILWIND -->
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> -->
    <script src="../assets/js/tw.js"></script>

    <!-- GOOGLE FONT (optional but recommended) -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>  
        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }
        *{
            scroll-behavior: smooth;
            transition-property: all;
            transition-duration: 300ms; 
        }

        /* Hide scrollbar (for sliders) */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;  /* IE/Edge */
            scrollbar-width: none;     /* Firefox */
        }

        /* ✅ PREMIUM SCROLLBAR (GENRES ONLY) */
        .custom-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: rgba(59,130,246,0.4);
            border-radius: 10px;
        }

        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(59,130,246,0.7);
        }

        /* Firefox */
        .custom-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(59,130,246,0.4) transparent;
        }
        .hero-bg {
            background-size: cover;
            background-position: center;
            transition: opacity 0.8s ease;
        }

        .hero-trailer iframe {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 177.78vh; /* 16:9 ratio trick */
            height: 100vh;
            min-width: 100%;
            min-height: 56.25vw;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }

        /* card appearing animations */
        @keyframes cardReveal {
            0% {
                opacity: 0;
                transform: translateY(25px) scale(0.96);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes fadeSlideUp {
            0% {
                opacity: 0;
                transform: translateY(20px) scale(0.96);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .card-animate {
            animation: fadeSlideUp 0.4s ease forwards;
        }
        .card-remove {
            opacity: 0;
            transform: scale(0.85) translateY(10px);
            transition: all 0.25s ease;
        }
        /* INITIAL STATE */
        .card-hidden {
            opacity: 0;
            transform: translateY(20px) scale(0.96);
        }

        /* FINAL STATE */
        .card-show {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        /* SMOOTH TRANSITION */
        .animate-item {
            transition: all 0.6s cubic-bezier(.22,1,.36,1);
        }
    </style>

</head>

<body class="bg-black text-white">

<?php include __DIR__ . '/navbar.php'; ?>

<!-- MAIN WRAPPER -->
<main class="py-20">

<script>
    document.addEventListener('click', async (e) => {

        let btn = e.target;

        while (btn && !btn.classList.contains('removeBtn')) {
            btn = btn.parentElement;
        }

        if (!btn) return;

        e.preventDefault();

        const id = btn.dataset.id;
        // const card = btn.closest('.card-item');
        const card = btn.closest('a');

        try {
            const res = await fetch('../api/watchlist_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `content_id=${id}&action=remove`
            });

            const data = await res.json();

            if (data.status === 'removed') {

                if (card) {
                    card.style.transition = "all 0.25s ease";
                    card.style.opacity = "0";
                    card.style.transform = "scale(0.9)";

                    setTimeout(() => {
                        card.remove();

                        const grid = document.querySelector('#watchlist-grid .grid');
                        const emptyState = document.getElementById('empty-state');

                        if (grid && grid.children.length === 0) {
                            grid.style.display = 'none';
                            emptyState?.classList.remove('hidden');
                        }

                    }, 200);
                }
            }

        } catch (err) {
            console.error(err);
        }

    });

    function animateCards(container = document) {

        const cards = container.querySelectorAll('.animate-item');

        cards.forEach((card, index) => {

            // prevent re-animation
            if (card.classList.contains('animated')) return;

            const type = card.dataset.anim || 'poster';

            let delay = index * 20;

            // 🎬 DIFFERENT SPEEDS
            if (type === 'backdrop') {
                delay = index * 50;
            }

            setTimeout(() => {
                card.classList.remove('card-hidden');
                card.classList.add('card-show');
                card.classList.add('animated'); // mark as done
            }, delay);

        });
    }

    /* RUN ON PAGE LOAD */
    document.addEventListener("DOMContentLoaded", () => {
        animateCards();
    });
</script>
