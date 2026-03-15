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
                initProsesObatSIMRS();
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

function initProsesObatSIMRS() {

    const wrapper = document.getElementById('resepWrapper');
    if (!wrapper) return;

    wrapper.removeEventListener('click', handleProsesObatClick);
    wrapper.addEventListener('click', handleProsesObatClick);
}

function fungsi_sidebar_resepSIMRS() {

    const wrapper = document.getElementById('resepWrapper');
    if (!wrapper) return;

    const counter   = document.getElementById('selectedCounter');
    const search    = document.getElementById('searchResep');
    const sepKosong = document.getElementById('sepkosong');
    const sepAda    = document.getElementById('sepada');
    const btnProses = document.getElementById('btnProsesTerpilih');

    /* ======================
       BTN PROSES
    ====================== */
    function updateBtnProses() {
        if (!btnProses) return;
        btnProses.disabled =
            !wrapper.querySelector('.obat-check:checked');
    }

    /* ======================
       COUNTER
    ====================== */
    function updateCounter() {
        counter.innerText =
            wrapper.querySelectorAll('.resep-check:checked').length +
            ' resep terpilih';

        updateBtnProses();
    }

    /* ======================
       DELEGATION CHECK
    ====================== */
    wrapper.addEventListener('change', function (e) {

        /* CHECK ALL GLOBAL */
        if (e.target.id === 'checkAllGlobal') {
            const checked = e.target.checked;
            wrapper
                .querySelectorAll('.resep-check, .check-group, .obat-check')
                .forEach(cb => cb.checked = e.target.checked);

            wrapper
                .querySelectorAll('.resep-item')
                .forEach(item => {
                    item.dataset.headerChecked = checked ? '1' : '0';
                });

            updateCounter();
            return;
        }

        /* CHECK GROUP */
        if (e.target.classList.contains('check-group')) {
            const checked = e.target.checked;
            const group = e.target.closest('.resep-group');
            if (!group) return;

            group.querySelectorAll('.resep-check, .obat-check')
                .forEach(cb => cb.checked = checked);

            // 🔑 TAMBAHAN KECIL (INGAT STATE HEADER PER RESEP)
            group.querySelectorAll('.resep-item')
                .forEach(item => {
                    item.dataset.headerChecked = checked ? '1' : '0';
                });

            updateCounter();
            return;
        }

        /* CHECK HEADER RESEP → DETAIL OBAT */
        if (e.target.classList.contains('resep-check')) {
            const resepItem = e.target.closest('.resep-item');
            if (!resepItem) return;

            // SIMPAN NIAT HEADER
            resepItem.dataset.headerChecked = e.target.checked ? '1' : '0';

            // kalau detail SUDAH ada → langsung centang
            resepItem
                .querySelectorAll('.obat-check')
                .forEach(cb => cb.checked = e.target.checked);

            updateCounter();
        }

        /* CHECK DETAIL OBAT → HEADER RESEP */
        if (e.target.classList.contains('obat-check')) {
            const resepItem = e.target.closest('.resep-item');
            if (!resepItem) return;

            const allDetail = resepItem.querySelectorAll('.obat-check');
            const checked   = resepItem.querySelectorAll('.obat-check:checked');

            const header = resepItem.querySelector('.resep-check');
            if (!header) return;

            if (checked.length === 0) {
                header.checked = false;
                header.indeterminate = false;
                resepItem.dataset.headerChecked = '0';
            }
            else if (checked.length === allDetail.length) {
                header.checked = true;
                header.indeterminate = false;
                resepItem.dataset.headerChecked = '1';
            }
            else {
                header.checked = false;
                header.indeterminate = true;
                resepItem.dataset.headerChecked = '0';
            }

            updateCounter();
        }

    });

    /* ======================
       SEARCH + FILTER (RAF)
    ====================== */
    let rafId = null;

    function applyFilter() {
        const keyword = search.value.toLowerCase();
        const onlySepKosong = sepKosong?.checked;
        const onlySepAda    = sepAda?.checked;

        wrapper.querySelectorAll('.resep-item').forEach(item => {
            const match = item.dataset.search.includes(keyword);
            const sep   = item.dataset.sep;

            let sepOK = true;
            if (onlySepKosong) sepOK = sep === '0';
            if (onlySepAda)    sepOK = sep !== '0';

            item.style.display = (match && sepOK) ? '' : 'none';
        });

        rafId = null;
    }

    function requestFilter() {
        if (rafId) return;
        rafId = requestAnimationFrame(applyFilter);
    }

    search?.addEventListener('input', requestFilter);
    sepKosong?.addEventListener('change', requestFilter);
    sepAda?.addEventListener('change', requestFilter);

    /* ======================
       COLLAPSE + LAZY LOAD
    ====================== */
    wrapper.addEventListener('click', function (e) {

        const toggle = e.target.closest('.toggle-detail');
        if (!toggle) return;

        const targetId = toggle.dataset.target;
        const target   = document.getElementById(targetId);
        if (!target) return;

        /* toggle manual */
        target.classList.toggle('show');

        /* lazy load hanya 1x */
        if (target.dataset.loaded === '1') return;

        target.innerHTML = `
            <div class="bg-primary text-white rounded p-2 small">
                <i class="bi bi-hourglass-split me-1"></i>
                Memuat detail obat...
            </div>`;

        fetch(BASE_URL + '/res/getDetailObat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                no_out : toggle.dataset.noout,
                tgl_out: toggle.dataset.tglout
            })
        })
        .then(r => r.text())
        .then(html => {
            // target.innerHTML = html;
            // target.dataset.loaded = '1';
            target.innerHTML = html;
            target.dataset.loaded = '1';

            const resepItem = toggle.closest('.resep-item');
            if (!resepItem) return;

            // JIKA HEADER SUDAH DICENTANG SEBELUMNYA
            if (resepItem.dataset.headerChecked === '1') {
                resepItem
                    .querySelectorAll('.obat-check')
                    .forEach(cb => cb.checked = true);
            }

            updateCounter();
        })
        .catch(() => {
            target.innerHTML =
                `<div class="text-danger small">Gagal memuat detail obat</div>`;
        });
    });

    updateCounter();
}

