<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FileModel;
use App\Models\RequestModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();
        $userId = $session->get('id');

        // Restrict access
        if ($session->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Unauthorized access.');
        }

        $fileModel = new FileModel();
        $requestModel = new RequestModel();

        // Count all files (can be refined based on admin's scope if needed)
        $totalFiles = $fileModel->countAllResults();

        // Count all files with 'pending' status for the "Manage Uploads" card
        $newUploadedFiles = $fileModel->where('status', 'pending')->countAllResults();

        // Count requests made BY THIS ADMIN that are 'approved'
        $approvedRequests = $requestModel
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->countAllResults();

        $data = [
            'title'            => 'Admin Dashboard',
            'totalFiles'       => $totalFiles,
            'newUploadedFiles' => $newUploadedFiles,
            'approvedRequests' => $approvedRequests, // Use this new variable in the view
        ];

        return view('admin/dashboard', $data);
    }
}