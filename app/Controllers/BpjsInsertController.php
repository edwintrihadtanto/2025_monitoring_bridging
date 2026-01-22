<?php

namespace App\Controllers;
use Config\Services;

class BpjsInsertController extends BaseController
{

    public function viewgetdaftar_resep(){
        return $this->renderView('resep/sidebar-listresep');
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
}