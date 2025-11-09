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

        // User Controls
        $user_controls = $this->configModel->getUserControls();
        $canUpload = ($role === 'user') && (($user_controls['enable_file_upload'] ?? 0) == 1);
        $canEdit   = ($role === 'user') && (($user_controls['enable_file_edit'] ?? 0) == 1);
        $canDelete = ($role === 'user') && (($user_controls['enable_file_delete'] ?? 0) == 1);

        if (in_array($role, ['admin', 'superadmin'])) {
            $canUpload = $canEdit = $canDelete = true;
        }

        // System File Upload Settings
        $uploadSettings = $this->configModel->getSystemUploadSettings();

        $data = [
            'title' => 'Global & System Configuration',
            'configs' => $this->configModel->getAdminControls(),
            'user_controls' => $user_controls,
            'role' => $role,
            'canUpload' => $canUpload,
            'canEdit' => $canEdit,
            'canDelete' => $canDelete,
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
