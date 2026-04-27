<div class="page-heading monitoring-theme">

    <!-- HEADER -->
    <div class="page-title mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Monitoring API Bridging</h4>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">
                    Log aktivitas integrasi BPJS Farmasi
                </p>
            </div>

            <!-- LIVE INDICATOR -->
            <div>
                <span class="badge bg-danger">LIVE</span>
            </div>
        </div>
    </div>

    <section class="section">

        <!-- STAT CARD -->
        <div class="row g-3 mb-3">

            <div class="col-6 col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">2xx Success</small>
                            <h3 class="mb-0"><?= number_format($rekap['code200'], 0, ',', '.') ?></h3>
                        </div>
                        <div class="badge bg-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">3xx Redirect</small>
                            <h3 class="mb-0"><?= number_format($rekap['code300'], 0, ',', '.') ?></h3>
                        </div>
                        <div class="badge bg-info">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">4xx Client</small>
                            <h3 class="mb-0"><?= number_format($rekap['code400'], 0, ',', '.') ?></h3>
                        </div>
                        <div class="badge bg-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">5xx Server</small>
                            <h3 class="mb-0"><?= number_format($rekap['code500'], 0, ',', '.') ?></h3>
                        </div>
                        <div class="badge bg-danger">
                            <i class="bi bi-x-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- TABLE -->
        <div class="card">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle datatable" id="table1" data-current-perpage="<?= $perPage ?>">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Waktu</th>
                                <th>Endpoint</th>
                                <th width="10%">Method</th>
                                <th width="10%">Code</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($logs as $i => $log): ?>
                            <tr>

                                <td><?= $i + 1 ?></td>

                                <td>
                                    <span class="d-inline-block text-truncate">
                                        <?= $log['created_at'] ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="d-inline-block text-truncate"
                                          style="max-width: 400px;"
                                          title="<?= $log['endpoint'] ?>">
                                        <?php
                                            $path = parse_url($log['endpoint'], PHP_URL_PATH);
                                            $short = strstr($path, 'vclaim-rest-dev/');
                                            echo $short ?: $path;
                                        ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-dark">
                                        <?= $log['method'] ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ($log['response_code'] >= 200 && $log['response_code'] < 300): ?>
                                        <span class="badge bg-success"><?= $log['response_code'] ?></span>

                                    <?php elseif ($log['response_code'] >= 300 && $log['response_code'] < 400): ?>
                                        <span class="badge bg-info"><?= $log['response_code'] ?></span>

                                    <?php elseif ($log['response_code'] >= 400 && $log['response_code'] < 500): ?>
                                        <span class="badge bg-warning"><?= $log['response_code'] ?></span>

                                    <?php else: ?>
                                        <span class="badge bg-danger"><?= $log['response_code'] ?></span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary btn-detail-log"
                                        data-request='<?= htmlspecialchars($log['endpoint'], ENT_QUOTES, 'UTF-8') ?>'
                                        data-requestbody='<?= htmlspecialchars($log['request_body'], ENT_QUOTES, 'UTF-8') ?>'
                                        data-response='<?= htmlspecialchars($log['response_body'], ENT_QUOTES, 'UTF-8') ?>'>
                                        
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>

                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div id="custom-pagination-container" class="d-none">
                    <nav aria-label="Page navigation">
                        <?= $pagination->links('group1', 'bootstrap_full') ?>
                    </nav>
                </div>
            </div>
        </div>

    </section>
</div>