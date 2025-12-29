function initPasienPage() {
    console.log("Inisialisasi Halaman Pasien...");

    // --- 1. LOGIKA TOGGLE RADIO (NIK / KARTU) ---
    const radioNIK = document.getElementById('opt_nik');
    const radioKartu = document.getElementById('opt_kartu');

    if (radioNIK && radioKartu) {
        function toggleInput() {
            const wrapperNik = document.getElementById('wrapper_nik');
            const wrapperKartu = document.getElementById('wrapper_kartu');
            const hiddenType = document.getElementById('search_type');

            if (radioNIK.checked) {
                hiddenType.value = 'nik';
                wrapperNik.classList.remove('d-none');
                wrapperKartu.classList.add('d-none');
                document.getElementById('input_nik').focus();
            } else {
                hiddenType.value = 'kartu';
                wrapperNik.classList.add('d-none');
                wrapperKartu.classList.remove('d-none');
                document.getElementById('input_kartu').focus();
            }
        }

        // Pasang listener change
        radioNIK.addEventListener('change', toggleInput);
        radioKartu.addEventListener('change', toggleInput);
    }

    // --- 2. LOGIKA SUBMIT KHUSUS PASIEN ---
    // Kita buat fungsi khusus agar tidak mengotori footer
    window.handlePasienSubmit = function(e, form) {
        e.preventDefault();

        // ... (Kode loading & fetch sama seperti sebelumnya) ...
        const btnSubmit = form.querySelector('button[type="submit"]');
        const resultContainer = document.getElementById('result-container');
        const alertContainer = document.getElementById('alert-container');
        const type = document.getElementById('search_type').value;
        
        let value = '';
        if(type === 'nik') {
            value = document.getElementById('input_nik').value;
        } else {
            value = document.getElementById('input_kartu').value;
        }

        if(!value) {
            alert('Mohon isi data pencarian terlebih dahulu.');
            return;
        }

        const formData = new FormData();
        formData.append('search_type', type);
        formData.append('search_value', value);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

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
}