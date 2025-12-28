<?php

//defined('BASEPATH') OR exit('No direct script access allowed');
namespace App\Controllers;

use CodeIgniter\Controller;

class BridgingTES extends BaseController
{
    public function __construct()
    {
        require_once(APPPATH . '/../vendor/autoload.php');
        date_default_timezone_set("Asia/Jakarta");
        $this->db =  db_connect();
    }
    function index()
    {
        $this->load->model('main/vi_gettrustee');
    }
    public function tStamp()
    {
        $tStamp = strval(time() - strtotime('1970-01-01 07:00:00'));
        return $tStamp;
    }
    // public function url($request, $param, $method, $tStamp)
    // {
    //     $url = $this->db->query("SELECT nilai FROM seting_bpjs WHERE key_setting = 'url'")->row()->nilai;
    //     $headers = $this->getSignatureVedikaBaru($tStamp);
    //     $opts    = array(
    //         'http' => array(
    //             'method' => $method,
    //             'header' => $headers,
    //             'content' => $param
    //         ),
    //         "ssl" => array(
    //             "verify_peer" => false,
    //             "verify_peer_name" => false,
    //         )
    //     );
    //     $context = stream_context_create($opts);
    //     $string = json_decode(file_get_contents($url'/' . $request, true, $context));


    //     return $string;
    // }
    public function url($request, $param, $method, $tStamp)
    {
        $headers = $this->getSignatureVedikaBaru($tStamp);
        $opts = array(
            'http' => array(
                'method' => $method,
                'header' => $headers,
                'content' => $param
            ),
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            )
        );
        $context = stream_context_create($opts);
        $string = json_decode(file_get_contents('https://apijkn.bpjs-kesehatan.go.id/vclaim-rest/' . $request, true, $context));


