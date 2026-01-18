<?php

namespace App\Controllers;

use App\Libraries\BpjsFarmasiService;
use App\Libraries\BpjsVclaimService;

class BpjsController extends BaseController
{
    protected $bpjsService;
    protected $bpjsvclaimService;

    public function __construct()
    {
        $this->bpjsService = new BpjsFarmasiService();
        $this->bpjsvclaimService = new BpjsVclaimService();
    }

    /**
     * Contoh endpoint: Mencari data peserta berdasarkan No. Kartu BPJS
     * URL: /bpjs/peserta/0001234567890
     */
    public function getPesertaByNoKartu($noKartu)
    {
        $tgl        = date("Y-m-d");
        $endpoint = "Peserta/nokartu/" . $noKartu . '/tglSEP/' . $tgl;
        $result = $this->bpjsvclaimService->request('GET', $endpoint);
        return $this->response->setJSON($result);
    }

    /**
     * Contoh endpoint: Mencari data peserta berdasarkan NIK
     * URL: /bpjs/peserta/nik/3201011234560001
     */
    public function getPesertaByNik($nik)
    {
        
        $tgl        = date("Y-m-d");
        $endpoint   = "Peserta/nik/" . $nik . '/tglSEP/' . $tgl;
        $result     = $this->bpjsvclaimService->request('GET', $endpoint);
        return $this->response->setJSON($result);
    }
    
    /**
     * Contoh endpoint: Membuat SEP (Surat Eligibilitas Peserta)
     * URL: /bpjs/sep
     * Method: POST
     */
    public function createSEPX()
    {
        $dataRequest = $this->request->getJSON();
        
        // Endpoint dari dokumentasi API BPJS
        $endpoint = "SEP/1.1/insert";
        
        // Lakukan request POST ke BPJS
        $result = $this->bpjsService->request('POST', $endpoint, (array)$dataRequest);

        return $this->response->setJSON($result);
    }

    public function searchingSEPPasien($sep)
    {
        
        $endpoint   = "sep/" . $sep;
        $result     = $this->bpjsService->request('GET', $endpoint);
        return $this->response->setJSON($result);
    }
    
    /**
     * =================================================================
     *              ENDPOINT UNTUK REFERENSI
     * =================================================================
     */

    /**
     * Mencari data obat berdasarkan kriteria.
     * URL: /bpjs/referensi/obat/{kdObat}/{tgl}/{parameter}
     * Contoh: /bpjs/referensi/obat/1/2024-09-01/asam
     */
    public function getReferensiObat($kdObat, $tgl, $parameter)
    {
        $endpoint = "referensi/obat/{$kdObat}/{$tgl}/{$parameter}";
        $result = $this->bpjsService->request('GET', $endpoint);

        if ($result['status_code'] == 200) {
            $response = [
                'status' => 'sukses',
                'pesan'  => 'Berhasil',
                'data'   => $result['body']['response'] // Data sudah didekripsi di service
            ];
        } else {
            $response = [
                'status' => 'gagal',
                'pesan'  => $result['body']['metaData']['message'] ?? 'Terjadi kesalahan'
            ];
        }

        return $this->response->setJSON($response);
    }

    /**
     * Mendapatkan data referensi spesialistik.
     * URL: /bpjs/referensi/spesialistik
     */
    public function getReferensiSpesialistik()
    {
        $endpoint = "referensi/spesialistik";
        $result = $this->bpjsService->request('GET', $endpoint);

        if ($result['status_code'] == 200) {
            $response = [
                'status' => 'sukses',
                'pesan'  => 'Berhasil',
                'data'   => $result['body']['response']
            ];
        } else {
            $response = [
                'status' => 'gagal',
                'pesan'  => $result['body']['metaData']['message'] ?? 'Terjadi kesalahan'
            ];
        }

        return $this->response->setJSON($response);
    }

    /**
     * Mendapatkan data setting PPK.
     * URL: /bpjs/referensi/settingppk/{ppk}
     * Contoh: /bpjs/referensi/settingppk/0216A026
     */
    public function getReferensiSettingPpk($ppk)
    {
        $endpoint = "referensi/settingppk/read/{$ppk}";
        $result = $this->bpjsService->request('GET', $endpoint);
        return $this->response->setJSON($result);
        /*if ($result['status_code'] == 200) {
            $response = [
                'status' => 'sukses',
                'pesan'  => 'Berhasil',
                'data'   => $result['body']['response']
            ];
        } else {
            $response = [
                'status' => 'gagal',
                'pesan'  => $result['body']['metaData']['message'] ?? 'Terjadi kesalahan'
            ];
        }

        return $this->response->setJSON($response);*/
    }

