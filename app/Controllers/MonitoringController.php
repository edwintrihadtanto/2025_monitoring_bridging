<?php

namespace App\Controllers;

class MonitoringController extends BaseController
{
    public function index()
    {
        $logModel   = new \App\Models\BpjsLogModel();
        $rekap      = $logModel
                        ->select('response_code, COUNT(*) as total')
                        ->whereIn('response_code', [200, 404, 403])
                        ->groupBy('response_code')
                        ->findAll();
        $counts = [
            200 => 0,
            404 => 0,
            403 => 0
        ];

        foreach ($rekap as $row) {
            $counts[$row['response_code']] = $row['total'];
        }

        $perPage = $this->request->getGet('perPage') ?? 10;
        
        $data = [
            'page_title'    => 'Halaman Utama',
            // 'logs'          => $logModel->orderBy('created_at', 'DESC')->paginate(10, 'group1'),
            'logs'          => $logModel->orderBy('id', 'DESC')->paginate($perPage, 'group1'),
            // 'pager'         => $logModel->pager,
            'pagination'    => $logModel->pager,
            'perPage'       => $perPage, // Kirim kembali ke view agar dropdown tahu posisi aktif
            'rekap'         => [
                                'code200' => $counts[200],
                                'code404' => $counts[404],
                                'code403' => $counts[403],
                            ]
        ];

        // return view('dashboard/monitoringX', $data);
        return $this->renderView('dashboard/monitoring2', $data);
    }
}