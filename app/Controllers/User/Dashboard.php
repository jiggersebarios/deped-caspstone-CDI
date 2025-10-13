<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\UserDashboardModel;
use App\Models\FileModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();

        // Ensure only logged-in users with role "user" can access
        if ($session->get('role') !== 'user') {
            return redirect()->to('/login')->with('error', 'Unauthorized access.');
        }

        $userId = $session->get('id');

        $userModel = new UserDashboardModel();
        $fileModel = new FileModel();

        $data = [
            'title' => 'User Dashboard - HR Archiving System',
            'user' => $userModel->getUserInfo($userId),
            'uploaded_files' => $fileModel->where('uploaded_by', $userId)->findAll(),
        ];

        return view('user/dashboard', $data);
    }
}
