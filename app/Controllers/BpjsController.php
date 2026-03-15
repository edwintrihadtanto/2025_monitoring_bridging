<?php

namespace App\Controllers;

use App\Libraries\BpjsFarmasiService;
use App\Libraries\BpjsVclaimService;
use App\Libraries\BpjsFarmasi_InsertService;
use App\Libraries\BpjsVclaim_InsertService;

class BpjsController extends BaseController
{
    protected $bpjsService;
    protected $bpjsvclaimService;
    protected $bpjsInsertService;
    protected $ppkFarmasi;

    public function __construct()
    {
        $this->bpjsService              = new BpjsFarmasiService();
        $this->bpjsInsertService        = new BpjsFarmasi_InsertService();
        $this->bpjsvclaimService        = new BpjsVclaimService();
        $this->bpjsInsertVclaimService  = new BpjsVclaim_InsertService();
        $this->ppkFarmasi               = env('BPJS.Ppk');
        $this->ppkSoedono               = env('BPJSSOEDONO.Ppk');
    }

    public function getPesertaByNoKartu($noKartu)
    {
        $tgl        = date("Y-m-d");
        $endpoint = "Peserta/nokartu/" . $noKartu . '/tglSEP/' . $tgl;
        $result = $this->bpjsvclaimService->request('GET', $endpoint);
        return $this->response->setJSON($result);
    }

    /**
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
     * URL: /bpjs/sep
     * Method: POST
     */
    public function createSEPBPJS()
    {
        // $dataRequest = $this->request->getJSON();
        
        // Endpoint dari dokumentasi API BPJS
        // $endpoint = "/SEP/2.0/insert";
        
        // // Lakukan request POST ke BPJS
        // $result = $this->bpjsService->request('POST', $endpoint, (array)$dataRequest);

        // return $this->response->setJSON($result);
        $userID     = '12345';
        $payload    = json_encode([
                    "request" => [
                        "t_sep" => [
                            "noKartu"      => "0002059334728",
                            "tglSep"       => date('Y-m-d'),
                            "ppkPelayanan" => $this->ppkSoedono,
                            "jnsPelayanan" => "2", // 1=RANAP, 2=RAJAL

                            "klsRawat" => [
                                "klsRawatHak"  => "3",
                                "klsRawatNaik" => "",
                                "pembiayaan"   => "",
                                "penanggungJawab" => ""
                            ],

                            "noMR" => '0-00-00-01',

                            "rujukan" => [
                                "asalRujukan" => "2",
                                "tglRujukan"  => date('Y-m-d'),
                                "noRujukan"   => "",
                                "ppkRujukan"  => ""
                            ],

                            "catatan" => "-",
                            "diagAwal" => "J18.0",

                            "poli" => [
                                "tujuan"    => "IGD",
                                "eksekutif" => "0"
                            ],

                            "cob" => [
                                "cob" => "0"
                            ],

                            "katarak" => [
                                "katarak" => "0"
                            ],

                            "jaminan" => [
                                "lakaLantas" => "0",
                                "noLP" => "",
                                "penjamin" => [
                                    "tglKejadian" => "",
                                    "keterangan"  => "",
                                    "suplesi" => [
                                        "suplesi" => "0",
                                        "noSepSuplesi" => "",
                                        "lokasiLaka" => [
                                            "kdPropinsi"  => "",
                                            "kdKabupaten" => "",
                                            "kdKecamatan" => ""
                                        ]
                                    ]
                                ]
                            ],

                            // ===== FIELD BARU =====
                            "tujuanKunj" => "0",
                            "flagProcedure" => "",
                            "kdPenunjang" => "",
                            "assesmentPel" => "",

                            "skdp" => [
                                "noSurat"  => "",
                                "kodeDPJP" => ""
                            ],

                            "dpjpLayan" => "37722", // wajib jika RAJAL
                            "noTelp" => "081111111101",
                            "user"   => "Coba Web Service Farmasi"
                        ]
                    ]
                ]);
        
        $endpoint = '/SEP/2.0/insert';        
        $result = $this->bpjsInsertVclaimService->request('POST', $endpoint, $payload, $userID);
        return $this->response->setJSON($result);
    }
    
    public function delSEP()
    {
        $userID     = '999';
        $payload    = json_encode([
                    "request" => [
                        "t_sep" => [
                            "noSep"  => "1308R0010326V000004",
                            "user"   => "Coba Web Service Farmasi"
                        ]
                    ]
                ]);
        $endpoint   = '/SEP/2.0/delete';
        $result     = $this->bpjsInsertVclaimService->request('DELETE', $endpoint, $payload, $userID);
        return $this->response->setJSON($result);
    }

    public function searchingSEPPasien($sep)
    {
        
        $endpoint   = "SEP/" . $sep;
        // $result     = $this->bpjsService->request('GET', $endpoint);
        $result     = $this->bpjsvclaimService->request('GET', $endpoint);
        return $this->response->setJSON($result);
    }
    
    public function getReferensiObat($kdObat, $tgl, $parameter)
    {
        $endpoint = "referensi/obat/{$kdObat}/{$tgl}/{$parameter}";
        $result = $this->bpjsService->request('GET', $endpoint);
        return $this->response->setJSON($result);
    }

    public function getReferensiSpesialistik()
    {
        $endpoint = "referensi/spesialistik";
        $result = $this->bpjsService->request('GET', $endpoint);
        return $this->response->setJSON($result);
    }

    public function getReferensiSettingPpk($ppk)
    {
        $endpoint = "referensi/settingppk/read/{$ppk}";
        $result = $this->bpjsService->request('GET', $endpoint);
        return $this->response->setJSON($result);
    }

