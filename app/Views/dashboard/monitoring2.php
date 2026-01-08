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
                                    <th width="150">Waktu</th>
                                    <th width="550">Endpoint</th>
                                    <th width="15">Method</th>
                                    <th width="150">Response Code</th>
                                    <th width="20">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?= $log['created_at'] ?></td>
                            
                                    <td><span class="d-inline-block text-truncate" style="" title="<?= $log['endpoint'] ?>"><?= $log['endpoint'] ?></span></td>
                                    
                                    <td><?= $log['method'] ?></td>
                                    <td>
                                        <?php if ($log['response_code'] == 200): ?>
                                            <span class="badge bg-success">200</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><?= $log['response_code'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info btn-detail-log" 
                                                data-request='<?= htmlspecialchars($log['request_header'] . "\n" . $log['request_body'], ENT_QUOTES, 'UTF-8') ?>'
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