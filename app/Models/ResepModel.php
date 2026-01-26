<?php

namespace App\Models;

use CodeIgniter\Model;

class ResepModel extends Model
{
    protected $DBGroup          = 'dbSIMRS';
    //CRUD standar (find, insert, update)
    protected $table            = 'apt_barang_out abo'; 
    //update(), delete()
    protected $primaryKey       = ['no_out','tgl_out']; 
    // insert() / save()
    protected $allowedFields    = ['no_out', 'tgl_out', 'kd_resep', 'kd_pasien', 'nm_pasien', 'tanggal'];
    protected $useTimestamps    = false;

    public function getResepGroupedX(array $filter = [])
    {
        $builder = $this->builder();

        $builder->select('
            abo.no_out,
            abo.tgl_out,
            abo.no_resep,
            abo.kd_pasienapt,
            abo.nmpasien,
            abo.kd_unit,
            abod.kd_prd,
            abod.jml_out,
            abod.harga_jual,
            apt_obat.nama_obat,
            un.nama_unit,
            kunj.no_sjp
        ')
        ->join('apt_barang_out_detail abod', 'abo.no_out = abod.no_out AND abo.tgl_out = abod.tgl_out', 'left')
        ->join('apt_obat', 'abod.kd_prd = apt_obat.kd_prd', 'left')
        ->join('unit un', 'un.kd_unit = abo.kd_unit', 'left')        
        ->join('transaksi trans', 'trans.no_transaksi = abo.apt_no_transaksi and trans.kd_unit = abo.kd_unit', 'INNER')
        ->join('kunjungan kunj', 'trans.kd_pasien = kunj.kd_pasien AND trans.kd_unit = kunj.kd_unit AND trans.tgl_transaksi = kunj.tgl_masuk' , 'INNER');
        $builder->whereIn('kunj.kd_customer', ['0000000043', '0000000044']);
        $builder->where('trans.tgl_transaksi >=', $filter['tgl_awal'].' 00:00:00')
                ->where('trans.tgl_transaksi <=', $filter['tgl_akhir'].' 00:00:00')
                ->where('tutup =',1);
        
        if (!empty($filter['medrec'])) {
            // $builder->where('abo.kd_pasienapt', $filter['medrec']);
            $medrec = trim($filter['medrec']); //6485474

            $builder->where(
                "REPLACE(kunj.kd_pasien, '-', '') = '{$medrec}'", null, false);
        }

        if (!empty($filter['nama_pasien'])) {
            $builder->where("LOWER(abo.nmpasien) LIKE '%" . strtolower($filter['nama_pasien']) . "%'",null,false);
        }

        if (!empty($filter['unit'])) {
            if ($filter['unit'] === '1') {
                // Rawat Inap
                $builder->where(
                    "LEFT(abo.kd_unit, 1) = '1'",
                    null,
                    false
                );
            }

            if ($filter['unit'] === '2') {
                // Rawat Jalan
                $builder->where(
                    "LEFT(abo.kd_unit, 1) = '2'",
                    null,
                    false
                );
            }

            if ($filter['unit'] === '3') {
                // Rawat IGD
                $builder->where(
                    "LEFT(abo.kd_unit, 1) = '3'",
                    null,
                    false
                );
            }

        }
        
        $rows = $builder->orderBy('abo.tgl_out, abo.nmpasien', 'ASC')
                        ->get()
                        ->getResultArray();
        // echo $this->db->getLastQuery()->getQuery();
        // die;
        $grouped = [];

        foreach ($rows as $row) {
            $key = $row['no_out'].'_'.$row['tgl_out'];

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'no_out'       => $row['no_out'],
                    'tgl_out'      => $row['tgl_out'],
                    'no_resep'     => $row['no_resep'],
                    'kd_pasienapt' => $row['kd_pasienapt'],
                    'nmpasien'     => $row['nmpasien'],
                    'nama_unit'    => $row['nama_unit'],
                    'no_sjp'       => $row['no_sjp'],
                    'obat'         => []
                ];
            }

            if (!empty($row['kd_prd'])) {
                $grouped[$key]['obat'][] = [
                    'kd_prd'        => $row['kd_prd'],
                    'nama_obat'     => $row['nama_obat'],
                    'jml_out'       => $row['jml_out'],
                    'harga_jual'    => $row['harga_jual'],
                    'nama_unit'     => $row['nama_unit'],
                ];
            }
        }

