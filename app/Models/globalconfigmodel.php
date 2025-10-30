<?php

namespace App\Models;

use CodeIgniter\Model;

class GlobalConfigModel extends Model
{
    protected $table = 'global_config';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'config_key',
        'setting_key',
        'setting_value',
        'config_value',
        'updated_at'
    ];

    public function isEnabled($key)
    {
        $config = $this->where('setting_key', $key)->first();
        return $config ? (bool)$config['setting_value'] : false;
    }

    public function getAdminControls()
    {
        return $this->where('config_key', 'admin')->findAll();
    }

    public function getUserControls()
    {
        return $this->where('config_key', 'user')->findAll();
    }
}
