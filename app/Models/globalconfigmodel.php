<?php

namespace App\Models;

use CodeIgniter\Model;

class GlobalConfigModel extends Model
{
    protected $table = 'global_config';
    protected $primaryKey = 'id';
    protected $allowedFields = ['setting_key', 'setting_value'];

    public function isEnabled($key)
    {
        $config = $this->where('setting_key', $key)->first();
        return $config ? (bool)$config['setting_value'] : false;
    }
}
