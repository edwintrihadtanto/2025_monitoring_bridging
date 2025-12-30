<?php
 $group = 'group1'; 

if (!$pager instanceof \CodeIgniter\Pager\PagerRenderer) {
    die('Error: Variabel $pager bukan PagerRenderer.');
}

// --- 1. AMBIL DATA MATEMATIKA DARI URL (Bukan dari Object Pager) ---

// A. Ambil URL saat ini dan pecah parameternya
 $currentUri = parse_url($_SERVER['REQUEST_URI']);
 $params = [];
if (isset($currentUri['query'])) {
    parse_str($currentUri['query'], $params);
}

// B. Ambil Halaman Aktif dari URL (misal: 6)
 $currentPage = 1;
if (isset($params['page_' . $group])) {
    $currentPage = (int)$params['page_' . $group];
} else {
    // Fallback: Cari di array links jika URL bersih (page 1)
    foreach ($pager->links(null, $group) as $link) {
        if ($link['active']) {
            $currentPage = (int)$link['title'];
            break;
        }
    }
}

// C. Ambil Total Halaman dari tombol LAST (misal: 16)
// Kita gunakan getLast() HANYA untuk ambil URLnya, lalu kita ekstrak nomornya.
 $realTotalPages = $currentPage; // Fallback
 $lastUrl = $pager->getLast();
if ($lastUrl) {
    $lastParams = [];
    parse_str(parse_url($lastUrl, PHP_URL_QUERY), $lastParams);
    if (isset($lastParams['page_' . $group])) {
        $realTotalPages = (int)$lastParams['page_' . $group];
    }
}

// --- 2. FUNGSI BANTU UNTUK MEMBUAT URL SECARA MANUAL ---
// Fungsi ini mengambil URL sekarang, dan ganti nomor halamannya saja.
 $buildUrl = function($targetPage) use ($currentUri, $params, $group) {
    $newParams = $params;
    $newParams['page_' . $group] = $targetPage;
    
    // Gabungkan path dan query string baru
    return $currentUri['path'] . '?' . http_build_query($newParams);
};

// --- 3. SLICING ANGKA HALAMAN (Agar tidak 1-16) ---
 $allLinks = $pager->links(null, $group);
 $activeIndex = -1;
 $totalLinks = count($allLinks);

foreach ($allLinks as $i => $link) {
    if ($link['active']) {
        $activeIndex = $i;
        break;
    }
}

 $surroundCount = 2;
 $limitedLinks = [];

if ($activeIndex !== -1) {
    $start = max(0, $activeIndex - $surroundCount);
    $end   = min($totalLinks - 1, $activeIndex + $surroundCount);
    
    for ($i = $start; $i <= $end; $i++) {
        $limitedLinks[] = $allLinks[$i];
    }
} else {
    $limitedLinks = $allLinks;
}
?>

<!-- --- 4. HTML PAGINATION --- -->
<nav aria-label="Page navigation">
    <ul class="pagination">
        <!-- Tombol FIRST -->
        <?php if ($currentPage > 1) : ?>
            <li class="page-item">
                <!-- PAKAI $buildUrl(1) agar 100% pasti ke page 1 -->
                <a href="<?= $buildUrl(1) ?>" class="page-link" aria-label="First">
                    <span aria-hidden="true">&laquo;&laquo;</span>
                </a>
            </li>
        <?php endif ?>

        <!-- Tombol PREVIOUS -->
        <?php if ($currentPage > 1) : ?>
            <li class="page-item">
                <!-- PAKAI $buildUrl($currentPage - 1) agar 100% pasti mundur 1 -->
                <a href="<?= $buildUrl($currentPage - 1) ?>" class="page-link" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php endif ?>

        <!-- NOMOR HALAMAN -->
        <?php foreach ($limitedLinks as $link) : ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <!-- Nomor halaman tetap pakai asli dari $pager karena href-nya pasti benar -->
                <a href="<?= $link['uri'] ?>" class="page-link">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <!-- Tombol NEXT -->
        <?php if ($currentPage < $realTotalPages) : ?>
            <li class="page-item">
                <!-- PAKAI $buildUrl($currentPage + 1) agar 100% pasti maju 1 -->
                <a href="<?= $buildUrl($currentPage + 1) ?>" class="page-link" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php endif ?>

        <!-- Tombol LAST -->
        <?php if ($currentPage < $realTotalPages) : ?>
            <li class="page-item">
                <!-- PAKAI $buildUrl($realTotalPages) agar 100% pasti ke page 16 -->
                <a href="<?= $buildUrl($realTotalPages) ?>" class="page-link" aria-label="Last">
                    <span aria-hidden="true">&raquo;&raquo;</span>
                </a>
            </li>
        <?php endif ?>
    </ul>
</nav>