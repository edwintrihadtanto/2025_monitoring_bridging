<?php
// Cek apakah data SEP ada
//if (!empty($pasien['noSep'])): 
?>
<?php if (!empty($pasien)): ?>
    <div class="alert alert-success mb-4">
        <h4 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Data SEP Ditemukan</h4>
        <p class="mb-0">Pencarian Surat Eligibilitas Peserta berhasil dilakukan.</p>
    </div>

    <!-- Grid 3 Kolom (Responsive: 1 kolom di HP, 3 kolom di PC) -->
    <div class="row">
        
        <!-- KARTU 1: INFORMASI SEP (BPJS) -->
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-primary"><i class="bi bi-file-earmark-medical me-2"></i>Info SEP</h4>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-striped mb-0">
                        <tr>
                            <th width="40%">No. SEP</th>
                            <td><strong class="text-danger"><?= $pasien['noSep'] ?></strong></td>
                        </tr>
                        <tr>
                            <th>Tgl SEP</th>
                            <td><?= $pasien['tglSep'] ?></td>
                        </tr>
                        <tr>
                            <th>Jns. Pelayanan</th>
                            <td>
                                <span class="badge bg-info"><?= $pasien['jnsPelayanan'] ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>Kelas Rawat</th>
                            <td><?= $pasien['kelasRawat'] ?></td>
                        </tr>
                        <tr>
                            <th>Diagnosa</th>
                            <td><?= $pasien['diagnosa'] ?></td>
                        </tr>
                        <tr>
                            <th>Catatan</th>
                            <td><?= $pasien['catatan'] ?: '-' ?></td>
                        </tr>
                        <?php if(isset($pasien['noRujukan'])): ?>
                        <tr>
                            <th>No. Rujukan</th>
                            <td><?= $pasien['noRujukan'] ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>

        <!-- KARTU 2: DATA PASIEN -->
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-secondary"><i class="bi bi-person-badge me-2"></i>Data Peserta</h4>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-striped mb-0">
                        <tr>
                            <th width="40%">Nama</th>
                            <td><strong><?= $pasien['peserta']['nama'] ?></strong></td>
                        </tr>
                        <tr>
                            <th>No. Kartu</th>
                            <td><?= $pasien['peserta']['noKartu'] ?></td>
                        </tr>
                        <tr>
                            <th>No. MR</th>
                            <td><?= $pasien['peserta']['noMr'] ?: '-' ?></td>
                        </tr>
                        <tr>
                            <th>Tgl Lahir</th>
                            <td><?= $pasien['peserta']['tglLahir'] ?></td>
                        </tr>
                        <tr>
                            <th>Kelamin</th>
                            <td><?= $pasien['peserta']['kelamin'] == 'P' ? 'Perempuan' : 'Laki-laki' ?></td>
                        </tr>
                        <tr>
                            <th>Jns. Peserta</th>
                            <td><?= $pasien['peserta']['jnsPeserta'] ?></td>
                        </tr>
                        <tr>
                            <th>Hak Kelas</th>
                            <td><?= $pasien['peserta']['hakKelas'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- KARTU 3: INFORMASI MEDIS & KONTROL -->
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-warning"><i class="bi bi-heart-pulse me-2"></i>Medis & Kontrol</h4>
                </div>
                <div class="card-body">
                    
                    <!-- Sub Bagian DPJP -->
                    <h6 class="text-muted font-italic mb-2">Dokter Penanggung Jawab (DPJP)</h6>
                    <table class="table table-sm table-striped mb-3">
                        <tr>
                            <th width="40%">Kode DPJP</th>
                            <td><?= $pasien['dpjp']['kdDPJP'] ?: '-' ?></td>
                        </tr>
                        <tr>
                            <th>Nama DPJP</th>
                            <td><?= $pasien['dpjp']['nmDPJP'] ?: '-' ?></td>
                        </tr>
                    </table>

                    <!-- Sub Bagian Kelas Rawat Detail -->
                    <h6 class="text-muted font-italic mb-2">Kelas Rawat Detail</h6>
                    <table class="table table-sm table-striped mb-3">
                        <tr>
                            <th>Kelas Hak</th>
                            <td><?= $pasien['klsRawat']['klsRawatHak'] ?></td>
                        </tr>
                        <tr>
                            <th>Pembiayaan</th>
                            <td><?= $pasien['klsRawat']['pembiayaan'] ?: '-' ?></td>
                        </tr>
                        <tr>
                            <th>Penanggung Jawab</th>
                            <td><?= $pasien['klsRawat']['penanggungJawab'] ?: '-' ?></td>
                        </tr>
                    </table>

                    <!-- Sub Bagian Kontrol -->
                    <h6 class="text-muted font-italic mb-2">Rencana Kontrol</h6>
                    <table class="table table-sm table-striped mb-0">
                        <tr>
                            <th width="40%">No. Surat</th>
                            <td><?= $pasien['kontrol']['noSurat'] ?></td>
                        </tr>
                        <tr>
                            <th>Kode Dokter</th>
                            <td><?= $pasien['kontrol']['kdDokter'] ?></td>
                        </tr>
                        <tr>
                            <th>Nama Dokter</th>
                            <td><?= $pasien['kontrol']['nmDokter'] ?></td>
                        </tr>
                    </table>

                </div>
            </div>
        </div>

    </div>
    <!-- Tutup Row -->

<?php else: ?>
    <div class="alert alert-warning">
        <h4 class="alert-heading">Data SEP Tidak Ditemukan</h4>
        <p>Silakan periksa kembali Nomor SEP yang Anda masukkan.</p>
    </div>
<?php endif; ?>