    public function getReferensiPpk($jenis, $nama, $userID)
    {   
        $data = null;
        $endpoint = "referensi/ppk/{$jenis}/{$nama}";
        $result = $this->bpjsService->request('GET', $endpoint, $data, $userID);
        return $this->response->setJSON($result);
    }

    public function getReferensiPoli($nama)
    {
        $endpoint = "referensi/poli/{$nama}";
        $result = $this->bpjsService->request('GET', $endpoint);
        return $this->response->setJSON($result);
    }

    public function getReferensiDpho()
    {
        $endpoint = "referensi/dpho";
        $result = $this->bpjsService->request('GET', $endpoint);
        return $this->response->setJSON($result);
    }

    public function getMonitoringKlaim($bulan, $tahun, $jenisobat, $status)
    {
        $endpoint   = "monitoring/klaim/{$bulan}/{$tahun}/{$jenisobat}/{$status}";
        $result     = $this->bpjsService->request('GET', $endpoint);
        return $this->response->setJSON($result);
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

    public function DELpelayananobat($sepapotek, $noresep, $kodeobat)
    {
        $payload = json_encode([
            'nosepapotek'   => '1801A00104190000001',
            'noresep'       => '12345',
            'kodeobat'      => '25180404057',
            'tipeobat'      => 'N'
        ]);
        $endpoint = '/pelayanan/obat/hapus/';
        $result = $this->bpjsInsertService->request('DELETE', $endpoint, $payload);
        return $this->response->setJSON($result);
    }

    public function getListPelayananObatX($SEP, $userID)
    {
        $data = null;
        $endpoint   = "obat/daftar/{$SEP}";
        $result     = $this->bpjsService->request('GET', $endpoint, $data, $userID);
        return $this->response->setJSON($result);
    }

    public function getListPelayananObat($SEP, $userID)
    {
        $data = null;
        $endpoint   = "Rujukan/Peserta/{$SEP}";
        $result     = $this->bpjsvclaimService->request('GET', $endpoint, $data, $userID);
        return $this->response->setJSON($result);
    }

    public function getRiwayatPelayananObat($tglawal, $tglakhr, $nokartu, $userID)
    {
        $data = null;
        $endpoint   = "riwayatobat/{$tglawal}/{$tglakhr}/{$nokartu}";
        $result     = $this->bpjsService->request('GET', $endpoint, $data, $userID);
        return $this->response->setJSON($result);
    }

    public function daftarresep($tglawal, $tglakhr, $kdjnisobat, $userID)
    {
        $payload = json_encode([
            'kdppk'     => $this->ppkFarmasi,
            'KdJnsObat' => $kdjnisobat, // (1. Obat PRB, 2. Obat Kronis Blm Stabil, 3. Obat Kemoterapi)
            'JnsTgl'    => 'TGLPELSJP',
            'TglMulai'  => $tglawal,
            'TglAkhir'  => $tglakhr
        ]);
        $endpoint = '/daftarresep';
        $result = $this->bpjsInsertService->request('POST', $endpoint, $payload, $userID);
        return $this->response->setJSON($result);
    }

    public function getkirimresep($refasalsjp, $poli, $noresep_bpjs, $tglresep, $tglpelayanan, $kd_dokterbpjs, $iterasi, $userID)
    // public function getkirimresep($userID)
    {
        /*$payload = json_encode([
            'TGLSJP'     => date('Y-m-d H:i:s'),
            'REFASALSJP' => '1308R0010326V000008',
            'POLIRSP'    => 'IGD',
            'KDJNSOBAT'  => '2', // (1. Obat PRB, 2. Obat Kronis Blm Stabil, 3. Obat Kemoterapi)
            'NORESEP'    => '00135', //harus 5 digit dan berulang
            'IDUSERSJP'  => 'USR-'.$userID,
            'TGLRSP'     => date('Y-m-d 00:00:00'),
            'TGLPELRSP'  => date('Y-m-d 00:00:00'),
            'KdDokter'   => '0',
            'iterasi'    => '0'
        ]);*/

        // Keterangan:
        // Tgl Entry : Tanggal Resep dientri/direkam ke aplikasi
        // TglResep : Tanggal Tertera pada Lembar Resep
        // TglPelayanan : Tanggal saat resep dilayani/diterima Apotek/Instasi Farmasi
        // TglSEP- 15  Hari <= TglSEP <= TglResep <= TglEntry <= TglSistem

        $payload = json_encode([
            'TGLSJP'     => date('Y-m-d H:i:s'),
            'REFASALSJP' => $refasalsjp,
            'POLIRSP'    => $poli,
            'KDJNSOBAT'  => '3',
            'NORESEP'    => $noresep_bpjs,
            'IDUSERSJP'  => 'FAR-'.$userID,
            'TGLRSP'     => $tglresep,
            'TGLPELRSP'  => $tglpelayanan,
            'KdDokter'   => $kd_dokterbpjs,
            'iterasi'    => $iterasi
        ]);

        $endpoint = '/sjpresep/v3/insert';
        $result = $this->bpjsInsertService->request('POST', $endpoint, $payload, $userID);

        log_message('info', 'Payload Resep: ' . $payload);
        log_message('info', 'Response BPJS: ' . json_encode($result));
        return $this->response->setJSON($result);
    }

    public function del_hapusresep($no_resep, $no_apotik, $refasalsjp, $userID)
    {
        $payload = json_encode([
            'nosjp'         => $no_apotik,
            'refasalsjp'    => $refasalsjp,
            'noresep'       => $no_resep
        ]);
        $endpoint = '/hapusresep';
        $result = $this->bpjsInsertService->request('DELETE', $endpoint, $payload, $userID);
        return $this->response->setJSON($result);
    }

    //END RESEP
}