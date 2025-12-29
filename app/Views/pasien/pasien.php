<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-12">
                <h3>Data Pasien</h3>
                <p class="text-subtitle text-muted">Pencarian Berdasarkan NIK atau No. Kartu BPJS</p>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Form Pencarian</h4>
            </div>
            <div class="card-body">
                
                <div id="alert-container" class="mb-3"></div>

                <form id="pencarianPasienForm" action="<?= site_url('pasien/search') ?>" method="POST">
                    
                    <?= csrf_field() ?>
                    <div class="form-group mb-3">
                        <p class="mb-2 font-weight-bold">Pilih Jenis Pencarian:</p>
                        <div class="d-flex align-items-center">

                            <input type="hidden" name="search_type" id="search_type" value="">

                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" name="option_radio" id="opt_nik" value="nik">
                                <label class="form-check-label" for="opt_nik">NIK</label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="option_radio" id="opt_kartu" value="kartu">
                                <label class="form-check-label" for="opt_kartu">Kartu BPJS</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group position-relative has-icon-left mb-3" id="wrapper_nik">
                        <input type="text" class="form-control" name="search_value" id="input_nik" placeholder="Masukkan NIK Pasien" maxlength="16">
                        <div class="form-control-icon">
                            <i class="bi bi-card-text"></i>
                        </div>
                    </div>

                    <div class="form-group position-relative has-icon-left mb-3 d-none" id="wrapper_kartu">
                        <input type="text" class="form-control" name="search_value_kartu" id="input_kartu" placeholder="Masukkan No. Kartu BPJS" maxlength="13">
                        <div class="form-control-icon">
                            <i class="bi bi-credit-card"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary shadow-lg btn-block">
                        <i class="bi bi-search me-2"></i> Cari Data Pasien
                    </button>
                </form>
            </div>
        </div>

        <!-- Area Hasil Pencarian (Muncul setelah submit) -->
        <div id="result-container" class="mt-4">
            <!-- Hasil akan muncul di sini -->
        </div>
    </section>
</div>