<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'page_title'    => 'Halaman Utama'
        ];
        
        return $this->renderView('dashboard/index', $data);
    }
}