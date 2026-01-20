<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h5>Data Obat</h5>
                <p class="text-subtitle text-muted">Pencarian Obat Berdasarkan Data BPJS.</p>
            </div>
        </div>
    </div>

    <section class="section">
        
        <div class="row">
            
            <div class="col-12 col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Filtering Data</h4>
                    </div>
                    <div class="card-body">
                        <div id="alert-container" class="mb-3"></div>

                        <form id="pencarianObatForm" action="<?= site_url('ref/search_obat') ?>" method="POST">
                            <?= csrf_field() ?>
                            
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Kode Jenis Obat</label>
                                    <input type="number" name="kd_jenis_obat" class="form-control" placeholder="Masukkan kode jenis" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Tgl Resep</label>
                                    <input type="date" name="tgl_resep" class="form-control" value="<?= date('Y-m-d') ?>"required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Nama Obat</label>
                                    <input type="text" name="nama_obat" class="form-control" placeholder="Masukkan nama obat" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block shadow-lg">
                                <i class="bi bi-search me-2"></i> Tampilkan Pencarian
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-7">
                <div id="result-container">
                </div>
            </div>

        </div> <!-- Tutup Row -->
       
    </section>
</div>