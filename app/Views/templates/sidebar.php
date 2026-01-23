<div id="sidebar">
    <div class="sidebar-wrapper active d-flex flex-column">
        <div class="sidebar-header position-relative p-0">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo logo-animasi">
                    <a href="<?= base_url(); ?>"><img src="<?= base_url('public/img/logo_aplikasi.png'); ?>" alt="Logo" style="height: 7rem;"></a>
                </div>
                <div class="sidebar-toggler  x">
                    <a href="javascript:void(0)" class="sidebar-hide d-xl-none d-block no-ajax"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu flex-grow-1 overflow-auto">
            <ul class="menu p-0">
                <li class="sidebar-title mb-0">Menu</li>
                
                <li class="sidebar-item m-0">
                    <a href="<?= site_url('dashboard') ?>" class='sidebar-link' data-page="dashboard">
                        <i class="bi bi-grid-fill"></i>
                        <span>Halaman Utama</span>
                    </a>
                </li>
                
                <li class="sidebar-item has-sub m-0">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-clipboard2-data"></i>
                        <span>Monitoring</span>
                    </a>
                    
                    <ul class="submenu">
                        <li class="submenu-item">
                            <a href="<?= site_url('monitoring') ?>" class="submenu-link" data-page="monitoring">Logs Result Bridging </a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item has-sub m-0">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-ui-checks"></i>
                        <span>Referensi</span>
                    </a>
                    
                    <ul class="submenu">
                        <li class="submenu-item">
                            <a href="<?= site_url('sidebar-faskes') ?>" class="submenu-link" data-page="faskes">Fasilitas Kesehatan</a>
                        </li>
                        <li class="submenu-item">
                            <a href="<?= site_url('sidebar-apotik') ?>" class="submenu-link" data-page="apotik">Apotik</a>
                        </li>
                        <li class="submenu-item">
                            <a href="<?= site_url('sidebar-poli') ?>" class="submenu-link" data-page="poli">Poli</a>
                        </li>
                        <li class="submenu-item"><!-- Daftar Plafon Harga Obat, -->
                            <a href="<?= site_url('sidebar-dpho') ?>" class="submenu-link" data-page="dpho" data-reload="true">DPHO</a>
                        </li>
                        <li class="submenu-item">
                            <a href="<?= site_url('sidebar-obat') ?>" class="submenu-link" data-page="obat">Obat</a>
                        </li>
                        <li class="submenu-item">
                            <a href="<?= site_url('sidebar-spesialis') ?>" class="submenu-link" data-page="spesialis">Spesialis</a>
                        </li>
                    </ul>
                </li>
                
                <li class="sidebar-item has-sub m-0">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-view-list"></i>
                        <span>Resep</span>
                    </a>
                    
                    <ul class="submenu">
                        <li class="submenu-item">
                            <a href="<?= site_url('sidebar-daftarresep') ?>" class="submenu-link" data-page="listresep">Resep SIM-RS</a>
                        </li>
                        <li class="submenu-item">
                            <a href="<?= site_url('sidebar-daftarresep') ?>" class="submenu-link" data-page="listresep">Daftar Resep</a>
                        </li>
                    </ul>
                </li>
                
                <li class="sidebar-item has-sub m-0">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-view-list"></i>
                        <span>Pelayanan Obat</span>
                    </a>
                    
                    <ul class="submenu">
                        <li class="submenu-item">
                            <a href="<?= site_url('sidebar-pelobat-listpersep') ?>" class="submenu-link" data-page="pelyanan-obat">Daftar Pelayanan Obat</a>
                        </li>
                        <li class="submenu-item">
                            <a href="<?= site_url('sidebar-pelobat-riwayat') ?>" class="submenu-link" data-page="pelyanan-riwayat">Riwayat Pelayanan Obat</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-title mb-0">Lain-Lain</li>
               
                <li class="sidebar-item m-0">
                    <a href="<?= site_url('pasien') ?>" class='sidebar-link' data-page="pasien">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Cek Data Pasien</<span></span>
                    </a>
                </li>
                <li class="sidebar-item m-0">
                    <a href="<?= site_url('seppasien') ?>" class='sidebar-link' data-page="seppasien">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Cek SEP Pasien</span>
                    </a>
                </li>
                <li class="sidebar-item m-0">
                    <a href="<?= site_url('monitoringklaim') ?>" class='sidebar-link' data-page="monitoringklaim">
                        <i class="bi bi-clipboard-pulse"></i>
                        <span>Cek Monitoring Klaim</span>
                    </a>
                </li>
                
            </ul>
        </div>
        <div class="sidebar-footer">
            <div class="theme-toggle d-flex gap-2 m-2 align-items-center justify-content-center">
                <img src="<?= base_url('public/img/lightmode.png'); ?>" style="width:20px">

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="toggle-dark" style="cursor:pointer">
                </div>

                <img src="<?= base_url('public/img/darkmode.png'); ?>" style="width:20px">
            </div>
        </div>
    </div>
</div>