function handleProsesObatClick(e) {

    const wrapper = document.getElementById('resepWrapper');
    if (!wrapper) return;

    /* =========================
       PROSES PER RESEP (DETAIL)
    ========================= */
    const btnDetail = e.target.closest('.btn-proses-detail');
    if (btnDetail) {

        const resepItem = btnDetail.closest('.resep-item');
        if (!resepItem) return;

        const detail = [];

        resepItem.querySelectorAll('.obat-check:checked').forEach(o => {
            detail.push({
                kd_obat: o.dataset.kdobat,
                qty    : o.dataset.qty,
                racikan : o.dataset.racikan
            });
        });

        if (detail.length === 0) {
            alert('Pilih minimal satu obat');
            return;
        }
        const kdJnsObat = item.find('.kdjnsobat-select').val();

        const payload = [{
            noresep   : resepItem.dataset.noresep,
            sep       : resepItem.dataset.sep,
            kdpasien  : resepItem.dataset.kdpasien,
            no_out    : resepItem.dataset.no_out,
            tgl_out   : resepItem.dataset.tgl_out,
            kd_unit   : resepItem.dataset.kd_unit,
            kd_dokter : resepItem.dataset.kd_dokter,
            iterasi   : resepItem.dataset.iterasi,
            kdjnsobat : kdJnsObat,
            detailobat: detail
        }];

        console.log('PROSES DETAIL PER RESEP:', payload);
        prosesBatchSIMRS(payload);

        return; // ⛔ STOP DI SINI
    }

    /* =========================
       PROSES GLOBAL
    ========================= */
    if (!e.target.closest('#btnProsesTerpilih')) return;

    const payload = [];

    wrapper.querySelectorAll('.resep-check:checked').forEach(cb => {

        const resepItem = cb.closest('.resep-item');
        if (!resepItem) return;

        const detail = [];

        resepItem.querySelectorAll('.obat-check:checked').forEach(o => {
            detail.push({
                kd_obat: o.dataset.kdobat,
                qty    : o.dataset.qty,
                racikan : o.dataset.racikan
            });
        });

        if (detail.length === 0) return;
        const kdJnsObat = item.find('.kdjnsobat-select').val();
        payload.push({
            noresep   : resepItem.dataset.noresep,
            sep       : resepItem.dataset.sep,
            kdpasien  : resepItem.dataset.kdpasien,
            no_out    : resepItem.dataset.no_out,
            tgl_out   : resepItem.dataset.tgl_out,
            kd_unit   : resepItem.dataset.kd_unit,
            kd_dokter : resepItem.dataset.kd_dokter,
            iterasi   : resepItem.dataset.iterasi,
            kdjnsobat : kdJnsObat,
            detailobat: detail
        });
    });

    if (payload.length === 0) {
        alert('Tidak ada detail obat yang dipilih');
        return;
    }

    console.log('PROSES GLOBAL:', payload);
    prosesBatchSIMRS(payload);
}

function prosesBatchSIMRS(payload) {
    const wrapper = document.getElementById('resepWrapper');
    if (!wrapper) return;

    payload.forEach((item, index) => {

        const resepItem = wrapper.querySelector(
            `.resep-check[data-id="${item.noresep}"]`
        )?.closest('.resep-item');

        const bar = resepItem?.querySelector('.resep-progress');
        const inner = bar?.querySelector('.progress-bar');

        if (!bar || !inner) return;

        bar.classList.remove('d-none');

        fetch(BASE_URL + 'bpjs/insert/insresepobat', {
            method: 'POST',
            headers: {
                'Content-Type':'application/json',
                'X-Requested-With':'XMLHttpRequest'
            },            
            body: JSON.stringify(item)
        })
        // .then(() => {
        //     inner.style.width = '100%';
        //     inner.classList.remove('progress-bar-animated');
        //     inner.classList.add('bg-success');
        // })
        .then(res => res.json())
        .then(data => {
            console.log('Response server:', data);
            // if (data.status) {
            //     // resultContainer.innerHTML = data.html;
            // } else {
            //     alertContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            // }
            if(data.status){
                Toast.fire({
                    icon: 'success',
                    title: data.message
                });
            }else{
                Toast.fire({
                    icon: 'error',
                    title: data.message
                });
            }
            inner.style.width = '100%';
            inner.classList.remove('progress-bar-animated');
            inner.classList.add('bg-success');
        })
        // .catch(() => {
        //     inner.classList.add('bg-danger');
        // });
        .catch(error => {
            console.error('Error:', error);
            inner.classList.add('bg-danger');
        });
    });
}