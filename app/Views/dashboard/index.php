<div id="dashboard-container" class="page-heading dashboard-bpjs">

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


    <!-- KPI -->
    <section class="row kpi-section">

        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card">
                <div class="card-body">

                    <div class="kpi-info">
                        <span class="kpi-title">Total Resep</span>
                        <h3>128</h3>
                        <small>Hari ini</small>
                    </div>
                    <a href="" class="btn icon btn-lg btn-primary"><i class="bi bi-receipt"></i></a>
                </div>
            </div>
        </div>


        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card">
                <div class="card-body">

                    <div class="kpi-info">
                        <span class="kpi-title">Berhasil</span>
                        <h3 class="text-success">120</h3>
                        <small>Bridging sukses</small>
                    </div>
                    <a href="" class="btn icon btn-lg btn-success"><i class="bi bi-check-circle"></i></a>
                </div>
            </div>
        </div>


        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card">
                <div class="card-body">

                    <div class="kpi-info">
                        <span class="kpi-title">Gagal</span>
                        <h3 class="text-danger">5</h3>
                        <small>Error API</small>
                    </div>
                    <a href="" class="btn icon btn-lg btn-danger"><i class="bi bi-x-circle"></i></a>
                </div>
            </div>
        </div>


        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card">
                <div class="card-body">

                    <div class="kpi-info">
                        <span class="kpi-title">Pending</span>
                        <h3 class="text-warning">3</h3>
                        <small>Menunggu proses</small>
                    </div>
                    <a href="" class="btn icon btn-lg btn-warning"><i class="bi bi-clock"></i></a>
                </div>
            </div>
        </div>

    </section>



    <!-- CHART -->
    <section class="row">

        <div class="col-lg-8">
            <div class="card dashboard-chart">
                <div class="card-header">
                    Trend Resep BPJS
                </div>

                <div class="card-body">
                    <div id="chartResep"></div>
                </div>
            </div>
        </div>


        <div class="col-lg-4">
            <div class="card dashboard-chart">
                <div class="card-header">
                    Status Bridging
                </div>

                <div class="card-body">
                    <div id="chartStatus"></div>
                </div>
            </div>
        </div>

    </section>



    <!-- SECOND CHART -->
    <section class="row">

        <div class="col-lg-6">
            <div class="card dashboard-chart">
                <div class="card-header">
                    Top Obat BPJS
                </div>

                <div class="card-body">
                    <div id="chartObat"></div>
                </div>
            </div>
        </div>


        <div class="col-lg-6">
            <div class="card dashboard-chart">
                <div class="card-header">
                    Distribusi Resep per Poli
                </div>

                <div class="card-body">
                    <div id="chartPoli"></div>
                </div>
            </div>
        </div>

    </section>



    <!-- ERROR TABLE -->
    <section class="row">

        <div class="col-12">
            <div class="card">

                <div class="card-header">
                    Error Bridging BPJS
                </div>

                <div class="card-body">

                    <table class="table table-hover">

                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>No Resep</th>
                                <th>Error</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr>
                                <td>10:21</td>
                                <td>RX20250321</td>
                                <td>Token Expired</td>
                                <td><span class="badge bg-danger">FAILED</span></td>
                            </tr>

                            <tr>
                                <td>10:35</td>
                                <td>RX20250322</td>
                                <td>Timeout API</td>
                                <td><span class="badge bg-danger">FAILED</span></td>
                            </tr>

                        </tbody>

                    </table>

                </div>
            </div>
        </div>

    </section>

</div>