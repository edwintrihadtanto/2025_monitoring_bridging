<div class="page-heading monitoring-theme connection-shell" id="koneksi-monitor">

    <div class="page-title mb-3">
        <div class="connection-toolbar d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    
                        <i class="bi bi-router-fill"></i>
                    <h4 class="connection-title mb-0">Cek Koneksi</h4>
                </div>
                <p class="connection-message mb-0" style="font-size: 0.85rem;">
                    Monitoring akses internet server dan koneksi BPJS
                </p>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="connection-meta">
                    <i class="bi bi-clock-history"></i>
                    <span id="koneksiCheckedAt"><?= esc($checked_at) ?></span>
                </span>
                <button type="button" class="btn btn-primary btn-sm" id="btnRefreshKoneksi">
                    <i class="bi bi-arrow-clockwise"></i>
                    <!-- <span>Cek Ulang</span> -->
                </button>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="connection-note d-flex flex-wrap align-items-center gap-2 mb-3">
            <span class="fw-semibold me-1">
                <i class="bi bi-stopwatch me-1"></i>Kategori durasi:
            </span>
            <span class="speed-chip speed-fast">Cepat <= 300 ms</span>
            <span class="speed-chip speed-normal">Normal 301-1000 ms</span>
            <span class="speed-chip speed-slow">Lambat 1001-3000 ms</span>
            <span class="speed-chip speed-critical">Sangat Lambat > 3000 ms</span>
        </div>

        <div class="row g-3" id="koneksiCards">
            <?php foreach ($checks as $check): ?>
                <?= view('dashboard/partials/koneksi_card', ['check' => $check]) ?>
            <?php endforeach; ?>
        </div>
    </section>
</div>
