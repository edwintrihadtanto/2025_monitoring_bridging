function initListResepPage() {

    window.handleListResepSubmit = function(e, form) {
        e.preventDefault(); // Mencegah reload halaman
        
        const btnSubmit = form.querySelector('button[type="submit"]');
        const resultContainer = document.getElementById('result-container');
        const alertContainer = document.getElementById('alert-container');
        
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mencari Data...';
        resultContainer.innerHTML = '';
        alertContainer.innerHTML = '';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
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
                // if (typeof initializePageComponents === 'function') {
                //     initializePageComponents();
                // }
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

    window.handleDeleteResepSubmit = function(e, form) {
        e.preventDefault(); // Mencegah reload halaman
        
        const btnSubmit = form.querySelector('button[type="submit"]');
        // const resultContainer = document.getElementById('result-container');
        const alertContainer = document.getElementById('pesan-deletedaftarresep');
        
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        // resultContainer.innerHTML = '';
        alertContainer.innerHTML = '';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-trash"></i>';

            if (data.status) {

                handleListResepSubmit();
                
            } else {
                // alertContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                alertContainer.innerHTML = `<div class="alert alert-danger mb-2"><h6 class="alert-heading"><i class="bi bi-info-circle-fill"></i> ${data.message}</h6></div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btnSubmit.disabled  = false;
            btnSubmit.innerHTML = '<i class="bi bi-trash"></i>';
            alertContainer.innerHTML = `<div class="alert alert-danger mb-2"><h6 class="alert-heading"><i class="bi bi-info-circle-fill"></i> Terjadi Kesalahan Sistem</h6></div>`;
        });
    }
}

function initListPelyananObatPage() {

    window.handleListPelyananObatSubmit = function(e, form) {
        e.preventDefault(); // Mencegah reload halaman
        
        const btnSubmit = form.querySelector('button[type="submit"]');
        const resultContainer = document.getElementById('result-container');
        const alertContainer = document.getElementById('alert-container');
        
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mencari Data...';
        resultContainer.innerHTML = '';
        alertContainer.innerHTML = '';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
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
                // if (typeof initializePageComponents === 'function') {
                //     initializePageComponents();
                // }
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

function initRiwayatPelyananObatPage() {

    window.handleRiwayatPelyananObatSubmit = function(e, form) {
        e.preventDefault(); // Mencegah reload halaman
        
        const btnSubmit = form.querySelector('button[type="submit"]');
        const resultContainer = document.getElementById('result-container');
        const alertContainer = document.getElementById('alert-container');
        
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mencari Data...';
        resultContainer.innerHTML = '';
        alertContainer.innerHTML = '';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-search me-2"></i> Cari Data Riwayat';

            if (data.status) {
                // Masukkan HTML Result ke Container
                resultContainer.innerHTML = data.html;
                
                // Re-inisialisasi DataTables (PENTING: Agar tabel bisa di-sort)
                // if (typeof initializePageComponents === 'function') {
                //     initializePageComponents();
                // }
            } else {
                alertContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-search me-2"></i> Cari Data Riwayat';
            alertContainer.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan sistem.</div>`;
        });
    }
}