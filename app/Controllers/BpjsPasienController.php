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

    public function fomrseppasien()
    {
        return $this->renderView('pasien/sep_pasien', []);
    }

    public function search()
    {
        $searchType = $this->request->getPost('search_type'); 
        $searchVal  = $this->request->getPost('search_value');

        if (empty($searchType)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Jenis pencarian belum di pilih!'
            ]);
        }

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
            // $response = $client->get($targetUrl);
            $response = $client->get($targetUrl, [
                'headers' => ['X-Internal-Request' => 'TRUE']
            ]);
            
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
            // --- 3. Cek ERROR (Code Selain 200, misal 201) ---
            elseif (isset($bpjsJson['metaData']['code']) && $bpjsJson['metaData']['code'] != "200") {
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

    public function searchsep()
    {
        $searchType = $this->request->getPost('searchsep_type'); 
        $searchVal  = $this->request->getPost('searchsep_value');

        if (empty($searchType)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Jenis pencarian belum di pilih!'
            ]);
        }

        if (empty($searchVal)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'SEP tidak boleh kosong.'
            ]);
        }

        try {
            $baseUrl = base_url();
            $targetUrl = "";
            
            if ($searchType === '1') { //rajal
                $targetUrl = $baseUrl . 'bpjs/getSEPPasien/' . $searchVal;
            } else if ($searchType === '2') { //ranap
                $targetUrl = $baseUrl . 'bpjs/getSEPPasien/' . $searchVal;
            }
            // var_dump($targetUrl);
            // exit;
            $client = Services::curlrequest();
            // $response = $client->get($targetUrl);
            $response = $client->get($targetUrl, [
                'headers' => ['X-Internal-Request' => 'TRUE']
            ]);
            
            // Decode
            $wrapper = json_decode($response->getBody(), true);
            
            // Ambil data asli dari key 'body'
            $bpjsJson = $wrapper['body'] ?? $wrapper;

            $statusResult = false;
            $message = '';
            $htmlResult = '';

            // 1. Cek Error HTTP 404 di Wrapper
            if (isset($wrapper['status_code']) && $wrapper['status_code'] == 404) {
                $message = 'Data SEP Peserta tidak ditemukan (Status 404).';
            }
            // 2. Cek SUKSES (Code 200)
            elseif (isset($bpjsJson['metaData']['code']) && $bpjsJson['metaData']['code'] == "200") {
                
                if (!empty($bpjsJson['response']['peserta'])) {
                    $statusResult = true;
                    $pasienData = $bpjsJson['response'];
                    $htmlResult = view('pasien/partial_sep_result', ['pasien' => $pasienData]);
                } else {
                    $message = 'Data SEP kosong.';
                }
            }
            // --- 3. Cek ERROR (Code Selain 200, misal 201) ---
            elseif (isset($bpjsJson['metaData']['code']) && $bpjsJson['metaData']['code'] != "200") {
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