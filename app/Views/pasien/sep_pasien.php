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
                    <div class="form-group mb-3">
                        <p class="mb-2 font-weight-bold">Pilih Jenis Pencarian:</p>
                        <div class="d-flex align-items-center">

                            <input type="hidden" name="searchsep_type" id="searchsep_type" value="">

                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" name="option_radio" id="opt_rajal" value="1">
                                <label class="form-check-label" for="opt_rajal">Rawat Jalan</label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="option_radio" id="opt_ranap" value="2">
                                <label class="form-check-label" for="opt_ranap">Rawat Inap</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group position-relative has-icon-left mb-3" id="wrapper_sep">
                        <input type="text" class="form-control" name="searchsep_value" id="input_sep" placeholder="Masukkan SEP Pasien" maxlength="19">
                        <div class="form-control-icon">
                            <i class="bi bi-card-text"></i>
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