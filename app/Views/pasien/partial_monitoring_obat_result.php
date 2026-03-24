<?php
// Cek apakah data monitoring ada
if (isset($monitoringData) && !empty($monitoringData['response'])): 
    $rekap = $monitoringData['response'];
    $list = $rekap['listsep'];
?>

    <!-- A. SUMMARY CARDS -->
    <div class="row mb-4">
        <div class="col-12 col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Total Data SEP</h6>
                    <h2 class="text-white mb-0"><?= number_format($rekap['jumlahdata'], 0, ',', '.') ?></h2>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6 class="card-title text-dark-50">Biaya Pengajuan</h6>
                    <h2 class="text-dark mb-0">Rp <?= number_format($rekap['totalbiayapengajuan'], 0, ',', '.') ?></h2>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Biaya Setuju</h6>
                    <h2 class="text-white mb-0">Rp <?= number_format($rekap['totalbiayasetuju'], 0, ',', '.') ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- B. TABLE DAFTAR OBAT -->
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Daftar Rencana Obat</h4>
        </div>
        <div class="card-body table-responsive">
            <!-- Tambahkan class 'datatable' agar otomatis inisialisasi DataTables -->
            <table class="table table-striped datatable" id="table-monitoring-obat" width="100%">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Peserta</th>
                        <th width="10%">No Resep</th>
                        <th width="20%">Jenis Obat</th>
                        <th width="13%">Tgl Pelayanan</th>
                        <th width="20%">Biaya Pengajuan</th>
                        <th width="20%">Biaya Setuju</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($list as $index => $item): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <div class="small text-truncate">No Apotik: <?= $item['nosepapotek'] ?></div>
                            <strong><?= $item['nmpst'] ?></strong>
                            <div class="small text-muted">No Kartu: <?= $item['nokapst'] ?></div>
                        </td>
                        <td><?= $item['noresep'] ?></td>
                        <td>
                            <?php 
                            $badgeClass = 'bg-secondary';
                            if(strpos($item['nmjnsobat'], 'PRB') !== false) $badgeClass = 'bg-info';
                            if(strpos($item['nmjnsobat'], 'Kronis') !== false) $badgeClass = 'bg-warning';
                            if(strpos($item['nmjnsobat'], 'Kemoterapi') !== false) $badgeClass = 'bg-danger';
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= $item['nmjnsobat'] ?></span>
                        </td>
                        <td><?= $item['tglpelayanan'] ?></td>
                        <td class="text-end">
                            <span class="fw-bold text-warning">
                                Rp <?= number_format($item['biayapengajuan'], 0, ',', '.') ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-success">
                                Rp <?= number_format($item['biayasetujui'], 0, ',', '.') ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php else: ?>
    <div class="alert alert-warning">
        <h4 class="alert-heading">Data Monitoring Tidak Ditemukan</h4>
        <p>Silakan periksa kembali parameter Bulan, Tahun, atau Jenis Obat.</p>
    </div>
<?php endif; ?>