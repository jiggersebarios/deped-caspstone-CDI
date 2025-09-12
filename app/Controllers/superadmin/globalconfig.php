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
        $configs = $this->configModel->findAll();

        return view('superadmin/globalconfig', [
            'title'   => 'Global Configuration',
            'configs' => $configs
        ]);
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

    public static function renderFriendlyName(string $key): string
{
    $map = [
        'allow_admin_add_folder'       => 'Allow Admin to Add Main Folder',
        'allow_admin_delete_folder'    => 'Allow Admin to Delete Main Folder',
        'allow_admin_add_subfolder'    => 'Allow Admin to Add Subfolder',
        'allow_admin_delete_subfolder' => 'Allow Admin to Delete Subfolder',
    ];

    return $map[$key] ?? ucfirst(str_replace('_', ' ', $key));
}

}