        return $string;
    }
    public function getSignatureVedikaBaru($tStamp)
    {

        $data = "21780";
        $secretKey = "1hDF0B4057";
        $user_key = "6861b75624e5e2b742c3d270ec096b98";
        $signature = hash_hmac('sha256', $data . "&" . $tStamp, $secretKey, true);
        $encodedSignature = base64_encode($signature);
        return array("X-Cons-ID: " . $data, "X-Timestamp: " . $tStamp, "X-Signature: " . $encodedSignature, "user_key:" . $user_key, "Content-Type: application/x-www-form-urlencoded\r\n");
    }
    public function Decrypt($response, $tStamp)
    {
        $data = "21780";
        $secretKey = "1hDF0B4057";
        $user_key = "6861b75624e5e2b742c3d270ec096b98";

        $key = $data . $secretKey . $tStamp;
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($response), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil = json_decode(\LZCompressor\LZString::decompressFromEncodedURIComponent($output), true);
        return $hasil;
    }
    

    public function cekNik()
    {
        $input = json_decode(file_get_contents('php://input'));
        $hasil = array();
        $nik   = '3577010308920001';
        $tgl   = date("Y-m-d");
        $tStamp = $this->tStamp();
        $method = 'GET';
        $param = '';
        $request = 'Peserta/nik/' . $nik . '/tglSEP/' . $tgl;
        $string = $this->url($request, $param, $method, $tStamp);

        if ($string == null) {
            $hasil['status'] = 'sukses';
            $hasil['pesan']  = 'data nik kosong';
            $hasil['data']   = '';
            $hasil['code']   = 201;
            echo json_encode($hasil);
            return;
        }
        $response = $string->response;
        $hasil['code']   = 200;
        $resp = $this->Decrypt($response, $tStamp);
        $hasil['data']   = $this->Decrypt($response, $tStamp);
        $hasil['status'] = 'sukses';

        echo json_encode($hasil);
    }

    public function cekNik_pendaftaran()
    {
        $input = json_decode(file_get_contents('php://input'));
        $hasil = array();
        $nik   = $input->nik;
        $tgl   = date("Y-m-d");
        $tStamp = $this->tStamp();
        $method = 'GET';
        $param = '';
        $request = 'Peserta/nik/' . $nik . '/tglSEP/' . $tgl;
        $string = $this->url($request, $param, $method, $tStamp);

        if ($string->metaData->code != '200') {
            $hasil['status'] = 'sukses';
            $hasil['pesan']  = 'data nik kosong';
            $hasil['data']   = '';
            $hasil['code']   = 201;
            echo json_encode($hasil);
            return;
        }
        $response = $string->response;
        $hasil['code']   = 200;
        $resp = $this->Decrypt($response, $tStamp);
        $hasil['data']   = $resp['peserta']['noKartu'];
        $hasil['status'] = 'sukses';

        echo json_encode($hasil);
    }
    public function CariRujukanbyNomor($norujukan)
    {
        $input          = json_decode(file_get_contents('php://input'));
        //$norujukan      = "021600010823P000086"; //Nomor Rujukan
        $tStamp         = $this->tStamp();
        $method         = 'GET';
        $param          = '';
        $request        = '/Rujukan/' . $norujukan;
        $string         = $this->url($request, $param, $method, $tStamp);
        if ($string->metaData->code != 200) {
            $hasil = array();
            $hasil['status']    = 'gagal';
            $hasil['pesan']     = $string->metaData->message;
            $hasil['code']      = $string->metaData->code;
        } else {
            $hasil              = $this->Decrypt($string->response, $tStamp);
            $hasil['status']    = 'sukses';
            $hasil['pesan']     = $string->metaData->message;
            $hasil['code']      = $string->metaData->code;
        }

        echo json_encode($hasil);
    }
    public function CariRujukanIrja()
    {
        $input = json_decode(file_get_contents('php://input'));
        $noka   = $input->noka;
        $faskes = $input->faskes;
        if ($faskes == '2' || $faskes == '3') {
            $request = '/Rujukan/RS/List/Peserta/' . $noka;
        } else {
            $request = '/Rujukan/List/Peserta/' . $noka;
        }

        $tStamp = $this->tStamp();
        $method = 'GET';
        $param  = '';
        $string = $this->url($request, $param, $method, $tStamp);
        if ($string->metaData->code == 201) {
            $hasil = array();
            $hasil['status'] = 'sukses';
            $hasil['code']   = 201;
            $hasil['pesan'] = 'Rujukan Tidak Ada';
            $hasil['keterangan'] = $request;
            echo json_encode($hasil);
            return;
        }
        $response = $string->response;
        $hasil = $this->Decrypt($response, $tStamp);
        $hasil['status'] = 'sukses';
        $hasil['code']   = 200;
        echo json_encode($hasil);
    }

    public function CariDokterPostMrs()
    {
        $input = json_decode(file_get_contents('php://input'));
        $poli   = $input->poli;
        $tStamp = $this->tStamp();
        $hasil = array();
        $method = 'GET';
        $param  = '';
        $request = '/RencanaKontrol/JadwalPraktekDokter/JnsKontrol/2/KdPoli/' . $poli . '/TglRencanaKontrol/' . date("Y-m-d");
        $string = $this->url($request, $param, $method, $tStamp);
        if ($string->metaData->code == 201) {
            $hasil = array();
            $hasil['status'] = 'gagal';
            $hasil['pesan'] = $string->metaData->message;
            echo json_encode($hasil);
            return;
        } else {
            $sepirna = $this->db->query("SELECT no_sjp FROM transaksi inner join kunjungan using(id_transaksi) inner join penjamin_transaksi using(id_transaksi) where left(id_unit,1)='2' and no_sjp >'' and no_rm='$input->rm' and penjamin_utama='t' order by tgl_transaksi DESC limit 1 ")->getRow();
            if (empty($sepirna)) {
                $hasil['status'] = 'gagal';
                $hasil['pesan'] = 'Tidak ada SEP IRNA';
            } else {
                $response = $string->response;
                $hasil = $this->Decrypt($response, $tStamp);
                $hasil['status'] = 'sukses';
                $hasil['sepirna'] = $sepirna;
            }
        }
        echo json_encode($hasil);
    }
    public function CariDokter()
    {
        $input = json_decode(file_get_contents('php://input'));
        $poli   = $input->poli;
        $tStamp = $this->tStamp();
        $hasil = array();
        $method = 'GET';
        $param  = '';
        $request = '/RencanaKontrol/JadwalPraktekDokter/JnsKontrol/2/KdPoli/' . $poli . '/TglRencanaKontrol/' . date("Y-m-d");
        $string = $this->url($request, $param, $method, $tStamp);
        if ($string->metaData->code == 201) {
            $hasil = array();
            $hasil['status'] = 'gagal';
            $hasil['pesan'] = $string->metaData->message;
            echo json_encode($hasil);
            return;
        } else {
            $response = $string->response;
            $hasil = $this->Decrypt($response, $tStamp);
            $hasil['status'] = 'sukses';
            //$hasil['sepirna']= $sepirna;
        }
        echo json_encode($hasil);
    }
    public function CariDoktertes($poli)
    {
        $input = json_decode(file_get_contents('php://input'));
        //$poli   = $input->poli;
        $tStamp = $this->tStamp();
        $hasil = array();
        $method = 'GET';
        $param  = '';
        $request = '/RencanaKontrol/JadwalPraktekDokter/JnsKontrol/2/KdPoli/' . $poli . '/TglRencanaKontrol/' . date("Y-m-d");
        $string = $this->url($request, $param, $method, $tStamp);
        if ($string->metaData->code == 201) {
            $hasil = array();
            $hasil['status'] = 'gagal';
            $hasil['pesan'] = $string->metaData->message;
            echo json_encode($hasil);
            return;
        } else {
            $response = $string->response;
            $hasil = $this->Decrypt($response, $tStamp);
            $hasil['status'] = 'sukses';
            //$hasil['sepirna']= $sepirna;
        }
        echo json_encode($hasil);
    }
    //$sepirna = $this->db->query("SELECT no_sjp FROM transaksi inner join kunjungan using(id_transaksi) inner join penjamin_transaksi using(id_transaksi) where left(id_unit,1)='2' and no_sjp >'' and no_rm='$input->rm' order by tgl_transaksi DESC limit 1 ")->getRow()->no_sjp;
    //if ($sepirna=='') {
    //$hasil['status'] = 'gagal';
    //$hasil['pesan'] = 'Tidak ada SEP';
    //}

    public function CariDokterdischarge()
    {
        $input  = json_decode(file_get_contents('php://input'));
        $poli   = $input->poli;
        $tgl    = $input->tgl;
        $dpjp   = $input->dpjp;
        $unitbpjs = $this->db->query("SELECT map_bpjs from unit where id_unit='" . $poli . "' ")->getRow()->map_bpjs;
        $dpjpDokter = $this->db->query("SELECT kd_dokter_bpjs from pegawai where id_pegawai='" . $dpjp . "' ")->getRow()->kd_dokter_bpjs;
        $tStamp = $this->tStamp();
        $method = 'GET';
        $param  = '';
        $request = '/RencanaKontrol/JadwalPraktekDokter/JnsKontrol/2/KdPoli/' . $unitbpjs . '/TglRencanaKontrol/' . $tgl;
        $string = $this->url($request, $param, $method, $tStamp);
        if ($string->metaData->code == 201) {
            $hasil = array();
            $hasil['status'] = 'gagal';
            $hasil['pesan'] = 'Dokter Tidak Tersedia tanggal ' . $tgl . '';
            echo json_encode($hasil);
            return;
        }
        $response = $string->response;
        $hasil = $this->Decrypt($response, $tStamp);
        $hasil['status'] = 'sukses';
        echo json_encode($hasil);
    }
    public function caripolirujukan()
    {
        $input = json_decode(file_get_contents('php://input'));
        $poli   = $input->poli;
        $tStamp = $this->tStamp();
        $method = 'GET';
        $param  = '';
        $request = '/referensi/poli/' . $poli;
        $string = $this->url($request, $param, $method, $tStamp);
        if ($string->metaData->code == 201) {
            $hasil = array();
            $hasil['status'] = 'gagal';
            $hasil['pesan'] = 'error jaringan';
            echo json_encode($hasil);
            return;
        }
        $response = $string->response;
        $hasil = $this->Decrypt($response, $tStamp);
        $hasil['status'] = 'sukses';
        echo json_encode($hasil);
    }
    public function carirsrujukan()
    {
        $input = json_decode(file_get_contents('php://input'));
        $rs   = $input->rs;
        $klas = $input->klas;
        $tStamp = $this->tStamp();
        $method = 'GET';
        $param  = '';
        $request = '/referensi/faskes/' . $rs . '/' . $klas;
        $string = $this->url($request, $param, $method, $tStamp);
        if ($string->metaData->code == 201) {
            $hasil = array();
            $hasil['status'] = 'gagal';
            $hasil['pesan'] = 'error jaringan';
            echo json_encode($hasil);
            return;
        }
        $response = $string->response;
        $hasil = $this->Decrypt($response, $tStamp);
        $hasil['status'] = 'sukses';
        echo json_encode($hasil);
    }

    public function CariSep()
    {
        $input    = json_decode(file_get_contents('php://input'));
        $tgl_mulai = date('Y-m-d', strtotime(' - 2 months'));
        $tStamp = $this->tStamp();
        $method = 'GET';
        $param  = '';
        $request = '/monitoring/HistoriPelayanan/NoKartu/' . $input->noka . '/tglMulai/' . $tgl_mulai . '/tglAkhir/' . date("Y-m-d");

        $string = $this->url($request, $param, $method, $tStamp);
        if ($string->metaData->code != 200) {
            $hasil = array();
            $hasil['status']    = 'sukses';
            $hasil['code']      = 201;
            $hasil['pesan']     = $string->metaData->message;
        } else {
            $hasil    = $this->Decrypt($string->response, $tStamp);;
            $hasil['status']    = 'sukses';
            $hasil['code']      = 200;
            //$hasil['pesan']     = 'Berhasil';
        }
        echo json_encode($hasil);
    }

    public function CariSepPostMrs($noka)
    {
        $input    = json_decode(file_get_contents('php://input'));
        $tgl_mulai = date('Y-m-d', strtotime(' - 2 months'));
        $tStamp = $this->tStamp();
        $method = 'GET';
        $param  = '';
        $request = '/monitoring/HistoriPelayanan/NoKartu/' . $noka . '/tglMulai/' . $tgl_mulai . '/tglAkhir/' . date("Y-m-d");

        $string = $this->url($request, $param, $method, $tStamp);
        if ($string->metaData->code != 200) {
            $hasil = array();
            $hasil['status']    = 'sukses';
            $hasil['code']      = 201;
            $hasil['pesan']     = $string->metaData->message;
            echo json_encode($hasil);
            return;
        } else {
            $hasil    = $this->Decrypt($string->response, $tStamp);
            $data     = $hasil['histori']['2']['noSep'];
            return $data;
            // $hasil['data']    = $this->Decrypt($string->response, $tStamp);
            // $data     = $hasil['histori']['2']['noSep'];
            // echo json_encode($hasil);
            // return ;
        }
    }

    public function HistoriSepIrja()
    {
        $input    = json_decode(file_get_contents('php://input'));
        $sep   = $this->db->query("SELECT no_sjp from ")->row();
    }
    public function buatSEPIGD()
    {
        $input = json_decode(file_get_contents('php://input'));
        $url = $this->db->query("SELECT nilai FROM seting_bpjs WHERE key_setting = 'url_2'")->row()->nilai;
        $ppk = $this->db->query("SELECT nilai FROM seting_bpjs WHERE key_setting = 'ppk_2'")->row()->nilai;
        $tStamp = $this->tStamp();
        $headers = $this->getSignatureVedikaBaru($tStamp);

        $tglSEP     = date("Y-m-d");
        $tglKejadian = $input->tglKejadian;

        $param = '{
            "request":{
             "t_sep":{
              "noKartu":"' . $input->noKartu . '",
              "tglSep":"' . $tglSEP . '",
              "ppkPelayanan":"' . $ppk . '",
              "jnsPelayanan":"2",
              "klsRawat":{
               "klsRawatHak":"",
               "klsRawatNaik":"",
               "pembiayaan":"",
               "penanggungJawab":""
               },
               "noMR":"' . $input->noMR . '",
               "rujukan":{
                   "asalRujukan":"2",
                   "tglRujukan":"",
                   "noRujukan":"",
                   "ppkRujukan":""
                   },
                   "catatan":"' . $input->catatan . '",
                   "diagAwal":"' . $input->diagAwal . '",
                   "poli":{
                       "tujuan":"IGD",
                       "eksekutif":"0"
                       },
                       "cob":{
                           "cob":"' . $input->cob . '"
                           },
                           "katarak":{
                               "katarak":"' . $input->katarak . '"
                               },
                               "jaminan":{
                                   "lakaLantas":"' . $input->lakaLantas . '",
                                   "penjamin":{
                                    "tglKejadian":"' . $tglKejadian . '",
                                    "keterangan":"",
                                    "suplesi":{
                                     "suplesi":"' . $input->suplesi . '",
                                     "noSepSuplesi":"' . $input->no_suplesi . '",
                                     "lokasiLaka":{
                                      "kdPropinsi":"' . $input->kdPropinsi . '",
                                      "kdKabupaten":"' . $input->kdKabupaten . '",
                                      "kdKecamatan":"' . $input->kdKecamatan . '"
                                  }
                              }
                          }
                          },
                          "tujuanKunj":"0",
                          "flagProcedure":"",
                          "kdPenunjang":"",
                          "assesmentPel":"",
                          "skdp":{
                           "noSurat":"",
                           "kodeDPJP":""
                           },
                           "dpjpLayan":"' . $input->kodeDPJP . '",
                           "noTelp":"' . $input->noTelp . '",
                           "user":"' . $input->user . '"
                       }
                   }
               }';

        $opts = array(
            'http' => array(
                'method' => "POST",
                'header' => $headers,
                'content' => $param
            )
        );

        $context = stream_context_create($opts);

        $hasil_raw = file_get_contents($url . 'SEP/2.0/insert', false, $context);

        $hasil = json_decode($hasil_raw, true);

        $output = array();
        $output['status'] = 'gagal';
        $output['metaData'] = $hasil['metaData'];
        if ($hasil['metaData']['code'] <> '200') {
            $output['pesan'] = $hasil['metaData']['message'];
            echo json_encode($output);
            return;
        }

        $response = $this->Decrypt($hasil['response'], $tStamp);
        $output['status'] = 'sukses';
        $output['pesan'] = '';
        $output['data'] =  $response;
        echo json_encode($output);
    }
    public function cariUnit()
    {
        $input    = json_decode(file_get_contents('php://input'));
        $output['data'] = $this->db->query("SELECT map_bpjs from unit where id_unit='$input->id' ")->getRow()->map_bpjs;
        $output['status'] = 'sukses';
        $output['pesan'] = '';
        echo json_encode($output);
    }
    public function CariDetailPesertaBPJS()
    {
        $input    = json_decode(file_get_contents('php://input'));
        $noka     = $input->noka;
        $tgl_mulai = date('Y-m-d', strtotime(' - 2 months'));
        $tStamp = $this->tStamp();
        $method = 'GET';
        $param  = '';
        $request = '/Peserta/nokartu/' . $noka . '/tglSEP/' . date("Y-m-d");
        $string = $this->url($request, $param, $method, $tStamp);
        if ($string == null) {
            $hasil = array();
            $hasil['status'] = 'gagal';
            $hasil['pesan'] = 'error jaringan';
            echo json_encode($hasil);
            return;
        }
        $response = $string->response;
        $hasil = $this->Decrypt($response, $tStamp);
        $hasil['status'] = 'sukses';
        echo json_encode($hasil);
    }


    public function JsonDeleteRencanaKontrol($id, $user)
    {
        $json = ' {
                "request": {
                    "t_suratkontrol":{
                        "noSuratKontrol": "' . $id . '",
                        "user": "' . $user . '"
                    }
                }
            }
            ';
        return $json;
    }
    public function DeleteRencanaKontrol()
    {

        $input      = json_decode(file_get_contents('php://input'));
        $skdp       = $input->skdp;
        $user       = $input->user;
        $tStamp     = $this->tStamp();
        $tgl        = date('Y-m-d');
        $method     = 'DELETE';
        $param      = $this->JsonDeleteRencanaKontrol($skdp, $user);
        $request    = '/RencanaKontrol/Delete';
        $string     = $this->url($request, $param, $method, $tStamp);
        if ($string->metaData->code != 200) {
            $hasil = array();
            $hasil['status'] = 'gagal';
            $hasil['pesan'] = $string->metaData->message;
            echo json_encode($hasil);
            return;
        }
        $response = $string->response;
        $hasil['data']   = $this->Decrypt($response, $tStamp);
        $hasil['pesan']  = 'Berhasil';
        $hasil['status'] = 'sukses';
        echo json_encode($hasil);
    }
    public function JsonCreateRencanaKontrol($skdp, $dokter, $kdunit, $tgl)
    {
        if ($kdunit == 'HDL') {
            $unit = 'INT';
        } else {
            $unit = $kdunit;
        }

        $json = '      {
                "request": {
                    "noSEP":"' . $skdp . '",
                    "kodeDokter":"' . $dokter . '",
                    "poliKontrol":"' . $unit . '",
                    "tglRencanaKontrol":"' . $tgl . '",
                    "user":"coba ws"
                }
            }

            ';
        return $json;
    }

    /*end manipulasi bedging bpjs*/
    public function CreateRencanaKontrolPostMrs()
    {
        $input      = json_decode(file_get_contents('php://input'));
        $tStamp     = $this->tStamp();
        $idkunjungan = $input->id_kunjungan;
        $idtransaksi = $input->id_transaksi;
        $unit        = $input->id_unit;
        $tgl         = $input->tgl_kontrol;
        $dokter      = $input->dpjp;
        $dpjp        = $this->db->query("select kd_dokter_bpjs from pegawai where id_pegawai='" . $dokter . "' ")->getRow()->kd_dokter_bpjs;
        $sepasal    = $this->db->query("select no_sjp from penjamin_transaksi where id_transaksi='" . $idtransaksi . "' and penjamin_utama=true ")->getRow()->no_sjp;
        if (empty($sepasal)) {
            $hasil['pesan']  = 'Berhasil';
            $hasil['status'] = 'sukses';
             $cekrencanakontrol = $this->db->query("SELECT * FROM rencana_kontrol WHERE id_kunjungan='$idkunjungan' limit 1")->getRow();
            if ($cekrencanakontrol) {
                $this->db->query("UPDATE rencana_kontrol SET id_unit='" . $unit . "',tgl_kunjungan='" . $tgl . "'  WHERE id_kunjungan='$idkunjungan'  ");
                echo json_encode($hasil);
                return;
            }else {
                $this->db->query(" INSERT INTO rencana_kontrol(id_transaksi,
                    id_kunjungan,
                    tgl_kunjungan,
                    id_unit
                ) VALUES('" . $idtransaksi . "', '" . $idkunjungan . "', '" . $tgl . "', '" . $unit . "')  ");
                echo json_encode($hasil);
                return;
            }
        } else {

            $methodsep   = 'GET';
            $paramsep    = '';
            $requestsep  = '/SEP/' . $sepasal;
            $stringsep   = $this->url($requestsep, $paramsep, $methodsep, $tStamp);
            $responsesep = $stringsep->response;
            $hasilsep    = $this->Decrypt($responsesep, $tStamp);
            $dokter      = $hasilsep['dpjp']['kdDPJP'];
            $norujukan   = $hasilsep['noRujukan'];
            $tgl         = $tgl;
            //$dokter     = $this->db->query("select kd_dokter_bpjs from pegawai where id_pegawai='".$input->dpjp."' ")->getRow()->kd_dokter_bpjs;
            $unit       = $this->db->query("select map_bpjs from unit where id_unit='" . $unit . "' ")->getRow()->map_bpjs;
            $method     = 'POST';
            $param      = $this->JsonCreateRencanaKontrol($sepasal, $dokter, $unit, $tgl);
            $request    = '/RencanaKontrol/insert';
            $string     = $this->url($request, $param, $method, $tStamp);
            if ($string->metaData->code != 200) {
                $hasil = array();
                $hasil['status'] = 'gagal';
                $hasil['pesan'] = $string->metaData->message;
                echo json_encode($hasil);
                return;
            }
            $response = $string->response;
            $data            = $this->Decrypt($response, $tStamp);
            $skdp            = $data['noSuratKontrol'];
            $noka            = $data['noKartu'];
            $hasil['data']   = $data;
            $hasil['pesan']  = 'Berhasil';
            $hasil['status'] = 'sukses';
            $cekrencanakontrol=$this->db->query("SELECT * FROM rencana_kontrol WHERE id_kunjungan='$idkunjungan' limit 1")->getRow();
            if ($cekrencanakontrol) {
                $this->db->query("UPDATE rencana_kontrol SET id_unit='".$unit."',tgl_kunjungan='".$tgl."'  WHERE id_kunjungan='$idkunjungan'  ");
                echo json_encode($hasil);return;
            } else {
                $this->db->query(" INSERT INTO rencana_kontrol(id_transaksi,
                    id_kunjungan,
                    tgl_kunjungan,
                    id_unit,
                    skdp,
                    noka,
                    sep_awal,
                    no_rujukan
                ) VALUES('" . $idtransaksi . "','" . $idkunjungan . "','" . $tgl . "','" . $unit . "','" . $skdp . "','" . $noka . "','" . $sepasal . "','" . $norujukan . "')  ");
            }
            echo json_encode($hasil);return;
        }
    }
    // update tgl 19 02 25 ->cek rencana kontrol
    public function CekRencanaKontrol($sep)
    {
        $input = json_decode(file_get_contents('php://input'));
        $tStamp = $this->tStamp();
        $method = 'GET';
        $param = '';
        $request = '/RencanaKontrol/noSuratKontrol/' . $sep;
        $string = $this->url($request, $param, $method, $tStamp);

        if ($string->metaData->code != 200) {
            $hasil = [
                'status' => 'gagal',
                'code' => 201,
                'pesan' => $string->metaData->message
            ];
            return $hasil; // Kembalikan hasil sebagai array
        }

        $response = $string->response;
        $rencana_kontrol = $this->Decrypt($response, $tStamp);

        $hasil = [
            'code'      => 200,
            'status'    => 'sukses',
            'tgl_kunjungan' => $rencana_kontrol->tglRencanaKontrol,
            'skdp'      => $rencana_kontrol->noSuratKontrol
        ];
        return $hasil; // Kembalikan hasil sebagai array
    }

    public function CekRencanaKontrolfrompenata()
    {
        $input = json_decode(file_get_contents('php://input'));
        $data  = $this->db->query("SELECT * FROM rencana_kontrol WHERE id_kunjungan='$input->id_kunjungan' limit 1")->getRow();
        if ($data) {
            $hasil['data']   = $data;
            $hasil['pesan']  = 'Berhasil';
            $hasil['status'] = 'sukses';
            echo json_encode($hasil);
        } else {
            $hasil['pesan']  = 'gagal';
            $hasil['pesan']  = 'Rencana kontrol belum dibuatkan !';
            $hasil['status'] = 'gagal';
            echo json_encode($hasil);
        }
    }

    public function CreateRencanaKontrol()
    {

        $input      = json_decode(file_get_contents('php://input'));
        $tStamp     = $this->tStamp();
        $sepakhir   = $input->skdp;
        $dokter     = $input->dokter;
        $unitrs     = $input->unitrs;
        $unitbpjs   = $input->unitbpjs;
        $noka       = $input->noka;
        $no_rujukan = $input->no_rujukan;
        $tgl        = date('Y-m-d');
        //$cekskdp    = $this->db->query("select * from rencana_kontrol where noka='".$noka."' and aktif is true and no_rujukan='".$no_rujukan."' and id_unit='".$unitrs."' order by tgl_kunjungan limit 1")->getRow();
        $cekskdp1   = $this->CekRencanaKontrol($sepakhir);
        if (!is_array($cekskdp1) || !isset($cekskdp1['code'])) {
            echo json_encode(['status' => 'gagal', 'pesan' => 'Gagal memeriksa rencana kontrol']);
            return;
        }
        if ($cekskdp1['code'] == 200) {

            if ($cekskdp1['tgl_kunjungan'] == $tgl) {

                $hasil['data']   = $cekskdp1['skdp'];
                $hasil['pesan']  = 'Berhasil';
                $hasil['status'] = 'sukses';
                echo json_encode($hasil);
            } else {

                $skdp       = $cekskdp1['skdp'];
                $method     = 'PUT';
                $param      = $this->JsonUpdateRencanaKontrol($skdp, $sepakhir, $dokter, $unitbpjs, $tgl);
                $request    = '/RencanaKontrol/Update';
                $string     = $this->url($request, $param, $method, $tStamp);
                $response = $string->response;
                $data     = $this->Decrypt($response, $tStamp);
                $hasil['data']   = $data->noSuratKontrol;
                $hasil['pesan']  = 'Berhasil';
                $hasil['status'] = 'sukses';
                echo json_encode($hasil);
            }
        } else {

            $method     = 'POST';
            $param      = $this->JsonCreateRencanaKontrol($sepakhir, $dokter, $unitbpjs, $tgl);
            $request    = '/RencanaKontrol/insert';
            $string     = $this->url($request, $param, $method, $tStamp);

            if ($string->metaData->code != 200) {
                $hasil = array();
                $hasil['status'] = 'gagal';
                $hasil['pesan'] = $string->metaData->message;
                echo json_encode($hasil);
                return;
            } else {
                $response = $string->response;
                $data     = $this->Decrypt($response, $tStamp);
                $hasil['data']   = $data->noSuratKontrol;
                $hasil['pesan']  = 'Berhasil';
                $hasil['status'] = 'sukses';
                echo json_encode($hasil);
            }
        }
    }

    public function CreateRencanaKontrolirna()
    {
        $input      = json_decode(file_get_contents('php://input'));
        $tStamp     = $this->tStamp();
        $sepakhir   = $input->skdp;
        $dokter     = $input->dokter;
        $unitrs     = $input->unitrs;
        $unitbpjs   = $input->unitbpjs;
        $noka       = $input->noka;
        $no_rujukan = $input->no_rujukan;
        $tgl        = date('Y-m-d');
        $cekskdp    = $this->db->query("select * from rencana_kontrol where noka='" . $noka . "' and aktif is true and no_rujukan='" . $no_rujukan . "' and id_unit='" . $unitrs . "' order by tgl_kunjungan limit 1")->getRow();

        if ($cekskdp > '') {

            if ($cekskdp->tgl_kunjungan == $tgl) {

                $hasil['data']   = $cekskdp->skdp;
                $hasil['pesan']  = 'Berhasil';
                $hasil['status'] = 'sukses';
                echo json_encode($hasil);
            } else {

                $skdp       = $cekskdp->skdp;
                $method     = 'PUT';
                $param      = $this->JsonUpdateRencanaKontrol($skdp, $sepakhir, $dokter, $unitbpjs, $tgl);
                $request    = '/RencanaKontrol/Update';
                $string     = $this->url($request, $param, $method, $tStamp);
                $response = $string->response;
                $hasil['data']   = $this->Decrypt($response, $tStamp);
                $hasil['pesan']  = 'Berhasil';
                $hasil['status'] = 'sukses';
                echo json_encode($hasil);
            }
        } else {

            $method     = 'POST';
            $param      = $this->JsonCreateRencanaKontrol($sepakhir, $dokter, $unitbpjs, $tgl);
            $request    = '/RencanaKontrol/insert';
            $string     = $this->url($request, $param, $method, $tStamp);

            if ($string->metaData->code != 200) {
                $hasil = array();
                $hasil['status'] = 'gagal';
                $hasil['pesan'] = $string->metaData->message;
                echo json_encode($hasil);
                return;
            } else {
                $response = $string->response;
                $hasil['data']   = $this->Decrypt($response, $tStamp);
                $hasil['pesan']  = 'Berhasil';
                $hasil['status'] = 'sukses';
                echo json_encode($hasil);
            }
        }
    }
    public function CreateRencanaKontrolbyRM()
    {

        $input      = json_decode(file_get_contents('php://input'));
        $tStamp     = $this->tStamp();
        $hasil = array();
        $id_transaksi = $input->id_transaksi;
        $id_kunjungan = $input->id_kunjungan;
        $no_rm        = $input->no_rm;
        $cekinap      = $this->db->query("WITH transaksi_unik AS (
                                SELECT 
                                    id_transaksi,
                                    no_sjp,
                                    LEFT(id_unit, 1) AS id_unit,
                                    tgl_transaksi
                                FROM transaksi
                                INNER JOIN penjamin_transaksi USING (id_transaksi)
                                INNER JOIN kunjungan USING (id_transaksi)
                                WHERE
                                    no_rm = '" . $no_rm . "'
                                    AND penjamin_utama = 't'
                                    AND LEFT(id_unit, 1) IN ('1', '2')
                                    AND id_penjamin = '2'
                            ),
                            transaksi_bernomor AS (
                                SELECT 
                                    id_transaksi,
                                    no_sjp,
                                    id_unit,
                                    tgl_transaksi
                                FROM transaksi_unik
                            ),
                            hasil_group AS (
                                SELECT 
                                    no_sjp,
                                    MIN(tgl_transaksi) AS tgl_transaksi_pertama,
                                    ARRAY_AGG(id_transaksi) AS daftar_transaksi,
                                    ARRAY_AGG(id_unit) AS daftar_unit,
                                    CASE 
                                        WHEN '2' = ANY(ARRAY_AGG(id_unit)) THEN 'IRNA'
                                        ELSE 'IRJ'
                                    END AS kategori
                                FROM transaksi_bernomor
                                GROUP BY no_sjp
                            )
                            SELECT 
                                ROW_NUMBER() OVER (ORDER BY tgl_transaksi_pertama DESC) AS no_urut,
                                *
                            FROM hasil_group ")->getResult();
        if (empty($cekinap)) {
            $inputrencanakontrol = $this->db->query(" INSERT INTO rencana_kontrol(id_transaksi,
                            id_kunjungan,tgl_kunjungan, id_unit,dpjp) VALUES('$input->id_transaksi', '$input->id_kunjungan', '$input->tgl', '$input->unit', '$input->dpjp')  ");
            $hasil['status'] = 'gagal';
            $hasil['pesan']  = 'Pasien bukan penjamin BPJS';
            echo json_encode($hasil);
            return;
        } else {
            if (count($cekinap) >= 3) {
                $dataKedua = $cekinap[1]; // Data urutan ke-2 (index ke-1)
                $unitArray = explode(',', trim($dataKedua->daftar_unit, '{}'));
                if (in_array('2', $unitArray)) {
                    // Jika unit kedua adalah rawat inap, ambil data ketiga
                    $dataKetiga = $cekinap[2] ?? null;
                    if ($dataKetiga) {
                        $nokapasien = $this->db->query("SELECT no_kartu FROM penjamin_pasien where no_rm='" . $no_rm . "' and id_penjamin=2 limit 1")->getRow()->no_kartu;
                        //$sepasal = $dataKetiga->no_sjp;
                        $sepasal = $this->CariSepPostMrs($nokapasien);
                        //echo json_encode($sepasal);return;
                    } else {
                        $hasil['status'] = 'gagal';
                        $hasil['pesan']  = 'Pasien tidak ada kunjungan IRJA sebelumnya, silahkan cari rujukan';
                        echo json_encode($hasil);
                        return;
                    }
                } else {
                    $ceksepasal    = $this->db->query("select no_sjp from penjamin_transaksi where id_transaksi='" . $id_transaksi . "' ")->getRow();
                    if ($ceksepasal && !empty($ceksepasal->no_sjp)) {
                        $sepasal = $ceksepasal->no_sjp;
                    } else {
                        $queryskdp=" INSERT INTO rencana_kontrol(id_transaksi,
                                                id_kunjungan,tgl_kunjungan, id_unit,dpjp) 
                                                VALUES('$input->id_transaksi', '$input->id_kunjungan', '$input->tgl', '$input->unit', '$input->dpjp')  ";
                        $inputrencanakontrol = $this->db->simpleQuery($queryskdp);
                            $hasil['status'] = 'gagal';
                            $hasil['pesan']  = 'Pasien bukan penjamin BPJS';
                            echo json_encode($hasil);
                            return;
                    }
                }
            } else {
                $ceksepasal    = $this->db->query("select no_sjp from penjamin_transaksi where id_transaksi='" . $id_transaksi . "' ")->getRow();
                if ($ceksepasal && !empty($ceksepasal->no_sjp)) {
                    $sepasal = $ceksepasal->no_sjp;
                } else {
                    $cekskdp= $this->db->query("SELECT * FROM rencana_kontrol WHERE id_kunjungan='" . $id_kunjungan . "' and tgl_kunjungan='$input->tgl' and id_unit='$input->unit' and dpjp='$input->dpjp' limit 1 ")->getRow();
                    if (!is_null($cekskdp)) {
                        $queryskdp=" UPDATE rencana_kontrol SET id_transaksi='$input->id_transaksi', 
                                                tgl_kunjungan='$input->tgl', dpjp='$input->dpjp' 
                                                WHERE id_kunjungan='$input->id_kunjungan' and tanggal='$input->tgl' and id_unit='$input->unit' ";
                        $hasil['pesan']  = 'Berhasil update rencana kontrol';
                    } else {
                        $queryskdp=" INSERT INTO rencana_kontrol(id_transaksi,
                                                id_kunjungan,tgl_kunjungan, id_unit,dpjp,penjamin) 
                                                VALUES('$input->id_transaksi', '$input->id_kunjungan', '$input->tgl', '$input->unit', '$input->dpjp',1)  ";
                        $hasil['pesan']  = 'Berhasil simpan rencana kontrol';
                    }   
                    $inputrencanakontrol = $this->db->simpleQuery($queryskdp);
                    $hasil['status'] = 'sukses';
                    
                    echo json_encode($hasil);
                    return;
                }
            }
        }
        // pembuatan skdp
        $cekrencanakontrol = $this->db->query("SELECT * FROM rencana_kontrol WHERE id_kunjungan='" . $id_kunjungan . "' limit 1 ")->getRow();
        if (!is_null($cekrencanakontrol)) {
            if ($cekrencanakontrol->skdp == '' || $cekrencanakontrol->skdp == null) {
                $deleterecordskdp = $this->db->simpleQuery("DELETE FROM rencana_kontrol WHERE id_kunjungan='" . $id_kunjungan . "'");
                if ($deleterecordskdp) {
                    $methodsep   = 'GET';
                    $paramsep    = '';
                    $requestsep  = '/SEP/' . $sepasal;
                    $stringsep   = $this->url($requestsep, $paramsep, $methodsep, $tStamp);
                    $responsesep = $stringsep->response;
                    $hasilsep    = $this->Decrypt($responsesep, $tStamp);
                    $dokter      = $hasilsep['dpjp']['kdDPJP'];
                    $norujukan   = $hasilsep['noRujukan'];
                    $tgl         = $input->tgl;
                    $unit       = $this->db->query("select map_bpjs from unit where id_unit='" . $input->unit . "' ")->getRow()->map_bpjs;
                    $method     = 'POST';
                    $param      = $this->JsonCreateRencanaKontrol($sepasal, $dokter, $unit, $tgl);
                    $request    = '/RencanaKontrol/insert';
                    $string     = $this->url($request, $param, $method, $tStamp);
                    if ($string->metaData->code != 200) {
                        $inputrencanakontrol = $this->db->query(" INSERT INTO rencana_kontrol(id_transaksi,
                                id_kunjungan,tgl_kunjungan, id_unit,dpjp) VALUES('$input->id_transaksi', '$input->id_kunjungan', '$input->tgl', '$input->unit', '$input->dpjp')  ");
                        if ($inputrencanakontrol) {
                            //$hasil = array();
                            $hasil['status'] = 'gagal';
                            $hasil['pesan'] = $string->metaData->message;
                            echo json_encode($hasil);
                            return;
                        } else {
                            //$hasil = array();
                            $hasil['status'] = 'gagal';
                            $hasil['pesan'] = $string->metaData->message;
                            echo json_encode($hasil);
                            return;
                        }
                    }
                    $response = $string->response;
                    $data            = $this->Decrypt($response, $tStamp);
                    $skdp            = $data['noSuratKontrol'];
                    $noka            = $data['noKartu'];
                    $this->db->query(" INSERT INTO rencana_kontrol(id_transaksi,
                                id_kunjungan,tgl_kunjungan,id_unit,skdp,noka,sep_awal,no_rujukan,dpjp) VALUES('$input->id_transaksi', '$input->id_kunjungan','$input->tgl','$input->unit','" . $skdp . "','" . $noka . "','" . $sepasal . "','" . $norujukan . "','$input->dpjp')  ");
                    $hasil['data']   = $data;
                    $hasil['pesan']  = 'Berhasil';
                    $hasil['status'] = 'sukses';
                    echo json_encode($hasil);
                } else {
                    $hasil['status'] = 'gagal';
                    $hasil['pesan']  = 'Gagal hapus rencana kontrol, sesuaika data inputan';
                    echo json_encode($hasil);
                    return;
                }
            } else {
                # code...
                $tgl        = $input->tgl;
                $skdp       = $cekrencanakontrol->skdp;
                $sep        = $cekrencanakontrol->sep_awal;
                $dokter     = $this->db->query("select kd_dokter_bpjs from pegawai where id_pegawai='" . $input->dpjp . "' ")->getRow()->kd_dokter_bpjs;
                $unit       = $this->db->query("select map_bpjs from unit where id_unit='" . $input->unit . "' ")->getRow()->map_bpjs;
                $method     = 'PUT';
                $param      = $this->JsonUpdateRencanaKontrol($skdp, $sep, $dokter, $unit, $tgl);
                $request    = '/RencanaKontrol/Update';
                $string     = $this->url($request, $param, $method, $tStamp);
                if ($string->metaData->code != 200) {
                    $hasil['status'] = 'gagal';
                    $hasil['pesan'] = $string->metaData->message . ' sep asal:' . $sep;
                    echo json_encode($hasil);
                    return;
                } else {
                    $updateskdp = $this->db->query("UPDATE rencana_kontrol set tgl_kunjungan='$input->tgl' where id_kunjungan='$input->id_kunjungan' ");
                    if ($updateskdp) {
                        # code...
                        $hasil['pesan']  = 'Berhasil';
                        $hasil['status'] = 'sukses';
                        echo json_encode($hasil);
                        return;
                    } else {
                        $hasil['status'] = 'gagal';
                        $hasil['pesan'] = 'gagal update skdp, cek kembali inputan anda';
                        echo json_encode($hasil);
                        return;
                    }
                }
            }
        } else {
            $methodsep   = 'GET';
            $paramsep    = '';
            $requestsep  = '/SEP/' . $sepasal;
            $stringsep   = $this->url($requestsep, $paramsep, $methodsep, $tStamp);
            if ($stringsep->metaData->code == 200) {
                $responsesep = $stringsep->response;
                $hasilsep    = $this->Decrypt($responsesep, $tStamp);
                $dokter      = $hasilsep['dpjp']['kdDPJP'];
                $norujukan   = $hasilsep['noRujukan'];
                $tgl         = $input->tgl;
                $unit       = $this->db->query("select map_bpjs from unit where id_unit='" . $input->unit . "' ")->getRow()->map_bpjs;
                $method     = 'POST';
                $param      = $this->JsonCreateRencanaKontrol($sepasal, $dokter, $unit, $tgl);
                $request    = '/RencanaKontrol/insert';
                $string     = $this->url($request, $param, $method, $tStamp);
                if ($string->metaData->code != 200) {
                    $inputrencanakontrol = $this->db->query(" INSERT INTO rencana_kontrol(id_transaksi,
                        id_kunjungan,tgl_kunjungan, id_unit,dpjp) VALUES('$input->id_transaksi', '$input->id_kunjungan', '$input->tgl', '$input->unit', '$input->dpjp')  ");
                    if ($inputrencanakontrol) {
                        //$hasil = array();
                        $hasil['status'] = 'gagal';
                        $hasil['pesan'] = $string->metaData->message . ',' . $sepasal;
                        echo json_encode($hasil);
                        return;
                    } else {
                        //$hasil = array();
                        $hasil['status'] = 'gagal';
                        $hasil['pesan'] = $string->metaData->message . ',' . $sepasal;
                        echo json_encode($hasil);
                        return;
                    }
                } else {
                    $response = $string->response;
                    $data            = $this->Decrypt($response, $tStamp);
                    $skdp            = $data['noSuratKontrol'];
                    $noka            = $data['noKartu'];
                    $this->db->query(" INSERT INTO rencana_kontrol(id_transaksi,
                            id_kunjungan,tgl_kunjungan,id_unit,skdp,noka,sep_awal,no_rujukan,dpjp) VALUES('$input->id_transaksi', '$input->id_kunjungan','$input->tgl','$input->unit','" . $skdp . "','" . $noka . "','" . $sepasal . "','" . $norujukan . "','$input->dpjp')  ");
                    $hasil['data']   = $data;
                    $hasil['pesan']  = 'Berhasil';
                    $hasil['status'] = 'sukses';
                    echo json_encode($hasil);
                }
            } else {
                $hasil['status'] = 'gagal';
                $hasil['pesan'] = $stringsep->metaData->message . ' sep asal :' . $sepasal;
                echo json_encode($hasil);
                return;
            }
        }
    }
    public function CreateRencanaKontrolbyRMdev()
    {

        $input      = json_decode(file_get_contents('php://input'));
        $tStamp     = $this->tStamp();
        $id_transaksi = $input->id_transaksi;
        $id_kunjungan = $input->id_kunjungan;
        $sepasal    = $this->db->query("select no_sjp,id_penjamin from penjamin_transaksi where id_transaksi='" . $id_transaksi . "' ")->getRow()->no_sjp;
        if ($sepasal > '' || !empty($sepasal)) {
            # code...
            $cekrencanakontrol = $this->db->query("SELECT * FROM rencana_kontrol WHERE id_kunjungan='" . $id_kunjungan . "' and id_unit='$input->unit' ")->getRow();
            if ($cekrencanakontrol > '' && $sepasal == '') {
                $hasil['status'] = 'gagal';
                $hasil['pesan'] = 'Rencana Kontrol Sudah Ada';
                echo json_encode($hasil);
                return;
            } else {
                $methodsep   = 'GET';
                $paramsep    = '';
                $requestsep  = '/SEP/' . $sepasal;
                $stringsep   = $this->url($requestsep, $paramsep, $methodsep, $tStamp);
                $responsesep = $stringsep->response;
                $hasilsep    = $this->Decrypt($responsesep, $tStamp);
                $dokter      = $hasilsep['dpjp']['kdDPJP'];
                $norujukan   = $hasilsep['noRujukan'];
                $tgl         = $input->tgl;
                //$dokter     = $this->db->query("select kd_dokter_bpjs from pegawai where id_pegawai='".$input->dpjp."' ")->getRow()->kd_dokter_bpjs;
                $unit       = $this->db->query("select map_bpjs from unit where id_unit='" . $input->unit . "' ")->getRow()->map_bpjs;
                $method     = 'POST';
                $param      = $this->JsonCreateRencanaKontrol($sepasal, $dokter, $unit, $tgl);
                $request    = '/RencanaKontrol/insert';
                $string     = $this->url($request, $param, $method, $tStamp);
                if ($string->metaData->code != 200) {
                    if ($cekrencanakontrol > '') {
                        $inputrencanakontrol = $this->db->query(" UPDATE rencana_kontrol SET 
                        tgl_kunjungan='$input->tgl' WHERE id_transaksi=$input->id_transaksi' and id_kunjungan='$input->id_kunjungan'");
                        if ($inputrencanakontrol) {
                            $hasil = array();
                            $hasil['status'] = 'gagal';
                            $hasil['pesan'] = $string->metaData->message;
                            echo json_encode($hasil);
                            return;
                        } else {
                            $hasil = array();
                            $hasil['status'] = 'gagal';
                            $hasil['pesan'] = $string->metaData->message;
                            echo json_encode($hasil);
                            return;
                        }
                    } else {
                        $inputrencanakontrol = $this->db->query(" INSERT INTO rencana_kontrol(id_transaksi,
                            id_kunjungan,
                            tgl_kunjungan,
                            id_unit,
                            dpjp
                        ) VALUES('$input->id_transaksi', '$input->id_kunjungan', '$input->tgl', '$input->unit', '$input->dpjp')  ");
                        if ($inputrencanakontrol) {
                            $hasil = array();
                            $hasil['status'] = 'gagal';
                            $hasil['pesan'] = $string->metaData->message;
                            echo json_encode($hasil);
                            return;
                        } else {
                            $hasil = array();
                            $hasil['status'] = 'gagal';
                            $hasil['pesan'] = $string->metaData->message;
                            echo json_encode($hasil);
                            return;
                        }
                    }
                }
                $response = $string->response;
                $data            = $this->Decrypt($response, $tStamp);
                $skdp            = $data['noSuratKontrol'];
                $noka            = $data['noKartu'];
                $hasil['data']   = $data;
                $hasil['pesan']  = 'Berhasil';
                $hasil['status'] = 'sukses';
                if ($cekrencanakontrol > '') {
                    $this->db->query(" UPDATE rencana_kontrol SET 
                        tgl_kunjungan='$input->tgl',skdp='" . $skdp . "' WHERE id_transaksi=$input->id_transaksi' and id_kunjungan='$input->id_kunjungan'");
                } else {
                    $this->db->query(" INSERT INTO rencana_kontrol(id_transaksi,
                        id_kunjungan,
                        tgl_kunjungan,
                        id_unit,
                        skdp,
                        noka,
                        sep_awal,
                        no_rujukan
                    ) VALUES('$input->id_transaksi', '$input->id_kunjungan','$input->tgl','$input->unit','" . $skdp . "','" . $noka . "','" . $sepasal . "','" . $norujukan . "')  ");
                }

                echo json_encode($hasil);
            }
        } else {
            # code...
            $hasil['status'] = 'gagal';
            $hasil['pesan'] = 'Rencana Kontrol Sudah Ada';
            echo json_encode($hasil);
            return;
        }
    }
    public function PostSEP($noka, $idokter, $diagnosa, $polirs, $noRujukan, $TujuanKunjungan, $prosedurpelayanan, $Penunjang, $assesmenpelayanan, $createRencanaKontrol, $kd_perujuk, $kelas, $rs)
    {


        $tStamp     = $this->tStamp();
        $method     = 'POST';
        $param      = $this->jsonCreateSep($noka, $idokter, $diagnosa, $polirs, $noRujukan, $TujuanKunjungan, $prosedurpelayanan, $Penunjang, $assesmenpelayanan, $kd_perujuk, $kelas, $rs, $norm, $createRencanaKontrol);
        $request    = '/SEP/2.0/insert';
        $string     = $this->url($request, $param, $method, $tStamp);
        $hasil = array();
        if ($string->metaData->code != 200) {
            $hasil['status']    = 'gagal';
            $hasil['pesan']     = $string->metaData->message;
            $hasil['ppk']       = $param;
        } else {
            $response   = $this->Decrypt($string->response, $tStamp);
            $hasil['status']    = 'sukses';
            $hasil['pesan']     = '';
            $hasil['data']      = $response;
        }
        echo json_encode($hasil);
    }
    public function CreateSepIrja()
    {
        $tStamp         = $this->tStamp();
        $input          = json_decode(file_get_contents('php://input'));
        $method         = 'POST';
        $cek_kunjungan  = $this->db->query("SELECT no_sjp FROM penjamin_transaksi WHERE no_rujukan = '$input->rujukan'")->getNumRows();
        $laka           = $input->laka;
        $no_lp          = $input->no_lp;
        $tgl_laka       = $input->tgl_laka;
        $keterangan_laka = $input->keterangan_laka;
        $suplesi        = $input->suplesi;
        $no_suplesi     = $input->no_suplesi;
        $provinsi_laka  = $input->provinsi_laka;
        $kota_laka      = $input->kota_laka;
        $kecamatan_laka = $input->kecamatan_laka;
        $postmrs        = $input->postmrs;
        $katarak        = $input->katarak;
        $cob            = $input->cob;
        if ($postmrs == 0) {
            if ($input->polirs == $input->polibpjs && $input->polibpjs != 'HDL' && $input->skdp > '') {
                $createRencanaKontrol = $input->skdp;
                $TujuanKunjungan    = $input->tujuankunj;
                $prosedurpelayanan  = $input->prosedurkunj;
                $Penunjang          = $input->penunjangkunj;
                $assesmenpelayanan  = $input->assesmenkunj;
                $rujukan            = $input->rujukan;
                $kd_perujuk         =  substr($input->rujukan, 0, 8);
                $kelas              = 3;
                if (substr($kd_perujuk, 4, 1) == 'R') {
                    $rs = '2';
                } else {
                    $rs = '1';
                }
            } elseif ($input->polibpjs == 'HDL') {
                $unit = 'INT';
                $createRencanaKontrol = $input->skdp;
                $TujuanKunjungan    = 1;
                $prosedurpelayanan  = 1;
                $Penunjang          = 12;
                $assesmenpelayanan  = $input->assesmenkunj;
                $rujukan            = $input->rujukan;
                $kd_perujuk         =  substr($input->rujukan, 0, 8);
                $kelas              = 3;
                if (substr($kd_perujuk, 4, 1) == 'R') {
                    $rs = '2';
                } else {
                    $rs = '1';
                }
            } else {
                $TujuanKunjungan    = $input->tujuankunj;
                $prosedurpelayanan  = $input->prosedurkunj;
                $Penunjang          = $input->penunjangkunj;
                $assesmenpelayanan  = $input->assesmenkunj;
                $rujukan            = $input->rujukan;
                $kd_perujuk         =  substr($input->rujukan, 0, 8);
                $kelas              = 3;
                $createRencanaKontrol = '';
                if (substr($kd_perujuk, 4, 1) == 'R') {
                    $rs = '2';
                } else {
                    $rs = '1';
                }
            }
        } else {
            $TujuanKunjungan    = $input->tujuankunj;
            $prosedurpelayanan  = $input->prosedurkunj;
            $Penunjang          = $input->penunjangkunj;
            $assesmenpelayanan  = $input->assesmenkunj;
            $createRencanaKontrol = $input->skdp;
            $rujukan            = $input->rujukan;
            $kd_perujuk         = '0216R010';
            $kelas              = 3;
            $rs                 = '2';
        }

        $param      = $this->jsonCreateSep($input->no_rm, $input->noka, $input->dpjp, $input->diagnosa, $input->polirs, $rujukan, $input->tglRujukan, $TujuanKunjungan, $prosedurpelayanan, $Penunjang, $assesmenpelayanan, $kd_perujuk, $kelas, $rs, $input->skdp, $laka, $no_lp, $tgl_laka, $keterangan_laka, $suplesi, $no_suplesi, $provinsi_laka, $kota_laka, $kecamatan_laka, $katarak, $cob);
        $request    = '/SEP/2.0/insert';
        $string     = $this->url($request, $param, $method, $tStamp);
        $hasil = array();
        if ($string->metaData->code != 200) {
            $variable=$string->metaData->message;
            switch ($variable) {
                case 'Tanggal rujukan tidak sesuai':
                            $tglhd      = date("Y-m-d");
                            $param      = $this->jsonCreateSep($input->no_rm, $input->noka, $input->dpjp, $input->diagnosa, $input->polirs, $rujukan, $tglhd, $TujuanKunjungan, $prosedurpelayanan, $Penunjang, $assesmenpelayanan, $kd_perujuk, $kelas, $rs, $input->skdp, $laka, $no_lp, $tgl_laka, $keterangan_laka, $suplesi, $no_suplesi, $provinsi_laka, $kota_laka, $kecamatan_laka, $katarak, $cob);
                            $request    = '/SEP/2.0/insert';
                            $string     = $this->url($request, $param, $method, $tStamp);
                            if ($string->metaData->code != 200) {
                                $hasil['code']      = 201;
                                $hasil['status']    = 'sukses';
                                $hasil['pesan']     = $string->metaData->message;
                                $hasil['ppk']       = $param;
                            } else {
                                $response   = $this->Decrypt($string->response, $tStamp);
                                $hasil['code']      = 200;
                                $hasil['status']    = 'sukses';
                                $hasil['pesan']     = '';
                                $hasil['data']      = $response;
                            }
                    break;
                case 'tujuanKunj tidak sesuai':
                            $tglhd      = date("Y-m-d");
                            $pesan      ='coba kunjungan';
                            $param      = $this->jsonCreateSep($input->no_rm, $input->noka, $input->dpjp, $input->diagnosa, $input->polirs, $rujukan, $tglhd, $TujuanKunjungan, $prosedurpelayanan, $Penunjang, $assesmenpelayanan, $kd_perujuk, $kelas, $rs, $input->skdp, $laka, $no_lp, $tgl_laka, $keterangan_laka, $suplesi, $no_suplesi, $provinsi_laka, $kota_laka, $kecamatan_laka, $katarak, $cob,$pesan);
                            $request    = '/SEP/2.0/insert';
                            $string     = $this->url($request, $param, $method, $tStamp);
                            if ($string->metaData->code != 200) {
                                $hasil['code']      = 201;
                                $hasil['status']    = 'sukses';
                                $hasil['pesan']     = $string->metaData->message;
                                $hasil['ppk']       = $param;
                            } else {
                                $response   = $this->Decrypt($string->response, $tStamp);
                                $hasil['code']      = 200;
                                $hasil['status']    = 'sukses';
                                $hasil['pesan']     = '';
                                $hasil['data']      = $response;
                            }
                    break;
                
                default:
                    $hasil['code']      = 201;
                    $hasil['status']    = 'sukses';
                    $hasil['pesan']     = $string->metaData->message;
                    $hasil['ppk']       = $param;
                    break;
            }
            
        } else {
            $response   = $this->Decrypt($string->response, $tStamp);
            $hasil['code']      = 200;
            $hasil['status']    = 'sukses';
            $hasil['pesan']     = '';
            $hasil['data']      = $response;
        }


        echo json_encode($hasil);
    }
    public function jsonInsertRujukan($sep, $tgl, $tglkontrol, $rs, $jnsPelayanan, $catatan, $icd, $tipeRujukan, $unit, $user)
    {
        $json = '
            {
                "request": {
                    "t_rujukan": {
                        "noSep": "' . $sep . '",
                        "tglRujukan": "' . $tgl . '",
                        "tglRencanaKunjungan":"' . $tglkontrol . '",
                        "ppkDirujuk": "' . $rs . '",
                        "jnsPelayanan": "' . $jnsPelayanan . '",
                        "catatan": "' . $catatan . '",
                        "diagRujukan": "' . $icd . '",
                        "tipeRujukan": "' . $tipeRujukan . '",
                        "poliRujukan": "' . $unit . '",
                        "user": "' . $user . '"
                    }
                }
            }';
        return $json;
    }

    public function jsonupdateRujukan($norujukan, $tgl, $tglkontrol, $rs, $jns_pelayanan, $catatan, $icd, $tiperujukan, $unit, $user)
    {
        $json = '
            {
                "request": {
                    "t_rujukan": {
                        "noRujukan": "' . $norujukan . '",
                        "tglRujukan": "' . $tgl . '",
                        "tglRencanaKunjungan":"' . $tglkontrol . '",
                        "ppkDirujuk": "' . $rs . '",
                        "jnsPelayanan": "' . $jns_pelayanan . '",
                        "catatan": "' . $catatan . '",
                        "diagRujukan": "' . $icd . '",
                        "tipeRujukan": "' . $tiperujukan . '",
                        "poliRujukan": "' . $unit . '",
                        "user": "' . $user . '"
                    }
                }
            }';

        return $json;
    }

    public function jsonDeleteRujukan($idrujukan, $user)
    {
        $json = '
            {
                "request": {
                    "t_rujukan": {
                        "noRujukan": "' . $idrujukan . '",
                        "user": "' . $user . '",
                    }
                }
            }';
        return $json;
    }
    public function InsertRujukan()
    {
        $input      = json_decode(file_get_contents('php://input'));
        $icd            = $input->diagnosa;
        $tglkontrol     = $input->tgl;
        $sep            = $input->sep;
        $catatan        = $input->keterangan;
        $jnsPelayanan   = $input->pelayanan;
        $tiperujukan    = $input->tipe;
        $rs             = $input->rs;
        $unit           = $input->poli;
        $tgl            = date('Y-m-d');
        $user           = 'Admin RS';

        $tStamp     = $this->tStamp();
        $method     = 'POST';
        $param      = $this->jsonInsertRujukan($sep, $tgl, $tglkontrol, $rs, $jnsPelayanan, $catatan, $icd, $tiperujukan, $unit, $user);
        $request    = '/Rujukan/2.0/insert';
        $string     = $this->url($request, $param, $method, $tStamp);
        $hasil = array();
        if ($string->metaData->code != 200) {
            //$hasil = array();
            $hasil['status']    = 'gagal';
            $hasil['pesan']     = $string->metaData->message;
            $hasil['code']      = $string->metaData->code;
        } else {
            $hasil              = $this->Decrypt($string->response, $tStamp);
            $hasil['status']    = 'sukses';
            $hasil['pesan']     = $string->metaData->message;
            $hasil['code']      = $string->metaData->code;
        }
        echo json_encode($hasil);
    }

    public function UpdateRujukan($norujukan, $tgl, $tglkontrol, $rs, $jns_pelayanan, $catatan, $icd, $tiperujukan, $unit, $user)
    {

        $catatan        = "catatan rujukan";
        $jns_pelayanan  = 1; // jenis pelayanan -> 1.R.Inap 2.R.Jalan
        $tiperujukan    = 0; // tipe rujukan -> 0.penuh, 1.Partial 2.rujuk balik

        $tStamp     = $this->tStamp();
        $method     = 'PUT';
        $param      = $this->jsonupdateRujukan($norujukan, $tgl, $tglkontrol, $rs, $jns_pelayanan, $catatan, $icd, $tiperujukan, $unit, $user);
        $request    = '/Rujukan/2.0/Update';
        $string     = $this->url($request, $param, $method, $tStamp);
        $hasil = array();
        if ($string->metaData->code != 200) {
            //$hasil = array();
            $hasil['status']    = 'gagal';
            $hasil['pesan']     = $string->metaData->message;
            $hasil['code']      = $string->metaData->code;
        } else {
            $hasil              = $this->Decrypt($string->response, $tStamp);
            $hasil['status']    = 'sukses';
            $hasil['pesan']     = $string->metaData->message;
            $hasil['code']      = $string->metaData->code;
        }
        echo json_encode($hasil);
    }

    public function DeleteRujukan($idrujukan, $user)
    {
        $input      = json_decode(file_get_contents('php://input'));
        $tStamp     = $this->tStamp();
        $method     = 'DELETE';
        $param      = $this->jsonDeleteRujukan($idrujukan, $user);
        $request    = '/Rujukan/delete';
        $string     = $this->url($request, $param, $method, $tStamp);
        if ($string->metaData->code != 200) {
            $hasil = array();
            $hasil['status']    = 'gagal';
            $hasil['pesan']     = $string->metaData->message;
            $hasil['code']      = $string->metaData->code;
        } else {
            $hasil              = $this->Decrypt($string->response, $tStamp);
            $hasil['status']    = 'sukses';
            $hasil['pesan']     = $string->metaData->message;
            $hasil['code']      = $string->metaData->code;
        }
        echo json_encode($hasil);
    }
    public function CekListRencanaKontrol()
    {

        $input    = json_decode(file_get_contents('php://input'));
        $noka     = $input->noka;
        $jenis    = $input->jenis;
        $tgl_mulai = date('Y-m-d', strtotime(' - 2 months'));
        $tStamp = $this->tStamp();
        $method = 'GET';
        $param  = '';
        $request = '/RencanaKontrol/ListRencanaKontrol/Bulan/' . date('m') . '/Tahun/' . date('Y') . '/Nokartu/' . $noka . '/filter/' . $jenis;

        $string = $this->url($request, $param, $method, $tStamp);
        if ($string->metaData->code != 200) {
            $jenis=2;
            $request = '/RencanaKontrol/ListRencanaKontrol/Bulan/' . date('m') . '/Tahun/' . date('Y') . '/Nokartu/' . $noka . '/filter/' . $jenis;
            $string = $this->url($request, $param, $method, $tStamp);
                if ($string->metaData->code == 200) {
                    $response = $string->response;
                    $rencana_kontrol = $this->Decrypt($response, $tStamp);
                    $hasil['status']    = 'sukses';
                    $hasil['pesan']     = '';
                    $hasil['data']      = $rencana_kontrol;
                    echo json_encode($hasil);
                    return;
                } else {
                    $hasil = array();
                    $hasil['status'] = 'gagal';
                    $hasil['pesan'] = $string->metaData->message;
                    echo json_encode($hasil);
                    return;
                }
            
        }
        $response = $string->response;
        $rencana_kontrol = $this->Decrypt($response, $tStamp);
        $hasil['status']    = 'sukses';
        $hasil['pesan']     = '';
        $hasil['data']      = $rencana_kontrol;
        echo json_encode($hasil);
    }


    public function CekListRujukanKeluar()
    {

        $input    = json_decode(file_get_contents('php://input'));

        $tgl_akhir     = date('Y-m-d');
        $tgl_mulai = date('Y-m-d', strtotime(' - 1 months'));
        $tStamp = $this->tStamp();
        $method = 'GET';
        $param  = '';
        $request = '/Rujukan/Keluar/List/tglMulai/' . $tgl_mulai . '/tglAkhir/' . $tgl_akhir;

        $string = $this->url($request, $param, $method, $tStamp);
        if ($string->metaData->code != 200) {
            $hasil = array();
            $hasil['status'] = 'gagal';
            $hasil['pesan'] = $string->metaData->message;
            echo json_encode($hasil);
            return;
        }
        $response = $string->response;
        $rujukan = $this->Decrypt($response, $tStamp);
        $hasil['status']    = 'sukses';
        $hasil['pesan']     = '';
        $hasil['data']      = $rujukan;
        echo json_encode($hasil);
    }
    public function UpdateRencanaKontrol()
    {
        $input      = json_decode(file_get_contents('php://input'));
        $tStamp     = $this->tStamp();
        $skdp       = $input->skdp;
        $dokter     = $input->dpjp;
        $unit       = $input->unit;
        $tgl        = $input->tgl;
        $sep        = '';
        $method     = 'PUT';
        $param      = $this->JsonUpdateRencanaKontrol($skdp, $sep, $dokter, $unit, $tgl);
        $request    = '/RencanaKontrol/Update';
        $string     = $this->url($request, $param, $method, $tStamp);
        if ($string->metaData->code != 200) {
            $hasil['status'] = 'gagal';
            $hasil['pesan'] = $string->metaData->message;
            echo json_encode($hasil);
            return;
        }
        $response = $string->response;
        $hasil['data']   = $this->Decrypt($response, $tStamp);
        $hasil['pesan']  = 'Berhasil';
        $hasil['status'] = 'sukses';
        echo json_encode($hasil);
    }
    public function JsonUpdateRencanaKontrol($skdp, $sep, $dokter, $unit, $tgl)
    {
        $json = '  {
            "request": {
                "noSuratKontrol":"' . $skdp . '",
                "noSEP":"' . $sep . '",
                "kodeDokter":"' . $dokter . '",
                "poliKontrol":"' . $unit . '",
                "tglRencanaKontrol":"' . $tgl . '",
                "user":"coba"
            }
        }
        ';
        return $json;
    }
    public function CekSEP($noka)
    {
        $tStamp = $this->tStamp();
        $method = 'GET';
        $param = '';
        $tglMulai = date("Y-m-d", strtotime('+8 hours'));
        $tglAkhir = date("Y-m-d", strtotime('+8 hours'));
        $request = 'monitoring/HistoriPelayanan/NoKartu/' . $noka . '/tglMulai/' . $tglMulai . '/tglAkhir/' . $tglAkhir;
        $string = $this->url($request, $param, $method, $tStamp);
        $response = $string->response;
        $hasil = $this->Decrypt($response, $tStamp);
        $res2 = $hasil['histori'];
        if ($res2[0]['tglSep'] == $tglMulai && $res2[0]['ppkPelayanan'] == 'RSU Darmayu Madiun') {
            return $res2[0]['noSep'];
        } else {
            return 'kosong';
        }
    }
    public function CekListRencanaKontroltes($noka)
    {
        $tStamp = $this->tStamp();
        $method = 'GET';
        $param  = '';
        $tgl    = date("Y-m-d", strtotime('+7 hours'));
        $request = 'RencanaKontrol/ListRencanaKontrol/Bulan/' . date('m') . '/Tahun/' . date('Y') . '/Nokartu/' . $noka . '/filter/2';
        $string = $this->url($request, $param, $method, $tStamp);
        $response = $string->response;
        $hasil = $this->Decrypt($response, $tStamp);
        $res2 = $hasil['list'];
        if ($res2[0]['tglRencanaKontrol'] == $tgl) {
            return $res2[0]['noSuratKontrol'];
        } else {
            return 'kosong';
        }
    }

    public function CekRujukanBPJS($noka, $no)
    {
        $input    = json_decode(file_get_contents('php://input'));
        $tStamp = $this->tStamp();
        $tglAkhir = date('Y-m-d', strtotime('-90 days', strtotime(date('Y-m-d'))));
        $request = 'monitoring/HistoriPelayanan/NoKartu/' . $noka . '/tglMulai/' . $tglAkhir . '/tglAkhir/' . date('Y-m-d');
        $method = 'GET';
        $param = '';
        $string = $this->url($request, $param, $method, $tStamp);
        $response = $string->response;
        $hasil = $this->Decrypt($response, $tStamp);
        $res2 = $hasil['histori'];
        $res3 = json_decode(json_encode($res2));
        $jumlah = 0;
        foreach ($res3 as $key) {
            if ($key->noRujukan == $no) {
                $jumlah++;
            }
        }
        if ($jumlah > 0) {
            return 'ada';
        } else {
            return 'kosong';
        }
    }
    public function JsonPengajuan($noka, $tgl)
    {
        $json = ' {
            "request": {
                "t_sep": {
                    "noKartu": "' . $noka . '",
                    "tglSep": "' . $tgl . '",
                    "jnsPelayanan": "1",
                    "jnsPengajuan": "2",
                    "keterangan": "Approv finger",
                    "user": "mandiri"
                }
            }
        }
        ';
        return $json;
    }
    public function bridging_tes()
    {
        $input    = json_decode(file_get_contents('php://input'));
        $data      = $input->data;
        $method    = $input->method;
        $parameter = $input->parameter;
        $parameter2 = $input->parameter2;
        $tgl = date('Y-m-d');
        if ($method == 'GET') {
            $param = "";
        }
        switch ($data) {
            /*GET*/
            case 'SEPInternal':
                $request  = 'SEP/Internal/' . $parameter;
                $this->tampilhasil($request, $method, $param);
                break;
            case 'ListSpesialistik':
                $request  = '/Rujukan/ListSpesialistik/PPKRujukan/' . $parameter . '/TglRujukan/' . date("Y-m-d");
                $this->tampilhasil($request, $method, $param);
                break;
            case 'CariSEP':
                $request  = 'SEP/' . $parameter;
                $this->tampilhasil($request, $method, $param);
                break;
            case 'GetFingerPrint':
                $request  = 'SEP/FingerPrint/Peserta/' . $parameter . '/TglPelayanan/' . $parameter2;
                $this->tampilhasil($request, $method, $param);
                break;
            case 'GetListFingerPrint':
                $request  = 'SEP/FingerPrint/List/Peserta/TglPelayanan/' . $tgl;
                $this->tampilhasil($request, $method, $param);
                break;
            /*DELETE*/
            case 'HapusSEPInternal':
                $request   = '/SEP/Internal/delete';
                $param     = $this->ambiljson($data, $parameter);
                $this->tampilhasil($request, $method, $param);
                break;
        }
    }
    public function tampilhasil($request, $method, $param)
    {
        $tStamp     = $this->tStamp();
        $string     = $this->url($request, $param, $method, $tStamp);
        if ($string->metaData->code != 200) {
            $hasil = array();
            $hasil['status'] = 'gagal';
            $hasil['pesan'] = $string->metaData->message;
            echo json_encode($hasil);
            return;
        }
        $response = $string->response;
        $hasil['data']   = $this->Decrypt($response, $tStamp);
        $hasil['pesan']  = 'Berhasil';
        $hasil['status'] = 'sukses';
        echo json_encode($hasil);
    }
    public function ApprovAuto()
    {
        $input      = json_decode(file_get_contents('php://input'));
        $date       = date("Y-m-d");
        $noka       = $input->noka;
        $tStamp     = $this->tStamp();
        $param      = $this->JsonPengajuan($noka, $date);
        $request    = 'Sep/aprovalSEP';
        $method     = 'POST';
        $string     = $this->url($request, $param, $method, $tStamp);
        if ($string->metaData->code == 200) {
            $response   = $string->response;
            $hasil      = $this->Decrypt($response, $tStamp);
            $hasil['status']    = 'sukses';
            $hasil['pesan']     = 'Berhasil';
        } else {
            $hasil['status']    = 'gagal';
            $hasil['pesan']     = $string->metaData->message;
        }
        echo json_encode($hasil);
    }
    //$noka,$dokter,$diagnosa,$polirs,$noRujukan,$tglRujukan,$TujuanKunjungan,$prosedurpelayanan,$Penunjang,$assesmenpelayanan,$createRencanaKontrol,$kd_perujuk,$kelas,$rs
    public function jsonCreateSep($rm, $noka, $dokter, $diagnosa, $polirs, $noRujukan, $tglRujukan, $TujuanKunjungan, $prosedurpelayanan, $Penunjang, $assesmenpelayanan, $kd_perujuk, $kelas, $rs, $createRencanaKontrol, $laka, $no_lp, $tgl_laka, $keterangan_laka, $suplesi, $no_suplesi, $provinsi_laka, $kota_laka, $kecamatan_laka, $katarak, $cob,$pesan=null)

    {

        // if ($polirs == 'HDL') {
        //     if ($pesan=='coba kunjungan') {
        //         # code...
        //         $TKunjungan     = $TujuanKunjungan;
        //         $ppelayanan     = $prosedurpelayanan;
        //         $PenunjangSep   = $Penunjang;
        //         $apelayanan     = $assesmenpelayanan;
        //         $cRencanaKontrol = $createRencanaKontrol;
        //         $tglRujukanpas = $tglRujukan;
        //     } else {
        //         # code...
        //         $TKunjungan = '2';
        //         $ppelayanan = '';
        //         $PenunjangSep = '';
        //         $apelayanan = '5';
        //         $cRencanaKontrol = $createRencanaKontrol;
        //         $tglRujukanpas = $tglRujukan;
        //     }
            
        // } else {
        //     # code..
        // }
            // if ($TujuanKunjungan == '1') {
            //     $TKunjungan  = '';
            // } else
        if ($polirs == 'HDL') {
            if ($createRencanaKontrol == '0' || $createRencanaKontrol == null) {
                $TKunjungan  = $TujuanKunjungan;
                $ppelayanan  = '';
                $PenunjangSep = '';
                $apelayanan  = '2';
                $tglRujukanpas = $tglRujukan;
                $cRencanaKontrol = '';
            } else {
                $TKunjungan = '2';
                $ppelayanan = '';
                $PenunjangSep = '';
                $apelayanan = '2';
                $cRencanaKontrol = $createRencanaKontrol;
                $tglRujukanpas = $tglRujukan;
            }
        } else {
            if ($TujuanKunjungan == '0') {
                $TKunjungan  = $TujuanKunjungan;
                $ppelayanan  = '';
                $PenunjangSep = '';
                $apelayanan  = '';
            } else {
                $TKunjungan  = $TujuanKunjungan;
            }
            if ($prosedurpelayanan == '99') {
                $ppelayanan  = '';
            } else {
                $ppelayanan  = $prosedurpelayanan;
            }

            if ($Penunjang == '99') {
                $PenunjangSep  = '';
            } else {
                $PenunjangSep  = $Penunjang;
            }
            if ($assesmenpelayanan == '99') {
                $apelayanan  = '';
            } else {
                $apelayanan  = $assesmenpelayanan;
            }
            if ($createRencanaKontrol == '0' || $createRencanaKontrol == null) {
                $cRencanaKontrol = '';
            } else {
                $cRencanaKontrol = $createRencanaKontrol;
            }
            if ($tglRujukan == '') {
                $tglRujukanpas = date("Y-m-d");
            } else {
                $tglRujukanpas = $tglRujukan;
            }
        }

        $json = '    {
        "request":{
            "t_sep":{
                "noKartu":"' . $noka . '",
                "tglSep":"' . date("Y-m-d") . '",       
                "ppkPelayanan":"0216R010",
                "jnsPelayanan":"2",
                "klsRawat":{
                    "klsRawatHak":"",
                    "klsRawatNaik":"",
                    "pembiayaan":"",
                    "penanggungJawab":""
                    },
                    "noMR":"' . $rm . '",
                    "rujukan":{
                        "asalRujukan":"' . $rs . '",
                        "tglRujukan":"' . $tglRujukanpas . '",
                        "noRujukan":"' . $noRujukan . '",
                        "ppkRujukan":"' . $kd_perujuk . '"
                        },
                        "catatan":"",
                        "diagAwal":"' . $diagnosa . '",
                        "poli":{
                            "tujuan":"' . $polirs . '",
                            "eksekutif":"0"},
                            "cob":{
                                "cob":"' . $cob . '"},
                                "katarak":{
                                    "katarak":"' . $katarak . '" },
                                    "jaminan":{
                                        "lakaLantas":"' . $laka . '",
                                        "noLP":"' . $no_lp . '",
                                        "penjamin":{
                                            "tglKejadian":"' . $tgl_laka . '",
                                            "keterangan":"' . $keterangan_laka . '",
                                            "suplesi":{
                                                "suplesi":"0",
                                                "noSepSuplesi":"",
                                                "lokasiLaka":{
                                                    "kdPropinsi":"' . $provinsi_laka . '",
                                                    "kdKabupaten":"' . $kota_laka . '",
                                                    "kdKecamatan":"' . $kecamatan_laka . '"
                                                }
                                            }

                                        }
                                        },
                                        "tujuanKunj"    :"' . $TKunjungan . '",
                                        "flagProcedure" :"' . $ppelayanan . '",   
                                        "kdPenunjang"   :"' . $PenunjangSep . '",
                                        "assesmentPel"  :"' . $apelayanan . '",
                                        "skdp":{
                                            "noSurat"   :"' . $cRencanaKontrol . '",
                                            "kodeDPJP"  :"' . $dokter . '"
                                            },
                                            "dpjpLayan" :"' . $dokter . '",
                                            "noTelp"    :"112233445566",
                                            "user"      :"Admin RS Darmayu"
                                        }
                                    }
                                }';
        return $json;
    }
    private function getTstamp()
    {
        return strval(time() - strtotime('1970-01-01 07:00:00'));
    }
    public function decript($tStamp, $response)
    {
        $cons_id = $this->db->query("SELECT nilai FROM seting_bpjs WHERE key_setting = 'cons_id_2'")->getRow()->nilai;
        $secretKey = $this->db->query("SELECT nilai FROM seting_bpjs WHERE key_setting = 'secret_key_2'")->getRow()->nilai;
        $key = $cons_id . $secretKey . $tStamp;
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($response), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        return json_decode(\LZCompressor\LZString::decompressFromEncodedURIComponent($output), true);
    }
    /*cetak*/



    // public function CetakSEPIRJA()
    // {
    //     $input = json_decode(file_get_contents('php://input'));
    //     $SEP   = $_POST['sep'];
    //     $tStamp = $this->tStamp();
    //     $method = 'GET';
    //     $param  = '';
    //     $request = '/SEP/' . $SEP;
    //     $string = $this->url($request, $param, $method, $tStamp);
    //     $response = $string->response;
    //     $hasil = $this->Decrypt($response, $tStamp);
    //     // var_dump($hasil);exit();
    //     $nama = $hasil['peserta']['nama'];
    //     $peserta = $hasil['peserta']['jnsPeserta'];
    //     $tglsep = $hasil['tglSep'];
    //     if ($hasil['peserta']['kelamin'] == 'P') {
    //         $jk = 'Perempuan';
    //     } else {
    //         $jk = 'Laki-Laki';
    //     }
    //     $kelasRawat = '';
    //     if ($hasil['jnsPelayanan'] == 'Rawat Inap') {
    //         $kelasRawat = $hasil['peserta']['hakKelas'];
    //     }
    //     $kelasRawat = $hasil['peserta']['hakKelas'];

    //     //get peserta
    //     $noka     = $hasil['peserta']['noKartu'];
    //     $tgl_mulai = date('Y-m-d', strtotime(' - 2 months'));
    //     $tStamp = $this->tStamp();
    //     $method = 'GET';
    //     $param  = '';
    //     $request1 = '/Peserta/nokartu/' . $noka . '/tglSEP/' . date("Y-m-d");
    //     $string1 = $this->url($request1, $param, $method, $tStamp);
    //     if ($string1 == null) {
    //         $hasil1 = array();
    //         $hasil1['status'] = 'gagal';
    //         $hasil1['pesan'] = 'error jaringan';
    //         echo json_encode($hasil1);
    //         return;
    //     }
    //     $response1 = $string1->response;
    //     $hasil_peserta = $this->Decrypt($response1, $tStamp);

    //     //get rujukan 
    //     $norujukan     = $hasil['noRujukan'];
    //     $tStamp = $this->tStamp();
    //     $method = 'GET';
    //     $param  = '';
    //     $request2 = '/Rujukan/' . $norujukan;
    //     $string2 = $this->url($request2, $param, $method, $tStamp);
    //     if ($string2 == null) {
    //         $hasil2 = array();
    //         $hasil2['status'] = 'gagal';
    //         $hasil2['pesan'] = 'error jaringan';
    //         echo json_encode($hasil2);
    //         return;
    //     }
    //     $response2 = $string2->response;
    //     $hasil_rujukan = $this->Decrypt($response2, $tStamp);

    //     if (isset($hasil_rujukan['rujukan']['poliRujukan']['nama'])) {
    //         $rujukanpolinama = $hasil_rujukan['rujukan']['poliRujukan']['nama'];
    //     } else {
    //         $rujukanpolinama = '-';
    //     }

    //     if (isset($hasil_rujukan['rujukan']['provPerujuk']['nama'])) {
    //         $rujukanprovPerujuk = $hasil_rujukan['rujukan']['provPerujuk']['nama'];
    //     } else {
    //         $rujukanprovPerujuk = 'RSU DARMAYU MADIUN';
    //     }

    //     if ($hasil['dpjp']['nmDPJP'] == 0 || $hasil['dpjp']['nmDPJP'] == '') {
    //         $nmdokter = $hasil['kontrol']['nmDokter'];
    //     } else {
    //         $nmdokter = $hasil['dpjp']['nmDPJP'];
    //     }

    //     if ($hasil['poli'] == 0 || $hasil['poli'] == '') {
    //         $polihasil = '-';
    //     } else {
    //         $polihasil = $hasil['poli'];
    //     }



    //     $writer = new PngWriter();
    //     $qrCode = QrCode::create($hasil['peserta']['noKartu'])
    //         ->setEncoding(new Encoding('UTF-8'))
    //         ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
    //         ->setSize(150)
    //         ->setMargin(10)
    //         ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
    //         ->setForegroundColor(new Color(0, 0, 0))
    //         ->setBackgroundColor(new Color(255, 255, 255));

    //     $result = $writer->write($qrCode);

    //     $dataUri = $result->getDataUri();

    //     if ($hasil['tujuanKunj']['nama'] == 'Konsul Dokter') {
    //         $tujuankunj = "Kunjungan Kontrol (ulangan)";
    //     } else {
    //         $tujuankunj = $hasil['tujuanKunj']['nama'];
    //     }

    //     if ($hasil['kelasRawat'] == 'Diatas Kelas 1') {
    //         $kelasRawat = $hasil['kelasRawat'];
    //     } else {
    //         $kelasRawat = $hasil['klsRawat']['klsRawatHak'];
    //     }
    //     //var_dump($hasil);
    //     $html = "<html>
    //                             <body>
    //                             <table style='text-align: center;' width=100%>
    //                             <tr>
    //                             <td>
    //                             <img src='_assets/dist/img/darmayu.jpg' height='30'>
    //                             </td>
    //                             <td>
    //                             <div style='font-size:14px;'><b>SURAT ELEGIBILITAS PESERTA</b></div>
    //                             <div style='font-size:11px;'>RSU DARMAYU MADIUN</div>
    //                             </td>
    //                             <td>                        
    //                             <img src='_assets/dist/img/bpjs1.jpg' height='30'>
    //                             </td>
    //                             </tr>           
    //                             </table>

    //                             <table style='font-size:10px;border-spacing: 0px;' border='0'>
    //                             <tr>
    //                             <td width='30mm'>
    //                             No. Sep
    //                             </td>
    //                             <td>
    //                             : 
    //                             </td>
    //                             <td width='50mm'>
    //                             <div style='font-size:10px;'><b>" . $SEP . "</b></div>
    //                             </td>

    //                             <td>

    //                             </td>
    //                             <td>

    //                             </td>
    //                             <td>

    //                             </td>
    //                             <tr>

    //                             <tr>
    //                             <td width='30mm'>
    //                             Tgl. SEP
    //                             </td>
    //                             <td>
    //                             : 
    //                             </td>
    //                             <td width='50mm'>
    //                             " . $tglsep . "
    //                             </td>

    //                             <td>
    //                             Peserta
    //                             </td>
    //                             <td>
    //                             :
    //                             </td>
    //                             <td>
    //                             " . $peserta . "
    //                             </td>
    //                             <tr>

    //                             <tr>
    //                             <td width='30mm'>
    //                             No. Kartu
    //                             </td>
    //                             <td>
    //                             : 
    //                             </td>
    //                             <td width='50mm'>
    //                             " . $hasil['peserta']['noKartu'] . "(MR. " . $hasil['peserta']['noMr'] . ")
    //                             </td>

    //                             <td width='25mm'>

    //                             </td>
    //                             <td>

    //                             </td>
    //                             <td>

    //                             </td>
    //                             <tr>

    //                             <tr>
    //                             <td width='30mm'>
    //                             Nama Peserta
    //                             </td>
    //                             <td>
    //                             : 
    //                             </td>
    //                             <td width='50mm'>
    //                             " . $nama . " 
    //                             </td>

    //                             <td>
    //                             Jns. Rawat
    //                             </td>
    //                             <td>
    //                             : 
    //                             </td>
    //                             <td>
    //                             " . $hasil['jnsPelayanan'] . "
    //                             </td>
    //                             <tr>

    //                             <tr>
    //                             <td width='30mm' valign='top'>
    //                             Tgl.Lahir
    //                             </td>
    //                             <td valign='top'>
    //                             : 
    //                             </td>
    //                             <td width='50mm' valign='top'>
    //                             " . $hasil['peserta']['tglLahir'] . " Kelamin : " . $jk . "
    //                             </td>

    //                             <td valign='top'>
    //                             Jns.Kunjungan
    //                             </td>
    //                             <td valign='top'>
    //                             : 
    //                             </td>
    //                             <td valign='top'>
    //                             - " . $tujuankunj . " <br>
    //                             - " . $hasil['flagProcedure']['nama'] . "
    //                             </td>
    //                             <tr>

    //                             <tr>
    //                             <td width='30mm'>
    //                             No.Telepon
    //                             </td>
    //                             <td>
    //                             : 
    //                             </td>
    //                             <td width='50mm'>
    //                             " . $hasil_peserta['peserta']['mr']['noTelepon'] . "
    //                             </td>

    //                             <td width='25mm'>

    //                             </td>
    //                             <td>

    //                             </td>
    //                             <td>

    //                             </td>
    //                             <tr>


    //                             <tr>
    //                             <td width='30mm'>
    //                             Sub/Spesialis
    //                             </td>
    //                             <td>
    //                             : 
    //                             </td>
    //                             <td width='50mm'>
    //                              " . $polihasil . "
    //                             </td>

    //                             <td>
    //                             Poli Perujuk
    //                             </td>
    //                             <td>
    //                             : 
    //                             </td>
    //                             <td>
    //                             " . $rujukanpolinama . "
    //                             </td>
    //                             <tr>

    //                             <tr>
    //                             <td width='30mm'>
    //                             Dokter
    //                             </td>
    //                             <td>
    //                             : 
    //                             </td>
    //                             <td width='50mm'>
    //                             " . $nmdokter  . "
    //                             </td>
    //                             <td>Kls.Hak</td>
    //                             <td>:</td>
    //                             <td>" . $hasil['peserta']['hakKelas'] . "</td>
    //                             <tr>

    //                             <tr>
    //                             <td width='30mm'>
    //                             Faskes Perujuk
    //                             </td>
    //                             <td>
    //                             : 
    //                             </td>
    //                             <td width='50mm'>
    //                              " . $rujukanprovPerujuk . "
    //                             </td>
    //                             <td>Kls.Rawat</td>
    //                             <td>:</td>
    //                             <td>Kelas " . $kelasRawat . "</td>
    //                             <tr>


    //                             <tr>
    //                             <td width='30mm' valign='top'>
    //                             Diagnosa Awal
    //                             </td>
    //                             <td valign='top'>
    //                             : 
    //                             </td>
    //                             <td width='50mm' valign='top'>
    //                             " . $hasil['diagnosa'] . "
    //                             </td>
    //                             ";
    //     if ($hasil['jnsPelayanan'] == 'Rawat Inap') {
    //         $html .= "<td>Penjamin</td>
    //                             <td>:</td>
    //                             <td>" . $hasil['penjamin'] . " </td>
    //                             <tr>";
    //     }

    //     $html .= "<tr>
    //                             <td  width='30mm'>
    //                             Catatan
    //                             </td>
    //                             <td>
    //                             : 
    //                             </td>
    //                             <td colspan='4'>
    //                             " . $hasil['catatan'] . "
    //                             </td>
    //                             </tr>

    //                             </table>

    //                             <table >
    //                             <tr>
    //                             <td width='100mm' style='font-size:7px;'>
    //         *Saya Menyetujui BPJS Kesehatan untuk:<br>
    //         a. membuka dan atau menggunakan informasi medis Pasien untuk keperluan administrasi pembayaran asuransi atau jaminan pembiayaan kesehatan<br>
    //         b. memberikan akses informasi medis atau riwayat pelayanan kepada dokter/tenaga medis pada RSU DARMAYU MADIUN untuk kepentingan pemeliharaan kesehatan, pengobatan, penyembuhan dan perawatan Pasien<br>
    //         *Saya mengetahi dan memahami :<br>
    //         a. Rumah Sakit dapat melakukan koordinasi dengan PT. Jasa Raharja / PT Taspen / PTASABRI / BPJS Ketenagakerjaan atau Penjamin lainnya, jika Peserta merupakan pasien mengalami kecelakaan lalulintas dan / atau kecelakaan kerja<br>
    //         b. SEP bukan sebagai bukti penjaminan peserta<br>
    //         **Dengan tampilnya luaran SEP elektronik ini merupakan hasil validasi terhadap eligibilitas Pasien secara elektronik (validasi finger print atau biometrik / sistem validasi lain)<br>
    //         dan selanjutnya Pasien dapat mengakses pelayanan kesehatan rujukan sesuai ketentuan berlaku.<br>
    //         Kebenaran dan keaslian data Pasien menjadi tanggun jawab penuh FKRTL<br>
    //         Cetakan Ke 1 : " . str_pad(gmdate("d-M-Y H:i:s", time() + 60 * 61 * 7), 71, " ") . "
    //                             </td>
    //                             <td width='40mm' style='text-align: center;font-size:10px;'>
    //                             Persetujuan<br>
    //                             Pasien/Keluarga Pasien<br>
    //                             <img src='" . $dataUri . "' width='50' height='50'><br>
    //                             " . substr($nama, 0, 25) . "<br>

    //                             </td>
    //                             </tr>
    //                             </table>
    //                             </body>
    //                             </html>";
    //     //echo $html;exit();
    //     $mpdf   = new \Mpdf\Mpdf([
    //         'mode' => 'utf-8',
    //         'A4',
    //         'format' => [160, 100]
    //     ]);
    //     //$mpdf->showImageErrors = true;

    //     //$mpdf=new \Mpdf\Mpdf('utf-8', array(160,80));
    //     $mpdf->AddPage(
    //         'P', // L - landscape, P - portrait
    //         '',
    //         '',
    //         '',
    //         '',
    //         2, // margin_left
    //         2, // margin right
    //         2, // margin top
    //         2, // margin bottom
    //         0, // margin header
    //         2
    //     ); // margin footer

    //     $mpdf->WriteHTML($html);
    //     $mpdf->Output("cetak.pdf", 'I');
    //     exit;
    //     //echo $html;
    //     //$mpdf->Output('sep_termal.pdf', 'I');
    //     //$klien = str_replace(".","",$_SERVER['REMOTE_ADDR']);
    //     //$mpdf->Output($klien.'sep_termal.pdf', 'I');


    // }
    public function CetakSEPIRJA()
    {
        $input = json_decode(file_get_contents('php://input'));
        $SEP   = $_POST['sep'];
        $tStamp = $this->tStamp();
        $method = 'GET';
        $param  = '';
        $request = '/SEP/' . $SEP;
        $string = $this->url($request, $param, $method, $tStamp);
        $response = $string->response;
        $hasil = $this->Decrypt($response, $tStamp);
        // var_dump($hasil);exit();
        $nama = $hasil['peserta']['nama'];
        $peserta = $hasil['peserta']['jnsPeserta'];
        $tglsep = $hasil['tglSep'];
        if ($hasil['peserta']['kelamin'] == 'P') {
            $jk = 'Perempuan';
        } else {
            $jk = 'Laki-Laki';
        }
        $kelasRawat = '';
        if ($hasil['jnsPelayanan'] == 'Rawat Inap') {
            $kelasRawat = $hasil['peserta']['hakKelas'];
        }
        $kelasRawat = $hasil['peserta']['hakKelas'];

        //get peserta
        $noka     = $hasil['peserta']['noKartu'];
        $tgl_mulai = date('Y-m-d', strtotime(' - 2 months'));
        $tStamp = $this->tStamp();
        $method = 'GET';
        $param  = '';
        $request1 = '/Peserta/nokartu/' . $noka . '/tglSEP/' . date("Y-m-d");
        $string1 = $this->url($request1, $param, $method, $tStamp);
        if ($string1 == null) {
            $hasil1 = array();
            $hasil1['status'] = 'gagal';
            $hasil1['pesan'] = 'error jaringan';
            echo json_encode($hasil1);
            return;
        }
        $response1 = $string1->response;
        $hasil_peserta = $this->Decrypt($response1, $tStamp);

        //get rujukan 
        $norujukan     = $hasil['noRujukan'];
        $tStamp = $this->tStamp();
        $method = 'GET';
        $param  = '';
        $request2 = '/Rujukan/' . $norujukan;
        $string2 = $this->url($request2, $param, $method, $tStamp);
        if ($string2 == null) {
            $hasil2 = array();
            $hasil2['status'] = 'gagal';
            $hasil2['pesan'] = 'error jaringan';
            echo json_encode($hasil2);
            return;
        }
        $response2 = $string2->response;
        $hasil_rujukan = $this->Decrypt($response2, $tStamp);

        if (isset($hasil_rujukan['rujukan']['poliRujukan']['nama'])) {
            $rujukanpolinama = $hasil_rujukan['rujukan']['poliRujukan']['nama'];
        } else {
            $rujukanpolinama = '-';
        }

        if (isset($hasil_rujukan['rujukan']['provPerujuk']['nama'])) {
            $rujukanprovPerujuk = $hasil_rujukan['rujukan']['provPerujuk']['nama'];
        } else {
            $rujukanprovPerujuk = 'RSU DARMAYU MADIUN';
        }

        if ($hasil['dpjp']['nmDPJP'] == 0 || $hasil['dpjp']['nmDPJP'] == '') {
            $nmdokter = $hasil['kontrol']['nmDokter'];
        } else {
            $nmdokter = $hasil['dpjp']['nmDPJP'];
        }

        if ($hasil['poli'] == 0 || $hasil['poli'] == '') {
            $polihasil = '-';
        } else {
            $polihasil = $hasil['poli'];
        }



        $writer = new PngWriter();
        $qrCode = QrCode::create($hasil['peserta']['noKartu'])
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(150)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $result = $writer->write($qrCode);

        $dataUri = $result->getDataUri();

        if ($hasil['tujuanKunj']['nama'] == 'Konsul Dokter') {
            $tujuankunj = "Kunjungan Kontrol (ulangan)";
        } else {
            $tujuankunj = $hasil['tujuanKunj']['nama'];
        }

        if ($hasil['kelasRawat'] == 'Diatas Kelas 1') {
            $kelasRawat = $hasil['kelasRawat'];
        } else {
            //$kelasRawat = $hasil['klsRawat']['klsRawatHak'];
            if ($hasil['jnsPelayanan'] == 'Rawat Inap') {
                $kelasRawat =  " " . $hasil['kelasRawat'];
            } else {
                $kelasRawat = "";
            }
            //$kelasRawat = $hasil['kelasRawat'];
        }
        //var_dump($hasil);
        $html = "<html>
                                <body>
                                <table style='text-align: center;' width=100%>
                                <tr>
                                <td>
                                <img src='_assets/dist/img/darmayu.jpg' height='30'>
                                </td>
                                <td>
                                <div style='font-size:14px;'><b>SURAT ELEGIBILITAS PESERTA</b></div>
                                <div style='font-size:11px;'>RSU DARMAYU MADIUN</div>
                                </td>
                                <td>                        
                                <img src='_assets/dist/img/bpjs1.jpg' height='30'>
                                </td>
                                </tr>           
                                </table>

                                <table style='font-size:10px;border-spacing: 0px;' border='0'>
                                <tr>
                                <td width='30mm'>
                                No. Sep
                                </td>
                                <td>
                                : 
                                </td>
                                <td width='50mm'>
                                <div style='font-size:10px;'><b>" . $SEP . "</b></div>
                                </td>

                                <td>
                                
                                </td>
                                <td>
                                
                                </td>
                                <td>
                                
                                </td>
                                <tr>

                                <tr>
                                <td width='30mm'>
                                Tgl. SEP
                                </td>
                                <td>
                                : 
                                </td>
                                <td width='50mm'>
                                " . $tglsep . "
                                </td>

                                <td>
                                Peserta
                                </td>
                                <td>
                                :
                                </td>
                                <td>
                                " . $peserta . "
                                </td>
                                <tr>

                                <tr>
                                <td width='30mm'>
                                No. Kartu
                                </td>
                                <td>
                                : 
                                </td>
                                <td width='50mm'>
                                " . $hasil['peserta']['noKartu'] . "(MR. " . $hasil['peserta']['noMr'] . ")
                                </td>

                                <td width='25mm'>
                                
                                </td>
                                <td>
                                 
                                </td>
                                <td>
                                
                                </td>
                                <tr>

                                <tr>
                                <td width='30mm'>
                                Nama Peserta
                                </td>
                                <td>
                                : 
                                </td>
                                <td width='50mm'>
                                " . $nama . " 
                                </td>

                                <td>
                                Jns. Rawat
                                </td>
                                <td>
                                : 
                                </td>
                                <td>
                                " . $hasil['jnsPelayanan'] . "
                                </td>
                                <tr>

                                <tr>
                                <td width='30mm' valign='top'>
                                Tgl.Lahir
                                </td>
                                <td valign='top'>
                                : 
                                </td>
                                <td width='50mm' valign='top'>
                                " . $hasil['peserta']['tglLahir'] . " Kelamin : " . $jk . "
                                </td>

                                <td valign='top'>
                                Jns.Kunjungan
                                </td>
                                <td valign='top'>
                                : 
                                </td>
                                <td valign='top'>
                                - " . $tujuankunj . " <br>
                                - " . $hasil['flagProcedure']['nama'] . "
                                </td>
                                <tr>

                                <tr>
                                <td width='30mm'>
                                No.Telepon
                                </td>
                                <td>
                                : 
                                </td>
                                <td width='50mm'>
                                " . $hasil_peserta['peserta']['mr']['noTelepon'] . "
                                </td>

                                <td width='25mm'>
                               
                                </td>
                                <td>
                                 
                                </td>
                                <td>
                                
                                </td>
                                <tr>


                                <tr>
                                <td width='30mm'>
                                Sub/Spesialis
                                </td>
                                <td>
                                : 
                                </td>
                                <td width='50mm'>
                                 " . $polihasil . "
                                </td>

                                <td>
                                Poli Perujuk
                                </td>
                                <td>
                                : 
                                </td>
                                <td>
                                " . $rujukanpolinama . "
                                </td>
                                <tr>

                                <tr>
                                <td width='30mm'>
                                Dokter
                                </td>
                                <td>
                                : 
                                </td>
                                <td width='50mm'>
                                " . $nmdokter  . "
                                </td>
                                <td>Kls.Hak</td>
                                <td>:</td>
                                <td>" . $hasil['peserta']['hakKelas'] . "</td>
                                <tr>

                                <tr>
                                <td width='30mm'>
                                Faskes Perujuk
                                </td>
                                <td>
                                : 
                                </td>
                                <td width='50mm'>
                                 " . $rujukanprovPerujuk . "
                                </td>
                                <td>Kls.Rawat</td>
                                <td>:</td>
                                <td> " . $kelasRawat . "</td>
                                <tr>


                                <tr>
                                <td width='30mm' valign='top'>
                                Diagnosa Awal
                                </td>
                                <td valign='top'>
                                : 
                                </td>
                                <td width='50mm' valign='top'>
                                " . $hasil['diagnosa'] . "
                                </td>
                                ";
        if ($hasil['jnsPelayanan'] == 'Rawat Inap') {
            $html .= "<td>Penjamin</td>
                                <td>:</td>
                                <td>" . $hasil['penjamin'] . " </td>
                                <tr>";
        }

        $html .= "<tr>
                                <td  width='30mm'>
                                Catatan
                                </td>
                                <td>
                                : 
                                </td>
                                <td colspan='4'>
                                " . $hasil['catatan'] . "
                                </td>
                                </tr>

                                </table>

                                <table >
                                <tr>
                                <td width='100mm' style='font-size:7px;'>
            *Saya Menyetujui BPJS Kesehatan untuk:<br>
            a. membuka dan atau menggunakan informasi medis Pasien untuk keperluan administrasi pembayaran asuransi atau jaminan pembiayaan kesehatan<br>
            b. memberikan akses informasi medis atau riwayat pelayanan kepada dokter/tenaga medis pada RSU DARMAYU MADIUN untuk kepentingan pemeliharaan kesehatan, pengobatan, penyembuhan dan perawatan Pasien<br>
            *Saya mengetahi dan memahami :<br>
            a. Rumah Sakit dapat melakukan koordinasi dengan PT. Jasa Raharja / PT Taspen / PTASABRI / BPJS Ketenagakerjaan atau Penjamin lainnya, jika Peserta merupakan pasien mengalami kecelakaan lalulintas dan / atau kecelakaan kerja<br>
            b. SEP bukan sebagai bukti penjaminan peserta<br>
            **Dengan tampilnya luaran SEP elektronik ini merupakan hasil validasi terhadap eligibilitas Pasien secara elektronik (validasi finger print atau biometrik / sistem validasi lain)<br>
            dan selanjutnya Pasien dapat mengakses pelayanan kesehatan rujukan sesuai ketentuan berlaku.<br>
            Kebenaran dan keaslian data Pasien menjadi tanggun jawab penuh FKRTL<br>
            Cetakan Ke 1 : " . str_pad(gmdate("d-M-Y H:i:s", time() + 60 * 61 * 7), 71, " ") . "
                                </td>
                                <td width='40mm' style='text-align: center;font-size:10px;'>
                                Persetujuan<br>
                                Pasien/Keluarga Pasien<br>
                                <img src='" . $dataUri . "' width='50' height='50'><br>
                                " . substr($nama, 0, 25) . "<br>
                                
                                </td>
                                </tr>
                                </table>
                                </body>
                                </html>";
        //echo $html;exit();
        $mpdf   = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'A4',
            'format' => [160, 100]
        ]);
        //$mpdf->showImageErrors = true;

        //$mpdf=new \Mpdf\Mpdf('utf-8', array(160,80));
        $mpdf->AddPage(
            'P', // L - landscape, P - portrait
            '',
            '',
            '',
            '',
            2, // margin_left
            2, // margin right
            2, // margin top
            2, // margin bottom
            0, // margin header
            2
        ); // margin footer

        $mpdf->WriteHTML($html);
        $mpdf->Output("cetak.pdf", 'I');
        exit;
        //echo $html;
        //$mpdf->Output('sep_termal.pdf', 'I');
        //$klien = str_replace(".","",$_SERVER['REMOTE_ADDR']);
        //$mpdf->Output($klien.'sep_termal.pdf', 'I');


    }
    /*end cetak*/
    public function getHeader($tStamp)
    {
        $cons_id   = $this->db->query("SELECT nilai FROM seting_bpjs WHERE key_setting = 'cons_id_2'")->getRow()->nilai;
        $secretKey = $this->db->query("SELECT nilai FROM seting_bpjs WHERE key_setting = 'secret_key_2'")->getRow()->nilai;
        $user_key  = $this->db->query("SELECT nilai FROM seting_bpjs WHERE key_setting = 'user_key_2'")->getRow()->nilai;
        $signature = hash_hmac('sha256', $cons_id . "&" . $tStamp, $secretKey, true);
        $encodedSignature = base64_encode($signature);
        return array("X-Cons-ID: " . $cons_id, "X-Timestamp: " . $tStamp, "X-Signature: " . $encodedSignature, "user_key:" . $user_key, "Content-Type: application/x-www-form-urlencoded\r\n");
    }
    public function modalsepinap()
    {


        $data = json_decode($_GET['data']);
        $sep       = str_replace('"', '', json_encode($data->seprajal));
        $telp  = str_replace('"', '', json_encode($data->telp));
        $tgl_sep  = str_replace('"', '', json_encode($data->tglsep));
        $id_unit  = str_replace('"', '', json_encode($data->id_unit));
        $user  = str_replace('"', '', json_encode($data->user));
        $no_rm  = str_replace('"', '', json_encode($data->no_rm));
        $poli  = str_replace('"', '', json_encode($data->poli));
        $postdpjpDokter  = str_replace('"', '', json_encode($data->kd_dokter));
        $kode_diagnosa  = str_replace('"', '', json_encode($data->kd_diagnosa));

        if ($sep == null || $sep == '') {
            $output['metaData']['message'] = "SEP DI SPRI BELUM DI ISI, Silahkan isi sep rawat jalan di Form Penata Jasa SPRI / isi sep rawat jalan di atas tombol BUAT SEP ";
            $output['metaData']['code'] = '0';
            $outputx['data']['datax'] =   $output;
            return view('view/modal/rwi/modalsepinap', $outputx);

            echo json_encode($output);
            exit();
        }

        if (isset($tgl_sep)) {
            // $tgl_sep = $_POST['tgl_sep'];
            $bulan_sep = substr($tgl_sep, 5, 2);
            $tahun_sep = substr($tgl_sep, 0, 4);
            $a = $bulan_sep . $tahun_sep;
            //var_dump($a);

        } else {
            $tgl_sep = date("Y-m-d");
            $bulan_sep = date("m");
            $tahun_sep = date("Y");
        }
        if (strlen($telp) < 8) {
            $telp = '08123456789';
        }
        $buatSPRI = false;
        $kelas = $this->db->query("SELECT map_bpjs FROM unit WHERE id_unit = '" . $id_unit . "'")->getRow()->map_bpjs;
        $url = $this->db->query("SELECT nilai FROM seting_bpjs WHERE key_setting = 'url_2'")->getRow()->nilai;
        $cons_id = $this->db->query("SELECT nilai FROM seting_bpjs WHERE key_setting = 'cons_id_2'")->getRow()->nilai;
        $ppk = $this->db->query("SELECT nilai FROM seting_bpjs WHERE key_setting = 'ppk_2'")->getRow()->nilai;
        $tStamp = $this->getTstamp();
        $headers = $this->getHeader($tStamp);
        $opts = array(
            'http' => array(
                'method' => 'GET',
                'header' => $headers
            )
        );
        //cek sep rajal
        $context = stream_context_create($opts);
        $hasil_raw = file_get_contents($url . 'SEP/' . $sep, true, $context);
        $hasil = json_decode($hasil_raw, true);
        if ($hasil['metaData']['code'] <> '200') {
            $output['metaData'] = $hasil['metaData'];
            $output['url'] = $url . 'SEP/' . $sep;
            $outputx['data']['datax'] =   $output;
            return view('view/modal/rwi/modalsepinap', $outputx);
            // echo json_encode($output);

            // return;
        }
        $data_sep =  $this->decript($tStamp, $hasil['response']);
        // echo json_encode($data_sep);
        // exit();

        $data_sep_2 =  $this->decript($tStamp, $hasil['response']);
        $kode_poli = explode(" ", $data_sep_2["poli"])[0];
        // if (isset($kode_diagnosa)) {
        //     $kode_diagnosa = explode(" ", $data_sep_2["diagnosa"])[0];
        //     // var_dump($data_sep_2); exit();
        //     //cek spri
        // }
        $hasil_raw = file_get_contents($url . 'RencanaKontrol/ListRencanaKontrol/Bulan/' . $bulan_sep . '/Tahun/' . $tahun_sep . '/Nokartu/' . $data_sep["peserta"]["noKartu"] . '/filter/2', true, $context);
        $hasil = json_decode($hasil_raw, true);
        $noSPRI = '';
        if ($hasil['metaData']['code'] <> '200') {
            if ($hasil['metaData']['code'] == '201' && $hasil['metaData']['message'] == 'Data Tidak Ditemukan') {
                $buatSPRI = true;
            } else {
                $output['metaData'] = $hasil['metaData'];
                $output['param'] = json_decode($param, true);
                $output['url'] = $url . 'RencanaKontrol/ListRencanaKontrol/Bulan/' . $bulan_sep . '/Tahun/' . $tahun_sep . '/Nokartu/' . $data_sep["peserta"]["noKartu"] . '/filter/2';
                echo json_encode($output);
                return;
            }
        } else {
            $data_list_kontrol =  $this->decript($tStamp, $hasil['response'])["list"];
            //var_dump($data_list_kontrol);exit();
            $buatSPRI = true;
            for ($i = 0; $i < count($data_list_kontrol); $i++) {
                $data_kontrol = $data_list_kontrol[$i];
                if ($data_kontrol['namaJnsKontrol'] == 'SPRI' && $data_kontrol['tglRencanaKontrol'] == $tgl_sep) {
                    $noSPRI = $data_kontrol['noSuratKontrol'];
                    $kodedokterspri = $data_kontrol['kodeDokter'];
                    //echo"$noSPRI";
                    //exit();
                    $buatSPRI = false;
                    $i = count($data_list_kontrol);
                }
            }
        }


        // cek dokter -- belum
        if (isset($poli)) {
            if ($postdpjpDokter == '' || $postdpjpDokter == null) {
                $postdpjpDokter = '0';
                $output['metaData']['code'] = 'X';
                $output['metaData']['message'] = 'Silahkan Pilih Dokter';
                $outputx['data']['datax'] =   $output;
                return view('view/modal/rwi/modalsepinap', $outputx);
                exit;
            }
            $dpjpDokter = $this->db->query("SELECT * FROM pegawai WHERE id_pegawai = '" . $postdpjpDokter . "'")->getRow()->kd_dokter_bpjs;
            $kode_poli = $poli;
        } else {
            if ($data_sep["dpjp"]["kdDPJP"] == '0') {
                $dpjpDokter = $data_sep["kontrol"]["kdDokter"];
            } else {
                $dpjpDokter = $data_sep["dpjp"]["kdDPJP"];
            }

            if ($dpjpDokter == "") {
                $dpjpDokter = $this->db->query("SELECT * FROM pegawai WHERE id_pegawai = '" . $postdpjpDokter . "'")->getRow()->kd_dokter_bpjs;
            }
        }
        // echo"$postdpjpDokter";
        if ($kode_poli == 'IGD') {
            // echo"halo";
            // exit();
            $dpjpDokter = $this->db->query("SELECT * FROM pegawai WHERE id_pegawai = '" . $postdpjpDokter . "'")->getRow()->kd_dokter_bpjs;
            $queryPoli = "SELECT
                                    un.map_bpjs
                                    FROM
                                    dokter_klinik dk
                                    JOIN unit un ON un.id_unit = dk.id_unit 
                                    WHERE
                                    dk.id_pegawai = '" . $postdpjpDokter . "' 
                                    AND un.map_bpjs <> 'IGD'";
            // echo" $queryPoli ";
            // exit;
            $kode_poli = $this->db->query($queryPoli)->getRow()->map_bpjs;
        }
        //exit();
        //HARDCODESEK
        // $dpjpDokter = '12687';
        // $kode_poli = 'INT';

        if ($buatSPRI == false) {
            $dpjpDokter = $kodedokterspri;
        }
        //echo"$dpjpDokter";
        //HARDCODESEK
        if ($buatSPRI) {
            $param = '
                                    {
                                       "request":
                                       {
                                         "noKartu":"' . $data_sep["peserta"]["noKartu"] . '",
                                         "kodeDokter":"' . $dpjpDokter . '",
                                         "poliKontrol":"' . $kode_poli . '",
                                         "tglRencanaKontrol":"' . $tgl_sep . '",
                                         "user":"user' . $user . '"
                                     }
                                 }
                                 ';

            $opts = array(
                'http' => array(
                    'method' => "POST",
                    'header' => $headers,
                    'content' => $param
                )
            );

            $context = stream_context_create($opts);

            $hasil_raw = file_get_contents($url . 'RencanaKontrol/InsertSPRI', false, $context);

            $hasil = json_decode($hasil_raw, true);

            if ($hasil['metaData']['code'] <> '200') {
                $output['metaData'] = $hasil['metaData'];
                $output['url'] = $url . 'RencanaKontrol/InsertSPRI';
                $output['param'] = json_decode($param, true);
                $output['pesan'] = "gagal";
                $outputx['data']['datax'] =   $output;
                return view('view/modal/rwi/modalsepinap', $outputx);

                //echo json_encode($output);
                return;
            } else {
                $output['pesan'] = "berhasil";
                echo json_encode($output);
                // return;
            }



            $data_spri = $this->decript($tStamp, $hasil['response']);
            $noSPRI = $data_spri["noSPRI"];
        }

        // cek kelas

        if (($kelas - 2) == $data_sep["klsRawat"]["klsRawatHak"] || $kelas == '6' || $kelas == '7') {
            $param_kelas = '
                                "klsRawatNaik":"",
                                "pembiayaan":"",
                                "penanggungJawab":""
                                ';
        } else {
            $param_kelas = '
                                "klsRawatNaik":"' . $kelas . '",
                                "pembiayaan":"1",
                                "penanggungJawab":"Pribadi"
                                ';
        }

        $param = '{
                                "request":{
                                 "t_sep":{
                                  "noKartu":"' . $data_sep["peserta"]["noKartu"] . '",
                                  "tglSep":"' . $tgl_sep . '",
                                  "ppkPelayanan":"' . $ppk . '",
                                  "jnsPelayanan":"1",
                                  "klsRawat":{
                                   "klsRawatHak":"' . $data_sep["klsRawat"]["klsRawatHak"] . '",
                                   ' . $param_kelas . '
                                   },
                                   "noMR":"' . $no_rm . '",
                                   "rujukan":{
                                       "asalRujukan":"2",
                                       "tglRujukan":"' . $data_sep["tglSep"] . '",
                                       "noRujukan":"' . $sep . '",
                                       "ppkRujukan":"' . $ppk . '"
                                       },
                                       "catatan":"SJP RWI",
                                       "diagAwal":"' . $kode_diagnosa . '",
                                       "poli":{
                                           "tujuan":"",
                                           "eksekutif":"0"
                                           },
                                           "cob":{
                                               "cob":"' . $data_sep["cob"] . '"
                                               },
                                               "katarak":{
                                                   "katarak":"' . $data_sep["katarak"] . '"
                                                   },
                                                   "jaminan":{
                                                       "lakaLantas":"' . $data_sep["kdStatusKecelakaan"] . '",
                                                       "penjamin":{
                                                        "tglKejadian":"' . $data_sep["lokasiKejadian"]["tglKejadian"] . '",
                                                        "keterangan":"' . $data_sep["lokasiKejadian"]["ketKejadian"] . '",
                                                        "suplesi":{
                                                         "suplesi":"0",
                                                         "noSepSuplesi":"",
                                                         "lokasiLaka":{
                                                          "kdPropinsi":"' . $data_sep["lokasiKejadian"]["kdProp"] . '",
                                                          "kdKabupaten":"' . $data_sep["lokasiKejadian"]["kdProp"] . '",
                                                          "kdKecamatan":"' . $data_sep["lokasiKejadian"]["kdKec"] . '"
                                                      }
                                                  }
                                              }
                                              },
                                              "tujuanKunj":"0",
                                              "flagProcedure":"",
                                              "kdPenunjang":"",
                                              "assesmentPel":"",
                                              "skdp":{
                                               "noSurat":"' . $noSPRI . '",
                                               "kodeDPJP":"' . $dpjpDokter . '"
                                               },
                                               "dpjpLayan":"",
                                               "noTelp":"' . $telp . '",
                                               "user":"user' . $user . '"
                                           }
                                       }
                                   }
                                   ';
        $opts = array(
            'http' => array(
                'method' => "POST",
                'header' => $headers,
                'content' => $param
            )
        );

        // echo"$dpjpDokter-$kode_poli";
        // $dpjpDokter = '12687';
        // $kode_poli = 'INT';

        $context = stream_context_create($opts);

        $hasil_raw = file_get_contents($url . 'SEP/2.0/insert', false, $context);

        $hasil = json_decode($hasil_raw, true);

        if ($hasil['metaData']['code'] <> '200') {
            $output['metaData'] = $hasil['metaData'];
            $output['param'] = json_decode($param, true);
            $output['url'] = $url . 'SEP/2.0/insert';
            //echo json_encode($output);
            $outputx['data']['datax'] =   $output;
            return view('view/modal/rwi/modalsepinap', $outputx);
        } else {

            $output['metaData'] = $hasil['metaData'];
            $output['response'] = $this->decript($tStamp, $hasil['response']);
            $outputx['data']['datax'] =   $output;
            // response asli bpjs dibawah ini di komen
            //$response = $this->decript($tStamp, $hasil['response']);
            // $response = $this->decript($tStamp, $hasil['response']);
            $outputxx = json_decode(json_encode($outputx), true);
            //var_dump($outputx); echo"<br>";
            return view('view/modal/rwi/modalsepinap', $outputxx);
        }
        // $outputx['metaData'] = $hasil['metaData'];
        // $outputx['no_sep'] = $response['sep']['noSep'];
        // $outputx['no_spri'] = $noSPRI;
        // echo json_encode($outputx);


        //output
        // tinggal html sep


    }
    public function modalsepinap_v2()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        // echo"x";exit();

        $data = json_decode($_GET['data']);
        $sep       = str_replace('"', '', json_encode($data->seprajal));
        //$sep       = ''; // SEMENTARA DIKOMEN UNTUK MENGHINDARI ERROR KARENA BELUM ADA SEP RAJAL

        $telp  = str_replace('"', '', json_encode($data->telp));
        $tgl_sep  = str_replace('"', '', json_encode($data->tglsep));
        $id_unit  = str_replace('"', '', json_encode($data->id_unit));
        $user  = str_replace('"', '', json_encode($data->user));
        $no_rm  = str_replace('"', '', json_encode($data->no_rm));
        $poli  = str_replace('"', '', json_encode($data->poli)); // sudah value poli maping bpjs dari spri simrs
        $postdpjpDokter  = str_replace('"', '', json_encode($data->kd_dokter));
        $kode_diagnosa  = str_replace('"', '', json_encode($data->kd_diagnosa));
        $nosasuransi  = str_replace('"', '', json_encode($data->noasuransi));
        $inap_cob = str_replace('"', '', json_encode($data->inap_cob));
        $inap_lakalantas = str_replace('"', '', json_encode($data->inap_lakalantas));
        $inap_nomorlaporanpolisi = str_replace('"', '', json_encode($data->inap_nomorlaporanpolisi));

        $inap_tgl_kll = str_replace('"', '', json_encode($data->inap_tgl_kll));
        $inap_ket_kll = str_replace('"', '', json_encode($data->inap_ket_kll));
        $inap_suplesi = str_replace('"', '', json_encode($data->inap_suplesi));
        $inap_no_suplesi = str_replace('"', '', json_encode($data->inap_no_suplesi));
        $inap_kll_provinsi = str_replace('"', '', json_encode($data->inap_kll_provinsi));
        $inap_kll_kabupaten = str_replace('"', '', json_encode($data->inap_kll_kabupaten));
        $inap_kll_kecamatan = str_replace('"', '', json_encode($data->inap_kll_kecamatan));
        //echo"$inap_lakalantas";exit();
        if (strlen($nosasuransi) < 13) {
            $output['metaData']['message'] = "NOMOR BPJS TIDAK VALID";
            $output['metaData']['code'] = '0';
            $outputx['data']['datax'] =   $output;
            return view('view/modal/rwi/modalsepinap', $outputx);

            echo json_encode($output);
            exit();
        }

        if (isset($tgl_sep)) {
            $bulan_sep = substr($tgl_sep, 5, 2);
            $tahun_sep = substr($tgl_sep, 0, 4);
            $a = $bulan_sep . $tahun_sep;
        } else {
            $tgl_sep = date("Y-m-d");
            $bulan_sep = date("m");
            $tahun_sep = date("Y");
        }
        if (strlen($telp) < 8) {
            $telp = '08123456789';
        }
        if ($id_unit == '' || $id_unit == null || $id_unit == 'null') {
            $output['metaData']['message'] = "Pilih Kamar Rawat Inap Terlebih Dahulu";
            $output['metaData']['code'] = '0';
            $outputx['data']['datax'] =   $output;
            return view('view/modal/rwi/modalsepinap', $outputx);
            exit();
        }

        $buatSPRI = false;
        $kelas = $this->db->query("SELECT map_bpjs FROM unit WHERE id_unit = '" . $id_unit . "'")->getRow()->map_bpjs;
        $url = $this->db->query("SELECT nilai FROM seting_bpjs WHERE key_setting = 'url_2'")->getRow()->nilai;
        $cons_id = $this->db->query("SELECT nilai FROM seting_bpjs WHERE key_setting = 'cons_id_2'")->getRow()->nilai;
        $ppk = $this->db->query("SELECT nilai FROM seting_bpjs WHERE key_setting = 'ppk_2'")->getRow()->nilai;
        $tStamp = $this->getTstamp();
        $headers = $this->getHeader($tStamp);
        $opts = array(
            'http' => array(
                'method' => 'GET',
                'header' => $headers
            )
        );
        // echo"lol$sep";
        //cek sep rajal
        // Validasi: harus 16 digit angka
        if (preg_match('/^\d{16}$/', $sep)) {
            // echo"a";exit();
            $context = stream_context_create($opts);
            $hasil_raw = file_get_contents($url . 'SEP/' . $sep, true, $context);
            $hasil = json_decode($hasil_raw, true);
            if ($hasil['metaData']['code'] <> '200') {
                $output['metaData'] = $hasil['metaData'];
                $output['url'] = $url . 'SEP/' . $sep;
                $outputx['data']['datax'] =   $output;
                $seprajal = false;
                return view('view/modal/rwi/modalsepinap', $outputx);
            }
            $data_sep =  $this->decript($tStamp, $hasil['response']);
            //$data_sep_2 =  $this->decript($tStamp, $hasil['response']);
            $kode_poli = explode(" ", $data_sep["poli"])[0];
            $nokartu = $data_sep["peserta"]["noKartu"];
            $seprajal = true;
            $kelasrawathak = $data_sep["klsRawat"]["klsRawatHak"];
            $tglrujukan_sepinap = $data_sep["tglSep"];
        } else {
            //echo"gak aada sep rajal";exit();
            $context = stream_context_create($opts);
            $nokartu = $nosasuransi;
            //cek kepesertaan by nokartu
            $context = stream_context_create($opts);
            $hasil_raw_kepesertaan = file_get_contents($url . '/Peserta/nokartu/' . $nokartu . '/tglSEP/' . date("Y-m-d"), true, $context);
            $hasil_kepesertaan = json_decode($hasil_raw_kepesertaan, true);
            $data_kepesertaan =  $this->decript($tStamp, $hasil_kepesertaan['response']);
            $kelasrawathak = $data_kepesertaan['peserta']["hakKelas"]["kode"];
            // var_dump($kelasrawathak);exit();
        }




        $hasil_raw = file_get_contents($url . 'RencanaKontrol/ListRencanaKontrol/Bulan/' . $bulan_sep . '/Tahun/' . $tahun_sep . '/Nokartu/' . $nokartu  . '/filter/2', true, $context);
        $hasil = json_decode($hasil_raw, true);
        // var_dump($hasil);
        // exit();

        //$noSPRI = '';
        if ($hasil['metaData']['code'] <> '200') {
            if ($hasil['metaData']['code'] == '201' && $hasil['metaData']['message'] == 'Data Tidak Ditemukan') {
                $buatSPRI = true;
            } else {
                $output['metaData'] = $hasil['metaData'];
                // $output['param'] = json_decode($param, true);
                $output['url'] = $url . 'RencanaKontrol/ListRencanaKontrol/Bulan/' . $bulan_sep . '/Tahun/' . $tahun_sep . '/Nokartu/' . $nokartu  . '/filter/2';
                echo json_encode($output);
                return;
            }
        } else {
            $data_list_kontrol =  $this->decript($tStamp, $hasil['response'])["list"];
            // echo json_encode($data_list_kontrol);exit();
            //var_dump($data_list_kontrol);exit();
            $buatSPRI = true;
            for ($i = 0; $i < count($data_list_kontrol); $i++) {
                $data_kontrol = $data_list_kontrol[$i];
                if ($data_kontrol['namaJnsKontrol'] == 'SPRI' && $data_kontrol['tglRencanaKontrol'] == $tgl_sep) {
                    $noSPRI = $data_kontrol['noSuratKontrol'];
                    $kodedokterspri = $data_kontrol['kodeDokter'];
                    $tglrujukan_sepinap = $data_kontrol['tglRencanaKontrol'];
                    $sep = $data_kontrol['noSuratKontrol'];
                    //echo"$noSPRI";
                    //exit();
                    $buatSPRI = false;
                    $i = count($data_list_kontrol);
                }
            }
        }


        // cek dokter -- belum
        if (isset($poli)) {
            if ($postdpjpDokter == '' || $postdpjpDokter == null) {
                $postdpjpDokter = '0';
                $output['metaData']['code'] = 'X';
                $output['metaData']['message'] = 'Silahkan Pilih Dokter';
                $outputx['data']['datax'] =   $output;
                return view('view/modal/rwi/modalsepinap', $outputx);
                exit;
            }
            $dpjpDokter = $this->db->query("SELECT * FROM pegawai WHERE id_pegawai = '" . $postdpjpDokter . "'")->getRow()->kd_dokter_bpjs;
            $kode_poli = $poli;
        } else {
            if ($seprajal == true) {
                if ($data_sep["dpjp"]["kdDPJP"] == '0') {
                    $dpjpDokter = $data_sep["kontrol"]["kdDokter"];
                } else {
                    $dpjpDokter = $data_sep["dpjp"]["kdDPJP"];
                }
            }
            if ($dpjpDokter == "") {
                $dpjpDokter = $this->db->query("SELECT * FROM pegawai WHERE id_pegawai = '" . $postdpjpDokter . "'")->getRow()->kd_dokter_bpjs;
            }
        }
        // echo"$postdpjpDokter";
        if ($kode_poli == 'IGD') {

            $dpjpDokter = $this->db->query("SELECT * FROM pegawai WHERE id_pegawai = '" . $postdpjpDokter . "'")->getRow()->kd_dokter_bpjs;
            $queryPoli = "SELECT
                                    un.map_bpjs
                                    FROM
                                    dokter_klinik dk
                                    JOIN unit un ON un.id_unit = dk.id_unit 
                                    WHERE
                                    dk.id_pegawai = '" . $postdpjpDokter . "' 
                                    AND un.map_bpjs <> 'IGD' order by map_bpjs desc limit 1 ";
            // echo" $queryPoli ";
            // exit;
            $kode_poli = $this->db->query($queryPoli)->getRow()->map_bpjs;
        }
        //exit();
        //HARDCODESEK
        // $dpjpDokter = '12687';
        // $kode_poli = 'INT';
        //    echo"$kode_poli";exit();
        if ($buatSPRI == false) {
            $dpjpDokter = $kodedokterspri;
        }
        //echo"$dpjpDokter";

        $cek_queryPoli = "SELECT
                                    un.map_bpjs
                                    FROM
                                    dokter_klinik dk
                                    JOIN unit un ON un.id_unit = dk.id_unit 
                                    WHERE
                                    dk.id_pegawai = '" . $postdpjpDokter . "' 
                                    AND un.map_bpjs <> 'IGD' order by map_bpjs desc limit 1 ";
        $cek_kode_poli = $this->db->query($cek_queryPoli)->getRow()->map_bpjs;

        if ($kode_poli <> $cek_kode_poli) {
            $kode_poli = $cek_kode_poli;
        }
        //HARDCODESEK
        if ($buatSPRI == true) {

            $param = '
                                    {
                                       "request":
                                       {
                                         "noKartu":"' .  $nokartu . '",
                                         "kodeDokter":"' . $dpjpDokter . '",
                                         "poliKontrol":"' . $kode_poli . '",
                                         "tglRencanaKontrol":"' . $tgl_sep . '",
                                         "user":"user' . $user . '"
                                     }
                                 }
                                 ';

            $opts = array(
                'http' => array(
                    'method' => "POST",
                    'header' => $headers,
                    'content' => $param
                )
            );

            $context = stream_context_create($opts);

            $hasil_raw = file_get_contents($url . 'RencanaKontrol/InsertSPRI', false, $context);

            $hasil = json_decode($hasil_raw, true);
            if ($hasil['metaData']['code'] <> '200') {
                $output['metaData'] = $hasil['metaData'];
                $output['url'] = $url . 'RencanaKontrol/InsertSPRI';
                $output['param'] = json_decode($param, true);
                $output['pesan'] = "gagal";
                $outputx['data']['datax'] =   $output;
                return view('view/modal/rwi/modalsepinap', $outputx);

                //echo json_encode($output);
                return;
            } else {
                $output['pesan'] = "berhasil";
                echo json_encode($output);
                // return;
            }



            $data_spri = $this->decript($tStamp, $hasil['response']);
            $noSPRI = $data_spri["noSPRI"];
            $sep = $noSPRI;
            $tglrujukan_sepinap = $data_spri["tglRencanaKontrol"];
        }



        // // cek kelas
        // if (($kelas - 2) ==  $kelasrawathak  || $kelas == '6' || $kelas == '7') {
        //     $param_kelas = '
        //                         "klsRawatNaik":"",
        //                         "pembiayaan":"",
        //                         "penanggungJawab":""
        //                         ';
        // } else {
        //     // $param_kelas = '
        //     //                     "klsRawatNaik":"' . $kelas . '",
        //     //                     "pembiayaan":"1",
        //     //                     "penanggungJawab":"Pribadi"
        //     //                     ';
        //     // ATAS REKOMNDASI BPJS ASA DIAKRENAKAN GAGAL JIKA DI ISI VIP / VVIP HARDCODE 8 15/05/2025
        //     $param_kelas = '
        //                         "klsRawatNaik":"8",
        //                         "pembiayaan":"1",
        //                         "penanggungJawab":"Pribadi"
        //                         ';
        // }
        // cek kelas
        if (($kelas - 2) ==  $kelasrawathak  || $kelas == '6' || $kelas == '7') {
            //jika tidak naik
            $param_kelas = '
                                "klsRawatNaik":"",
                                "pembiayaan":"",
                                "penanggungJawab":""
                                ';
        } else {
            if ($kelas == '3' ||  $kelas == '4') {
                //jika naik kelas 1 atau 2
                $param_kelas = '
                                "klsRawatNaik": "' . $kelas . '",
                                "pembiayaan":"1",
                                "penanggungJawab":"Pribadi"
                                ';
            } else {
                // jika naik diatas kelas 1
                // ATAS REKOMNDASI BPJS ASA DIAKRENAKAN GAGAL JIKA DI ISI VIP / VVIP HARDCODE 8 15/05/2025
                $param_kelas = '
                                "klsRawatNaik":"8",
                                "pembiayaan":"1",
                                "penanggungJawab":"Pribadi"
                                ';
            }
        }

        //echo"$param_kelas";exit();

        $param = '{
                                "request":{
                                 "t_sep":{
                                  "noKartu":"' . $nokartu . '",
                                  "tglSep":"' . $tgl_sep . '",
                                  "ppkPelayanan":"' . $ppk . '",
                                  "jnsPelayanan":"1",
                                  "klsRawat":{
                                   "klsRawatHak":"' .  $kelasrawathak  . '",
                                   ' . $param_kelas . '
                                   },
                                   "noMR":"' . $no_rm . '",
                                   "rujukan":{
                                       "asalRujukan":"2",
                                       "tglRujukan":"' . $tglrujukan_sepinap . '",
                                       "noRujukan":"' . $sep . '",
                                       "ppkRujukan":"' . $ppk . '"
                                       },
                                       "catatan":"SEP Rawat Inap",
                                       "diagAwal":"' . $kode_diagnosa . '",
                                       "poli":{
                                           "tujuan":"",
                                           "eksekutif":"0"
                                           },
                                           "cob":{
                                               "cob":"' . $inap_cob . '"
                                               },
                                               "katarak":{
                                                   "katarak":"0"
                                                   },
                                                   "jaminan":{
                                                       "lakaLantas":"' . $inap_lakalantas . '", 
                                                       "noLP":"' . $inap_nomorlaporanpolisi . '",
                                                       "penjamin":{
                                                        "tglKejadian":"' . $inap_tgl_kll . '",
                                                        "keterangan":"' . $inap_ket_kll . '",
                                                        "suplesi":{
                                                         "suplesi":"' . $inap_suplesi . '",
                                                         "noSepSuplesi":"' . $inap_no_suplesi . '",
                                                         "lokasiLaka":{
                                                          "kdPropinsi":"' . $inap_kll_provinsi . '",
                                                          "kdKabupaten":"' .  $inap_kll_kabupaten . '",
                                                          "kdKecamatan":"' . $inap_kll_kecamatan . '"
                                                      }
                                                  }
                                              }
                                              },
                                              "tujuanKunj":"0",
                                              "flagProcedure":"0",
                                              "kdPenunjang":"",
                                              "assesmentPel":"",
                                              "skdp":{
                                               "noSurat":"' . $noSPRI . '",
                                               "kodeDPJP":"' . $dpjpDokter . '"
                                               },
                                               "dpjpLayan":"",
                                               "noTelp":"' . $telp . '",
                                               "user":"user' . $user . '"
                                           }
                                       }
                                   }
                                   ';
        //echo"$param";exit();
        $opts = array(
            'http' => array(
                'method' => "POST",
                'header' => $headers,
                'content' => $param
            )
        );
        //echo"$param";exit();
        // echo"$dpjpDokter-$kode_poli";
        // $dpjpDokter = '12687';
        // $kode_poli = 'INT';

        $context = stream_context_create($opts);

        $hasil_raw = file_get_contents($url . 'SEP/2.0/insert', false, $context);

        $hasil = json_decode($hasil_raw, true);

        if ($hasil['metaData']['code'] <> '200') {
            $output['metaData'] = $hasil['metaData'];
            $output['param'] = json_decode($param, true);
            $output['url'] = $url . 'SEP/2.0/insert';
            //echo json_encode($output);
            $outputx['data']['datax'] =   $output;
            return view('view/modal/rwi/modalsepinap', $outputx);
        } else {

            $output['metaData'] = $hasil['metaData'];
            $output['response'] = $this->decript($tStamp, $hasil['response']);
            $outputx['data']['datax'] =   $output;
            // response asli bpjs dibawah ini di komen
            //$response = $this->decript($tStamp, $hasil['response']);
            // $response = $this->decript($tStamp, $hasil['response']);
            $outputxx = json_decode(json_encode($outputx), true);
            //var_dump($outputx); echo"<br>";
            return view('view/modal/rwi/modalsepinap', $outputxx);
        }
        // $outputx['metaData'] = $hasil['metaData'];
        // $outputx['no_sep'] = $response['sep']['noSep'];
        // $outputx['no_spri'] = $noSPRI;
        // echo json_encode($outputx);


        //output
        // tinggal html sep


    }
    public function modalsepinap_X()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);


        $data = json_decode($_GET['data']);
        $sep       = str_replace('"', '', json_encode($data->seprajal));
        $telp  = str_replace('"', '', json_encode($data->telp));
        $tgl_sep  = str_replace('"', '', json_encode($data->tglsep));
        $id_unit  = str_replace('"', '', json_encode($data->id_unit));
        $user  = str_replace('"', '', json_encode($data->user));
        $no_rm  = str_replace('"', '', json_encode($data->no_rm));
        $poli  = str_replace('"', '', json_encode($data->poli)); // sudah value poli maping bpjs dari spri simrs
        $postdpjpDokter  = str_replace('"', '', json_encode($data->kd_dokter));
        $kode_diagnosa  = str_replace('"', '', json_encode($data->kd_diagnosa));
        $nosasuransi  = str_replace('"', '', json_encode($data->noasuransi));

        if (isset($tgl_sep)) {
            $bulan_sep = substr($tgl_sep, 5, 2);
            $tahun_sep = substr($tgl_sep, 0, 4);
            $a = $bulan_sep . $tahun_sep;
        } else {
            $tgl_sep = date("Y-m-d");
            $bulan_sep = date("m");
            $tahun_sep = date("Y");
        }
        if (strlen($telp) < 8) {
            $telp = '08123456789';
        }
        if ($id_unit == '' || $id_unit == null || $id_unit == 'null') {
            $output['metaData']['message'] = "Pilih Kamar Rawat Inap Terlebih Dahulu";
            $output['metaData']['code'] = '0';
            $outputx['data']['datax'] =   $output;
            return view('view/modal/rwi/modalsepinap', $outputx);
            exit();
        }

        $buatSPRI = false;
        $kelas = $this->db->query("SELECT map_bpjs FROM unit WHERE id_unit = '" . $id_unit . "'")->getRow()->map_bpjs;
        $url = $this->db->query("SELECT nilai FROM seting_bpjs WHERE key_setting = 'url_2'")->getRow()->nilai;
        $cons_id = $this->db->query("SELECT nilai FROM seting_bpjs WHERE key_setting = 'cons_id_2'")->getRow()->nilai;
        $ppk = $this->db->query("SELECT nilai FROM seting_bpjs WHERE key_setting = 'ppk_2'")->getRow()->nilai;
        $tStamp = $this->getTstamp();
        $headers = $this->getHeader($tStamp);
        $opts = array(
            'http' => array(
                'method' => 'GET',
                'header' => $headers
            )
        );

        //cek sep rajal
        if (!empty($sep) || $sep <> '0') {

            $context = stream_context_create($opts);
            $hasil_raw = file_get_contents($url . 'SEP/' . $sep, true, $context);
            $hasil = json_decode($hasil_raw, true);
            if ($hasil['metaData']['code'] <> '200') {
                $output['metaData'] = $hasil['metaData'];
                $output['url'] = $url . 'SEP/' . $sep;
                $outputx['data']['datax'] =   $output;
                return view('view/modal/rwi/modalsepinap', $outputx);
            }
            $data_sep =  $this->decript($tStamp, $hasil['response']);
            $data_sep_2 =  $this->decript($tStamp, $hasil['response']);
            $kode_poli = explode(" ", $data_sep_2["poli"])[0];
            $nokartu = $data_sep["peserta"]["noKartu"];
        } else {
            $context = stream_context_create($opts);
            $nokartu = $nosasuransi;
        }


        $hasil_raw = file_get_contents($url . 'RencanaKontrol/ListRencanaKontrol/Bulan/' . $bulan_sep . '/Tahun/' . $tahun_sep . '/Nokartu/' . $nokartu  . '/filter/2', true, $context);
        $hasil = json_decode($hasil_raw, true);
        //var_dump($hasil);exit();

        echo "$a";
        exit();

        $noSPRI = '';
        if ($hasil['metaData']['code'] <> '200') {
            if ($hasil['metaData']['code'] == '201' && $hasil['metaData']['message'] == 'Data Tidak Ditemukan') {
                $buatSPRI = true;
            } else {
                $output['metaData'] = $hasil['metaData'];
                // $output['param'] = json_decode($param, true);
                $output['url'] = $url . 'RencanaKontrol/ListRencanaKontrol/Bulan/' . $bulan_sep . '/Tahun/' . $tahun_sep . '/Nokartu/' . $nokartu  . '/filter/2';
                echo json_encode($output);
                return;
            }
        } else {
            $data_list_kontrol =  $this->decript($tStamp, $hasil['response'])["list"];
            //var_dump($data_list_kontrol);exit();
            $buatSPRI = true;
            for ($i = 0; $i < count($data_list_kontrol); $i++) {
                $data_kontrol = $data_list_kontrol[$i];
                if ($data_kontrol['namaJnsKontrol'] == 'SPRI' && $data_kontrol['tglRencanaKontrol'] == $tgl_sep) {
                    $noSPRI = $data_kontrol['noSuratKontrol'];
                    $kodedokterspri = $data_kontrol['kodeDokter'];
                    //echo"$noSPRI";
                    //exit();
                    $buatSPRI = false;
                    $i = count($data_list_kontrol);
                }
            }
        }


        // cek dokter -- belum
        if (isset($poli)) {
            if ($postdpjpDokter == '' || $postdpjpDokter == null) {
                $postdpjpDokter = '0';
                $output['metaData']['code'] = 'X';
                $output['metaData']['message'] = 'Silahkan Pilih Dokter';
                $outputx['data']['datax'] =   $output;
                return view('view/modal/rwi/modalsepinap', $outputx);
                exit;
            }
            $dpjpDokter = $this->db->query("SELECT * FROM pegawai WHERE id_pegawai = '" . $postdpjpDokter . "'")->getRow()->kd_dokter_bpjs;
            $kode_poli = $poli;
        } else {

            if ($data_sep["dpjp"]["kdDPJP"] == '0') {
                $dpjpDokter = $data_sep["kontrol"]["kdDokter"];
            } else {
                $dpjpDokter = $data_sep["dpjp"]["kdDPJP"];
            }

            if ($dpjpDokter == "") {
                $dpjpDokter = $this->db->query("SELECT * FROM pegawai WHERE id_pegawai = '" . $postdpjpDokter . "'")->getRow()->kd_dokter_bpjs;
            }
        }
        // echo"$postdpjpDokter";
        if ($kode_poli == 'IGD') {
            // echo"halo";
            // exit();
            $dpjpDokter = $this->db->query("SELECT * FROM pegawai WHERE id_pegawai = '" . $postdpjpDokter . "'")->getRow()->kd_dokter_bpjs;
            $queryPoli = "SELECT
                                    un.map_bpjs
                                    FROM
                                    dokter_klinik dk
                                    JOIN unit un ON un.id_unit = dk.id_unit 
                                    WHERE
                                    dk.id_pegawai = '" . $postdpjpDokter . "' 
                                    AND un.map_bpjs <> 'IGD'";
            // echo" $queryPoli ";
            // exit;
            $kode_poli = $this->db->query($queryPoli)->getRow()->map_bpjs;
        }
        //exit();
        //HARDCODESEK
        // $dpjpDokter = '12687';
        // $kode_poli = 'INT';

        if ($buatSPRI == false) {
            $dpjpDokter = $kodedokterspri;
        }
        //echo"$dpjpDokter";
        //HARDCODESEK

        if ($buatSPRI) {
            $param = '
                                    {
                                       "request":
                                       {
                                         "noKartu":"' . $data_sep["peserta"]["noKartu"] . '",
                                         "kodeDokter":"' . $dpjpDokter . '",
                                         "poliKontrol":"' . $kode_poli . '",
                                         "tglRencanaKontrol":"' . $tgl_sep . '",
                                         "user":"user' . $user . '"
                                     }
                                 }
                                 ';

            $opts = array(
                'http' => array(
                    'method' => "POST",
                    'header' => $headers,
                    'content' => $param
                )
            );

            $context = stream_context_create($opts);

            $hasil_raw = file_get_contents($url . 'RencanaKontrol/InsertSPRI', false, $context);

            $hasil = json_decode($hasil_raw, true);
            if ($hasil['metaData']['code'] <> '200') {
                $output['metaData'] = $hasil['metaData'];
                $output['url'] = $url . 'RencanaKontrol/InsertSPRI';
                $output['param'] = json_decode($param, true);
                $output['pesan'] = "gagal";
                $outputx['data']['datax'] =   $output;
                return view('view/modal/rwi/modalsepinap', $outputx);

                //echo json_encode($output);
                return;
            } else {
                $output['pesan'] = "berhasil";
                echo json_encode($output);
                // return;
            }



            $data_spri = $this->decript($tStamp, $hasil['response']);
            $noSPRI = $data_spri["noSPRI"];
        }

        // cek kelas

        if (($kelas - 2) == $data_sep["klsRawat"]["klsRawatHak"] || $kelas == '6' || $kelas == '7') {
            $param_kelas = '
                                "klsRawatNaik":"",
                                "pembiayaan":"",
                                "penanggungJawab":""
                                ';
        } else {
            $param_kelas = '
                                "klsRawatNaik":"' . $kelas . '",
                                "pembiayaan":"1",
                                "penanggungJawab":"Pribadi"
                                ';
        }

        $param = '{
                                "request":{
                                 "t_sep":{
                                  "noKartu":"' . $data_sep["peserta"]["noKartu"] . '",
                                  "tglSep":"' . $tgl_sep . '",
                                  "ppkPelayanan":"' . $ppk . '",
                                  "jnsPelayanan":"1",
                                  "klsRawat":{
                                   "klsRawatHak":"' . $data_sep["klsRawat"]["klsRawatHak"] . '",
                                   ' . $param_kelas . '
                                   },
                                   "noMR":"' . $no_rm . '",
                                   "rujukan":{
                                       "asalRujukan":"2",
                                       "tglRujukan":"' . $data_sep["tglSep"] . '",
                                       "noRujukan":"' . $sep . '",
                                       "ppkRujukan":"' . $ppk . '"
                                       },
                                       "catatan":"SJP RWI",
                                       "diagAwal":"' . $kode_diagnosa . '",
                                       "poli":{
                                           "tujuan":"",
                                           "eksekutif":"0"
                                           },
                                           "cob":{
                                               "cob":"' . $data_sep["cob"] . '"
                                               },
                                               "katarak":{
                                                   "katarak":"' . $data_sep["katarak"] . '"
                                                   },
                                                   "jaminan":{
                                                       "lakaLantas":"' . $data_sep["kdStatusKecelakaan"] . '",
                                                       "penjamin":{
                                                        "tglKejadian":"' . $data_sep["lokasiKejadian"]["tglKejadian"] . '",
                                                        "keterangan":"' . $data_sep["lokasiKejadian"]["ketKejadian"] . '",
                                                        "suplesi":{
                                                         "suplesi":"0",
                                                         "noSepSuplesi":"",
                                                         "lokasiLaka":{
                                                          "kdPropinsi":"' . $data_sep["lokasiKejadian"]["kdProp"] . '",
                                                          "kdKabupaten":"' . $data_sep["lokasiKejadian"]["kdProp"] . '",
                                                          "kdKecamatan":"' . $data_sep["lokasiKejadian"]["kdKec"] . '"
                                                      }
                                                  }
                                              }
                                              },
                                              "tujuanKunj":"0",
                                              "flagProcedure":"",
                                              "kdPenunjang":"",
                                              "assesmentPel":"",
                                              "skdp":{
                                               "noSurat":"' . $noSPRI . '",
                                               "kodeDPJP":"' . $dpjpDokter . '"
                                               },
                                               "dpjpLayan":"",
                                               "noTelp":"' . $telp . '",
                                               "user":"user' . $user . '"
                                           }
                                       }
                                   }
                                   ';
        $opts = array(
            'http' => array(
                'method' => "POST",
                'header' => $headers,
                'content' => $param
            )
        );

        // echo"$dpjpDokter-$kode_poli";
        // $dpjpDokter = '12687';
        // $kode_poli = 'INT';

        $context = stream_context_create($opts);

        $hasil_raw = file_get_contents($url . 'SEP/2.0/insert', false, $context);

        $hasil = json_decode($hasil_raw, true);

        if ($hasil['metaData']['code'] <> '200') {
            $output['metaData'] = $hasil['metaData'];
            $output['param'] = json_decode($param, true);
            $output['url'] = $url . 'SEP/2.0/insert';
            //echo json_encode($output);
            $outputx['data']['datax'] =   $output;
            return view('view/modal/rwi/modalsepinap', $outputx);
        } else {

            $output['metaData'] = $hasil['metaData'];
            $output['response'] = $this->decript($tStamp, $hasil['response']);
            $outputx['data']['datax'] =   $output;
            // response asli bpjs dibawah ini di komen
            //$response = $this->decript($tStamp, $hasil['response']);
            // $response = $this->decript($tStamp, $hasil['response']);
            $outputxx = json_decode(json_encode($outputx), true);
            //var_dump($outputx); echo"<br>";
            return view('view/modal/rwi/modalsepinap', $outputxx);
        }
        // $outputx['metaData'] = $hasil['metaData'];
        // $outputx['no_sep'] = $response['sep']['noSep'];
        // $outputx['no_spri'] = $noSPRI;
        // echo json_encode($outputx);


        //output
        // tinggal html sep


    }
    public function DeleteSEP()
    {

        $input      = json_decode(file_get_contents('php://input'));
        $tStamp     = $this->tStamp();
        $sep        = $input->sep;
        $tgl        = date('Y-m-d');
        $method     = 'DELETE';
        $param      = $this->JsonDeleteSEP($sep);
        $request    = '/SEP/2.0/delete';
        $string     = $this->url($request, $param, $method, $tStamp);
        if ($string->metaData->code != 200) {
            $hasil = array();
            $hasil['status'] = 'gagal';
            $hasil['pesan'] = $string->metaData->message;
            echo json_encode($hasil);
            return;
        }
        $response = $string->response;
        $hasil['data']   = $this->Decrypt($response, $tStamp);
        $hasil['pesan']  = 'Berhasil';
        $hasil['status'] = 'sukses';
        echo json_encode($hasil);
    }


    public function JsonDeleteSEP($id)
    {
        $json = '
                                {
                                    "request": {
                                        "t_sep": {
                                            "noSep": "' . $id . '",
                                            "user": "Coba Ws"
                                        }
                                    }
                                }';
        return $json;
    }
    public function CetakSEPIrna()
    {
        $input = json_decode(file_get_contents('php://input'));
        $SEP   = $_POST['sep'];
        $tStamp = $this->tStamp();
        $method = 'GET';
        $param  = '';
        $request = '/SEP/' . $SEP;
        $string = $this->url($request, $param, $method, $tStamp);
        $response = $string->response;
        $hasil = $this->Decrypt($response, $tStamp);
        $nama = $hasil['peserta']['nama'];
        $peserta = $hasil['peserta']['jnsPeserta'];
        $tglsep = $hasil['tglSep'];
        if ($hasil['peserta']['kelamin'] == 'P') {
            $jk = 'WANITA';
        } else {
            $jk = 'PRIA';
        }
        $kelasRawat = '';
        if ($hasil['jnsPelayanan'] == 'Rawat Inap') {
            $kelasRawat = $hasil['peserta']['hakKelas'];
        }

        $writer = new PngWriter();
        $qrCode = QrCode::create($hasil['peserta']['noKartu'])
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(150)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $result = $writer->write($qrCode);

        $dataUri = $result->getDataUri();

        $html = "<html>
                                <body>
                                <table style='text-align: center;' width=100%>
                                <tr>
                                <td>
                                <img src='_assets/dist/img/darmayu.jpg' height='30'>
                                </td>
                                <td>
                                <div style='font-size:16px;'><b>SURAT ELEGIBILITAS PESERTA</b></div>
                                <div style='font-size:13px;'>RS DARMAYU MADIUN</div>
                                </td>
                                <td>                        
                                <img src='_assets/dist/img/bpjs1.jpg' height='30'>
                                </td>
                                </tr>           
                                </table>

                                <table style='font-size:12px;'>
                                <tr>
                                <td width='30mm'>
                                No. Sep
                                </td>
                                <td>
                                : 
                                </td>
                                <td width='50mm'>
                                <div style='font-size:14px;'><b>" . $SEP . "</b></div>
                                </td>

                                <td width='25mm'>
                                No. Kartu
                                </td>
                                <td>
                                : 
                                </td>
                                <td>
                                <div style='font-size:14px;'><b>" . $hasil['peserta']['noKartu'] . "</b></div>
                                </td>
                                <tr>

                                <tr>
                                <td width='30mm'>
                                Tgl. SEP
                                </td>
                                <td>
                                : 
                                </td>
                                <td width='50mm'>
                                " . $tglsep . "
                                </td>

                                <td width='25mm'>
                                No. Medrec
                                </td>
                                <td>
                                : 
                                </td>
                                <td>
                                <b>'" . $hasil['peserta']['noMr'] . "'</b>
                                </td>
                                <tr>

                                <tr>
                                <td width='30mm'>
                                Peserta
                                </td>
                                <td>
                                : 
                                </td>
                                <td width='50mm'>
                                " . $peserta . "
                                </td>

                                <td width='25mm'>
                                Nama Peserta
                                </td>
                                <td>
                                : 
                                </td>
                                <td>
                                " . $nama . "
                                </td>
                                <tr>

                                <tr>
                                <td width='30mm'>
                                COB
                                </td>
                                <td>
                                : 
                                </td>
                                <td width='50mm'>
                                - 
                                </td>

                                <td width='25mm'>
                                Tgl. Lahir
                                </td>
                                <td>
                                : 
                                </td>
                                <td>
                                " . $hasil['peserta']['tglLahir'] . "
                                </td>
                                <tr>

                                <tr>
                                <td width='30mm'>
                                Jns. Rawat
                                </td>
                                <td>
                                : 
                                </td>
                                <td width='50mm'>
                                " . $hasil['jnsPelayanan'] . "
                                </td>

                                <td width='25mm'>
                                Jns. Kelamin
                                </td>
                                <td>
                                : 
                                </td>
                                <td>
                                " . $jk . "
                                </td>
                                <tr>

                                <tr>
                                <td width='30mm'>
                                Kls. Rawat
                                </td>
                                <td>
                                : 
                                </td>
                                <td width='50mm'>
                                " . $kelasRawat . "
                                </td>

                                <td width='25mm'>
                                Kelas Rawat
                                </td>
                                <td>
                                : 
                                </td>
                                <td>
                                " . $hasil['kelasRawat'] . "
                                </td>
                                <tr>


                                <tr>
                                <td width='30mm'>
                                
                                </td>
                                <td>
                                
                                </td>
                                <td width='50mm'>

                                </td>

                                <td width='25mm'>
                                Diagnosa Awal
                                </td>
                                <td>
                                : 
                                </td>
                                <td>
                                " . $hasil['diagnosa'] . "
                                </td>
                                <tr>

                                <tr>
                                <td width='30mm'>
                                Catatan
                                </td>
                                <td>
                                : 
                                </td>
                                <td width='50mm'>
                                <div style='font-size:14px;'><b>" . $hasil['catatan'] . "</b></div>
                                </td>
                                <td></td>
                                <td>:</td>
                                <td></td>
                                <tr>


                                </table>

                                <table style='font-size:10px;'>
                                <tr>
                                <td width='100mm'>
            *Saya Menyetujui BPJS Kesehatan menggunakan informasi Medis Pasien jika diperlukan<br>
            *SEP bukan sebagai bukti penjamin peserta<br>
                                Cetakan Ke 1 : " . str_pad(gmdate("d-M-Y H:i:s", time() + 60 * 61 * 7), 71, " ") . "
                                </td>
                                <td width='40mm' style='text-align: right;font-size:12px;'>
                                Pasien / Keluarga Pasien
                                <img src='" . $dataUri . "' width='50' height='50'>
                                </td>
                                </tr>
                                </table>
                                </body>
                                </html>";
        //echo $html;
        $mpdf   = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'A4',
            'format' => [160, 80]
        ]);
        //$mpdf=new \Mpdf\Mpdf('utf-8', array(160,80));
        $mpdf->AddPage(
            'P', // L - landscape, P - portrait
            '',
            '',
            '',
            '',
            2, // margin_left
            2, // margin right
            2, // margin top
            2, // margin bottom
            0, // margin header
            2
        ); // margin footer

        $mpdf->WriteHTML($html);
        $mpdf->Output("cetak.pdf", 'I');
        exit;
        //echo $html;
        //$mpdf->Output('sep_termal.pdf', 'I');
        //$klien = str_replace(".","",$_SERVER['REMOTE_ADDR']);
        //$mpdf->Output($klien.'sep_termal.pdf', 'I');


    }
    public function createbedbpjs()
    {
        //echo"a";exit();
        /**
     Setting database connection
         */

        //$con = pg_connect("host=103.184.180.97 port=5432 dbname=db_madiun user='postgres' password='darmayu'");
        /**
    Initialize API header parameters
         */
        $consid    = "21780";
        $secretKey = "1hDF0B4057";
        $ipclient = $_SERVER['REMOTE_ADDR'];
        // echo"$ipclient";
        // exit();
        if ($ipclient === "::1" || $ipclient === "127.0.0.1"  || $ipclient === "192.168.1.72" || $ipclient === "103.175.25.4" ||  $ipclient === "103.47.133.91" ||  $ipclient === "103.175.25.4" ||  $ipclient === "36.67.108.193" || $ipclient = "103.47.133.163") {

            // Do query's
            // @pg_query($con, "SET CHARACTER SET utf8");

            // @pg_query($con, "SET NAMES 'utf8'");

            //             $query = pg_query($con, "SELECT
            //     vkam.kodekelas_v AS kodekelas,
            //     vkam.koderuang_v AS koderuang,
            //     vkam.namaruang_v AS namaruang,
            //     SUM ( kam.jumlah_bed ) AS kapasitas,
            // -- SUM ( kam.digunakan ) AS jumdigunakan,
            // -- SUM ( kam.rusak ) AS jumrusak,
            //     SUM ( kam.jumlah_bed - digunakan - rusak ) AS tersedia,
            //     0 AS tersediapria,
            //     0 AS tersediawanita,
            //     0 AS tersediapriawanita 
            // FROM
            //     kamar kam
            //     JOIN v_kamarbpjs vkam ON vkam.namaruang_v = kam.kamar_bpjs and vkam.aktif='t'
            // GROUP BY
            //     vkam.namaruang_v,
            //     vkam.kodekelas_v,
            //     vkam.koderuang_v");

            $query = "SELECT
        vkam.kodekelas_v AS kodekelas,
        vkam.koderuang_v AS koderuang,
        vkam.namaruang_v AS namaruang,
        SUM ( kam.jumlah_bed ) AS kapasitas,
            
            SUM ( kam.jumlah_bed - digunakan - rusak ) AS tersedia,
            0 AS tersediapria,
            0 AS tersediawanita,
            0 AS tersediapriawanita 
            FROM
            kamar kam
            JOIN v_kamarbpjs vkam ON vkam.namaruang_v = kam.kamar_bpjs and vkam.aktif='t'
	        --WHERE kodekelas_v in ('SAL') and namaruang_v='COBA KAMAR BPJS'
            WHERE kam.show_table = 'f'
            GROUP BY
            vkam.namaruang_v,
            vkam.kodekelas_v,
            vkam.koderuang_v";
            $row1 = $this->db->query($query);

            // Start of loop process

            foreach ($row1->getResult()  as $row) {
                // create record to JSON 
                //echo "$row";
                $data = json_encode($row);
                //echo"$data";exit();
                // Computes the timestamp
                date_default_timezone_set('UTC');
                $tStamp           = strval(time() - strtotime('1970-01-01 00:00:00'));
                // Computes the signature by hashing the salt with the secret key as the key
                $signature        = hash_hmac('sha256', $consid . "&" . $tStamp, $secretKey, true);
                // base64 encode…
                $encodedSignature = base64_encode($signature);

                $ch      = curl_init();
                $headers = array(
                    'X-cons-id: ' . $consid . '',
                    'X-timestamp: ' . $tStamp . '',
                    'X-signature: ' . $encodedSignature . '',
                    'Content-Type: Application/JSON',
                    'Accept: Application/JSON'
                );


                /** 
         Sending record to API Aplicares (for INSERT)
                 */
                curl_setopt($ch, CURLOPT_URL, "https://new-api.bpjs-kesehatan.go.id/aplicaresws/rest/bed/create/0216R010");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $content = curl_exec($ch);
                $err     = curl_error($ch);

                print_r($err);
                print_r($content);

                // close cURL resource, and free up system resources
                curl_close($ch);
            }
            // End of loop process
        } else {
            echo "ip tidak terjangkau";
        }
    }

    public function deletebedbpjs()
    {

        /**
    Initialize API header parameters
         */
        $consid    = "21780";
        $secretKey = "1hDF0B4057";
        $ipclient = $_SERVER['REMOTE_ADDR'];
        //echo"$ipclient";

        if ($ipclient === "::1" || $ipclient === "127.0.0.1"  || $ipclient === "192.168.1.72" || $ipclient === "103.175.25.4" || $ipclient === "103.47.133.91" || $ipclient === "36.67.108.193" || $ipclient = "103.47.133.163") {

            $query = "SELECT
        vkam.kodekelas_v AS kodekelas,
        vkam.koderuang_v AS koderuang,
        vkam.namaruang_v AS namaruang,
        SUM ( kam.jumlah_bed ) AS kapasitas,
        SUM ( kam.jumlah_bed - digunakan - rusak ) AS tersedia,
        0 AS tersediapria,
        0 AS tersediawanita,
        0 AS tersediapriawanita 
        FROM
        kamar kam
        JOIN v_kamarbpjs vkam ON vkam.namaruang_v = kam.kamar_bpjs and vkam.aktif='t'
        --WHERE kodekelas_v in ('SAL') and namaruang_v='COBA KAMAR BPJS'
        WHERE kam.show_table = 'f'
        GROUP BY
        vkam.namaruang_v,
        vkam.kodekelas_v,
        vkam.koderuang_v";

            //$query="SELECT * from temp_bedbpjs";



            $row1 = $this->db->query($query);

            // Start of loop process

            foreach ($row1->getResult()  as $row) {
                // create record to JSON 
                $data = json_encode($row);
                //echo"$data";exit();
                // Computes the timestamp
                date_default_timezone_set('UTC');
                $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
                // Computes the signature by hashing the salt with the secret key as the key
                $signature = hash_hmac('sha256', $consid . "&" . $tStamp, $secretKey, true);
                // base64 encode…
                $encodedSignature = base64_encode($signature);

                $ch = curl_init();
                $headers = array(
                    'X-cons-id: ' . $consid . '',
                    'X-timestamp: ' . $tStamp . '',
                    'X-signature: ' . $encodedSignature . '',
                    'Content-Type: Application/JSON',
                    'Accept: Application/JSON'
                );


                /** 
              Sending record to API Aplicares (for DELETE)
                 */
                curl_setopt($ch, CURLOPT_URL, "https://new-api.bpjs-kesehatan.go.id/aplicaresws/rest/bed/delete/0216R010");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $content = curl_exec($ch);
                $err = curl_error($ch);

                print_r($err);
                print_r($content);

                // close cURL resource, and free up system resources
                curl_close($ch);
            }
            // End of loop process
            // echo "yyy";
        } else {
            echo "ip tidak terjangkau";
        }
    }

    public function updatebedbpjs()
    {
        /**
     Setting database connection
         */

        /**
    Initialize API header parameters
         */
        $consid    = "21780";
        $secretKey = "1hDF0B4057";
        $ipclient = $_SERVER['REMOTE_ADDR'];
        //echo"$ipclient";exit();

        if ($ipclient === "::1" || $ipclient === "127.0.0.1"  || $ipclient === "192.168.1.72" || $ipclient === "103.175.25.4" ||  $ipclient === "103.47.133.91" || $ipclient === "192.168.1.2"  || $ipclient === "36.67.108.193") {

            /**
  Initialize API header parameters
             */



            // Do query's
            // pg_query($con, "SET CHARACTER SET utf8");

            //pg_query($con, "SET NAMES 'utf8'");

            $query = "SELECT
        vkam.kodekelas_v as kodekelas,
        vkam.koderuang_v as koderuang,
        vkam.namaruang_v as namaruang,
        SUM ( kam.jumlah_bed ) AS kapasitas,
        SUM ( kam.jumlah_bed - digunakan - rusak ) AS tersedia, 
        0 as tersediapria,
        0 as tersediawanita,
        0 as tersediapriawanita
        FROM	
        kamar kam
        join v_kamarbpjs vkam on vkam.namaruang_v=kam.kamar_bpjs and vkam.aktif='t'
        --WHERE kodekelas_v in ('SAL') and namaruang_v='COBA KAMAR BPJS'
        WHERE kam.show_table = 'f'
        GROUP BY vkam.namaruang_v,vkam.kodekelas_v,vkam.koderuang_v";
            $row1 = $this->db->query($query);

            // Start of loop process

            foreach ($row1->getResult()  as $row) {
                // create record to JSON 
                $data = json_encode($row);
                //echo"$data";exit();
                // Computes the timestamp
                date_default_timezone_set('UTC');
                //date_default_timezone_set('Asia/Jakarta');
                $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
                // Computes the signature by hashing the salt with the secret key as the key
                $signature = hash_hmac('sha256', $consid . "&" . $tStamp, $secretKey, true);
                // base64 encode…
                $encodedSignature = base64_encode($signature);

                $ch = curl_init();
                $headers = array(
                    'X-cons-id: ' . $consid . '',
                    'X-timestamp: ' . $tStamp . '',
                    'X-signature: ' . $encodedSignature . '',
                    'Content-Type: Application/JSON',
                    'Accept: Application/JSON'
                );


                /** 
      Sending record to API Aplicares (for UPDATE)
                 */
                curl_setopt($ch, CURLOPT_URL, "https://new-api.bpjs-kesehatan.go.id/aplicaresws/rest/bed/update/0216R010");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $content = curl_exec($ch);
                $err = curl_error($ch);

                print_r($err);
                print_r($content);

                // close cURL resource, and free up system resources
                curl_close($ch);
            }
            // End of loop process

        } else {
            echo "ip tidak terjangkau";
        }
    }

    public function viewbedbpjs()
    {
        // echo "a";
        // exit();

        return view('view/modal/rwi/viewbedbpjs');
    }
    public function Getdokter($datawal, $limit)
    {
        // echo"a";exit();
        // $input    = json_decode(file_get_contents('php://input'));
        $tStamp = $this->tStamp();

        $method = 'GET';
        $param  = '';
        // Parameter 1 : Row data awal yang akan ditampilkan
        // Parameter 2 : Limit jumlah data yang akan ditampilkan
        //$request = 'dokter/' . $param1 . '/' . $param2');
        $request = 'referensi/dokter/' . $datawal . '/' . $limit;
        // echo"ax";exit();

        $string = $this->url($request, $param, $method, $tStamp);
        //echo"a";exit();



        if ($string->metaData->code != 200) {
            $hasil = array();
            $hasil['status']    = 'gagal';
            $hasil['pesan']     = $string->metaData->message;
        } else {
            $hasil    = $this->Decrypt($string->response, $tStamp);;
            $hasil['status']    = 'sukses';
            //$hasil['pesan']     = 'Berhasil';
        }
        echo json_encode($hasil);
    }
    public function ref($input0 = null, $input1 = null, $input2 = null, $input3 = null)
    {
        $inputSalah = false;

        if ($input0 == 'Diagnosa' && $input1 != null) {
            $request = 'referensi/diagnosa/' . $input1;
        } else if ($input0 == 'Poli' && $input1 != null) {
            $request = 'referensi/poli/' . $input1;
        } else if ($input0 == 'Faskes' && $input1 != null && in_array($input2, ['1', '2'])) {
            $request = 'referensi/faskes/' . $input1 . '/' . $input2;
        } else if ($input0 == 'DPJP' && $input1 != null && in_array($input2, [null, '1', '2'])) {
            if ($input3 == null) {
                $input3 = date('Y-m-d');
            }
            if ($input2 == null) {
                $input2 = '2';
            }
            $request = 'referensi/dokter/pelayanan/' . $input2 . '/tglPelayanan/' . $input3 . '/Spesialis/' . $input1;
        } else if ($input0 == 'Propinsi') {
            $request = 'referensi/propinsi';
        } else if ($input0 == 'Kabupaten' && $input1 != null) {
            $request = 'referensi/kabupaten/propinsi/' . $input1;
        } else if ($input0 == 'Kecamatan' && $input1 != null) {
            $request = 'referensi/kecamatan/kabupaten/' . $input1;
        } else if ($input0 == 'DiagnosaPRB') {
            $request = 'referensi/diagnosaprb';
        } else if ($input0 == 'Tindakan' && $input1 != null) {
            $request = 'referensi/procedure/' . $input1;
        } else if ($input0 == 'KelasRawat') {
            $request = 'referensi/kelasrawat';
        } else if ($input0 == 'Dokter' && $input1 != null) {
            $request = 'referensi/dokter/' . str_replace(' ', '%20', $input1);
        } else if ($input0 == 'Spesialistik') {
            $request = 'referensi/spesialistik';
        } else if ($input0 == 'RuangRawat') {
            $request = 'referensi/ruangrawat';
        } else if ($input0 == 'CaraKeluar') {
            $request = 'referensi/carakeluar';
        } else if ($input0 == 'PascaPulang') {
            $request = 'referensi/pascapulang';
        } else {
            $inputSalah = true;
        }

        if ($inputSalah) {
            $hasil = array();
            $hasil['status'] = 'gagal';
            $hasil['pesan'] = 'Input tidak sesuai';
            echo json_encode($hasil);
            return;
        }

        $tStamp     = $this->tStamp();
        $method     = 'GET';
        $param      = '';
        $string     = $this->url($request, $param, $method, $tStamp);
        if ($string->metaData->code != 200) {
            $hasil = array();
            $hasil['status'] = 'gagal';
            $hasil['pesan'] = $string->metaData->message;
            echo json_encode($hasil);
            return;
        }
        $response = $string->response;
        $hasil['data']   = $this->Decrypt($response, $tStamp);
        $hasil['pesan']  = 'Berhasil';
        $hasil['status'] = 'sukses';
        echo json_encode($hasil);
    }

    public function rencanakontrolGet($input1, $input2, $input3)
    {
        //{BASE URL}/{Service Name}/RencanaKontrol/ListRencanaKontrol/tglAwal/{parameter 1}/tglAkhir/{parameter 2}/filter/{parameter 3}
        // arameter 1: Tanggal awal format : yyyy-MM-dd

        // Parameter 2: Tanggal akhir format : yyyy-MM-dd

        // Parameter 3: Format filter --> 1: tanggal entri, 2: tanggal rencana kontrol
        // echo"a";exit();
        // $input    = json_decode(file_get_contents('php://input'));
        $tStamp = $this->tStamp();

        $method = 'GET';
        $param  = '';
        // Parameter 1 : Row data awal yang akan ditampilkan
        // Parameter 2 : Limit jumlah data yang akan ditampilkan
        //$request = 'dokter/' . $param1 . '/' . $param2');
        $request = 'referensi/dokter/' . $datawal . '/' . $limit;
        $request = "/RencanaKontrol/ListRencanaKontrol/tglAwal/" . $input1 . "/tglAkhir/" . $input2 . "/filter/" . $input3 . "";

        // echo"ax";exit();

        $string = $this->url($request, $param, $method, $tStamp);
        //echo"a";exit();



        if ($string->metaData->code != 200) {
            $hasil = array();
            $hasil['status']    = 'gagal';
            $hasil['pesan']     = $string->metaData->message;
        } else {
            $hasil    = $this->Decrypt($string->response, $tStamp);;
            $hasil['status']    = 'sukses';
            //$hasil['pesan']     = 'Berhasil';
        }
        echo json_encode($hasil);
    }
    public function carisepeklaim()
    {
        $input = json_decode(file_get_contents('php://input'));
        $SEP   = $input->sep;
        $tStamp = $this->tStamp();
        $method = 'GET';
        $param  = '';
        $request = '/SEP/' . $SEP;
        $string = $this->url($request, $param, $method, $tStamp);
        if ($string == null) {
            $hasil = array();
            $hasil['status'] = 'gagal';
            $hasil['pesan'] = 'error jaringan';
            echo json_encode($hasil);
            return;
        }
        $response = $string->response;
        $hasil = $this->Decrypt($response, $tStamp);
        $hasil['status'] = 'sukses';
        echo json_encode($hasil);
    }
    public function caripesertabpjs()
    {
        echo "ss";
        exit();
        $input = json_decode(file_get_contents('php://input'));
        $SEP   = $_POST['sep'];
        $tStamp = $this->tStamp();
        $method = 'GET';
        $param  = '';
        // $request = '/SEP/' . $SEP;
        $request = 'Peserta/nokartu/0000786850244/tglSEP/2025-02-21';

        $string = $this->url($request, $param, $method, $tStamp);
        $response = $string->response;
        $hasil = $this->Decrypt($response, $tStamp);
        echo json_encode($hasil);
    }
}
