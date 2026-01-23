<?php

namespace App\Models;

use CodeIgniter\Model;

class ResepModel extends Model
{
    protected $DBGroup          = 'dbSIMRS';  // Gunakan koneksi ke database kedua
    protected $table            = 'apt_barang_out'; // Tabel utama (header resep)
    protected $primaryKey       = ['no_out','tgl_out'];
    protected $allowedFields    = ['kd_resep', 'kd_pasien', 'nm_pasien', 'tanggal']; // Sesuaikan dengan kolom yang Anda butuhkan
    protected $useTimestamps    = false;

    /**
     * Ambil data resep yang menggabungkan tabel-tabel terkait
     */
    public function getResepDetails()
    {
        // Melakukan join antar tabel
        return $this->builder()
            ->select('*')
            ->from('acc_anggaran')
            // ->join('apt_barang_out_det', 'apt_barang_out.kd_resep = apt_barang_out_det.kd_resep', 'inner')
            // ->join('apt_obat', 'apt_barang_out_det.kd_obat = apt_obat.kd_obat', 'inner')
            // ->orderBy('apt_barang_out.tanggal', 'DESC')
            ->limit(5)
            ->get()->getResultArray();
    }
}
