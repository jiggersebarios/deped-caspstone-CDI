<?php

namespace App\Controllers;

use App\Models\FileModel;
use App\Models\CategoryModel;
use App\Models\UserModel;

class ManageUploads extends BaseController
{
    public function index()
    {
        $session = session();
        $role = $session->get('role');

        if (!in_array($role, ['admin', 'superadmin'])) {
            return redirect()->to('/login')->with('error', 'Access denied.');
        }

        $fileModel = new FileModel();
        $categoryModel = new CategoryModel();
        $userModel = new UserModel();

        // Optional filters (status, category)
        $statusFilter = $this->request->getGet('status');
        $categoryFilter = $this->request->getGet('category_id');

        $query = $fileModel->select('
                files.*,
                categories.category_name,
                users.username AS uploader_name
            ')
            ->join('categories', 'categories.id = files.category_id', 'left')
            ->join('users', 'users.id = files.uploaded_by', 'left');

        if (!empty($statusFilter)) {
            $query->where('files.status', $statusFilter);
        }

        if (!empty($categoryFilter)) {
            $query->where('files.category_id', $categoryFilter);
        }

        $files = $query->orderBy('files.uploaded_at', 'DESC')->findAll();

        return view('shared/manage_uploads', [
            'files' => $files,
            'categories' => $categoryModel->findAll(),
            'selectedStatus' => $statusFilter,
            'selectedCategory' => $categoryFilter,
            'role' => $role,
        ]);
    }
}
