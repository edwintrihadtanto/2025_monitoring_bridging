<?php if (empty($detail)): ?>
    <div class="text-muted fst-italic py-2 small">
        Tidak ada detail obat
    </div>
<?php else: ?>

<?php
$obat_jadi = [];
$racikan = [];

foreach ($detail as $item) {

    $is_racikan = !empty($item['nm_racikan']) && strtolower($item['nm_racikan']) != 'tidak';

    if (!$is_racikan) {
        $obat_jadi[] = $item;
        continue;
    }

    $key = $item['nm_racikan'];

    if (!isset($racikan[$key])) {
        $racikan[$key] = [
            'nama'   => $key,
            'items'  => [],
            'signa'  => '',
        ];
    }

    $racikan[$key]['items'][] = $item;

    if (!empty($item['lbl_signa']) && empty($racikan[$key]['signa'])) {
        $racikan[$key]['signa'] = $item['lbl_signa'];
    }
}
?>

<div class="mt-2 detail-obat-wrapper compact-mode"
     data-noresep="<?= esc($noresep ?? '') ?>"
     data-sep="<?= esc($sep ?? '') ?>"
     data-kdpasien="<?= esc($kd_pasien ?? '') ?>"
     data-no_out="<?= esc($no_out ?? '') ?>"
     data-tgl_out="<?= esc($tgl_out ?? '') ?>"
     data-kd_unit="<?= esc($kd_unit ?? '') ?>"
     data-kd_dokter="<?= esc($dokter ?? '') ?>"
     data-sts_iter="<?= esc($sts_iter ?? '') ?>">

    <div class="fw-semibold mb-2 small">
        Total item Obat
        <span class="badge bg-primary"><?= count($detail) ?></span>
    </div>

    <div class="row g-2">

        <div class="col-md-6">

            <div class="card md-cardresep mb-1">
                <div class="card-header py-1 px-2 bg-light fw-semibold small border-bottom">
                    <i class="bi bi-capsule text-success"></i>
                    Obat Jadi
                    <span class="badge bg-primary float-end">
                        <?= count($obat_jadi) ?>
                    </span>
                </div>

                <div class="card-body p-1" style="border-left: 1px dashed #ddd; border-right: 1px dashed #ddd;border-bottom: 1px dashed #ddd; border-radius: 0 0 6px 6px;">

                    <?php if (!empty($obat_jadi)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($obat_jadi as $item): ?>
                                <?= view('resep/partial_detail_itemobat_jadi', ['item' => $item]) ?>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-muted small fst-italic p-2">
                            Tidak ada obat jadi
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>

        <div class="col-md-6">

            <div class="card md-cardresep mb-1">
                <div class="card-header py-1 px-2 bg-light fw-semibold small border-bottom">
                    <i class="bi bi-bezier2 text-warning"></i>
                    Obat Racikan
                    <span class="badge bg-danger text-dark float-end">
                        <?= count($racikan) ?> Racikan
                    </span>
                </div>

                <div class="card-body p-1">

                    <?php if (!empty($racikan)): ?>

                        <div class="accordion accordion-flush" id="accordionRacikan">

                            <?php foreach ($racikan as $racik): ?>
                            <?php
                                $nama  = $racik['nama'];
                                $items = $racik['items'];
                            ?>

                                <div class="racikan-box mb-1">

                                    <div class="racikan-header d-flex align-items-center gap-1 bg-light px-1 py-1 border-bottom"
                                         data-bs-toggle="collapse"
                                         data-bs-target="#racik<?= md5($nama) ?>"
                                         style="cursor:pointer">

                                        <i class="bi bi-chevron-right text-muted flex-shrink-0"></i>
                                        <span class="fw-semibold text-truncate flex-grow-1" style="min-width:0">
                                            <?= esc($nama) ?>
                                        </span>
                                        <span class="badge bg-primary"><?= count($items) ?></span>

                                        <?php if (!empty($racik['signa'])): ?>
                                            <span class="badge bg-danger">
                                                <?= esc($racik['signa']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div id="racik<?= md5($nama) ?>" class="collapse show">

                                        <div class="racikan-list">
                                            <?php foreach ($items as $item): ?>
                                                <div class="racikan-item d-flex align-items-center gap-1 border-bottom">

                                                    <input type="checkbox"
                                                           class="form-check-input m-0 obat-check flex-shrink-0"
                                                           data-kdobat="<?= esc($item['kd_prd']) ?>"
                                                           data-qty="<?= esc($item['jml_out']) ?>"
                                                           data-racikan="<?= esc($item['nm_racikan']) ?>"
                                                           data-catatan="<?= esc($item['catatan']) ?>">

                                                    <?php if ($item['kd_obat_bpjs'] != 0): ?>
                                                        <i class="bi bi-check-circle text-success flex-shrink-0" data-bs-toggle="tooltip" title="Sudah mapping BPJS"></i>
                                                    <?php endif; ?>

                                                    <div class="flex-grow-1" style="min-width:0">
                                                        <div class="d-flex align-items-center gap-1">
                                                            <span class="fw-semibold text-truncate flex-grow-1" style="min-width:0">
                                                                <?= esc($item['nama_obat']) ?>
                                                            </span>
                                                            <span class="text-muted small flex-shrink-0 d-none">(<?= esc($item['kd_prd']) ?>)</span>
                                                            <span class="obat-bpjs-status small text-muted flex-shrink-0" data-kdobat="<?= esc($item['kd_prd']) ?>"></span>
                                                        </div>

                                                        <?php if (!empty($item['catatan'])): ?>
                                                            <div class="text-warning small text-truncate lh-sm" title="<?= esc($item['catatan']) ?>">
                                                                Cat: <?= esc($item['catatan']) ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="d-flex align-items-center gap-1 flex-shrink-0 small text-muted">
                                                        Qty
                                                        <input type="number"
                                                               class="form-control form-control-sm qty px-1 py-0"
                                                               value="<?= esc($item['jml_out']) ?>"
                                                               style="width:52px">
                                                    </div>

                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <div class="d-flex flex-wrap align-items-center justify-content-end gap-1 pt-1 small text-muted">
                                            <span class="d-flex align-items-center gap-1">
                                                <input type="number" class="form-control form-control-sm signa1 px-1 py-0" style="width:42px">
                                                x
                                                <input type="number" class="form-control form-control-sm signa2 px-1 py-0" style="width:42px">
                                            </span>

                                            <span class="d-flex align-items-center gap-1">
                                                Permintaan
                                                <input type="number" class="form-control form-control-sm permintaan px-1 py-0" style="width:58px">
                                            </span>

                                            <span class="d-flex align-items-center gap-1">
                                                JHO
                                                <input type="number" class="form-control form-control-sm jho px-1 py-0" style="width:46px">
                                            </span>
                                        </div>
                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    <?php else: ?>
                        <div class="text-muted small fst-italic p-2">
                            Tidak ada obat racikan
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>

    </div>

    <div class="text-end">
        <button type="button" class="btn btn-sm btn-success btn-proses-detail">
            <i class="bi bi-check2-circle"></i> Proses
        </button>
    </div>

</div>
<?php endif; ?>
