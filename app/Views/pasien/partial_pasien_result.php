<!-- Cek apakah data pasien ada -->
<?php if (!empty($pasien)): ?>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title text-success"><i class="bi bi-person-check-fill"></i> Data Peserta Ditemukan</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <tr>
                    <th width="30%">Nama Peserta</th>
                    <td><strong><?= $pasien['nama'] ?? '-' ?></strong></td>
                </tr>
                <tr>
                    <th>No. Kartu</th>
                    <td><?= $pasien['noKartu'] ?? '-' ?></td>
                </tr>
                <tr>
                    <th>NIK</th>
                    <td><?= $pasien['nik'] ?? '-' ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge bg-primary"><?= $pasien['statusPeserta']['keterangan'] ?? '-' ?></span>
                    </td>
                </tr>
                <tr>
                    <th>Umur</th>
                    <td><?= $pasien['umur']['umurSaatPelayanan'] ?? 0 ?> Tahun</td>
                </tr>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-warning">
        <h4 class="alert-heading">Data Tidak Ditemukan</h4>
        <p>Silakan periksa kembali NIK atau Nomor Kartu BPJS yang Anda masukkan.</p>
    </div>
<?php endif; ?>