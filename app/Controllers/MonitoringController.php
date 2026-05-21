<?php

namespace App\Controllers;

use Config\Services;

class MonitoringController extends BaseController
{
    public function index()
    {
        $logModel   = new \App\Models\BpjsLogModel();
        /*$rekap      = $logModel
                        ->select('response_code, COUNT(*) as total')
                        ->whereIn('response_code', [200, 201, 401, 402, 403, 404, 405 ])
                        ->groupBy('response_code')
                        ->findAll();
        $counts = [
            200 => 0,
            201 => 0,
            202 => 0,
            203 => 0,
            300 => 0,
            301 => 0,
            302 => 0,
            303 => 0,
            304 => 0,
            400 => 0,
            401 => 0,
            402 => 0,
            403 => 0,
            404 => 0,
            405 => 0,
            406 => 0,
            407 => 0,
            408 => 0,
            500 => 0,
            501 => 0,
            502 => 0,
            503 => 0,
            504 => 0,
            505 => 0,
        ];*/

        $rekap = $logModel
            ->select('response_code, COUNT(*) as total')
            ->groupBy('response_code')
            ->findAll();

        // Inisialisasi grup
        $counts = [
            200 => 0,
            300 => 0,
            400 => 0,
            500 => 0,
        ];

        foreach ($rekap as $row) {
            // $counts[$row['response_code']] = $row['total'];
            $group = floor($row['response_code'] / 100) * 100;

            if (isset($counts[$group])) {
                $counts[$group] += $row['total'];
            }
        }

        $perPage = $this->request->getGet('perPage') ?? 10;
        
        // $code200 = $counts[200] + $counts[201] + $counts[202] + $counts[203];
        // $code300 = $counts[300] + $counts[301] + $counts[302] + $counts[303] + $counts[304];
        // $code400 = $counts[400] + $counts[401] + $counts[402] + $counts[403] + $counts[404] + $counts[405] + $counts[406] + $counts[407] + $counts[408]  ;
        // $code500 = $counts[500] + $counts[501] + $counts[502] + $counts[503] + $counts[504] + $counts[505];
        
        
        $data = [
            'page_title'    => 'Halaman Utama',
            // 'logs'          => $logModel->orderBy('created_at', 'DESC')->paginate(10, 'group1'),
            'logs'          => $logModel->orderBy('id', 'DESC')->paginate($perPage, 'group1'),
            // 'pager'         => $logModel->pager,
            'pagination'    => $logModel->pager,
            'perPage'       => $perPage, // Kirim kembali ke view agar dropdown tahu posisi aktif
            'rekap'         => [
                                'code200' => $counts[200],
                                'code300' => $counts[300],
                                'code400' => $counts[400],
                                'code500' => $counts[500],
                            ]
        ];

        // return view('dashboard/monitoringX', $data);
        return $this->renderView('dashboard/logmonitoring_bridging', $data);
    }

