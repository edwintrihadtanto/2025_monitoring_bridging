<?php if (!empty($pasien)): ?>
    <div class="alert alert-success mb-4">
        <h4 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Data SEP Ditemukan</h4>
        <p class="mb-0">Pencarian Surat Eligibilitas Peserta berhasil dilakukan.</p>
    </div>

    <!-- Grid 3 Kolom -->
    <div class="row">
        
        <!-- KARTU 1: INFORMASI SEP -->
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
                            <td><?= tgl_indo($pasien['tglsep']) ?? $pasien['tglsep'] ?></td>
                        </tr>
                        <tr>
                            <th>Tgl Pulang</th>
                            <td><?= tgl_indo($pasien['tglplgsep']) ?? $pasien['tglplgsep'] ?></td>
                        </tr>
                        <tr>
                            <th>Jns. Pelayanan</th>
                            <td>
                                <span class="badge bg-info">
                                    <?= $pasien['jnspelayanan'] == 'RITL' ? 'Rawat Inap Tingkat Lanjut' : $pasien['jnspelayanan'] ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Diagnosa</th>
                            <td><?= $pasien['nmdiag'] ?: '-' ?></td>
                        </tr>
                        <tr>
                            <th>Poli</th>
                            <td><?= $pasien['poli'] ?: '-' ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Faskes Asal Resep -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title text-success mb-0"><i class="bi bi-hospital me-2"></i>Faskes Asal Resep</h6>
                </div>
                <div class="card-body py-2">
                    <table class="table table-sm table-striped mb-0">
                        <tr>
                            <th width="40%">Kode</th>
                            <td><?= $pasien['faskesasalresep'] ?: '-' ?></td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td><strong><?= $pasien['nmfaskesasalresep'] ?: '-' ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- KARTU 2: DATA PESERTA -->
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-secondary"><i class="bi bi-person-badge me-2"></i>Data Peserta</h4>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-striped mb-0">
                        <tr>
                            <th width="40%">Nama</th>
                            <td><strong><?= $pasien['namapeserta'] ?></strong></td>
                        </tr>
                        <tr>
                            <th>No. Kartu</th>
                            <td><?= $pasien['nokartu'] ?></td>
                        </tr>
                        <tr>
                            <th>Tgl Lahir</th>
                            <td><?= tgl_indo($pasien['tgllhr']) ?? $pasien['tgllhr'] ?></td>
                        </tr>
                        <tr>
                            <th>Kelamin</th>
                            <td>
                                <span class="badge <?= $pasien['jnskelamin'] == 'P' ? 'bg-pink' : 'bg-blue' ?>">
                                    <?= $pasien['jnskelamin'] == 'P' ? 'Perempuan' : 'Laki-laki' ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Info Kepegawaian -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title text-info mb-0"><i class="bi bi-building me-2"></i>Info Kepegawaian</h6>
                </div>
                <div class="card-body py-2">
                    <table class="table table-sm table-striped mb-0">
                        <tr>
                            <th width="40%">Jns. Peserta</th>
                            <td><?= $pasien['nmjenispeserta'] ?: '-' ?></td>
                        </tr>
                        <tr>
                            <th>Kode BU</th>
                            <td><?= $pasien['kodebu'] ?: '-' ?></td>
                        </tr>
                        <tr>
                            <th>Nama BU</th>
                            <td><strong><?= $pasien['namabu'] ?: '-' ?></strong></td>
                        </tr>
                        <tr>
                            <th>Pisat</th>
                            <td>
                                <span class="badge <?= $pasien['pisat'] == '1' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $pasien['pisat'] == '1' ? 'PISAT' : 'Non PISAT' ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- KARTU 3: INFORMASI MEDIS & PRB -->
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-warning"><i class="bi bi-heart-pulse me-2"></i>Medis & Lainnya</h4>
                </div>
                <div class="card-body">
                    
                    <!-- DPJP -->
                    <h6 class="text-muted font-italic mb-2">Dokter Penanggung Jawab (DPJP)</h6>
                    <table class="table table-sm table-striped mb-3">
                        <tr>
                            <th width="40%">Kode DPJP</th>
                            <td><?= $pasien['kodedokter'] ?: '-' ?></td>
                        </tr>
                        <tr>
                            <th>Nama DPJP</th>
                            <td><?= $pasien['namadokter'] ?: '-' ?></td>
                        </tr>
                    </table>

                    <!-- PRB -->
                    <h6 class="text-muted font-italic mb-2">Program Pengelolaan Penyakit (PRB)</h6>
                    <table class="table table-sm table-striped mb-0">
                        <tr>
                            <th width="40%">Flag PRB</th>
                            <td>
                                <span class="badge <?= $pasien['flagprb'] == '1' ? 'bg-danger' : 'bg-secondary' ?>">
                                    <?= $pasien['flagprb'] == '1' ? 'Ya' : 'Tidak' ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Nama PRB</th>
                            <td><?= $pasien['namaprb'] ?: '-' ?></td>
                        </tr>
                    </table>

                </div>
            </div>
        </div>

    </div>

<?php else: ?>
    <div class="alert alert-warning">
        <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i>Data SEP Tidak Ditemukan</h4>
        <p>Silakan periksa kembali Nomor SEP yang Anda masukkan.</p>
    </div>
<?php endif; ?>