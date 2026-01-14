function initMonitoringKlaimPage() {
    console.log('initializePagemonitoring');
    window.handlemonitoringklaim = function(e, form) {
        e.preventDefault(); // Mencegah reload halaman
        
        const btnSubmit = form.querySelector('button[type="submit"]');
        const resultContainer = document.getElementById('result-container');
        const alertContainer = document.getElementById('alert-container');
        
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';
        resultContainer.innerHTML = '';
        alertContainer.innerHTML = '';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form), // Gunakan FormData langsung dari form
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-search me-2"></i> Tampilkan Monitoring';

            if (data.status) {
                // Masukkan HTML Result ke Container
                resultContainer.innerHTML = data.html;
                
                // Re-inisialisasi DataTables (PENTING: Agar tabel bisa di-sort)
                if (typeof initializePageComponents === 'function') {
                    initializePageComponents();
                }
            } else {
                alertContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-search me-2"></i> Tampilkan Monitoring';
            alertContainer.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan sistem.</div>`;
        });
    }
}

function initReferensiPage() {
    console.log('initializePageReferensi');
    window.handleFaskesPage = function(e, form) {
        e.preventDefault(); // Mencegah reload halaman
        
        const btnSubmit = form.querySelector('button[type="submit"]');
        const resultContainer = document.getElementById('result-container');
        const alertContainer = document.getElementById('alert-container');
        
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menacri Data...';
        resultContainer.innerHTML = '';
        alertContainer.innerHTML = '';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form), // Gunakan FormData langsung dari form
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-search me-2"></i> Tampilkan Pencarian';

            if (data.status) {
                // Masukkan HTML Result ke Container
                resultContainer.innerHTML = data.html;
                
                // Re-inisialisasi DataTables (PENTING: Agar tabel bisa di-sort)
                if (typeof initializePageComponents === 'function') {
                    initializePageComponents();
                }
            } else {
                alertContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-search me-2"></i> Tampilkan Pencarian';
            alertContainer.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan sistem.</div>`;
        });
    }

    window.handleApotikSubmit = function(e, form) {
        e.preventDefault(); // Mencegah reload halaman
        
        const btnSubmit = form.querySelector('button[type="submit"]');
        const resultContainer = document.getElementById('result-container');
        const alertContainer = document.getElementById('alert-container');
        
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menacri Data...';
        resultContainer.innerHTML = '';
        alertContainer.innerHTML = '';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form), // Gunakan FormData langsung dari form
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-search me-2"></i> Tampilkan Pencarian';

            if (data.status) {
                // Masukkan HTML Result ke Container
                resultContainer.innerHTML = data.html;
                
                // Re-inisialisasi DataTables (PENTING: Agar tabel bisa di-sort)
                if (typeof initializePageComponents === 'function') {
                    initializePageComponents();
                }
            } else {
                alertContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-search me-2"></i> Tampilkan Pencarian';
            alertContainer.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan sistem.</div>`;
        });
    }

    window.handlePoliklinikSubmit = function(e, form) {
        e.preventDefault(); // Mencegah reload halaman
        
        const btnSubmit = form.querySelector('button[type="submit"]');
        const resultContainer = document.getElementById('result-container');
        const alertContainer = document.getElementById('alert-container');
        
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menacri Data...';
        resultContainer.innerHTML = '';
        alertContainer.innerHTML = '';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form), // Gunakan FormData langsung dari form
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-search me-2"></i> Tampilkan Pencarian';

            if (data.status) {
                // Masukkan HTML Result ke Container
                resultContainer.innerHTML = data.html;
                
                // Re-inisialisasi DataTables (PENTING: Agar tabel bisa di-sort)
                if (typeof initializePageComponents === 'function') {
                    initializePageComponents();
                }
            } else {
                alertContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-search me-2"></i> Tampilkan Pencarian';
            alertContainer.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan sistem.</div>`;
        });
    }

    window.handleDPHOSubmit = function(e, form) {
        e.preventDefault(); // Mencegah reload halaman
        
        const btnSubmit = form.querySelector('button[type="submit"]');
        const resultContainer = document.getElementById('result-container');
        const alertContainer = document.getElementById('alert-container');
        
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menacri Data...';
        resultContainer.innerHTML = '';
        alertContainer.innerHTML = '';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form), // Gunakan FormData langsung dari form
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-search me-2"></i> Tampilkan Pencarian';

            if (data.status) {
                // Masukkan HTML Result ke Container
                resultContainer.innerHTML = data.html;
                
                // Re-inisialisasi DataTables (PENTING: Agar tabel bisa di-sort)
                if (typeof initializePageComponents === 'function') {
                    initializePageComponents();
                }
            } else {
                alertContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-search me-2"></i> Tampilkan Pencarian';
            alertContainer.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan sistem.</div>`;
        });
    }

    window.handleObatSubmit = function(e, form) {
        e.preventDefault(); // Mencegah reload halaman
        
        const btnSubmit = form.querySelector('button[type="submit"]');
        const resultContainer = document.getElementById('result-container');
        const alertContainer = document.getElementById('alert-container');
        
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menacri Data...';
        resultContainer.innerHTML = '';
        alertContainer.innerHTML = '';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form), // Gunakan FormData langsung dari form
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-search me-2"></i> Tampilkan Pencarian';

            if (data.status) {
                // Masukkan HTML Result ke Container
                resultContainer.innerHTML = data.html;
                
                // Re-inisialisasi DataTables (PENTING: Agar tabel bisa di-sort)
                if (typeof initializePageComponents === 'function') {
                    initializePageComponents();
                }
            } else {
                alertContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-search me-2"></i> Tampilkan Pencarian';
            alertContainer.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan sistem.</div>`;
        });
    }
}