<?php

namespace App\Controllers;

use App\Models\FileModel;
use App\Models\CategoryModel;
use CodeIgniter\Controller;
use App\Controllers\NotificationController;
use App\Models\NotificationModel;
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

        // Create a notification for the uploader
        $notifCtrl = new \App\Controllers\NotificationController();
        $notifCtrl->createNotification(
            $file['uploaded_by'], // userId
            $id,                  // fileId
            'accepted',           // type
            'File Accepted',      // title
            'Your file "' . $file['file_name'] . '" has been accepted.' // message
        );

        return redirect()->back()->with('success', 'File has been accepted and activated.');
    }

    /**
     *  Reject a file → mark as rejected
     */
    public function reject($id)
    {
        $fileModel = new \App\Models\FileModel();
        $notifCtrl = new \App\Controllers\NotificationController();

        $file = $fileModel->find($id);

        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Get rejection reason from admin form input
        $reason = $this->request->getPost('reason') ?? 'No reason provided';

        // Create notification to the uploader
        $notifCtrl->createNotification(
            $file['uploaded_by'],         // uploader ID
            $id,                          // file ID
            'rejected',                   // type
            'File Rejected',              // title
            'Your file "' . $file['file_name'] . '" has been rejected by the admin.', // message
            $reason                       // rejection reason
        );

        // Now, delete the file from the database and server
        $fileModel->deleteFileWithAudit($id, session()->get('id'), 'Rejected by admin: ' . $reason);

        return redirect()->back()->with('success', 'File rejected and uploader notified.');
    }


}