    /**
     * Mencari data PPK.
     * URL: /bpjs/referensi/ppk/{jenis}/{nama}
     * Contoh: /bpjs/referensi/ppk/2/darmayu
     */
    public function getReferensiPpk($jenis, $nama)
    {
        $endpoint = "referensi/ppk/{$jenis}/{$nama}";
        $result = $this->bpjsService->request('GET', $endpoint);
        return $this->response->setJSON($result);
        // if ($result['status_code'] == 200) {
        //     $response = [
        //         'status' => 'sukses',
        //         'pesan'  => 'Berhasil',
        //         'data'   => $result['body']['response']
        //     ];
        // } else {
        //     $response = [
        //         'status' => 'gagal',
        //         'pesan'  => $result['body']['metaData']['message'] ?? 'Terjadi kesalahan'
        //     ];
        // }

        // return $this->response->setJSON($response);
    }

    /**
     * Mencari data poli.
     * URL: /bpjs/referensi/poli/{nama}
     * Contoh: /bpjs/referensi/poli/INT
     */
    public function getReferensiPoli($nama)
    {
        $endpoint = "referensi/poli/{$nama}";
        $result = $this->bpjsService->request('GET', $endpoint);
        return $this->response->setJSON($result);
        // if ($result['status_code'] == 200) {
        //     $response = [
        //         'status' => 'sukses',
        //         'pesan'  => 'Berhasil',
        //         'data'   => $result['body']['response']
        //     ];
        // } else {
        //     $response = [
        //         'status' => 'gagal',
        //         'pesan'  => $result['body']['metaData']['message'] ?? 'Terjadi kesalahan'
        //     ];
        // }

        // return $this->response->setJSON($response);
    }

    /**
     * Mendapatkan data DPHO.
     * URL: /bpjs/referensi/dpho
     */
    public function getReferensiDpho()
    {
        $endpoint = "referensi/dpho";
        $result = $this->bpjsService->request('GET', $endpoint);
        return $this->response->setJSON($result);
        // if ($result['status_code'] == 200) {
        //     $response = [
        //         'status' => 'sukses',
        //         'pesan'  => 'Berhasil',
        //         'data'   => $result['body']['response']
        //     ];
        // } else {
        //     $response = [
        //         'status' => 'gagal',
        //         'pesan'  => $result['body']['metaData']['message'] ?? 'Terjadi kesalahan'
        //     ];
        // }

        // return $this->response->setJSON($response);
    }

    public function getMonitoringKlaim($bulan, $tahun, $jenisobat, $status)
    {

        $endpoint   = "monitoring/klaim/{$bulan}/{$tahun}/{$jenisobat}/{$status}";
        $result     = $this->bpjsService->request('GET', $endpoint);

        if ($result['status_code'] == 200) {
            $response = [
                'status' => 'sukses',
                'pesan'  => 'Berhasil',
                'data'   => $result['body']
            ];
        } else {
            $response = [
                'status' => 'gagal',
                'pesan'  => $result['body']['metaData']['message'] ?? 'Terjadi kesalahan'
            ];
        }

        return $this->response->setJSON($response);
    }

    public function getRekapPasienPRB($tahun, $bulan)
    {

        $endpoint   = "Prb/rekappeserta/tahun/{$tahun}/bulan/{$bulan}";
        $result     = $this->bpjsService->request('GET', $endpoint);

        if ($result['status_code'] == 200) {
            $response = [
                'status' => 'sukses',
                'pesan'  => 'Berhasil',
                'data'   => $result['body']
            ];
        } else {
            $response = [
                'status' => 'gagal',
                'pesan'  => $result['body']['metaData']['message'] ?? 'Terjadi kesalahan'
            ];
        }

        return $this->response->setJSON($result);
    }

    public function getListPelayananObat($SEP)
    {

        $endpoint   = "obat/daftar/{$SEP}";
        $result     = $this->bpjsService->request('GET', $endpoint);

        if ($result['status_code'] == 200) {
            $response = [
                'status' => 'sukses',
                'pesan'  => 'Berhasil',
                'data'   => $result['body']
            ];
        } else {
            $response = [
                'status' => 'gagal',
                'pesan'  => $result['body']['metaData']['message'] ?? 'Terjadi kesalahan'
            ];
        }

        return $this->response->setJSON($result);
    }

    public function getRiwayatPelayananObat($tglawal,$tglakhr,$nokartu)
    {

        $endpoint   = "riwayatobat/{$tglawal}/{$tglakhr}/{$nokartu}";
        $result     = $this->bpjsService->request('GET', $endpoint);

        if ($result['status_code'] == 200) {
            $response = [
                'status' => 'sukses',
                'pesan'  => 'Berhasil',
                'data'   => $result['body']
            ];
        } else {
            $response = [
                'status' => 'gagal',
                'pesan'  => $result['body']['metaData']['message'] ?? 'Terjadi kesalahan'
            ];
        }

        return $this->response->setJSON($result);
    }
}