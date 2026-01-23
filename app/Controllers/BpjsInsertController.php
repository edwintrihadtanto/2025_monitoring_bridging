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

    public function getResepSIMRS()
    {
        // Inisialisasi model untuk mengambil data resep
        $ResepModel = new ResepModel();

        // Ambil data resep dari model
        $rekap = $ResepModel->getResepDetails();
        echo 'a';
        // Hitung jumlah status berdasarkan response code (200, 201, 404, 403)
        $counts = [
            200 => 0,
            201 => 0,
            404 => 0,
            403 => 0
        ];

        foreach ($rekap as $row) {
            $counts[200] += 1;  // Semua data yang berhasil dihitung di response code 200
        }

        // Tentukan jumlah per halaman untuk pagination
        $perPage = $this->request->getGet('perPage') ?? 10;

        // Siapkan data untuk dikirim ke view
        $data = [
            'logs' => $rekap,
            'pagination' => $rekap, // Jika perlu menerapkan pagination manual
            'perPage' => $perPage,
            'rekap' => [
                'code200' => $counts[200],
                'code201' => $counts[201],
                'code404' => $counts[404],
                'code403' => $counts[403],
            ]
        ];

        var_dump($data);
        // Tampilkan hasil di view
        return $this->renderView('resep/list_resep', $data);
    }
}