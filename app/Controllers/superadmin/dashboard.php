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
        // Restrict access to superadmin only
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/login')->with('error', 'Unauthorized access');
        }

        // Initialize models
        $userModel     = new UserModel();
        $fileModel     = new FileModel();
        $requestModel  = new RequestModel();

        // Prepare dashboard data
        $data = [
            'title'            => 'SuperAdmin Dashboard',
            'role'             => 'superadmin',

            // Total users
            'totalUsers'       => $userModel->countAllResults(),

            // Total files in the system
            'totalFiles'       => $fileModel->countAllResults(),

            // Files with status = 'pending'
            'newUploadedFiles' => (new FileModel())->where('status', 'pending')->countAllResults(),

            // Pending archive requests (if your RequestModel tracks them)
            'pendingRequests'  => (new RequestModel())->where('status', 'pending')->countAllResults(),
        ];

        return view('superadmin/dashboard', $data);
    }
}
