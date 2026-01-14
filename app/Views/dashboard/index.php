<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Dashboard</h3>
                <p class="text-subtitle text-muted">Selamat datang di Dashboard.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <!-- <div class="card-header">
                <h4 class="card-title">Tentang Dashboard</h4>
            </div> -->
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-12 col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Bridging</h6>
                                <h2 class="text-white mb-0"><?= number_format(0, 0, ',', '.') ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card bg-secondary text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Resep</h6>
                                <h2 class="text-white mb-0"><?= number_format(0, 0, ',', '.') ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="card-title">Keberhasilan Bridging</h6>
                                <h2 class="text-white mb-0"><?= number_format(0, 0, ',', '.') ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h6 class="card-title">Gagal Bridging</h6>
                                <h2 class="text-white mb-0"><?= number_format(0, 0, ',', '.') ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<script>
    var btnclosesidebar = document.getElementById("navbarCollapse"); 
     btnclosesidebar.click();
</script>