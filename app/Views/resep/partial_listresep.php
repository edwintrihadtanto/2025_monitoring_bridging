<?php if (!empty($resepList)): ?>

<div class="alert alert-success py-2 small">
    <i class="bi bi-check-circle"></i> Data ditemukan: <?= count($resepList) ?> resep
</div>

<div class="row g-1">

    <?php foreach ($resepList as $index => $item): ?>
    <div class="col-12 col-md-3 card-listresep" data-noapotik="<?= $item['NOAPOTIK'] ?>">

        <div class="card resep-card h-90">

            <!-- HEADER -->
            <div class="card-body p-3">

                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="fw-bold text-info">
                            <?= $item['NAMA'] ?>
                        </div>
                        <small class="text-muted"><?= $item['NOKARTU'] ?></small>
                    </div>

                    <span class="badge <?= $item['BYVERRSP'] === '0' ? 'bg-warning' : 'bg-success' ?>">
                        <?= $item['BYVERRSP'] === '0' ? 'Pending' : 'Verified' ?>
                    </span>
                </div>

                <!-- INFO UTAMA -->
                <div class="small mb-2">
                    <div><strong>No Resep:</strong> <span class="text-danger"><?= $item['NORESEP'] ?></span></div>
                    <div><strong>No SEP:</strong> <?= $item['NOSEP_KUNJUNGAN'] ?></div>
                    <div><strong>Tgl Resep:</strong> <?= $item['TGLRESEP'] ?></div>
                </div>

                <!-- ACTION -->
                <div class="d-flex justify-content-between align-items-center">

                    <button class="btn btn-sm btn-outline-info"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse<?= $index ?>">
                        <i class="bi bi-eye"></i>
                    </button>

                    <form id="DeleteResepForm"  action="<?= site_url('res/del_hapusresep') ?>" method="post">
                        <?= csrf_field() ?>

                        <input type="hidden" name="no_resep" value="<?= esc($item['NORESEP']) ?>">
                        <input type="hidden" name="no_apotik" value="<?= esc($item['NOAPOTIK']) ?>">
                        <input type="hidden" name="refasalsjp" value="<?= esc($item['NOSEP_KUNJUNGAN']) ?>">
                        <input type="hidden" name="byverrsp" value="<?= esc($item['BYVERRSP']) ?>">

                        <input type="hidden" name="tgl_awal" value="<?= esc($item['TGLRESEP']) ?>">
                        <input type="hidden" name="tgl_akhr" value="<?= esc($item['TGLRESEP']) ?>">
                        <input type="hidden" name="jns_obat" value="<?= esc($item['KDJNSOBAT']) ?>">

                        <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip"title="Hapus Resep BPJS">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>

            </div>

            <!-- DETAIL -->
            <div class="collapse border-top" id="collapse<?= $index ?>">
                <div class="card-body p-3 small">

                    <div class="mb-1"><strong>Tgl Entry:</strong> <?= $item['TGLENTRY'] ?></div>
                    <div class="mb-1"><strong>Tgl Pelayanan:</strong> <?= $item['TGLPELRSP'] ?></div>
                    <div class="mb-1"><strong>Biaya:</strong> Rp <?= number_format($item['BYTAGRSP'], 0, ',', '.') ?></div>

                    <div class="mb-1">
                        <strong>Jenis Obat:</strong>
                        <?= $item['KDJNSOBAT'] == '1' ? 'PRB' : ($item['KDJNSOBAT'] == '2' ? 'Kronis' : 'Kemoterapi') ?>
                    </div>

                    <div class="mb-1"><strong>Faskes:</strong> <?= $item['FASKESASAL'] ?></div>

                    <div>
                        <strong>Iterasi:</strong>
                        <?= $item['FLAGITER'] === 'False' ? 'Tidak' : 'Ya' ?>
                    </div>

                </div>
            </div>

        </div>

    </div>
    <?php endforeach; ?>

</div>

<?php else: ?>

<div class="alert alert-warning small">
    <i class="bi bi-exclamation-circle"></i> Data tidak ditemukan
</div>

<?php endif; ?>