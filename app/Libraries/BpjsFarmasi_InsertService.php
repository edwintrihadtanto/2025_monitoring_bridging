<?php

namespace App\Libraries;

use LZCompressor\LZString;

class BpjsFarmasi_InsertService
{
    protected string $baseUrl;
    protected string $consId;
    protected string $secretKey;
    protected string $userKey;

    public function __construct()
    {
        $this->baseUrl   = env('BPJS.api.baseURL');
        $this->consId    = env('BPJS.ConsID');
        $this->secretKey = env('BPJS.SekretKey');
        $this->userKey   = env('BPJS.UserKey');

        date_default_timezone_set('Asia/Jakarta');
    }

    /* ===============================
     * TIMESTAMP BPJS
     * =============================== */
    protected function timestamp(): string
    {
        return (string)(time() - strtotime('1970-01-01 07:00:00'));
    }

    /* ===============================
     * HEADER SIGNATURE (DINAMIS)
     * =============================== */
    protected function headers(string $method, string $tStamp): array
    {
        $signature = base64_encode(
            hash_hmac('sha256', $this->consId . '&' . $tStamp, $this->secretKey, true)
        );

        $contentType = in_array($method, ['POST', 'DELETE'])
            ? 'application/x-www-form-urlencoded'
            : 'application/json; charset=utf-8';

        return [
            "X-Cons-ID: {$this->consId}",
            "X-Timestamp: {$tStamp}",
            "X-Signature: {$signature}",
            "user_key: {$this->userKey}",
            "Content-Type: {$contentType}",
        ];
    }
    /* ===============================
     * DECRYPT RESPONSE
     * =============================== */
    protected function decrypt(string $response, string $tStamp): array
    {
        $key  = $this->consId . $this->secretKey . $tStamp;
        $hash = hex2bin(hash('sha256', $key));
        $iv   = substr($hash, 0, 16);

        $decrypt = openssl_decrypt(
            base64_decode($response),
            'AES-256-CBC',
            $hash,
            OPENSSL_RAW_DATA,
            $iv
        );

        return json_decode(
            LZString::decompressFromEncodedURIComponent($decrypt),
            true
        );
    }
    /* ===============================
     * REQUEST GENERIC
     * =============================== */
    public function request(
        string $method,
        string $endpoint,
        string $payload = null
    ): array {
        $method = strtoupper($method);
        $tStamp = $this->timestamp();
        $url    = rtrim($this->baseUrl, '/') . $endpoint;

        $headers = $this->headers($method, $tStamp);

        // === LOG REQUEST ===
        $logData = [
            'endpoint'       => $url,
            'method'         => $method,
            'request_header' => json_encode($headers),
            'request_body'   => $payload ?? '',
        ];

        $opts = [
            'http' => [
                'method'  => $method,
                'header'  => $headers,
                'content' => $payload ?? '',
                'timeout' => 30,
            ],
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ];

        $context = stream_context_create($opts);
        $rawBody = @file_get_contents($url, false, $context);

        // === LOG RESPONSE RAW ===
        $logData['response_body'] = $rawBody ?: 'NO RESPONSE';

        if ($rawBody === false) {
            $logData['response_code']    = 500;
            $logData['response_message'] = 'Connection failed';
            log_to_db($logData);

            return [
                'status'  => 'error',
                'message' => 'Gagal koneksi ke BPJS'
            ];
        }

        $json = json_decode($rawBody);

        if (!isset($json->metaData)) {
            $logData['response_code']    = 500;
            $logData['response_message'] = 'Invalid response';
            log_to_db($logData);

            return [
                'status' => 'error',
                'raw'    => $rawBody
            ];
        }

        // === LOG METADATA ===
        $logData['response_code']    = $json->metaData->code;
        $logData['response_message'] = $json->metaData->message;
        log_to_db($logData);

        if ($json->metaData->code != 200) {
            return [
                'status'  => 'gagal',
                'code'    => $json->metaData->code,
                'message' => $json->metaData->message
            ];
        }

        // return [
        //     'status' => 'sukses',
        //     'data'   => $this->decrypt($json->response, $tStamp)
        // ];
        return [
            // 'status_code' => $responseCode,
            'status_code' => $json->metaData->code,
            'data' => $this->decrypt($json->response, $tStamp)
        ];
    }
}
