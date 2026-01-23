<?php if (!empty($dataList)): ?>
    <div class="alert alert-success mb-2">
        <h6 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Data Pelayanan Obat Ditemukan</h6>
    </div>

    <div class="row">
        <?php foreach ($dataList as $index => $item): ?>
            <div class="col-12 col-md-4">
                <div class="card mb-3">
                    <div class="card-header p-3 border-start border-4 border-success shadow-sm">
                        <!-- Informasi dasar yang selalu terlihat -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>No. Resep : </strong><strong class="text-danger"><?= $item['noresep'] ?></strong><br>
                                <strong>No. SEP : </strong> <?= $item['noSepAsal'] ?><br>
                                <strong>Nama Pasien : </strong><strong class="text-primary"> <?= $item['nmpst'] ?></strong>
                            </div>
                            <!-- Tombol collapse -->
                            <button class="btn btn-link btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDetails<?= $index ?>" aria-expanded="false" aria-controls="collapseDetails<?= $index ?>">
                                <i class="bi bi-chevron-down"></i> Detail
                            </button>
                            <button class="btn btn-danger btn-sm" type="button">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </div>
                    </div>

                    <!-- Bagian Collapse untuk menampilkan detail -->
                    <div class="collapse" id="collapseDetails<?= $index ?>">
                        <div class="card-body p-3 border-start border-bottom border-4 border-success shadow-sm">
                            <div class="mb-2">
                                <strong>Jenis Obat:</strong>
                                <span><?= $item['nmjnsobat'] ?></span>
                            </div>
                            <div class="mb-2">
                                <strong>Tgl. Pelayanan Resep:</strong>
                                <span><?= $item['tglpelayanan'] ?></span>
                            </div>
                            <div class="mb-2">
                                <strong>Nama Obat:</strong>
                                <span><?= $item['listobat']['kodeobat']." / ".$item['listobat']['nmjnsobat'] ?></span>
                            </div>
                            <div class="mb-2">
                                <strong>Penggunaan:</strong>
                                <span><?= $item['listobat']['hari' ?> Hari</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <!-- Tutup Row -->
<?php else: ?>
    <div class="alert alert-warning">
        <h4 class="alert-heading">Data Pelayanan Obat Tidak Ditemukan</h4>
        <p>Silakan periksa kembali Nomor SEP yang Anda masukkan.</p>
    </div>
<?php endif; ?>
