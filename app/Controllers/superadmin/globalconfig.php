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

public function index()
{
    $role = session()->get('role') ?? 'user';
    $user_controls = $this->configModel->getUserControls();

    $canUpload = ($role === 'user') && (($user_controls['enable_file_upload'] ?? 0) == 1);
    $canEdit   = ($role === 'user') && (($user_controls['enable_file_edit'] ?? 0) == 1);
    $canDelete = ($role === 'user') && (($user_controls['enable_file_delete'] ?? 0) == 1);

    if (in_array($role, ['admin', 'superadmin'])) {
        $canUpload = $canEdit = $canDelete = true;
    }

    $data = [
        'title' => 'Global Configuration',
        'configs' => $this->configModel->getAdminControls(),
        'user_controls' => $user_controls,
        'role' => $role,
        'canUpload' => $canUpload,
        'canEdit' => $canEdit,
        'canDelete' => $canDelete,
    ];

    return view('superadmin/globalconfig', $data);
}





    
    public function toggle()
    {
        $id     = $this->request->getPost('id');
        $status = $this->request->getPost('status'); // 0 or 1 from AJAX

        if ($id !== null && $status !== null) {
            $this->configModel->update($id, ['setting_value' => $status]);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Setting updated successfully.',
                'status'  => $status
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request.'
        ]);
    }

    
}
