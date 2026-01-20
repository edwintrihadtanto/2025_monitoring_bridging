<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?><?php echo SITE_NAME; ?></title>
    
    <link rel="shortcut icon" href="" type="image/x-icon">
    <link rel="shortcut icon" href="<?= base_url('public/img/rssmico.png') ?>" type="image/png">
    <link rel="stylesheet" crossorigin href="<?= base_url('public/assets/dist/assets/compiled/css/app.css'); ?>">
    <link rel="stylesheet" crossorigin href="<?= base_url('public/assets/dist/assets/compiled/css/app-dark.css'); ?>">
    <link rel="stylesheet" crossorigin href="<?= base_url('public/assets/dist/assets/compiled/css/iconly.css'); ?>">
    <!-- PENTING: Pastikan CSS jQuery DataTables juga terpanggil -->
    <link rel="stylesheet" href="<?= base_url('public/assets/dist/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') ?>">

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
            animation: rgbBorder 6s linear infinite;
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
            animation: gerakLogo 30s cubic-bezier(0.45, 0.05, 0.55, 0.95) infinite;
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
            animation: waveFloat 3s ease-in-out infinite;
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
    </style>
</head>
<body>
    <script src="<?= base_url('public/assets/dist/assets/static/js/initTheme.js'); ?>"></script>
    <div id="app">