<?php

namespace App\Models;

use CodeIgniter\Model;

class ResepModel extends Model
{
    protected $DBGroup          = 'dbSIMRS';
    //CRUD standar (find, insert, update)
    protected $table            = 'apt_barang_out'; 
    //update(), delete()
    protected $primaryKey       = ['no_out']; 
    // insert() / save()
    protected $allowedFields    = ['no_out', 'tgl_out', 'kd_resep', 'kd_pasien', 'nm_pasien', 'tanggal'];
    protected $useTimestamps    = false;

    public function getResepGroupedXX(array $filter = [])
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

    public function getResepGrouped_last(array $filter = [])
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

    public function getResepHeader(array $filter = [])
    {
        // $builder = $this->builder();

        $builder = $this->builder('apt_barang_out o');
        $builder->distinct();

        $builder->select("
            o.no_resep,
            o.tutup AS status_posting,
            o.no_out,
            o.tgl_out,
            o.kd_pasienapt,
            o.nmpasien,
            o.dokter,
            d.nama AS nama_dokter,
            o.kd_unit,
            u.nama_unit,
            o.apt_no_transaksi,
            T.tgl_transaksi,
            o.apt_kd_kasir,
            o.admracik,
            o.jasa,
            o.admprhs,
            o.admresep,
            CASE
                WHEN ko.jenis_cust = 0 THEN 'Perorangan'
                WHEN ko.jenis_cust = 1 THEN 'Perusahaan'
                WHEN ko.jenis_cust = 2 THEN 'Asuransi'
            END AS jenis_pasien,
            kun.tgl_masuk,
            kun.urut_masuk,
            o.catatandr,
            kun.no_sjp,
            py.kd_pay,
            py.uraian AS payment,
            pyt.jenis_pay,
            pyt.deskripsi AS payment_type,
            o.tgl_resep,
            sjp.no_sjp AS no_sep,
            o.id_mrresep,
            mr.cat_alergi,
            o.siapa,
            o.sts_kronis,
            o.sts_iter,
            CASE
                WHEN o.kd_customer NOT IN ('0000000043', '0000000044') THEN '1' ELSE '0'
            END AS kd_customer_status,
            kun.kd_customer as kd_customer_kunjungan,
            o.kd_customer as kd_customer_apt_brangout,
            C.customer
        ");

        // JOIN
        $builder->join('unit u', 'o.kd_unit = u.kd_unit', 'left');
        $builder->join('dokter d', 'o.dokter = d.kd_dokter', 'left');
        $builder->join('customer C', 'C.kd_customer = o.kd_customer', 'left');
        $builder->join('kontraktor ko', 'C.kd_customer = ko.kd_customer', 'left');
        // $builder->join('apt_barang_out_detail bo', 'bo.no_out = o.no_out AND bo.tgl_out = o.tgl_out', 'left');
        $builder->join('transaksi T', 'T.no_transaksi = o.apt_no_transaksi AND T.kd_kasir = o.apt_kd_kasir', 'left');
        $builder->join(
            'kunjungan kun',
            'T.kd_pasien = kun.kd_pasien 
             AND T.kd_unit = kun.kd_unit 
             AND T.urut_masuk = kun.urut_masuk 
             AND T.tgl_transaksi = kun.tgl_masuk 
             ',
            'left'
        );
        $builder->join('payment py', 'py.kd_customer = o.kd_customer', 'inner');
        $builder->join('payment_type pyt', 'pyt.jenis_pay = py.jenis_pay', 'inner');
        $builder->join(
            'sjp_kunjungan sjp',
            'sjp.kd_pasien = kun.kd_pasien 
             AND sjp.tgl_masuk = kun.tgl_masuk 
             AND sjp.kd_unit = kun.kd_unit 
             AND sjp.urut_masuk = kun.urut_masuk',
            'left'
        );
        $builder->join('mr_resep mr', 'o.id_mrresep = mr.id_mrresep', 'left');

        // WHERE utama
        $builder->whereIn('kun.kd_customer', ['0000000043', '0000000044']);
        $builder->where('o.returapt', 0);
        $builder->where('o.tutup', 1);

        // Filter tanggal
        if (!empty($filter['tgl_awal']) && !empty($filter['tgl_akhir'])) {
            $builder->where('o.tgl_out >=', $filter['tgl_awal'].' 00:00:00');
            $builder->where('o.tgl_out <=', $filter['tgl_akhir'].' 00:00:00');
        }

        if (!empty($filter['medrec'])) {
            // $builder->where('abo.kd_pasienapt', $filter['medrec']);
            $medrec = trim($filter['medrec']); //6485474

            // $builder->where("REPLACE(o.kd_pasienapt, '-', '') = '{$medrec}'", null, false);
            $builder->where(
                "REPLACE(kun.kd_pasien, '-', '') =",
                $medrec,
                false
            );
        }

        if (!empty($filter['nama_pasien'])) {
            // $builder->where("LOWER(abo.nmpasien) LIKE '%" . strtolower($filter['nama_pasien']) . "%'",null,false);
            $builder->like('LOWER(o.nmpasien)', strtolower($filter['nama_pasien']), 'both', false);

        }

        if (!empty($filter['unit'])) {
            if ($filter['unit'] === '1') {
                // Rawat Inap
                $builder->where(
                    "LEFT(kun.kd_unit, 1) = '1'",
                    null,
                    false
                );
            }

            if ($filter['unit'] === '2') {
                // Rawat Jalan
                $builder->where(
                    "LEFT(kun.kd_unit, 1) = '2'",
                    null,
                    false
                );
            }

            if ($filter['unit'] === '3') {
                // Rawat IGD
                $builder->where(
                    "LEFT(kun.kd_unit, 1) = '3'",
                    null,
                    false
                );
            }

        }

        $builder->orderBy('o.tgl_out', 'ASC');
        $builder->orderBy('o.nmpasien', 'ASC');
        //         ->get()
        //         ->getResultArray();
        // echo $this->db->getLastQuery()->getQuery();
        // die;
        return $builder->get()->getResultArray();
    }

    public function getDetailObat(array $filter = [])
    {
        $builder = $this->builder('apt_barang_out_detail abod');
       
        $builder->select("
            abod.kd_prd,
            abod.jml_out,
            abod.harga_jual,
            apt_obat.nama_obat
        ");

        $builder->join('apt_obat', 'abod.kd_prd = apt_obat.kd_prd', 'inner');
       
        if (!empty($filter['noOut']) && !empty($filter['tglOut'])) {
            $builder->where('no_out =', $filter['noOut']);
            $builder->where('tgl_out =', $filter['tglOut']);
        }

        $builder->orderBy('no_urut', 'ASC');
        //         ->get()
        //         ->getResultArray();
        // echo $this->db->getLastQuery()->getQuery();
        // die;
        return $builder->get()->getResultArray();
    }

}
