            <footer>
                <div class="footer-new">
                    <div class="footer clearfix mb-0 text-muted">
                        <div class="float-start" style="bottom: 0; /*position: fixed;*/">
                            <p>2025 &copy; ITISI - RSSM</p>
                        </div>
                        <div class="float-end" style="bottom: 0; right: 0; /*position: fixed;*/">
                            <p>Crafted with <span class="text-danger"><i class="bi bi-heart-fill icon-mid"></i></span>
                                by <a href="https://saugi.me">Edwin</a></p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- END MAIN -->

    </div>
    
    <script src="<?= base_url('public/assets/dist/assets/static/js/components/dark.js'); ?>"></script>
    <script src="<?= base_url('public/assets/dist/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js'); ?>"></script>
    <script src="<?= base_url('public/assets/dist/assets/compiled/js/app.js'); ?> "></script>
    
    <script src="<?= base_url('public/assets/dist/assets/extensions/jquery/jquery.min.js') ?> "></script>
    <script src="<?= base_url('public/assets/dist/assets/extensions/datatables.net/js/jquery.dataTables.min.js') ?> "></script>
    <script src="<?= base_url('public/assets/dist/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js')?> "></script>

    <script src="<?= base_url('public/js/page-pasien.js') ?>"></script>
    <script src="<?= base_url('public/js/page-sep.js') ?>"></script>
    <script src="<?= base_url('public/js/page-monitoring.js') ?>"></script>

    <script>
        // --- 1. GLOBAL VARIABLES (Agar bisa diakses semua fungsi) ---
        let detailModalInstance = null;

        // --- 2. DEFINISI FUNGSI UTAMA (Dipindahkan ke Luar / Global Scope) ---

        // Helper Modal
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

        // Helper Update Active Menu
        window.updateActiveMenu = function(linkElement) {
            document.querySelectorAll('.sidebar-item').forEach(item => item.classList.remove('active'));
            if (linkElement) {
                const parent = linkElement.closest('.sidebar-item');
                if (parent) parent.classList.add('active');
            }
        }

        // Fungsi Load Content Utama (Sekarang Global)
        window.loadPageContent = function(url, pageIdentifier, clickedLinkElement = null) {
            const mainContent = document.getElementById('main-content');
            if(mainContent) {
                // mainContent.innerHTML = '<div class="text-center p-5"><div class="spinner-grow text-info" role="status"></div><h5>sedang memuat halaman...</h5></div>';
                mainContent.innerHTML = '<div class="text-center p-5"><img src="<?= base_url('public/loading.svg'); ?>" class="me-4" style="width: 3rem" alt="audio"><h6>sedang memuat halaman...</h6></div>';
            }
            
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.text();
            })
            .then(html => {
                // --- TAMBAHKAN LOGIKA CEK LOGIN INI ---
                // Jika HTML yang dimuat mengandung id="auth" (ada di view Login),
                // berarti user terlempar ke halaman login karena session habis.
                if (html.includes('id="auth"')) {
                    // Paksa reload halaman secara penuh ke URL tersebut (Login)
                    window.location.href = url; 
                    return; // Stop proses agar tidak dimasukkan ke main-content
                }
                // ----------------------------------------------

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

        // Setup Entries Per Page (Sekarang Bisa Panggil loadPageContent)
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
                    
                    // Panggil fungsi GLOBAL loadPageContent
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
                                    /*,initComplete: function() {
                                        var api = this.api();
                                        var tableId = api.table().node().id; // Contoh: 'table1'
                                        console.log(tableId);
                                        // --- A. Handle Top Row (Dropdown Custom) ---
                                        var currentPerPage = $('#' + tableId).data('current-perpage') || 10;
                                        var dropdownHtml = `
                                            <div class="d-flex align-items-center">
                                                <span class="me-2" style="font-size: 0.9rem;">Tampilkan:</span>
                                                <select id="entriesPerPage" class="form-select w-auto" style="width: 80px !important;">
                                                    <option value="10" ${currentPerPage == 10 ? 'selected' : ''}>10</option>
                                                    <option value="25" ${currentPerPage == 25 ? 'selected' : ''}>25</option>
                                                    <option value="50" ${currentPerPage == 50 ? 'selected' : ''}>50</option>
                                                    <option value="100" ${currentPerPage == 100 ? 'selected' : ''}>100</option>
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
                                    }*/
                                };
                                
                                if (isObatTable) {                                    
                                    // Karena datanya Hardcode/API Result, kita gunakan PAGING DATATABLES
                                    config.paging = true;
                                    config.lengthChange = true;
                                    config.pageLength = 10;
                                } else {                                    
                                    // Pakai PAGING PHP (Server-Side), MATIKAN PAGING DATATABLES
                                    config.paging = false;
                                    config.lengthChange = false;
                                }

                                const dataTable = $table.DataTable(config);
                                $table.dataTable = dataTable;

                                
                                if (!isObatTable) {
                                    var api = dataTable;
                                    var tableId = api.table().node().id;
                                    console.log(tableId);                                    
                                    var currentPerPage = $('#' + tableId).data('current-perpage') || 10;
                                    var dropdownHtml = `
                                        <div class="d-flex align-items-center">
                                            <span class="me-2" style="font-size: 0.9rem;">Tampilkan:</span>
                                            <select id="entriesPerPage" class="form-select w-auto" style="width: 80px !important;">
                                                <option value="10" ${currentPerPage == 10 ? 'selected' : ''}>10</option>
                                                <option value="25" ${currentPerPage == 25 ? 'selected' : ''}>25</option>
                                                <option value="50" ${currentPerPage == 50 ? 'selected' : ''}>50</option>
                                                <option value="100" ${currentPerPage == 100 ? 'selected' : ''}>100</option>
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
            const apotikForm            = document.getElementById('pencarianFasKesForm');
            const poliForm              = document.getElementById('pencarianPoliForm');
            const dphoForm              = document.getElementById('pencarianDPHOForm');
            const obatForm              = document.getElementById('pencarianObatForm');
            
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

            if ((faskesForm)||(apotikForm)||(poliForm)||(dphoForm)||(obatForm)) {
                
                if (typeof initReferensiPage === 'function') {
                    initReferensiPage();
                }
            }
        }

        // --- 3. DOM CONTENT LOADED (Hanya untuk menempel Event Listener Awal) ---
        document.addEventListener('DOMContentLoaded', function() {
            
            // Event Delegation Link Klik
            document.body.addEventListener('click', function(e) {
                // Tombol Detail
                const btnDetail = e.target.closest('.btn-detail-log');
                if (btnDetail) {
                    e.preventDefault();
                    const modalEl = document.getElementById('detailModal');
                    if (modalEl) {
                        document.getElementById('modalRequest').textContent = btnDetail.getAttribute('data-request');
                        document.getElementById('modalResponse').textContent = btnDetail.getAttribute('data-response');
                        window.showModal('detailModal'); // Panggil global function
                    }
                    return;
                }

                // Link Navigation
                const link = e.target.closest('a');
                if (!link) return;
                const isInternal = link.hostname === window.location.hostname;
                const isHash = link.getAttribute('href')?.startsWith('#');
                const isBlank = link.target === '_blank';
                const isNoAjax = link.classList.contains('no-ajax');

                const isJSLink = link.getAttribute('href')?.startsWith('javascript');

                if (isInternal && !isHash && !isBlank && !isNoAjax && !isJSLink) {
                    e.preventDefault();
                    window.loadPageContent(link.href, link.getAttribute('data-page') || link.href, link);
                }
            });

            /*document.body.addEventListener('submit', function(e) {
                // Cek apakah form yang disubmit adalah form ganti password
                const form = e.target.closest('#changePasswordForm');
                
                if (form) {
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
                }
            });*/

            // Submit Universal
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
                }else if (form.id === 'pencarianDPHOForm') {
                    if (typeof handleDPHOSubmit === 'function') {
                        handleDPHOSubmit(e, form);
                    }
                }else if (form.id === 'pencarianObatForm') {
                    if (typeof handleObatSubmit === 'function') {
                        handleObatSubmit(e, form);
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


        // --- Khusus untuk Halaman Pencarian Pasien ---
        /*document.body.addEventListener('change', function(e) {
            const radioNIK = document.getElementById('opt_nik');
            const radioKartu = document.getElementById('opt_kartu');
            
            // Cek apakah user mengganti radio button
            if (e.target === radioNIK || e.target === radioKartu) {
                const wrapperNik = document.getElementById('wrapper_nik');
                const wrapperKartu = document.getElementById('wrapper_kartu');
                const hiddenType = document.getElementById('search_type');

                if (radioNIK.checked) {
                    // Pilih NIK
                    hiddenType.value = 'nik';
                    wrapperNik.classList.remove('d-none'); // Tampil
                    wrapperKartu.classList.add('d-none'); // Sembunyi
                    document.getElementById('input_nik').focus();
                } else {
                    // Pilih Kartu
                    hiddenType.value = 'kartu';
                    wrapperNik.classList.add('d-none'); // Sembunyi
                    wrapperKartu.classList.remove('d-none'); // Tampil
                    document.getElementById('input_kartu').focus();
                }
            }
        });

        document.body.addEventListener('submit', function(e) {
            const form = e.target.closest('#pencarianPasienForm');
            
            if (form) {
                e.preventDefault();
                
                const btnSubmit = form.querySelector('button[type="submit"]');
                const resultContainer = document.getElementById('result-container');
                const alertContainer = document.getElementById('alert-container');
                const type = document.getElementById('search_type').value;
                
                // Tentukan value berdasarkan tipe (Ambil dari input yang terlihat)
                let value = '';
                if (type === 'nik') {
                    value = document.getElementById('input_nik').value;
                } else {
                    value = document.getElementById('input_kartu').value;
                }

                // Validasi Sederhana di JS
                if(!value) {
                    alert('Mohon isi data pencarian terlebih dahulu.');
                    return;
                }

                // Buat FormData Manual agar nilai search_value terkirim dengan benar
                const formData = new FormData();
                formData.append('search_type', type);
                formData.append('search_value', value);
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                // UI Loading
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mencari...';
                resultContainer.innerHTML = '';
                alertContainer.innerHTML = '';

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="bi bi-search me-2"></i> Cari Data Pasien';

                    if (data.status) {
                        resultContainer.innerHTML = data.html;
                    } else {
                        alertContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="bi bi-search me-2"></i> Cari Data Pasien';
                    alertContainer.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan sistem.</div>`;
                });
            }
        });*/
    </script>
</body>
</html>