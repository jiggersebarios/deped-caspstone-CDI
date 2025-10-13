<?php

namespace App\Models;

use CodeIgniter\Model;

class UserDashboardModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'email', 'role', 'created_at'];

    public function getUserInfo($id)
    {
        return $this->where('id', $id)->first();
    }
}
