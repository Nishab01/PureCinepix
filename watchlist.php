<?php
session_start();
require_once './includes/config.php';

$user_id = $_SESSION['user_id'] ?? 0;

// FETCH WATCHLIST AGAIN
$result = $conn->query("
    SELECT movies.* FROM watchlist
    JOIN movies ON watchlist.movie_id = movies.id
    WHERE watchlist.user_id = $user_id
    ORDER BY id DESC
");

include './includes/header.php';
include './includes/navbar.php';
?>

<div class="mt-20 max-w-[1400px] mx-auto px-6">

    <h1 class="text-2xl font-bold mb-6">My Watchlist</h1>

    <?php if($result->num_rows > 0): ?>

        <div id="watchlist-grid">
        
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
                <?php
                $isWatchlistPage = true;
                while($m = $result->fetch_assoc()):
                    include './includes/card.php';
                endwhile;
                ?>
            </div>

        </div>

        <div id="empty-state"
            class="hidden flex flex-col items-center justify-center py-20 text-center">

            <h2 class="text-2xl font-semibold mb-2 text-white">
                Your Watchlist is Empty
            </h2>

            <p class="text-white/60 mb-6">
                Start adding movies you want to watch 🎬
            </p>

            <a href="./movies.php"
                class="bg-blue-500 hover:bg-blue-600 px-6 py-2 rounded-lg font-semibold transition">
                Browse Movies →
            </a>

        </div>

    <?php else: ?>

        <div class="flex flex-col items-center justify-center py-20 text-center">

            <div class="text-5xl mb-4">🎬</div>

            <h2 class="text-2xl font-semibold mb-2 text-white">
                Your Watchlist is Empty
            </h2>

            <p class="text-white/60 mb-6">
                Start adding movies you want to watch 🎬
            </p>

            <a href="./movies.php"
                class="bg-blue-500 hover:bg-blue-600 px-6 py-2 rounded-lg font-semibold transition">
                Browse Movies →
            </a>
        </div>

    <?php endif; ?>

</div>

<script>
    const removeButtons = document.querySelectorAll('.removeBtn');
    const grid = document.querySelector('#watchlist-grid .grid');
    const emptyState = document.getElementById('empty-state');

    removeButtons.forEach(btn => {
        btn.addEventListener('click', async (e) => {

            e.preventDefault();

            const movieId = btn.dataset.movieId;
            const card = btn.closest('.card-item');

            try {
                const res = await fetch('watchlist_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `movie_id=${movieId}&action=remove`
                });

                const data = await res.json();

                if (data.status === 'removed') {

                    showToast("Removed from Watchlist", "error");

                    if (card) {
                        card.classList.add('opacity-0', 'scale-90', 'translate-y-2');

                        setTimeout(() => {
                            card.remove();

                            // CHECK IF EMPTY
                            if (grid.children.length === 0) {
                                grid.style.display = 'none';
                                emptyState.classList.remove('hidden');
                            }

                        }, 200);
                    }
                }

            } catch (err) {
                console.error(err);
            }

        });
    });
</script>

<?php include './includes/footer.php'; ?>