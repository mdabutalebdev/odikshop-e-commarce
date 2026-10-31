<?php
/*
|--------------------------------------------------------------------------
| storage:link — one-time helper (no terminal needed)
|--------------------------------------------------------------------------
| 1. Upload this file into:  odhikshop.shop/public/
| 2. Open in browser:        https://odhikshop.shop/storage-link.php
| 3. DELETE this file when it says DONE.
*/

header('Content-Type: text/plain; charset=utf-8');

$public = __DIR__;                                     // .../odhikshop.shop/public
$root   = dirname(__DIR__);                            // .../odhikshop.shop
$target = $root . '/storage/app/public';               // where Laravel saves uploads
$link   = $public . '/storage';                        // what the browser asks for

echo "=== Odik Shop storage link ===\n\n";
echo "Project : {$root}\n";
echo "Target  : {$target}\n";
echo "Link    : {$link}\n\n";

// --- 1. Is the upload folder there at all? ---------------------------------
if (! is_dir($target)) {
    echo "[X] PROBLEM: upload folder pawa jai nai.\n";
    echo "    File Manager e {$target} folder ta banan, tarpor abar ei page refresh korun.\n";
    exit(1);
}

$count = 0;
foreach (['products', 'categories', 'banners', 'brands', 'testimonials', 'editor'] as $sub) {
    if (is_dir("{$target}/{$sub}")) {
        $count += max(0, count(scandir("{$target}/{$sub}")) - 2);
    }
}
echo "[OK] Upload folder ache. Chobi paowa gelo: {$count} ta\n\n";

// --- 2. Clear out a broken or wrong link -----------------------------------
if (is_link($link)) {
    $points = readlink($link);
    if (realpath($points) === realpath($target)) {
        echo "[OK] Link aged theke thik ache -> {$points}\n\n";
        echo "=== DONE ===\n";
        echo "Chobi ekhono na ashle eta link er somossa noy — jananen.\n";
        echo "Ei file ta ekhon DELETE kore din.\n";
        exit;
    }

    echo "[!] Link bhul jaygay tak kore ache -> {$points}\n";
    echo "    Purono link ta muche notun kore banacchi...\n";
    @unlink($link);
} elseif (is_dir($link)) {
    echo "[X] PROBLEM: public/storage ekta ashol folder, symlink noy.\n";
    echo "    File Manager e {$link} folder ta rename kore storage-old korun,\n";
    echo "    tarpor ei page abar refresh korun.\n";
    exit(1);
} elseif (file_exists($link)) {
    @unlink($link);
}

// --- 3. Make the link ------------------------------------------------------
if (@symlink($target, $link)) {
    echo "[OK] Symlink toiri hoyeche!\n";
    echo "     {$link}\n       -> {$target}\n\n";
    echo "=== DONE ===\n";
    echo "Ekhon site e giye Ctrl+Shift+R din — chobi chole ashar kotha.\n";
    echo "Tarpor EI FILE TA (storage-link.php) DELETE kore din.\n";
    exit;
}

// --- 4. Symlink blocked by the host ----------------------------------------
echo "[X] symlink() kaj koreni — host e eta bondho thakte pare.\n\n";
echo "Ki korben:\n";
echo "  1. Hosting support ke bolun: 'please run: ln -s {$target} {$link}'\n";
echo "  2. Othoba cPanel -> Cron Jobs e ekbar ei command chalan:\n";
echo "     cd {$root} && php artisan storage:link\n";
echo "     (kaj sesh hole cron ta delete kore diben)\n";
exit(1);
