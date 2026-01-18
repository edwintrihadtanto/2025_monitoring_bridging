<?php
if (!empty($apotikList)): 
    $data = $apotikList; // Agar penulisan variabel lebih pendek
?>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="card-title mb-0"><i class="bi bi-building me-2"></i>Profil Apotik</h4>
            <small><?= $data['nama'] ?? '-' ?></small>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <tr>
                        <th width="40%">Kode Faskes</th>
                        <td><strong class="text-primary"><?= $data['kode'] ?></strong></td>
                    </tr>
                    <tr>
                        <th>Nama Apoteker</th>
                        <td><?= $data['namaapoteker'] ?></td>
                    </tr>
                    <tr>
                        <th>Nama Kepala</th>
                        <td>
                            <?= $data['namakepala'] ?> <br>
                            <small class="text-muted">(<?= $data['jabatankepala'] ?>)</small>
                        </td>
                    </tr>
                    <tr>
                        <th>NIP Kepala</th>
                        <td><?= $data['nipkepala'] ?></td>
                    </tr>
                    <tr>
                        <th>SIUP</th>
                        <td><?= $data['siup'] ?></td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td><?= $data['alamat'] ?></td>
                    </tr>
                    <tr>
                        <th>Kota</th>
                        <td><?= $data['kota'] ?></td>
                    </tr>
                    <tr>
                        <th>Stock Obat</th>
                        <td>
                            <?php 
                            $checkStock = strtolower($data['checkstock']);
                            if ($checkStock == 'true' || $checkStock == '1'): ?>
                                <span class="badge bg-success">Tersedia</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Tidak Tersedia</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>

            <hr>

            <h6 class="text-muted">Data Verifikator</h6>
            <table class="table table-sm table-bordered mb-0">
                <tr>
                    <th width="40%">Nama Verifikator</th>
                    <td><?= $data['namaverifikator'] ?></td>
                </tr>
                <tr>
                    <th>NPP Verifikator</th>
                    <td><?= $data['nppverifikator'] ?></td>
                </tr>
                <tr>
                    <th>Nama Petugas Apotek</th>
                    <td><?= $data['namapetugasapotek'] ?></td>
                </tr>
                <tr>
                    <th>NIP Petugas Apotek</th>
                    <td><?= $data['nippetugasapotek'] ?></td>
                </tr>
            </table>
        </div>
    </div>

<?php else: ?>
    <div class="alert alert-warning text-center">
        <i class="bi bi-exclamation-triangle display-4"></i>
        <h4 class="mt-2">Data Apotik Tidak Ditemukan</h4>
        <p>Silakan periksa kembali Kode Apotik dari BPJS.</p>
    </div>
<?php endif; ?>