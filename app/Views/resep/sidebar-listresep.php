<div class="page-heading monitoring-theme">

    <!-- HEADER -->
    <div class="page-title mb-3">
        <h6 class="mb-1">Daftar Resep BPJS</h6>
        <p class="text-muted mb-0" style="font-size: 0.85rem;">
            Pencarian data pelayanan resep SIMRS
        </p>
    </div>

    <section class="section">

        <div class="card shadow-sm">
            <div class="card-body p-3">
                <div id="alert-container" class="mb-3"></div>
                <form id="pencarianListResepForm" action="<?= site_url('res/search_listResep') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="row g-2 align-items-end">

                        <div class="col-md-3">
                            <label class="form-label small text-muted">Tgl Awal</label>
                            <input type="date" name="tgl_awal" class="form-control form-control-sm"
                                   value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small text-muted">Tgl Akhir</label>
                            <input type="date" name="tgl_akhr" class="form-control form-control-sm"
                                   value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small text-muted">Jenis Obat</label>
                            <select name="jns_obat" class="form-select form-select-sm" required>
                                <option value="0">Semua</option>
                                <option value="1">PRB</option>
                                <option value="2">Kronis</option>
                                <option value="3">Kemoterapi</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100 btn-sm">
                                <i class="bi bi-search"></i> Cari
                            </button>
                        </div>

                    </div>

                </form>

            </div>
        </div>

        <!-- RESULT -->
        <div class="mt-3" id="result-container"></div>

    </section>
</div>