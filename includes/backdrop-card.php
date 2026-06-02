<?php
$title = htmlspecialchars($item['title']);
$backdrop1 = $item['backdrop1'] ?? '';
$backdrop2 = $item['backdrop2'] ?? '';
$backdrop3 = $item['backdrop3'] ?? '';

$dateRaw = $item['release_date'] ?? '';
$releasedate = $dateRaw ? date('j M Y', strtotime($dateRaw)) : '';

$dateRaw2 = $item['last_date'] ?? '';
$lastdate = $dateRaw ? date('j M Y', strtotime($dateRaw2)) : '';

$type = $item['type'] ?? '';
$ctype = $type == 'tv' ? 'TV Show' : 'Movie';

$rating = $item['tmdb_rating'] ?? null;

if($type == 'movie'){
    $date = $releasedate;
}
if($type == 'tv'){
    $date = $releasedate." - ".$lastdate;
}
?>

<a href="../pages/content.php?slug=<?= $item['id'] ?>&title=<?= urlencode($title) ?>"
    class="w-[80%] md:w-[30%] flex-shrink-0 group block rounded-xl overflow-hidden hover:-translate-y-2 transition duration-300 border border-white/5"
    title="<?= $title ?>">

    <div class="card-item card-hidden animate-item bg-white/10 hover:bg-white/5 rounded-xl"
        title="<?= $title ?>"
        data-anim="backdrop">

        <!-- IMAGE -->
        <div class="h-32 sm:h-36 md:h-44 lg:h-48 xl:h-52 w-full overflow-hidden">
            <img src="<?= $backdrop1 ?>"
                class="w-full h-full object-cover transition duration-300 group-hover:scale-[1.05]"
                alt="<?= $title ?>" title="<?= $title ?>">
        </div>

        <!-- TEXT -->
        <div class="px-1 md:px-3 py-2 space-y-1">
            <h3 class="text-[12px] md:text-sm font-semibold text-white line-clamp-1 group-hover:text-blue-400 transition">
                <?= $title ?>
            </h3>

            <div class="text-[10px] md:text-xs text-white/60 line-clamp-1 flex flex-row gap-1">
                <div class="bg-white/10 px-1 md:px-2 py-1 rounded-md">
                    <?= $ctype ?>
                </div>
                
                <div class="bg-white/10 px-1 md:px-2 py-1 rounded-md">
                    <span class="text-yellow-400">★</span>
                    <?= $rating ?>
                </div>
            </div>

            <div class="text-[10px] md:text-xs text-white/60 line-clamp-1">
                <?= $date ?>
            </div>
        </div>

    </div>

</a>
