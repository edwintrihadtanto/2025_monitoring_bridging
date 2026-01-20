<?php
if (!empty($obatList)): 
?>

    <div class="alert alert-info">
        <i class="bi bi-list-check me-2"></i>
        Ditemukan <strong><?= count($obatList) ?></strong> Obat.
    </div>

    <!-- GRID CARDS -->
    <div class="row">
        <?php foreach ($obatList as $item): ?>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <div class="card h-100 border-start border-4 border-primary shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-truncate" title="<?= $item['nama'] ?>">
                        <i class="bi bi-hospital text-primary me-2"></i>
                        <?= $item['nama'] ?>
                    </h6>
                    <p class="card-text mb-0 text-muted small">
                        Kode: <strong><?= $item['kode'] ?></strong>
                    </p>
                    <p class="card-text mb-0 text-muted small">
                        Harga: <strong><?= $item['harga'] ?></strong>
                    </p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

<?php else: ?>
    <div class="alert alert-warning">
        <h4 class="alert-heading">Data Obat Tidak Ditemukan</h4>
        <p>Silakan periksa kembali entrian anda.</p>
    </div>
<?php endif; ?>