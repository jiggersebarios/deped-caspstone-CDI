<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FileModel;
use App\Models\RequestModel;

class Dashboard extends BaseController
{
    protected $fileModel;
    protected $requestModel;

    public function __construct()
    {
        $this->fileModel    = new FileModel();
        $this->requestModel = new RequestModel();
    }

    public function index()
    {
        // --- Security check ---
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Unauthorized access');
        }

        // --- Dashboard stats ---
        $data = [
            'title'            => 'Admin Dashboard',
            'role'             => 'admin',
            'totalFiles'       => $this->fileModel->countAllResults(),
            'newUploadedFiles' => $this->fileModel->where('status', 'pending')->countAllResults(),
            'pendingRequests'  => $this->requestModel->where('status', 'pending')->countAllResults(),
        ];

        return view('admin/dashboard', $data);
    }

    // AJAX notifications
    public function getNotifications()
    {
        $data = [
            'newUploadedFiles' => $this->fileModel->where('status', 'pending')->countAllResults(),
            'pendingRequests'  => $this->requestModel->where('status', 'pending')->countAllResults(),
        ];

        return $this->response->setJSON($data);
    }
}
