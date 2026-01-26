<div id="resepContainer">
<div class="d-flex gap-2 mb-2 align-items-center">
    <div class="input-group input-group-sm" style="max-width:300px">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" id="searchResep" class="form-control"
               placeholder="Cari SEP / Kode Pasien / Nama Pasien">
    </div>
    <div class="form-check ms-auto">
        <input class="form-check-input" type="checkbox" id="sepkosong">
        <label class="form-check-label text-danger" for="sepkosong">Tampilkan SEP Kosong</label>
    </div>
    <div class="form-check ms-auto">
        <input class="form-check-input" type="checkbox" id="checkAllGlobal">
        <label class="form-check-label fw-semibold" for="checkAllGlobal">Pilih Semua</label>
    </div>

    <span class="badge bg-primary" id="selectedCounter">
        0 terpilih
    </span>
</div>

<?php foreach ($groups as $gIndex => $group): ?>
<div class="card mb-2 resep-group">

    <div class="card-header py-2 bg-light d-flex justify-content-between align-items-center">
        <div class="fw-semibold">
            <i class="bi bi-calendar-event me-1"></i>
            <span class="badge bg-primary ms-2">Group Resep Tgl. <?= $group['tgl'] ?></span>
        </div>

        <div class="form-check">
            <input class="form-check-input check-group" type="checkbox">
            <label class="form-check-label small">Pilih semua</label>
        </div>
    </div>

    <div class="list-group list-group-flush">

    <?php foreach ($group['data'] as $index => $item): ?>
        <div class="list-group-item resep-item"
             data-search="<?= strtolower(
                 $item['no_sjp'].' '.
                 $item['kd_pasienapt'].' '.
                 $item['nmpasien']
             ) ?>">

            <div class="d-flex justify-content-between align-items-center">

                <div class="d-flex align-items-center gap-2 toggle-detail cursor-pointer"
                     data-bs-toggle="collapse"
                     data-bs-target="#detail-<?= md5($item['no_resep'].$index) ?>">

                    <input type="checkbox" class="form-check-input resep-check" data-id="<?= $item['no_resep'] ?>">

                    <i class="bi bi-chevron-down rotate-icon text-muted"></i>

                    <div>
                        <div class="fw-bold">
                            <?php if ($item['no_sjp'] != ''): ?>
                            <span class="badge bg-success ms-2"><?= $item['no_sjp'] ?></span> <i class="bi bi-copy text-muted" title="Copy SEP"></i>
                            <?php else: ?>
                            <span class="badge bg-danger ms-2">SEP Kosong</span>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted">
                            Resep : <?= $item['no_resep'] ?> / <?= $item['customer'] ?>
                        </small>
                    </div>
                </div>

                <div class="align-items-end">
                    <div class="fw-semibold text-primary">
                        <?= $item['nmpasien'] ?>
                    </div>
                    <small class="text-muted">
                        <?= $item['kd_pasienapt'] ?> / <?= $item['nama_unit'] ?> 
                    </small>
                </div>
            </div>

            <!-- DETAIL -->
            <div class="collapse mt-2 ps-4"
                 id="detail-<?= md5($item['no_resep'].$index) ?>">
                <div class="bg-light rounded p-2 small">
                    <?php foreach ($item['obat'] ?? [] as $obat): ?>
                        <div>
                            <i class="bi bi-capsule me-1 text-success"></i>
                            <?= esc($obat['nama_obat']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    <?php endforeach; ?>

    </div>
</div>
<?php endforeach; ?>

</div>

