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

    const counter = document.getElementById('selectedCounter');
    const checkAll = document.getElementById('checkAllGlobal');

    /* ======================
       COUNTER + CHECK ALL
    ====================== */
    function updateCounter() {
        counter.innerText =
            document.querySelectorAll('.resep-check:checked').length + ' terpilih';
    }

    checkAll.addEventListener('change', function () {
        document.querySelectorAll('.resep-check').forEach(cb => {
            cb.checked = this.checked;
        });
        document.querySelectorAll('.check-group').forEach(cb => {
            cb.checked = this.checked;
        });
        updateCounter();
    });

    document.querySelectorAll('.resep-check').forEach(cb => {
        cb.addEventListener('change', updateCounter);
    });

    document.querySelectorAll('.check-group').forEach(groupCb => {
        groupCb.addEventListener('change', function () {
            const group = this.closest('.resep-group');
            group.querySelectorAll('.resep-check')
                .forEach(cb => cb.checked = this.checked);
            updateCounter();
        });
    });

    /* ======================
       SEARCH + FILTER SEP
    ====================== */
    const search = document.getElementById('searchResep');
    const sepKosong = document.getElementById('sepkosong');
    let timer = null;

    function filterList() {
        const keyword = search.value.toLowerCase();
        const onlyEmptySep = sepKosong.checked;

        document.querySelectorAll('.resep-item').forEach(item => {
            const matchSearch = item.dataset.search.includes(keyword);
            const isSepEmpty = item.innerText.includes('SEP Kosong');

            item.style.display =
                matchSearch && (!onlyEmptySep || isSepEmpty)
                    ? ''
                    : 'none';
        });
    }

    search.addEventListener('keyup', () => {
        clearTimeout(timer);
        timer = setTimeout(filterList, 250);
    });

    sepKosong.addEventListener('change', filterList);

    /* ======================
       LAZY LOAD DETAIL OBAT
    ====================== */
    document.querySelectorAll('.toggle-detail').forEach(btn => {
        btn.addEventListener('click', function () {

            const target = document.querySelector(
                this.dataset.bsTarget
            );

            if (target.dataset.loaded === 'true') return;

            fetch(BASE_URL + '/res/getDetailObat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    no_out: this.dataset.noout,
                    tgl_out: this.dataset.tglout
                })
            })
            .then(res => res.text())
            .then(html => {
                target.innerHTML = html;
                target.dataset.loaded = 'true';
            });
        });
    });

    updateCounter();
}