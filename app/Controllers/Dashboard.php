<?php

namespace App\Controllers;

// Pastikan extends BaseController
class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'page_title' => 'Dashboard',
            // 'user_name' => 'John Ducky'
        ];
        
        // Cukup panggil renderView
        return $this->renderView('dashboard/index', $data);
    }
}