<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-12">
                <h3>Data Pasien</h3>
                <p class="text-subtitle text-muted">Pencarian Berdasarkan NIK atau No. Kartu BPJS</p>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Form Pencarian</h4>
            </div>
            <div class="card-body">
                
                <div id="alert-container" class="mb-3"></div>

                <form id="pencarianPasienForm" action="<?= site_url('pasien/search') ?>" method="POST">
                    
                    <?= csrf_field() ?>
                    <div class="form-group mb-3">
                        <p class="mb-2 font-weight-bold">Pilih Jenis Pencarian:</p>
                        <div class="d-flex align-items-center">

                            <input type="hidden" name="search_type" id="search_type" value="">

                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" name="option_radio" id="opt_nik" value="nik" checked>
                                <label class="form-check-label" for="opt_nik">NIK</label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="option_radio" id="opt_kartu" value="kartu">
                                <label class="form-check-label" for="opt_kartu">Kartu BPJS</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group position-relative has-icon-left mb-3" id="wrapper_nik">
                        <input type="text" class="form-control form-control-xl" name="search_value" id="input_nik" placeholder="Masukkan NIK Pasien" maxlength="16">
                        <div class="form-control-icon">
                            <i class="bi bi-card-text"></i>
                        </div>
                    </div>

                    <div class="form-group position-relative has-icon-left mb-3 d-none" id="wrapper_kartu">
                        <input type="text" class="form-control form-control-xl" name="search_value_kartu" id="input_kartu" placeholder="Masukkan No. Kartu BPJS" maxlength="13">
                        <div class="form-control-icon">
                            <i class="bi bi-credit-card"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary shadow-lg btn-block">
                        <i class="bi bi-search me-2"></i> Cari Data Pasien
                    </button>
                </form>
            </div>
        </div>

        <!-- Area Hasil Pencarian (Muncul setelah submit) -->
        <div id="result-container" class="mt-4">
            <!-- Hasil akan muncul di sini -->
        </div>
    </section>
</div>

<script>
   
// --- Khusus untuk Halaman Pencarian Pasien ---
document.body.addEventListener('change', function(e) {
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

// --- Handler Submit Form Pencarian Pasien ---
/*document.body.addEventListener('submit', function(e) {
    const form = e.target.closest('#pencarianPasienForm');
    
    if (form) {
        e.preventDefault();
        
        const btnSubmit = form.querySelector('button[type="submit"]');
        const resultContainer = document.getElementById('result-container');
        const alertContainer = document.getElementById('alert-container');
        
        // Loading UI
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mencari...';
        resultContainer.innerHTML = '';
        alertContainer.innerHTML = '';

        // Siapkan data form
        const formData = new FormData(form);
        
        // Penting: Karena input NIK dan Kartu punya name berbeda, kita harus ambil 
        // value dari yang TIDAK disabled/hidden.
        const type = formData.get('search_type');
        let value = '';
        
        if(type === 'nik') {
            value = document.getElementById('input_nik').value;
        } else {
            // Ganti name search_value menjadi search_value saat kirim agar server terima benar
            value = document.getElementById('input_kartu').value;
        }
        
        formData.set('search_value', value);

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
                // Masukkan HTML hasil ke container
                resultContainer.innerHTML = data.html;
            } else {
                alertContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-search me-2"></i> Cari Data Pasien';
            alertContainer.innerHTML = `<div class="alert alert-danger">Gagal memproses permintaan.</div>`;
        });
    }
});*/

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
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>'); // Tambahkan CSRF manual karena manual form data

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
});
</script>