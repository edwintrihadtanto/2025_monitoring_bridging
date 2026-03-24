<?php if (!empty($resepList)): ?>
    <div id="pesan-deletedaftarresep">
        <div class="alert alert-success mb-2">
            <h6 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Data Resep Ditemukan</h6>
        </div>
    </div>

    <!-- Grid 3 Kolom (Responsive: 1 kolom di HP, 3 kolom di PC) -->
    <div class="row">
        <?php foreach ($resepList as $index => $item): ?>
            <div class="col-12 col-md-4 card-listresep" style="font-size: 15px;" data-noapotik="<?= $item['NOAPOTIK'] ?>">
                <div class="card mb-3">
                    <div class="card-header p-2 border-start border-4 border-success shadow-sm">
                        <!-- Informasi dasar yang selalu terlihat -->
                        <div class="col-12">
                            <strong class="text-primary"><?= $item['NAMA'] ?></strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>No. Resep : </strong><strong class="text-danger"><?= $item['NORESEP'] ?></strong><br>
                                <strong>No. Apotik : </strong><strong class="text-danger"><?= $item['NOAPOTIK'] ?></strong><br>
                                <strong>No. SEP : </strong> <?= $item['NOSEP_KUNJUNGAN'] ?>
                            </div>
                            <!-- Tombol collapse -->
                            <button class="btn btn-link btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDetails<?= $index ?>" aria-expanded="false" aria-controls="collapseDetails<?= $index ?>">
                                <i class="bi bi-chevron-down"></i> Detail
                            </button>

                            <form id="DeleteResepForm" class="form-delete-resep" action="<?= site_url('res/del_hapusresep') ?>" method="post" >                                
                                <?= csrf_field() ?>
                                <input type="hidden" name="no_resep" value="<?= esc($item['NORESEP']) ?>">
                                <input type="hidden" name="no_apotik" value="<?= esc($item['NOAPOTIK']) ?>">
                                <input type="hidden" name="refasalsjp" value="<?= esc($item['NOSEP_KUNJUNGAN']) ?>">
                                <input type="hidden" name="byverrsp" value="<?= esc($item['BYVERRSP']) ?>">

                                <input type="hidden" name="tgl_awal" value="<?= esc($item['TGLRESEP']) ?>">
                                <input type="hidden" name="tgl_akhr" value="<?= esc($item['TGLRESEP']) ?>">
                                <input type="hidden" name="jns_obat" value="<?= esc($item['KDJNSOBAT']) ?>">

                                <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip"
                                    title="Hapus Resep BPJS">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Bagian Collapse untuk menampilkan detail -->
                    <div class="collapse" id="collapseDetails<?= $index ?>">
                        <div class="card-body p-3 border-start border-bottom border-4 border-success shadow-sm">
                            <div class="mb-2">
                                <strong>No. BPJS:</strong>
                                <span><?= $item['NOKARTU'] ?></span>
                            </div>
                            <div class="mb-2">
                                <strong>Tgl. Entry:</strong>
                                <span><?= $item['TGLENTRY'] ?></span>
                            </div>
                            <div class="mb-2">
                                <strong>Tgl. Resep:</strong>
                                <span><?= $item['TGLRESEP'] ?></span>
                            </div>
                            <div class="mb-2">
                                <strong>Tgl. Pelayanan Resep:</strong>
                                <span><?= $item['TGLPELRSP'] ?></span>
                            </div>
                            <div class="mb-2">
                                <strong>Biaya Tagihan Resep:</strong>
                                <span>Rp <?= number_format($item['BYTAGRSP'], 0, ',', '.') ?></span>
                            </div>
                            <div class="mb-2">
                                <strong>Verifikasi Resep:</strong>
                                <span><?= $item['BYVERRSP'] === '0' ? 'Belum Diverifikasi' : 'Diverifikasi' ?></span>
                            </div>
                            <div class="mb-2">
                                <strong>Kode Jenis Obat:</strong>
                                <span><?= $item['KDJNSOBAT'] ?>. <?= $item['KDJNSOBAT'] === '1' ? 'PRB' : ($item['KDJNSOBAT'] === '2' ? 'Kronis' : ($item['KDJNSOBAT'] === '3' ? 'Kemoterapi' : '-')) ?></span>
                            </div>
                            <div class="mb-2">
                                <strong>Faskes Asal:</strong>
                                <span><?= $item['FASKESASAL'] ?></span>
                            </div>
                            <div class="mb-2">
                                <strong>Iterasi:</strong>
                                <span><?= $item['FLAGITER'] === 'False' ? 'Tidak Iterasi' : 'Iterasi' ?></span>
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
        <h4 class="alert-heading">Data Resep tidak ditemukan</h4>
        <p>Silakan periksa kembali filter data yang anda tentukan.</p>
    </div>
<?php endif; ?>
