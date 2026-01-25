<?php

//defined('BASEPATH') OR exit('No direct script access allowed');
namespace App\Controllers;

use CodeIgniter\Controller;

class Bridging_apotek extends BaseController
{
	public function __construct()
	{
		require_once(APPPATH . '/../vendor/autoload.php');
		date_default_timezone_set("Asia/Jakarta");
		$this->db =  db_connect();
	}

	public function index(){
		echo "TESTING";
	}

	public function tStamp()
	{
		$tStamp = strval(time() - strtotime('1970-01-01 07:00:00'));
		return $tStamp;
	}
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
		// $string = json_decode(file_get_contents('https://apijkn-dev.bpjs-kesehatan.go.id/apotek-rest-dev' . $request, true, $context));
		$string = json_decode(file_get_contents('https://apijkn.bpjs-kesehatan.go.id/apotek-rest' . $request, true, $context));

		return $string;
	}
	public function getSignatureVedikaBaru($tStamp)
	{
		#PO
		// $data = "25648";
		// $secretKey = "4nLBDBAD5C";
		// $user_key = "6a6ee894eae9232d1170ef87c120edf2";
        
        $data = "14321";
		$secretKey = "5iW20C7181";
		$user_key = "5d50b7d8b05f3207171d1bad86e8ea73";
        
		$signature = hash_hmac('sha256', $data . "&" . $tStamp, $secretKey, true);
		$encodedSignature = base64_encode($signature);
		return array("X-Cons-ID: " . $data, "X-Timestamp: " . $tStamp, "X-Signature: " . $encodedSignature, "user_key:" . $user_key, "Content-Type: application/x-www-form-urlencoded\r\n");
	}
	public function Decrypt($response, $tStamp)
	{
		#PO
		// $data = "25648";
		// $secretKey = "4nLBDBAD5C";
		// $user_key = "6a6ee894eae9232d1170ef87c120edf2";
        
        $data = "14321";
		$secretKey = "5iW20C7181";
		$user_key = "5d50b7d8b05f3207171d1bad86e8ea73";
        
		$key = $data . $secretKey . $tStamp;
		$encrypt_method = 'AES-256-CBC';
		$key_hash = hex2bin(hash('sha256', $key));
		$iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
		$output = openssl_decrypt(base64_decode($response), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
		$hasil = json_decode(\LZCompressor\LZString::decompressFromEncodedURIComponent($output), true);
		return $hasil;
	}
	public function JsonInsertObatNonRacikan($nosjp,$noresep,$kdobat,$nmobat,$signa1,$signa2,$jmlobat,$jho,$ket)
	{
		$json = ' {
			"NOSJP"		: '.$nosjp.',
			"NORESEP"	: '.$noresep.',
			"KDOBT"		: '.$kdobat.',
			"NMOBAT"	: '.$nmobat.',
			"SIGNA1OBT"	: '.$signa1.',
			"SIGNA2OBT"	: '.$signa2.',
			"JMLOBT"	: '.$jmlobat.',
			"JHO"		: '.$jho.',
			"CatKhsObt"	: '.$ket.'
		}   
		';
		
		return $json;
	}
	public function JsonInsertObatRacikan($nosjp,$noresep,$jnsobat,$kdobat,$nmobat,$signa1,$signa2,$permintaan,$jmlobat,$jho,$ket)
	{
		$json = ' {
			"NOSJP": "'.$nosjp.'",
			"NORESEP": "'.$noresep.'",
			"JNSROBT": "'.$jnsobat.'",
			"KDOBT": "'.$kdobat.'",
			"NMOBAT": "'.$nmobat.'",
			"SIGNA1OBT": '.$signa1.',
			"SIGNA2OBT": '.$signa2.',
			"PERMINTAAN":'.$permintaan.',
			"JMLOBT": '.$jmlobat.',
			"JHO": '.$jho.',
			"CatKhsObt": "'.$ket.'"
		}   
		';

		return $json;
	}

	public function JsonInsertResep($TGLSJP,$REFASALSJP,$POLIRSP,$KDJNSOBAT,$NORESEP,$USER,$TGLRSP,$TGLPELRSP,$KdDokter,$iterasi)
	{
		$json = '   {
			"TGLSJP": "'.$TGLSJP.'",
			"REFASALSJP": "'.$REFASALSJP.'",
			"POLIRSP": "'.$POLIRSP.'",
			"KDJNSOBAT": "'.$KDJNSOBAT.'",
			"NORESEP": "'.$NORESEP.'", 
			"IDUSERSJP": "'.$USER.'",
			"TGLRSP": "'.$TGLRSP.'", 
			"TGLPELRSP": "'.$TGLPELRSP.'",
			"KdDokter": "'.$KdDokter.'",
			"iterasi":"'.$iterasi.'"
		} 
		';

		return $json;
	}

	public function JsonInsertListResep($kdppk, $KdJnsObat, $JnsTgl, $TglMulai, $TglAkhir)
	{
		$json = '{
			"kdppk" 	: "'.$kdppk.'",
			"KdJnsObat" : "'.$KdJnsObat.'",
			"JnsTgl" 	: "'.$JnsTgl.'",
			"TglMulai" 	: "'.$TglMulai.'",
			"TglAkhir" 	: "'.$TglAkhir.'"
		} 
		';

		$jsonx = '{
			"kdppk" 	: "0216A016",
			"KdJnsObat" : "0",
			"JnsTgl" 	: "TGLPELSJP",
			"TglMulai" 	: "2019-03-01 08:49:45",
			"TglAkhir" 	: "2019-03-31 06:18:33"
		} 
		';
		return $json;
	}

	public function JsonDeleteResep($nosjp, $refasalsjp, $noresep)
	{
		$json = '{
			"nosjp" 	: "'.$nosjp.'",
			"refasalsjp" : "'.$refasalsjp.'",
			"noresep" 	: "'.$noresep.'"
		} 
		';

		return $json;
	}

	/*--------- RESEP ---------*/
	public function insertresep()
	{
		$input       = json_decode(file_get_contents('php://input'));
		/*
		$TGLSJP      = $input->tglsjp;
		$REFASALSJP  = $input->sepasal;
		$POLIRSP     = $input->poli;
		$KDJNSOBAT   = $input->jnsobat;
		$NORESEP     = $input->noresep;
		$IDUSERSJP   = $input->user;
		$TGLRSP      = $input->tglrsp;
		$TGLPELRSP   = $input->tglpelrsp;
		$KdDokter    = $input->kddokter;
		$iterasi     = $input->iterasi;
		*/

		$TGLSJP      = date('Y-m-d H:i:s');
		$REFASALSJP  = '1202R0010318V000092';
		$POLIRSP     = 'INT';
		$KDJNSOBAT   = 1; //(1. Obat PRB, 2. Obat Kronis Blm Stabil, 3. Obat Kemoterapi)
		$NORESEP     = 25843;
		$IDUSERSJP   = "USR-01";
		$TGLRSP      = date('Y-m-d H:i:s');
		$TGLPELRSP   = date('Y-m-d H:i:s');
		$KdDokter    = 0;
		$iterasi     = 0; //(0. Non Iterasi, 1. Iterasi)

		$tStamp      = $this->tStamp();
		$tgl         = date('Y-m-d');
		$method      = 'POST';
		$param       = $this->JsonInsertResep($TGLSJP,$REFASALSJP,$POLIRSP,$KDJNSOBAT,$NORESEP,$IDUSERSJP,$TGLRSP,$TGLPELRSP,$KdDokter,$iterasi);
		$request     = '/sjpresep/v3/insert';
		// http://localhost/clone/DartoMakanYuyu/cross_ci3/Bridging_apotek/insertresep
		$string      = $this->url($request, $param, $method, $tStamp);
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

	public function insertlistresep()
    {

        $input       = json_decode(file_get_contents('php://input'));
		/*
		$kdppk      = $input->kdppk;
		$KdJnsObat  = $input->KdJnsObat;
		$JnsTgl     = $input->JnsTgl;
		$TglMulai   = $input->TglMulai;
		$TglAkhir   = $input->TglAkhir;
		*/

		$kdppk      = '0182A007';
		$KdJnsObat  = '0';
		$JnsTgl     = 'TGLPELSJP';
		$TglMulai   = date('2026-01-23 H:i:s');
		$TglAkhir   = date('Y-m-d H:i:s');

		$tStamp      = $this->tStamp();
		$tgl         = date('Y-m-d');
		$method      = 'POST';
		$param       = $this->JsonInsertListResep($kdppk, $KdJnsObat, $JnsTgl, $TglMulai, $TglAkhir);
		$request     = '/daftarresep';
		// http://localhost/clone/DartoMakanYuyu/cross_ci3/Bridging_apotek/insertlistresep

		$string      = $this->url($request, $param, $method, $tStamp);
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

    public function deleteresep()
    {

        $input       = json_decode(file_get_contents('php://input'));
		/*
		$nosjp      = $input->nosjp;
		$refasalsjp = $input->refasalsjp;
		$noresep    = $input->noresep;
		*/

		$nosjp      = '1202A00201210000032';
		$refasalsjp = '1202R0010121V000325';
		$noresep    = '0SI44';

		$tStamp      = $this->tStamp();
		$tgl         = date('Y-m-d');
		$method      = 'POST';
		$param       = $this->JsonDeleteResep($nosjp, $refasalsjp, $noresep);
		$request     = '/hapusresep';
		// http://localhost/clone/DartoMakanYuyu/cross_ci3/Bridging_apotek/deleteresep

		$string      = $this->url($request, $param, $method, $tStamp);
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
    /*--------- END RESEP ---------*/

    /*--------- PENYIMPANAN OBAT ---------*/
	public function insertobatnonracikan()
	{

		$input       = json_decode(file_get_contents('php://input'));
		/*
		$nosjp       = $input->nosjp;
		$noresep     = $input->noresep;
		$kdobat      = $input->kdobat;
		$nmobat      = $input->nmobat;
		$signa1      = $input->signa1;
		$signa2      = $input->signa2;
		$jmlobat     = $input->jmlobat;
		$jho         = $input->jho;
		$ket         = $input->ket;
		*/

		$nosjp       = '0112A01704190000001';
		$noresep     = '01236';
		$kdobat      = '123456';
		$nmobat      = 'aSAM';
		$signa1      = 1;
		$signa2      = 1;
		$jmlobat     = 1;
		$jho         = 1;
		$ket         = 'tes';
		
		$tStamp      = $this->tStamp();
		$tgl         = date('Y-m-d');
		$method      = 'POST';
		$param       = $this->JsonInsertObatNonRacikan($nosjp,$noresep,$kdobat,$nmobat,$signa1,$signa2,$jmlobat,$jho,$ket);
		$request     = '/obatnonracikan/v3/insert';
		// http://localhost/clone/DartoMakanYuyu/cross_ci3/Bridging_apotek/insertobatnonracikan
		$string      = $this->url($request, $param, $method, $tStamp);
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

	public function insertobatracikan()
	{

		$input       = json_decode(file_get_contents('php://input'));
		/*
		$nosjp       = $input->nosjp;
		$noresep     = $input->noresep;
		$jnsobat     = $input->jnsobat;
		$kdobat      = $input->kdobat;
		$nmobat      = $input->nmobat;
		$signa1      = $input->signa1;
		$signa2      = $input->signa2;
		$permintaan  = $input->permintaan;
		$jmlobat     = $input->jmlobat;
		$jho         = $input->jho;
		$ket         = $input->ket;
		*/
		
		$nosjp       = '0112A01704190000001';
		$noresep     = '01236';
		$jnsobat     = 'R.01';
		$kdobat      = '123456';
		$nmobat      = 'aSAM';
		$signa1      = 1;
		$signa2      = 1;
		$permintaan  = 1;
		$jmlobat     = 1;
		$jho         = 1;
		$ket         = 'RACIKAN 1';

		$tStamp      = $this->tStamp();
		$tgl         = date('Y-m-d');
		$method      = 'POST';
		$param       = $this->JsonInsertObatRacikan($nosjp,$noresep,$jnsobat,$kdobat,$nmobat,$signa1,$signa2,$permintaan,$jmlobat,$jho,$ket);
		$request     = '/obatracikan/v3/insert';
		// http://localhost/clone/DartoMakanYuyu/cross_ci3/Bridging_apotek/insertobatracikan

		$string      = $this->url($request, $param, $method, $tStamp);
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
	/*--------- END PENYIMPANAN OBAT ---------*/

	public function tampilhasil($request,$method,$param)
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

    public function referensiobat($data, $method, $parameter1=null, $parameter2=null, $parameter3=null)
	{
		if ($method=='GET') {
			$param="";
		}
		switch ($data) {
			/*GET*/
			case 'obat':
			$request  ='/referensi/obat/'.$parameter1.'/'.$parameter2.'/'.$parameter3;
			// http://localhost/clone/DartoMakanYuyu/cross_ci3/Bridging_apotek/referensiobat/obat/GET/1/2024-09-01/asam
			$this->tampilhasil($request,$method,$param);
			break;
			case 'spesialistik':
			$request  = '/referensi/spesialistik';
			// http://localhost/clone/DartoMakanYuyu/cross_ci3/Bridging_apotek/referensiobat/spesialistik/GET
			$this->tampilhasil($request,$method,$param);
			break;
			case 'settingppk':
			$request  = '/referensi/settingppk/read/'.$parameter1;
			// http://localhost/clone/DartoMakanYuyu/cross_ci3/Bridging_apotek/referensiobat/settingppk/GET/0216A026
			$this->tampilhasil($request,$method,$param);
			break;
			case 'ppk':
			$request  = '/referensi/ppk/'.$parameter1.'/'.$parameter2;
			// http://localhost/clone/DartoMakanYuyu/cross_ci3/Bridging_apotek/referensiobat/ppk/GET/2/darmayu
			$this->tampilhasil($request,$method,$param);
			break;
			case 'poli':
			$request  = '/referensi/poli/'.$parameter1;
			// http://localhost/clone/DartoMakanYuyu/cross_ci3/Bridging_apotek/referensiobat/poli/GET/INT
			$this->tampilhasil($request,$method,$param);
			break;
			case 'dpho':
			$request   = '/referensi/dpho';
			// http://localhost/clone/DartoMakanYuyu/cross_ci3/Bridging_apotek/referensiobat/dpho/GET
			$this->tampilhasil($request,$method,$param);
			break;

		}
	} 

	/*--------- PELAYANAN OBAT ---------*/
	public function pelayanan_obat_list($Parameter1)
    {

        $input    	= json_decode(file_get_contents('php://input'));
        $tStamp 	= $this->tStamp();
        $method 	= 'GET';
        $param  	= '';
        $request 	= '/obat/daftar/'.$Parameter1;
        // http://localhost/clone/DartoMakanYuyu/cross_ci3/Bridging_apotek/pelayanan_obat_list/0000106419396
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

    public function pelayanan_obat_riwayatobat($Parameter1, $Parameter2, $Parameter3)
    {

        $input    	= json_decode(file_get_contents('php://input'));
        $tStamp 	= $this->tStamp();
        $method 	= 'GET';
        $param  	= '';
        $request 	= '/riwayatobat/'.$Parameter1.'/'.$Parameter2.'/'.$Parameter3;
        // http://localhost/clone/DartoMakanYuyu/cross_ci3/Bridging_apotek/pelayanan_obat_riwayatobat/2024-08-20/2024-08-20/0000106419396
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
    /*--------- END PELAYANAN OBAT ---------*/

    public function getSEP($Parameter1)
    {

        $input    	= json_decode(file_get_contents('php://input'));
        $tStamp 	= $this->tStamp();
        $method 	= 'GET';
        $param  	= '';
        $request 	= '/sep/'.$Parameter1;
        // http://localhost/clone/DartoMakanYuyu/cross_ci3/Bridging_apotek/getSEP/1202R0010318V000092
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

    public function getMonitoring($Parameter1, $Parameter2, $Parameter3, $Parameter4)
    {

        $input    	= json_decode(file_get_contents('php://input'));
        $tStamp 	= $this->tStamp();
        $method 	= 'GET';
        $param  	= '';
        $request 	= '/monitoring/klaim/'.$Parameter1.'/'.$Parameter2.'/'.$Parameter3.'/'.$Parameter4;
        // http://localhost/clone/DartoMakanYuyu/cross_ci3/Bridging_apotek/getMonitoring/1/2024/0/2
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

    public function getrekapprb($Parameter1, $Parameter2)
    {

        $input    	= json_decode(file_get_contents('php://input'));
        $tStamp 	= $this->tStamp();
        $method 	= 'GET';
        $param  	= '';
        $request 	= '/Prb/rekappeserta/tahun/'.$Parameter1.'/bulan/'.$Parameter2;
        // http://localhost/clone/DartoMakanYuyu/cross_ci3/Bridging_apotek/getrekapprb/1/2024
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
}
