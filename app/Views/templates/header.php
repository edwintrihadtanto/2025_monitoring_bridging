<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?><?php echo SITE_NAME; ?></title>
    
    <link rel="shortcut icon" href="" type="image/x-icon">
    <link rel="shortcut icon" href="<?= base_url('img/rssmico.png') ?>" type="image/png">
    <link rel="stylesheet" crossorigin href="<?= base_url('assets/dist/assets/compiled/css/app.css'); ?>">
    <link rel="stylesheet" crossorigin href="<?= base_url('assets/dist/assets/compiled/css/app-dark.css'); ?>">
    <link rel="stylesheet" crossorigin href="<?= base_url('assets/dist/assets/compiled/css/iconly.css'); ?>">
    <!-- PENTING: Pastikan CSS jQuery DataTables juga terpanggil -->
    <link rel="stylesheet" href="<?= base_url('assets/dist/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/dist/assets/extensions/sweetalert2/sweetalert2.min.css') ?>">
    <style>
        /* --- PAKSA STYLE BOOTSTRAP 5 PAGINATION --- */
        /* Reset margin list pagination */
        nav ul.pagination {
            margin: 0;
            display: flex;
            list-style: none;
            padding-left: 0;
            border-radius: 0.25rem;
        }

        /* Style Link Item */
        .page-item {
            margin-bottom: 0;
            margin-left: -1px;
        }

        .page-link {
            position: relative;
            display: block;
            padding: 0.375rem 0.75rem;
            line-height: 1.25;
            color: #0d6efd; /* Warna Biru Bootstrap */
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #dee2e6;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        /* Hover State */
        .page-link:hover {
            z-index: 2;
            color: #0a58ca;
            background-color: #000000;
            border-color: #dee2e6;
        }

        /* Active State (Halaman yang sedang aktif) */
        .page-item.active .page-link {
            z-index: 3;
            color: #fff;
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        /* Disabled State (Tombol First/Prev/Next/Last saat ujung) */
        .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            background-color: #fff;
            border-color: #dee2e6;
        }

        /* Rounded First & Last items (Supaya ujung tombol bulat) */
        .page-item:first-child .page-link {
            margin-left: 0;
            border-top-left-radius: 0.25rem;
            border-bottom-left-radius: 0.25rem;
        }
        .page-item:last-child .page-link {
            border-top-right-radius: 0.25rem;
            border-bottom-right-radius: 0.25rem;
        }

        @media (max-width: 768px) {
            /* Kurangi padding card di HP */
            .card-body {
                padding: 10px !important;
            }
            
            /* Sesuaikan ukuran font tabel di HP */
            .table td, .table th {
                font-size: 0.85rem; /* Font sedikit lebih kecil */
                padding: 0.5rem;    /* Jarak sel lebih rapat */
                vertical-align: middle;
            }

            /* Sembunyikan kolom yang kurang penting di HP jika perlu */
            /* Contoh: Sembunyikan kolom 'Method' jika di HP (Opsional) */
            /* .table td:nth-child(3), .table th:nth-child(3) { display: none; } */
        }

        
        /* Yang scroll hanya menu */
        .sidebar-menu {
            overflow-y: auto;
        }

        /* Area bawah untuk toggle */
        .sidebar-footer {
            padding-bottom: 0;
            border-top: 1px solid rgba(0,0,0,0.1);
        }

        footer{
            padding-left: 0;
            padding-right: 0;
        }

        .footer-new {
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            bottom: 0;
            position: fixed;
            width: -webkit-fill-available;
            padding-left: 2rem;
            padding-right: 2rem;
            padding-top: 2px;
            background-color: white;
        }
        /* Dark mode */
        [data-bs-theme="dark"] .sidebar-footer {
            border-top: 1px solid rgba(255,255,255,0.15);
        }
        [data-bs-theme="dark"] .footer-new {
            border-top: 1px solid white;
            background-color: var(--bs-body-bg);
        }
        /* Card Body RGB Border Animation */
        .card-body.rgb-border {
            position: relative;
            z-index: 1;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .card-body.rgb-border::before {
            content: "";
            position: absolute;
            inset: 0;
            padding: 3px; /* ketebalan border */
            border-radius: inherit;
            background: linear-gradient(
                270deg,
                #ff0000,
                #ff9900,
                #ffff00,
                #00ff00,
                #00ffff,
                #0000ff,
                #ff00ff,
                #ff0000
            );
            background-size: 400% 400%;
            animation: rgbBorder 20s linear infinite;
            -webkit-mask:
                linear-gradient(#fff 0 0) content-box,
                linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        .card-body.rgb-border-200 {
            position: relative;
            z-index: 1;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .card-body.rgb-border-200::before {
            content: "";
            position: absolute;
            inset: 0;
            padding: 2px;
            border-radius: inherit;
            background: linear-gradient(270deg, #06c142, #06c142, #000000, #06c142, #06c142);
            background-size: 400% 400%;
            animation: rgbBorder 5s linear infinite;
            -webkit-mask:
                linear-gradient(#fff 0 0) content-box,
                linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        [data-bs-theme="dark"] .rgb-border-200::before  {
            background: linear-gradient(270deg, #06c142, #06c142, #ffffff, #06c142, #06c142);
            background-size: 400% 400%;
        }

        .card-body.rgb-border-300 {
            position: relative;
            z-index: 1;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .card-body.rgb-border-300::before {
            content: "";
            position: absolute;
            inset: 0;
            padding: 2px;
            border-radius: inherit;
            background: linear-gradient(270deg, #64B5F6, #64B5F6, #000000, #64B5F6, #64B5F6);
            background-size: 400% 400%;
            animation: rgbBorder 5s linear infinite;
            -webkit-mask:
                linear-gradient(#fff 0 0) content-box,
                linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        [data-bs-theme="dark"] .rgb-border-300::before  {
            background: linear-gradient(270deg, #64B5F6, #64B5F6, #ffffff, #64B5F6, #64B5F6);
            background-size: 400% 400%;
        }

        .card-body.rgb-border-400 {
            position: relative;
            z-index: 1;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .card-body.rgb-border-400::before {
            content: "";
            position: absolute;
            inset: 0;
            padding: 2px;
            border-radius: inherit;
            background: linear-gradient(270deg, #ffff00, #ffff00, #000000, #ffff00);
            background-size: 400% 400%;
            animation: rgbBorder 5s linear infinite;
            -webkit-mask:
                linear-gradient(#fff 0 0) content-box,
                linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        [data-bs-theme="dark"] .rgb-border-400::before  {
            background: linear-gradient(270deg, #ffff00, #ffff00, #ffffff, #ffff00);
            background-size: 400% 400%;
        }

        .card-body.rgb-border-500 {
            position: relative;
            z-index: 1;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .card-body.rgb-border-500::before {
            content: "";
            position: absolute;
            inset: 0;
            padding: 2px;
            border-radius: inherit;
            background: linear-gradient(270deg, #F44336, #EF5350, #000000, #F44336);
            background-size: 400% 400%;
            animation: rgbBorder 5s linear infinite;
            -webkit-mask:
                linear-gradient(#fff 0 0) content-box,
                linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        [data-bs-theme="dark"] .rgb-border-500::before  {
            background: linear-gradient(270deg, #F44336, #EF5350, #ffffff, #F44336);
            background-size: 400% 400%;
        }

        .btn-info.rgb-border {
            position: relative;
            z-index: 1;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .btn-info.rgb-border::before {
            content: "";
            position: absolute;
            inset: 0;
            padding: 2px; /* ketebalan border */
            border-radius: inherit;
            background: linear-gradient(
                270deg,
                #ff0000,
                #ff9900,
                #ffff00,
                #00ff00,
                #00ffff,
                #0000ff,
                #ff00ff,
                #ff0000
            );
            background-size: 400% 400%;
            animation: rgbBorder 3s linear infinite;
            -webkit-mask:
                linear-gradient(#fff 0 0) content-box,
                linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        /* Animasi */
        @keyframes rgbBorder {
            0%   { background-position:   0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position:   0% 50%; }
        }

        .logo-animasi {
            animation: gerakLogo 50s cubic-bezier(0.45, 0.05, 0.55, 0.95) infinite;
            /*animation: gerakLogo 8s ease-in-out infinite;
            position: relative;*/
        }

        @keyframes gerakLogo {
            0% {
                transform: translate(0, 0);
            }
            25% {
                transform: translate(30px, -20px); /* naik serong ke kanan */
            }
            50% {
                transform: translate(30px, 20px);  /* turun ke bawah */
            }
            75% {
                transform: translate(-20px, 0);    /* geser ke kiri */
            }
            100% {
                transform: translate(0, 0);        /* kembali ke posisi awal */
            }
        }

        .logo-animasi img {
            animation: waveFloat 20s ease-in-out infinite;
        }

        /* Ombak halus */
        @keyframes waveFloat {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-6px);
            }
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .rotate-icon {
            transition: transform .2s ease;
        }

        .toggle-detail[aria-expanded="true"] .rotate-icon {
            transform: rotate(180deg);
        }

        /* Supaya tombol tidak ikut toggle */
        .no-toggle {
            z-index: 2;
        }

        /*#btnToTop {
            position: fixed;
            bottom: 50px;
            right: 15px;
            z-index: 1050;
            display: none;

            width: 45px;
            height: 45px;
            padding: 0px 2px 5px 0px;

            border-radius: 50%;
            align-items: center;
            justify-content: center;

            line-height: 1;
        }*/

        #btnToTop {
            position: fixed;
            bottom: 50px;
            right: 15px;
            z-index: 1050;
            display: none;
            width: 50px;
            height: 50px;
            padding: 0px 2px 5px 0px;
            border-radius: 100%;
            align-items: center;
            justify-content: center;
            line-height: 1;
            border-block-color: #d6338400;
        }
        
        #btnToTop.show {
            display: flex;
        }

        #btnToTop:hover {
            transform: translateY(-3px);
            /*background-color: #1e1e2d5c;*/
            color: #ffe8ba;
        }

        #btnToTop i {
            font-size: 1.2rem;
            line-height: 1;          /* 🔑 kunci ikon */
        }

        .dashboard-bpjs .dashboard-header{
            background:linear-gradient(90deg,#2563eb,#06b6d4);
            color:white;
            border:none;
        }

        .dashboard-bpjs .title{
            color:white;
            margin-bottom:4px;
        }

        .dashboard-bpjs .subtitle{
            opacity:0.8;
        }

        .kpi-card .card-body{
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        .kpi-title{
            color:#6b7280;
            font-size:14px;
        }

        .kpi-icon{
            width:48px;
            height:48px;
            display:flex;
            align-items:center;
            justify-content:center;
            border-radius:12px;
            color:white;
            font-size:20px;
        }

        .dashboard-chart{
            border:none;
            box-shadow:0 4px 20px rgba(0,0,0,0.05);
        }

        .kdjnsobat-select{
            font-size:12px;
            padding:2px 6px;
        }

        .resep-item {
            border-bottom: 1px solid #2c2f3a;
            transition: 0.2s;
        }

        .resep-item:hover {
            background: rgba(255,255,255,0.03);
        }

        .form-check-input:checked {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <script src="<?= base_url('assets/dist/assets/static/js/initTheme.js'); ?>"></script>
    <script>
        const BASE_URL = "<?= base_url() ?>";
        const LOADING_HTML = '<div class="text-center py-2 text-muted small"><div class="spinner-border spinner-border-sm"></div>Memuat detail obat...</div>';
    </script>

    <div id="app">