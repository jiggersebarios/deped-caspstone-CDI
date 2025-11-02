<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FileModel;
use App\Models\RequestModel;

class Dashboard extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Unauthorized access');
        }

        $fileModel = new FileModel();
        $requestModel = new RequestModel(); // if you track archive requests

        $data = [
            'title'            => 'Admin Dashboard',
            'role'             => 'admin',
            'totalFiles'       => $fileModel->countAllResults(),
            'newUploadedFiles' => (new FileModel())->where('status', 'pending')->countAllResults(),
            'pendingRequests'  => $requestModel->where('status', 'pending')->countAllResults(), // optional
        ];

        return view('admin/dashboard', $data);
    }
}
