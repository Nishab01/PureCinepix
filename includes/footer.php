</main>

<footer class="mt-10 border-t border-white/10 bg-black/70 backdrop-blur-lg">

    <div class="max-w-[1400px] mx-auto px-4 py-10 grid grid-cols-4 gap-8">

        <!-- LOGO & DESCRIPTION -->
        <div class="md:col-span-2 col-span-4">
            <a href="../" class="flex items-center gap-2 text-xl font-bold mb-3">
                <img src="../assets/logo.svg" class="h-8">
                <span class="text-lg md:text-xl font-semibold tracking-wide">
                    Pure<span class="text-blue-500">Cinepix</span>
                </span>
            </a>
            

            <p class="text-justify text-sm text-white/60 leading-relaxed">
                Download your favorite movies and TV shows in high quality. Step into a world of stories. Discover trending movies, explore hidden gems, and build your perfect watchlist — all in one place. Track what you love, discover what’s next, and make every watch count with PureCinepix.
            </p>
        </div>

        <!-- QUICK LINKS -->
        <div class="md:col-span-1 col-span-4">
            <h3 class="text-sm font-semibold mb-3 text-white/80">Quick Links</h3>

            <div class="flex flex-col gap-2 text-sm text-white/60">

                <a href="../" class="hover:text-blue-400 transition">
                    Home
                </a>

                <a href="../pages/browse.php?type=movie" class="hover:text-blue-400 transition">
                    Movies
                </a>

                <a href="../pages/browse.php?type=tv" class="hover:text-blue-400 transition">
                    TV Shows
                </a>
                
            </div>
        </div>

        <!-- USER LINKS -->
        <div class="md:col-span-1 col-span-4">
            <h3 class="text-sm font-semibold mb-3 text-white/80">Account</h3>

            <div class="flex flex-col gap-2 text-sm text-white/60">

                <?php if ($user): ?>
                    <a href="../pages/watchlist.php" class="hover:text-blue-400 transition">
                        Watchlist
                    </a>
                <?php endif; ?>

                <a href="../auth/login.php" class="hover:text-blue-400 transition">
                    Login
                </a>

                <a href="../auth/register.php" class="hover:text-blue-400 transition">
                    Register
                </a>

            </div>
        </div>

    </div>

    <!-- BOTTOM -->
    <div class="border-t border-white/10 text-center py-4 text-sm text-white/50">
        © <?= date('Y') ?> PureCinepix. All rights reserved.
    </div>

</footer>

</body>
</html>