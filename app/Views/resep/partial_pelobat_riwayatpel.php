<?php
 $peserta = $data['response']['list'] ?? null;
 $history = $peserta['history'] ?? [];
?>

<?php if (!$peserta || empty($history)): ?>
    <div class="alert alert-warning">
        <i class="bi bi-info-circle me-1"></i>
        Data Riwayat Pelayanan Obat Kosong
    </div>
    <?php return; ?>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-body py-2">
        <div class="row">
            <div class="col-md-4">
                <small class="text-muted">No Kartu</small>
                <div class="fw-semibold"><?= esc($peserta['nokartu']) ?></div>
            </div>
            <div class="col-md-4">
                <small class="text-muted">Nama Peserta</small>
                <div class="fw-semibold"><?= esc($peserta['namapeserta']) ?></div>
            </div>
            <div class="col-md-4">
                <small class="text-muted">Tanggal Lahir</small>
                <div class="fw-semibold">
                    <?= date('d-m-Y', strtotime($peserta['tgllhr'])) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
 $grouped = [];
foreach ($history as $row) {
    $grouped[$row['tglpelayanan']][] = $row;
}
?>

<?php foreach ($grouped as $tgl => $items): ?>
    <div class="card mb-2">

        <div class="card-header py-2 bg-light">
            <i class="bi bi-calendar-event me-1"></i>
            <?= date('d M Y', strtotime($tgl)) ?>
            <span class="badge bg-secondary ms-2">
                <?= count($items) ?> obat
            </span>
        </div>

        <div class="card-body py-2">

            <?php foreach ($items as $obat): ?>
                
                <?php
                    $no_resep   = $obat['noresep'] ?? '';
                    $no_apotik  = $obat['nosjp'] ?? ''; 
                ?>

                <form action="<?= base_url('del_itemobat') ?>" 
                      method="POST" 
                      class="d-flex align-items-stretch gap-1 mb-2"
                      onsubmit="return window.handleDeleteItemObatSubmit(event, this)">

                    <?= csrf_field() ?>
                    <input type="hidden" name="no_resep" value="<?= esc($no_resep) ?>">
                    <input type="hidden" name="no_apotik" value="<?= esc($no_apotik) ?>">
                    <input type="hidden" name="tgl_awal" value="<?= date('Y-m-d', strtotime($tgl)) ?>">
                    <input type="hidden" name="tgl_akhr" value="<?= date('Y-m-d', strtotime($tgl)) ?>">
                    <input type="hidden" name="jns_obat" value="0"> 

                    <!-- Konten Obat (Dipindahkan ke dalam flex item flex-grow-1) -->
                    <div class="border rounded p-2 small flex-grow-1">
                        
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="fw-semibold text-primary">
                                    <?= esc($obat['namaobat']) ?>
                                </div>
                                <div class="text-muted">
                                    Kode: <?= esc($obat['kodeobat']) ?>
                                </div>
                            </div>

                            <div class="text-end">
                                <span class="badge bg-info">
                                    <?= rtrim($obat['jmlobat']) ?>
                                </span>
                            </div>
                        </div>

                        <hr class="my-1">

                        <div class="d-flex justify-content-between text-muted">
                            <div>
                                <i class="bi bi-receipt me-1"></i>
                                Resep: <?= esc($obat['noresep']) ?>
                            </div>
                            <div>
                                <i class="bi bi-file-earmark-text me-1"></i>
                                SEP: <?= esc($obat['nosjp']) ?>
                            </div>
                        </div>

                    </div>

                    <!-- Tombol Hapus (Di luar div border, sebelah kanan) -->
                    <div class="d-flex align-items-center">
                        <button type="submit" class="btn btn-outline-danger btn-sm h-100 d-flex align-items-center" title="Hapus Resep">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>

                </form>

            <?php endforeach; ?>

        </div>
    </div>
<?php endforeach; ?>