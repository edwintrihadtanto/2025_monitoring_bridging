<?php
if (!function_exists('tgl_indo')) {
    function tgl_indo($tanggal) {
        if (empty($tanggal)) return null;
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $pecah = explode('-', $tanggal);
        return $pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
    }
}