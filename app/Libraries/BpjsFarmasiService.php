<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;
use LZCompressor\LZString; // Impor library LZ-String

class BpjsFarmasiService
{
    protected $consID;
    protected $secretKey;
    protected $userKey;
    protected $baseURL;
    protected $client;

    public function __construct()
    {
        $this->baseURL = env('BPJS.api.baseURL');
        $this->consID = env('BPJS.ConsID');
        $this->secretKey = env('BPJS.SekretKey');
        $this->userKey = env('BPJS.UserKey');

        $this->client = \Config\Services::curlrequest();
    }

    /**
     * Menghasilkan timestamp sesuai contoh CI3 (WIB).
     */
    private function getTimestamp(): string
    {
        // Ini adalah cara untuk mendapatkan timestamp WIB tanpa mengubah timezone server
        return strval(time() - strtotime('1970-01-01 07:00:00'));
    }

    /**
     * Membuat signature untuk autentikasi API BPJS.
     */
    private function generateSignature(string $timestamp): string
    {
        $data = $this->consID . "&" . $timestamp;
        $signature = hash_hmac('sha256', $data, $this->secretKey, true);
        return base64_encode($signature);
    }

    /**
     * Menghasilkan kunci untuk dekripsi response.
     */
    private function generateKey(string $timestamp): string
    {
        return $this->consID . $this->secretKey . $timestamp;
    }

    /**
     * Mendekripsi data response dari BPJS (LOGIKA YANG BENAR).
     * @param string $key Kunci untuk dekripsi.
     * @param string $encryptedData Data terenkripsi (dari key 'response').
     * @return string|false Data JSON yang sudah didekripsi, atau false jika gagal.
     */
    private function decryptResponse(string $key, string $encryptedData)
    {
        // 1. Buat hash dari key untuk mendapatkan kunci dan IV
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);

        // 2. Dekripsi dengan AES-256-CBC
        $output = openssl_decrypt(
            base64_decode($encryptedData),
            'AES-256-CBC',
            $key_hash,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($output === false) {
            log_message('error', "BPJS API: OpenSSL decryption (CBC) failed.");
            return false;
        }

        // 3. Dekompres hasil dekripsi dengan LZ-String
        $decompressed = LZString::decompressFromEncodedURIComponent($output);

        if ($decompressed === null) {
            log_message('error', "BPJS API: LZ-String decompression failed.");
            return false;
        }
        
        // 4. Kembalikan string JSON yang sudah bersih
        return $decompressed;
    }

    /**
     * Melakukan request ke API BPJS.
     */
    public function request(string $method, string $endpoint, array $data = null): array
    {
        $timestamp = $this->getTimestamp();
        $signature = $this->generateSignature($timestamp);

        $headers = [
            'X-cons-id'   => $this->consID,
            'X-timestamp' => $timestamp,
            'X-signature' => $signature,
            'user_key'    => $this->userKey,
            'Accept'      => 'application/json',
        ];

        $options = [
            'headers' => $headers,
            'debug' => false,
            'http_errors' => false,
            'verify' => false, // Sama dengan 'verify_peer' => false
        ];

        if ($method === 'POST' && $data !== null) {
            $options['json'] = $data;
        }

        $url = $this->baseURL . $endpoint;
        
        $logData = [
            'endpoint' => $url,
            'method' => $method,
            'request_header' => json_encode($headers),
            'request_body' => json_encode($data),
        ];

        $response = $this->client->request($method, $url, $options);
        $responseBody = $response->getBody();
        $responseCode = $response->getStatusCode();

        $responseArray = json_decode($responseBody, true);

        // --- PROSES DEKRIPSI YANG BENAR ---
        if ($responseCode == 200 && isset($responseArray['response'])) {
            $key = $this->generateKey($timestamp);
            $decryptedJsonString = $this->decryptResponse($key, $responseArray['response']);

            if ($decryptedJsonString !== false) {
                $responseArray['response'] = json_decode($decryptedJsonString, true);
            } else {
                $responseArray['response'] = "FAILED TO DECRYPT RESPONSE";
                $responseArray['decryption_error'] = true;
            }
        }
        
        // Simpan log response
        $logData['response_code'] = $responseCode;
        $logData['response_body'] = json_encode($responseArray);
        log_to_db($logData);

        return [
            'status_code' => $responseCode,
            'body' => $responseArray
        ];
    }
}