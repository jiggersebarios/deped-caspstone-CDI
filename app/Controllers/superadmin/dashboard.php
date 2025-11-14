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
        $userModel    = new UserModel();
        $fileModel    = new FileModel();
        $requestModel = new RequestModel();

        // Prepare dashboard data
        $data = [
            'title'             => 'SuperAdmin Dashboard',
            'role'              => 'superadmin',

            // Total users
            'totalUsers'        => $userModel->countAllResults(),

            // Total files in the system
            'totalFiles'        => $fileModel->countAllResults(),

            // Files pending approval
            'newUploadedFiles'  => $fileModel->where('status', 'pending')->countAllResults(),

            // Requests pending approval
            'pendingRequests'   => $requestModel->where('status', 'pending')->countAllResults(),
        ];

        return view('superadmin/dashboard', $data);
    }


    // ===============================
    // AJAX NOTIFICATION ENDPOINT
    // ===============================
    public function getNotifications()
    {
        $fileModel    = new FileModel();
        $requestModel = new RequestModel();

        $data = [
            'newUploadedFiles' => $fileModel->where('status', 'pending')->countAllResults(),
            'pendingRequests'  => $requestModel->where('status', 'pending')->countAllResults(),
        ];

        return $this->response->setJSON($data);
    }
}
