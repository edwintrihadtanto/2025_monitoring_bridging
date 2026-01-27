<?php

namespace App\Controllers;
use Config\Services;
use App\Models\ResepModel;

class BpjsInsertController extends BaseController
{

    public function viewgetdaftar_resep(){
        return $this->renderView('resep/sidebar-listresep');
    }

    public function viewpelobat_listpersep(){
        return $this->renderView('resep/sidebar-pelobat-listpel');
    }

    public function viewpelobat_riwayat(){
        return $this->renderView('resep/sidebar-pelobat-riwayat');
    }

    public function viewresepsimrs(){
        return $this->renderView('resep/sidebar-resepsimrs');
    }

    public function getdaftarresep()
    {
        $tgl_awal = $this->request->getPost('tgl_awal');
        $tgl_akhr = $this->request->getPost('tgl_akhr');

        if (!$tgl_awal || !$tgl_akhr) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Tanggal awal dan akhir wajib diisi'
            ]);
        }

        try {

            // ==========================
            // PANGGIL ENDPOINT INTERNAL
            // ==========================
            $targetUrl = base_url("bpjs/insert/daftarresep/{$tgl_awal}/{$tgl_akhr}");

            $client = Services::curlrequest([
                'timeout' => 60,
            ]);

            $response = $client->get($targetUrl, [
                'headers' => [
                    'X-Internal-Request' => 'TRUE'
                ]
            ]);

            $wrapper = json_decode($response->getBody(), true);
            // var_dump($wrapper);exit();
            $bpjsJson = $wrapper['body'] ?? $wrapper;
            // var_dump($bpjsJson);
            $statusResult = false;
            $message = '';
            $htmlResult = '';

            if (isset($bpjsJson['metaData']['code']) && $bpjsJson['metaData']['code'] == "200") {

                // Cek kalau response tidak null
                if (!is_null($bpjsJson['data'])) {
                    if (!empty($bpjsJson['response']['list'])) {
                        $statusResult = true;
                        $resepList = $bpjsJson['response']['list'];
                        $htmlResult = view('resep/partial_listresep', ['resepList' => $resepList]);
                    } else {
                        $message = 'Data Resep kosong.';
                    }
                } else {
                    $message = 'Data Resep tidak ditemukan (Response Null).';
                }
            } elseif (isset($bpjsJson['status_code']) && $bpjsJson['status_code'] == "200") {

                if (!empty($bpjsJson['data'])) {
                    $statusResult = true;
                    $resepList = $bpjsJson['data'];
                    $htmlResult = view('resep/partial_listresep', ['resepList' => $resepList]);
                } else {
                    $message = 'Data Resep kosong.';
                }
            } else {
                // Pastikan metaData message terbaca
                $message = $bpjsJson['metaData']['message']
                            ?? $bpjsJson['pesan']
                            ?? $bpjsJson['message']
                            ?? 'Respon server BPJS tidak dikenali.';
            }

            return $this->response->setJSON([
                'status' => $statusResult,
                'message' => $message,
                'html' => $htmlResult
            ]);


        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal terhubung ke API BPJS '. $e->getMessage(),
                'error'   => $e->getMessage()
            ]);
        }
    }

    public function getdaftar_pelayanan()
    {
        $sep  = $this->request->getPost('searchsep_value');

        if (empty($sep)) {
            return $this->response->setJSON(['status' => false, 'message' => 'No. Kunjungan / SEP tidak boleh kosong.']);
        }
        
        try {
            $userID     = session()->get('id');
            $baseUrl    = base_url();
            $targetUrl  = $baseUrl . 'bpjs/listpelayananobat_perSEP/' . $sep.'/'.$userID;
            
            $client = Services::curlrequest();
            $response = $client->get($targetUrl, [
                'headers' => ['X-Internal-Request' => 'TRUE']
            ]);
            
            $wrapper = json_decode($response->getBody(), true);
            // var_dump($wrapper);
            $bpjsJson = $wrapper['body'] ?? $wrapper;

            $statusResult = false;
            $message = '';
            $htmlResult = '';

            if (isset($bpjsJson['metaData']['code']) && $bpjsJson['metaData']['code'] == "200") {
                
                if (!is_null($bpjsJson['response'])) {
                    if (!empty($bpjsJson['response']['detailsep'])) {
                        $statusResult = true;
                        $dataList = $bpjsJson['response']['detailsep'];
                        $htmlResult = view('resep/partial_pelobat_listpel', ['dataList' => $dataList]);
                    } else {
                        $message = 'Data Obat kosong.';
                    }
                } else {
                    $message = 'Data Obat tidak ditemukan (Response Null).';
                }
            }elseif (isset($bpjsJson['status']) && $bpjsJson['status'] == "sukses") {
                
                if (!empty($bpjsJson['data']['detailsep'])) {
                    $statusResult = true;
                    $dataList = $bpjsJson['data']['detailsep'];
                    $htmlResult = view('resep/partial_pelobat_listpel', ['dataList' => $dataList]);
                } else {
                    $message = 'Data Obat kosong.';
                }
            }else {
                $message = $bpjsJson['metaData']['message'] 
                        ?? $bpjsJson['pesan'] 
                        ?? $bpjsJson['message'] 
                        ?? 'Respon server BPJS tidak dikenali.';
            }

            return $this->response->setJSON([
                'status' => $statusResult,
                'message' => $message,
                'html' => $htmlResult
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'    => false,
                'message'   => 'Gagal terhubung ke API BPJS!<br>Error ' . $e->getMessage(),
                'error'     => $e->getMessage()
            ]);
        }
    }

    public function getResepSIMRSLAMA()
    {
        $ResepModel = new ResepModel();
        $searchType = $this->request->getPost('search_typepasien');

        $filter = [
            'tgl_awal'  => trim($this->request->getPost('tgl_awal')),
            'tgl_akhir' => trim($this->request->getPost('tgl_akhr')),
            'unit'      => trim($this->request->getPost('option_radio')),
        ];

        if ($searchType === 'medrec') {
            $filter['medrec'] = trim($this->request->getPost('medrec'));
        } else {
            $filter['nama_pasien'] = trim($this->request->getPost('nama_pasien'));
        }

        if (
            (!empty($filter['tgl_awal']) && empty($filter['tgl_akhir'])) || (empty($filter['tgl_awal']) && !empty($filter['tgl_akhir']))
        ) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Tgl Awal & Akhir harus diisi.'
            ]);
        }

        if (!empty($filter['tgl_awal']) && !empty($filter['tgl_akhir'])) {
            if (strtotime($filter['tgl_awal']) > strtotime($filter['tgl_akhir'])) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Tgl Awal tidak boleh lebih besar dari Tgl Akhir'
                ]);
            }
        }

        try {
            
            $statusResult = false;
            $message = '';
            $htmlResult = '';
            
            $rekap = $ResepModel->getResepGrouped($filter);

            if (!empty($rekap)) {
                $statusResult = true;
                $message = 'Data ditemukan';
                $htmlResult = view('resep/partial_resepsimrs', ['dataList' => $rekap]);
            } else {
                $message = 'Data Obat tidak ditemukan!';
            }
    
            return $this->response->setJSON([
                'status' => $statusResult,
                'message' => $message,
                'html' => $htmlResult
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'    => false,
                'message'   => 'Error ' . $e->getMessage(),
                'error'     => $e->getMessage()
            ]);
        }
    }

    public function getResepSIMRSLAMA2()
    {
        $ResepModel = new ResepModel();
        $searchType = $this->request->getPost('search_typepasien');

        $filter = [
            'tgl_awal'  => trim($this->request->getPost('tgl_awal')),
            'tgl_akhir' => trim($this->request->getPost('tgl_akhr')),
            'unit'      => trim($this->request->getPost('option_radio')),
        ];

        if ($searchType === 'medrec') {
            $filter['medrec'] = trim($this->request->getPost('medrec'));
        } else {
            $filter['nama_pasien'] = trim($this->request->getPost('nama_pasien'));
        }

        /* VALIDASI TANGGAL */
        if (
            (!empty($filter['tgl_awal']) && empty($filter['tgl_akhir'])) ||
            (empty($filter['tgl_awal']) && !empty($filter['tgl_akhir']))
        ) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Tgl Awal & Akhir harus diisi.'
            ]);
        }

        if (!empty($filter['tgl_awal']) && !empty($filter['tgl_akhir'])) {
            if (strtotime($filter['tgl_awal']) > strtotime($filter['tgl_akhir'])) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Tgl Awal tidak boleh lebih besar dari Tgl Akhir'
                ]);
            }
        }

        try {

            $rekap = $ResepModel->getResepGrouped($filter);

            if (empty($rekap)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data Obat tidak ditemukan!'
                ]);
            }

            $grouped = [];

            foreach ($rekap as $row) {

                $tgl = $row['tgl_out'];

                if (!isset($grouped[$tgl])) {
                    $grouped[$tgl] = [
                        'tgl'  => $tgl,
                        'data' => []
                    ];
                }

                $grouped[$tgl]['data'][] = $row;
            }


            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data ditemukan',
                'html'    => view('resep/partial_resepsimrs', [
                    'groups' => $grouped
                ])
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Error ' . $e->getMessage()
            ]);
        }
    }

    public function getResepSIMRS()
    {
        $ResepModel = new ResepModel();
        $searchType = $this->request->getPost('search_typepasien');

        $filter = [
            'tgl_awal'  => trim($this->request->getPost('tgl_awal')),
            'tgl_akhir' => trim($this->request->getPost('tgl_akhr')),
            'unit'      => trim($this->request->getPost('option_radio')),
        ];

        if ($searchType === 'medrec') {
            $filter['medrec'] = trim($this->request->getPost('medrec'));
        } else {
            $filter['nama_pasien'] = trim($this->request->getPost('nama_pasien'));
        }

        if (
            (!empty($filter['tgl_awal']) && empty($filter['tgl_akhir'])) ||
            (empty($filter['tgl_awal']) && !empty($filter['tgl_akhir']))
        ) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Tgl Awal & Akhir harus diisi.'
            ]);
        }

        if (!empty($filter['tgl_awal']) && !empty($filter['tgl_akhir'])) {
            if (strtotime($filter['tgl_awal']) > strtotime($filter['tgl_akhir'])) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Tgl Awal tidak boleh lebih besar dari Tgl Akhir'
                ]);
            }
        }

        try {

            $rekap = $ResepModel->getResepHeader($filter);

            if (empty($rekap)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data Obat tidak ditemukan!'
                ]);
            }

            $grouped = [];

            foreach ($rekap as $row) {

                $tglKey = date('Y-m-d', strtotime($row['tgl_out']));

                if (!isset($grouped[$tglKey])) {
                    $grouped[$tglKey] = [
                        'tgl'  => $tglKey,
                        'data' => []
                    ];
                }

                $grouped[$tglKey]['data'][] = $row;
            }

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data ditemukan',
                'html'    => view('resep/partial_resepsimrs', [
                    'groups' => $grouped
                ])
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Error ' . $e->getMessage()
            ]);
        }
    }

    public function getDetailObat()
    {
        $ResepModel = new ResepModel();

        $noOut  = $this->request->getPost('no_out');
        $tglOut = $this->request->getPost('tgl_out');

        if ((!empty($noOut)) && (!empty($tglOut))) {
            $filter = [
                'noOut'  => trim($noOut),
                'tglOut' => trim($tglOut)
            ];

            $getdetail = $ResepModel->getDetailObat($filter);
            
            // $data = $this->db->query("
            //     SELECT kd_prd, nama_obat
            //     FROM apt_barang_out_detail
            //     INNER JOIN apt_obat USING (kd_prd)
            //     WHERE no_out = ?
            //       AND tgl_out = ?
            //     ORDER BY nama_obat
            // ", [$noOut, $tglOut])->getResultArray();

            return view('resep/partial_detail_obat', [
                'detail' => $getdetail
            ]);

        }else{
            return $this->response->setJSON([
                'status' => false,
                'message' => 'No Out dan Tgl Transaksi Tidak diketahui!'
            ]);
        }

        
    }

}