    public function koneksi()
    {
        return $this->renderView('dashboard/koneksi', [
            'page_title' => 'Cek Koneksi',
            'checks'     => $this->getConnectionChecks(),
            'checked_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function koneksiStatus()
    {
        return $this->response->setJSON([
            'status'     => true,
            'checked_at' => date('Y-m-d H:i:s'),
            'checks'     => $this->getConnectionChecks(),
        ]);
    }

    private function getConnectionChecks(): array
    {
        return [
            $this->checkApplicationServer(),
            $this->checkPublicInternet(),
            $this->checkPostgreDatabase(),
            $this->checkBpjsGateway('BPJS Farmasi Gateway', env('BPJS.api.baseURL')),
            $this->checkBpjsGateway('BPJS VClaim Gateway', env('BPJSVCLAIM.api.baseURL')),
            $this->checkBpjsFarmasiService(),
            $this->checkBpjsVclaimService(),
        ];
    }

    private function checkApplicationServer(): array
    {
        $serverIp = env('APP_SERVER_IP') ?: '192.168.0.98';
        $serverAddr = $this->request->getServer('SERVER_ADDR');
        $httpHost = $this->request->getServer('HTTP_HOST');
        $currentHost = $serverAddr ?: gethostname();
        $isTargetServer = (string) $serverAddr === (string) $serverIp;
        $status = $isTargetServer ? 'ok' : 'warning';
        $message = $isTargetServer
            ? "Aplikasi berjalan di server target {$serverIp}."
            : "Aplikasi belum berjalan di server target {$serverIp}. Saat ini terdeteksi dari " . ($serverAddr ?: $httpHost ?: gethostname()) . ".";

        if ($httpHost && str_contains((string) $httpHost, $serverIp)) {
            $status = 'ok';
            $message = "Aplikasi diakses melalui server target {$serverIp}.";
        }

        return $this->makeCheckResult(
            'application_server',
            'Server Aplikasi',
            $status,
            $message,
            null,
            null,
            (string) $currentHost
        );
    }

    private function makeCheckResult(
        string $key,
        string $label,
        string $status,
        string $message,
        ?float $durationMs = null,
        ?int $httpCode = null,
        ?string $endpoint = null
    ): array {
        return [
            'key'         => $key,
            'label'       => $label,
            'status'      => $status,
            'message'     => $message,
            'duration_ms' => $durationMs,
            'speed'       => $this->classifyDuration($durationMs),
            'http_code'   => $httpCode,
            'endpoint'    => $endpoint,
        ];
    }

    private function classifyDuration(?float $durationMs): ?array
    {
        if ($durationMs === null) {
            return null;
        }

        if ($durationMs <= 300) {
            return [
                'label' => 'Cepat',
                'class' => 'speed-fast',
                'icon'  => 'bi-lightning-charge',
            ];
        }

        if ($durationMs <= 1000) {
            return [
                'label' => 'Normal',
                'class' => 'speed-normal',
                'icon'  => 'bi-check2-circle',
            ];
        }

        if ($durationMs <= 3000) {
            return [
                'label' => 'Lambat',
                'class' => 'speed-slow',
                'icon'  => 'bi-hourglass-split',
            ];
        }

        return [
            'label' => 'Sangat Lambat',
            'class' => 'speed-critical',
            'icon'  => 'bi-exclamation-octagon',
        ];
    }

    private function requestHealth(string $url, string $method = 'GET', array $options = []): array
    {
        $client = Services::curlrequest();
        $start = microtime(true);

        $defaultOptions = [
            'http_errors'     => false,
            'verify'          => false,
            'timeout'         => 8,
            'connect_timeout' => 5,
        ];

        try {
            $response = $client->request($method, $url, array_replace_recursive($defaultOptions, $options));
            $durationMs = round((microtime(true) - $start) * 1000, 2);

            return [
                'ok'          => true,
                'http_code'   => $response->getStatusCode(),
                'body'        => $response->getBody(),
                'duration_ms' => $durationMs,
                'error'       => null,
            ];
        } catch (\Throwable $e) {
            $durationMs = round((microtime(true) - $start) * 1000, 2);

            return [
                'ok'          => false,
                'http_code'   => null,
                'body'        => null,
                'duration_ms' => $durationMs,
                'error'       => $e->getMessage(),
            ];
        }
    }

    private function checkPublicInternet(): array
    {
        $endpoint = 'https://www.google.com';
        $result = $this->requestHealth($endpoint, 'GET');

        if ($result['ok'] && in_array($result['http_code'], [204, 200], true)) {
            return $this->makeCheckResult(
                'internet',
                'Internet Server',
                'ok',
                'Server dapat mengakses internet publik.',
                $result['duration_ms'],
                $result['http_code'],
                $endpoint
            );
        }

        return $this->makeCheckResult(
            'internet',
            'Internet Server',
            'down',
            $result['error'] ?: 'Server belum berhasil mengakses internet publik.',
            $result['duration_ms'],
            $result['http_code'],
            $endpoint
        );
    }

    private function checkPostgreDatabase(): array
    {
        $config = config('Database');
        $dbConfig = $config->default ?? [];
        $host = $dbConfig['hostname'] ?? 'localhost';
        $port = $dbConfig['port'] ?? 5432;
        $database = $dbConfig['database'] ?? '-';
        $endpoint = "PostgreSQL {$host}:{$port}/{$database}";
        $start = microtime(true);

        try {
            $db = \Config\Database::connect('default');
            $db->query('SELECT 1 AS health_check')->getRowArray();
            $durationMs = round((microtime(true) - $start) * 1000, 2);

            return $this->makeCheckResult(
                'database_postgresql',
                'Database PostgreSQL',
                'ok',
                'Koneksi database default berhasil.',
                $durationMs,
                null,
                $endpoint
            );
        } catch (\Throwable $e) {
            $durationMs = round((microtime(true) - $start) * 1000, 2);

            return $this->makeCheckResult(
                'database_postgresql',
                'Database PostgreSQL',
                'down',
                $e->getMessage(),
                $durationMs,
                null,
                $endpoint
            );
        }
    }

    private function checkBpjsGateway(string $label, ?string $baseUrl): array
    {
        if (empty($baseUrl)) {
            return $this->makeCheckResult(
                strtolower(str_replace(' ', '_', $label)),
                $label,
                'down',
                'Base URL belum dikonfigurasi di .env.'
            );
        }

        $result = $this->requestHealth($baseUrl, 'GET');
        $status = $result['ok'] ? 'ok' : 'down';
        $message = $result['ok']
            ? 'Gateway BPJS dapat dijangkau dari server.'
            : ($result['error'] ?: 'Gateway BPJS tidak dapat dijangkau.');

        return $this->makeCheckResult(
            strtolower(str_replace(' ', '_', $label)),
            $label,
            $status,
            $message,
            $result['duration_ms'],
            $result['http_code'],
            $baseUrl
        );
    }

    private function checkBpjsFarmasiService(): array
    {
        $baseUrl = env('BPJS.api.baseURL');
        $endpoint = $baseUrl ? rtrim($baseUrl, '/') . '/referensi/spesialistik' : null;

        if (!$endpoint) {
            return $this->makeCheckResult(
                'bpjs_farmasi_service',
                'Service BPJS Farmasi',
                'down',
                'Base URL BPJS Farmasi belum dikonfigurasi di .env.'
            );
        }

        $timestamp = $this->getBpjsTimestamp();
        $result = $this->requestHealth($endpoint, 'GET', [
            'headers' => [
                'X-Cons-ID'   => env('BPJS.ConsID'),
                'X-Timestamp' => $timestamp,
                'X-Signature' => $this->generateBpjsSignature(env('BPJS.ConsID'), env('BPJS.SekretKey'), $timestamp),
                'user_key'    => env('BPJS.UserKey'),
                'Accept'      => 'application/json',
            ],
        ]);

        $body = json_decode($result['body'] ?? '', true);
        $metaCode = $body['metaData']['code'] ?? null;
        $metaMessage = $body['metaData']['message'] ?? null;

        if ($result['ok'] && (string) $metaCode === '200') {
            return $this->makeCheckResult(
                'bpjs_farmasi_service',
                'Service BPJS Farmasi',
                'ok',
                $metaMessage ?: 'Service BPJS Farmasi merespons normal.',
                $result['duration_ms'],
                $result['http_code'],
                $endpoint
            );
        }

        $status = $result['ok'] ? 'warning' : 'down';
        $message = $metaMessage ?: ($result['error'] ?: 'Service BPJS Farmasi merespons tidak normal.');

        return $this->makeCheckResult(
            'bpjs_farmasi_service',
            'Service BPJS Farmasi',
            $status,
            $message,
            $result['duration_ms'],
            $result['http_code'],
            $endpoint
        );
    }

    private function checkBpjsVclaimService(): array
    {
        $baseUrl = env('BPJSVCLAIM.api.baseURL');
        $endpoint = $baseUrl ? rtrim($baseUrl, '/') . '/referensi/propinsi' : null;

        if (!$endpoint) {
            return $this->makeCheckResult(
                'bpjs_vclaim_service',
                'Service BPJS VClaim',
                'down',
                'Base URL BPJS VClaim belum dikonfigurasi di .env.'
            );
        }

        $timestamp = $this->getBpjsTimestamp();
        $result = $this->requestHealth($endpoint, 'GET', [
            'headers' => [
                'X-cons-id'   => env('BPJSVCLAIM.ConsID'),
                'X-timestamp' => $timestamp,
                'X-signature' => $this->generateBpjsSignature(env('BPJSVCLAIM.ConsID'), env('BPJSVCLAIM.SekretKey'), $timestamp),
                'user_key'    => env('BPJSVCLAIM.UserKey'),
                'Accept'      => 'application/json',
            ],
        ]);

        $body = json_decode($result['body'] ?? '', true);
        $metaCode = $body['metaData']['code'] ?? null;
        $metaMessage = $body['metaData']['message'] ?? null;

        if ($result['ok'] && (string) $metaCode === '200') {
            return $this->makeCheckResult(
                'bpjs_vclaim_service',
                'Service BPJS VClaim',
                'ok',
                $metaMessage ?: 'Service BPJS VClaim merespons normal.',
                $result['duration_ms'],
                $result['http_code'],
                $endpoint
            );
        }

        $status = $result['ok'] ? 'warning' : 'down';
        $message = $metaMessage ?: ($result['error'] ?: 'Service BPJS VClaim merespons tidak normal.');

        return $this->makeCheckResult(
            'bpjs_vclaim_service',
            'Service BPJS VClaim',
            $status,
            $message,
            $result['duration_ms'],
            $result['http_code'],
            $endpoint
        );
    }

    private function getBpjsTimestamp(): string
    {
        return strval(time() - strtotime('1970-01-01 07:00:00'));
    }

    private function generateBpjsSignature(?string $consId, ?string $secretKey, string $timestamp): string
    {
        return base64_encode(hash_hmac('sha256', ($consId ?? '') . '&' . $timestamp, $secretKey ?? '', true));
    }
}
