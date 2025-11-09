<?php

if (!function_exists('log_to_db')) {
    function log_to_db(array $data)
    {
        $logModel = new \App\Models\BpjsLogModel();
        $logModel->insert($data);
    }
}