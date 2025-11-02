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
        // Mengambil konfigurasi dari .env
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
        // BPJS memerlukan format UTC
        date_default_timezone_set('UTC');
        return strval(time() - strtotime('1970-01-01 00:00:00'));
    }

    /**
     * Membuat signature untuk autentikasi API BPJS.
     * Mengikuti contoh yang diberikan oleh BPJS.
     */
    private function generateSignature(string $timestamp): string
    {
        // Data yang akan di-hash adalah ConsID + "&" + Timestamp
        $data = $this->consID . "&" . $timestamp;
        
        // Menggunakan HMAC-SHA256
        $signature = hash_hmac('sha256', $data, $this->secretKey, true);
        
        // Encode hasilnya dengan Base64
        return base64_encode($signature);
    }

    /**
     * Melakukan request ke API BPJS.
     * @param string $method Metode HTTP (GET, POST)
     * @param string $endpoint Endpoint API (contoh: "Peserta/nokartu/0001234567890")
     * @param array|null $data Data untuk request POST (jika ada)
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
            'user_key'    => $this->user_key,
            'Accept'      => 'application/json',
        ];

        $options = [
            'headers' => $headers,
            'debug' => true, // Set ke false di production
            'http_errors' => false, // Agar kita bisa handle error manual
        ];

        if ($method === 'POST' && $data !== null) {
            $options['json'] = $data;
        }

        $url = $this->baseURL . $endpoint;
        
        // Simpan log request sebelum dikirim
        $logData = [
            'endpoint' => $url,
            'method' => $method,
            'request_header' => json_encode($headers),
            'request_body' => json_encode($data),
        ];

        $response = $this->client->request($method, $url, $options);
        $responseBody = $response->getBody();
        $responseCode = $response->getStatusCode();

        // Simpan log response
        $logData['response_code'] = $responseCode;
        $logData['response_body'] = $responseBody;

        // Panggil helper untuk menyimpan log
        log_to_db($logData);

        return [
            'status_code' => $responseCode,
            'body' => json_decode($responseBody, true)
        ];
    }
}