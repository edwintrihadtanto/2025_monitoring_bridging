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

    /*VCLAIM*/
    public function getPesertaByNoKartu($noKartu)
    {
        $tgl        = date("Y-m-d");
        $endpoint = "Peserta/nokartu/" . $noKartu . '/tglSEP/' . $tgl;
        $result = $this->bpjsvclaimService->request('GET', $endpoint);
        return $this->response->setJSON($result);
    }

    public function getPesertaByNik($nik)
    {
        
        $tgl        = date("Y-m-d");
        $endpoint   = "Peserta/nik/" . $nik . '/tglSEP/' . $tgl;
        $result     = $this->bpjsvclaimService->request('GET', $endpoint);
        return $this->response->setJSON($result);
    }
    
    //IGD
    public function createSEPBPJS_IGD()
    {
        $userID     = '12345';
        $payload    = json_encode([
                    "request" => [
                        "t_sep" => [
                            "noKartu"      => "0002056469703",
                            "tglSep"       => date('Y-m-d'),
                            "ppkPelayanan" => $this->ppkSoedono,
                            "jnsPelayanan" => "2", // 1=RANAP, 2=RAJAL

                            "klsRawat" => [
                                "klsRawatHak"  => "2",
                                "klsRawatNaik" => "",
                                "pembiayaan"   => "",
                                "penanggungJawab" => ""
                            ],

                            "noMR" => '0-00-00-04',

                            "rujukan" => [
                                "asalRujukan" => "2",
                                "tglRujukan"  => "",
                                "noRujukan"   => "",
                                "ppkRujukan"  => $this->ppkSoedono,
                            ],

                            "catatan" => "-",
                            "diagAwal" => "I11",

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

                            "dpjpLayan" => "299693", // wajib jika RAJAL
                            "noTelp" => "081111111101",
                            "user"   => "Coba Web Service Farmasi"
                        ]
                    ]
                ]);
        
        $endpoint = '/SEP/2.0/insert';        
        $result = $this->bpjsInsertVclaimService->request('POST', $endpoint, $payload, $userID);
        return $this->response->setJSON($result);
    }
    //JALAN
    public function createSEPBPJS_JALAN()
    {
        $userID     = '12345';
        $payload    = json_encode([
                    "request" => [
                        "t_sep" => [
                            "noKartu"      => "0002056469703",
                            "tglSep"       => date('Y-m-d'),
                            "ppkPelayanan" => $this->ppkSoedono,
                            "jnsPelayanan" => "2", // 1=RANAP, 2=RAJAL

                            "klsRawat" => [
                                "klsRawatHak"  => "2",
                                "klsRawatNaik" => "",
                                "pembiayaan"   => "",
                                "penanggungJawab" => ""
                            ],

                            "noMR" => '6-90-63-64',

                            "rujukan" => [
                                "asalRujukan" => "2",
                                "tglRujukan"  => "2026-04-07",
                                "noRujukan"   => "1308R0010426V000003",
                                "ppkRujukan"  => $this->ppkSoedono,
                            ],

                            "catatan" => "-",
                            "diagAwal" => "I10",

                            "poli" => [
                                "tujuan"    => "INT",
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
                                "noSurat"  => "1308R0010426K000005",
                                "kodeDPJP" => "299693"
                            ],

                            "dpjpLayan" => "299693", // wajib jika RAJAL
                            "noTelp" => "081111111101",
                            "user"   => "Coba Web Service Farmasi"
                        ]
                    ]
                ]);
        
        $endpoint = '/SEP/2.0/insert';        
        $result = $this->bpjsInsertVclaimService->request('POST', $endpoint, $payload, $userID);
        return $this->response->setJSON($result);
    }

    public function createSEPBPJS_JALAN2()
    {
        $userID     = '12345';
        $payload    = json_encode([
                    "request" => [
                        "t_sep" => [
                            "noKartu"      => "0002046121615",
                            "tglSep"       => date('Y-m-d'),
                            "ppkPelayanan" => $this->ppkSoedono,
                            "jnsPelayanan" => "2", // 1=RANAP, 2=RAJAL

                            "klsRawat" => [
                                "klsRawatHak"  => "2",
                                "klsRawatNaik" => "",
                                "pembiayaan"   => "",
                                "penanggungJawab" => ""
                            ],

                            "noMR" => '0-00-00-01',

                            "rujukan" => [
                                "asalRujukan" => "2",
                                "tglRujukan"  => "",
                                "noRujukan"   => "",
                                "ppkRujukan"  => "",
                            ],

                            "catatan" => "-",
                            "diagAwal" => "I10",

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
                                "kodeDPJP" => "299693"
                            ],

                            "dpjpLayan" => "299693", // wajib jika RAJAL
                            "noTelp" => "081111111101",
                            "user"   => "Coba Web Service Farmasi"
                        ]
                    ]
                ]);
        
        $endpoint = '/SEP/2.0/insert';        
        $result = $this->bpjsInsertVclaimService->request('POST', $endpoint, $payload, $userID);
        return $this->response->setJSON($result);
    }
    //INAP
    public function createSEPBPJS_INAP()
    {
        $userID     = '12345';
        $payload    = json_encode([
                    "request" => [
                        "t_sep" => [
                            "noKartu"      => "0002056469703",
                            "tglSep"       => date('Y-m-d'),
                            "ppkPelayanan" => $this->ppkSoedono,
                            "jnsPelayanan" => "1", // 1=RANAP, 2=RAJAL

                            "klsRawat" => [
                                "klsRawatHak"  => "2",
                                "klsRawatNaik" => "",
                                "pembiayaan"   => "",
                                "penanggungJawab" => ""
                            ],

                            "noMR" => '0-00-00-04',

                            "rujukan" => [
                                "asalRujukan" => "2",
                                "tglRujukan"  => date('Y-m-d'),
                                "noRujukan"   => "1308R0010326V000039", //INI PENTING SESUAI SEP AWAL IGD
                                "ppkRujukan"  => $this->ppkSoedono
                            ],

                            "catatan" => "SJP RWI",
                            "diagAwal" => "I10",

                            "poli" => [
                                "tujuan"    => "",
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
                                "noSurat"  => "1308R0010326K000002", //INI PENTING SESUAI BUATSPRI
                                "kodeDPJP" => "30882"
                            ],

                            "dpjpLayan" => "", // wajib jika RAJAL
                            "noTelp" => "081111111101",
                            "user"   => "Coba Web Service Farmasi"
                        ]
                    ]
                ]);
        
        $endpoint = '/SEP/2.0/insert';        
        $result = $this->bpjsInsertVclaimService->request('POST', $endpoint, $payload, $userID);
        return $this->response->setJSON($result);
    }
    
    public function createRUJUKAN()
    {
        $userID     = '12345';
        $payload    = json_encode([
                    "request" => [
                        "t_rujukan" => [
                            "noSep"      => "1308R0010326V000040",
                            "tglRujukan"       => '2026-03-27',
                            "tglRencanaKunjungan" => '2026-04-03',
                            "ppkDirujuk" => '0216R010', 
                            "jnsPelayanan" => "1", 
                            "catatan" => "buatrujuakan", 
                            "diagRujukan" => "I10", 
                            "tipeRujukan" => "0", //{0->Penuh, 1->Partial, 2->balik PRB}
                            "poliRujukan" => "", //{kosong untuk tipe rujukan 2, harus diisi jika 0 atau 1}
                            "user"   => "Coba Web Service Farmasi"
                        ]
                    ]
                ]);
        
        $endpoint = '/Rujukan/2.0/insert';        
        $result = $this->bpjsInsertVclaimService->request('POST', $endpoint, $payload, $userID);
        return $this->response->setJSON($result);
    }

    public function createPostMRS()
    {
        $userID     = '12345';
        $payload    = json_encode([
                        "request" => [                        
                            "noSEP"             => "1308R0010326V000040",
                            "kodeDokter"        => '299693',
                            "poliKontrol"       => 'INT',
                            "tglRencanaKontrol" => '2026-04-04',
                            "user"              => "Coba Web Service Farmasi"
                        ]
                    ]);
        $endpoint = '/RencanaKontrol/insert';        
        $result = $this->bpjsInsertVclaimService->request('POST', $endpoint, $payload, $userID);
        return $this->response->setJSON($result);
    }

    public function batalPostMRS()
    {
        $userID     = '12345';
        $payload    = json_encode([
                        "request" => [                        
                            "t_suratkontrol" => [
                                    "noSuratKontrol"      => "1308R0010326K000004",
                                    "user"          => "Coba Web Service Farmasi"
                                ]
                        ]
                    ]);
        $endpoint = '/RencanaKontrol/Delete';        
        $result = $this->bpjsInsertVclaimService->request('DELETE', $endpoint, $payload, $userID);
        return $this->response->setJSON($result);
    }

    public function getRencanaKontrol()
    {
        $userID     = '12345';
        $payload    = '';
        $endpoint = '/RencanaKontrol/nosep/1308R0010326V000040';        
        $result = $this->bpjsInsertVclaimService->request('GET', $endpoint, $payload, $userID);
        return $this->response->setJSON($result);
    }

    public function getfinger()
    {
        $userID     = '12345';
        $payload    = '';
        $endpoint = '/SEP/FingerPrint/Peserta/0003339213344/TglPelayanan/2026-04-02';        
        $result = $this->bpjsvclaimService->request('GET', $endpoint);
        return $this->response->setJSON($result);
    }

    public function batalRUJUKAN()
    {
        $userID     =   '12345';
        $payload    =   json_encode([
                            "request" => [
                                "t_rujukan" => [
                                    "noRujukan"      => "1308R0010326B000003",
                                    "user"          => "Coba Web Service Farmasi"
                                ]
                            ]
                        ]);
        
        $endpoint = '/Rujukan/delete';        
        $result = $this->bpjsInsertVclaimService->request('DELETE', $endpoint, $payload, $userID);
        return $this->response->setJSON($result);
    }

    public function approval()
    {
        $userID     =   '12345';
        $payload    =   json_encode([
                            "request" => [
                                "t_sep" => [
                                    "noKartu"      => "0002056469703",
                                    "tglSep"       => "2026-03-27",
                                    "jnsPelayanan" => "2",
                                    // "jnsPengajuan" => "2",
                                    "keterangan"    => "Hari libur",
                                    "user"          => "Coba Web Service Farmasi"
                                ]
                            ]
                        ]);
        
        $endpoint = '/Sep/aprovalSEP';        
        $result = $this->bpjsInsertVclaimService->request('POST', $endpoint, $payload, $userID);
        return $this->response->setJSON($result);
    }

    public function buatSPRI(){
        $userID     = '12345';
        $payload    =   json_encode([
                            "request" => [
                                "noKartu"      => "0002056469703",
                                "kodeDokter"       => "30882",
                                "poliKontrol" => "BED",
                                "tglRencanaKontrol" => date('Y-m-d'),
                                "user"   => "Coba Web Service Farmasi"
                            ]
                        ]);
        
        $endpoint = '/RencanaKontrol/InsertSPRI';        
        $result = $this->bpjsInsertVclaimService->request('POST', $endpoint, $payload, $userID);
        return $this->response->setJSON($result);
    }

    public function getRujukan($nokartu)
    {
        $endpoint = "Rujukan/List/Peserta/{$nokartu}";
        $result = $this->bpjsvclaimService->request('GET', $endpoint);
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
    /*END VCLAIM*/
    public function searchingSEPPasien($sep)
    {
        
        // $endpoint   = "SEP/" . $sep;
        // $result     = $this->bpjsvclaimService->request('GET', $endpoint);
        $endpoint   = "sep/" . $sep;
        $result     = $this->bpjsService->request('GET', $endpoint);
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

    public function getListPelayananObat($SEP, $userID)
    {
        $data = null;
        $endpoint   = "obat/daftar/{$SEP}";
        $result     = $this->bpjsService->request('GET', $endpoint, $data, $userID);
        return $this->response->setJSON($result);
    }

    public function getListPelayananObatXX($SEP, $userID)
    {
        $data = null;
        $endpoint   = "Rujukan/{$SEP}";
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

    public function getkirimresep($refasalsjp, $poli, $noresep_bpjs, $tglresep, $tglpelayanan, $kd_dokterbpjs, $iterasi, $kdjnsobat, $userID)
    // public function getkirimresep($userID)
    {
        // $payload = json_encode([
        //     'TGLSJP'     => date('Y-m-d H:i:s'),
        //     'REFASALSJP' => '1308R0010326V000017',
        //     'POLIRSP'    => 'INT',
        //     'KDJNSOBAT'  => '2', // (1. Obat PRB, 2. Obat Kronis Blm Stabil, 3. Obat Kemoterapi)
        //     'NORESEP'    => '00005', //harus 5 digit dan berulang
        //     'IDUSERSJP'  => 'USR-'.$userID,
        //     'TGLRSP'     => date('Y-m-d 00:00:00'),
        //     'TGLPELRSP'  => date('Y-m-d 00:00:00'),
        //     'KdDokter'   => '0',
        //     'iterasi'    => '0' //(0. Non Iterasi, 1. Iterasi)
        // ]);

        // Keterangan:
        // Tgl Entry : Tanggal Resep dientri/direkam ke aplikasi
        // TglResep : Tanggal Tertera pada Lembar Resep
        // TglPelayanan : Tanggal saat resep dilayani/diterima Apotek/Instasi Farmasi
        // TglSEP- 15  Hari <= TglSEP <= TglResep <= TglEntry <= TglSistem

        $payload = json_encode([
            'TGLSJP'     => date('Y-m-d H:i:s'),
            'REFASALSJP' => $refasalsjp,
            'POLIRSP'    => $poli,
            'KDJNSOBAT'  => $kdjnsobat,
            'NORESEP'    => $noresep_bpjs,
            'IDUSERSJP'  => 'FAR-'.$userID,
            'TGLRSP'     => $tglresep,
            'TGLPELRSP'  => $tglpelayanan,
            'KdDokter'   => $kd_dokterbpjs,
            'iterasi'    => $iterasi
        ]);

        $endpoint = '/sjpresep/v3/insert';
        $result = $this->bpjsInsertService->request('POST', $endpoint, $payload, $userID);

        // log_message('info', 'Payload Resep: ' . $payload);
        // log_message('info', 'Response BPJS: ' . json_encode($result));
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

       // public function obatnonracikan($sepapotik, $noresep_bpjs, $kdobat, $nmobat, $signasatu, $signadua, $qty, $jho, $catkhususobat)
    public function obatnonracikanXX()
    {
        $userID = '1';
        $payload = json_encode([
            'NOSJP'         => '0216A01603260000018',
            'NORESEP'       => '00003',
            'KDOBT'         => '14250800912',
            'NMOBAT'        => 'Mesalazine 250 SK tab 250 mg',
            'SIGNA1OBT'     => '2',
            'SIGNA2OBT'     => '1',
            'JMLOBT'        => 10,
            'JHO'           => '5',
            'CatKhsObt'     => 'single'
        ]);

       /* $payload = json_encode([
            'NOSJP'         => $sepapotik,
            'NORESEP'       => $noresep_bpjs,
            'KDOBT'         => $kdobat,
            'NMOBAT'        => $nmobat,
            'SIGNA1OBT'     => $signasatu,
            'SIGNA2OBT'     => $signadua,
            'JMLOBT'        => $qty,
            'JHO'           => $jho,
            'CatKhsObt'     => $catkhususobat
        ]);*/

        $endpoint = '/obatnonracikan/v3/insert';
        $result = $this->bpjsInsertService->request('POST', $endpoint, $payload, $userID);

        return $this->response->setJSON($result);
    }

    public function obatnonracikan()
    {
        $request = $this->request->getJSON(true);
        
        if (!$request) {
            return $this->response->setJSON([
                'status'  => false,
                'code'    => '400',
                'message' => 'Payload kosong'
            ]);
        }

        $userID = session()->get('id') ?? '1';

        // Ambil data dari payload
        $payload = json_encode([
            'NOSJP'         => $request['NOSJP'] ?? '',
            'NORESEP'       => $request['NORESEP'] ?? '',
            'KDOBT'         => $request['KDOBT'] ?? '',
            'NMOBAT'        => $request['NMOBAT'] ?? '',
            'SIGNA1OBT'     => $request['SIGNA1OBT'] ?? '1',
            'SIGNA2OBT'     => $request['SIGNA2OBT'] ?? '1',
            'JMLOBT'        => $request['JMLOBT'] ?? 0,
            'JHO'           => $request['JHO'] ?? '0',
            'CatKhsObt'     => $request['CatKhsObt'] ?? 'Single'
        ]);

        $endpoint = '/obatnonracikan/v3/insert';
        $result = $this->bpjsInsertService->request('POST', $endpoint, $payload, $userID);

        return $this->response->setJSON($result);
    }

    public function obatracikan()
    {
        $request = $this->request->getJSON(true);
        
        if (!$request) {
            return $this->response->setJSON([
                'status'  => false,
                'code'    => '400',
                'message' => 'Payload kosong'
            ]);
        }

        $userID = session()->get('id') ?? '1';

        $signa1     = 1;
        $signa2     = 1;
        $jho        = 1;
        $permintaan = 1;

        // $qty = $signa1 * $signa2 * $jho;
        $qty = 1;
        $payload = json_encode([
            'NOSJP'         => $request['NOSJP'] ?? '',
            'NORESEP'       => $request['NORESEP'] ?? '',
            'JNSROBT'       => 'R.01',
            'KDOBT'         => $request['KDOBT'] ?? '',
            'NMOBAT'        => $request['NMOBAT'] ?? '',
            'SIGNA1OBT'     => $request['SIGNA1OBT'] ?? '1',
            'SIGNA2OBT'     => $request['SIGNA2OBT'] ?? '1',
            'PERMINTAAN'    => $request['PERMINTAAN'] ?? '1',
            'JMLOBT'        => $request['JMLOBT'] ?? 0,
            'JHO'           => $request['JHO'] ?? '0',
            'CatKhsObt'     => $request['CatKhsObt'] ?? 'Racikan'
        ]);
        /*$payload = json_encode([
            'NOSJP'      => '0216A01603260000015',
            'NORESEP'    => '00014',
            'JNSROBT'    => 'R.01',
            'KDOBT'      => '14250805202',
            'NMOBAT'     => 'tiotropium 2,5 SK cairan ih 2,5 mcg/semprot',
            'SIGNA1OBT'  => $signa1,
            'SIGNA2OBT'  => $signa2,
            'PERMINTAAN' => $permintaan,
            'JHO'        => $jho,
            'JMLOBT'     => $qty,
            'CatKhsObt'  => 'RACIKAN PUYER'
        ]);*/

       /* $payload = json_encode([
            'NOSJP'         => $sepapotik,
            'NORESEP'       => $noresep_bpjs,
            'JNSROBT'       => 'R.01',
            'KDOBT'         => $kdobat,
            'NMOBAT'        => $nmobat,
            'SIGNA1OBT'     => $signasatu,
            'SIGNA2OBT'     => $signadua,
            'PERMINTAAN'    => '1',
            'JMLOBT'        => $qty,
            'JHO'           => $jho,
            'CatKhsObt'     => $catkhususobat
        ]);*/

        $endpoint = '/obatracikan/v3/insert';
        $result = $this->bpjsInsertService->request('POST', $endpoint, $payload, $userID);

        return $this->response->setJSON($result);
    }

    /*{
        "status":"gagal",
        "code":"404",
        "message":"Unauthorized! You are not registered for this service!"
    }*/

    // public function updatestokobat($kd_obat, $qty)
    public function updatestokobat()
    {
        $userID = '1';
        $payload = json_encode([
            'KDOBT'   => '14250805250',
            'STOK'    => '100',
        ]);

        /* 
        $payload = json_encode([
            'KDOBAT'   => $kd_obat,
            'STOK'     => $qty,
        ]);
        */

        $endpoint = '/UpdateStokObat/updatestok';
        $result = $this->bpjsInsertService->request('POST', $endpoint, $payload, $userID);

        return $this->response->setJSON($result);
    }

    public function hapusobat($no_resep, $no_apotik, $kd_obat, $userID, $tipeobat)
    // public function hapusobatX()
    {
        // $userID     = '1';
        // $payload = json_encode([
        //     'nosepapotek'   => '0216A01603260000018',
        //     'noresep'       => '00003',
        //     'kodeobat'      => '14250800912',
        //     'tipeobat'      => 'N' //Jenis obat harus N atau R (khusus racik nama racikan harus sesuai ex:R.01)            
        // ]);

        $payload = json_encode([
            'nosepapotek'   => $no_apotik,
            'noresep'       => $no_resep,
            'kodeobat'      => $kd_obat,
            'tipeobat'      => $tipeobat
        ]);

        $endpoint = '/pelayanan/obat/hapus';
        $result = $this->bpjsInsertService->request('DELETE', $endpoint, $payload, $userID);
        return $this->response->setJSON($result);
    }

}