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
            return $this->response->setJSON(['status' => false, 'message' => 'Payload kosong']);
        }

        $no_out     = $request['no_out'] ?? null;
        $tgl_out    = $request['tgl_out'] ?? null;
        $tgl_pelayanan = $request['tgl_pelayanan'] ?? null;
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

        // CEK FLAG PRB
        // ==========================================
        $prbCheck = $this->_cekSepPasien($refasalsjp);
        if (!$prbCheck['status']) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => $prbCheck['message'] ?? 'Gagal mengecek data SEP pasien.',
                'data'    => $prbCheck
            ]);
        }

        if ($prbCheck['nokartu'] !== '0') {

            $noka = $prbCheck['nokartu'] ?? 0;
            $statusCheck = $this->_cekNokaPasien($noka);

            // 🔥 WAJIB: cek dulu status BPJS
            if (!$statusCheck['status']) {

                log_message('warning', 'Cek NOKA gagal, lanjut proses: ' . ($statusCheck['message'] ?? ''));

                // 👉 pilih salah satu:
                // return error (strict)
                
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Gagal validasi peserta BPJS, coba lagi'
                ]);
                
            } else {

                $kode = $statusCheck['kode'] ?? null;
                $ket  = $statusCheck['ket'] ?? '-';

                if ($kode !== '0') {
                    return $this->response->setJSON([
                        'status'  => false,
                        'message' => "Status Peserta ({$ket})"
                    ]);
                }
            }
        }
        // if ($prbCheck['nokartu'] !== '0' ) {
        //     $noka = $prbCheck['nokartu'] ?? 0;
        //     $statusCheck = $this->_cekNokaPasien($noka);
        //     if ($statusCheck['kode'] !== '0' ) {
        //         return $this->response->setJSON([
        //             'status'  => false,
        //             'message' => "Status Peserta (".($statusCheck['ket'].")" ?: "tidak diketahui!")
        //         ]);
        //     }
        // }

        if ($prbCheck['flagprb'] !== '0' ) {
            // return $this->response->setJSON([
            //     'status'  => false,
            //     'message' => "Resep tidak dapat diproses. Status PRB Aktif (".($prbCheck['namaprb'].")" ?: "-")                
            // ]);
            $infoPrb = [
                'statusprb' => true,
                'message'   => "Status PRB Aktif (" . ($prbCheck['namaprb'] ?: '-') . ")"
            ];
        }

        try {
            $ResepModel = new \App\Models\ResepModel();
            $tglresep       = $tgl_out;
            $tglpelayanan   = $tgl_pelayanan ?: $tgl_out;
            $userID         = session()->get('id');

            if ($kdmodulresep !== '1'){
                $poliData = $ResepModel->getMappingUnitBPJS($kd_unit);
                if (!$poliData) return $this->response->setJSON(['status' => false, 'message' => 'Mapping Unit BPJS tidak ditemukan!']);
                $poli = $poliData[0]['unit_bpjs'];

                $dokterBPJS = $ResepModel->getMappingDokterBPJS($kd_dokter);
                if (!$dokterBPJS) return $this->response->setJSON(['status' => false, 'message' => 'Mapping Dokter BPJS tidak ditemukan!']);
                $kd_dokterbpjs = $dokterBPJS[0]['kd_dokter_bpjs'];
            } else {
                $poli = 'INT';
                // $kd_dokterbpjs = '299693';
                $dokterBPJS = $ResepModel->getMappingDokterBPJS($kd_dokter);
                if (!$dokterBPJS) return $this->response->setJSON(['status' => false, 'message' => 'Mapping Dokter BPJS tidak ditemukan!']);
                $kd_dokterbpjs = $dokterBPJS[0]['kd_dokter_bpjs'];
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

                $ResepModel->updateIterResepBPJS($noresep, $noresep_bpjs, $no_out, $tglresep, $kdjnsobat, $iterasi);
            } else {
                // Jika belum ada log sama sekali, buat nomor resep BPJS baru
                $noresep_bpjs   = $ResepModel->generateNoResepBpjs($tglresep);
                $noResepBPJS    = $noresep_bpjs;
                $ResepModel->insertMappingResepBPJS($noresep, $noresep_bpjs, $no_out, $tglresep, false, $kdjnsobat, $iterasi);
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

            $tanggal = $tglresep ? date('Y-m-d', strtotime($tglresep)) : null;
            $check = $this->_getResepStatus($tanggal, $tanggal, $kdjnsobat, $noResepBPJS, $noApotik);
            
            if (!$check['status']) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => $check['message'] ?? 'Gagal mengecek status resep ke BPJS.'
                ]);
            }
            
            if ($check['byverrsp'] !== '0') {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => "Obat tidak dapat ditambahkan karena resep sudah diverifikasi.",
                    'csrfHash' => csrf_hash()
                ]);
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
                $tgl_out,
                $alreadySentObats
            );

            // ==========================================
            // FINALISASI
            // ==========================================
            if (empty($resultObat['errors'])) {
                
                // $ResepModel->updateMappingResepBPJS($noresep, $noresep_bpjs, $no_out, $tglresep, true, ['message' => 'Resep dan semua detail obat sukses']);
                
                return $this->response->setJSON([
                    'status'  => true,
                    'message' => "Resep & Detail Obat berhasil dikirim. No Apotik: {$noApotik}",
                    'data'    => ['noApotik' => $noApotik, 'noResep' => $noResepBPJS],
                    'prb'     => $infoPrb
                ]);
            } else {
                // Ada obat yang gagal, rollback status header menjadi FALSE
                // $ResepModel->updateMappingResepBPJS($noresep, $noresep_bpjs, $no_out, $tglresep, false, ['message' => 'Sebagian detail obat gagal', 'errors' => $resultObat['errors']]);
                
                /*return $this->response->setJSON([
                    'status'  => false, 
                    // 'message' => "Header Resep aman, tapi ada kesalahan saat mengirim detail obat:<br>" . implode("<br>", $resultObat['errors']."<br>"),                    
                    'message' => "Header Resep aman, tapi ada kesalahan saat mengirim detail obat:<br>" . implode("<br>", $resultObat['errors']),
                    'detailobat' => $resultObat['errors'],
                    'data'    => ['noApotik' => $noApotik, 'noResep' => $noResepBPJS],
                    'pesan2'  => $resultObat
                ]);*/
                 return $this->response->setJSON([
                    'status'  => false, 
                    'message' => "Header Resep tersimpan (No Apotik: {$noApotik}), namun ada obat yang gagal dikirim ke BPJS.",
                    'data'    => ['noApotik' => $noApotik, 'noResep' => $noResepBPJS],
                    'errors'  => $resultObat['errors'], // ✅ Langsung kirim array error terstruktur
                    'prb'     => $infoPrb
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
        string $tgl_out,
        array $alreadySentObats = []
    ) {
        $client = Services::curlrequest(['timeout' => 60]);
        $ResepModel = new \App\Models\ResepModel();
        $successCount = 0;
        $errorsObat = [];

        if (is_string($detailObat)) {
            $detailObat = json_decode($detailObat, true) ?? [];
        }

        $flatSentObats = array_column($alreadySentObats, 'kd_obat_simrs');

        // ==========================================
        // KELOMPOKKAN OBAT UNTUK MENCARI NMRACIK
        // ==========================================
        $racikanGroups = [];
        $nonRacikan = [];

        foreach ($detailObat as $obat) {
            if (is_string($obat)) $obat = json_decode($obat, true);
            if (!is_array($obat)) continue;

            $kdObatSimrs = $obat['kd_obat'] ?? null;
            if (empty($kdObatSimrs)) continue;

            if (in_array($kdObatSimrs, $flatSentObats)) {
                $successCount++;
                continue;
            }

            $jenisRacikan = $obat['racikan'] ?? 'Tidak';

            if ($jenisRacikan === 'Tidak' || strtolower($jenisRacikan) === 'tidak') {
                $nonRacikan[] = $obat;
            } else {
                $racikanGroups[$jenisRacikan][] = $obat;
            }
        }

        // ==========================================
        // PROSES OBAT NON-RACIKAN
        // ==========================================
        foreach ($nonRacikan as $obat) {
            $kdObatSimrs = $obat['kd_obat'];
            $mappingObat = $ResepModel->getMappingObatBPJS($kdObatSimrs);

            if (!$mappingObat) {
                $errorsObat[] = ['kd_obat' => $kdObatSimrs, 'nama_obat' => $kdObatSimrs, 'error' => "Tidak memiliki mapping BPJS"];
                continue;
            }

            $payloadItem = [
                'NOSJP'         => $noApotik,
                'NORESEP'       => $noResepBPJS,
                'KDOBT'         => $mappingObat['kd_obat_bpjs'],
                'NMOBAT'        => $mappingObat['nama_obat'],
                'SIGNA1OBT'     => (string)($obat['signa1'] ?? '1'),
                'SIGNA2OBT'     => (string)($obat['signa2'] ?? '1'),
                'JMLOBT'        => (int)($obat['qty'] ?? 0),
                'JHO'           => (string)($obat['jho'] ?? 1),
                'CatKhsObt'     => ''
            ];
            
            $logId = $ResepModel->insertLogDetailResepBPJS(
                $noresep, $no_out, $tgl_out, $noResepBPJS, $noApotik,
                $kdObatSimrs, $payloadItem['KDOBT'], $payloadItem['NMOBAT'],
                $payloadItem['SIGNA1OBT'], $payloadItem['SIGNA2OBT'],
                $payloadItem['JMLOBT'], $payloadItem['JHO'], $payloadItem['CatKhsObt'],
                false, ['payload' => $payloadItem, 'status' => 'PENDING'], null, 'N'
            );

            $targetUrl = site_url("bpjs/insert/obatnonracikan");

            try {
                $resDetail = $client->post($targetUrl, [
                    'headers' => ['X-Internal-Request' => 'TRUE', 'Content-Type' => 'application/json'],
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
                    $errorsObat[] = ['kd_obat' => $kdObatSimrs, 'nama_obat' => $payloadItem['NMOBAT'], 'error' => $errMsg];
                    $ResepModel->updateLogDetailResepBPJS($logId, false, $detailWrapper);
                }
            } catch (\Exception $e) {
                $errorsObat[] = ['kd_obat' => $kdObatSimrs, 'nama_obat' => $payloadItem['NMOBAT'], 'error' => 'Koneksi error: ' . $e->getMessage()];
                $ResepModel->updateLogDetailResepBPJS($logId, false, ['status_code' => '500', 'message' => $e->getMessage()]);
            }
        }

        // ==========================================
        // PROSES OBAT RACIKAN (KIRIM PER ITEM)
        // ==========================================
        $noRacikan = 1; // Mulai dari 1

        foreach ($racikanGroups as $namaRacikan => $items) {
            
            // ✅ Susun JNSROBT berdasarkan urutan racikan (R.01, R.02, R.03, dst)
            $jnsrobt = 'R.' . str_pad($noRacikan, 2, '0', STR_PAD_LEFT);
            // $jnsrobt = 'R.' . sprintf('%02d', $noRacikan);
            foreach ($items as $obat) {
                $kdObatSimrs = $obat['kd_obat'];
                $mappingObat = $ResepModel->getMappingObatBPJS($kdObatSimrs);

                if (!$mappingObat) {
                    $errorsObat[] = ['kd_obat' => $kdObatSimrs, 'nama_obat' => $kdObatSimrs, 'error' => "Tidak memiliki mapping BPJS (Racikan: {$namaRacikan})"];
                    continue;
                }

                // ✅ Payload Sesuai Endpoint Racikan BPJS (Tanpa NMRACIK)
                $payloadItem = [
                    'NOSJP'         => $noApotik,
                    'NORESEP'       => $noResepBPJS,
                    'JNSROBT'       => $jnsrobt,                 // ✅ "R.01", "R.02", dst...
                    'KDOBT'         => $mappingObat['kd_obat_bpjs'],
                    'NMOBAT'        => $mappingObat['nama_obat'],
                    'SIGNA1OBT'     => (string)($obat['signa1'] ?? '1'),
                    'SIGNA2OBT'     => (string)($obat['signa2'] ?? '1'),
                    'PERMINTAAN'    => (string)($obat['permintaan'] ?? '1'),
                    'JMLOBT'        => (int)($obat['qty'] ?? 0),
                    'JHO'           => (string)($obat['jho'] ?? 1),
                    'CatKhsObt'     => $namaRacikan
                ];

                $logId = $ResepModel->insertLogDetailResepBPJS(
                    $noresep, $no_out, $tgl_out, $noResepBPJS, $noApotik,
                    $kdObatSimrs, $payloadItem['KDOBT'], $payloadItem['NMOBAT'],
                    $payloadItem['SIGNA1OBT'], $payloadItem['SIGNA2OBT'],
                    $payloadItem['JMLOBT'], $payloadItem['JHO'], $payloadItem['CatKhsObt'],
                    false, ['payload' => $payloadItem, 'status' => 'PENDING'], $payloadItem['PERMINTAAN'], $payloadItem['JNSROBT']
                );

                $targetUrl = site_url("bpjs/insert/obatracikan");

                try {
                    $resDetail = $client->post($targetUrl, [
                        'headers' => ['X-Internal-Request' => 'TRUE', 'Content-Type' => 'application/json'],
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
                        $errorsObat[] = ['kd_obat' => $kdObatSimrs, 'nama_obat' => $payloadItem['NMOBAT'], 'error' => "[Racikan: {$namaRacikan}] " . $errMsg];
                        $ResepModel->updateLogDetailResepBPJS($logId, false, $detailWrapper);
                    }
                } catch (\Exception $e) {
                    $errorsObat[] = ['kd_obat' => $kdObatSimrs, 'nama_obat' => $payloadItem['NMOBAT'], 'error' => "[Racikan: {$namaRacikan}] Koneksi error: " . $e->getMessage()];
                    $ResepModel->updateLogDetailResepBPJS($logId, false, ['status_code' => '500', 'message' => $e->getMessage()]);
                }
            }

            $noRacikan++; // ✅ Increment untuk mengubah JNSROBT berikutnya (R.02)
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
        // $tipeobat   = 'N';
        
        $ResepModel = new \App\Models\ResepModel();

        $dataTipe = $ResepModel->getTipeObat(
            $no_resep,
            $no_apotik,
            $kd_obat
        );

        $id       = $dataTipe['id'] ?? null;
        $tipeobat = $dataTipe['jenisracikan'] ?? null;

        if (empty($tipeobat)) {
            $tipeobat = 'N';
        } else {
            $tipeobat = (strtoupper($tipeobat) === 'N') ? 'N' : 'R';
        }

        // return $this->response->setJSON(['status' => false, 'message' => $tipeobat."/". $id ]);

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
            
            // Gunakan site_url agar path ikut konfigurasi base_url yang benar
            $targetUrl = site_url("bpjs/delete/del_hapusobat/{$no_resep}/{$no_apotik}/{$kd_obat}/{$userID}/{$tipeobat}");

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

            if (!empty($id)) {
                $ResepModel->updateLogDetailResepBPJS(
                    $id,
                    false,
                    [
                        'status'  => 'DELETED',
                        'message' => 'Obat berhasil dihapus dari BPJS'
                    ]
                );
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

    private function _getSepPasien($refasalsjp)
    {
        try {
            $userID    = session()->get('id');
            $targetUrl = base_url("bpjs/getSEPPasien/{$refasalsjp}");

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

    private function _cekSepPasien(string $refasalsjp): array
    {
        try {
            $client = Services::curlrequest(['timeout' => 15]);
            $targetUrl = base_url('bpjs/getSEPPasien/' . $refasalsjp);
            $response = $client->get($targetUrl, [
                'headers' => ['X-Internal-Request' => 'TRUE']
            ]);

            $wrapper = json_decode($response->getBody(), true);
            $bpjsJson = $wrapper['body'] ?? $wrapper;

            if (isset($bpjsJson['metaData']['code']) && $bpjsJson['metaData']['code'] == "200") {
                $sepData = $bpjsJson['response'] ?? [];

                if (!empty($sepData['noSep'])) {
                    // return [
                    //     'status'  => false,
                    //     'message' => 'Resep tidak dapat diproses. Status PRB Aktif (' . ($sepData['namaprb'] .')' ?: '-')
                    // ];
                    return [
                        'status'    => true,
                        'flagprb'   => $sepData['flagprb'] ?? null,
                        'namaprb'   => $sepData['namaprb'] ?? '--',
                        'nokartu'   => $sepData['nokartu'] ?? '0',
                        'data'      => $sepData
                    ];
                }
            }

            return ['status' => false, 'message' => $bpjsJson['metaData']['message'] ?? 'Gagal Cek SEP!', 'data' => $bpjsJson];

        } catch (\Exception $e) {
            // API error tidak memblokir proses resep
            log_message('error', 'Gagal cek flag PRB SEP ' . $refasalsjp . ': ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Gagal Cek SEP! '. $refasalsjp . '<br>Koneksi BPJS timeout / error',
                'error'   => $e->getMessage()
            ];
        }
    }

    private function _cekNokaPasien(string $noka): array
    {
        try {
            $client = Services::curlrequest(['timeout' => 10]);
            $targetUrl = base_url('bpjs/peserta/nokartu/' . $noka);
            $response = $client->get($targetUrl, [
                'headers' => ['X-Internal-Request' => 'TRUE']
            ]);

            $wrapper = json_decode($response->getBody(), true);
            $bpjsJson = $wrapper['body'] ?? $wrapper;

            if (isset($bpjsJson['metaData']['code']) && $bpjsJson['metaData']['code'] == "200") {
                $data = $bpjsJson['response']['peserta'] ?? [];

                if (!empty($data['noKartu'])) {
                    // return [
                    //     'status'  => false,
                    //     'message' => 'Resep tidak dapat diproses. Status PRB Aktif (' . ($sepData['namaprb'] .')' ?: '-')
                    // ];
                    return [
                        'status'    => true,
                        'kode'      => $data['statusPeserta']['kode'] ?? null,
                        'ket'       => $data['statusPeserta']['keterangan'] ?? '--',
                        'noKartu'   => $data['noKartu'] ?? '0',
                        'data'      => $data
                    ];
                }
            }

            return [
                'status'    => false, 
                'message'   => $bpjsJson['metaData']['message'] ?? 'Gagal Cek Data Pasien!', 
                'data'      => $bpjsJson
            ];

        } catch (\Exception $e) {
            // API error tidak memblokir proses resep
            log_message('error', 'Gagal cek data pasien ' . $noka . ': ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Gagal cek data pasien '. $noka . '<br>Koneksi BPJS timeout / error',
                'error'   => $e->getMessage()
            ];
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

        $page = (int) ($this->request->getPost('page') ?? 1);
        $perPage = (int) ($this->request->getPost('per_page') ?? 50);
        $allowedPerPage = [50, 100];

        if ($page < 1) {
            $page = 1;
        }

        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 50;
        }

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

            $totalRows = $ResepModel->countResepHeader($filter);
            $totalPages = max(1, (int) ceil($totalRows / $perPage));

            if ($page > $totalPages) {
                $page = $totalPages;
            }

            $offset = ($page - 1) * $perPage;
            $rekap = $ResepModel->getResepHeader($filter, $perPage, $offset);

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
                    'groups' => $grouped,
                    'pagination' => [
                        'current_page' => $page,
                        'per_page'     => $perPage,
                        'total_rows'   => $totalRows,
                        'total_pages'  => $totalPages,
                        'has_prev'     => $page > 1,
                        'has_next'     => $page < $totalPages,
                        'from'         => $totalRows > 0 ? $offset + 1 : 0,
                        'to'           => min($offset + count($rekap), $totalRows),
                    ],
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
