<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use App\Models\GlobalconfigModel;

class Globalconfig extends BaseController
{
    protected $configModel;

    public function __construct()
    {
        $this->configModel = new GlobalconfigModel();
    }

    // Display Global & System Configs
    public function index()
    {
        $role = session()->get('role') ?? 'user';
    
        // ✅ Fetch ALL settings for admin, system, and user controls
        $all_controls = $this->configModel
            ->whereIn('config_key', ['admin', 'system', 'user'])
            ->findAll();
    
        // System File Upload Settings
        $uploadSettings = $this->configModel->getSystemUploadSettings();
    
        $data = [
            'title'          => 'Global & System Configuration',
            'all_controls'   => $all_controls, // Use this new variable in the view
            'role' => $role,
            'uploadSettings' => $uploadSettings,
        ];

        return view('superadmin/globalconfig', $data);
    }

    // Toggle any global/user setting
    public function toggle()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        $setting = $this->configModel->find($id);
        if (!$setting) {
            return $this->response->setJSON(['success' => false, 'message' => 'Setting not found.']);
        }

        $newValue = ($status == 1) ? 1 : 0;
        $updated = $this->configModel->update($id, [
            'setting_value' => $newValue,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['success' => $updated]);
    }



    // Toggle file type enable/disable
    public function toggleFileType()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        if ($id !== null && $status !== null) {
            // Update the setting_value of the file type
            $this->configModel->update($id, [
                'setting_value' => ($status == 1 ? 1 : 0),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            return $this->response->setJSON(['success' => true, 'status' => $status]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Invalid request.']);
    }
}
