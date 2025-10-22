<?php

namespace App\Controllers;

use App\Models\FileModel;
use App\Models\CategoryModel;
use CodeIgniter\Controller;

class ManageUploads extends Controller
{
    public function index()
    {
        $fileModel = new FileModel();

        $pendingFiles = $fileModel->select('
                files.*,
                categories.category_name,
                users.username AS uploader_name
            ')
            ->join('categories', 'categories.id = files.category_id', 'left')
            ->join('users', 'users.id = files.uploaded_by', 'left')
            ->where('files.status', 'pending')
            ->orderBy('files.uploaded_at', 'DESC')
            ->findAll();

        $session = session();
        $role = $session->get('role') ?? 'admin';

        return view('shared/ManageUploads', [
            'pendingFiles' => $pendingFiles,
            'role' => $role
        ]);
    }

    /**
     * ✅ Accept a pending file → set as active + auto-archive timer
     */
    public function accept($id)
    {
        $fileModel = new FileModel();
        $file = $fileModel->find($id);

        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Activate using existing logic in FileModel
        $fileModel->activateFile($id);

        return redirect()->back()->with('success', 'File has been accepted and activated.');
    }

    /**
     * ❌ Reject a file → mark as rejected
     */
    public function reject($id)
    {
        $fileModel = new FileModel();
        $file = $fileModel->find($id);

        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $fileModel->update($id, [
            'status' => 'rejected',
            'is_archived' => 0,
        ]);

        return redirect()->back()->with('success', 'File has been rejected.');
    }
}
