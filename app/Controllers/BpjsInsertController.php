<?php

namespace App\Controllers;

use App\Libraries\BpjsFarmasiService;

class BpjsInsertController extends BaseController
{
    protected $bpjsInsertService;

    public function __construct()
    {
        $this->bpjsInsertService = new BpjsFarmasiService();
    }

    public function viewgetdaftar_resep(){
        return $this->renderView('resep/sidebar-listresep');
    }

    public function daftarresep()
    {
        // $payload = $this->request->getJSON(true); // ambil body JSON
        $payload = [            
            "kdppk" => "0112A017",
            "KdJnsObat" => "0",
            "JnsTgl" => "TGLPELSJP",
            "TglMulai" => "2019-03-01 08:49:45",
            "TglAkhir" => "2019-03-31 06:18:33"
        ];
        
        if (empty($payload)) {
            return $this->response->setJSON([
                'status_code' => 400,
                'message' => 'Payload daftarresep wajib diisi'
            ])->setStatusCode(400);
        }

        $endpoint = 'daftarresep';
        $result   = $this->bpjsInsertService->request('POST', $endpoint, $payload);

        return $this->response->setJSON($result);
    }


    /*public function getRiwayatPelayananObat($tglawal,$tglakhr,$nokartu)
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
    }*/
}