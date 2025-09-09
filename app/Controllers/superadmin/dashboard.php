<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\FolderModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // Restrict access: only superadmin role
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/login')->with('error', 'Unauthorized access');
        }

        $userModel   = new UserModel();
        $folderModel = new FolderModel();

        $data = [
            'title'        => 'SuperAdmin Dashboard',
            'totalUsers'   => $userModel->countAllResults(),
            'totalFolders' => $folderModel->countAllResults(),
        ];

        return view('superadmin/dashboard', $data);
    }
}
