<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h5>Monitoring Obat</h5>
                <p class="text-subtitle text-muted">Monitoring rencana obat dan biaya pengajuan.</p>
            </div>
        </div>
    </div>

    <section class="section">
        
        <!-- KARTU 1: FORM PENCARIAN -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Filtering Data</h4>
            </div>
            <div class="card-body">
                <form id="pencarianMonitoringKlaimForm" action="<?= site_url('pasien/monitoring_obat') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <!-- 1. Bulan -->
                        <div class="col-6 col-md-3 mb-3">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-select" required>
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>

                        <!-- 2. Tahun -->
                        <div class="col-6 col-md-3 mb-3">
                            <label class="form-label">Tahun</label>
                            <input type="number" name="tahun" class="form-control" value="<?= date('Y') ?>" required>
                        </div>

                        <!-- 3. Jenis Obat -->
                        <div class="col-6 col-md-3 mb-3">
                            <label class="form-label">Jenis Obat</label>
                            <select name="jenis_obat" class="form-select" required>
                                <option value="0">Semua Jenis</option>
                                <option value="1">Obat PRB</option>
                                <option value="2">Obat Kronis Blm Stabil</option>
                                <option value="3">Obat Kemoterapi</option>
                            </select>
                        </div>

                        <!-- 4. Status -->
                        <div class="col-6 col-md-3 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="1">Belum Diverifikasi</option>
                                <option value="2">Sudah Verifikasi</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block shadow-lg">
                        <i class="bi bi-search me-2"></i> Tampilkan Monitoring
                    </button>
                </form>
            </div>
        </div>

        <div id="result-container" class="mt-4"></div>
       
    </section>
</div>

