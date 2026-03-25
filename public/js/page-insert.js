function initListResepPage() {

    window.handleListResepSubmit = function(e, form) {

        if (e) e.preventDefault();

        if (!form) {
            form = document.getElementById('pencarianListResepForm');
        }

        if (!form) {
            console.warn('Form pencarian tidak ditemukan');
            return;
        }

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
                resultContainer.innerHTML = data.html;
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

    window.handleDeleteResepSubmitXX = function (e, form) {
        e.preventDefault();

        const btnSubmit = form.querySelector('button[type="submit"]');
        const alertContainer = document.getElementById('pesan-deletedaftarresep');

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        alertContainer.innerHTML = '';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            // ⬅️ JANGAN langsung response.json()
            return response.text();
        })
        .then(text => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-trash"></i>';
            // console.log(text);
            // ================= HANDLE BODY KOSONG =================
            if (text === '' || text === '""') {
                handleListResepSubmit();
                return;
            }

            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('JSON parse error:', text);
                throw e; // masuk catch
            }

            if (data.status === true) {
                // handleListResepSubmit(); 
                alertContainer.innerHTML =
                    `<div class="alert alert-success mb-2">
                        <h6 class="alert-heading">
                            <i class="bi bi-chec-circle-fill"></i> ${data.message}
                        </h6>
                    </div>`;
            } else {
                alertContainer.innerHTML =
                    `<div class="alert alert-danger mb-2">
                        <h6 class="alert-heading">
                            <i class="bi bi-info-circle-fill"></i> ${data.message}
                        </h6>
                    </div>`;
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-trash"></i>';

            alertContainer.innerHTML =
                `<div class="alert alert-danger mb-2">
                    <h6 class="alert-heading">
                        <i class="bi bi-info-circle-fill"></i> Terjadi Kesalahan Sistem
                    </h6>
                </div>`;
        });
    };

    window.handleDeleteResepSubmitXX = async function(e, form){

        e.preventDefault();
        /*const verifikasi = form.querySelector('[name="byverrsp"]').value;

        if(verifikasi !== '0'){
            Swal.fire({
                icon:'warning',
                title:'Tidak bisa dihapus',
                text:'Resep sudah diverifikasi'
            });
            return;
        }*/
        
        const konfirmasi = await Swal.fire({
            title: "Hapus resep?",
            text: "Data tidak bisa dikembalikan, detail obat juga akan dihapus.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus",
            cancelButtonText: "Batal"
        });

        if(!konfirmasi.isConfirmed) return;

        const btn = form.querySelector("button");
        const formData = new FormData(form);

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        try{

            const res = await fetch(form.action,{
                method:'POST',
                body:formData,
                headers:{'X-Requested-With':'XMLHttpRequest'}
            });

            const text = await res.text();
            const data = JSON.parse(text);

            btn.disabled=false;
            btn.innerHTML='<i class="bi bi-trash"></i>';

            if(!data.status){
                throw data.message;
            }

            // ======================
            // HAPUS CARD
            // ======================
            const noApotik = form.querySelector('[name="no_apotik"]').value;

            const card = document.querySelector(
                `.card-listresep[data-noapotik="${noApotik}"]`
            );

            if(card){

                card.style.transition="0.25s";
                card.style.opacity="0";
                card.style.transform="scale(0.9)";

                setTimeout(()=>card.remove(),250);
            }

            // ======================
            // UPDATE CSRF TOKEN
            // ======================
            if(data.csrfHash){
                document.querySelectorAll('input[name="csrf_test_name"]')
                    .forEach(el=>el.value=data.csrfHash);
            }

            Swal.fire({
                icon:'success',
                title:'Berhasil',
                text:data.message,
                timer:1400,
                showConfirmButton:false
            });

        }
        catch(err){

            console.error(err);

            btn.disabled=false;
            btn.innerHTML='<i class="bi bi-trash"></i>';

            Swal.fire({
                icon:'error',
                title:'Error',
                text: err || "Terjadi kesalahan sistem"
            });

        }

    };

    window.handleDeleteResepSubmit = async function(e, form) {

        e.preventDefault();

        // 1. Tampilkan Alert Konfirmasi dengan Input Alasan
        const { value: alasan, isConfirmed } = await Swal.fire({
            title: "Hapus resep?",
            text: "Data tidak bisa dikembalikan, detail obat juga akan dihapus.",
            icon: "warning",
            input: 'textarea',
            inputLabel: 'Alasan Penghapusan',
            inputPlaceholder: 'Masukkan alasan mengapa resep dihapus...',
            inputAttributes: {
                'aria-label': 'Alasan penghapusan'
            },
            showCancelButton: true,
            confirmButtonText: "Ya, hapus",
            cancelButtonText: "Batal",
            inputValidator: (value) => {
                if (!value || value.trim() === '') {
                    return 'Alasan penghapusan wajib diisi!';
                }
            }
        });

        // Jika user batal atau tidak mengisi alasan (validasi gagal)
        if (!isConfirmed) return;

        const btn = form.querySelector("button");
        const formData = new FormData(form);

        // 2. Sisipkan alasan ke FormData (akan dibaca controller sebagai POST)
        formData.append('alasan_hapus', alasan);

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        try {

            const res = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const text = await res.text();
            const data = JSON.parse(text);

            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-trash"></i>';

            if (!data.status) {
                throw data.message;
            }

            // ======================
            // HAPUS CARD
            // ======================
            const noApotik = form.querySelector('[name="no_apotik"]').value;

            const card = document.querySelector(
                `.card-listresep[data-noapotik="${noApotik}"]`
            );

            if (card) {
                card.style.transition = "0.25s";
                card.style.opacity = "0";
                card.style.transform = "scale(0.9)";

                setTimeout(() => card.remove(), 250);
            }

            // ======================
            // UPDATE CSRF TOKEN
            // ======================
            if (data.csrfHash) {
                document.querySelectorAll('input[name="csrf_test_name"]')
                    .forEach(el => el.value = data.csrfHash);
            }

            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message,
                timer: 1400,
                showConfirmButton: false
            });

        } catch (err) {

            console.error(err);

            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-trash"></i>';

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: err || "Terjadi kesalahan sistem"
            });

        }
    };

    window.handleDeleteItemObatSubmit = async function(e, form){

        e.preventDefault();
        
        const konfirmasi = await Swal.fire({
            title: "Hapus item obat?",
            text: "Data tidak bisa dikembalikan.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus",
            cancelButtonText: "Batal"
        });

        if(!konfirmasi.isConfirmed) return;

        const btn = form.querySelector("button");
        const formData = new FormData(form);

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        try{

            const res = await fetch(form.action,{
                method:'POST',
                body:formData,
                headers:{'X-Requested-With':'XMLHttpRequest'}
            });

            const text = await res.text();
            const data = JSON.parse(text);

            btn.disabled=false;
            btn.innerHTML='<i class="bi bi-trash"></i>';

            if(!data.status){
                throw data.message;
            }

            if(data.csrfHash){
                document.querySelectorAll('input[name="csrf_test_name"]')
                    .forEach(el=>el.value=data.csrfHash);
            }

            Swal.fire({
                icon:'success',
                title:'Berhasil',
                text:data.message,
                timer:1400,
                showConfirmButton:false
            });

        }
        catch(err){

            console.error(err);

            btn.disabled=false;
            btn.innerHTML='<i class="bi bi-trash"></i>';

            Swal.fire({
                icon:'error',
                title:'Error',
                text: err || "Terjadi kesalahan sistem"
            });

        }

    };

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