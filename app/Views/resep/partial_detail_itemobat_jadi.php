<div class="obat-item obat-jadi border-bottom" data-kdobat="<?= esc($item['kd_prd']) ?>">
    <div class="d-flex align-items-center gap-1">

        <input type="checkbox"
               class="form-check-input m-0 obat-check flex-shrink-0"
               data-kdobat="<?= esc($item['kd_prd']) ?>"
               data-qty="<?= esc($item['jml_out']) ?>"
               data-racikan="<?= esc($item['nm_racikan']) ?>"
               data-catatan="<?= esc($item['catatan']) ?>">

        <div class="flex-grow-1" style="min-width:0">
            <div class="d-flex align-items-center gap-1">
                <?php if ($item['kd_obat_bpjs'] != 0): ?>
                    <i class="bi bi-check-circle text-success flex-shrink-0" data-bs-toggle="tooltip" title="Sudah mapping BPJS"></i>
                <?php endif; ?>

                <span class="fw-semibold text-truncate nama-obat flex-grow-1" style="min-width:0">
                    <?= esc($item['nama_obat']) ?>
                </span>

                <span class="obat-bpjs-status small text-muted flex-shrink-0" data-kdobat="<?= esc($item['kd_prd']) ?>"></span>

                <?php if (!empty($item['lbl_signa'])): ?>
                    <span class="badge bg-danger">
                        <?= esc($item['lbl_signa']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <?php if (!empty($item['catatan'])): ?>
                <div class="text-warning small text-truncate lh-sm" title="<?= esc($item['catatan']) ?>">
                    Cat: <?= esc($item['catatan']) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="d-flex align-items-center gap-1 flex-shrink-0 small text-muted">
            <span class="d-flex align-items-center gap-1">
                <input type="number" class="form-control form-control-sm signa1 px-1 py-0" style="width:42px">
                x
                <input type="number" class="form-control form-control-sm signa2 px-1 py-0" style="width:42px">
            </span>

            <span class="d-flex align-items-center gap-1">
                JHO
                <input type="number"
                       class="form-control form-control-sm jho px-1 py-0"
                       style="width:46px"
                       value="0">
            </span>

            <span class="d-flex align-items-center gap-1">
                Qty
                <input type="number"
                       class="form-control form-control-sm qty px-1 py-0"
                       value="<?= esc($item['jml_out']) ?>"
                       style="width:52px">
            </span>
        </div>
    </div>
</div>
