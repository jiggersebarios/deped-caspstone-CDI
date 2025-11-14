<?php

namespace App\Models;

use CodeIgniter\Model;

class GlobalconfigModel extends Model
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

    // Check if a setting is enabled (toggle)
    public function isEnabled($key)
    {
        $config = $this->where('setting_key', $key)->first();
        return $config ? (bool) $config['setting_value'] : false;
    }

    // Admin access controls
    public function getAdminControls()
    {
        return $this->where('config_key', 'admin')->findAll();
    }

    // User access controls
    public function getUserControls()
    {
        return $this->where('config_key', 'user')->findAll();
    }

    // System file upload settings (max size + file types)
    public function getSystemUploadSettings()
    {
        $settings = [];

     
        // File type settings (allow_pdf, allow_docx, etc.)
        $fileTypes = $this->like('setting_key', 'allow_')->findAll();
        foreach ($fileTypes as $type) {
            $settings[$type['setting_key']] = [
                'id'      => $type['id'],
                'enabled' => (int)$type['setting_value']
            ];
        }

        return $settings;
    }

    // Update a toggle or numeric setting
public function updateSetting($settingKey, $settingValue, $enabled = null, $configKey = 'system')
{
    $setting = $this->where('setting_key', $settingKey)->first();

    $data = ['updated_at' => date('Y-m-d H:i:s')];

    if ($setting) {
        $data['setting_value'] = (int)$settingValue;

        if ($enabled !== null) {
            $data['config_value'] = (int)$enabled;
        }

        return $this->update($setting['id'], $data);
    } else {
        return $this->insert([
            'config_key'    => $configKey,
            'setting_key'   => $settingKey,
            'setting_value' => (int)$settingValue,
            'config_value'  => $enabled ?? 1,
            'updated_at'    => date('Y-m-d H:i:s')
        ]);
    }
}



}
