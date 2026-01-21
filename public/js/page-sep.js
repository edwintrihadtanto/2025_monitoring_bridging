function initPasienSepPage() {
    // const opt_rajal = document.getElementById('opt_rajal');
    // const opt_ranap = document.getElementById('opt_ranap');

    // if (opt_rajal && opt_ranap) {
    //     function toggleInput() {
    //         const wrapper_sep = document.getElementById('wrapper_sep');
    //         const hiddenType = document.getElementById('searchsep_type');

    //         if (opt_rajal.checked) {
    //             hiddenType.value = '1';
    //             document.getElementById('input_sep').focus();
    //         } else {
    //             hiddenType.value = '2';
    //             document.getElementById('input_sep').focus();
    //         }
    //     }

    //     opt_rajal.addEventListener('change', toggleInput);
    //     opt_ranap.addEventListener('change', toggleInput);
    // }

    window.handleSEPPasienSubmit = function(e, form) {
        e.preventDefault();

        const btnSubmit = form.querySelector('button[type="submit"]');
        const resultContainer = document.getElementById('result-container');
        const alertContainer = document.getElementById('alert-container');
        
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mencari data...';
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
            btnSubmit.innerHTML = '<i class="bi bi-search me-2"></i> Cari SEP Pasien';

            if (data.status) {
                resultContainer.innerHTML = data.html;
            } else {
                alertContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-search me-2"></i> Cari SEP Pasien';
            alertContainer.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan sistem.</div>`;
        });
    }
}