<?php include './includes/config.php'; ?>
<?php include './includes/header.php'; ?>
<?php include './includes/navbar.php'; ?>

<?php
    $featured = $conn->query("
        SELECT movies.* FROM movies
        JOIN movie_categories ON movies.id = movie_categories.movie_id
        JOIN categories ON categories.id = movie_categories.category_id
        WHERE categories.name = 'Featured'
        ORDER BY movies.id DESC
        LIMIT 5
    ");

    $trending = $conn->query("
        SELECT movies.* FROM movies
        JOIN movie_categories ON movies.id = movie_categories.movie_id
        JOIN categories ON categories.id = movie_categories.category_id
        WHERE categories.name = 'Trending'
        ORDER BY movies.id DESC
        LIMIT 10
    ");
?>

<section class="mt-20 relative h-[70vh] w-full overflow-hidden">

    <div id="heroSlider" class="h-full w-full relative">

        <!-- LEFT BUTTON -->
        <button onclick="prevSlide()"
            class="absolute left-4 top-1/2 -translate-y-1/2 z-20 bg-black/50 hover:bg-black/80 md:p-4 p-2 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left-icon lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
        </button>

        <!-- RIGHT BUTTON -->
        <button onclick="nextSlide()"
            class="absolute right-4 top-1/2 -translate-y-1/2 z-20 bg-black/50 hover:bg-black/80 md:p-4 p-2 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right-icon lucide-chevron-right"><path d="m9 18 6-6-6-6"/></svg>
        </button>

        <?php $first = true; ?>

        <?php while($m = $featured->fetch_assoc()): ?>

            <a href="movie.php?id=<?= $m['id'] ?>"
               class="hero-slide absolute inset-0 block 
                    <?= $first ? 'opacity-100 z-10' : 'opacity-0 z-0' ?> 
                    transition duration-700 cursor-pointer">

                <!-- BACKDROP -->
                <img src="<?= $m['backdrop'] ?>"
                     class="w-full h-full object-cover">

                <!-- OVERLAY -->
                <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-transparent to-black/60"></div>

                <!-- CONTENT -->
                <div class="absolute inset-0 flex md:items-center items-end px-6 max-w-[1400px] mx-auto">

                    <div class="hero-text max-w-xl bg-black/60 p-6 rounded-xl backdrop-blur-sm space-y-4 pointer-events-none">

                        <h1 class="text-4xl md:text-5xl font-bold text-white">
                            <?= $m['title'] ?>
                        </h1>

                        <p class="text-white text-sm md:text-base">
                            <?= $m['description'] ?>
                        </p>

                    </div>

                </div>

            </a>

            <?php $first = false; ?>
        <?php endwhile; ?>

    </div>

</section>

<main class="pt-10 px-6 max-w-[1400px] mx-auto space-y-14">

    <section class="relative">

        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-semibold">Trending Now</h2>
            <a href="#" class="text-sm text-blue-400 hover:underline">View All</a>
        </div>

        <!-- Buttons -->
        <button onclick="scrollLeftFn('slider1')"
            class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-black/50 hover:bg-black/80 p-2 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left-icon lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
        </button>

        <button onclick="scrollRightFn('slider1')"
            class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-black/50 hover:bg-black/80 p-2 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right-icon lucide-chevron-right"><path d="m9 18 6-6-6-6"/></svg>
        </button>

        <!-- Slider -->
        <div id="slider1" class="flex gap-5 overflow-x-auto scroll-smooth pb-4 slider">

            <?php while($m = $trending->fetch_assoc()): ?>
                
                <div class="min-w-[220px]">
                    <?php include './includes/card.php'; ?>
                </div>

            <?php endwhile; ?>

        </div>

    </section>

</main>

<?php include './includes/footer.php'; ?>