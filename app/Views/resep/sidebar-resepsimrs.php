<div class="page-heading monitoring-theme">

    <!-- HEADER -->
    <div class="page-title mb-2">
        <h6 class="mb-0">Pencarian Resep SIMRS</h6>
        <small class="text-muted">Filter kunjungan pasien & resep</small>
    </div>

    <section class="section">

        <div class="card mb-2">
            <div class="card-body py-2">
                <div id="alert-container" class="mb-2"></div>
                <form id="pencarianResepSIMRS"
                      action="<?= site_url('res/search_resepSIMRS') ?>"
                      method="POST">
                    <?= csrf_field() ?>

                    <div class="row g-2 align-items-end">

                        <!-- UNIT -->
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Unit</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <label class="form-check form-check-inline m-0">
                                    <input class="form-check-input" type="radio" name="option_radio" value="2" checked>
                                    <small>Rajal</small>
                                </label>
                                <label class="form-check form-check-inline m-0">
                                    <input class="form-check-input" type="radio" name="option_radio" value="1">
                                    <small>Ranap</small>
                                </label>
                                <label class="form-check form-check-inline m-0">
                                    <input class="form-check-input" type="radio" name="option_radio" value="3">
                                    <small>IGD</small>
                                </label>
                            </div>
                        </div>

                        <!-- TANGGAL -->
                        <div class="col-md-2">
                            <label class="form-label small text-muted mb-1">Tgl Awal</label>
                            <input type="date" name="tgl_awal" class="form-control form-control-sm"
                                   value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small text-muted mb-1">Tgl Akhir</label>
                            <input type="date" name="tgl_akhr" class="form-control form-control-sm"
                                   value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <!-- SEARCH -->
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Cari Pasien</label>
                            <div class="input-group input-group-sm">

                                <button class="btn btn-outline-secondary dropdown-toggle"
                                        data-bs-toggle="dropdown" type="button"
                                        id="searchTypeBtn">
                                    Medrec
                                </button>

                                <input type="hidden" name="search_typepasien" id="searchTypePasien" value="medrec">

                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item search-type" href="#" data-type="medrec">Medrec</a></li>
                                    <li><a class="dropdown-item search-type" href="#" data-type="nama">Nama</a></li>
                                </ul>

                                <input type="text" class="form-control"
                                       id="searchInput"
                                       name="medrec"
                                       placeholder="Masukkan data...">
                            </div>
                        </div>

                        <!-- BUTTON -->
                        <div class="col-md-2 d-grid">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>

                    </div>

                </form>

            </div>
        </div>

        <div id="result-container"></div>

    </section>
</div>