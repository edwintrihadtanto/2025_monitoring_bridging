function initSIMRS() {
    let pelayananZoom = 1;

    const MIN_ZOOM = 0.5;
    const MAX_ZOOM = 1.4;
    const STEP_ZOOM = 0.05;

    function applyPelayananZoom() {

        const wrapper = document.getElementById('resepWrapper');

        if (!wrapper) return;

        document.documentElement
            .style
            .setProperty('--pelayanan-scale', pelayananZoom);

        wrapper.classList.remove('zoom-small', 'zoom-large');

        if (pelayananZoom <= 0.9) {
            wrapper.classList.add('zoom-small');
        }

        if (pelayananZoom >= 1.2) {
            wrapper.classList.add('zoom-large');
        }

        localStorage.setItem('pelayananZoom', pelayananZoom);
    }

    // expose ke global
    window.zoomInPelayanan = function () {

        pelayananZoom += STEP_ZOOM;

        if (pelayananZoom > MAX_ZOOM) {
            pelayananZoom = MAX_ZOOM;
        }

        applyPelayananZoom();
    };

    window.zoomOutPelayanan = function () {

        pelayananZoom -= STEP_ZOOM;

        if (pelayananZoom < MIN_ZOOM) {
            pelayananZoom = MIN_ZOOM;
        }

        applyPelayananZoom();
    };

    window.zoomResetPelayanan = function () {

        pelayananZoom = 1;

        applyPelayananZoom();
    };

    function loadResepSIMRSPage(form, page = 1, perPage = 50) {
        const btnSubmit = form.querySelector('button[type="submit"]');
        const resultContainer = document.getElementById('result-container');
        const alertContainer = document.getElementById('alert-container');
        const formData = new FormData(form);

        formData.set('page', page);
        formData.set('per_page', perPage);
        
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mencari Data...';
        }

        if (resultContainer) {
            resultContainer.innerHTML = '';
        }

        alertContainer.innerHTML = '';

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="bi bi-search"></i>';
            }

            if (data.status) {
                resultContainer.innerHTML = data.html;
                fungsi_sidebar_resepSIMRS();
                initProsesObatSIMRS();

                const savedZoom = localStorage.getItem('pelayananZoom');

                if (savedZoom) {
                    pelayananZoom = parseFloat(savedZoom);
                }

                applyPelayananZoom();
            } else {
                alertContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="bi bi-search"></i>';
            }
            alertContainer.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan sistem.</div>`;
        });
    }

    window.handleResepSIMRSSubmit = function(e, form) {
        e.preventDefault();

        const perPage = parseInt(document.querySelector('.simrs-per-page')?.value || '50', 10);
        loadResepSIMRSPage(form, 1, perPage);
    }

    const resultContainer = document.getElementById('result-container');
    if (resultContainer && resultContainer.dataset.simrsPaginationBound !== '1') {
        resultContainer.dataset.simrsPaginationBound = '1';

        resultContainer.addEventListener('click', function(e) {
            const btn = e.target.closest('.simrs-page-btn');
            if (!btn || btn.disabled) return;

            const form = document.getElementById('pencarianResepSIMRS');
            if (!form) return;

            const wrapper = document.getElementById('resepWrapper');
            const page = parseInt(btn.dataset.page || '1', 10);
            const perPage = parseInt(wrapper?.dataset.perPage || '50', 10);

            loadResepSIMRSPage(form, page, perPage);
        });

        resultContainer.addEventListener('change', function(e) {
            if (!e.target.classList.contains('simrs-per-page')) return;

            const form = document.getElementById('pencarianResepSIMRS');
            if (!form) return;

            const perPage = parseInt(e.target.value || '50', 10);
            loadResepSIMRSPage(form, 1, perPage);
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

function initProsesObatSIMRSXX() {

    const wrapper = document.getElementById('resepWrapper');
    if (!wrapper) return;

    wrapper.removeEventListener('click', handleProsesObatClick);
    wrapper.addEventListener('click', handleProsesObatClick);
}

function initProsesObatSIMRS() {
    const wrapper = document.getElementById('resepWrapper');
    if (!wrapper) return;

    // ===============================
    // CLICK HANDLER
    // ===============================
    wrapper.removeEventListener('click', handleProsesObatClick);
    wrapper.addEventListener('click', handleProsesObatClick);

    // ===============================
    // INPUT JHO (ANTI DOUBLE BIND)
    // ===============================
    wrapper.removeEventListener('input', handleInputJHO);
    wrapper.addEventListener('input', handleInputJHO);
}

function handleInputJHO(e) {

    /*if (!e.target.matches('.qty, .signa1, .signa2')) return;

    const obatItem = e.target.closest('.obat-jadi');
    if (!obatItem) return;

    let qty    = parseFloat(obatItem.querySelector('.qty')?.value) || 0;
    let signa1 = parseFloat(obatItem.querySelector('.signa1')?.value) || 0;
    let signa2 = parseFloat(obatItem.querySelector('.signa2')?.value) || 0;

    let jho = 0;

    if (signa1 > 0 && signa2 > 0) {
        jho = qty / (signa1 * signa2);
    }

    const jhoInput = obatItem.querySelector('.jho');
    if (jhoInput) {
        jhoInput.value = Math.floor(jho);
    }*/

    if (!e.target.matches('.qty, .signa1, .signa2')) return;

    // OBAT JADI
    const obatItem = e.target.closest('.obat-jadi');

    if (obatItem) {
        let qty    = parseFloat(obatItem.querySelector('.qty')?.value) || 0;
        let signa1 = parseFloat(obatItem.querySelector('.signa1')?.value) || 0;
        let signa2 = parseFloat(obatItem.querySelector('.signa2')?.value) || 0;

        let jho = 0;

        if (signa1 > 0 && signa2 > 0) {
            jho = qty / (signa1 * signa2);
        }

        const jhoInput = obatItem.querySelector('.jho');
        if (jhoInput) {
            jhoInput.value = Math.floor(jho);
        }

        return;
    }

    // RACIKAN
    const racikanBox = e.target.closest('.racikan-box');

    if (racikanBox) {

        // total qty semua item racikan
        let totalQty = 0;

        racikanBox.querySelectorAll('.racikan-item .qty').forEach(input => {
            totalQty += parseFloat(input.value) || 0;
        });

        let signa1 = parseFloat(racikanBox.querySelector('.signa1')?.value) || 0;
        let signa2 = parseFloat(racikanBox.querySelector('.signa2')?.value) || 0;

        let jho = 0;

        if (signa1 > 0 && signa2 > 0) {
            jho = totalQty / (signa1 * signa2);
        }

        const jhoInput = racikanBox.querySelector('.jho');
        if (jhoInput) {
            jhoInput.value = Math.floor(jho);
        }
    }
}

function parseSigna(text) {
    if (!text) return { s1: 0, s2: 0 };

    text = text.toUpperCase();

    // =========================
    // PRIORITAS 1: FORMAT X (1X1, 3X1, dll)
    // =========================
    let match = text.match(/(\d+)\s*[X]\s*(\d+)/);
    if (match) {
        return {
            s1: parseInt(match[1]),
            s2: parseInt(match[2])
        };
    }

    // =========================
    // PRIORITAS 2: FORMAT (1 0 0)
    // =========================
    match = text.match(/\((\d+)\s+(\d+)\s+(\d+)\)/);
    if (match) {
        const pagi = parseInt(match[1]);
        const siang = parseInt(match[2]);
        const malam = parseInt(match[3]);

        const s1 = [pagi, siang, malam].filter(v => v > 0).length;
        const s2 = Math.max(pagi, siang, malam);

        return { s1, s2 };
    }

    return { s1: 0, s2: 0 };
}

function initAutoSigna(wrapper) {
    // const items = wrapper.querySelectorAll('.obat-jadi');
    // alert('jumlah: ' + items.length);
    wrapper.querySelectorAll('.obat-jadi').forEach(item => {
        const label = item.querySelector('.badge.bg-danger');
        if (!label) return;

        const text = label.textContent.trim();

        const { s1, s2 } = parseSigna(text);

        if (s1 > 0 && s2 > 0) {

            const input1 = item.querySelector('.signa1');
            const input2 = item.querySelector('.signa2');

            // isi hanya jika kosong
            if (input1 && !input1.value) input1.value = s1;
            if (input2 && !input2.value) input2.value = s2;

            // trigger hitung JHO
            input2?.dispatchEvent(new Event('input', { bubbles: true }));
        }
    });

    // const items2 = wrapper.querySelectorAll('.racikan-box');
    // alert('jumlah rac: ' + items2.length);
    // RACIKAN
    wrapper.querySelectorAll('.racikan-box').forEach(box => {

        const label = box.querySelector('.badge.bg-danger');
        if (!label) return;

        const text = label.textContent.trim();

        const { s1, s2 } = parseSigna(text);

        if (s1 > 0 && s2 > 0) {

            const input1 = box.querySelector('.signa1');
            const input2 = box.querySelector('.signa2');

            if (input1 && !input1.value) input1.value = s1;
            if (input2 && !input2.value) input2.value = s2;

            input2?.dispatchEvent(new Event('input', { bubbles: true }));
        }
    });
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
        if (
            e.target.closest(
                'input, select, option, button, textarea, .prevent-collapse'
            )
        ) {
            return;
        }
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
            // AUTO SIGNA
            initAutoSigna(target);
        })
        .catch(() => {
            target.innerHTML =
                `<div class="text-danger small">Gagal memuat detail obat</div>`;
        });
    });

    updateCounter();
}

function getInputValueDetailItem(parent, selector) {
    const el = parent?.querySelector(selector);
    return el ? el.value : '';
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
        let isValid = true;
        /*resepItem.querySelectorAll('.obat-check:checked').forEach(o => {
            const parent = o.closest('.py-1'); // wrapper item obat
            detail.push({
                kd_obat   : o.dataset.kdobat,
                // qty       : o.dataset.qty,
                racikan   : o.dataset.racikan,
                signa1    : parent.querySelector('.signa1')?.value || 0,
                signa2    : parent.querySelector('.signa2')?.value || 0,
                qty       : parent.querySelector('.qty')?.value || 0,
                jho       : parent.querySelector('.jho')?.value || 0,
                permintaan: parent.querySelector('.permintaan')?.value || ''
            });
        });*/
        resepItem.querySelectorAll('.obat-check:checked').forEach(o => {

            if (!isValid) return;
            const racikanBox = o.closest('.racikan-box');
            const obatItem   = o.closest('.obat-item, .racikan-item');

            let data = {
                kd_obat : o.dataset.kdobat,
                racikan : o.dataset.racikan || '',
                catatan : o.dataset.catatan || ''
            };

            if (racikanBox) {
                // ✅ RACIKAN
                data.signa1     = parseFloat(getInputValueDetailItem(racikanBox, '.signa1')) || 0;
                data.signa2     = parseFloat(getInputValueDetailItem(racikanBox, '.signa2')) || 0;
                data.jho        = parseFloat(getInputValueDetailItem(racikanBox, '.jho')) || 0;
                // data.permintaan = getInputValueDetailItem(racikanBox, '.permintaan');
                const permintaan = getInputValueDetailItem(racikanBox, '.permintaan');

                if (!permintaan) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Kolom Permintaan Racikan masih kosong!'
                    });
                    isValid = false;
                    return;
                }

                data.permintaan = permintaan;
                data.qty        = parseFloat(getInputValueDetailItem(obatItem, '.qty')) || 0;
                
            } else {
                // ✅ NON RACIKAN
                data.signa1     = parseFloat(getInputValueDetailItem(obatItem, '.signa1')) || 0;
                data.signa2     = parseFloat(getInputValueDetailItem(obatItem, '.signa2')) || 0;
                data.jho        = parseFloat(getInputValueDetailItem(obatItem, '.jho')) || 0;
                data.permintaan = getInputValueDetailItem(obatItem, '.permintaan');
                data.qty        = parseFloat(getInputValueDetailItem(obatItem, '.qty')) || 0;
            }

            detail.push(data);
        });
        
        if (!isValid) return;

        if (detail.length === 0) {
            // alert('Pilih minimal satu obat');
            // return;

            Toast.fire({
                icon: 'warning',
                title: 'Pilih minimal satu obat!'
            });
            return;
        }
        
        const kdjnsobat = resepItem.dataset.kdjnsobat || '1';
        const iterasi = resepItem.dataset.iterasi || '0';
        
        const payload = [{
            noresep   : resepItem.dataset.noresep,
            sep       : resepItem.dataset.sep,
            kdpasien  : resepItem.dataset.kdpasien,
            no_out    : resepItem.dataset.no_out,
            tgl_out   : resepItem.dataset.tgl_out,
            kd_unit   : resepItem.dataset.kd_unit,
            kd_dokter : resepItem.dataset.kd_dokter,
            iterasi   : iterasi,
            kdjnsobat : kdjnsobat,
            kdmodulresep : document.querySelector('input[name="option_radio"]:checked')?.value,
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

        /*resepItem.querySelectorAll('.obat-check:checked').forEach(o => {
            const parent = o.closest('.py-1'); // wrapper item obat
            detail.push({
                kd_obat   : o.dataset.kdobat,
                // qty       : o.dataset.qty,
                racikan   : o.dataset.racikan,
                signa1    : parent.querySelector('.signa1')?.value || 0,
                signa2    : parent.querySelector('.signa2')?.value || 0,
                qty       : parent.querySelector('.qty')?.value || 0,
                jho       : parent.querySelector('.jho')?.value || 0,
                permintaan: parent.querySelector('.permintaan')?.value || ''
            });
        });*/
        resepItem.querySelectorAll('.obat-check:checked').forEach(o => {

            const racikanBox = o.closest('.racikan-box');
            const obatItem   = o.closest('.obat-item, .racikan-item');

            let data = {
                kd_obat : o.dataset.kdobat,
                racikan : o.dataset.racikan || ''
            };

            if (racikanBox) {
                // ✅ RACIKAN
                data.signa1     = parseFloat(getInputValueDetailItem(racikanBox, '.signa1')) || 0;
                data.signa2     = parseFloat(getInputValueDetailItem(racikanBox, '.signa2')) || 0;
                data.jho        = parseFloat(getInputValueDetailItem(racikanBox, '.jho')) || 0;
                data.permintaan = getInputValueDetailItem(racikanBox, '.permintaan');

                data.qty        = parseFloat(getInputValueDetailItem(obatItem, '.qty')) || 0;

            } else {
                // ✅ NON RACIKAN
                data.signa1     = parseFloat(getInputValueDetailItem(obatItem, '.signa1')) || 0;
                data.signa2     = parseFloat(getInputValueDetailItem(obatItem, '.signa2')) || 0;
                data.jho        = parseFloat(getInputValueDetailItem(obatItem, '.jho')) || 0;
                data.permintaan = getInputValueDetailItem(obatItem, '.permintaan');
                data.qty        = parseFloat(getInputValueDetailItem(obatItem, '.qty')) || 0;
            }

            detail.push(data);
        });

        if (detail.length === 0) return;
        
        const kdjnsobat = resepItem.dataset.kdjnsobat || '1';
        const iterasi = resepItem.dataset.iterasi || '0';
        payload.push({
            noresep   : resepItem.dataset.noresep,
            sep       : resepItem.dataset.sep,
            kdpasien  : resepItem.dataset.kdpasien,
            no_out    : resepItem.dataset.no_out,
            tgl_out   : resepItem.dataset.tgl_out,
            kd_unit   : resepItem.dataset.kd_unit,
            kd_dokter : resepItem.dataset.kd_dokter,
            iterasi   : iterasi,
            kdjnsobat : kdjnsobat,
            kdmodulresep : document.querySelector('input[name="option_radio"]:checked')?.value,
            detailobat: detail
        });
    });

    if (payload.length === 0) {
        alert('Tidak ada detail obat yang dipilih');
        return;
    }

    // console.log('PROSES GLOBAL:', payload);
    prosesBatchSIMRS(payload);
}

function prosesBatchSIMRS(payload) {
    const wrapper = document.getElementById('resepWrapper');
    if (!wrapper) return;

    payload.forEach((item, index) => {

        const resepItem = wrapper.querySelector(
            `.resep-check[data-id="${item.noresep}"]`
        )?.closest('.resep-item');

        const spinner = resepItem?.querySelector('.resep-overlay-spinner');
        if (!spinner) return;

        // Tampilkan spinner
        spinner.classList.remove('d-none');

        fetch(BASE_URL + 'bpjs/insert/insresepobat', {
            method: 'POST',
            headers: {
                'Content-Type':'application/json',
                'X-Requested-With':'XMLHttpRequest'
            },            
            body: JSON.stringify(item)
        })        
        .then(res => res.json())
        .then(data => {
            console.log('Response server:', data);
            
            // ==========================================
            // HELPER: UPDATE BADGE NO RESEP
            // ==========================================
            const updateNoResepBadge = (icon, bgClass, tooltipText) => {
                const noResepDiv = resepItem.querySelector('.no-resep-bpjs');
                if (noResepDiv && data.data && data.data.noResep) {
                    noResepDiv.innerHTML = `<span class="badge ${bgClass}" data-bs-toggle="tooltip" title="${tooltipText}">${icon} ${data.data.noResep}</span>`;
                }
            };

            if(data.status){
                Toast.fire({
                    icon: 'success',
                    title: data.message
                });
                                
                updateNoResepBadge('✅', 'bg-primary', `No Apotik: ${data.data.noApotik}`);                
                if (item.detailobat && item.detailobat.length > 0) {
                    item.detailobat.forEach(obat => {
                        if (obat.kd_obat) {
                            const statusEl = resepItem.querySelector(`.obat-bpjs-status[data-kdobat="${obat.kd_obat}"]`);
                            if (statusEl) {
                                statusEl.innerHTML = ` <i class="bi bi-check-circle-fill text-success" data-bs-toggle="tooltip" title="Berhasil dikirim ke BPJS"></i>`;
                            }
                        }
                    });
                }
            } else {
                // ✅ PROSES ERROR SPESIFIK UNTUK DETAIL OBAT
                if (data.errors && data.errors.length > 0) {
                    let detailMsg = "<ul class='text-start mb-0' style='font-size:13px;'>";
                    
                    // 1. Tampilkan pesan di SweetAlert
                    data.errors.forEach(err => {
                        detailMsg += `<li><strong>${err.nama_obat}</strong>: ${err.error}</li>`;
                    });
                    detailMsg += "</ul>";

                    Swal.fire({
                        icon: 'warning',
                        title: `Gagal Kirim Obat (No Apotek: ${data.data?.noApotik || '-'})`,
                        html: detailMsg,
                        timer: 6000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        // confirmButtonText: 'Tutup'
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });

                    // ⚠️ Tempel icon seru di view obat
                    data.errors.forEach(err => {
                        if (err.kd_obat) {
                            const statusEl = resepItem.querySelector(`.obat-bpjs-status[data-kdobat="${err.kd_obat}"]`);
                            if (statusEl) {
                                statusEl.innerHTML = ` <i class="bi bi-exclamation-triangle-fill text-danger" data-bs-toggle="tooltip" title="${err.error}"></i>`;
                            }
                        }
                    });
                    // ✅ Ubah badge jadi Info (Header aman, tapi obat ada yang gagal)
                    if (data.data && data.data.noApotik) {
                        updateNoResepBadge('✅', 'bg-primary', `Header Sukses, Detail Obat Gagal. No Apotik: ${data.data.noApotik}`);
                    }

                } else {
                    // Error umum (bukan dari detail obat)
                    Swal.fire({
                        icon: 'error',
                        html: data.message,
                        timer: 3400,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                }
            }
            
            // ✅ SEMBUNYIKAN SPINNER SETELAH SELESAI (SUKSES MAUPUN GAGAL)
            spinner.classList.add('d-none');
        })
        .catch(error => {
            console.error('Error:', error);
             // ✅ SEMBUNYIKAN SPINNER JIKA ERROR JARINGAN
            spinner.classList.add('d-none');
            
            Toast.fire({
                icon: 'error',
                title: 'Terjadi kesalahan koneksi ke server.'
            });
        });
    });
}

function prosesBatchSIMRSXX(payload) {
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
                inner.style.width = '100%';
                inner.classList.remove('progress-bar-animated');
                inner.classList.add('bg-success');
            }else{
                // Toast.fire({
                //     icon: 'error',
                //     title: data.message
                // });
                Swal.fire({
                    icon: 'error',
                    html: data.message,
                    timer: 3400,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
                bar.classList.add('d-none');
            }

            if(data.data != null){
                console.log(data.data.noResep);   
            }
            
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
