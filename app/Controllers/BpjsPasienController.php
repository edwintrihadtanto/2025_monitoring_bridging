<?php

namespace App\Controllers;

use App\Controllers\BpjsController;
use Config\Services; // Import Services

class BpjsPasienController extends BaseController
{
    public function index()
    {
        // $data = [
        //     'title' => 'Pencarian Data Pasien',
        //     'validation' => \Config\Services::validation()
        // ];
        // return $this->renderView('pasien/pasien', $data);
        return $this->renderView('pasien/pasien', []);
    }

    public function searchCARA_AWAL()
    {
        // 1. Ambil Input Form
        $searchType = $this->request->getPost('search_type'); // nik atau kartu
        $searchVal  = $this->request->getPost('search_value');

        // 2. Validasi Sederhana
        if (empty($searchVal)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Nomor pencarian tidak boleh kosong.'
            ]);
        }

        // 3. Panggil Controller BPJS (Logic Existing)
        // Kita buat instance BpjsController dan panggil methodnya
        // Method tersebut biasanya mengembalikan JSON response atau Array
        $bpjsController = new BpjsController();

        try {
            // Cek tipe pencarian
            if ($searchType === 'nik') {
                // Panggil method getByNIK
                // Catatan: Pastikan method di BpjsController tidak meng-echo langsung,
                // tapi return data atau mengembalikan response->setJSON()
                $result = $bpjsController->getPesertaByNik($searchVal);
            } else {
                // Panggil method getByNoKartu
                $result = $bpjsController->getPesertaByNoKartu($searchVal);
            }

            // Jika result adalah object Response (JSON), kita parsing dulu
            // Atau jika BpjsController hanya return array:
            $dataPasien = json_decode($result->getBody(), true);

            // 4. Load View Hasil Pencarian (Partial)
            // Kita render hanya tampilan kartu pasien, bukan full layout
            $html = view('pasien/partial_pasien_result', ['pasien' => $dataPasien['response'] ?? []]);

            return $this->response->setJSON([
                'status' => true,
                'html' => $html
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Gagal menghubungi server BPJS: ' . $e->getMessage()
            ]);
        }
    }

    public function search()
    {
        $searchType = $this->request->getPost('search_type'); 
        $searchVal  = $this->request->getPost('search_value');

        if (empty($searchVal)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Nomor pencarian tidak boleh kosong.'
            ]);
        }

        try {
            $baseUrl = base_url();
            $targetUrl = "";
            
            if ($searchType === 'nik') {
                $targetUrl = $baseUrl . 'bpjs/peserta/nik/' . $searchVal;
            } else {
                $targetUrl = $baseUrl . 'bpjs/peserta/nokartu/' . $searchVal;
            }

            $client = Services::curlrequest();
            $response = $client->get($targetUrl);
            
            // Decode
            $wrapper = json_decode($response->getBody(), true);
            
            // Ambil data asli dari key 'body'
            $bpjsJson = $wrapper['body'] ?? $wrapper;

            $statusResult = false;
            $message = '';
            $htmlResult = '';

            // 1. Cek Error HTTP 404 di Wrapper
            if (isset($wrapper['status_code']) && $wrapper['status_code'] == 404) {
                $message = 'Data Peserta tidak ditemukan (Status 404).';
            }
            // 2. Cek SUKSES (Code 200)
            elseif (isset($bpjsJson['metaData']['code']) && $bpjsJson['metaData']['code'] == "200") {
                
                if (!empty($bpjsJson['response']['peserta'])) {
                    $statusResult = true;
                    $pasienData = $bpjsJson['response']['peserta'];
                    $htmlResult = view('pasien/partial_pasien_result', ['pasien' => $pasienData]);
                } else {
                    $message = 'Data Peserta kosong.';
                }
            }
            // --- 3. TAMBAHAN: Cek ERROR (Code Selain 200, misal 201) ---
            elseif (isset($bpjsJson['metaData']['code']) && $bpjsJson['metaData']['code'] != "200") {
                // Ambil pesan error dari BPJS
                // Contoh: "No.Kartu Tidak Sesuai"
                $message = $bpjsJson['metaData']['message'];
            }
            else {
                $message = 'Respon server BPJS format tidak dikenali.';
            }

            return $this->response->setJSON([
                'status' => $statusResult,
                'message' => $message,
                'html' => $htmlResult
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Gagal terhubung ke API BPJS: ' . $e->getMessage()
            ]);
        }
    }
}