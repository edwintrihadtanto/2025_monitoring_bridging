            <footer>
                <div class="footer-new">
                    <div class="footer clearfix mb-0 text-muted">
                        <div class="float-start" style="bottom: 0; /*position: fixed;*/">
                            <p>2025-<?php echo date('Y');?> &copy; ITISI - RSSM</p>
                        </div>
                        <div class="float-end" style="bottom: 0; right: 0; /*position: fixed;*/">
                            <p>Crafted with <span class="text-danger"><i class="bi bi-heart-fill icon-mid"></i></span>
                                by <a href="https://www.instagram.com/edwiinbyonicmadiun" target="_blank"><i class="bi bi-instagram icon-mid"></i> Edwiin</a></p>
                        </div>
                    </div>
                </div>
            </footer>

            <button id="btnToTop"
                    class="btn"
                    title="Kembali ke atas"
                    aria-label="Back to top">
                <!-- <i class="bi bi-arrow-up"></i> -->
                <img src="rocket.gif" alt="Kembali ke atas" width="140%" height="160%" />
            </button>
        </div>
        <!-- END MAIN -->

    </div>
    
    <script src="<?= base_url('assets/dist/assets/static/js/components/dark.js'); ?>"></script>
    <script src="<?= base_url('assets/dist/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js'); ?>"></script>
    <script src="<?= base_url('assets/dist/assets/compiled/js/app.js'); ?> "></script>
    
    <script src="<?= base_url('assets/dist/assets/extensions/jquery/jquery.min.js') ?> "></script>
    <script src="<?= base_url('assets/dist/assets/extensions/datatables.net/js/jquery.dataTables.min.js') ?> "></script>
    <script src="<?= base_url('assets/dist/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js')?> "></script>
    <script src="<?= base_url('assets/dist/assets/extensions/sweetalert2/sweetalert2.min.js')?>"></script>
    <script src="<?= base_url('assets/dist/assets/extensions/sweetalert2/sweetalert2.js')?>"></script>
    <script src="<?= base_url('assets/dist/assets/extensions/apexcharts/apexcharts.min.js')?>"></script>
    <script src="<?= base_url('assets/dist/assets/extensions/apexcharts/apexcharts.js')?>"></script>
    <script src="<?= base_url('assets/dist/assets/extensions/toastify-js/src/toastify.js')?>"></script>
    

    <script src="<?= base_url('js/page-dashboard.js') ?>"></script>
    <script src="<?= base_url('js/page-pasien.js') ?>"></script>
    <script src="<?= base_url('js/page-sep.js') ?>"></script>
    <script src="<?= base_url('js/page-monitoring.js') ?>"></script>
    <script src="<?= base_url('js/page-insert.js') ?>"></script>
    <script src="<?= base_url('js/page-simrs.js') ?>"></script>
    <script src="<?= base_url('js/page-koneksi.js') ?>"></script>

    <script>

        document.querySelectorAll('.has-sub > .sidebar-link').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();

                let parent = this.parentElement;

                document.querySelectorAll('.sidebar-item.has-sub').forEach(el => {
                    if (el !== parent) el.classList.remove('active');
                });

                parent.classList.toggle('active');
            });
        });

        /* Auto active dari URL */
        const current = window.location.href;
        document.querySelectorAll('.submenu-link').forEach(link => {
            if (current.includes(link.getAttribute('href'))) {
                link.closest('.sidebar-item').classList.add('active');
            }
        });
        const Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
          }
        })

        window.AppToast = function ({
            text = '',
            type = 'success',
            duration = 5000,
            position = 'left',
            gravity = 'top'
        } = {}) {

            let backgroundColor = '#4fbe87';

            switch (type) {

                case 'success':
                    backgroundColor = '#198754';
                    break;

                case 'error':
                    backgroundColor = '#dc3545';
                    break;

                case 'warning':
                    backgroundColor = '#ffc107';
                    break;

                case 'info':
                    backgroundColor = '#0dcaf0';
                    break;

                default:
                    backgroundColor = '#6c757d';
            }

            Toastify({
                text,
                duration,
                close: true,
                gravity,
                position,
                stopOnFocus: true,
                style: {
                    background: backgroundColor,
                    borderRadius: '8px',
                    fontSize: '13px'
                }
            }).showToast();
        };

        // --- 1. GLOBAL VARIABLES (Agar bisa diakses semua fungsi) ---
        let detailModalInstance = null;
        let isDPHOLoaded = false;
        let lastFocusedElement = null;        
        
        document.body.setAttribute('tabindex', '-1');
        //khusus modal ketika di open dan close
        window.addEventListener('show.bs.modal', () => {
            lastFocusedElement = document.activeElement;
        }, true);

        window.addEventListener('hide.bs.modal', (e) => {
            e.target.querySelector(':focus')?.blur();
        }, true);
        
        window.addEventListener('hidden.bs.modal', () => {
            lastFocusedElement?.focus();
            lastFocusedElement = null;
        }, true);

        window.showModal = function(modalId) {
            const modalEl = document.getElementById(modalId);
            if (!modalEl) return;

            if (typeof bootstrap !== 'undefined') {
                if (!detailModalInstance) {
                    detailModalInstance = new bootstrap.Modal(modalEl);
                }
                detailModalInstance.show();
            } else if (typeof $ !== 'undefined' && $.fn.modal) {
                $(modalEl).modal('show');
            }
        }
        // end modal
        
        window.updateActiveMenu = function(linkElement) {
            document.querySelectorAll('.sidebar-item').forEach(item => item.classList.remove('active'));
            if (linkElement) {
                const parent = linkElement.closest('.sidebar-item');
                if (parent) parent.classList.add('active');
            }
        }


        window.loadPageContent = function(url, pageIdentifier, clickedLinkElement = null) {
            const mainContent = document.getElementById('main-content');
            if(mainContent) {
                // mainContent.innerHTML = '<div class="text-center p-5"><div class="spinner-grow text-info" role="status"></div><h5>sedang memuat halaman...</h5></div>';
                mainContent.innerHTML = '<div class="text-center p-5"><img src="<?= base_url('loading.svg'); ?>" class="me-4" style="width: 3rem" alt="audio"><h6>sedang memuat halaman...</h6></div>';
            }

            // DESTROY halaman aktif (jika ada)
            if (window.PageDashboard?.destroy) {
                window.PageDashboard.destroy();
            }
            window.__dashboardInitialized = false;
            // RESET FLAG JIKA PINDAH HALAMAN
            if (pageIdentifier !== 'dpho') {
                isDPHOLoaded = false;
            }

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.text();
            })
            .then(html => {                
                if (html.includes('id="auth"')) {                    
                    window.location.href = url; 
                    return;
                }
                
                mainContent.innerHTML = html;
                history.pushState({page: pageIdentifier}, '', url);

                // Highlight Menu
                if (clickedLinkElement && clickedLinkElement.classList.contains('sidebar-link')) {
                    window.updateActiveMenu(clickedLinkElement);
                } else {
                    const baseUrl = url.split('?')[0];
                    const sidebarLink = document.querySelector(`.sidebar-link[href="${baseUrl}"]`);
                    window.updateActiveMenu(sidebarLink);
                }

                // Reinitialize Komponen
                window.initializePageComponents();
            })
            .catch(error => {
                console.error('Error:', error);
                if(mainContent) {
                    mainContent.innerHTML = '<div class="alert alert-danger">Gagal memuat halaman.</div>';
                }
            });
        }
        
        window.setupEntriesPerPage = function() {
            const selectElement = document.getElementById('entriesPerPage');
            
            if (selectElement) {
                const newSelect = selectElement.cloneNode(true);
                selectElement.parentNode.replaceChild(newSelect, selectElement);

                newSelect.addEventListener('change', function(e) {
                    const perPage = e.target.value;
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('perPage', perPage);
                    currentUrl.searchParams.set('page', 1);
                                        
                    window.loadPageContent(currentUrl.toString(), 'monitoring', null);
                });
            }
        }

        window.initializePageComponents = function() {
            if (typeof $ !== 'undefined' && $.fn.DataTable) {
                
                const $tables = $('.datatable');
                
                if ($tables.length > 0) {
                    $tables.each(function() {
                        const $table = $(this);
                        const isObatTable = $table.attr('id') === 'table-monitoring-obat';
                        const isDPHOTable = $table.attr('id') === 'table-dpho';

                        if (!$table.hasClass('dataTable')) {
                            try {
                               
                                const config = {
                                    searching: true,
                                    info: true,
                                    ordering: true,
                                    responsive: true,
                                    language: {
                                        search: "Cari:",
                                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                                        infoEmpty: "Tidak ada data ditemukan",
                                        zeroRecords: "Data tidak ditemukan"
                                    }
                                };
                                
                                if ((isObatTable)||(isDPHOTable)) {                                                                        
                                    config.paging = true;
                                    config.lengthChange = true;
                                    config.pageLength = 15;
                                } else {
                                    config.paging = false;
                                    config.lengthChange = false;
                                }

                                const dataTable = $table.DataTable(config);
                                $table.dataTable = dataTable;

                                
                                if (!isObatTable) {
                                    var api = dataTable;
                                    var tableId = api.table().node().id;
                                    console.log(tableId+' footer');                                    
                                    var currentPerPage = $('#' + tableId).data('current-perpage') || 10;
                                    var dropdownHtml = `
                                        <div class="d-flex align-items-center">
                                            <span class="me-2" style="font-size: 0.9rem;">Tampilkan:</span>
                                            <select id="entriesPerPage" class="form-select w-auto" style="width: 80px !important;">
                                                <option value="10" ${currentPerPage == 10 ? 'selected' : ''}>10</option>
                                                <option value="15" ${currentPerPage == 15 ? 'selected' : ''}>15</option>
                                                <option value="25" ${currentPerPage == 25 ? 'selected' : ''}>25</option>
                                                <option value="50" ${currentPerPage == 50 ? 'selected' : ''}>50</option>
                                                <option value="100" ${currentPerPage == 100 ? 'selected' : ''}>100</option>
                                                <option value="0" ${currentPerPage == 0 ? 'selected' : ''}>Semua</option>
                                            </select>
                                        </div>
                                    `;
                                    
                                    $('#' + tableId + '_wrapper .row:first .col-md-6:first').html(dropdownHtml);
                                    if ($('#custom-pagination-container').length > 0) {
                                        $('#custom-pagination-container')
                                            .removeClass('d-none')
                                            .addClass('d-flex justify-content-end')
                                            .appendTo('#' + tableId + '_wrapper .row:last .col-md-7');
                                    }
                                    
                                    window.setupEntriesPerPage();
                                }
                            } catch (err) {
                                console.error("Gagal inisialisasi DataTables:", err);
                            }
                        }
                    });
                }
            }

            const pasienForm            = document.getElementById('pencarianPasienForm');
            const pasienSEPForm         = document.getElementById('pencarianSEPPasienForm');
            const monitoringklaimForm   = document.getElementById('pencarianMonitoringKlaimForm');
            const faskesForm            = document.getElementById('pencarianFasKesForm');
            const apotikForm            = document.getElementById('pencarianApotikForm');
            const poliForm              = document.getElementById('pencarianPoliForm');
            const dphoForm              = document.getElementById('loadHalamanDPHO');
            const obatForm              = document.getElementById('pencarianObatForm');
            const spesialisForm         = document.getElementById('loadHalamanSpesialis');
            const listResepForm         = document.getElementById('pencarianListResepForm');
            const pelObatList           = document.getElementById('pencarianListPelyananObatForm');
            const pelObatRiwayat        = document.getElementById('pencarianRiwayatPelyananObatForm');
            const simrsResep            = document.getElementById('pencarianResepSIMRS');
            const koneksiMonitor        = document.getElementById('koneksi-monitor');
             
            if (pasienForm) {
                
                if (typeof initPasienPage === 'function') {
                    initPasienPage();
                }
            }

            if (pasienSEPForm) {
                
                if (typeof initPasienSepPage === 'function') {
                    initPasienSepPage();
                }
            }

            if (monitoringklaimForm) {
                
                if (typeof initMonitoringKlaimPage === 'function') {
                    initMonitoringKlaimPage();
                }
            }

            if ((faskesForm)||(apotikForm)||(poliForm)||(obatForm)) {
                
                if (typeof initReferensiPage === 'function') {
                    initReferensiPage();
                }
            }

            if (dphoForm && !isDPHOLoaded) {
                if (typeof initDphoPage === 'function') {
                    initDphoPage();
                }
            }

            if (spesialisForm) {
                if (typeof initSpesialisPage === 'function') {
                    initSpesialisPage();
                }
            }

            if (listResepForm) {
                if (typeof initListResepPage === 'function') {
                    initListResepPage();
                }
            }

            if (pelObatList) {
                if (typeof initListPelyananObatPage === 'function') {
                    initListPelyananObatPage();
                }
            }

            if (pelObatRiwayat) {
                if (typeof initRiwayatPelyananObatPage === 'function') {
                    initRiwayatPelyananObatPage();

                }
            }

            if (simrsResep) {
                if (typeof initSIMRS === 'function') {
                    initSIMRS();
                }
            }

            if (koneksiMonitor) {
                if (typeof initKoneksiPage === 'function') {
                    initKoneksiPage();
                }
            }

            const dashboardEl = document.getElementById('dashboard-container');
            if (dashboardEl) {
                window.PageDashboard?.init();
            } else {
                window.PageDashboard?.destroy();
            }

            // const loadHalamanDPHO = document.getElementById('loadHalamanDPHO');            
            // const dphoTable = document.getElementById('table-dpho');
            
            // // Jika ada TRIGGER ATAU TABEL, jalankan init
            // if (loadHalamanDPHO || (dphoTable)) {
                
            //     if (typeof initDPHOPage === 'function') {
            //         initDPHOPage();
            //     } else {
            //         console.error(">>> ERROR: Fungsi initDPHOPage tidak ditemukan atau bukan global.");
            //     }
            // }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            
            document.body.addEventListener('click', function(e) {
                // Tombol Detail
                const btnDetail = e.target.closest('.btn-detail-log');
                const copybtn = e.target.closest('.copy-btn');
                if (btnDetail) {
                    e.preventDefault();
                    const modalEl = document.getElementById('detailModal');
                    if (modalEl) {
                        document.getElementById('modalRequest').textContent = btnDetail.getAttribute('data-request');
                        document.getElementById('modalRequestBody').textContent = btnDetail.getAttribute('data-requestbody');
                        document.getElementById('modalResponse').textContent = btnDetail.getAttribute('data-response');                        
                        window.showModal('detailModal'); // Panggil global function
                    }
                    return;
                }

                if (copybtn) {
                    const targetId = copybtn.dataset.target;
                    const targetEl = document.getElementById(targetId);

                    if (!targetEl) return;

                    navigator.clipboard.writeText(targetEl.textContent).then(() => {
                        copybtn.innerHTML = '<i class="bi bi-check"></i>';
                    }).catch(() => {
                        copybtn.innerHTML = '<i class="bi bi-x"></i>';
                    });

                    // efek visual
                    copybtn.innerHTML = '<i class="bi bi-check"></i>';
                    setTimeout(() => {
                        copybtn.innerHTML = '<i class="bi bi-clipboard"></i>';
                    }, 1500);
                }
                // Link Navigation
                const link = e.target.closest('a');
                if (!link) return;
                const isInternal = link.hostname === window.location.hostname;
                const isHash = link.getAttribute('href')?.startsWith('#');
                const isBlank = link.target === '_blank';
                const isNoAjax = link.classList.contains('no-ajax');

                const isJSLink = link.getAttribute('href')?.startsWith('javascript');

                /*
                if (isInternal && !isHash && !isBlank && !isNoAjax && !isJSLink) {
                    e.preventDefault();
                    window.loadPageContent(link.href, link.getAttribute('data-page') || link.href, link);
                }
                */
                if (isInternal && !isHash && !isBlank && !isNoAjax && !isJSLink) {
                    e.preventDefault();

                    const page = link.getAttribute('data-page');
                    
                    if (link.dataset.closeSidebar === 'true') {
                        
                        const btn = document.querySelector('.sidebar-hide');
                        if (btn) btn.click();
                    }

                    if (page === 'dpho' && link.dataset.reload === 'true') {
                        isDPHOLoaded = false;
                    }

                    // console.log('PAGE:', page);
                    // console.log('CLOSE SIDEBAR:', link.dataset.closeSidebar);
                    
                    window.loadPageContent(link.href, page || link.href, link);
                }
            });

            document.body.addEventListener('submit', function(e) {
                const form = e.target.closest('form');
                if (!form) return;

                // Jika ini FORM PROFILE (Ganti Password)
                if (form.id === 'changePasswordForm') {
                    e.preventDefault(); // Stop reload halaman
                    
                    const btnSubmit = form.querySelector('button[type="submit"]');
                    const originalBtnText = btnSubmit.innerHTML;
                    
                    // Loading State pada Tombol
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';

                    // Ambil data form
                    const formData = new FormData(form);
                    
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.json())
                    .then(data => {
                        const alertContainer = document.getElementById('alert-container');
                        
                        if (data.status) {
                            // SUKSES
                            alertContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                            // Reset form
                            form.reset();
                        } else {
                            // GAGAL
                            alertContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                        }
                    })
                    .catch(error => {
                        const alertContainer = document.getElementById('alert-container');
                        alertContainer.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan sistem.</div>`;
                        console.error('Error:', error);
                    })
                    .finally(() => {
                        // Kembalikan tombol ke kondisi normal
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = originalBtnText;
                    });
                
                }else if (form.id === 'pencarianPasienForm') {
                    if (typeof handlePasienSubmit === 'function') {
                        handlePasienSubmit(e, form);
                    }
                }else if (form.id === 'pencarianSEPPasienForm') {
                    if (typeof handleSEPPasienSubmit === 'function') {
                        handleSEPPasienSubmit(e, form);
                    }
                }else if (form.id === 'pencarianMonitoringKlaimForm') {
                    if (typeof handlemonitoringklaim === 'function') {
                        handlemonitoringklaim(e, form);
                    }
                }else if (form.id === 'pencarianFasKesForm') {
                    if (typeof handleFaskesPage === 'function') {
                        handleFaskesPage(e, form);
                    }
                }else if (form.id === 'pencarianApotikForm') {
                    if (typeof handleApotikSubmit === 'function') {
                        handleApotikSubmit(e, form);
                    }
                }else if (form.id === 'pencarianPoliForm') {
                    if (typeof handlePoliklinikSubmit === 'function') {
                        handlePoliklinikSubmit(e, form);
                    }
                }else if (form.id === 'pencarianObatForm') {
                    if (typeof handleObatSubmit === 'function') {
                        handleObatSubmit(e, form);
                    }
                }else if (form.id === 'pencarianListResepForm') {
                    if (typeof handleListResepSubmit === 'function') {
                        handleListResepSubmit(e, form);
                    }
                }else if (form.id === 'DeleteResepForm') {
                    if (typeof handleDeleteResepSubmit === 'function') {
                        handleDeleteResepSubmit(e, form);
                    }
                }else if (form.id === 'DeleteItemObat') {
                    if (typeof handleDeleteItemObatSubmit === 'function') {
                        handleDeleteItemObatSubmit(e, form);
                    }
                }else if (form.id === 'pencarianListPelyananObatForm') {
                    if (typeof handleListPelyananObatSubmit === 'function') {
                        handleListPelyananObatSubmit(e, form);
                    }
                }else if (form.id === 'pencarianRiwayatPelyananObatForm') {
                    if (typeof handleRiwayatPelyananObatSubmit === 'function') {
                        handleRiwayatPelyananObatSubmit(e, form);
                    }
                }else if (form.id === 'pencarianResepSIMRS') {
                    if (typeof handleResepSIMRSSubmit === 'function') {
                        handleResepSIMRSSubmit(e, form);
                    }
                }
            });
            
            // Handle Back Button
            window.addEventListener('popstate', function(e) {
                if (e.state && e.state.page) {
                    window.loadPageContent(window.location.href, e.state.page, null);
                } else {
                    window.location.reload();
                }
            });
        });

    </script>
</body>
</html>
