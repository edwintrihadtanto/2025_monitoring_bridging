<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h5>Data Fasilitas Kesehatan</h5>
                <p class="text-subtitle text-muted">Pencarian Fasilitas Kesehatan Berdasarkan Data BPJS.</p>
            </div>
        </div>
    </div>

    <section class="section">
        
        <!-- TAMBAHKAN WRAPPER ROW INI -->
        <div class="row">
            
            <!-- KOLOM KIRI: FORM PENCARIAN (Lebih Kecil) -->
            <div class="col-12 col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Filtering Data</h4>
                    </div>
                    <div class="card-body">
                        <div id="alert-container" class="mb-3"></div>

                        <form id="pencarianFasKesForm" action="<?= site_url('res/search_faskes') ?>" method="POST">
                            <?= csrf_field() ?>
                            
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Jenis Faskes</label>
                                    <select name="jns_faskes" class="form-select" required>
                                        <option value="1">Faskes 1</option>
                                        <option value="2">Faskes 2 / Rumah Sakit</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Nama Faskes</label>
                                    <input type="text" name="nama_faskes" class="form-control" placeholder="Masukkan nama faskes" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block shadow-lg">
                                <i class="bi bi-search me-2"></i> Tampilkan Pencarian
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: HASIL DETAIL (Lebih Besar) -->
            <div class="col-12 col-md-7">
                <div id="result-container">
                    <!-- Hasil pencarian (Card Detail Faskes) akan muncul di sini -->
                </div>
            </div>

        </div> <!-- Tutup Row -->
       
    </section>
</div>