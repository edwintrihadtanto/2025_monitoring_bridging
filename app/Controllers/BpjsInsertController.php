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


    public function kirimresepLAMA()
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
        // $sts_iter   = $request['iterasi'] ?? null;
        $iterasi    = $request['iterasi'] ?? null;
        $kd_dokter  = $request['kd_dokter'] ?? null;
        $kdjnsobat  = $request['kdjnsobat'] ?? null;

        $kdmodulresep  = $request['kdmodulresep'] ?? null;

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

            if ($kdmodulresep !== '1'){
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
            }else{
                $poli = 'BED';
                $kd_dokterbpjs = '30882';
            }
            

            $mappingResep = $ResepModel->getMappingResepBPJS($noresep, $tglresep);

            if ($mappingResep) {
                if ($mappingResep['status_kirim'] == 't') {
                    return $this->response->setJSON([
                        'status'  => false,
                        'message' => 'Resep sudah pernah berhasil dikirim ke BPJS!<br>No.Resep: '.$mappingResep['noresep_bpjs'].'<br>No.Apotik: '.$mappingResep['noApotik'],
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

            $targetUrlDetailObat = base_url("bpjs/insert/obatnonracikan/{$refasalsjp}/{$poli}/{$noresep_bpjs}/{$tglresep}/{$tglpelayanan}/{$kd_dokterbpjs}/{$iterasi}/$kdjnsobat/{$userID}");

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

    public function kirimresep_BERHASIL()
    {
        $request = $this->request->getJSON(true);
        if (!$request) {
            return $this->response->setJSON(['status' => false, 'message' => 'Payload kosong']);
        }

        // ... (Ambil variabel input sama persis seperti kode Anda sebelumnya) ...
        $no_out     = $request['no_out'] ?? null;
        $tgl_out    = $request['tgl_out'] ?? null;
        $kdpasien   = $request['kdpasien'] ?? null;
        $noresep    = $request['noresep'] ?? null;
        $refasalsjp = $request['sep'] ?? null;
        $kd_unit    = $request['kd_unit'] ?? null;
        $iterasi    = $request['iterasi'] ?? null;
        $kd_dokter  = $request['kd_dokter'] ?? null;
        $kdjnsobat  = $request['kdjnsobat'] ?? null;
        $detailObat = $request['detailobat'] ?? [];
        
        $kdmodulresep  = $request['kdmodulresep'] ?? null;

        // ... (Validasi input sama seperti kode Anda) ...
        if (!$no_out || !$tgl_out || !$kdpasien || !$noresep || !$refasalsjp || !$kd_dokter || !$kd_unit || empty($detailObat)) {
            return $this->response->setJSON(['status'  => false, 'message' => 'Parameter masih ada yang kurang!']);
        }

        try {
            $ResepModel = new \App\Models\ResepModel();
            $tglresep       = $tgl_out;
            $tglpelayanan   = $tgl_out;
            $userID         = session()->get('id');

            if ($kdmodulresep !== '1'){
                $poliData = $ResepModel->getMappingUnitBPJS($kd_unit);
                if (!$poliData) return $this->response->setJSON(['status' => false, 'message' => 'Mapping Unit BPJS tidak ditemukan!']);
                $poli = $poliData[0]['unit_bpjs'];

                $dokterBPJS = $ResepModel->getMappingDokterBPJS($kd_dokter);
                if (!$dokterBPJS) return $this->response->setJSON(['status' => false, 'message' => 'Mapping Dokter BPJS tidak ditemukan!']);
                $kd_dokterbpjs = $dokterBPJS[0]['kd_dokter_bpjs'];

            }else{
                $poli = 'BED';
                $kd_dokterbpjs = '30882';
            }

            // ... (Proses Generate/Check No Resep BPJS) ...
            $mappingResep = $ResepModel->getMappingResepBPJS($noresep, $tglresep);
            if ($mappingResep) {
                if ($mappingResep['status_kirim'] == 't') {
                     return $this->response->setJSON([
                        'status'  => false,
                        'message' => 'Resep sudah pernah berhasil dikirim ke BPJS!<br>No.Resep: '.$mappingResep['noresep_bpjs'].'<br>No.Apotik: '.$mappingResep['noApotik'],
                        'noresep_bpjs' => $mappingResep['noresep_bpjs']
                    ]);
                }
                $noresep_bpjs = $mappingResep['noresep_bpjs'];
            } else {
                $noresep_bpjs = $ResepModel->generateNoResepBpjs($tglresep);
                $ResepModel->insertMappingResepBPJS($noresep, $noresep_bpjs, $no_out, $tglresep, false);
            }

            // ==========================================
            // STEP 1: KIRIM HEADER RESEP
            // ==========================================
            $client = Services::curlrequest(['timeout' => 60]);
            $targetUrl = site_url("bpjs/insert/getkirimresep/{$refasalsjp}/{$poli}/{$noresep_bpjs}/{$tglresep}/{$tglpelayanan}/{$kd_dokterbpjs}/{$iterasi}/{$kdjnsobat}/{$userID}");

            $responseHeader = $client->get($targetUrl, ['headers' => ['X-Internal-Request' => 'TRUE']]);
            
            $bodyHeader = $responseHeader->getBody();
            $wrapperHeader = json_decode($bodyHeader, true);
            $bpjsJson = $wrapperHeader['body'] ?? $wrapperHeader;

            $code = $bpjsJson['status_code'] ?? $bpjsJson['metaData']['code'] ?? '500';
            
            if ($code != '200') {
                $msg = $bpjsJson['message'] ?? $bpjsJson['metaData']['message'] ?? 'Gagal membuat header resep di BPJS';
                return $this->response->setJSON(['status' => false, 'message' => $msg]);
            }

            // Ambil Data Penting
            $dataResponse = $bpjsJson['data'] ?? null;
            $noApotik    = $dataResponse['noApotik'] ?? null;
            $noResepBPJS = $dataResponse['noResep'] ?? $noresep_bpjs;
            $ResepModel->updateMappingResepBPJS($noresep, $no_out, $tglresep, true, $bpjsJson);

            if (!$noApotik) {
                 return $this->response->setJSON(['status' => false, 'message' => 'Header Resep berhasil, tapi No Apotik tidak ditemukan.']);
            }

            // ==========================================
            // STEP 2: KIRIM DETAIL OBAT (Menggunakan Fungsi Terpisah)
            // ==========================================
            $resultObat = $this->_sendDetailObat($detailObat, $noApotik, $noResepBPJS, $kdjnsobat, $userID, $noresep, $no_out);

            // ==========================================
            // FINALISASI
            // ==========================================
            if (empty($resultObat['errors'])) {
                // Update status kirim menjadi TRUE
                
                return $this->response->setJSON([
                    'status'  => true,
                    'message' => "Resep & Detail Obat berhasil dikirim. No Apotik: {$noApotik}",
                    'data'    => $dataResponse
                ]);
            } else {
                $ResepModel->updateMappingResepBPJS($noresep, $no_out, $tglresep, false, $bpjsJson);
                // Sebagian obat gagal
                return $this->response->setJSON([
                    'status'  => false, 
                    'message' => "Header Resep berhasil, tapi ada kesalahan saat mengirim detail obat:<br>" . implode("<br>", $resultObat['errors']),
                    'data'    => $dataResponse
                ]);
            }

        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    private function _sendDetailObat_BERHASIL(array $detailObat, string $noApotik, string $noResepBPJS, string $kdjnsobat, string $userID, string $noresep, string $no_out)
    {
        $client = Services::curlrequest(['timeout' => 60]);
        $ResepModel = new \App\Models\ResepModel();
        $successCount = 0;
        $errorsObat = [];

        // TAMBAHKAN INI: Double protection
        if (is_string($detailObat)) {
            $detailObat = json_decode($detailObat, true) ?? [];
        }

        foreach ($detailObat as $index => $obat) {
            // TAMBAHKAN INI: Jika tiap item juga masih string
            if (is_string($obat)) {
                $obat = json_decode($obat, true);
            }

            // Skip jika bukan array setelah decode
            if (!is_array($obat)) {
                $errorsObat[] = "Baris " . ($index + 1) . ": Format data obat tidak valid";
                continue;
            }

            $kdObatSimrs = $obat['kd_obat'] ?? null;
            $nmobat      = $obat['nmobat'] ?? '';
            if (empty($kdObatSimrs)) {
                $errorsObat[] = "Baris " . ($index + 1) . ": Kode obat kosong";
                continue;
            }

            // ============================================
            // MAPPING OBAT SIMRS -> BPJS
            // ============================================
            $mappingObat = $ResepModel->getMappingObatBPJS($kdObatSimrs);

            if (!$mappingObat) {
                $errorsObat[] = "Baris " . ($index + 1) . ": Obat {$nmobat} tidak memiliki mapping BPJS";
                continue;
            }

            // Siapkan payload sesuai struktur BPJS (DINAMIS dari data loop)
            $payloadItem = [
                'NOSJP'         => $noApotik,
                'NORESEP'       => $noResepBPJS,
                'KDOBT'         => $mappingObat['kd_obat_bpjs'],  // ← Kode BPJS
                'NMOBAT'        => $mappingObat['nama_obat'],     // ← Nama dari DB
                // 'SIGNA1OBT'     => $obat['signa1'] ?? '1',
                // 'SIGNA2OBT'     => $obat['signa2'] ?? '1',
                // 'JMLOBT'        => (int)($obat['qty'] ?? 0),
                // 'JHO'           => (string)($obat['jho'] ?? 0),
                'SIGNA1OBT'     => '2',
                'SIGNA2OBT'     => '1',
                'JMLOBT'        => 10,
                'JHO'           => '5',
                'CatKhsObt'     => $obat['catkhusus'] ?? 'Single'
            ];

            /*$payloadItem = [
                'NOSJP'         => $noApotik,
                'NORESEP'       => $noResepBPJS,
                'KDOBT'         => '14250805021',
                'NMOBAT'        => 'Hidroksi Urea 500 SK tab 500 mg',
                'SIGNA1OBT'     => '2',
                'SIGNA2OBT'     => '1',
                'JMLOBT'        => 10,
                'JHO'           => '5',
                'CatKhsObt'     => 'Single'
            ];*/

            // Validasi data obat wajib
            // if (empty($payloadItem['KDOBT'])) {
            //     $errorsObat[] = "Baris " . ($index + 1) . ": Kode obat kosong";
            //     continue;
            // }

            // ============================================
            // LOG 1: Insert log AWAL (status = false)
            // ============================================
            $logId = $ResepModel->insertLogDetailResepBPJS(
                $noresep,
                $no_out,
                $noResepBPJS,
                $noApotik,
                $kdObatSimrs,                        // kd_obat_simrs
                $payloadItem['KDOBT'],               // kd_obat_bpjs
                $payloadItem['NMOBAT'],
                $payloadItem['SIGNA1OBT'],
                $payloadItem['SIGNA2OBT'],
                $payloadItem['JMLOBT'],
                $payloadItem['JHO'],
                $payloadItem['CatKhsObt'],
                false,  // status kirim = gagal dulu
                [
                    'kd_obat_simrs' => $kdObatSimrs,
                    'kd_obat_bpjs'  => $payloadItem['KDOBT'],  // Log juga kode BPJS-nya
                    'payload'       => $payloadItem, 
                    'status'        => 'PENDING'
                ]
            );

            $targetUrlDetail = site_url("bpjs/insert/obatnonracikan");
            
            try {
                $resDetail = $client->post($targetUrlDetail, [
                    'headers' => [
                        'X-Internal-Request' => 'TRUE',
                        'Content-Type'       => 'application/json'
                    ],
                    // Encode sekali saja di sini
                    'body' => json_encode($payloadItem)
                ]);

                $resBodyDetail = json_decode($resDetail->getBody(), true);
                
                // Handle response wrapper dari BPJSController
                $detailWrapper = $resBodyDetail['body'] ?? $resBodyDetail;
                
                // Cek status code (bisa string '200' atau int 200)
                $detailCode = $detailWrapper['status_code'] 
                              ?? $detailWrapper['metaData']['code'] 
                              ?? $detailWrapper['code'] 
                              ?? '500';

                if ((string)$detailCode === '200') {
                    $successCount++;
                    // ============================================
                    // LOG 2: Update log SUKSES
                    // ============================================
                    $ResepModel->updateLogDetailResepBPJS($logId, true, $detailWrapper);
                } else {
                    $errMsg = $detailWrapper['message'] 
                             ?? $detailWrapper['metaData']['message'] 
                             ?? 'Error tidak diketahui';
                    $errorsObat[] = "Obat {$payloadItem['NMOBAT']} ({$payloadItem['KDOBT']}): {$errMsg}";

                    // ============================================
                    // LOG 3: Update log GAGAL
                    // ============================================
                    $ResepModel->updateLogDetailResepBPJS($logId, false, $detailWrapper);
                }

            } catch (\Exception $e) {
                $errorsObat[] = "Koneksi error Obat {$payloadItem['NMOBAT']}: " . $e->getMessage();

                // ============================================
                // LOG 4: Update log ERROR KONEKSI
                // ============================================
                $ResepModel->updateLogDetailResepBPJS($logId, false, [
                    'status_code' => '500',
                    'message'     => $e->getMessage()
                ]);
            }
        }

        return [
            'success' => $successCount,
            'errors'  => $errorsObat
        ];
    }
    
    public function kirimresep()
    {
        $request = $this->request->getJSON(true);
        if (!$request) {
            return $this->response->setJSON(['status' => false, 'message' => 'Payload kosong']);
        }

        $no_out     = $request['no_out'] ?? null;
        $tgl_out    = $request['tgl_out'] ?? null;
        $kdpasien   = $request['kdpasien'] ?? null;
        $noresep    = $request['noresep'] ?? null;
        $refasalsjp = $request['sep'] ?? null;
        $kd_unit    = $request['kd_unit'] ?? null;
        $iterasi    = $request['iterasi'] ?? null;
        $kd_dokter  = $request['kd_dokter'] ?? null;
        $kdjnsobat  = $request['kdjnsobat'] ?? null;
        $detailObat = $request['detailobat'] ?? [];
        $kdmodulresep  = $request['kdmodulresep'] ?? null;

        if (!$no_out || !$tgl_out || !$kdpasien || !$noresep || !$refasalsjp || !$kd_dokter || !$kd_unit || empty($detailObat)) {
            return $this->response->setJSON(['status'  => false, 'message' => 'Parameter masih ada yang kurang!']);
        }

        if (is_string($detailObat)) {
            $detailObat = json_decode($detailObat, true) ?? [];
        }

        try {
            $ResepModel = new \App\Models\ResepModel();
            $tglresep       = $tgl_out;
            $tglpelayanan   = $tgl_out;
            $userID         = session()->get('id');

            if ($kdmodulresep !== '1'){
                $poliData = $ResepModel->getMappingUnitBPJS($kd_unit);
                if (!$poliData) return $this->response->setJSON(['status' => false, 'message' => 'Mapping Unit BPJS tidak ditemukan!']);
                $poli = $poliData[0]['unit_bpjs'];

                $dokterBPJS = $ResepModel->getMappingDokterBPJS($kd_dokter);
                if (!$dokterBPJS) return $this->response->setJSON(['status' => false, 'message' => 'Mapping Dokter BPJS tidak ditemukan!']);
                $kd_dokterbpjs = $dokterBPJS[0]['kd_dokter_bpjs'];
            } else {
                $poli = 'BED';
                $kd_dokterbpjs = '30882';
            }

            // ==========================================
            // CEK LOG HEADER SEBELUM KIRIM KE BPJS
            // ==========================================
            $mappingResep = $ResepModel->getMappingResepBPJS($noresep, $tglresep);
            $skipHeaderApi = false;
            $noApotik = null;
            $noResepBPJS = null;

            if ($mappingResep) {
                $noresep_bpjs = $mappingResep['noresep_bpjs'];
                $noResepBPJS = $noresep_bpjs;
                
                // Coba ambil noApotik dari response_bpjs yang tersimpan di log
                // $prevResponse = json_decode($mappingResep['response_bpjs'], true);
                // $bodyPrev = $prevResponse['body'] ?? $prevResponse;
                // $noApotik = $bodyPrev['data']['noApotik'] ?? null;
                // $noResepBPJS = $bodyPrev['data']['noResep'] ?? $noResepBPJS;
                $noApotik = $mappingResep['noApotik'];
                // Jika noApotik ada di log, berarti header sudah SUKSES di BPJS, skip HTTP request header
                if ($noApotik) {
                    $skipHeaderApi = true;
                }
            } else {
                // Jika belum ada log sama sekali, buat nomor resep BPJS baru
                $noresep_bpjs = $ResepModel->generateNoResepBpjs($tglresep);
                $noResepBPJS = $noresep_bpjs;
                $ResepModel->insertMappingResepBPJS($noresep, $noresep_bpjs, $no_out, $tglresep, false);
            }

            // ==========================================
            // STEP 1: KIRIM HEADER RESEP (JIKA BELUM PERNAH)
            // ==========================================
            if (!$skipHeaderApi) {
                $client = Services::curlrequest(['timeout' => 60]);
                $targetUrl = site_url("bpjs/insert/getkirimresep/{$refasalsjp}/{$poli}/{$noResepBPJS}/{$tglresep}/{$tglpelayanan}/{$kd_dokterbpjs}/{$iterasi}/{$kdjnsobat}/{$userID}");

                $responseHeader = $client->get($targetUrl, ['headers' => ['X-Internal-Request' => 'TRUE']]);
                
                $bodyHeader = $responseHeader->getBody();
                $wrapperHeader = json_decode($bodyHeader, true);
                $bpjsJson = $wrapperHeader['body'] ?? $wrapperHeader;

                $code = $bpjsJson['status_code'] ?? $bpjsJson['metaData']['code'] ?? '500';
                
                if ($code != '200') {
                    $msg = $bpjsJson['message'] ?? $bpjsJson['metaData']['message'] ?? 'Gagal membuat header resep di BPJS';
                    // Simpan response gagal ke log
                    $ResepModel->updateMappingResepBPJS($noresep, $noresep_bpjs, $no_out, $tglresep, false, $bpjsJson);
                    return $this->response->setJSON(['status' => false, 'message' => $msg]);
                }

                $dataResponse = $bpjsJson['data'] ?? null;
                $noApotik    = $dataResponse['noApotik'] ?? null;
                $noResepBPJS = $dataResponse['noResep'] ?? $noResepBPJS;

                if (!$noApotik) {
                     return $this->response->setJSON(['status' => false, 'message' => 'Header Resep berhasil, tapi No Apotik tidak ditemukan.']);
                }

                // Simpan response SUKSES ke log header
                $ResepModel->updateMappingResepBPJS($noresep, $noresep_bpjs, $no_out, $tglresep, true, $bpjsJson);
            }

            // ==========================================
            // STEP 2: AMBIL DAFTAR OBAT YANG SUDAH SUKSES
            // ==========================================
            $alreadySentObats = $ResepModel->getDetailObatSukses($noresep, $no_out);

            // ==========================================
            // STEP 3: KIRIM DETAIL OBAT (HANYA YANG BELUM SUKSES)
            // ==========================================
            $resultObat = $this->_sendDetailObat(
                $detailObat, 
                $noApotik, 
                $noResepBPJS, 
                $kdjnsobat, 
                $userID, 
                $noresep, 
                $no_out, 
                $alreadySentObats // ← TAMBAHKAN INI
            );

            // ==========================================
            // FINALISASI
            // ==========================================
            if (empty($resultObat['errors'])) {
                // Update status header menjadi TRUE (jika sebelumnya false karena ada error di detail)
                $ResepModel->updateMappingResepBPJS($noresep, $noresep_bpjs, $no_out, $tglresep, true, ['message' => 'Resep dan semua detail obat sukses']);
                
                return $this->response->setJSON([
                    'status'  => true,
                    'message' => "Resep & Detail Obat berhasil dikirim. No Apotik: {$noApotik}",
                    'data'    => ['noApotik' => $noApotik, 'noResep' => $noResepBPJS]
                ]);
            } else {
                // Ada obat yang gagal, rollback status header menjadi FALSE
                $ResepModel->updateMappingResepBPJS($noresep, $noresep_bpjs, $no_out, $tglresep, false, ['message' => 'Sebagian detail obat gagal', 'errors' => $resultObat['errors']]);
                
                return $this->response->setJSON([
                    'status'  => false, 
                    'message' => "Header Resep aman, tapi ada kesalahan saat mengirim detail obat:<br>" . implode("<br>", $resultObat['errors']),
                    'data'    => ['noApotik' => $noApotik, 'noResep' => $noResepBPJS]
                ]);
            }

        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    private function _sendDetailObat(
        array $detailObat, 
        string $noApotik, 
        string $noResepBPJS, 
        string $kdjnsobat, 
        string $userID,
        string $noresep,
        string $no_out,
        array $alreadySentObats = [] // ← TAMBAHKAN PARAMETER INI
    ) {
        $client = Services::curlrequest(['timeout' => 60]);
        $ResepModel = new \App\Models\ResepModel();
        $successCount = 0;
        $errorsObat = [];

        if (is_string($detailObat)) {
            $detailObat = json_decode($detailObat, true) ?? [];
        }

        // Flatten array hasil database menjadi 1 dimensi: ['00002178', '00002179']
        $flatSentObats = array_column($alreadySentObats, 'kd_obat_simrs');

        foreach ($detailObat as $index => $obat) {
            if (is_string($obat)) {
                $obat = json_decode($obat, true);
            }

            if (!is_array($obat)) {
                $errorsObat[] = "Baris " . ($index + 1) . ": Format data obat tidak valid";
                continue;
            }

            $kdObatSimrs = $obat['kd_obat'] ?? null;

            if (empty($kdObatSimrs)) {
                $errorsObat[] = "Baris " . ($index + 1) . ": Kode obat kosong";
                continue;
            }

            // ============================================
            // SKIP JIKA OBAT INI SUDAH SUKSES DIKIRIM SEBELUMNYA
            // ============================================
            if (in_array($kdObatSimrs, $flatSentObats)) {
                $successCount++; // Dianggap sukses agar tidak mengganggu hitungan final
                continue;
            }

            // MAPPING OBAT SIMRS -> BPJS
            $mappingObat = $ResepModel->getMappingObatBPJS($kdObatSimrs);

            if (!$mappingObat) {
                $errorsObat[] = "Baris " . ($index + 1) . ": Obat {$kdObatSimrs} tidak memiliki mapping BPJS";
                continue;
            }

            $payloadItem = [
                'NOSJP'         => $noApotik,
                'NORESEP'       => $noResepBPJS,
                'KDOBT'         => $mappingObat['kd_obat_bpjs'],
                'NMOBAT'        => $mappingObat['nama_obat'],
                'SIGNA1OBT'     => $obat['signa1'] ?? '1',
                'SIGNA2OBT'     => $obat['signa2'] ?? '1',
                'JMLOBT'        => (int)($obat['qty'] ?? 0),
                'JHO'           => (string)($obat['jho'] ?? 1),
                'CatKhsObt'     => $obat['catkhusus'] ?? 'Single'
            ];

            // LOG DATABASE AWAL
            $logId = $ResepModel->insertLogDetailResepBPJS(
                $noresep, $no_out, $noResepBPJS, $noApotik,
                $kdObatSimrs, $payloadItem['KDOBT'], $payloadItem['NMOBAT'],
                $payloadItem['SIGNA1OBT'], $payloadItem['SIGNA2OBT'],
                $payloadItem['JMLOBT'], $payloadItem['JHO'], $payloadItem['CatKhsObt'],
                false, ['payload' => $payloadItem, 'status' => 'PENDING']
            );

            $targetUrlDetail = site_url("bpjs/insert/obatnonracikan");
            
            try {
                $resDetail = $client->post($targetUrlDetail, [
                    'headers' => [
                        'X-Internal-Request' => 'TRUE',
                        'Content-Type'       => 'application/json'
                    ],
                    'body' => json_encode($payloadItem)
                ]);

                $resBodyDetail = json_decode($resDetail->getBody(), true);
                $detailWrapper = $resBodyDetail['body'] ?? $resBodyDetail;
                $detailCode = $detailWrapper['status_code'] ?? $detailWrapper['metaData']['code'] ?? $detailWrapper['code'] ?? '500';

                if ((string)$detailCode === '200') {
                    $successCount++;
                    $ResepModel->updateLogDetailResepBPJS($logId, true, $detailWrapper);
                } else {
                    $errMsg = $detailWrapper['message'] ?? $detailWrapper['metaData']['message'] ?? 'Error tidak diketahui';
                    $errorsObat[] = "Obat {$payloadItem['NMOBAT']} ({$kdObatSimrs}): {$errMsg}";
                    $ResepModel->updateLogDetailResepBPJS($logId, false, $detailWrapper);
                }

            } catch (\Exception $e) {
                $errorsObat[] = "Koneksi error Obat {$payloadItem['NMOBAT']}: " . $e->getMessage();
                $ResepModel->updateLogDetailResepBPJS($logId, false, ['status_code' => '500', 'message' => $e->getMessage()]);
            }
        }

        return [
            'success' => $successCount,
            'errors'  => $errorsObat
        ];
    }

    private function _sendDetailObatXX(array $detailObat, string $noApotik, string $noResepBPJS, string $kdjnsobat, string $userID)
    {
        $client = Services::curlrequest(['timeout' => 60]);
        $successCount = 0;
        $errorsObat = [];

        foreach ($detailObat as $obat) {
            // Siapkan payload sesuai struktur BPJS
            // Asumsi: $detailObat dari front-end sudah memiliki key: kd_obat, nmobat, signa1, signa2, qty, jho, catkhusus
            // Jika key berbeda, sesuaikan di sini.
            
            $payloadItemX = [
                'NOSJP'         => $noApotik,          // Dari response header
                'NORESEP'       => $noResepBPJS,       // Dari response header
                'KDOBT'         => $obat['kd_obat'] ?? null,
                'NMOBAT'        => $obat['nmobat'] ?? '', // Pastikan front-end mengirim ini
                'SIGNA1OBT'     => $obat['signa1'] ?? '1', // Default jika kosong
                'SIGNA2OBT'     => $obat['signa2'] ?? '1', // Default jika kosong
                'JMLOBT'        => $obat['qty'] ?? 0,
                'JHO'           => $obat['jho'] ?? 0,      // Jumlah Hari Obat
                'CatKhsObt'     => $obat['catkhusus'] ?? 'single' // Kategori khusus
            ];

            $payloadItem = json_encode([
                'NOSJP'         => $noApotik,
                'NORESEP'       => $noResepBPJS,
                'KDOBT'         => '14250805021',
                'NMOBAT'        => 'Hidroksi Urea 500 SK tab 500 mg',
                'SIGNA1OBT'     => '2',
                'SIGNA2OBT'     => '1',
                'JMLOBT'        => 10,
                'JHO'           => '5',
                'CatKhsObt'     => 'Single'
            ]);

            // URL endpoint internal
            $targetUrlDetail = site_url("bpjs/insert/obatnonracikan");

            try {
                // Kirim via POST
                $resDetail = $client->post($targetUrlDetail, [
                    'headers' => [
                        'X-Internal-Request' => 'TRUE',
                        'Content-Type'       => 'application/json'
                    ],
                    'body' => json_encode($payloadItem)
                ]);

                $resBodyDetail = json_decode($resDetail->getBody(), true);
                $detailWrapper = $resBodyDetail['body'] ?? $resBodyDetail;
                $detailCode = $detailWrapper['status_code'] ?? $detailWrapper['code'] ?? '500';

                if ($detailCode == '200') {
                    $successCount++;
                } else {
                    $errMsg = $detailWrapper['message'] ?? 'Error tidak diketahui';
                    $errorsObat[] = "Obat " . ($obat['nmobat'] ?? $obat['kd_obat']) . ": {$errMsg}";
                }

            } catch (\Exception $e) {
                $errorsObat[] = "Koneksi error Obat " . ($obat['nmobat'] ?? $obat['kd_obat']) . ": " . $e->getMessage();
            }
        }

        return [
            'success' => $successCount,
            'errors'  => $errorsObat
        ];
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

    public function del_hapusresepTERAKHIR()
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

            $ResepModel = new \App\Models\ResepModel();
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

                $ResepModel->deleteMappingResepBPJS(
                    $no_resep,
                    $no_apotik
                );

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

    public function del_hapusresep()
    {
        $no_resep   = $this->request->getPost('no_resep');
        $no_apotik  = $this->request->getPost('no_apotik');
        $refasalsjp = $this->request->getPost('refasalsjp');
        
        // Parameter Tambahan untuk Validasi BYVERRSP
        $tgl_awal   = $this->request->getPost('tgl_awal');
        $tgl_akhr   = $this->request->getPost('tgl_akhr');
        $jns_obat   = $this->request->getPost('jns_obat');

        $alasan_hapus = $this->request->getPost('alasan_hapus');

        if (!$alasan_hapus) {
             return $this->response->setJSON([
                'status'  => false,
                'message' => 'Alasan penghapusan wajib diisi.'
            ]);
        }

        // ================= VALIDASI INPUT =================
        if (!$no_resep || !$no_apotik || !$refasalsjp) {
            return $this->response->setJSON(['status' => false, 'message' => 'Parameter Resep tidak lengkap.']);
        }
        
        if (!$tgl_awal || !$tgl_akhr) {
            return $this->response->setJSON(['status' => false, 'message' => 'Parameter Tanggal wajib diisi untuk validasi.']);
        }

        // ================= 1. VALIDASI STATUS BYVERRSP =================
        // Panggil private helper yang sudah kita buat sebelumnya
        $check = $this->_getResepStatus($tgl_awal, $tgl_akhr, $jns_obat, $no_resep, $no_apotik);

        if (!$check['status']) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => $check['message'] ?? 'Gagal mengecek status resep ke BPJS.'
            ]);
        }

        // Jika BYVERRSP bukan '0', tolak penghapusan
        if ($check['byverrsp'] !== '0') {
            return $this->response->setJSON([
                'status'  => false,
                'message' => "Resep tidak dapat dihapus karena status sudah diverifikasi (Status: {$check['byverrsp']}).",
                'csrfHash' => csrf_hash()
            ]);
        }

        // ================= 2. PROSES HAPUS KE API BPJS =================
        try {
            $ResepModel = new \App\Models\ResepModel();
            $userID     = session()->get('id');
            
            $targetUrl = base_url(
                "bpjs/delete/del_hapusresep/{$no_resep}/{$no_apotik}/{$refasalsjp}/{$userID}"
            );

            $client = \Config\Services::curlrequest([
                'timeout'     => 60,
                'http_errors' => false,
            ]);

            $response = $client->get($targetUrl, [
                'headers' => ['X-Internal-Request' => 'TRUE']
            ]);

            $body = trim($response->getBody());

            // 1. Handle Jika Body Kosong (Sukses tapi tidak ada output)
            if ($body === '' || $body === '""') {
                $ResepModel->deleteMappingResepBPJS($no_resep, $no_apotik, $alasan_hapus);
                return $this->response->setJSON([
                    'status'  => true,
                    'message' => 'Resep ' . $no_resep . ' Berhasil di Hapus (Empty Response)',
                    'csrfHash' => csrf_hash()
                ]);
            }

            $wrapper = json_decode($body, true);

            // 2. Handle Jika JSON tidak valid
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($wrapper)) {
                 return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Response JSON tidak valid.',
                    'raw'     => $body
                ]);
            }

            // 3. Parsing Data (Handle struktur Library vs Raw BPJS)
            $bpjsJson = $wrapper['body'] ?? $wrapper;

            // [PERBAIKAN UTAMA DI SINI]
            // Cek 'status_code' (dari Library) ATAU 'metaData.code' (Raw BPJS) ATAU 'code' (Error Library)
            $code = $bpjsJson['status_code'] 
                 ?? $bpjsJson['metaData']['code'] 
                 ?? $bpjsJson['code'] 
                 ?? null;

            $message = $bpjsJson['message'] 
                    ?? $bpjsJson['metaData']['message'] 
                    ?? 'Respon server tidak dikenali';

            // 4. Validasi Kode
            if ($code !== '200') {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => $message . " (Code: $code)"
                ]);
            }

            // 5. Sukses
            // Panggil mapping delete jika ada
            $ResepModel->deleteMappingResepBPJS($no_resep, $no_apotik, $alasan_hapus);

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Resep ' . $no_resep . ' Berhasil di Hapus',
                'csrfHash' => csrf_hash()
            ]);

        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    public function del_itemobat()
    {
        $no_resep   = $this->request->getPost('no_resep');
        $no_apotik  = $this->request->getPost('no_apotik');
        $kd_obat    = $this->request->getPost('kodeobat');
        $nmobat     = $this->request->getPost('nmobat');
        
        $tgl_awal   = $this->request->getPost('tgl_awal');
        $tgl_akhr   = $this->request->getPost('tgl_akhr');
        $jns_obat   = $this->request->getPost('jns_obat');

        // ================= VALIDASI INPUT =================
        if (!$no_resep || !$no_apotik || !$kd_obat) {
            return $this->response->setJSON(['status' => false, 'message' => 'Parameter Resep tidak lengkap.']);
        }
        
        if (!$tgl_awal || !$tgl_akhr) {
            return $this->response->setJSON(['status' => false, 'message' => 'Parameter Tgl Resep tidak lengkap.']);
        }

        
        $check = $this->_getResepStatus($tgl_awal, $tgl_akhr, $jns_obat, $no_resep, $no_apotik);

        if (!$check['status']) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => $check['message'] ?? 'Gagal mengecek status resep ke BPJS.'
            ]);
        }
        
        if ($check['byverrsp'] !== '0') {
            return $this->response->setJSON([
                'status'  => false,
                'message' => "Obat tidak dapat dihapus karena status resep sudah diverifikasi (Status: {$check['byverrsp']}).",
                'csrfHash' => csrf_hash()
            ]);
        }
        
        try {
            $userID     = session()->get('id');
            $tipeobat   = 'N';
            
            // Gunakan site_url agar path ikut konfigurasi base_url yang benar
            $targetUrl = site_url("bpjs/delete/hapusobat/{$no_resep}/{$no_apotik}/{$kd_obat}/{$userID}/{$tipeobat}");

            $client = \Config\Services::curlrequest([
                'timeout'     => 60,
                'http_errors' => false,
            ]);

            $response = $client->get($targetUrl, [
                'headers' => ['X-Internal-Request' => 'TRUE']
            ]);

            $body = trim($response->getBody());
            
            // 1. Handle Kosong
            if ($body === '' || $body === '""') {
                return $this->response->setJSON([
                    'status'  => true,
                    'message' => 'Obat ' . $nmobat . ' Berhasil di Hapus!',
                    'csrfHash' => csrf_hash()
                ]);
            }

            $wrapper = json_decode($body, true);

            // 2. Handle JSON Error
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($wrapper)) {
                 return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Response JSON tidak valid.',
                    'raw'     => $body
                ]);
            }

            // 3. Parsing Data Aman
            // Prioritas ambil data dari 'data' (Library) atau 'response' (Raw BPJS)
            $responseData = $wrapper['data'] ?? $wrapper['response'] ?? null;
            
            $code = $wrapper['status_code'] 
                 ?? $wrapper['metaData']['code'] 
                 ?? $wrapper['code'] 
                 ?? '500';

            $message = $wrapper['message'] 
                    ?? $wrapper['metaData']['message'] 
                    ?? ($responseData['message'] ?? 'Respon server tidak dikenali');

            // 4. Validasi Kode
            if ($code !== '200') {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => $message . " (Code: $code)"
                ]);
            }

            // 5. Sukses
            return $this->response->setJSON([
                'status'  => true,
                'message' => $nmobat . ' berhasil dihapus. ' . ($responseData['message'] ?? ''),
                'csrfHash' => csrf_hash()
            ]);

        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Kesalahan sistem: ' . $e->getMessage()
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
            // $targetUrl  = $baseUrl . 'bpjs/listpelayananobat_perSEP/' . $sep.'/'.$userID;
            $targetUrl  = site_url("bpjs/listpelayananobat_perSEP/{$sep}/{$userID}");
            $client     = Services::curlrequest();
            $response   = $client->get($targetUrl, [
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

    private function _getResepStatus($tgl_awal, $tgl_akhr, $jns_obat, $search_noresep, $search_noapotik)
    {
        try {
            $userID    = session()->get('id');
            $targetUrl = base_url("bpjs/insert/daftarresep/{$tgl_awal}/{$tgl_akhr}/{$jns_obat}/{$userID}");

            $client = Services::curlrequest(['timeout' => 60]);

            $response = $client->get($targetUrl, [
                'headers' => ['X-Internal-Request' => 'TRUE']
            ]);

            $wrapper = json_decode($response->getBody(), true);
            $bpjsJson = $wrapper['body'] ?? $wrapper;
            // var_dump($bpjsJson);
            // exit();
            $resepList = [];

            // Parsing response (Handle 2 format: metaData / status_code)
            if (isset($bpjsJson['metaData']['code']) && $bpjsJson['metaData']['code'] == "200") {
                if (!is_null($bpjsJson['data']) && !empty($bpjsJson['response']['list'])) {
                    $resepList = $bpjsJson['response']['list'];
                }
            } elseif (isset($bpjsJson['status_code']) && $bpjsJson['status_code'] == "200") {
                if (!empty($bpjsJson['data'])) {
                    $resepList = $bpjsJson['data'];
                }
            }

            // Cari item spesifik
            if (!empty($resepList)) {
                foreach ($resepList as $item) {
                    if ($item['NORESEP'] == $search_noresep && $item['NOAPOTIK'] == $search_noapotik) {
                        return [
                            'status'   => true,
                            'byverrsp' => $item['BYVERRSP'] ?? null,
                            'data'     => $item
                        ];
                    }
                }
            }

            // return ['status' => false, 'message' => 'Data resep tidak ditemukan di server BPJS'];
            return ['status' => false, 'message' => $bpjsJson['message'] ?? 'Data resep tidak ditemukan di server BPJS'];

        } catch (\Throwable $e) {
            return ['status' => false, 'message' => 'Error koneksi: ' . $e->getMessage()];
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

    public function getDetailObatSIMRS()
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