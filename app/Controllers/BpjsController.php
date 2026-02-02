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
        $this->bpjsService = new BpjsFarmasiService();
        $this->bpjsvclaimService = new BpjsVclaimService();
        $this->bpjsInsertService = new BpjsFarmasi_InsertService();
        $this->bpjsInsertVclaimService = new BpjsVclaim_InsertService();
        $this->ppkFarmasi        = env('BPJS.Ppk');
        $this->ppkSoedono        = env('BPJSSOEDONO.Ppk');
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
    public function createSEPBPJS()
    {
        // $dataRequest = $this->request->getJSON();
        
        // Endpoint dari dokumentasi API BPJS
        // $endpoint = "SEP/1.1/insert";
        
        // // Lakukan request POST ke BPJS
        // $result = $this->bpjsService->request('POST', $endpoint, (array)$dataRequest);

        // return $this->response->setJSON($result);
        $userID = '123';
        $payload1_0 = json_encode([
                    "request" => [
                        "t_sep" => [
                            "noKartu"       => "0002056469703",
                            "tglSep"        => date('Y-m-d'),
                            "ppkPelayanan"  => $this->ppkSoedono,
                            "jnsPelayanan"  => "2", // 1=Rawat Inap, 2=Rawat Jalan
                            "klsRawat"      => "3",
                            "noMR"          => "0-00-00-1",

                            "rujukan" => [
                                "asalRujukan" => "1",
                                "tglRujukan"  => date('Y-m-d'),
                                "noRujukan"   => "130801021221P000002",
                                "ppkRujukan"  => "13080102"
                            ],

                            "catatan" => "testinsert RJ",
                            "diagAwal" => "J18.0",

                            "poli" => [
                                "tujuan"     => "PAR",
                                "eksekutif"  => "0"
                            ],

                            "cob" => [
                                "cob" => "0"
                            ],

                            "katarak" => [
                                "katarak" => "0"
                            ],

                            "jaminan" => [
                                "lakaLantas" => "0",
                                "penjamin" => [
                                    "penjamin"   => "",
                                    "tglKejadian"=> "",
                                    "keterangan" => "",
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

                            "skdp" => [
                                "noSurat"  => "1308R0011221K000003",
                                "kodeDPJP" => "31014"
                            ],

                            "noTelp" => "081111111101",
                            "user"   => "Coba Ws"
                        ]
                    ]
                ]);

        $payload    = json_encode([
                    "request" => [
                        "t_sep" => [
                            "noKartu"      => "0002056469703",
                            "tglSep"       => date('Y-m-d'),
                            "ppkPelayanan" => $this->ppkSoedono,
                            "jnsPelayanan" => "2", // 1=RANAP, 2=RAJAL

                            "klsRawat" => [
                                "klsRawatHak"  => "3",
                                "klsRawatNaik" => "",
                                "pembiayaan"   => "",
                                "penanggungJawab" => ""
                            ],

                            "noMR" => '0-00-00-1',

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
                            "user"   => "Coba ws fARMASI"
                        ]
                    ]
                ]);
        // $endpoint = '/SEP/1.1/insert';
        $endpoint = '/SEP/2.0/insert';        
        $result = $this->bpjsInsertVclaimService->request('POST', $endpoint, $payload, $userID);
        return $this->response->setJSON($result);
    }

    public function searchingSEPPasien($sep)
    {
        
        $endpoint   = "SEP/" . $sep;
        // $result     = $this->bpjsService->request('GET', $endpoint);
        $result     = $this->bpjsvclaimService->request('GET', $endpoint);
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
        return $this->response->setJSON($result);
        /*if ($result['status_code'] == 200) {
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

        return $this->response->setJSON($response);*/
    }

    /**
     * Mendapatkan data referensi spesialistik.
     * URL: /bpjs/referensi/spesialistik
     */
    public function getReferensiSpesialistik()
    {
        $endpoint = "referensi/spesialistik";
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
    public function getReferensiPpk($jenis, $nama, $userID)
    {   
        $data = null;
        $endpoint = "referensi/ppk/{$jenis}/{$nama}";
        $result = $this->bpjsService->request('GET', $endpoint, $data, $userID);
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
        return $this->response->setJSON($result);
        /*if ($result['status_code'] == 200) {
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

        return $this->response->setJSON($response);*/
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

    //RESEP
    public function daftarresep($tglawal, $tglakhr, $userID)
    {
        $payload = json_encode([
            'kdppk'     => $this->ppkFarmasi,
            'KdJnsObat' => '0',
            'JnsTgl'    => 'TGLPELSJP',
            'TglMulai'  => $tglawal . ' 00:00:00',
            'TglAkhir'  => $tglakhr . ' 23:59:59'
        ]);
        $endpoint = '/daftarresep';
        $result = $this->bpjsInsertService->request('POST', $endpoint, $payload, $userID);
        return $this->response->setJSON($result);
    }

    // public function sjpresep($tglsjp, $refasalsjp, $poli, $noresep, $tglresep, $tglpelayanan, $KdDokter, $userID)
    public function sjpresep($userID)
    {
        $payload = json_encode([
            'TGLSJP'     => date('Y-m-d H:i:s'),
            'REFASALSJP' => '1308R0010226V000001',
            'POLIRSP'    => 'IGD',
            'KDJNSOBAT'  => '3',
            'NORESEP'    => '00002',
            'IDUSERSJP'  => 'USR-01',
            'TGLRSP'     => '2026-02-02 00:00:00', 
            'TGLPELRSP'  => '2026-02-02 00:00:00',
            'KdDokter'   => '0',
            'iterasi'    => '0'
        ]);
        $endpoint = '/sjpresep/v3/insert';
        $result = $this->bpjsInsertService->request('POST', $endpoint, $payload, $userID);
        return $this->response->setJSON($result);
    }

    public function del_hapusresep($no_resep, $no_sep, $refasalsjp, $userID)
    {
        $payload = json_encode([
            'nosjp'         => $no_sep,
            'refasalsjp'    => $refasalsjp,
            'noresep'       => $no_resep
        ]);
        $endpoint = '/hapusresep';
        $result = $this->bpjsInsertService->request('DELETE', $endpoint, $payload, $userID);
        return $this->response->setJSON($result);
    }
    //END RESEP
}