            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start" style="bottom: 0; position: fixed;">
                        <p>2025 &copy; ITISI - RSSM</p>
                    </div>
                    <div class="float-end" style="bottom: 0; right: 0; position: fixed;">
                        <p>Crafted with <span class="text-danger"><i class="bi bi-heart-fill icon-mid"></i></span>
                            by <a href="https://saugi.me">Edwin</a></p>
                    </div>
                </div>
            </footer>
        </div>
        <!-- END MAIN -->

    </div>
    
    <script src="<?= base_url('public/assets/dist/assets/static/js/components/dark.js'); ?>"></script>
    <script src="<?= base_url('public/assets/dist/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js'); ?>"></script>
    <script src="<?= base_url('public/assets/dist/assets/compiled/js/app.js'); ?> "></script>
    
    <!-- PERTAHANKAN INI -->
    <script src="<?= base_url('public/assets/dist/assets/extensions/jquery/jquery.min.js') ?> "></script>
    <script src="<?= base_url('public/assets/dist/assets/extensions/datatables.net/js/jquery.dataTables.min.js') ?> "></script>
    <script src="<?= base_url('public/assets/dist/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js')?> "></script>

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
                mainContent.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div></div>';
            }
            
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.text();
            })
            .then(html => {
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

        // Initialize Components (Panggil setupEntriesPerPage)
        /*window.initializePageComponents = function() {
            // DataTables Init
            if (typeof $ !== 'undefined' && $.fn.DataTable) {
                const $table = $('#table1');
                if ($table.length > 0) {
                    if (!$table.hasClass('dataTable')) {
                        try {
                            $table.DataTable({
                                paging: false,
                                lengthChange: false,
                                searching: true,
                                info: true,
                                ordering: true,
                                responsive: true,
                                language: {
                                    search: "Cari:",
                                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                                    infoEmpty: "Tidak ada data ditemukan",
                                    infoFiltered: "(disaring dari _MAX_ total data)",
                                    zeroRecords: "Data tidak ditemukan"
                                }
                            });
                        } catch (err) {
                            console.error("Gagal inisialisasi jQuery DataTables:", err);
                        }
                    }
                }
            }
            
            // Panggil fungsi dropdown
            window.setupEntriesPerPage();
        }*/

        /*window.initializePageComponents = function() {
            if (typeof $ !== 'undefined' && $.fn.DataTable) {
                
                // Cari semua tabel dengan class .datatable
                const $tables = $('.datatable');
                
                if ($tables.length > 0) {
                    $tables.each(function() {
                        const $table = $(this);

                        // Cek apakah sudah diinisialisasi
                        if (!$table.hasClass('dataTable')) {
                            try {
                                const dataTable = $table.DataTable({
                                    paging: false,
                                    lengthChange: false,
                                    searching: true,
                                    info: true,
                                    ordering: true,
                                    responsive: true,
                                    language: {
                                        search: "Cari:",
                                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                                        infoEmpty: "Tidak ada data ditemukan",
                                        infoFiltered: "(disaring dari _MAX_ total data)",
                                        zeroRecords: "Data tidak ditemukan"
                                    },
                                    
                                    // --- INI KUNCI PERBAIKANNYA ---
                                    initComplete: function() {
                                        // Ambil ID table (misal: table1)
                                        var api = this.api();
                                        var tableId = api.table().node().id;
                                        
                                        // Ambil nilai perPage saat ini dari atribut data
                                        var currentPerPage = $('#' + tableId).data('current-perpage') || 10;
                                        
                                        // Generate HTML Dropdown dengan logika selected
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

                                        // INJECT HTML KE KOLOM PERTAMA (KOSONG)
                                        // DataTables membuat struktur: div.row -> div.col-md-6 (kiri) & div.col-md-6 (kanan)
                                        // Kita cari col-md-6 pertama (sebelah kiri) di wrapper tabel ini.
                                        $('#' + tableId + '_wrapper .row:first .col-md-6:first').html(dropdownHtml);
                                        
                                        // Panggil fungsi setupEntriesPerPage agar event listener aktif
                                        window.setupEntriesPerPage();
                                    }
                                });
                                
                                // Simpan instance agar tidak double-init
                                $table.dataTable = dataTable; 
                            } catch (err) {
                                console.error("Gagal inisialisasi DataTables:", err);
                            }
                        }
                    });
                }
            }
        }*/

        window.initializePageComponents = function() {
            if (typeof $ !== 'undefined' && $.fn.DataTable) {
                
                const $tables = $('.datatable');
                
                if ($tables.length > 0) {
                    $tables.each(function() {
                        const $table = $(this);

                        if (!$table.hasClass('dataTable')) {
                            try {
                                const dataTable = $table.DataTable({
                                    paging: false,
                                    lengthChange: false,
                                    searching: true,
                                    info: true,
                                    ordering: true,
                                    responsive: true,
                                    language: {
                                        search: "Cari:",
                                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                                        infoEmpty: "Tidak ada data ditemukan",
                                        infoFiltered: "(disaring dari _MAX_ total data)",
                                        zeroRecords: "Data tidak ditemukan"
                                    },
                                    
                                    initComplete: function() {
                                        var api = this.api();
                                        var tableId = api.table().node().id; // Contoh: 'table1'
                                        
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
                                        // Masukkan ke kolom kiri atas (col-md-6 pertama)
                                        $('#' + tableId + '_wrapper .row:first .col-md-6:first').html(dropdownHtml);
                                        
                                        // --- B. Handle Bottom Row (Pagination Custom) ---
                                        // Cek apakah container pagination kita ada
                                        if ($('#custom-pagination-container').length > 0) {
                                            // 1. Hapus class d-none (hidden)
                                            // 2. Tambahkan d-flex justify-content-end (Rata Kanan)
                                            // 3. Pindahkan container ke kolom kanan bawah (col-md-7)
                                            $('#custom-pagination-container')
                                                .removeClass('d-none')
                                                .addClass('d-flex justify-content-end')
                                                .appendTo('#' + tableId + '_wrapper .row:last .col-md-7');
                                        }

                                        // Panggil setup listener
                                        window.setupEntriesPerPage();
                                    }
                                });
                                
                                $table.dataTable = dataTable; 
                            } catch (err) {
                                console.error("Gagal inisialisasi DataTables:", err);
                            }
                        }
                    });
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

            // TAMBAHKAN EVENT LISTENER INI DI DALAM document.addEventListener('DOMContentLoaded', ...)
            document.body.addEventListener('submit', function(e) {
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