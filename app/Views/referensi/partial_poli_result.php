<?php
if (!empty($poliList)): 
    $data = $poliList;
?>

    <div class="alert alert-success">
        <i class="bi bi-check-all me-2"></i>
        Ditemukan <strong><?= count($data) ?></strong> Daftar Obat Poli.
    </div>

    <div class="row">
        <?php foreach ($data as $item): ?>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-start border-4 border-success shadow-sm">
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
        <h4 class="alert-heading">Data Tidak Ditemukan</h4>
        <p>Silakan periksa kembali Kode / Nama Poli.</p>
    </div>
<?php endif; ?>