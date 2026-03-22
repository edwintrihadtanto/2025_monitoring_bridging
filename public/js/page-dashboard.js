window.PageDashboard = (function () {

    let chartResep = null;
    let chartStatus = null;
    let chartObat = null;
    let chartPoli = null;

    let initialized = false;

    function destroyCharts() {
        chartResep?.destroy();
        chartStatus?.destroy();
        chartObat?.destroy();
        chartPoli?.destroy();

        chartResep = null;
        chartStatus = null;
        chartObat = null;
        chartPoli = null;
    }

    // 🔥 helper: tunggu element siap (anti NaN)
    function waitForElement(el, callback) {
        if (!el) return;

        if (el.offsetHeight > 0) {
            callback();
        } else {
            requestAnimationFrame(() => waitForElement(el, callback));
        }
    }

    function initCharts() {

        const elResep  = document.querySelector("#chartResep");
        const elStatus = document.querySelector("#chartStatus");
        const elObat   = document.querySelector("#chartObat");
        const elPoli   = document.querySelector("#chartPoli");

        if (elResep) {
            chartResep = new ApexCharts(elResep, {
                chart:{type:'area',height:320,toolbar:{show:false}},
                series:[{name:'Resep',data:[80,95,110,120,105,115,128]}],
                xaxis:{categories:['Sen','Sel','Rab','Kam','Jum','Sab','Min']},
                stroke:{curve:'smooth'},
                colors:['#2563eb']
            });
            chartResep.render();
        }

        if (elStatus) {
            chartStatus = new ApexCharts(elStatus, {
                chart:{type:'donut',height:250},
                series:[120,5,3],
                labels:['Berhasil','Gagal','Pending'],
                colors:['#22c55e','#ef4444','#f59e0b']
            });
            chartStatus.render();
        }

        if (elObat) {
            chartObat = new ApexCharts(elObat, {
                chart:{type:'bar',height:300},
                series:[{name:'Jumlah',data:[90,70,55,40,30]}],
                xaxis:{categories:['Paracetamol','Amoxicillin','Amlodipine','Cefixime','Metformin']}
            });
            chartObat.render();
        }

        if (elPoli) {
            chartPoli = new ApexCharts(elPoli, {
                chart:{type:'bar',height:300},
                series:[{name:'Resep',data:[60,45,35,20]}],
                xaxis:{categories:['Penyakit Dalam','Anak','Saraf','Umum']}
            });
            chartPoli.render();
        }
    }

    function initScrollTop() {
        const btn = document.getElementById('btnToTop');
        if (!btn) return;

        window.addEventListener('scroll', function () {
            btn.classList.toggle('show', window.scrollY > 300);
        });

        btn.onclick = function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };
    }

    return {
        init: function () {

            if (initialized) return; // 🔥 anti double init
            initialized = true;

            console.log('Dashboard initialized');

            initCharts();
            initScrollTop();
        },

        destroy: function () {
            destroyCharts();
            initialized = false;
        }
    };

})();