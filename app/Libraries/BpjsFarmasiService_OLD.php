<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;

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
     * Menghasilkan timestamp Unix saat ini dalam format string.
     */
    private function getTimestamp(): string
    {
        date_default_timezone_set('UTC');
        return strval(time() - strtotime('1970-01-01 00:00:00'));
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
     * Kunci ini adalah hasil hash dari ConsID + SecretKey + Timestamp.
     */
    private function generateKey(string $timestamp): string
    {
        return $this->consID . $this->secretKey . $timestamp;
    }

    /**
     * Mendekripsi data response dari BPJS.
     * @param string $key Kunci untuk dekripsi.
     * @param string $encryptedData Data terenkripsi (biasanya dari key 'response').
     * @return string|false Data yang sudah didekripsi, atau false jika gagal.
     */
    private function decryptResponse(string $key, string $encryptedData)
    {
        // Generate hash dari key untuk mendapatkan IV
        $hashKey = hash('sha256', $key, true);
        
        // Ambil 16 byte pertama dari hash sebagai IV
        $iv = substr($hashKey, 0, 16);

        // Lakukan dekripsi
        $decrypted = openssl_decrypt(
            base64_decode($encryptedData),
            'AES-256-CBC',
            $hashKey,
            OPENSSL_RAW_DATA,
            $iv
        );

        return $decrypted;
    }

    /**
     * Melakukan request ke API BPJS.
     * @param string $method Metode HTTP (GET, POST)
     * @param string $endpoint Endpoint API
     * @param array|null $data Data untuk request POST
     * @return array
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
            'debug' => false, // Set ke false di production
            'http_errors' => false,
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

        // --- PROSES DEKRIPSI ---
        // Jika response sukses dan ada key 'response' yang terenkripsi
        /*if ($responseCode == 200 && isset($responseArray['response'])) {
            $key = $this->generateKey($timestamp);
            $decryptedData = $this->decryptResponse($key, $responseArray['response']);

            if ($decryptedData !== false) {
                // Ganti nilai 'response' yang terenkripsi dengan data yang sudah didekripsi
                $responseArray['response'] = json_decode($decryptedData, true);
            } else {
                // Jika dekripsi gagal, beri tahu
                $responseArray['response'] = "FAILED TO DECRYPT RESPONSE";
                $responseArray['decryption_error'] = true;
            }
        }*/

        // --- PROSES DEKRIPSI ---
        // Jika response sukses dan ada key 'response' yang terenkripsi
        if ($responseCode == 200 && isset($responseArray['response'])) {
            $key = $this->generateKey($timestamp);
            $decryptedData = $this->decryptResponse($key, $responseArray['response']);

            if ($decryptedData !== false) {
                // Tambahkan ini untuk debugging: simpan string mentah hasil dekripsi
                $responseArray['decrypted_raw'] = $decryptedData;

                // Coba decode string JSON-nya
                $decodedData = json_decode($decryptedData, true);

                // Cek apakah hasil decode adalah null (berarti ada error JSON)
                if ($decodedData === null) {
                    // Jika gagal, beri tahu dengan pesan error dan tampilkan datanya
                    $responseArray['response'] = "DECRYPTION SUCCESSFUL, BUT JSON DECODE FAILED.";
                    $responseArray['json_error_msg'] = json_last_error_msg(); // Pesan error dari PHP
                } else {
                    // Jika berhasil, ganti nilai 'response' dengan data yang sudah di-decode
                    $responseArray['response'] = $decodedData;
                }
            } else {
                // Jika dekripsi gagal dari awal
                $responseArray['response'] = "FAILED TO DECRYPT RESPONSE";
                $responseArray['decryption_error'] = true;
            }
        }

        // Simpan log response (sudah didekripsi)
        $logData['response_code'] = $responseCode;
        $logData['response_body'] = json_encode($responseArray);

        log_to_db($logData);

        return [
            'status_code' => $responseCode,
            'body' => $responseArray
        ];
    }
}