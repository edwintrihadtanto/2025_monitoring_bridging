<?php if (empty($detail)): ?>
    <div class="text-muted fst-italic py-2">
        Tidak ada detail obat
    </div>
<?php else: ?>

<div class="mt-2 detail-obat-wrapper"
     data-noresep="<?= esc($noresep ?? '') ?>"
     data-sep="<?= esc($sep ?? '') ?>"
     data-kdpasien="<?= esc($kd_pasien ?? '') ?>"
     data-no_out="<?= esc($no_out ?? '') ?>"
     data-tgl_out="<?= esc($tgl_out ?? '') ?>"
     data-kd_unit="<?= esc($kd_unit ?? '') ?>"
     data-kd_dokter="<?= esc($dokter ?? '') ?>"
     data-iterasi="<?= esc($sts_iter ?? '') ?>"
     >

    <div class="fw-semibold mb-1">
        Detail Obat
        <span class="badge bg-primary ms-1">
            <?= count($detail) ?>
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">

            <thead>
                <tr class="text-muted">
                    <th style="width:36px"></th>
                    <th style="width:130px">Kode</th>
                    <th>Nama Obat</th>
                    <th style="width:70px" class="text-center">Qty</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($detail as $item): ?>
                <tr>
                    <td class="text-center">
                        <input type="checkbox"
                               class="form-check-input obat-check"
                               data-kdobat="<?= esc($item['kd_prd']) ?>"
                               data-qty="<?= esc($item['jml_out']) ?>">
                    </td>

                    <td class="text-truncate">
                        <?= esc($item['kd_prd']) ?>
                    </td>

                    <td class="fw-semibold">
                        <?= esc($item['nama_obat']) ?>
                    </td>

                    <td class="text-center fw-semibold">
                        <?= esc($item['jml_out']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>

    <!-- BUTTON PROSES -->
    <div class="text-end">
        <button type="button" class="btn btn-sm btn-success btn-proses-detail">
            <i class="bi bi-check2-circle me-1"></i> Proses
        </button>
    </div>
</div>

<?php endif ?>
