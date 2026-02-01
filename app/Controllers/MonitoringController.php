<?php

namespace App\Controllers;

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
}