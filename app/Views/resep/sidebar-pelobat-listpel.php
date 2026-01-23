<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-12">
                <h5>Daftar Pelayanan Obat</h5>
                <p class="text-subtitle text-muted">Daftar Pelayanan Obat berdasarkan Data BPJS</p>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
           <!--  <div class="card-header">
                <h4 class="card-title">Form Pencarian</h4>
            </div> -->
            <div class="card-body">
                
                <div id="alert-container" class="mb-3"></div>

                <form id="pencarianListPelyananObatForm" action="<?= site_url('pel_obat/search_dftarresep') ?>" method="POST">
                    
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <p class="mb-2 font-weight-bold">Pencarian No. Kunjungan / SEP Pasien:</p>
                        <div class="form-group position-relative has-icon-left mb-3">
                            <input type="text" class="form-control" name="searchsep_value" id="searchsep_value" placeholder="Masukkan No. Kunjungan / SEP Pasien" maxlength="19" required>
                            <div class="form-control-icon">
                                <i class="bi bi-card-text"></i>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary shadow-lg btn-block">
                        <i class="bi bi-search me-2"></i> Cari Data
                    </button>
                </form>
            </div>
        </div>

        <div id="result-container" class="mt-4"></div>
    </section>
</div>