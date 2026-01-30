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

function fungsi_sidebar_resepSIMRS_cara1() {

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

function fungsi_sidebar_resepSIMRS_Cara2() {

    document.querySelectorAll('.resep-header').forEach(header => {

        header.addEventListener('click', function () {

            const targetId = this.dataset.target;
            const target   = document.getElementById(targetId);

            // toggle tampilan
            target.classList.toggle('d-none');

            // kalau sudah pernah load → STOP
            if (target.dataset.loaded === 'true') return;

            // tampilkan loading
            target.innerHTML = LOADING_HTML;

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
            })
            .catch(() => {
                target.innerHTML = `
                    <div class="text-danger small">
                        Gagal memuat detail obat
                    </div>`;
            });

        });

    });
}

function fungsi_sidebar_resepSIMRS_Cara3() {

    const wrapper   = document.getElementById('resepWrapper');
    const container = document.getElementById('resepContainer');
    const counter   = document.getElementById('selectedCounter');
    const checkAll  = document.getElementById('checkAllGlobal');
    const search    = document.getElementById('searchResep');
    const sepKosong = document.getElementById('sepkosong');

    /* ======================
       COUNTER
    ====================== */
    function updateCounter() {
        counter.innerText =
            container.querySelectorAll('.resep-check:checked').length +
            ' terpilih';
    }

    /* ======================
       GLOBAL CHECK ALL
    ====================== */
    checkAll.addEventListener('change', function () {
        container.querySelectorAll('.resep-check, .check-group')
            .forEach(cb => cb.checked = this.checked);
        updateCounter();
    });

    /* ======================
       EVENT DELEGATION
    ====================== */
    container.addEventListener('change', function (e) {

        /* resep checkbox */
        if (e.target.classList.contains('resep-check')) {
            updateCounter();
        }

        /* group checkbox */
        if (e.target.classList.contains('check-group')) {
            const group = e.target.closest('.resep-group');
            group.querySelectorAll('.resep-check')
                .forEach(cb => cb.checked = e.target.checked);
            updateCounter();
        }
    });

    /* ======================
       SEARCH + FILTER
    ====================== */
    let timer = null;

    function filterList() {
        const keyword = search.value.toLowerCase();
        const onlySepEmpty = sepKosong.checked;

        container.querySelectorAll('.resep-item').forEach(item => {
            const match = item.dataset.search.includes(keyword);
            const sepOK = !onlySepEmpty || item.dataset.sep === '0';
            item.style.display = (match && sepOK) ? '' : 'none';
        });
    }

    search.addEventListener('keyup', () => {
        clearTimeout(timer);
        timer = setTimeout(filterList, 250);
    });

    sepKosong.addEventListener('change', filterList);

    document.getElementById('resepWrapper')
        .addEventListener('click', function (e) {

            const header = e.target.closest('.toggle-detail');
            if (!header) return;

            const targetId = header.dataset.target;
            const target = document.getElementById(targetId);

            if (!target) return; // 🔐 SAFETY

            // toggle manual (tanpa bootstrap)
            target.classList.toggle('show');

            // lazy load 1x
            if (target.dataset.loaded === '1') return;

            target.innerHTML = `
                <div class="bg-info rounded p-2 small text-muted">
                    <i class="bi bi-hourglass-split me-1"></i>
                    Memuat detail obat...
                </div>`;

            fetch(BASE_URL + '/res/getDetailObat', {
                method: 'POST',
                headers: {
                    'Content-Type':'application/x-www-form-urlencoded',
                    'X-Requested-With':'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    no_out: header.dataset.noout,
                    tgl_out: header.dataset.tglout
                })
            })
            .then(r => r.text())
            .then(html => {
                target.innerHTML = html;
                target.dataset.loaded = '1';
            })
            .catch(() => {
                target.innerHTML =
                    `<div class="text-danger small">Gagal memuat detail</div>`;
            });
        });
    updateCounter();
}

function fungsi_sidebar_resepSIMRS() {

    const wrapper   = document.getElementById('resepWrapper');
    if (!wrapper) return; // safety global

    const counter   = document.getElementById('selectedCounter');
    const search    = document.getElementById('searchResep');
    const sepKosong = document.getElementById('sepkosong');

    /* ======================
       COUNTER
    ====================== */
    function updateCounter() {
        counter.innerText =
            wrapper.querySelectorAll('.resep-check:checked').length +
            ' terpilih';
    }

    /* ======================
       DELEGATION CHECK-ALL
    ====================== */
    wrapper.addEventListener('change', function (e) {

        /* CHECK ALL GLOBAL */
        if (e.target.id === 'checkAllGlobal') {
            const checked = e.target.checked;

            wrapper
                .querySelectorAll('.resep-check, .check-group')
                .forEach(cb => cb.checked = checked);

            updateCounter();
            return;
        }

        /* CHECK GROUP */
        if (e.target.classList.contains('check-group')) {
            const group = e.target.closest('.resep-group');
            if (!group) return;

            group.querySelectorAll('.resep-check')
                .forEach(cb => cb.checked = e.target.checked);

            updateCounter();
            return;
        }

        /* CHECK SINGLE */
        if (e.target.classList.contains('resep-check')) {
            updateCounter();
        }
    });

    /* ======================
       SEARCH (requestAnimationFrame)
    ====================== */
    let rafId = null;

    function filterList() {
        const keyword = search.value.toLowerCase();
        const onlySepEmpty = sepKosong.checked;

        wrapper.querySelectorAll('.resep-item').forEach(item => {
            const match = item.dataset.search.includes(keyword);
            const sepOK = !onlySepEmpty || item.dataset.sep === '0';
            item.style.display = (match && sepOK) ? '' : 'none';
        });

        rafId = null;
    }

    function requestFilter() {
        if (rafId) return;
        rafId = requestAnimationFrame(filterList);
    }

    if (search) {
        search.addEventListener('input', requestFilter);
    }
    if (sepKosong) {
        sepKosong.addEventListener('change', requestFilter);
    }

    /* ======================
       COLLAPSE + LAZY LOAD DETAIL OBAT
    ====================== */
    wrapper.addEventListener('click', function (e) {

        const header = e.target.closest('.toggle-detail');
        if (!header) return;

        const targetId = header.dataset.target;
        const target = document.getElementById(targetId);
        if (!target) return;

        /* toggle manual (tanpa bootstrap) */
        target.classList.toggle('show');

        /* lazy load hanya 1x */
        if (target.dataset.loaded === '1') return;

        target.innerHTML = `
            <div class="bg-primary rounded p-2 small text-white">
                <i class="bi bi-hourglass-split me-1"></i>
                Memuat detail obat...
            </div>`;

        fetch(BASE_URL + '/res/getDetailObat', {
            method: 'POST',
            headers: {
                'Content-Type':'application/x-www-form-urlencoded',
                'X-Requested-With':'XMLHttpRequest'
            },
            body: new URLSearchParams({
                no_out : header.dataset.noout,
                tgl_out: header.dataset.tglout
            })
        })
        .then(r => r.text())
        .then(html => {
            target.innerHTML = html;
            target.dataset.loaded = '1';
        })
        .catch(() => {
            target.innerHTML =
                `<div class="text-danger small">Gagal memuat detail</div>`;
        });
    });

    updateCounter();
}
