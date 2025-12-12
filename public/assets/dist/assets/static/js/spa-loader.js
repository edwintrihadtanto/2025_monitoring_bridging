document.addEventListener('DOMContentLoaded', function() {
    // Ambil semua link di sidebar yang memiliki atribut 'data-page'
    const pageLinks = document.querySelectorAll('[data-page]');
    
    // Tambahkan event listener untuk setiap link
    pageLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault(); // Cegah aksi default link
            
            const page = this.getAttribute('data-page'); // Ambil nama halaman dari atribut data-page
            loadPage(page); // Panggil fungsi untuk memuat halaman
            
            // Perbarui status menu aktif
            updateActiveMenu(this);
        });
    });
    
    // Fungsi untuk memuat konten halaman menggunakan AJAX
    function loadPage(page) {
        // Tampilkan indikator loading (opsional, tapi bagus untuk UX)
        showLoader();
        
        // Lakukan request AJAX ke controller kita
        fetch(`<?= base_url('main/page/') ?>${page}`)
            .then(response => response.text())
            .then(html => {
                // Ganti isi dari div #main-content dengan HTML yang baru
                document.getElementById('main-content').innerHTML = html;
                
                // Sembunyikan indikator loading
                hideLoader();
                
                // Jalankan skrip khusus untuk halaman yang baru dimuat jika ada
                // Contoh: jika halaman 'dashboard' punya fungsi init_dashboard(), jalankan itu.
                if (typeof window[`init_${page}`] === 'function') {
                    window[`init_${page}`]();
                }
            })
            .catch(error => {
                console.error('Error loading page:', error);
                document.getElementById('main-content').innerHTML = `
                    <div class="alert alert-danger">
                        Gagal memuat halaman. Silakan coba lagi.
                    </div>
                `;
                hideLoader();
            });
    }
    
    // Fungsi untuk menandai menu yang sedang aktif
    function updateActiveMenu(clickedLink) {
        // Hapus kelas 'active' dari semua link
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.classList.remove('active');
        });
        
        // Tambahkan kelas 'active' ke link yang diklik
        clickedLink.classList.add('active');
    }
    
    // Fungsi untuk menampilkan indikator loading
    function showLoader() {
        const loader = document.createElement('div');
        loader.id = 'page-loader';
        loader.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75';
        loader.style.zIndex = '9999';
        loader.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        `;
        document.body.appendChild(loader);
    }
    
    // Fungsi untuk menyembunyikan indikator loading
    function hideLoader() {
        const loader = document.getElementById('page-loader');
        if (loader) {
            loader.remove();
        }
    }
});