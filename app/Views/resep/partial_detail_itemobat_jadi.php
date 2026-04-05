<div class="py-1 px-2">
    <div class="d-flex align-items-start gap-2">

        <input type="checkbox"
               class="form-check-input mt-1 obat-check"
               data-kdobat="<?= esc($item['kd_prd'])?>"
               data-qty="<?= esc($item['jml_out']) ?>"
               data-racikan="<?= esc($item['nm_racikan']) ?>"
               >

        <div class="flex-grow-1">

            <!-- NAMA -->
            <div class="d-flex align-items-center gap-1 flex-wrap">

                <?php if ($item['kd_obat_bpjs'] != 0): ?>
                    <i class="bi bi-check-circle text-success" data-bs-toggle="tooltip" title="Sudah mapping BPJS"></i>
                <?php endif; ?>

                <span class="fw-semibold text-truncate">
                    <?= esc($item['nama_obat']) ?>
                </span>

                <span class="text-muted small">
                    (<?= esc($item['kd_prd']) ?>)
                </span>

            </div>

            <!-- SIGNA (ATAS SENDIRI) -->
            <?php if (!empty($item['lbl_signa'])): ?>
                <div class="mt-1">
                    <span class="badge bg-danger">
                        <?= esc($item['lbl_signa']) ?>
                    </span>
                </div>
            <?php endif; ?>

            <!-- INPUT & INFO -->
            <div class="d-flex align-items-center flex-wrap gap-2 mt-1 small text-muted">

                <!-- INPUT SIGNA -->
                <span class="d-flex align-items-center gap-1">
                    <input type="number" class="form-control form-control-sm signa1" style="width:55px">
                    x
                    <input type="number" class="form-control form-control-sm signa2" style="width:55px">
                </span>

                <!-- QTY -->
                <span class="d-flex align-items-center gap-1">
                    Qty:
                    <input type="number"
                           class="form-control form-control-sm"
                           style="width:60px"
                           value="<?= esc($item['jml_out']) ?>">
                </span>
                <!-- JHO -->
                <span class="d-flex align-items-center gap-1">
                    Jho:
                    <input type="number"
                           class="form-control form-control-sm"
                           style="width:60px"
                           value="0">
                </span>
                <!-- CATATAN -->
                <?php if (!empty($item['catatan'])): ?>
                    <span class="text-warning">
                        📝 <?= esc($item['catatan']) ?>
                    </span>
                <?php endif; ?>

            </div>

        </div>
    </div>
</div>