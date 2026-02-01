<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h5>Log Monitoring API Bridging BPJS Farmasi</h5>
            </div>
        </div>
    </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <?php
                        // $rekap = $data['rekap'];
                    ?>
                    <div class="row mb-4">
                        <div class="col-12 col-md-3">
                            <div class="card">
                                <div class="card-body rgb-border-200 p-3">
                                    <h6 class="card-title">Code >= 2xx</h6>
                                    <h2 class="mb-0"><?= number_format($rekap['code200'], 0, ',', '.') ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="card">
                                <div class="card-body rgb-border-300 p-3">
                                    <h6 class="card-title">Code >= 3xx</h6>
                                    <h2 class="mb-0"><?= number_format($rekap['code300'], 0, ',', '.') ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="card">
                                <div class="card-body rgb-border-400 p-3">
                                    <h6 class="card-title">Code >= 4xx</h6>
                                    <h2 class="mb-0"><?= number_format($rekap['code400'], 0, ',', '.') ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="card">
                                <div class="card-body rgb-border-500 p-3">
                                    <h6 class="card-title">Code >= 5xx</h6>
                                    <h2 class="mb-0"><?= number_format($rekap['code500'], 0, ',', '.') ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- CUSTOM SHOW ENTRIES DROPDOWN -->
                    <!-- <div class="d-flex justify-content-end mb-3">
                        <div class="d-flex align-items-center">
                            <span class="me-2" style="font-size: 0.9rem;">Tampilkan:</span>
                            <select id="entriesPerPage" class="form-select w-auto" style="width: 80px !important;">
                                <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
                                <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
                                <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
                                <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100</option>
                            </select>
                        </div>
                    </div> -->

                    <div class="table-responsive">
                        <table class="table table-striped datatable" id="table1" width="100%" data-current-perpage="<?= $perPage ?>">
  
                        <!-- <table class="table table-striped dt-responsive" id="table1" width="100%"> -->
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Waktu</th>
                                    <th>Endpoint</th>
                                    <th>Method</th>
                                    <th>Code</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $i => $log): ?>
                                <tr>
                                    <td width="2%"><?= $i + 1 ?></td>
                                    <td width="13%"><?= $log['created_at'] ?></td>
                            
                                    <td width="30%"><span class="d-inline-block text-truncate" style="width:500px;" title="<?= $log['endpoint'] ?>"><?= $log['endpoint'] ?></span></td>
                                    
                                    <td width="5%"><?= $log['method'] ?></td>
                                    <td width="5%" >
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
                                    <td width="5%">
                                       <!--  <button class="btn btn-sm btn-info btn-detail-log" 
                                                data-request='<?= htmlspecialchars($log['request_header'] . "\n" . $log['request_body'], ENT_QUOTES, 'UTF-8') ?>'
                                                data-response='<?= htmlspecialchars($log['response_body'], ENT_QUOTES, 'UTF-8') ?>'>
                                            Detail
                                        </button> -->
                                        <button class="btn btn-sm btn-dark btn-detail-log" 
                                                data-request='<?= htmlspecialchars($log['endpoint'], ENT_QUOTES, 'UTF-8') ?>'
                                                data-requestbody='<?= htmlspecialchars($log['request_body'], ENT_QUOTES, 'UTF-8') ?>'
                                                data-response='<?= htmlspecialchars($log['response_body'], ENT_QUOTES, 'UTF-8') ?>'>
                                            Detail
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