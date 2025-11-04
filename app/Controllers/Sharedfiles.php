<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FileModel;
use App\Models\SharedFileModel;
use App\Models\UserModel;

class Sharedfiles extends BaseController
{
    protected $fileModel;
    protected $sharedModel;
    protected $userModel;

    public function __construct()
    {
        $this->fileModel   = new FileModel();
        $this->sharedModel = new SharedFileModel();
        $this->userModel   = new UserModel();
    }

    // ==============================
    // MAIN PAGE (List + Modal Data)
    // ==============================
    public function index()
    {
        $session = session();
        $userId  = $session->get('id');
        $role    = $session->get('role') ?? 'user';

        // ✅ Files shared BY current user
        $sharedFiles = $this->sharedModel
            ->select('shared_files.*, files.file_name, categories.category_name, uploader.username as uploader_name')
            ->join('files', 'files.id = shared_files.file_id', 'left')
            ->join('categories', 'categories.id = files.category_id', 'left')
            ->join('users uploader', 'uploader.id = files.uploaded_by', 'left')
            ->where('shared_files.shared_by', $userId)
            ->orderBy('shared_files.created_at', 'DESC')
            ->findAll();

        // ✅ Files shared TO current user
        $allShared = $this->sharedModel
            ->select('shared_files.*, files.file_name, categories.category_name, sharer.username as shared_by')
            ->join('files', 'files.id = shared_files.file_id', 'left')
            ->join('categories', 'categories.id = files.category_id', 'left')
            ->join('users sharer', 'sharer.id = shared_files.shared_by', 'left')
            ->orderBy('shared_files.created_at', 'DESC')
            ->findAll();

        $sharedWithMe = [];
        foreach ($allShared as $share) {
            $sharedToList = explode(',', $share['shared_to'] ?? '');
            if (in_array((string) $userId, $sharedToList, true)) {
                $sharedWithMe[] = $share;
            }
        }

        // ✅ All available files for modal
        $allFiles = $this->fileModel
            ->select('files.id, files.file_name, categories.category_name, users.username as uploader_name')
            ->join('categories', 'categories.id = files.category_id', 'left')
            ->join('users', 'users.id = files.uploaded_by', 'left')
            ->orderBy('files.uploaded_at', 'DESC')
            ->findAll();

        // ✅ All users except current one
        $users = $this->userModel
            ->select('id, username, role')
            ->where('id !=', $userId)
            ->orderBy('role', 'ASC')
            ->findAll();

        return view('shared/sharedfiles', [
            'title'         => 'Shared Files',
            'sharedFiles'   => $sharedFiles,
            'sharedWithMe'  => $sharedWithMe,
            'allFiles'      => $allFiles,
            'users'         => $users,
            'role'          => $role,
        ]);
    }

    // ==============================
    // SHARE FILE
    // ==============================
    public function share()
    {
        $session = session();
        $userId  = $session->get('id');
        $role    = $session->get('role') ?? 'user';

        $fileId      = $this->request->getPost('file_id');
        $targetUsers = $this->request->getPost('target_users');

        if (empty($fileId) || empty($targetUsers)) {
            return redirect()->back()->with('error', 'Please select a file and at least one user to share with.');
        }

        $file = $this->fileModel->find($fileId);
        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        if ($role === 'user' && (int) $file['uploaded_by'] !== (int) $userId) {
            return redirect()->back()->with('error', 'You can only share your own uploaded files.');
        }

        $sharedTo = implode(',', $targetUsers);
        $exists = $this->sharedModel
            ->where('file_id', $fileId)
            ->where('shared_by', $userId)
            ->where('shared_to', $sharedTo)
            ->first();

        if ($exists) {
            return redirect()->back()->with('info', 'You already shared this file with these users.');
        }

        $this->sharedModel->insert([
            'file_id'     => $fileId,
            'file_name'   => $file['file_name'],
            'file_path'   => $file['file_path'],
            'uploaded_by' => $file['uploaded_by'],
            'shared_by'   => $userId,
            'shared_to'   => $sharedTo,
            'shared_role' => $role,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/sharedfiles')->with('success', 'File shared successfully.');
    }

    // ==============================
    // UNSHARE FILE
    // ==============================
    public function unshare($sharedId)
    {
        $session = session();
        $userId  = $session->get('id');
        $role    = $session->get('role') ?? 'user';

        $record = $this->sharedModel->find($sharedId);
        if (!$record) {
            return redirect()->back()->with('error', 'Shared record not found.');
        }

        if (!in_array($role, ['admin', 'superadmin']) && (int) $record['shared_by'] !== (int) $userId) {
            return redirect()->back()->with('error', 'You are not allowed to unshare this file.');
        }

        $this->sharedModel->delete($sharedId);
        return redirect()->back()->with('success', 'File unshared successfully.');
    }

    // ==============================
    // DOWNLOAD SHARED FILE
    // ==============================
    public function download($sharedId)
    {
        $record = $this->sharedModel->find($sharedId);
        if (!$record) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Shared file not found.');
        }

        $path = FCPATH . ltrim($record['file_path'], '/\\');
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        return $this->response->download($path, null);
    }
}