        return array_values($grouped);
    }

    public function getResepGrouped(array $filter = [])
    {
        $builder = $this->builder();

        $builder->select('
            abo.no_out,
            abo.tgl_out,
            abo.no_resep,
            abo.kd_pasienapt,
            abo.nmpasien,
            abo.kd_unit,
            abod.kd_prd,
            abod.jml_out,
            abod.harga_jual,
            apt_obat.nama_obat,
            un.nama_unit,
            kunj.no_sjp,
            cust.customer
        ')
        ->join('apt_barang_out_detail abod', 'abo.no_out = abod.no_out AND abo.tgl_out = abod.tgl_out', 'left')
        ->join('apt_obat', 'abod.kd_prd = apt_obat.kd_prd', 'left')
        ->join('unit un', 'un.kd_unit = abo.kd_unit', 'left')
        ->join('kunjungan kunj', 'kunj.kd_pasien = abo.kd_pasienapt and kunj.kd_unit = abo.kd_unit and kunj.tgl_masuk = abo.tgl_out', 'INNER')
        ->join('customer cust', 'kunj.kd_customer = cust.kd_customer', 'INNER');
        
        $builder->whereIn('kunj.kd_customer', ['0000000043', '0000000044']);
        $builder->where('abo.tgl_out >=', $filter['tgl_awal'].' 00:00:00')
                ->where('abo.tgl_out <=', $filter['tgl_akhir'].' 00:00:00')
                ->where('tutup =',1);
        
        if (!empty($filter['medrec'])) {
            // $builder->where('abo.kd_pasienapt', $filter['medrec']);
            $medrec = trim($filter['medrec']); //6485474

            $builder->where(
                "REPLACE(abo.kd_pasienapt, '-', '') = '{$medrec}'", null, false);
        }

        if (!empty($filter['nama_pasien'])) {
            $builder->where("LOWER(abo.nmpasien) LIKE '%" . strtolower($filter['nama_pasien']) . "%'",null,false);
        }

        if (!empty($filter['unit'])) {
            if ($filter['unit'] === '1') {
                // Rawat Inap
                $builder->where(
                    "LEFT(abo.kd_unit, 1) = '1'",
                    null,
                    false
                );
            }

            if ($filter['unit'] === '2') {
                // Rawat Jalan
                $builder->where(
                    "LEFT(abo.kd_unit, 1) = '2'",
                    null,
                    false
                );
            }

            if ($filter['unit'] === '3') {
                // Rawat IGD
                $builder->where(
                    "LEFT(abo.kd_unit, 1) = '3'",
                    null,
                    false
                );
            }

        }
        
        $rows = $builder->orderBy('abo.tgl_out, abo.nmpasien', 'ASC')
                        ->get()
                        ->getResultArray();
        // echo $this->db->getLastQuery()->getQuery();
        // die;
        $grouped = [];

        foreach ($rows as $row) {
            $key = $row['no_out'].'_'.$row['tgl_out'];

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'no_out'       => $row['no_out'],
                    'tgl_out'      => $row['tgl_out'],
                    'no_resep'     => $row['no_resep'],
                    'kd_pasienapt' => $row['kd_pasienapt'],
                    'nmpasien'     => $row['nmpasien'],
                    'nama_unit'    => $row['nama_unit'],
                    'no_sjp'       => $row['no_sjp'],
                    'customer'     => $row['customer'],
                    'obat'         => []
                ];
            }

            if (!empty($row['kd_prd'])) {
                $grouped[$key]['obat'][] = [
                    'kd_prd'        => $row['kd_prd'],
                    'nama_obat'     => $row['nama_obat'],
                    'jml_out'       => $row['jml_out'],
                    'harga_jual'    => $row['harga_jual'],
                    'nama_unit'     => $row['nama_unit'],
                ];
            }
        }

        return array_values($grouped);
    }
}
