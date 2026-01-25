function initSIMRS() {

    window.handleResepSIMRSSubmit = function(e, form) {
        e.preventDefault();

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
            btnSubmit.innerHTML = '<i class="bi bi-search"></i>';

            if (data.status) {
                resultContainer.innerHTML = data.html;
                fungsi_sidebar_resepSIMRS();
                fungsi_sidebar_resepSIMRS2();
            } else {
                alertContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-search"></i>';
            alertContainer.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan sistem.</div>`;
        });
    }

    document.querySelectorAll('.search-type').forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.preventDefault();

            const type = this.getAttribute('data-type');
            console.log(type);
            const button = document.getElementById('searchTypeBtn');
            const input  = document.getElementById('searchInput');
            document.getElementById('searchTypePasien').value = type;

            if (type === 'medrec') {
                // button.textContent = 'Medrec';
                input.placeholder = 'Masukkan Medrec';
                input.name = 'medrec';
                input.value = '';
            } else {
                // button.textContent = 'Nama Pasien';
                input.placeholder = 'Masukkan Nama Pasien';
                input.name = 'nama_pasien';
                input.value = '';
            }
        });
    });
}

function fungsi_sidebar_resepSIMRS() {
    const searchInput = document.getElementById('searchResep');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const keyword = this.value.toLowerCase();

            document.querySelectorAll('.resep-item').forEach(item => {
                const text = item.dataset.search;
                item.style.display = text.includes(keyword) ? '' : 'none';
            });
        });
    }

    /* ========== CHECK ALL GLOBAL ========== */
    const checkAllGlobal = document.getElementById('checkAllGlobal');
    if (checkAllGlobal) {
        checkAllGlobal.addEventListener('change', function () {
            document.querySelectorAll('.resep-check, .check-group')
                .forEach(cb => cb.checked = this.checked);
        });
    }

    /* ========== CHECK PER GROUP ========== */
    document.querySelectorAll('.resep-group').forEach(group => {

        const checkGroup = group.querySelector('.check-group');
        const items = group.querySelectorAll('.resep-check');

        checkGroup.addEventListener('change', function () {
            items.forEach(cb => cb.checked = this.checked);
        });

        items.forEach(cb => {
            cb.addEventListener('change', function () {
                checkGroup.checked =
                    [...items].every(i => i.checked);
            });
        });
    });
    
}

function fungsi_sidebar_resepSIMRS2() {

    const counter = document.getElementById('selectedCounter');
    const checkAll = document.getElementById('checkAllGlobal');

    function updateCounter() {
        const total = document.querySelectorAll('.resep-check:checked').length;
        counter.innerText = `${total} terpilih`;
    }

    // Check all
    checkAll?.addEventListener('change', function () {
        document.querySelectorAll('.resep-check').forEach(cb => {
            cb.checked = this.checked;
        });
        updateCounter();
    });

    // Single check
    document.querySelectorAll('.resep-check').forEach(cb => {
        cb.addEventListener('change', function () {
            const totalChecked = document.querySelectorAll('.resep-check:checked').length;
            const totalAll = document.querySelectorAll('.resep-check').length;

            checkAll.checked = totalChecked === totalAll;
            updateCounter();
        });
    });

    updateCounter();
}

