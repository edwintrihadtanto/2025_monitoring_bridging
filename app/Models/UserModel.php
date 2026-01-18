<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['username', 'password_hash', 'full_name'];

    public function getUserWithRule($username)
    {
        return $this->select('users.*, rules.rule_name')
                    ->join('rules', 'rules.id_rules = users.id_rules', 'left')
                    ->where('users.username', $username)
                    ->first();
    }
}