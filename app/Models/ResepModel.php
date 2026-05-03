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
            py.kd_pay,
            py.uraian AS payment,
            pyt.jenis_pay,
            pyt.deskripsi AS payment_type,
            o.tgl_resep,            
            o.id_mrresep,
            mr.cat_alergi,
            o.siapa,
            o.sts_kronis,
            o.sts_iter,
            CASE
                WHEN o.kd_customer NOT IN ('0000000043', '0000000044') THEN '1' ELSE '0'
            END AS kd_customer_status,
            kun.kd_customer as kd_customer_kunjungan,
            kun.no_sjp,
            o.kd_customer as kd_customer_apt_brangout,
            C.customer,
            CASE WHEN o.kd_customer = '0000000001' THEN '' ELSE sjp.no_sjp END AS no_sep,
            abrb.noresep_bpjs,
            abrb.status_kirim,
            abrb.response_message,
            abrb.kdjnsobat,
            COALESCE(NULLIF(o.sts_iter,0), abrb.kdjnsobat, o.sts_iter) AS sts_iter_final,
            abrb.iterasi
        ");

        // JOIN
        $builder->join('unit u', 'o.kd_unit = u.kd_unit', 'left');
        $builder->join('dokter d', 'o.dokter = d.kd_dokter', 'left');
        $builder->join('customer C', 'C.kd_customer = o.kd_customer', 'left');
        $builder->join('kontraktor ko', 'C.kd_customer = ko.kd_customer', 'left');
        // $builder->join('apt_barang_out_detail bo', 'bo.no_out = o.no_out AND bo.tgl_out = o.tgl_out', 'left');
        $builder->join('transaksi T', 'T.no_transaksi = o.apt_no_transaksi AND T.kd_kasir = o.apt_kd_kasir', 'left');
        $builder->join('kunjungan kun',
            'T.kd_pasien = kun.kd_pasien 
             AND T.kd_unit = kun.kd_unit 
             AND T.urut_masuk = kun.urut_masuk 
             AND T.tgl_transaksi = kun.tgl_masuk 
             ',
            'left'
        );
        $builder->join('payment py', 'py.kd_customer = o.kd_customer', 'inner');
        $builder->join('payment_type pyt', 'pyt.jenis_pay = py.jenis_pay', 'inner');
        $builder->join('sjp_kunjungan sjp',
            'sjp.kd_pasien = kun.kd_pasien 
             AND sjp.tgl_masuk = kun.tgl_masuk 
             AND sjp.kd_unit = kun.kd_unit 
             AND sjp.urut_masuk = kun.urut_masuk',
            'left'
        );
        $builder->join('mr_resep mr', 'o.id_mrresep = mr.id_mrresep', 'left');
        $builder->join('apt_bridging_resep_bpjs abrb', "abrb.no_out = o.no_out and abrb.tgl_out = o.tgl_out and abrb.sts_batal = 'false'", 'left');

        // WHERE utama
        // $builder->whereIn('kun.kd_customer', ['0000000043', '0000000044']);
        $builder->where('o.returapt', 0);
        // $builder->where('o.tutup', 1);

        // Filter tanggal
        if (!empty($filter['tgl_awal']) && !empty($filter['tgl_akhir'])) {
            $builder->where('o.tgl_out >=', $filter['tgl_awal'].' 00:00:00');
            $builder->where('o.tgl_out <=', $filter['tgl_akhir'].' 00:00:00');
        }

        if (!empty($filter['medrec'])) {
            // $builder->where('abo.kd_pasienapt', $filter['medrec']);
            $medrec = trim($filter['medrec']); //6485474

            $builder->where("REPLACE(o.kd_pasienapt, '-', '') = '{$medrec}'", null, false);
            // $builder->where(
            //     "REPLACE(kun.kd_pasien, '-', '') =",
            //     $medrec,
            //     false
            // );
        }

        if (!empty($filter['nama_pasien'])) {
            $builder->where("LOWER(o.nmpasien) LIKE '%" . strtolower($filter['nama_pasien']) . "%'",null,false);
            // $builder->like('LOWER(o.nmpasien)', strtolower($filter['nama_pasien']), 'both', false);

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
            abod.catatan,
            apt_obat.nama_obat,
            apt_obat_ifrs.kd_obat_bpjs,
            ar.nm_racikan,
            UPPER(aps.signa) || '(' || aps.jenis || ')' AS lbl_signa
        ");

        $builder->join('apt_obat', 'abod.kd_prd = apt_obat.kd_prd', 'inner');
        $builder->join('apt_obat_ifrs', 'abod.kd_prd = apt_obat_ifrs.kd_prd', 'left');
        $builder->join('apt_signa_barang_out asbo', 
            'asbo.no_out = abod.no_out AND asbo.tgl_out = abod.tgl_out AND asbo.no_urut = abod.no_urut','left');      
        $builder->join('apt_signa aps', 'aps.id = asbo.id_signa','left');
        $builder->join('apt_racikan ar', 'ar.kode_racikan = abod.jns_racikan','left');

        if (!empty($filter['noOut']) && !empty($filter['tglOut'])) {
            $builder->where('abod.no_out', $filter['noOut']);
            $builder->where('abod.tgl_out', $filter['tglOut']);
        }

        $builder->groupBy("
            abod.kd_prd,
            abod.jml_out,
            abod.harga_jual,
            abod.catatan,
            apt_obat.nama_obat,
            apt_obat_ifrs.kd_obat_bpjs,
            ar.nm_racikan,
            abod.no_urut,
            aps.signa,
            aps.jenis
        ");

        $builder->orderBy('abod.no_urut', 'ASC');
        /*$builder->orderBy('abod.no_urut', 'ASC')
                ->get()
                ->getResultArray();
        echo $this->db->getLastQuery()->getQuery();
        die;*/
        
        return $builder->get()->getResultArray();
    }

    public function getMappingUnitBPJS($kd_unit)
    {
        $builder = $this->builder('map_unit_bpjs mup');
       
        $builder->select("unit_bpjs, nama_unit_bpjs");
        $builder->where('kd_unit', $kd_unit);

        //         ->get()
        //         ->getResultArray();
        // echo $this->db->getLastQuery()->getQuery();
        // die;
        return $builder->get()->getResultArray();
    }

    public function getMappingDokterBPJS($kd_dokter)
    {
        $builder = $this->builder('dokter');
       
        $builder->select("kd_dokter_bpjs");
        $builder->where('kd_dokter', $kd_dokter);

        //         ->get()
        //         ->getResultArray();
        // echo $this->db->getLastQuery()->getQuery();
        // die;
        return $builder->get()->getResultArray();
    }

    public function generateNoResepBpjs($tgl)
    {
        $builder = $this->builder('apt_bridging_resep_bpjs');

        $builder->select("LPAD((COALESCE(MAX(noresep_bpjs::int),0)+1)::text,5,'0') AS noresep");
        $builder->where('tgl_out', $tgl);
        // $builder->where('DATE(created_at)', 'CURRENT_DATE', false);
        // $builder->where('DATE(tgl_out)', 'CURRENT_DATE', false);

        $query = $builder->get()->getRowArray();

        return $query['noresep'];
    }

    public function insertMappingResepBPJS($noresep_simrs,$noresep_bpjs,$no_out,$tgl_out,$status, $kdjnsobat, $iterasi)
    {
        $builder = $this->builder('apt_bridging_resep_bpjs');

        $builder->insert([
            'noresep_simrs' => $noresep_simrs,
            'noresep_bpjs'  => $noresep_bpjs,
            'no_out'        => $no_out,
            'tgl_out'       => $tgl_out,
            'status_kirim'  => $status,
            'created_at'    => date('Y-m-d H:i:s'),
            'kdjnsobat'     => $kdjnsobat,
            'iterasi'       => $iterasi
        ]);
    }

    public function updateIterResepBPJS($noresep_simrs, $noresep_bpjs, $no_out, $tglresep, $kdjnsobat, $iterasi)
    {
        
            $builder = $this->builder('apt_bridging_resep_bpjs');
                        
            $dataUpdate = [
                'kdjnsobat' => $kdjnsobat,
                'iterasi'   => $iterasi,
            ];            

            return $builder
                ->where('noresep_simrs', $noresep_simrs)
                ->where('noresep_bpjs', $noresep_bpjs)
                ->where('no_out', $no_out)
                ->where('tgl_out', $tglresep)
                ->update($dataUpdate);
    }

    public function updateMappingResepBPJS($noresep_simrs, $noresep_bpjs, $no_out, $tglresep, $status, $response = null)
    {
        /*return $this->db->table('apt_bridging_resep_bpjs')
            ->where('noresep_simrs', $noresep_simrs)
            ->where('no_out', $no_out)
            ->where('tgl_out', $tglresep)
            ->update([
                'status_kirim' => $status,
                'noApotik'      => json_encode($response)
                'response_bpjs' => json_encode($response)
            ]);*/

            $builder = $this->builder('apt_bridging_resep_bpjs');

            $noApotik = null;

            // if ($response && isset($response['response']['data']['noApotik'])) {
            //     $noApotik = $response['response']['data']['noApotik'];
            // }

            if ($response && isset($response['data']['noApotik'])) {
                $noApotik = $response['data']['noApotik'];
            }
            // if ($response['message'] == 'Resep berhasil dikirim ke BPJS') {
            //     $message = 'Ok';
            // }else{
                $message = $response['message'];
            // }

            $dataUpdate = [
                'status_kirim' => $status,
                'response_bpjs' => json_encode($response),
                'response_message' => $message
            ];

            if ($noApotik) {
                $dataUpdate['noApotik'] = $noApotik;
            }

            return $builder
                ->where('noresep_simrs', $noresep_simrs)
                ->where('noresep_bpjs', $noresep_bpjs)
                ->where('no_out', $no_out)
                ->where('tgl_out', $tglresep)
                ->update($dataUpdate);
    }

    public function getMappingResepBPJS($noresep_simrs, $tgl_out)
    {
        return $this->db->table('apt_bridging_resep_bpjs')
            ->where('noresep_simrs', $noresep_simrs)
            ->where('tgl_out', $tgl_out)
            ->where('sts_batal', false)
            ->get()
            ->getRowArray();
    }

    public function deleteMappingResepBPJS($no_resep, $no_apotik, $alasan_hapus)
    {
        $this->db->transBegin();

        // ================= HEADER =================
        $update = $this->db->table('apt_bridging_resep_bpjs')
            ->where('noresep_bpjs', $no_resep)
            ->where('noApotik', $no_apotik)
            ->update([
                'status_kirim' => false,
                'sts_batal'    => true,
                'alasan_batal' => $alasan_hapus
            ]);

        if (!$update) {
            $error = $this->db->error();
            log_message('error', 'UPDATE HEADER GAGAL: ' . json_encode($error));

            $this->db->transRollback();
            return false;
        }

        // ================= DETAIL =================
        $delete = $this->db->table('apt_bridging_resep_detail')
            ->where('noresep_bpjs', $no_resep)
            ->where('no_apotik', $no_apotik)
            ->delete();

        if (!$delete) {
            $error = $this->db->error();
            log_message('error', 'DELETE DETAIL GAGAL: ' . json_encode($error));

            $this->db->transRollback();
            return false;
        }

        $this->db->transCommit();
        return true;
    }

    public function insertLogDetailResepBPJS(
        string $noresep,
        string $no_out,
        string $tgl_out,
        ?string $noresep_bpjs,
        ?string $no_apotik,
        ?string $kd_obat_simrs,   // ← Ubah parameter
        ?string $kd_obat_bpjs,    // ← Tambah parameter
        ?string $nm_obat,
        ?string $signa1,
        ?string $signa2,
        int $jml_obat,
        ?string $jho,
        ?string $cat_khusus,
        bool $status_kirim,
        $response_bpjs,
        ?string $permintaan,
        ?string $jenisracikan
    ) {
        $data = [
            'noresep'       => $noresep,
            'no_out'        => $no_out,
            'tgl_out'       => $tgl_out,
            'noresep_bpjs'  => $noresep_bpjs,
            'no_apotik'     => $no_apotik,
            'kd_obat_simrs' => $kd_obat_simrs,
            'kd_obat_bpjs'  => $kd_obat_bpjs,
            'nm_obat'       => $nm_obat,
            'signa1'        => $signa1,
            'signa2'        => $signa2,
            'jml_obat'      => $jml_obat,
            'jho'           => $jho,
            'cat_khusus'    => $cat_khusus,
            'status_kirim'  => $status_kirim, // Langsung boolean, tanpa ? 1 : 0
            'response_bpjs' => is_array($response_bpjs) ? json_encode($response_bpjs) : $response_bpjs,
            'created_at'    => date('Y-m-d H:i:s'),
            'permintaan'    => $permintaan,
            'jenisracikan'  => $jenisracikan,
        ];

        $this->db->table('apt_bridging_resep_detail')->insert($data);
        return $this->db->insertID();
    }

    public function updateLogDetailResepBPJS(int $id, bool $status_kirim, $response_bpjs)
    {
        $this->db->table('apt_bridging_resep_detail')
            ->where('id', $id)
            ->update([
                'status_kirim'  => $status_kirim, // Langsung boolean
                'response_bpjs' => is_array($response_bpjs) ? json_encode($response_bpjs) : $response_bpjs
            ]);
    }

    /**
     * Get mapping obat SIMRS ke BPJS
     */
    public function getMappingObatBPJS($kdObatSimrs)
    {
        return $this->db->table('apt_obat')
            ->select('apt_obat.kd_prd, nama_obat, kd_obat_bpjs')
            ->join(
                'apt_obat_ifrs', 
                'apt_obat.kd_prd = apt_obat_ifrs.kd_prd AND apt_obat_ifrs.kd_obat_bpjs != 0', 
                'inner'
            )
            ->where('apt_obat.kd_prd', $kdObatSimrs)
            ->orderBy('apt_obat.kd_prd', 'ASC')
            ->get()
            ->getRowArray();
    }

    /**
     * Get daftar kode obat SIMRS yang sudah berhasil dikirim ke BPJS
     */
    public function getDetailObatSukses($noresep, $no_out)
    {
        return $this->db->table('apt_bridging_resep_detail')
            ->select('kd_obat_simrs')
            ->where('noresep', $noresep)
            ->where('no_out', $no_out)
            ->where('status_kirim', true) // true di PostgreSQL akan dibaca sebagai boolean true
            ->get()
            ->getResultArray();
    }

    public function getTipeObat($no_resep, $no_apotik, $kd_obat)
    {
        return $this->db->table('apt_bridging_resep_detail')
            ->select('id, jenisracikan')
            ->where('noresep_bpjs', $no_resep)
            ->where('no_apotik', $no_apotik)
            ->where('kd_obat_bpjs', $kd_obat)
            ->where('status_kirim', true)
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        // $tipe = $data['jenisracikan'] ?? null;

        // if (empty($tipe)) {
        //     return null; // tidak ada data valid
        // }

        // if (strtoupper($tipe) === 'N') {
        //     return 'N';
        // }

        // return 'R';
    }
}
