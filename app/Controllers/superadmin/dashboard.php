<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\FileModel;
use App\Models\RequestModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // Restrict access: only superadmin role
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/login')->with('error', 'Unauthorized access');
        }

        $userModel    = new UserModel();
        $fileModel    = new FileModel();
       // $requestModel = new RequestModel();

        $data = [
            'title'            => 'SuperAdmin Dashboard',
            'role'             => 'superadmin', // pass role for sidebar
            'totalUsers'       => $userModel->countAllResults(),
            'totalFiles'       => $fileModel->countAllResults(),
            //'pendingRequests'  => $requestModel->where('status', 'pending')->countAllResults(),
            'newUploadedFiles' => $fileModel->where('status', 'pending')->countAllResults(),
        ];

        return view('superadmin/dashboard', $data);
    }
}
