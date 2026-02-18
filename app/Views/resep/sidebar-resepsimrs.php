<div class="page-heading">
    <div class="page-title mb-2">
        <h6 class="mb-0">Daftar Resep SIMRS</h6>
        <small class="text-muted">Data resep dari SIMRS</small>
    </div>

    <section class="section">
        <div class="row">

            <div class="col-12">
                <div class="card mb-2">
                    <div class="card-header py-2">
                        <h6 class="card-title mb-0">Filter Pencarian Kunjungan Pasien</h6>
                    </div>

                    <div class="card-body py-2">
                        <div id="alert-container" class="mb-2"></div>

                        <form id="pencarianResepSIMRS"
                              action="<?= site_url('res/search_resepSIMRS') ?>"
                              method="POST">
                            <?= csrf_field() ?>

                            <!-- ROW 1 -->
                            <div class="row align-items-end g-2">

                                <!-- Unit -->
                                <div class="col-md-3">
                                    <label class="form-label mb-1">Unit</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input"
                                                   type="radio"
                                                   name="option_radio"
                                                   id="opt_inap"
                                                   value="2" checked>
                                            <label class="form-check-label" for="opt_inap">
                                                Rawat Jalan
                                            </label>
                                        </div>
                                        <div class="form-check mb-0">
                                            <input class="form-check-input"
                                                   type="radio"
                                                   name="option_radio"
                                                   id="opt_rajal"
                                                   value="1">
                                            <label class="form-check-label" for="opt_rajal">
                                                Rawat Inap
                                            </label>
                                        </div>
                                        <div class="form-check mb-0">
                                            <input class="form-check-input"
                                                   type="radio"
                                                   name="option_radio"
                                                   id="opt_igd"
                                                   value="3">
                                            <label class="form-check-label" for="opt_igd">
                                                Rawat Gawat Darurat
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tgl Awal -->
                                <div class="col-md-2">
                                    <label class="form-label mb-1">Tgl Awal</label>
                                    <input type="date"
                                           name="tgl_awal"
                                           class="form-control form-control-sm"
                                           value="<?= date('Y-m-d')?>"
                                           required>
                                </div>

                                <!-- Tgl Akhir -->
                                <div class="col-md-2">
                                    <label class="form-label mb-1">Tgl Akhir</label>
                                    <input type="date"
                                           name="tgl_akhr"
                                           class="form-control form-control-sm"
                                           value="<?= date('Y-m-d')?>"
                                           required>
                                </div>
                                <!-- value="2024-10-29" -->
                                <!-- Medrec / Nama -->
                                <div class="col-md-3">
                                    <label class="form-label mb-1">Medrec / Nama Pasien</label>
                                    <div class="input-group input-group-sm">
                                        <button class="btn btn-outline-primary dropdown-toggle"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                id="searchTypeBtn">
                                            Medrec
                                        </button>

                                        <input type="hidden"
                                               name="search_typepasien"
                                               id="searchTypePasien"
                                               value="medrec">

                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item search-type"
                                                   href="#"
                                                   data-type="medrec">
                                                    Medrec
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item search-type"
                                                   href="#"
                                                   data-type="nama">
                                                    Nama Pasien
                                                </a>
                                            </li>
                                        </ul>

                                        <input type="text"
                                               class="form-control"
                                               id="searchInput"
                                               name="medrec" value="0000001" 
                                               placeholder="Masukkan Medrec">
                                    </div>
                                </div>

                                <!-- Button -->
                                <div class="col-md-2 d-grid">
                                    <button type="submit"
                                            class="btn btn-primary btn-sm">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- RESULT -->
            <div class="col-12">
                <div id="result-container"></div>
            </div>

        </div>
    </section>
</div>
