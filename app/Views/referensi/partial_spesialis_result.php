<?php
if (!empty($spesialisList)): 
?>

    <div class="alert alert-success">
        <i class="bi bi-check-all me-2"></i>
        Ditemukan <strong><?= count($spesialisList) ?></strong> Obat.
    </div>

    <!-- GRID CARDS -->
    <div class="row">
        <?php foreach ($spesialisList as $item): ?>
        <div class="col-12 col-md-6 col-lg-3 mb-3">
            <div class="card h-100 border-start border-4 border-success shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-truncate" title="<?= $item['nama'] ?>">
                        <i class="bi bi-bookmark-check text-primary me-2"></i>
                        <?= $item['nama'] ?>
                    </h6>
                    <p class="card-text mb-0 text-muted small">
                        Kode: <strong><?= $item['kode'] ?></strong>
                    </p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

<?php else: ?>
    <div class="alert alert-warning">
        <h4 class="alert-heading">Data Spesialis Tidak Ditemukan</h4>
        <p>Silakan periksa kembali..</p>
    </div>
<?php endif; ?>