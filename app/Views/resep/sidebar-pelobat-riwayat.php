<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-12">
                <h5>Daftar Riwayat Pelayanan Obat</h5>
                <p class="text-subtitle text-muted">Daftar Riwayat Pelayanan Obat berdasarkan Data BPJS</p>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Filtering Data</h4>
            </div>
            <div class="card-body">
                
                <div id="alert-container" class="mb-3"></div>

                <form id="pencarianRiwayatPelyananObatForm" action="<?= site_url('pel_obat/search_riwayat') ?>" method="POST">
                    
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-12 col-md-3 mb-3">
                            <label class="form-label">Tgl. Awal</label>
                            <input type="date" name="tgl_awal" class="form-control" value="2026-03-01"required>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <label class="form-label">Tgl. Akhir</label>
                            <input type="date" name="tgl_akhr" class="form-control" value="<?= date('Y-m-d') ?>"required>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <p class="mb-2 font-weight-bold">Pencarian No. Kartu BPJS</p>
                            <div class="form-group position-relative has-icon-left mb-3">
                                <input type="text" class="form-control" name="no_kartu" id="no_kartu" placeholder="Masukkan No. Kartu BPJS" maxlength="13" value="0002059334728" required>
                                <div class="form-control-icon">
                                    <i class="bi bi-card-text"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary shadow-lg btn-block">
                        <i class="bi bi-search me-2"></i> Cari Data Riwayat
                    </button>
                </form>
            </div>
        </div>

        <div id="result-container" class="mt-4"></div>
    </section>
</div>