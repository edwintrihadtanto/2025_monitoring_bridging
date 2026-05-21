<?php
    $pagination = $pagination ?? [
        'current_page' => 1,
        'per_page'     => 50,
        'total_rows'   => 0,
        'total_pages'  => 1,
        'has_prev'     => false,
        'has_next'     => false,
        'from'         => 0,
        'to'           => 0,
    ];
?>

<div id="resepWrapper"
     data-page="<?= esc($pagination['current_page']) ?>"
     data-per-page="<?= esc($pagination['per_page']) ?>">

    <div class="resep-toolbar d-flex flex-wrap gap-2 mb-2 align-items-center">

        <div class="input-group input-group-sm" style="max-width:260px">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" id="searchResep" class="form-control"
                   placeholder="Cari SEP/Medrec/Nm Pasien">
        </div>

        <div class="d-flex align-items-center gap-2">

            <label class="form-check m-0">
                <input class="form-check-input"
                       type="radio"
                       name="filter_sep"
                       value="kosong"
                       id="sepkosong">
                <span class="form-check-label small text-danger">SEP Kosong</span>
            </label>

            <label class="form-check m-0">
                <input class="form-check-input"
                       type="radio"
                       name="filter_sep"
                       value="ada"
                       id="sepada">
                <span class="form-check-label small text-success">Ada SEP</span>
            </label>

            <label class="form-check m-0">
                <input class="form-check-input"
                       type="radio"
                       name="filter_sep"
                       value=""
                       checked>
                <span class="form-check-label small text-muted">Semua</span>
            </label>
        </div>

        <div class="ms-auto d-flex align-items-center gap-2">
            <button
                type="button"
                class="btn bg-light btn-sm border"
                onclick="zoomOutPelayanan()"
                title="Perkecil layar">
                <i class="bi bi-zoom-out"></i>
            </button>

            <button 
                type="button"
                class="btn bg-light btn-sm border"
                onclick="zoomResetPelayanan()"
                title="Reset layar">
                <i class="bi bi-aspect-ratio"></i>
            </button>

            <button 
                type="button"
                class="btn bg-light btn-sm border mr-1"
                onclick="zoomInPelayanan()"
                title="Perbesar layar">
                <i class="bi bi-zoom-in"></i>
            </button>

            <span class="badge bg-primary" id="selectedCounter">0</span>

            <button id="btnProsesTerpilih"
                    type="button"
                    class="btn btn-success btn-sm"
                    disabled>
                <i class="bi bi-lightning"></i>
            </button>

            <div class="form-check m-0">
                <input class="form-check-input" type="checkbox" id="checkAllGlobal">
                <label class="form-check-label small">All</label>
            </div>
        </div>

    </div>

    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-2">
        <div class="small text-danger">
            Menampilkan
            <strong><?= esc($pagination['from']) ?>-<?= esc($pagination['to']) ?></strong>
            dari
            <strong><?= esc($pagination['total_rows']) ?></strong>
            resep
        </div>

        <div class="d-flex align-items-center gap-2">
            <select class="form-select form-select-sm simrs-per-page" style="width:auto">
                <option value="50" <?= (int) $pagination['per_page'] === 50 ? 'selected' : '' ?>>50 / halaman</option>
                <option value="100" <?= (int) $pagination['per_page'] === 100 ? 'selected' : '' ?>>100 / halaman</option>
            </select>

            <button type="button"
                    class="btn btn-sm btn-outline-secondary simrs-page-btn"
                    data-page="<?= esc((int) $pagination['current_page'] - 1) ?>"
                    <?= $pagination['has_prev'] ? '' : 'disabled' ?>>
                <i class="bi bi-chevron-left"></i>
            </button>

            <span class="small text-muted">
                Halaman <?= esc($pagination['current_page']) ?> / <?= esc($pagination['total_pages']) ?>
            </span>

            <button type="button"
                    class="btn btn-sm btn-outline-secondary simrs-page-btn"
                    data-page="<?= esc((int) $pagination['current_page'] + 1) ?>"
                    <?= $pagination['has_next'] ? '' : 'disabled' ?>>
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- GROUP BY TANGGAL -->
    <div id="resepContainer">

        <?php foreach ($groups as $gIndex => $group): ?>
            <div class="card mb-2 resep-group">

                <!-- HEADER GROUP -->
                <div class="card-header py-2 bg-light d-flex justify-content-between align-items-center">
                    <div class="fw-semibold">
                        <i class="bi bi-box-seam me-1"></i><?= date('d M Y', strtotime($group['tgl'])) ?>
                        <span class="badge bg-secondary ms-2">
                            Ditampilkan : <?= count($group['data']) ?> Resep
                        </span>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input check-group" type="checkbox">
                        <label class="form-check-label small">Pilih semua</label>
                    </div>
                </div>

                <!-- LIST RESEP -->
                <div class="list-group list-group-flush">

                <?php foreach ($group['data'] as $item): 
                    $collapseId = 'detail-' . md5($item['no_out'].$item['tgl_out']);
                ?>

                    <div class="list-group-item resep-item"
                         data-noresep="<?= esc($item['no_resep']) ?>"
                         data-sep="<?= esc($item['no_sep'] ?? '') ?>"
                         data-kdpasien="<?= esc($item['kd_pasienapt']) ?>"
                         data-no_out="<?= esc($item['no_out']) ?>"
                         data-tgl_out="<?= esc($item['tgl_out']) ?>"
                         data-kd_unit="<?= esc($item['kd_unit']) ?>"
                         data-kd_dokter="<?= esc($item['dokter']) ?>"
                         data-iterasi="<?= esc($item['iterasi']) ?>"
                         data-kdjnsobat="<?= esc($item['sts_iter_final']) ?>"
                         data-search="<?= strtolower(
                             ($item['no_sep'] ?? '') . ' ' .
                             $item['kd_pasienapt'] . ' ' .
                             $item['nmpasien']
                         ) ?>" >

                        <!-- OVERLAY SPINNER -->
                        <div class="resep-overlay-spinner d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    
                        <div class="row align-items-center toggle-detail cursor-pointer" data-target="<?= $collapseId ?>"
                                 data-noout="<?= $item['no_out'] ?>"
                                 data-tglout="<?= $item['tgl_out'] ?>">

                            <!-- LEFT -->
                            <div class="col-md-4 d-flex align-items-center gap-2">

                                <input type="checkbox"
                                       class="form-check-input resep-check prevent-collapse"
                                       data-id="<?= $item['no_resep'] ?>">

                                <i class="bi bi-chevron-down text-muted"></i>

                                <div class="d-flex align-items-stretch gap-2">
                                    <span class="badge bg-primary d-flex align-items-center justify-content-center px-2">
                                        <?= esc($item['no_out']) ?>
                                    </span>

                                    <div>
                                        <div class="fw-bold d-flex gap-2 flex-wrap">

                                            <?php if (!empty($item['no_sep'])): ?>
                                                <span class="badge bg-success">
                                                    <?= esc($item['no_sep']) ?> <i class="bi bi-copy"></i>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">
                                                    SEP Kosong
                                                </span>
                                            <?php endif; ?>

                                            <div class="no-resep-bpjs">
                                                <?php if ($item['status_kirim'] == 'f'): ?>
                                                    <span class="badge bg-danger" title="<?= esc($item['response_message']) ?>">
                                                        <i class="bi bi-exclamation-triangle-fill me-1"></i><?= esc($item['noresep_bpjs']) ?>
                                                    </span>
                                                <?php elseif ($item['status_kirim'] == 't'): ?>
                                                    <span class="badge bg-primary" title="<?= esc($item['response_message']) ?>">
                                                        <i class="bi bi-check-circle-fill me-1"></i><?= esc($item['noresep_bpjs']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <?php if ($item['kd_customer_apt_brangout'] !== '0000000044' && $item['kd_customer_apt_brangout'] !== '0000000043'): ?>
                                            <span class="badge bg-danger" title="<?= esc($item['response_message']) ?>">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Resep: <?= esc($item['no_resep']) ?> /
                                                <?= esc($item['customer']) ?>
                                            </span>
                                        <?php else: ?>
                                            <small class="text-muted">
                                                Resep: <?= esc($item['no_resep']) ?> /
                                                <?= esc($item['customer']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="gap-2" style="display: flex; flex-direction: row; flex-wrap: wrap; justify-content: flex-end;">
                                    
                                    <!-- KIRI: NAMA PASIEN (Flex Grow) -->
                                    <div class="text-truncate" style="text-align: end;">
                                        <div class="gap-2" style="display: flex; flex-direction: row; flex-wrap: wrap; justify-content: flex-end;">
                                            <?php if (!empty($item['no_asuransi'])): ?>
                                            <span class="text-danger">
                                                ( <?= esc($item['no_asuransi']) ?> )
                                            </span>
                                            <?php endif; ?>
                                            <div class="fw-semibold text-primary text-truncate">
                                                <?= esc($item['nmpasien']) ?>
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <?= esc($item['kd_pasienapt']) ?> /
                                            <?= esc($item['nama_unit']) ?>
                                        </small>
                                    </div>

                                    <!-- KANAN: SELECT BOX (Tidak memuai / Shrink) -->
                                    <div class="d-flex gap-1 flex-shrink-0">
                                        <select class="form-select form-select-sm prevent-collapse" style="width:auto; min-width:90px"
                                                onchange="this.closest('.resep-item').dataset.kdjnsobat=this.value">
                                            <option value="1" <?= $item['sts_iter_final'] == '1' ? 'selected' : '' ?>>PRB</option>
                                            <option value="2" <?= $item['sts_iter_final'] == '2' ? 'selected' : '' ?>>Kronis</option>
                                            <option value="3" <?= $item['sts_iter_final'] == '3' ? 'selected' : '' ?>>Kemo</option>
                                        </select>
                                        <select class="form-select form-select-sm prevent-collapse" style="width:auto; min-width:110px"
                                                onchange="this.closest('.resep-item').dataset.iterasi=this.value">
                                            <option value="0" <?= $item['iterasi'] == '0' ? 'selected' : '' ?>>Non Iterasi</option>
                                            <option value="1" <?= $item['iterasi'] == '1' ? 'selected' : '' ?>>Iterasi 1 Kali</option>
                                            <option value="2" <?= $item['iterasi'] == '2' ? 'selected' : '' ?>>Iterasi 2 Kali</option>                                            
                                        </select>
                                        <div class="col-md-auto">                                            
                                            <input type="date" name="tgl_pelayanan" class="form-control form-control-sm" value="<?= esc(date('Y-m-d', strtotime($item['tgl_out']))) ?>" required>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <!-- DETAIL -->
                        <div class="collapse mt-2 ps-4" id="<?= $collapseId ?>" data-loaded="0"></div>

                    </div>

                <?php endforeach; ?>

                </div>
            </div>
        <?php endforeach; ?>

    </div>
</div>
