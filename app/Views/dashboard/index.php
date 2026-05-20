<div id="dashboard-container" class="dashboard-bpjs container-fluid py-4">

    <!-- HEADER -->
    <section class="dashboard-header card">
        <div class="card-body d-flex justify-content-between align-items-center">

            <div>
                <h3 class="title">Dashboard Bridging Farmasi BPJS</h3>
                <span class="subtitle">
                    Monitoring Integrasi Resep SIMRS ↔ BPJS
                </span>
            </div>

            <div>
                <span class="badge bg-light-primary">
                    <i class="bi bi-wifi"></i>
                    API Connected
                </span>
            </div>

        </div>
    </section>

    <section class="row g-4 kpi-section">

        <?php foreach ($rekap as $jenis => $r): ?>

            <div class="col-12">
                <h5 class="fw-bold mb-3">
                    <?= esc($jenis) ?>
                </h5>
            </div>

            <!-- TOTAL -->
            <div class="col-xl-2 col-md-4 col-6">

                <div class="card kpi-card border-0 shadow-sm">

                    <div class="card-body">

                        <div class="kpi-info">
                            <span class="kpi-title">Total Resep</span>

                            <h3>
                                <?= number_format($r['total_resep']) ?>
                            </h3>

                            <small>Hari ini</small>
                        </div>

                        <div class="btn icon btn-lg bg-primary kpi-icon">
                            <i class="bi bi-receipt"></i>
                        </div>


                    </div>

                </div>

            </div>



            <!-- SUCCESS -->
            <div class="col-xl-2 col-md-4 col-6">

                <div class="card kpi-card border-0 shadow-sm">

                    <div class="card-body">

                        <div class="kpi-info">
                            <span class="kpi-title">Success</span>

                            <h3 class="text-success">
                                <?= number_format($r['success']) ?>
                            </h3>

                            <small>Bridging sukses</small>
                        </div>

                        <div class="btn icon btn-lg bg-success kpi-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>

                    </div>

                </div>

            </div>



            <!-- WARNING -->
            <div class="col-xl-2 col-md-4 col-6">

                <div class="card kpi-card border-0 shadow-sm">

                    <div class="card-body">

                        <div class="kpi-info">
                            <span class="kpi-title">Warning</span>

                            <h3 class="text-warning">
                                <?= number_format($r['warning']) ?>
                            </h3>

                            <small>Warning BPJS</small>
                        </div>

                        <div class="btn icon btn-lg bg-warning kpi-icon">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>

                    </div>

                </div>

            </div>



            <!-- FAILED -->
            <div class="col-xl-2 col-md-4 col-6">

                <div class="card kpi-card border-0 shadow-sm">

                    <div class="card-body">

                        <div class="kpi-info">
                            <span class="kpi-title">Failed</span>

                            <h3 class="text-danger">
                                <?= number_format($r['failed']) ?>
                            </h3>

                            <small>Error API</small>
                        </div>

                        <div class="btn icon btn-lg bg-danger kpi-icon">
                            <i class="bi bi-x-circle"></i>
                        </div>

                    </div>

                </div>

            </div>



            <!-- PENDING -->
            <div class="col-xl-2 col-md-4 col-6">

                <div class="card kpi-card border-0 shadow-sm">

                    <div class="card-body">

                        <div class="kpi-info">
                            <span class="kpi-title">Pending</span>

                            <h3 class="text-secondary">
                                <?= number_format($r['pending']) ?>
                            </h3>

                            <small>Menunggu proses</small>
                        </div>

                        <div class="btn icon btn-lg bg-secondary kpi-icon">
                            <i class="bi bi-clock-history"></i>
                        </div>

                    </div>

                </div>

            </div>



            <!-- SUCCESS RATE -->
            <div class="col-xl-2 col-md-4 col-6">

                <div class="card kpi-card border-0 shadow-sm">

                    <div class="card-body">

                        <div class="kpi-info">
                            <span class="kpi-title">Success Rate</span>

                            <h3 class="text-primary">
                                <?= $r['success_rate'] ?>%
                            </h3>

                            <small>Kinerja bridging</small>
                        </div>

                        <div class="btn icon btn-lg bg-info kpi-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </section>


    <!-- CHARTS -->
    <section class="row g-4 mb-4">

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-bold">
                        Trend Bridging BPJS
                    </h5>
                </div>

                <div class="card-body">
                    <div id="chartResep"></div>
                </div>

            </div>

        </div>



        <div class="col-lg-4">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-bold">
                        Distribusi Status
                    </h5>
                </div>

                <div class="card-body">
                    <div id="chartStatus"></div>
                </div>

            </div>

        </div>

    </section>



    <!-- ERROR TABLE -->
    <section class="row">

        <div class="col-12">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white border-0 d-flex justify-content-between">

                    <h5 class="fw-bold mb-0">
                        Error Bridging BPJS
                    </h5>

                    <span class="badge bg-danger">
                        Realtime Monitoring
                    </span>

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle">

                            <thead class="table-light">

                                <tr>
                                    <th>Waktu</th>
                                    <th>Endpoint</th>
                                    <th>No Resep</th>
                                    <th>Response</th>
                                    <th>Status</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php if (!empty($errors)): ?>

                                    <?php foreach ($errors as $e): ?>

                                        <tr>

                                            <td>
                                                <?= date('H:i:s', strtotime($e['created_at'])) ?>
                                            </td>

                                            <td>
                                                <span class="badge bg-light-primary text-primary">
                                                    <?= esc($e['jenis_endpoint']) ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?= esc($e['noresep']) ?>
                                            </td>

                                            <td>

                                                <small class="text-danger">
                                                    <?= esc($e['response_message']) ?>
                                                </small>

                                            </td>

                                            <td>

                                                <span class="badge bg-danger">
                                                    <?= $e['response_code'] ?>
                                                </span>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            Tidak ada error hari ini
                                        </td>
                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </section>

</div>



<style>

body{
    background:#f4f7fb;
}

.bg-gradient-primary{
    background:linear-gradient(135deg,#2563eb,#1e40af);
}

.dashboard-card{
    border-radius:18px;
    transition:.3s;
    overflow:hidden;
}

.dashboard-card:hover{
    transform:translateY(-5px);
}

.icon-box{
    width:55px;
    height:55px;
    border-radius:15px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:24px;
}

.card{
    border-radius:20px;
}

.table td,
.table th{
    vertical-align:middle;
}

.progress{
    border-radius:20px;
}

.progress-bar{
    border-radius:20px;
}

</style> 