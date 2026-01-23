<?php

namespace App\Models;

use CodeIgniter\Model;

class BpjsLogModel extends Model
{
    protected $table      = 'bpjs_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'endpoint', 'method', 'request_header', 'request_body', 'response_code', 'response_body', 'response_message', 'iduser'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // Tidak ada update field
    protected $deletedField  = ''; // Tidak ada soft delete
}