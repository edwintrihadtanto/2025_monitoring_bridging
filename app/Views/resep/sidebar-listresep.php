<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h5>Daftar Resep</h5>
                <p class="text-subtitle text-muted">Daftar Resep Berdasarkan Data BPJS.</p>
            </div>
        </div>
    </div>

    <section class="section">
        
        <div class="row">
            
            <div class="col-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Filtering Data</h4>
                    </div>
                    <div class="card-body">
                        <div id="alert-container" class="mb-3"></div>

                        <form id="pencarianListResepForm" action="<?= site_url('res/search_listResep') ?>" method="POST">
                            <?= csrf_field() ?>
                            
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="form-label">Tgl. Awal</label>
                                    <input type="date" name="tgl_awal" class="form-control" value="<?= date('Y-m-d') ?>"required>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="form-label">Tgl. Akhir</label>
                                    <input type="date" name="tgl_akhr" class="form-control" value="<?= date('Y-m-d') ?>"required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block shadow-lg">
                                <i class="bi bi-search me-2"></i> Tampilkan Pencarian
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-12">
                <div id="result-container">
                </div>
            </div>

        </div> <!-- Tutup Row -->
       
    </section>
</div>