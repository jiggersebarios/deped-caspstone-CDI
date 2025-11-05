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
    $session  = session();
    $userId   = $session->get('id');
    $userRole = $session->get('role');

    // ==============================
    // Files shared BY current user
    // ==============================
    $sharedFiles = $this->sharedModel
        ->select('shared_files.*, files.file_name, categories.category_name, uploader.username as uploader_name, shared_user.username as shared_to_name')
        ->join('files', 'files.id = shared_files.file_id', 'left')
        ->join('categories', 'categories.id = files.category_id', 'left')
        ->join('users uploader', 'uploader.id = shared_files.shared_by', 'left')
        ->join('users shared_user', 'shared_user.id = shared_files.shared_to', 'left')
        ->where('shared_files.shared_by', $userId)
        ->orderBy('shared_files.created_at', 'DESC')
        ->findAll();

    // ==============================
    // Files shared TO current user
    // ==============================
    $sharedWithMe = $this->sharedModel
        ->select('shared_files.*, files.file_name, categories.category_name, uploader.username as shared_by_name, shared_user.username as shared_to_name')
        ->join('files', 'files.id = shared_files.file_id', 'left')
        ->join('categories', 'categories.id = files.category_id', 'left')
        ->join('users uploader', 'uploader.id = shared_files.shared_by', 'left')
        ->join('users shared_user', 'shared_user.id = shared_files.shared_to', 'left')
        ->where('shared_files.shared_to', $userId)
        ->orderBy('shared_files.created_at', 'DESC')
        ->findAll();

    // ==============================
    // All available files for modal (role-based access)
    // ==============================
    $allFilesQuery = $this->fileModel
        ->select('files.id, files.file_name, categories.category_name, folders.folder_name, users.username as uploader_name, files.folder_id')
        ->join('categories', 'categories.id = files.category_id', 'left')
        ->join('folders', 'folders.id = files.folder_id', 'left')
        ->join('users', 'users.id = files.uploaded_by', 'left')
        ->orderBy('files.uploaded_at', 'DESC');

    // Restrict regular users to their accessible folder scope
    if (!in_array($userRole, ['admin', 'superadmin'])) {
        $userFolderId = $session->get('main_folder_id');

        if ($userFolderId) {
            $folderModel = new \App\Models\FolderModel();

            // Collect all folder IDs within user's scope recursively
            $accessibleFolders = $this->getAllSubfolderIds($userFolderId, $folderModel);
            $accessibleFolders[] = $userFolderId; // Include main folder

            $allFilesQuery->whereIn('files.folder_id', $accessibleFolders);
        } else {
            // No assigned folder → show nothing
            $allFilesQuery->where('files.id', 0);
        }
    }

    $allFiles = $allFilesQuery->findAll();

    // ==============================
    // All users except current one
    // ==============================
    $usersQuery = $this->userModel->select('id, username, role')->orderBy('role', 'ASC');
    if (!empty($userId)) {
        $usersQuery->where('id !=', $userId);
    }
    $users = $usersQuery->findAll();

    return view('shared/sharedfiles', [
        'title'        => 'Shared Files',
        'sharedFiles'  => $sharedFiles,
        'sharedWithMe' => $sharedWithMe,
        'allFiles'     => $allFiles,
        'users'        => $users,
    ]);
}

/**
 * Recursively collect all subfolder IDs for a parent folder
 */
private function getAllSubfolderIds($parentId, $folderModel)
{
    $subfolders = $folderModel->where('parent_folder_id', $parentId)->findAll();
    $ids = [];

    foreach ($subfolders as $subfolder) {
        $ids[] = $subfolder['id'];
        $ids = array_merge($ids, $this->getAllSubfolderIds($subfolder['id'], $folderModel));
    }

    return $ids;
}

    // ==============================
    // SHARE FILE
    // ==============================
public function share()
{
    $session = session();
    $userId  = $session->get('id');

    $fileId      = $this->request->getPost('file_id');
    $targetUsers = $this->request->getPost('target_users');

    if (empty($fileId) || empty($targetUsers)) {
        return redirect()->back()->with('error', 'Please select a file and at least one user to share with.');
    }

    // Ensure $targetUsers is always an array
    if (!is_array($targetUsers)) {
        $targetUsers = [$targetUsers];
    }

    $file = $this->fileModel->find($fileId);
    if (!$file) {
        return redirect()->back()->with('error', 'File not found.');
    }

    // ✅ Get all users this file has already been shared with
    $existingShares = $this->sharedModel
        ->where('file_id', $fileId)
        ->where('shared_by', $userId)
        ->whereIn('shared_to', $targetUsers)
        ->findAll();

    $alreadyShared = array_column($existingShares, 'shared_to');

    // ✅ Prepare new shares for users that haven't received this file yet
    $newShares = [];
    foreach ($targetUsers as $targetId) {
        $targetId = (int) $targetId;
        if (in_array($targetId, $alreadyShared)) {
            continue;
        }

        $newShares[] = [
            'file_id'     => $fileId,
            'file_name'   => $file['file_name'],
            'file_path'   => $file['file_path'],
            'uploaded_by' => $file['uploaded_by'],
            'shared_by'   => $userId,
            'shared_to'   => $targetId,
            'created_at'  => date('Y-m-d H:i:s'),
        ];
    }

    if (!empty($newShares)) {
        $this->sharedModel->insertBatch($newShares);
    }

    // ✅ Prepare feedback message
    $message = '';
    if (!empty($alreadyShared) && empty($newShares)) {
        $message = 'This file was already shared with all selected users.';
    } elseif (!empty($alreadyShared) && !empty($newShares)) {
        $message = 'File shared successfully with some users. (Some were already shared)';
    } else {
        $message = 'File shared successfully!';
    }

    return redirect()->to('/sharedfiles')->with('success', $message);
}

    // ==============================
    // UNSHARE FILE
    // ==============================
    public function unshare($sharedId)
    {
        $session = session();
        $userId  = $session->get('id');

        $record = $this->sharedModel->find($sharedId);
        if (!$record) {
            return redirect()->back()->with('error', 'Shared record not found.');
        }

        if ((int) $record['shared_by'] !== (int) $userId) {
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
