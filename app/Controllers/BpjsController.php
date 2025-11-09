<?php

namespace App\Controllers;

use App\Libraries\BpjsFarmasiService;

class BpjsController extends BaseController
{
    protected $bpjsService;

    public function __construct()
    {
        $this->bpjsService = new BpjsFarmasiService();
    }

    /**
     * Contoh endpoint: Mencari data peserta berdasarkan No. Kartu BPJS
     * URL: /bpjs/peserta/0001234567890
     */
    public function getPesertaByNoKartu($noKartu)
    {
        // Endpoint dari dokumentasi API BPJS
        $endpoint = "Peserta/nokartu/" . $noKartu;
        
        // Lakukan request GET ke BPJS
        $result = $this->bpjsService->request('GET', $endpoint);

        // Kembalikan response ke client (aplikasi Anda)
        // Anda bisa menambahkan logika lain di sini
        return $this->response->setJSON($result);
    }

    /**
     * Contoh endpoint: Mencari data peserta berdasarkan NIK
     * URL: /bpjs/peserta/nik/3201011234560001
     */
    public function getPesertaByNik($nik)
    {
        $endpoint = "Peserta/nik/" . $nik;
        $result = $this->bpjsService->request('GET', $endpoint);
        return $this->response->setJSON($result);
    }
    
    /**
     * Contoh endpoint: Membuat SEP (Surat Eligibilitas Peserta)
     * URL: /bpjs/sep
     * Method: POST
     */
    public function createSEP()
    {
        $dataRequest = $this->request->getJSON();
        
        // Endpoint dari dokumentasi API BPJS
        $endpoint = "SEP/1.1/insert";
        
        // Lakukan request POST ke BPJS
        $result = $this->bpjsService->request('POST', $endpoint, (array)$dataRequest);

        return $this->response->setJSON($result);
    }
}