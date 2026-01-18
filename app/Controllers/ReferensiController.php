<?php

namespace App\Controllers;

use App\Controllers\BpjsController;
use Config\Services;

class ReferensiController extends BaseController
{

    public function viewfaskes(){
        return $this->renderView('referensi/sidebar-faskes');
    }

    public function viewapotik(){
        return $this->renderView('referensi/sidebar-apotik');
    }

    public function viewdpho(){
        return $this->renderView('referensi/sidebar-dpho');
    }

    public function viewpoli(){
        return $this->renderView('referensi/sidebar-poli');
    }

    public function viewspesialis(){
        return $this->renderView('referensi/sidebar-faskes');
    }

    public function viewobat(){
        return $this->renderView('referensi/sidebar-faskes');
    }

    public function search_faskes()
    {
        $jns_faskes = $this->request->getPost('jns_faskes'); 
        $nama_faskes  = $this->request->getPost('nama_faskes');

        if (empty($jns_faskes)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Jenis Fakses belum di pilih!']);
        }

        if (empty($nama_faskes)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Nama Faskes tidak boleh kosong.']);
        }

        try {
            $baseUrl = base_url();
            $targetUrl = $baseUrl . 'bpjs/referensi/getppk/' . $jns_faskes . '/' . $nama_faskes;
            
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

            // --- LOGIKA ADAPTIF (BISA DUA STRUKTUR) ---

            // 1. CEK STRUKTUR STANDAR (Yang ada di Log DB)
            // Cek: ada 'metaData' dengan code "200"
            if (isset($bpjsJson['metaData']['code']) && $bpjsJson['metaData']['code'] == "200") {
                
                if (!is_null($bpjsJson['response'])) {
                    if (!empty($bpjsJson['response']['list'])) {
                        $statusResult = true;
                        $faskesList = $bpjsJson['response']['list'];
                        $htmlResult = view('referensi/partial_faskes_result', ['faskesList' => $faskesList]);
                    } else {
                        $message = 'Data Faskes kosong (Structure A).';
                    }
                } else {
                    // Case: Response null tapi code 200 (Sering terjadi)
                    $message = 'Data Faskes tidak ditemukan (Response Null).';
                }
            }
            // 2. CEK STRUKTUR "SUCCES" (Yang ada di Vardump Lama)
            // Cek: ada 'status' dengan isi "sukses"
            elseif (isset($bpjsJson['status']) && $bpjsJson['status'] == "sukses") {
                
                if (!empty($bpjsJson['data']['list'])) {
                    $statusResult = true;
                    $faskesList = $bpjsJson['data']['list'];
                    $htmlResult = view('referensi/partial_faskes_result', ['faskesList' => $faskesList]);
                } else {
                    $message = 'Data Faskes kosong (Structure B).';
                }
            }
            // 3. CEK ERROR (Selain 200 atau sukses)
            else {
                // Ambil pesan error dari struktur mana saja yang tersedia
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
                'status' => false,
                'message' => 'Gagal terhubung ke API BPJS: ' . $e->getMessage()
            ]);
        }
    }

    public function search_apotik()
    {
        $kode_apotik  = $this->request->getPost('kode_apotik');

        if (empty($kode_apotik)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Kode Apotik tidak boleh kosong.']);
        }

        try {
            $baseUrl = base_url();
            $targetUrl = $baseUrl . 'bpjs/referensi/getsettingppk/' . $kode_apotik;
            
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
                    if (!empty($bpjsJson['response'])) {
                        $statusResult = true;
                        $apotikList = $bpjsJson['response'];
                        $htmlResult = view('referensi/partial_apotik_result', ['apotikList' => $apotikList]);
                    } else {
                        $message = 'Data Apotik kosong.';
                    }
                } else {
                    $message = 'Data Apotik tidak ditemukan (Response Null).';
                }
            }elseif (isset($bpjsJson['status']) && $bpjsJson['status'] == "sukses") {
                
                if (!empty($bpjsJson['data']['list'])) {
                    $statusResult = true;
                    $apotikList = $bpjsJson['data']['list'];
                    $htmlResult = view('referensi/partial_apotik_result', ['apotikList' => $apotikList]);
                } else {
                    $message = 'Data Apotik kosong.';
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
                'status' => false,
                'message' => 'Gagal terhubung ke API BPJS: ' . $e->getMessage()
            ]);
        }
    }

    public function search_poli()
    {
        $kode_poli  = $this->request->getPost('kode_poli');

        if (empty($kode_poli)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Kode Poli / Nama Poli tidak boleh kosong.']);
        }

        try {
            $baseUrl = base_url();
            $targetUrl = $baseUrl . 'bpjs/referensi/getpoli/' . $kode_poli;
            
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
                    if (!empty($bpjsJson['response']['list'])) {
                        $statusResult = true;
                        $poliList = $bpjsJson['response']['list'];
                        $htmlResult = view('referensi/partial_poli_result', ['poliList' => $poliList]);
                    } else {
                        $message = 'Data Poli kosong.';
                    }
                } else {
                    $message = 'Data Poli tidak ditemukan (Response Null).';
                }
            }elseif (isset($bpjsJson['status']) && $bpjsJson['status'] == "sukses") {
                
                if (!empty($bpjsJson['data']['list'])) {
                    $statusResult = true;
                    $poliList = $bpjsJson['data']['list'];
                    $htmlResult = view('referensi/partial_poli_result', ['poliList' => $poliList]);
                } else {
                    $message = 'Data Poli kosong.';
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
                'status' => false,
                'message' => 'Gagal terhubung ke API BPJS: ' . $e->getMessage()
            ]);
        }
    }

}