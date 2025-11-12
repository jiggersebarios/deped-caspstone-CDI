<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\FileModel;
use App\Models\FolderModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();

        // Restrict access
        if ($session->get('role') !== 'user') {
            return redirect()->to('/login')->with('error', 'Unauthorized access.');
        }

        $userId = $session->get('id');
        $userModel = new UserModel();
        $fileModel = new FileModel();

        // Get user info
        $user = $userModel->find($userId);
        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found.');
        }

        $mainFolderId = $user['main_folder_id'];

        // === Get main folder + all subfolder IDs recursively ===
        $folderIds = $this->getAllFolderIds($mainFolderId);

        // === Count all files in main folder + subfolders ===
        $totalFiles = $fileModel->whereIn('folder_id', $folderIds)->countAllResults();

        // Optional: get files with details
        $uploadedFiles = $fileModel->whereIn('folder_id', $folderIds)->findAll();

        $data = [
            'title'          => 'User Dashboard - HR Archiving System',
            'user'           => $user,
            'totalFiles'     => $totalFiles,
            'uploaded_files' => $uploadedFiles,
        ];

        return view('user/dashboard', $data);
    }

    // Recursive helper function
    private function getAllFolderIds($folderId)
    {
        $folderModel = new FolderModel();
        $allFolderIds = [$folderId]; // include main folder

        $subfolders = $folderModel->where('parent_folder_id', $folderId)->findAll();

        foreach ($subfolders as $sub) {
            $allFolderIds = array_merge($allFolderIds, $this->getAllFolderIds($sub['id']));
        }

        return $allFolderIds;
    }
}
