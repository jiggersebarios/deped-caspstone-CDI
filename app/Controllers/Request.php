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

    public function __construct()
    {
        $this->requestModel = new RequestModel();
        $this->tokenModel   = new RequestTokenModel();
        $this->fileModel    = new FileModel();
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

    // 1️⃣ Fetch active requests (pending or approved)
    $requests = $this->requestModel
        ->getUserActiveRequests($userId);

    // 2️⃣ Attach a single unused token to approved requests
    $tokenModel = new \App\Models\RequestTokenModel();
    foreach ($requests as &$req) {
        if ($req['status'] === 'approved') {
            $tokenData = $tokenModel
                ->where('request_id', $req['id'])
                ->where('used', 0)
                ->first();

            $req['download_token'] = $tokenData['token'] ?? null;
        } else {
            $req['download_token'] = null;
        }
    }
    unset($req); // break reference

    // 3️⃣ Fetch completed downloads separately
    $completedFiles = $this->requestModel->getUserCompletedFiles($userId);

    // 4️⃣ Fetch available files for requesting
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

    // Fetch pending or approved requests (main table)
    $requests = $this->requestModel
        ->select('file_requests.id, file_requests.file_id, file_requests.user_id, file_requests.reason, 
                  file_requests.status, file_requests.requested_at, file_requests.approved_at, 
                  files.file_name, users.username')
        ->join('files', 'files.id = file_requests.file_id', 'left')
        ->join('users', 'users.id = file_requests.user_id', 'left')
        ->whereIn('file_requests.status', ['pending', 'approved'])
        ->groupBy('file_requests.id') // prevent duplicates
        ->orderBy('file_requests.requested_at', 'DESC')
        ->findAll();

    // Fetch downloaded requests (modal)
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
    // ✅ APPROVE REQUEST (GENERATES TOKEN)
    // ============================================================
    public function approve($id)
    {
        // 1️⃣ Approve the request
        $this->requestModel->approveRequest($id);

        // 2️⃣ Generate secure token (valid 24 hours)
        $token = $this->tokenModel->createToken($id, 24);

        // (No need to store in request table — tokens live in requests_tokens)
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
// 🔽 ADMIN / SUPERADMIN DIRECT DOWNLOAD BY REQUEST ID (Secure)
// ============================================================
public function directDownload($requestId)
{
    $role = session()->get('role');
    $userId = session()->get('id');

    // 1️⃣ Fetch the request record
    $request = $this->requestModel->find($requestId);
    if (!$request) {
        return redirect()->back()->with('error', 'Request not found.');
    }

    // 2️⃣ Ensure only the requester can download their own request
    if ((int)$request['user_id'] !== (int)$userId) {
        return redirect()->back()->with('error', 'You are not authorized to download this file.');
    }

    // 3️⃣ Ensure the request is approved
    if (!in_array($request['status'], ['approved', 'downloaded'])) {
        return redirect()->back()->with('error', 'This file is not yet approved for download.');
    }

    // 4️⃣ Fetch the file record
    $file = $this->fileModel->find($request['file_id']);
    if (!$file) {
        return redirect()->back()->with('error', 'File record not found.');
    }

    // Convert to array if needed
    if (is_object($file)) $file = (array)$file;

    if (empty($file['file_path']) || !file_exists($file['file_path'])) {
        return redirect()->back()->with('error', 'File not found on the server.');
    }

    // 5️⃣ Mark request as downloaded (for tracking)
    $this->requestModel->update($request['id'], [
        'status'        => 'downloaded',
        'downloaded_at' => date('Y-m-d H:i:s')
    ]);

    // 6️⃣ Serve file securely
    if (ob_get_level()) ob_end_clean();

    return $this->response
        ->download($file['file_path'], null, true)
        ->setFileName($file['file_name']);
}

}
