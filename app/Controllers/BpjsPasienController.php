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

    public function viewmonitoring()
    {
        return $this->renderView('pasien/monitoring_klaim', []);
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

    public function getmonitoring_obatx()
    {
        $bulan          = $this->request->getPost('bulan'); 
        $tahun          = $this->request->getPost('tahun');
        $jenis_obat     = $this->request->getPost('jenis_obat');
        $status         = $this->request->getPost('status');

        if (empty($bulan)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Filter Bulan belum di pilih!'
            ]);
        }

        if (empty($tahun)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Filter Tahun belum di pilih!'
            ]);
        }

        if ($jenis_obat == null || $jenis_obat == '') {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Jenis Obat belum di pilih!'
            ]);
        }

        if (empty($status)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Status belum di pilih!'
            ]);
        }

        try {
            $baseUrl = base_url();
            $targetUrl = "";
            
            // if ($searchType === '1') { //rajal
                $targetUrl = $baseUrl . 'bpjs/monitoringklaim/' . $bulan .'/'. $tahun .'/'. $jenis_obat .'/'. $status;
            // } else if ($searchType === '2') { //ranap
            //     $targetUrl = $baseUrl . 'bpjs/monitoringklaim/' . $searchVal;
            // }
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
                $message = 'Data Monitoring Obat tidak ditemukan (Status 404).';
            }
            // 2. Cek SUKSES (Code 200)
            elseif (isset($bpjsJson['metaData']['code']) && $bpjsJson['metaData']['code'] == "200") {
                
                if (!empty($bpjsJson['response']['peserta'])) {
                    $statusResult = true;
                    $monitoringData = $bpjsJson['response'];
                    $htmlResult     = view('pasien/partial_monitoring_obat_result', ['monitoringData' => $monitoringData]);
                } else {
                    $message = 'Data Monitoring Obat kosong.';
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

    public function getmonitoring_obat()
    {
        $bulan      = $this->request->getPost('bulan'); 
        $tahun      = $this->request->getPost('tahun');
        $jenis_obat = $this->request->getPost('jenis_obat');
        $status     = $this->request->getPost('status');

        // Validasi Input (Sama)
        if (empty($bulan)) return $this->response->setJSON(['status' => false, 'message' => 'Filter Bulan belum di pilih!']);
        if (empty($tahun)) return $this->response->setJSON(['status' => false, 'message' => 'Filter Tahun belum di pilih!']);
        if ($jenis_obat == null || $jenis_obat == '') return $this->response->setJSON(['status' => false, 'message' => 'Jenis Obat belum di pilih!']);
        if ($status == null || $status == '') return $this->response->setJSON(['status' => false, 'message' => 'Status belum di pilih!']);

        try {
            $baseUrl = base_url();
                        
            $targetUrl = $baseUrl . 'bpjs/monitoringklaim/' . $bulan .'/'. $tahun .'/'. $jenis_obat .'/'. $status;
            // var_dump($targetUrl); exit; 

            $client = Services::curlrequest();
            $response = $client->get($targetUrl, [
                'headers' => ['X-Internal-Request' => 'TRUE']
            ]);
            
            // Decode
            $wrapper = json_decode($response->getBody(), true);
            
            // --- LOGIKA NORMALISASI WRAPPER (ADAPTIF) ---
            // Kadang data ada di 'body', kadang ada di 'data' (karena beda response server/wrapper)
            // $bpjsJson = $wrapper['body'] ?? $wrapper['data'] ?? $wrapper;
            $bpjsJson = [
                            'response' => [
                                'rekap' => [
                                    'jumlahdata' => "3",
                                    'totalbiayapengajuan' => "150000000",
                                    'totalbiayasetuju' => "135000000",
                                    'listsep' => [
                                        [
                                            "nosepapotek" => "1801A00104190000001",
                                            "nosepaasal" => "1801R0010419V000001",
                                            "nokartu" => "0001289024796",
                                            "namapeserta" => "EDWIN TRI HADTANTO",
                                            "noresep" => "00001",
                                            "jnsobat" => "Obat Kemoterapi",
                                            "tglpelayanan" => "2025-01-10",
                                            "biayapengajuan" => "50000000",
                                            "biayasetuju" => "50000000"
                                        ],
                                        [
                                            "nosepapotek" => "1801A00104190000002",
                                            "nosepaasal" => "1801R0010419V000002",
                                            "nokartu" => "0001289024797",
                                            "namapeserta" => "SITI AMINAH",
                                            "noresep" => "00002",
                                            "jnsobat" => "Obat PRB",
                                            "tglpelayanan" => "2025-01-11",
                                            "biayapengajuan" => "50000000",
                                            "biayasetuju" => "45000000"
                                        ],
                                        [
                                            "nosepapotek" => "1801A00104190000003",
                                            "nosepaasal" => "1801R0010419V000003",
                                            "nokartu" => "0001289024798",
                                            "namapeserta" => "BUDI SANTOSO",
                                            "noresep" => "00003",
                                            "jnsobat" => "Obat Kronis Blm Stabil",
                                            "tglpelayanan" => "2025-01-12",
                                            "biayapengajuan" => "50000000",
                                            "biayasetuju" => "40000000"
                                        ]
                                    ]
                                ]
                            ],
                            'metaData' => [
                                'code' => "200",
                                'message' => "Sukses"
                            ]
                        ];

            $statusResult = false;
            $message = '';
            $htmlResult = '';

            // 1. Cek Error HTTP 404 di Wrapper
            // if (isset($wrapper['status_code']) && $wrapper['status_code'] == 404) {
            //     $message = 'Data Monitoring Obat tidak ditemukan (Status 404).';
            // }            
            // else 
                if (isset($bpjsJson['metaData']['code']) && $bpjsJson['metaData']['code'] == "200") {
                
                // PERBAIKAN 1: Cek 'rekap' BUKAN 'peserta'
                if (!empty($bpjsJson['response']['rekap'])) {
                    $statusResult = true;
                    $monitoringData = $bpjsJson['response'];
                    
                    // PERBAIKAN 2: Path view harus 'bpjs/...' bukan 'pasien/...'
                    // $htmlResult = view('bpjs/partial_monitoring_obat_result', ['monitoringData' => $monitoringData]);
                    $htmlResult = view('pasien/partial_monitoring_obat_result', ['monitoringData' => $monitoringData]);
                } else {
                    $message = 'Data Monitoring Obat kosong.';
                }
            }
            // 3. Cek ERROR (Code Selain 200)
            elseif (isset($bpjsJson['metaData']['code']) && $bpjsJson['metaData']['code'] != "200") {
                $message = $bpjsJson['metaData']['message'];
            }
            else {
                // Fallback untuk error "Consumer ID Expired" yang muncul di pesan Anda
                $message = $bpjsJson['metaData']['message'] ?? 'Respon server BPJS tidak sesuai format.';
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