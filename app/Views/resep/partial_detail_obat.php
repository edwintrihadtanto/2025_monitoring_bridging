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

    // init racikan
    if (!isset($racikan[$key])) {
        $racikan[$key] = [
            'nama'   => $key,
            'items'  => [],
            'signa'  => '',
        ];
    }

    // push item
    $racikan[$key]['items'][] = $item;

    // ambil signa pertama yang ada
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
     data-sts_iter="<?= esc($sts_iter ?? '') ?>"
     >

    <!-- HEADER -->
    <div class="fw-semibold mb-2 small">
        Total item Obat
        <span class="badge bg-primary"><?= count($detail) ?></span>
    </div>

    <div class="row g-2">

        <!-- ================= KIRI : OBAT JADI ================= -->
        <div class="col-md-6">

            <div class="card h-100 md-cardresep">
                <div class="card-header py-1 px-2 bg-light fw-semibold small border-bottom">
                    <i class="bi bi-capsule text-success"></i>
                    Obat Jadi
                    <span class="badge bg-success float-end">
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

        <!-- ================= KANAN : OBAT RACIKAN ================= -->
        <div class="col-md-6">

            <div class="card h-100 md-cardresep">
                <div class="card-header py-1 px-2 bg-light fw-semibold small border-bottom">
                    <i class="bi bi-bezier2 text-warning"></i>
                    Obat Racikan
                    <span class="badge bg-danger text-dark float-end">
                        <?= count($racikan) ?>
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

                                <div class="racikan-box mb-2">

                                    <!-- HEADER -->
                                    <div class="racikan-header fw-semibold small bg-light px-2 py-1"
                                         data-bs-toggle="collapse"
                                         data-bs-target="#racik<?= md5($nama) ?>"
                                         style="cursor:pointer">

                                        ▶ <?= esc($nama) ?>
                                        <span class="badge bg-secondary"><?= count($items) ?></span>
                                    </div>

                                    <!-- BODY -->
                                    <div id="racik<?= md5($nama) ?>" class="collapse show px-2 py-1">

                                        <!-- LIST OBAT -->
                                        <div class="racikan-list">
                                            <?php foreach ($items as $item): ?>
                                                <div class="racikan-item py-1">

                                                    <!-- BARIS UTAMA -->
                                                    <div class="d-flex justify-content-between align-items-start gap-1">

                                                        <!-- KIRI -->
                                                        <div class="d-flex align-items-start gap-1 flex-grow-1">

                                                            <input type="checkbox"
                                                                   class="form-check-input mt-1 obat-check"
                                                                   data-kdobat="<?= esc($item['kd_prd'])?>"
                                                                   data-qty="<?= esc($item['jml_out']) ?>"
                                                                   data-racikan="<?= esc($item['nm_racikan']) ?>"
                                                                   data-catatan="<?= esc($item['catatan']) ?>"
                                                                   >

                                                            <?php if ($item['kd_obat_bpjs'] != 0): ?>
                                                                <i class="bi bi-check-circle text-success" data-bs-toggle="tooltip" title="Sudah mapping BPJS"></i>
                                                            <?php endif; ?>

                                                            <div class="flex-grow-1">

                                                                <!-- NAMA -->
                                                                <div class="fw-semibold text-truncate">
                                                                    <?= esc($item['nama_obat']) ?>
                                                                    <span class="text-muted small">(<?= esc($item['kd_prd']) ?>)</span>
                                                                    <!-- ✅ TAMBAHKAN SPAN INI UNTUK ICON STATUS ERROR -->
                                                                    <span class="obat-bpjs-status small text-muted" data-kdobat="<?= esc($item['kd_prd']) ?>"></span>
                                                                </div>

                                                                

                                                                <!-- CATATAN -->
                                                                <?php if (!empty($item['catatan'])): ?>
                                                                    <div class="text-warning small">
                                                                        📝 <?= esc($item['catatan']) ?>
                                                                    </div>
                                                                <?php endif; ?>

                                                            </div>

                                                        </div>

                                                        <!-- KANAN (QTY) -->
                                                        <div class="d-flex align-items-center gap-1">
                                                            <span class="small text-muted">Qty:</span>
                                                            <input type="number"
                                                                   class="form-control form-control-sm qty"
                                                                   value="<?= esc($item['jml_out']) ?>"
                                                                   style="width:65px">
                                                        </div>

                                                    </div>

                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="flex-grow-1" style="margin-top: -12px;">
                                            <!-- SIGNA (ATAS SENDIRI) -->
                                            <?php if (!empty($racik['signa'])): ?>
                                                <div class="mb-1">
                                                    <span class="badge bg-danger">
                                                        <?= esc($racik['signa']) ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <!-- INPUT GLOBAL RACIKAN -->
                                            <div class="d-flex flex-wrap align-items-center gap-2 small mb-0">
                                                
                                                <!-- SIGNA -->
                                                <span class="d-flex align-items-center gap-1">
                                                    <input type="number" class="form-control form-control-sm signa1" style="width:55px">
                                                    x
                                                    <input type="number" class="form-control form-control-sm signa2" style="width:55px">
                                                </span>

                                                <!-- PERMINTAAN -->
                                                <span class="d-flex align-items-center gap-1">
                                                    Permintaan:
                                                    <input type="number" class="form-control form-control-sm permintaan" style="width:65px">
                                                </span>

                                                <!-- JHO -->
                                                <span class="d-flex align-items-center gap-1">
                                                    JHO:
                                                    <input type="number" class="form-control form-control-sm jho" style="width:55px">
                                                </span>

                                            </div>
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

    <!-- BUTTON -->
    <div class="text-end mt-2">
        <button type="button" class="btn btn-sm btn-success btn-proses-detail">
            <i class="bi bi-check2-circle"></i> Proses
        </button>
    </div>

</div>
<?php endif; ?>