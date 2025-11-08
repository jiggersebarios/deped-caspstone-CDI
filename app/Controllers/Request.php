<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RequestModel;
use App\Models\RequestTokenModel;
use App\Models\FileModel;

class Request extends BaseController
{
    protected $requestModel;
    protected $tokenModel;
    protected $fileModel;
    protected $storagePath;

    public function __construct()
    {
        $this->requestModel = new RequestModel();
        $this->tokenModel   = new RequestTokenModel();
        $this->fileModel    = new FileModel();

        $this->storagePath = 'C:\\xampp\\depedfiles\\';
    if (!is_dir($this->storagePath)) mkdir($this->storagePath, 0777, true);

    }

    // ============================================================
    // 👤 USER SIDE
    // ============================================================
    public function userRequests()
    {
        $userId = session()->get('id');
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'You must be logged in.');
        }

        // Fetch active requests (pending or approved)
        $requests = $this->requestModel->getUserActiveRequests($userId);

        // Attach saved download token from file_requests
        foreach ($requests as &$req) {
            $req['download_token'] = ($req['status'] === 'approved') ? $req['download_token'] : null;
        }
        unset($req);

        // Fetch completed downloads separately
        $completedFiles = $this->requestModel->getUserCompletedFiles($userId);

        // Fetch available files for requesting
        $files = $this->fileModel->findAll();

        return view('shared/request', [
            'title' => 'Request for Archive Files',
            'requests' => $requests,
            'completedFiles' => $completedFiles,
            'files' => $files
        ]);
    }

    // ============================================================
    // 📨 SUBMIT REQUEST
    // ============================================================
    public function submit()
    {
        $userId = session()->get('id');
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'You must be logged in to make a request.');
        }

        $fileId = $this->request->getPost('file_id');
        $reason = trim($this->request->getPost('reason'));

        if (empty($fileId) || empty($reason)) {
            return redirect()->back()->with('error', 'Please fill in all required fields.');
        }

        $this->requestModel->createRequest($fileId, $userId, $reason);

        return redirect()->back()->with('success', 'Your request has been sent to admin.');
    }

    // ============================================================
    // 🧑‍💼 ADMIN / SUPERADMIN SIDE
    // ============================================================
    public function manage()
    {
        $role = session()->get('role');

        // Pending / approved requests
        $requests = $this->requestModel
            ->select('file_requests.id, file_requests.file_id, file_requests.user_id, file_requests.reason, 
                      file_requests.status, file_requests.requested_at, file_requests.approved_at, file_requests.download_token,
                      files.file_name, users.username')
            ->join('files', 'files.id = file_requests.file_id', 'left')
            ->join('users', 'users.id = file_requests.user_id', 'left')
            ->whereIn('file_requests.status', ['pending', 'approved'])
            ->orderBy('file_requests.requested_at', 'DESC')
            ->findAll();

        // Downloaded requests
        $completedFiles = $this->requestModel
            ->select('file_requests.id, file_requests.file_id, file_requests.user_id, file_requests.reason, 
                      file_requests.status, file_requests.requested_at, file_requests.downloaded_at,
                      files.file_name, users.username')
            ->join('files', 'files.id = file_requests.file_id', 'left')
            ->join('users', 'users.id = file_requests.user_id', 'left')
            ->where('file_requests.status', 'downloaded')
            ->orderBy('file_requests.downloaded_at', 'DESC')
            ->findAll();

        return view('shared/manage_request', [
            'title' => 'Manage File Requests',
            'requests' => $requests,
            'completedFiles' => $completedFiles,
            'role' => $role
        ]);
    }

    // ============================================================
    // ✅ APPROVE REQUEST (Generates Token)
    // ============================================================
    public function approve($id)
    {
        $token = $this->requestModel->approveRequest($id); // token is saved in file_requests
        return redirect()->back()->with('success', 'Request approved and download token created.');
    }

    // ============================================================
    // ❌ DENY REQUEST
    // ============================================================
    public function deny($id)
    {
        $this->requestModel->denyRequest($id);
        return redirect()->back()->with('error', 'Request denied.');
    }

    // ============================================================
    // 🔽 DOWNLOAD BY TOKEN (Secure)
    // ============================================================
public function directDownload($requestId = null)
{
    $userId = session()->get('id');
    if (!$userId) {
        return redirect()->to('/login')->with('error', 'You must be logged in.');
    }

    $token = $this->request->getGet('token');

    // 1️⃣ Fetch request based on token or request ID
    if ($token) {
        $tokenData = $this->tokenModel->validateToken($token);
        if (!$tokenData) {
            return redirect()->back()->with('error', 'Invalid or expired token.');
        }
        $request = $this->requestModel->find($tokenData['request_id']);
    } elseif ($requestId) {
        $request = $this->requestModel->find($requestId);
        if (!$request) {
            return redirect()->back()->with('error', 'Request not found.');
        }

        // Fallback: get latest unused token
        $tokenData = $this->tokenModel
            ->where('request_id', $requestId)
            ->where('used', 0)
            ->first();
        if (!$tokenData) {
            return redirect()->back()->with('error', 'No valid download token available.');
        }
        $token = $tokenData['token'];
    } else {
        return redirect()->back()->with('error', 'No token or request ID provided.');
    }

    // 2️⃣ Check ownership
    if ((int)$request['user_id'] !== (int)$userId) {
        return redirect()->back()->with('error', 'You are not authorized to download this file.');
    }

    // 3️⃣ Ensure approved or downloaded
    if (!in_array($request['status'], ['approved', 'downloaded'])) {
        return redirect()->back()->with('error', 'This file is not approved for download.');
    }

    // 4️⃣ Get the file record
    $file = $this->fileModel->find($request['file_id']);
    if (!$file) {
        return redirect()->back()->with('error', 'File record not found.');
    }

    // Convert to array if object
    if (is_object($file)) $file = (array)$file;

    // 5️⃣ Build full path using storage path
    $storagePath = 'C:\\xampp\\depedfiles\\';
    $filePath = $storagePath . $file['file_path'];

    if (!file_exists($filePath)) {
        log_message('error', 'File missing on server: ' . $filePath);
        return redirect()->to('/')->with('error', 'File missing on server.');
    }

    // 6️⃣ Mark as downloaded and token as used
    $this->requestModel->markAsDownloaded($request['id']);
    $this->tokenModel->markUsed($token);

    // 7️⃣ Serve file
    if (ob_get_level()) ob_end_clean();
    return $this->response
        ->download($filePath, null, true)
        ->setFileName($file['file_name']);
}


}
