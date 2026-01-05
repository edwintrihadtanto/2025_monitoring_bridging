<?php

namespace App\Controllers;

class MonitoringController extends BaseController
{
    public function index()
    {
        $logModel = new \App\Models\BpjsLogModel();
        
        $perPage = $this->request->getGet('perPage') ?? 10;
        
        $data = [
            'page_title'    => 'Halaman Utama',
            // 'logs'          => $logModel->orderBy('created_at', 'DESC')->paginate(10, 'group1'),
            'logs'          => $logModel->orderBy('id', 'DESC')->paginate($perPage, 'group1'),
            // 'pager'         => $logModel->pager,
            'pagination'    => $logModel->pager,
            'perPage'       => $perPage, // Kirim kembali ke view agar dropdown tahu posisi aktif
        ];

        // return view('dashboard/monitoringX', $data);
        return $this->renderView('dashboard/monitoring2', $data);
    }
}