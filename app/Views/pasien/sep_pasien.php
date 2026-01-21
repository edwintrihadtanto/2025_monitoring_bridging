<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-12">
                <h5>Pencarian SEP Pasien</h5>
                <p class="text-subtitle text-muted">Pencarian Berdasarkan No. SEP / Data Resep Pasien Rawat Jalan Kronis</p>
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

                <form id="pencarianSEPPasienForm" action="<?= site_url('pasien/searchsep') ?>" method="POST">
                    
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <p class="mb-2 font-weight-bold">Pencarian No. Kunjungan / SEP Pasien:</p>
                            <div class="form-group position-relative has-icon-left mb-3">
                                <input type="text" class="form-control" name="searchsep_value" id="searchsep_value" placeholder="Masukkan SEP Pasien" maxlength="19" required>
                                <div class="form-control-icon">
                                    <i class="bi bi-card-text"></i>
                                </div>
                            </div>
                    </div>

                    <button type="submit" class="btn btn-primary shadow-lg btn-block">
                        <i class="bi bi-search me-2"></i> Cari SEP Pasien
                    </button>
                </form>
            </div>
        </div>

        <div id="result-container" class="mt-4"></div>
    </section>
</div>