<?php

namespace App\Controllers;

class MonitoringController extends BaseController
{
    public function index()
    {
        $logModel = new \App\Models\BpjsLogModel();
        
        // Pagination
        $data = [
            'logs'  => $logModel->orderBy('created_at', 'DESC')->paginate(10, 'group1'),
            'pager' => $logModel->pager,
        ];

        return view('dashboard/monitoringX', $data);
    }
}