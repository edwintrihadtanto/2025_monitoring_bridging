<div id="resepWrapper">

    <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">

        <div class="input-group input-group-sm" style="max-width:320px">
            <span class="input-group-text">🔍</span>
            <input type="text"
                   id="searchResep"
                   class="form-control"
                   placeholder="Cari SEP / Medrec / Nama Pasien">
        </div>

        <div class="form-check ms-2">
            <input class="form-check-input" type="checkbox" id="sepkosong">
            <label class="form-check-label text-danger small" for="sepkosong">
                SEP Kosong
            </label>
        </div>

        <div class="form-check ms-2">
            <input class="form-check-input" type="checkbox" id="sepada">
            <label class="form-check-label text-danger small" for="sepada">
                Terdapat SEP
            </label>
        </div>

        <div class="form-check ms-auto">
            <button
                id="btnProsesTerpilih"
                type="button"
                class="btn btn-success btn-sm"
                disabled>
                <i class="bi bi-lightning"></i> Proses Terpilih
            </button>
        </div>
        <div class="form-check ms-auto">
            <input class="form-check-input" type="checkbox" id="checkAllGlobal">
            <label class="form-check-label fw-semibold">
                Pilih Semua
            </label>
        </div>

        <span class="badge bg-primary" id="selectedCounter">
            0 terpilih
        </span>
    </div>

    <!-- 📦 GROUP BY TANGGAL -->
    <div id="resepContainer">

        <?php foreach ($groups as $gIndex => $group): ?>
            <div class="card mb-2 resep-group">

                <!-- HEADER GROUP -->
                <div class="card-header py-2 bg-light d-flex justify-content-between align-items-center">
                    <div class="fw-semibold">
                        📦 <?= date('d M Y', strtotime($group['tgl'])) ?>
                        <span class="badge bg-secondary ms-2">
                            <?= count($group['data']) ?> resep
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
                    $isBPJS = !empty($item['noresep_bpjs']);
                    $iterValue = $isBPJS ? $item['iterasi'] : $item['sts_iter'];
                ?>

                    <div class="list-group-item resep-item"
                         data-noresep="<?= esc($item['no_resep']) ?>"
                         data-sep="<?= esc($item['no_sep'] ?? '') ?>"
                         data-kdpasien="<?= esc($item['kd_pasienapt']) ?>"
                         data-no_out="<?= esc($item['no_out']) ?>"
                         data-tgl_out="<?= esc($item['tgl_out']) ?>"
                         data-kd_unit="<?= esc($item['kd_unit']) ?>"
                         data-kd_dokter="<?= esc($item['dokter']) ?>"
                         data-sts_iter="<?= esc($item['sts_iter']) ?>"
                         data-kdjnsobat="1"
                         data-search="<?= strtolower(
                             ($item['no_sep'] ?? '') . ' ' .
                             $item['kd_pasienapt'] . ' ' .
                             $item['nmpasien']
                         ) ?>" >

                        <div class="progress mt-2 d-none resep-progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated"
                                 style="width:0%">
                            </div>
                        </div>
                        
                        <div class="row align-items-center">

                            <!-- LEFT -->
                            <div class="col-md-5 d-flex align-items-center gap-2 toggle-detail cursor-pointer"
                                 data-target="<?= $collapseId ?>"
                                 data-noout="<?= $item['no_out'] ?>"
                                 data-tglout="<?= $item['tgl_out'] ?>">

                                <input type="checkbox"
                                       class="form-check-input resep-check"
                                       data-id="<?= $item['no_resep'] ?>">

                                <i class="bi bi-chevron-down text-muted"></i>

                                <div>
                                    <div class="fw-bold d-flex gap-2 flex-wrap">

                                        <?php if (!empty($item['no_sep'])): ?>
                                            <span class="badge bg-success">
                                                <?= esc($item['no_sep']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">
                                                SEP Kosong
                                            </span>
                                        <?php endif; ?>

                                        <?php if ($item['status_kirim'] == 'f'): ?>
                                            <span class="badge bg-danger" title="<?= esc($item['response_message']) ?>">
                                                ⚠️ <?= esc($item['noresep_bpjs']) ?>
                                            </span>
                                        <?php elseif ($item['status_kirim'] == 't'): ?>
                                            <span class="badge bg-primary" title="<?= esc($item['response_message']) ?>">
                                                ✅ <?= esc($item['noresep_bpjs']) ?>
                                            </span>
                                        <?php endif; ?>

                                    </div>

                                    <small class="text-muted">
                                        Resep: <?= esc($item['no_resep']) ?> /
                                        <?= esc($item['customer']) ?>
                                    </small>
                                </div>
                            </div>

                            <!-- MIDDLE -->
                            <div class="col-md-3 text-center">
                                <div class="d-flex justify-content-center gap-3">

                                    <div class="form-check">
                                        <input type="radio"
                                               name="iterasi_<?= $item['no_resep'] ?>"
                                               class="form-check-input"
                                               value="1"
                                               <?= $iterValue == '1' ? 'checked' : '' ?>
                                               onchange="this.closest('.resep-item').dataset.sts_iter=this.value">
                                        <label class="form-check-label text-warning small">
                                            Iterasi
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input type="radio"
                                               name="iterasi_<?= $item['no_resep'] ?>"
                                               class="form-check-input"
                                               value="0"
                                               <?= $iterValue == '0' ? 'checked' : '' ?>
                                               onchange="this.closest('.resep-item').dataset.sts_iter=this.value">
                                        <label class="form-check-label text-secondary small">
                                            Non
                                        </label>
                                    </div>

                                </div>
                            </div>

                            <!-- RIGHT -->
                            <div class="col-md-4">
                                <div class="row align-items-center">

                                    <div class="col-8">
                                        <div class="fw-semibold text-primary text-truncate">
                                            <?= esc($item['nmpasien']) ?>
                                        </div>

                                        <small class="text-muted">
                                            <?= esc($item['kd_pasienapt']) ?> /
                                            <?= esc($item['nama_unit']) ?>
                                        </small>
                                    </div>

                                    <div class="col-4">
                                        <select class="form-select form-select-sm"
                                            onchange="this.closest('.resep-item').dataset.kdjnsobat=this.value">

                                            <option value="1" <?= $item['kdjnsobat'] == 1 ? 'selected' : '' ?>>Obat PRB</option>
                                            <option value="2" <?= $item['kdjnsobat'] == 2 ? 'selected' : '' ?>>Obat Kronis</option>
                                            <option value="3" <?= $item['kdjnsobat'] == 3 ? 'selected' : '' ?>>Obat Kemo</option>
                                        </select>
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