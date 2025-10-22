<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FileModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // Restrict access to admin only
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Unauthorized access');
        }

        $fileModel = new FileModel();

        $data = [
            'title'            => 'Admin Dashboard',
            'role'             => 'admin',
            'totalFiles'       => $fileModel->countAllResults(),
            'pendingRequests'  => 0, // Placeholder — add logic later if needed
            'newUploadedFiles' => $fileModel->where('status', 'pending')->countAllResults(),
        ];

        return view('admin/dashboard', $data);
    }
}
