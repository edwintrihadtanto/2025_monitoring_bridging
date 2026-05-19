<?php

namespace App\Controllers;
use Config\Services;
use App\Models\BpjsLogModel;

class Dashboard extends BaseController
{
    public function indexlama()
    {
        $data = [
            'page_title'    => 'Halaman Utama'
        ];
        
        return $this->renderView('dashboard/index', $data);
    }

    public function index()
    {
        $model = new BpjsLogModel();

        $rekap = $model->getDashboardToday();

        // default fallback
        $dataRekap = [
            'OBAT NON RACIKAN' => [
                'total_resep' => 0,
                'success'     => 0,
                'warning'     => 0,
                'failed'      => 0,
                'pending'     => 0,
                'success_rate'=> 0
            ],
            'OBAT RACIKAN' => [
                'total_resep' => 0,
                'success'     => 0,
                'warning'     => 0,
                'failed'      => 0,
                'pending'     => 0,
                'success_rate'=> 0
            ]
        ];

        // mapping hasil query
        foreach ($rekap as $row) {

            $dataRekap[$row['jenis_endpoint']] = [
                'total_resep' => $row['total_resep'],
                'success'     => $row['success'],
                'warning'     => $row['warning'],
                'failed'      => $row['failed'],
                'pending'     => $row['pending'],
                'success_rate'=> $row['success_rate']
            ];
        }

        return $this->renderView('dashboard/index', [
            'page_title' => 'Dashboard Monitoring BPJS',
            'rekap'      => $dataRekap
        ]);
    }

    public function getRekap()
    {
        $model = new JneModel();
        return $this->response->setJSON(
            $model->getDashboardToday()
        );
    }
}