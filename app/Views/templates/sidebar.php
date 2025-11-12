<!-- SIDEBAR -->
<div id="sidebar">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    <a href="<?= site_url('dashboard') ?>"><img src="" alt="Logo" srcset=""></a>
                </div>
                <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                    <div class="form-check form-switch fs-6">
                        <input class="form-check-input  me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                        <label class="form-check-label"></label>
                    </div>
                </div>
                <div class="sidebar-toggler  x">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu</li>
                
                <li class="sidebar-item">
                    <a href="<?= site_url('dashboard') ?>" class='sidebar-link' data-page="dashboard">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-stack"></i>
                        <span>Components</span>
                    </a>
                    
                    <ul class="submenu">
                        <li class="submenu-item">
                            <a href="<?= site_url('components/accordion') ?>" class="submenu-link" data-page="accordion">Accordion</a>        
                        </li>
                    </ul>
                </li>
                
                <li class="sidebar-title">Forms &amp; Tables</li>
                
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-hexagon-fill"></i>
                        <span>Form Elements</span>
                    </a>
                    
                    <ul class="submenu">
                        <li class="submenu-item">
                            <a href="<?= site_url('forms/input') ?>" class="submenu-link" data-page="input">Input</a>
                        </li>
                    </ul>
                </li>
                
                <li class="sidebar-title">Extra UI</li>
                <li class="sidebar-title">Pages</li>
            </ul>
        </div>
    </div>
</div>
<!-- END SIDEBAR -->