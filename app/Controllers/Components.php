<?php

namespace App\Controllers;

// Pastikan extends BaseController
class Components extends BaseController
{
    public function accordion()
    {
        $data = [
            'page_title' => 'Accordion Component'
        ];
        
        // return $this->renderView('components/accordion', $data);
        // return $this->renderView('dashboard/monitoringX', $data);

        $logModel = new \App\Models\BpjsLogModel();
        
        // Pagination
        $data = [
            'logs'  => $logModel->orderBy('created_at', 'DESC')->paginate(10, 'group1'),
            'pager' => $logModel->pager,
            'page_title' => 'Accordion Component'
        ];

        // return view('dashboard/monitoring', $data);
        return $this->renderView('dashboard/monitoringX', $data);
    }
}