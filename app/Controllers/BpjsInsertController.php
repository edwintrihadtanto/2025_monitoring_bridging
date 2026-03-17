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


    public function kirimresep()
    {   
        $request = $this->request->getJSON(true);
        if (!$request) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Payload kosong'
            ]);
        }

        $no_out     = $request['no_out'] ?? null;
        $tgl_out    = $request['tgl_out'] ?? null;
        $kdpasien   = $request['kdpasien'] ?? null;
        $noresep    = $request['noresep'] ?? null;
        $refasalsjp = $request['sep'] ?? null;
        $kd_unit    = $request['kd_unit'] ?? null;
        $sts_iter   = $request['sts_iter'] ?? null;
        $iterasi    = 0;
        $kd_dokter  = $request['kd_dokter'] ?? null;
        $kdjnsobat  = $request['kdjnsobat'] ?? null;

        $detailObat = $request['detailobat'] ?? [];
        // var_dump($no_out, $tgl_out, $kdpasien, $noresep, $refasalsjp, $kd_dokter, $kd_unit, $detailObat);
        // die;
        if (!$no_out || !$tgl_out || !$kdpasien || !$noresep || !$refasalsjp || !$kd_dokter || !$kd_unit || empty($detailObat)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Parameter masih ada yang kurang!'
            ]);
        }

        try {

            $ResepModel = new \App\Models\ResepModel();

            $tglresep       = $tgl_out;
            $tglpelayanan   = $tgl_out;
            $userID         = session()->get('id');

            $poliData = $ResepModel->getMappingUnitBPJS($kd_unit);

            if (!$poliData) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Mapping Unit BPJS tidak ditemukan!'
                ]);
            }

            $poli = $poliData[0]['unit_bpjs'];

            $dokterBPJS = $ResepModel->getMappingDokterBPJS($kd_dokter);

            if (!$dokterBPJS) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Mapping Dokter BPJS tidak ditemukan!'
                ]);
            }

            $kd_dokterbpjs = $dokterBPJS[0]['kd_dokter_bpjs'];

            $mappingResep = $ResepModel->getMappingResepBPJS($noresep, $tglresep);

            if ($mappingResep) {
                if ($mappingResep['status_kirim'] == 't') {
                    return $this->response->setJSON([
                        'status'  => false,
                        'message' => 'Resep sudah pernah berhasil dikirim ke BPJS!<br>No.Resep:'.$mappingResep['noresep_bpjs'],
                        'noresep_bpjs' => $mappingResep['noresep_bpjs']
                    ]);
                }

                $noresep_bpjs = $mappingResep['noresep_bpjs'];
            } else {
                $noresep_bpjs = $ResepModel->generateNoResepBpjs($tglresep);

                $ResepModel->insertMappingResepBPJS(
                    $noresep,
                    $noresep_bpjs,
                    $no_out,
                    $tglresep,
                    false
                );
            }

            // var_dump($no_out, $tgl_out, $kdpasien, $noresep, $refasalsjp, $kd_dokter, $kd_unit, $poli, $kd_dokterbpjs);
            // var_dump($refasalsjp, $poli, $noresep, $tglresep, $tglpelayanan, $kd_dokterbpjs, $iterasi, $userID);
            // die;
            $targetUrl = base_url("bpjs/insert/getkirimresep/{$refasalsjp}/{$poli}/{$noresep_bpjs}/{$tglresep}/{$tglpelayanan}/{$kd_dokterbpjs}/{$iterasi}/$kdjnsobat/{$userID}");

            $client = Services::curlrequest([
                'timeout' => 60,
            ]);

            $response = $client->get($targetUrl, [
                'headers' => [
                    'X-Internal-Request' => 'TRUE'
                ]
            ]);

            $wrapper = json_decode($response->getBody(), true);
            $bpjsJson = $wrapper['body'] ?? $wrapper;

            $statusResult = false;
            $message = 'Respon server BPJS tidak dikenali';
            $data = null;

            /* ===============================
               RESPONSE SUKSES BPJS
            ================================ */
            if (isset($bpjsJson['status_code']) && $bpjsJson['status_code'] == '200') {

                $statusResult = true;
                $message = $bpjsJson['message'] ?? 'Resep berhasil dikirim ke BPJS';
                $data = $bpjsJson['data'] ?? null;
                $noApotik = $bpjsJson['data']['noApotik'] ?? null;

                $responseUpdate = [
                    'response' => $bpjsJson,
                    'message'  => $message
                ];

                $ResepModel->updateMappingResepBPJS(
                    $noresep,
                    $no_out,
                    $tglresep,
                    true,
                    $responseUpdate
                );
            }

            /* ===============================
               RESPONSE ERROR BPJS
            ================================ */
            elseif (isset($bpjsJson['status']) && $bpjsJson['status'] == 'gagal') {

                $statusResult = false;
                $message = $bpjsJson['message'] ?? 'Terjadi kesalahan pada API BPJS';

                $ResepModel->updateMappingResepBPJS(
                    $noresep,
                    $no_out,
                    $tglresep,
                    false,
                    $bpjsJson
                );
            }

            /* ===============================
               RESPONSE TIDAK DIKENALI
            ================================ */
            else {

                $message = json_encode($bpjsJson);
            }

            return $this->response->setJSON([
                'status' => $statusResult,
                'message' => $message,
                'data' => $data
            ]);

        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal terhubung ke API BPJS '. $e->getMessage(),
                'error'   => $e->getMessage()
            ]);
        }
    }

    public function getdaftarresep()
    {
        $tgl_awal = $this->request->getPost('tgl_awal');
        $tgl_akhr = $this->request->getPost('tgl_akhr');
        $jns_obat = $this->request->getPost('jns_obat');
        if (!$tgl_awal || !$tgl_akhr) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Tanggal awal dan akhir wajib diisi'
            ]);
        }

        try {

            $userID    = session()->get('id');
            $targetUrl = base_url("bpjs/insert/daftarresep/{$tgl_awal}/{$tgl_akhr}/$jns_obat/{$userID}");

            $client = Services::curlrequest([ 'timeout' => 60 ]);

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

    public function del_hapusresepXX()
    {
        $no_resep   = $this->request->getPost('no_resep');
        $no_apotik  = $this->request->getPost('no_apotik');
        $refasalsjp = $this->request->getPost('refasalsjp');

        if (!$no_resep) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'No Resep Tidak diketahui'
            ]);
        }

        if (!$no_apotik) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'NoSjp Apotik Tidak diketahui'
            ]);
        }

        if (!$refasalsjp) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Referensi Asal SEP Tidak diketahui'
            ]);
        }

        try {

            $userID    = session()->get('id');
            $targetUrl = base_url("bpjs/delete/del_hapusresep/{$no_resep}/{$no_apotik}/{$refasalsjp}/{$userID}");
            // var_dump($wrapper);exit();
            $client = Services::curlrequest([
                'timeout' => 60,
                'http_errors' => false,
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

                // Cek kalau response null
                if (is_null($bpjsJson['data'])) {
                    $statusResult = true;
                    $message = $bpjsJson['metaData']['message'];
                } else {
                    $message = 'Respon tidak diketahui!';
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

    public function del_hapusresep()
    {
        $no_resep   = $this->request->getPost('no_resep');
        $no_apotik  = $this->request->getPost('no_apotik');
        $refasalsjp = $this->request->getPost('refasalsjp');
        $byverrsp   = $this->request->getPost('byverrsp');

        // ================= VALIDASI =================
        if (!$no_resep) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'No Resep Tidak diketahui'
            ]);
        }

        if (!$no_apotik) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'NoSJP Apotik Tidak diketahui'
            ]);
        }

        if (!$refasalsjp) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Referensi Asal SEP Tidak diketahui'
            ]);
        }

        if ($byverrsp !== '0') {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Resep sudah diverifikasi dan tidak dapat dihapus',
                'csrfHash' => csrf_hash()
            ]);
        }
        
        try {
            $userID    = session()->get('id');
            $targetUrl = base_url(
                "bpjs/delete/del_hapusresep/{$no_resep}/{$no_apotik}/{$refasalsjp}/{$userID}"
            );

            $client = \Config\Services::curlrequest([
                'timeout'     => 60,
                'http_errors' => false,
            ]);

            $response = $client->get($targetUrl, [
                'headers' => [
                    'X-Internal-Request' => 'TRUE'
                ]
            ]);

            // ================= HANDLE RESPONSE =================
            $body = trim($response->getBody());

            /**
             * CASE 1:
             * Body benar-benar kosong → DELETE SUKSES
             */
            if ($body === '') {
                return $this->response->setJSON([
                    'status'  => true,
                    'message' => 'Resep '.$no_resep.' Berhasil di Hapus',
                    'csrfHash' => csrf_hash()
                ]);
            }

            /**
             * CASE 2:
             * Body = JSON string kosong → "\"\"" → DELETE SUKSES
             */
            if ($body === '""') {
                return $this->response->setJSON([
                    'status'  => true,
                    'message' => 'Resep '.$no_resep.' Berhasil di Hapus',
                    'csrfHash' => csrf_hash()
                ]);
            }

            /**
             * CASE 3:
             * Decode JSON
             */
            $wrapper = json_decode($body, true);

            /**
             * CASE 4:
             * Hasil decode STRING kosong → DELETE SUKSES
             */
            if ($wrapper === '') {
                return $this->response->setJSON([
                    'status'  => true,
                    'message' => 'Resep '.$no_resep.' Berhasil di Hapus',
                    'csrfHash' => csrf_hash()
                ]);
            }

            /**
             * CASE 5:
             * Bukan array → response tidak valid
             */
            if (!is_array($wrapper)) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Response BPJS tidak valid',
                    'raw'     => $body
                ]);
            }

            // ================= PARSE JSON BPJS =================
            $bpjsJson = $wrapper['body'] ?? $wrapper;

            $code = $bpjsJson['metaData']['code'] ?? null;

            $message = $bpjsJson['metaData']['message']
                ?? $bpjsJson['pesan']
                ?? $bpjsJson['message']
                ?? 'Respon server BPJS tidak dikenali';

            /**
             * CASE 6:
             * BPJS kirim error (404, dll)
             */
            if ($code !== '200') {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => $message
                ]);
            }

            /**
             * CASE 7:
             * BPJS sukses + body JSON ada
             */
            return $this->response->setJSON([
                'status'  => true,
                'message' => $message
            ]);

        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Kesalahan sistem: ' . $e->getMessage(),
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

    public function getdaftar_pelayananHARDCODE()
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
            $bpjsJson = [
                            'response' => [   
                                'detailsep' => 
                                [
                                    [
                                        "noSepApotek" => "1801A00104190000001",
                                        "noSepAsal" => "1801R0010419V000001",
                                        "noresep" => "12345",
                                        "nokartu" => "1234567891011",
                                        "nmpst" => "TES HARDCODE",
                                        "kdjnsobat" => "Obat Kemoterapi",
                                        "nmjnsobat" => "2025-01-10",
                                        "tglpelayanan" => "50000000",
                                        "listobat" => [
                                            "kodeobat" => "25180404057",
                                            "namaobat" => "Amlodipin 10 Plab tab 10 mg",
                                            "tipeobat" => "N",
                                            "signa1" => "1.00",
                                            "signa2" => "1.00",
                                            "hari" => "23.00",
                                            "permintaan" => null,
                                            "jumlah" => "23.00",
                                            "harga" => "2797"
                                        ]
                                    ],
                                    [
                                        "noSepApotek" => "1801A00104190000001",
                                        "noSepAsal" => "1801R0010419V000001",
                                        "noresep" => "54321",
                                        "nokartu" => "1234567891011",
                                        "nmpst" => "TES HARDCODE 2",
                                        "kdjnsobat" => "Obat Kemoterapi",
                                        "nmjnsobat" => "2025-01-10",
                                        "tglpelayanan" => "50000000",
                                        "listobat" => [
                                            "kodeobat" => "25180404057",
                                            "namaobat" => "Amlodipin 10 Plab tab 10 mg",
                                            "tipeobat" => "N",
                                            "signa1" => "1.00",
                                            "signa2" => "1.00",
                                            "hari" => "23.00",
                                            "permintaan" => null,
                                            "jumlah" => "23.00",
                                            "harga" => "2797"
                                        ]
                                    ]
                                ]
                            ],
                            'metaData' => [
                                'code' => "200",
                                'message' => "Ok Hardcode"
                            ]
                        ];

            $statusResult = false;
            $message = '';
            $htmlResult = '';

            if (isset($bpjsJson['metaData']['code']) && $bpjsJson['metaData']['code'] == "200") {
                
                if (!is_null($bpjsJson['response'])) {
                    if (!empty($bpjsJson['response']['detailsep'])) {
                        $statusResult = true;
                        $dataList = $bpjsJson['response']['detailsep'];
                        // var_dump($dataList);
                        $htmlResult = view('resep/partial_pelobat_listpel', ['dataList' => $dataList]);
                    } else {
                        $message = 'Data Obat kosong.';
                    }
                } else {
                    $message = 'Data Obat tidak ditemukan (Response Null).';
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

    public function getriwayat_pelayanan()
    {
        $tgl_awal = $this->request->getPost('tgl_awal');
        $tgl_akhr = $this->request->getPost('tgl_akhr');
        $no_kartu = $this->request->getPost('no_kartu');

        if (empty($no_kartu)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'No. Kartu BPJS tidak boleh kosong.'
            ]);
        }

        try {
            $userID    = session()->get('id');
            $targetUrl = base_url(
                'bpjs/riwayatpelayananobat/' .
                $tgl_awal.'/'.$tgl_akhr.'/'.$no_kartu.'/'.$userID
            );

            $client   = \Config\Services::curlrequest();
            $response = $client->get($targetUrl, [
                'headers' => ['X-Internal-Request' => 'TRUE']
            ]);

            $wrapper  = json_decode($response->getBody(), true);
            $bpjsJson = $wrapper['body'] ?? $wrapper;

            /* ================= VALIDASI RESPONSE ================= */

            if (
                !isset($bpjsJson['metaData']['code']) ||
                $bpjsJson['metaData']['code'] !== '200'
            ) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => $bpjsJson['metaData']['message']
                                ?? $bpjsJson['pesan'] 
                                ?? $bpjsJson['message'] 
                                ?? 'Respon BPJS tidak valid'
                ]);
            }

            $list = $bpjsJson['response']['list'] ?? null;
            $history = $list['history'] ?? [];

            if (empty($history)) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data Riwayat Pelayanan Obat Kosong.'
                ]);
            }

            /* ================= RENDER VIEW ================= */

            $html = view('resep/partial_pelobat_riwayatpel', ['data' => $bpjsJson ]);

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data ditemukan',
                'html'    => $html
            ]);

        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal terhubung ke API BPJS!<br>Error ' . $e->getMessage(),
                'error'   => $e->getMessage()
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