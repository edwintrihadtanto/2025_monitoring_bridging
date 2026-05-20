<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <title><?= isset($page_title) ? $page_title . ' - ' : '' ?><?php echo SITE_NAME; ?></title> -->
    <title><?php echo SITE_NAME; ?></title>
    
    <link rel="shortcut icon" href="" type="image/x-icon">
    <link rel="shortcut icon" href="<?= base_url('img/rssmico.png') ?>" type="image/png">
    <link rel="stylesheet" crossorigin href="<?= base_url('assets/dist/assets/compiled/css/app.css'); ?>">
    <link rel="stylesheet" crossorigin href="<?= base_url('assets/dist/assets/compiled/css/app-dark.css'); ?>">
    <link rel="stylesheet" crossorigin href="<?= base_url('assets/dist/assets/compiled/css/iconly.css'); ?>">
    <!-- PENTING: Pastikan CSS jQuery DataTables juga terpanggil -->
    <link rel="stylesheet" href="<?= base_url('assets/dist/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/dist/assets/extensions/sweetalert2/sweetalert2.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/dist/assets/extensions/toastify-js/src/toastify.css') ?>">
    <link rel="stylesheet" href="<?= base_url('js/log.css') ?>">
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

       .kpi-card{
            border-radius:18px;
            transition:.3s;
            overflow:hidden;
            background:#fff;
        }

        .kpi-card:hover{
            transform:translateY(-4px);
        }

        .kpi-card .card-body{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:1.2rem;
        }

        .kpi-title{
            display:block;
            color:#94a3b8;
            font-size:.85rem;
            margin-bottom:6px;
        }

        .kpi-info h3{
            margin:0;
            font-weight:700;
        }

        .kpi-icon{
            width:52px;
            height:52px;
            border-radius:16px;
            color:#fff;
            /*display:flex;
            align-items:center;
            justify-content:center;            
            font-size:22px;*/
            box-shadow:0 8px 20px rgba(0,0,0,.08);
        }

        .bg-primary{
            background:linear-gradient(135deg,#3b82f6,#2563eb)!important;
        }

        .bg-success{
            background:linear-gradient(135deg,#22c55e,#16a34a)!important;
        }

        .bg-warning{
            background:linear-gradient(135deg,#facc15,#eab308)!important;
        }

        .bg-danger{
            background:linear-gradient(135deg,#ef4444,#dc2626)!important;
        }

        .bg-secondary{
            background:linear-gradient(135deg,#94a3b8,#64748b)!important;
        }

        .bg-info{
            background:linear-gradient(135deg,#06b6d4,#0891b2)!important;
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
            font-size: 0.85rem;
        }

        .resep-item:hover {
            background: rgba(255,255,255,0.03);
        }

        .form-check-input:checked {
            transform: scale(1.1);
        }
        .resep-card {
            /*background: #020617;*/
            border: 1px solid #1f2937;
            border-radius: 10px;
            transition: 0.2s;
            margin-bottom: 0.2rem;
        }

        .resep-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.2);
        }

        .resep-group {
            border: 1px solid #1f2937;
            border-radius: 8px;
        }

        /*.resep-item {
            font-size: 0.85rem;
        }*/

        .resep-toolbar {
            background: #020617;
            padding: 6px 8px;
            border-radius: 8px;
        }

        .connection-shell {
            display: grid;
            gap: 1rem;
        }

        .connection-toolbar {
            background: var(--bs-card-bg);
            border: 1px solid var(--bs-border-color);
            border-radius: 10px;
            padding: .85rem 1rem;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
        }

        .connection-card {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--bs-border-color);
            border-radius: 10px;
            background: var(--bs-card-bg);
            box-shadow: 0 10px 28px rgba(15, 23, 42, .08);
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .connection-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 34px rgba(15, 23, 42, .12);
        }

        .connection-card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: var(--connection-accent, var(--bs-secondary));
        }

        .connection-card.is-ok {
            --connection-accent: var(--bs-success);
        }

        .connection-card.is-warning {
            --connection-accent: var(--bs-warning);
        }

        .connection-card.is-down {
            --connection-accent: var(--bs-danger);
        }

        .connection-icon {
            min-width: 34px;
            height: 30px;
            border-radius: 8px;
            padding: 0 .6rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: var(--connection-accent, var(--bs-secondary));
            box-shadow: 0 8px 18px rgba(15, 23, 42, .14);
            font-size: .95rem;
        }

        .connection-status {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            border-radius: 999px;
            padding: .28rem .62rem;
            color: var(--connection-accent, var(--bs-secondary));
            background: color-mix(in srgb, var(--connection-accent, var(--bs-secondary)) 14%, transparent);
            font-size: .72rem;
            font-weight: 700;
        }

        .connection-meta {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            border: 1px solid var(--bs-border-color);
            border-radius: 999px;
            padding: .28rem .58rem;
            color: var(--bs-body-color);
            background: var(--bs-tertiary-bg);
            font-size: .72rem;
        }

        .connection-note {
            border: 1px solid var(--bs-border-color);
            border-radius: 10px;
            background: var(--bs-card-bg);
            padding: .85rem 1rem;
            color: #111827;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
        }

        .speed-chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            border-radius: 999px;
            padding: .28rem .58rem;
            font-size: .72rem;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .speed-fast {
            color: #15803d;
            background: rgba(34, 197, 94, .14);
            border-color: rgba(34, 197, 94, .25);
        }

        .speed-normal {
            color: #1d4ed8;
            background: rgba(59, 130, 246, .14);
            border-color: rgba(59, 130, 246, .25);
        }

        .speed-slow {
            color: #a16207;
            background: rgba(234, 179, 8, .18);
            border-color: rgba(234, 179, 8, .3);
        }

        .speed-critical {
            color: #b91c1c;
            background: rgba(239, 68, 68, .14);
            border-color: rgba(239, 68, 68, .25);
        }

        .connection-endpoint {
            border-radius: 8px;
            background: var(--bs-tertiary-bg);
            border: 1px solid var(--bs-border-color);
            color: var(--bs-secondary-color);
            padding: .45rem .6rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
            font-size: .72rem;
        }

        .connection-card .connection-label,
        .connection-card .connection-message,
        .connection-card .connection-title {
            color: #111827 !important;
        }

        .connection-card .connection-label {
            opacity: .72;
        }

        .connection-card .connection-endpoint {
            color: #1f2937;
        }

        [data-bs-theme="dark"] .connection-toolbar,
        [data-bs-theme="dark"] .connection-card {
            box-shadow: 0 12px 32px rgba(0, 0, 0, .28);
        }

        [data-bs-theme="dark"] .connection-card .connection-label,
        [data-bs-theme="dark"] .connection-card .connection-message,
        [data-bs-theme="dark"] .connection-card .connection-title {
            color: var(--bs-body-color) !important;
        }

        [data-bs-theme="dark"] .connection-card .connection-label {
            opacity: .78;
        }

        [data-bs-theme="dark"] .connection-card .connection-endpoint {
            color: var(--bs-secondary-color);
        }

        [data-bs-theme="dark"] .connection-note {
            color: var(--bs-body-color);
            box-shadow: 0 12px 32px rgba(0, 0, 0, .28);
        }

        [data-bs-theme="dark"] .speed-fast {
            color: #86efac;
        }

        [data-bs-theme="dark"] .speed-normal {
            color: #93c5fd;
        }

        [data-bs-theme="dark"] .speed-slow {
            color: #fde68a;
        }

        [data-bs-theme="dark"] .speed-critical {
            color: #fca5a5;
        }

        .detail-obat-wrapper .accordion-button {
            background: #f8f9fa;
            box-shadow: none;
        }

        .detail-obat-wrapper .accordion-button:not(.collapsed) {
            background: #e9ecef;
        }

        .detail-obat-wrapper .list-group-item {
            border: none;
            border-bottom: 1px solid #eee;
        }
        
        /* ===== SIDEBAR BASE ===== */
        #sidebar {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border-right: 1px solid rgba(0,0,0,0.05);
        }

        /* ===== HEADER ULTRA ===== */
        .sidebar-brand-ultra {
            position: relative;
            padding: 20px 10px 15px;
            overflow: hidden;
        }

        /* Glow background */
        .brand-glow {
            position: absolute;
            width: 140px;
            height: 140px;
            background: radial-gradient(circle, rgba(67,94,190,0.25) 0%, transparent 70%);
            top: -40px;
            left: 50%;
            transform: translateX(-50%);
            filter: blur(20px);
        }

        /* Logo */
        .logo-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logo-img {
            height: 58px;
            z-index: 2;
            position: relative;
            transition: all 0.35s ease;
            filter: drop-shadow(0 8px 20px rgba(0,0,0,0.12));
        }

        .logo-img:hover {
            transform: scale(1.08) rotate(2deg);
        }

        /* Text */
        .brand-text {
            margin-top: 6px;
        }

        .brand-title {
            font-size: 14px;
            font-weight: 700;
            color: #2d3748;
        }

        .brand-sub {
            font-size: 11px;
            color: #94a3b8;
        }

        /* ===== MENU ===== */
        .sidebar-menu {
            padding-top: 5px;
        }

        /* Menu item */
        .sidebar-item .sidebar-link {
            border-radius: 12px;
            margin: 3px 8px;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        /* Hover effect */
        .sidebar-item .sidebar-link:hover {
            background: rgba(67,94,190,0.08);
            transform: translateX(5px);
        }

        /* Active */
        .sidebar-item.active .sidebar-link {
            background: linear-gradient(135deg, #435ebe, #6366f1);
            color: #fff;
            box-shadow: 0 6px 18px rgba(67,94,190,0.35);
        }

        /* Active glow line */
        .sidebar-item.active .sidebar-link::before {
            content: "";
            position: absolute;
            left: 0;
            top: 20%;
            height: 60%;
            width: 4px;
            background: #fff;
            border-radius: 0 5px 5px 0;
        }

        /* ===== SUBMENU SMOOTH ===== */
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: all 0.35s ease;
            padding-left: 10px;
        }

        .sidebar-item.active .submenu {
            max-height: 500px;
        }

        .submenu-link {
            display: block;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
            color: #6b7280;
            transition: all 0.2s ease;
        }

        .submenu-link:hover {
            background: rgba(0,0,0,0.05);
            padding-left: 16px;
        }

        /* ===== ICON ANIMATION ===== */
        .sidebar-link i {
            transition: transform 0.3s ease;
        }

        .sidebar-item.active > .sidebar-link i {
            transform: scale(1.1);
        }

        /* ===== SCROLLBAR CLEAN ===== */
        .sidebar-menu::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.15);
            border-radius: 10px;
        }

        /* ===== COLLAPSE MODE ===== */
        .sidebar-collapsed .sidebar-link span {
            display: none;
        }

        .sidebar-collapsed .logo-text {
            display: none;
        }

        .sidebar-collapsed .logo-img {
            height: 40px;
        }

        :root{
            --pelayanan-scale: 1;
        }

        /* =========================
           WRAPPER UTAMA
        ========================= */
        #resepWrapper{
            font-size: calc(0.875rem * var(--pelayanan-scale));
            line-height: 1.4;
        }

        /* semua elemen ikut inherit */
        #resepWrapper *{
            font-size: inherit;
            box-sizing: border-box;
        }

        /* =========================
           TRANSITION
        ========================= */
        #resepWrapper .card,
        #resepWrapper .list-group-item,
        #resepWrapper .form-control,
        #resepWrapper .form-select,
        #resepWrapper .btn,
        #resepWrapper .badge,
        #resepWrapper .nama-obat{
            transition: all .2s ease;
        }

        /* =========================
           LIST ITEM
        ========================= */
        #resepWrapper .list-group-item{
            padding: calc(.75rem * var(--pelayanan-scale));
        }

        /* =========================
           NAMA OBAT
        ========================= */
        #resepWrapper .nama-obat{
            font-size: calc(0.95rem * var(--pelayanan-scale)) !important;
            line-height: 1.35;
            display: inline-block;
            word-break: break-word;
        }

        /* mode zoom besar */
        #resepWrapper.zoom-large .nama-obat{
            font-size: calc(1.05rem * var(--pelayanan-scale)) !important;
            font-weight: 600;
        }

        /* mode zoom kecil */
        #resepWrapper.zoom-small .nama-obat{
            font-size: calc(0.78rem * var(--pelayanan-scale)) !important;
        }

        /* =========================
           BADGE
        ========================= */
        #resepWrapper .badge{
            font-size: calc(.72rem * var(--pelayanan-scale));
        }

        /* =========================
           BUTTON
        ========================= */
        #resepWrapper .btn-sm{
            padding: calc(.25rem * var(--pelayanan-scale))
                     calc(.5rem * var(--pelayanan-scale));

            font-size: calc(.82rem * var(--pelayanan-scale));
        }

        /* =========================
           FORM
        ========================= */
        #resepWrapper .form-control,
        #resepWrapper .form-select{
            font-size: calc(.85rem * var(--pelayanan-scale));
        }

        /* =========================
           HEADER CARD
        ========================= */
        #resepWrapper .card-header{
            padding-top: calc(.5rem * var(--pelayanan-scale));
            padding-bottom: calc(.5rem * var(--pelayanan-scale));
        }

        /* =========================
           TEXT KECIL
        ========================= */
        #resepWrapper small,
        #resepWrapper .small,
        #resepWrapper .text-muted{
            font-size: calc(.74rem * var(--pelayanan-scale));
        }

        /* =========================
           ICON COLLAPSE
        ========================= */
        .toggle-detail i{
            transition: transform .2s ease;
        }

        .toggle-detail.active i{
            transform: rotate(180deg);
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
