<?php
// expected: $item

$title = htmlspecialchars($item['title']);
$poster1 = $item['poster1'] ?? '';
$poster2 = $item['poster2'] ?? '';
$poster3 = $item['poster3'] ?? '';

$dateRaw = $item['release_date'] ?? '';
$releasedate = $dateRaw ? date('j M Y', strtotime($dateRaw)) : '';

$dateRaw2 = $item['last_date'] ?? '';
$lastdate = $dateRaw2 ? date('j M Y', strtotime($dateRaw2)) : '';

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
    class="<?=
        isset($isGrid) && $isGrid
        ? 'w-full snap-x snap-proximity'
        : (isset($forwatchlist) && $forwatchlist
            ? 'w-full snap-x snap-proximity'
            : 'w-[40%] md:w-[15.67%] flex-shrink-0 snap-x snap-proximity')
        ?> group block rounded-xl overflow-hidden hover:-translate-y-2 transition duration-300 border border-white/5"
        title="<?= $title ?>"
        >

    <div class="card-item w-full card-hidden animate-item relative group bg-white/10 hover:bg-white/5 rounded-xl overflow-hidden" 
        title="<?= $title ?>"
        data-anim="poster">

        <div class="aspect-[2/3] w-full overflow-hidden">
            <img src="<?= $poster1 ?>"
                class="w-full h-auto object-cover transition duration-300 group-hover:scale-[1.05]"
                alt="<?= $title ?>" title="<?= $title ?>">
        </div>

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

        <?php if(isset($forwatchlist) && $forwatchlist): ?>
            <button
                class="removeBtn absolute top-1 right-1 z-20
                w-6 h-6 flex items-center justify-center
                rounded-full bg-black/60 hover:text-red-500
                text-white font-bold text-xs
                md:opacity-0 opacity-100 group-hover:opacity-100
                transition duration-300"
                data-id="<?= $item['id'] ?>">
                ✕
            </button>
        <?php endif; ?>

    </div>

</a>
