<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FileModel;
use App\Models\SharedFileModel;
use App\Models\UserModel;
use App\Models\SharedTokenModel;
use CodeIgniter\I18n\Time;

class Sharedfiles extends BaseController
{
    protected $fileModel;
    protected $sharedModel;
    protected $userModel;
    protected $tokenModel;

     protected $storagePath;


    public function __construct()
    {
        $this->fileModel   = new FileModel();
        $this->sharedModel = new SharedFileModel();
        $this->userModel   = new UserModel();
        $this->tokenModel = new SharedTokenModel();
        
         $this->storagePath = 'C:\\xampp\\depedfiles\\';
    if (!is_dir($this->storagePath)) mkdir($this->storagePath, 0777, true);

    }

    // ==============================
    // MAIN PAGE (List + Modal Data)
    // ==============================
public function index()
{
    $session = session();
    $userId  = $session->get('id');
    $role    = $session->get('role') ?? 'user';

    // ======================
    // Files shared BY current user
    // ======================
    $sharedFilesQuery = $this->sharedModel
        ->select('shared_files.*, files.file_name, categories.category_name, uploader.username as uploader_name')
        ->join('files', 'files.id = shared_files.file_id', 'left')
        ->join('categories', 'categories.id = files.category_id', 'left')
        ->join('users uploader', 'uploader.id = files.uploaded_by', 'left')
        ->where('shared_files.shared_by', $userId)
        ->orderBy('shared_files.created_at', 'DESC');

    // Regular users: only show files that are not yet downloaded
    if (!in_array($role, ['admin', 'superadmin'])) {
        $sharedFilesQuery->where('shared_files.downloaded', 0);
    }

    $sharedFiles = $sharedFilesQuery->findAll();

    // ======================
    // Files shared TO current user
    // ======================
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
        if (
            in_array((string) $userId, $sharedToList, true) &&
            ((int)$share['downloaded'] === 0 || in_array($role, ['admin', 'superadmin']))
        ) {
            $sharedWithMe[] = $share;
        }
    }

    // ======================
    // Files available for sharing modal
    // ======================
 $allFilesQuery = $this->fileModel
    ->select('files.id, files.file_name, categories.category_name, folders.folder_name, users.username as uploader_name')
    ->join('categories', 'categories.id = files.category_id', 'left')
    ->join('folders', 'folders.id = files.folder_id', 'left')
    ->join('users', 'users.id = files.uploaded_by', 'left')
    ->where('files.status', 'active') 
    ->orderBy('files.uploaded_at', 'DESC');

// Regular users: only their own files
if (!in_array($role, ['admin', 'superadmin'])) {
    $allFilesQuery->where('files.uploaded_by', $userId);
}

$allFiles = $allFilesQuery->findAll();

    // ======================
    // All users except current one
    // ======================
    $users = $this->userModel
        ->select('id, username, role')
        ->where('id !=', $userId)
        ->orderBy('role', 'ASC')
        ->findAll();

    return view('shared/sharedfiles', [
        'title'        => 'Shared Files',
        'sharedFiles'  => $sharedFiles,
        'sharedWithMe' => $sharedWithMe,
        'allFiles'     => $allFiles,
        'users'        => $users,
        'role'         => $role,
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

    // ✅ Check if file is active
    if (strtolower($file['status']) !== 'active') {
        return redirect()->back()->with('error', 'You can only share active files.');
    }

    // ✅ Check ownership for regular users
    if ($role === 'user' && (int) $file['uploaded_by'] !== (int) $userId) {
        return redirect()->back()->with('error', 'You can only share your own uploaded files.');
    }

    $sharedTo = implode(',', $targetUsers);



    // ✅ Save sharing record
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
    $session = session();
    $userId  = $session->get('id');
    $role    = $session->get('role') ?? 'user';

    $record = $this->sharedModel->find($sharedId);
    if (!$record) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Shared file not found.');
    }

    // Ensure only the sharer, recipients, or admin can download
    $sharedToList = explode(',', $record['shared_to'] ?? '');
    if (
        $role !== 'admin' &&
        $role !== 'superadmin' &&
        (int) $record['shared_by'] !== (int) $userId &&
        !in_array((string) $userId, $sharedToList, true)
    ) {
        return redirect()->back()->with('error', 'You are not authorized to download this file.');
    }

    // Ensure file exists and is active
    $file = (new \App\Models\FileModel())->find($record['file_id']);
    if (!$file || strtolower($file['status']) !== 'active') {
        return redirect()->back()->with('error', 'File is no longer active.');
    }

    // For regular users: Allow only one-time download
    if (!in_array($role, ['admin', 'superadmin'])) {
        if ((int) $record['downloaded'] === 1) {
            return redirect()->back()->with('error', 'This file has already been downloaded.');
        }
    }

    // Build absolute path using your new storage path
    $storagePath = 'C:\\xampp\\depedfiles\\';
    $filePath = $record['file_path']; // stored relative path/filename
    $path = $storagePath . $filePath;

    if (!file_exists($path)) {
        log_message('error', 'File not found: ' . $path);
        return redirect()->back()->with('error', 'File not found on server.');
    }

    // Mark as downloaded (one-time rule)
    if (!in_array($role, ['admin', 'superadmin'])) {
        $this->sharedModel->update($sharedId, [
            'downloaded' => 1,
        ]);
    }

    // Serve the file
    if (ob_get_level()) ob_end_clean();

    return $this->response->download($path, null)->setFileName($record['file_name']);
}


public function generateToken($sharedId)
{
    $record = $this->sharedModel->find($sharedId);
    if (!$record) {
        return redirect()->back()->with('error', 'Shared record not found.');
    }

    // Generate unique token
    $token = bin2hex(random_bytes(16));

    // Set expiry (e.g., 24 hours from now)
    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 day'));

    // Save token
    $this->tokenModel->insert([
        'shared_id'  => $sharedId,
        'token'      => $token,
        'created_at' => date('Y-m-d H:i:s'),
        'expires_at' => $expiresAt,
    ]);

    // Build public link
    $link = site_url('sharedfiles/access/' . $token);

    return redirect()->back()->with('success', 'Share link generated: ' . $link);
}

public function access($token)
{
    $tokenData = $this->tokenModel
        ->where('token', $token)
        ->first();

    if (!$tokenData) {
        return redirect()->to('/')->with('error', 'Invalid or expired token.');
    }

    // Check if expired
    if ($tokenData['expires_at'] && strtotime($tokenData['expires_at']) < time()) {
        return redirect()->to('/')->with('error', 'This shared link has expired.');
    }

    // Fetch the shared file
    $shared = $this->sharedModel->find($tokenData['shared_id']);
    if (!$shared) {
        return redirect()->to('/')->with('error', 'Shared file not found.');
    }

    $file = $this->fileModel->find($shared['file_id']);
    if (!$file) {
        return redirect()->to('/')->with('error', 'File not found.');
    }

    // Validate file status
    if (strtolower($file['status']) !== 'active') {
        return redirect()->to('/')->with('error', 'File is no longer active.');
    }

    // Build file path
    $filePath = FCPATH . 'uploads/' . basename($file['file_path']);
    if (!file_exists($filePath)) {
        return redirect()->to('/')->with('error', 'File missing on server.');
    }

    // Optional: mark as downloaded or track access
    $this->sharedModel->update($shared['id'], ['downloaded' => 1]);

    // Serve the file
    if (ob_get_level()) ob_end_clean();
    return $this->response->download($filePath, null)->setFileName($file['file_name']);
}

}
