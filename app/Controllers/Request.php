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
    // USER SIDE
    // ============================================================
public function userRequests()
{
    $userId = session()->get('id');
    if (!$userId) return redirect()->to('/login')->with('error', 'You must be logged in.');

$data = [
    'requests'       => $this->requestModel->getUserActiveRequests($userId), // pending + approved
    'completedFiles' => $this->requestModel->getUserCompletedFiles($userId), // downloaded
    'files'          => $this->fileModel->findAll()
];

return view('shared/request', $data);

}




    public function submit()
    {
        $userId = session()->get('id');
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'You must be logged in to make a request.');
        }

        $fileId = $this->request->getPost('file_id');
        $reason = $this->request->getPost('reason');

        if (empty($fileId) || empty($reason)) {
            return redirect()->back()->with('error', 'Please fill in all required fields.');
        }

        $this->requestModel->createRequest($fileId, $userId, $reason);
        return redirect()->back()->with('success', 'Your request has been sent to admin.');
    }

    // ============================================================
    // ADMIN / SUPERADMIN SIDE
    // ============================================================
 public function manage()
{
    $role = session()->get('role');

    // Only fetch pending or approved requests for main table
    $requests = $this->requestModel
                     ->select('file_requests.*, files.file_name, users.username')
                     ->join('files', 'files.id = file_requests.file_id', 'left')
                     ->join('users', 'users.id = file_requests.user_id', 'left')
                     ->whereIn('file_requests.status', ['pending', 'approved'])
                     ->orderBy('file_requests.requested_at', 'DESC')
                     ->findAll();

    // Fetch completed/downloaded requests for modal
    $completedFiles = $this->requestModel
                           ->select('file_requests.*, files.file_name, users.username')
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


    public function deny($id)
    {
        $this->requestModel->denyRequest($id);
        return redirect()->back()->with('error', 'Request denied.');
    }

    // ============================================================
    // DOWNLOAD HANDLER
    // ============================================================
    public function download($token)
    {
        $tokenData = $this->tokenModel->validateToken($token);
        if (!$tokenData) {
            return '⛔ This download link has expired or already been used.';
        }

        $request = $this->requestModel->find($tokenData['request_id']);
        if (!$request) {
            return '⚠️ Invalid request reference.';
        }

        $file = $this->fileModel->find($request['file_id']);
        if (is_object($file)) {
            $file = (array) $file;
        }

        if (empty($file['file_path']) || !file_exists($file['file_path'])) {
            return '⚠️ File not found on server.';
        }

        // ✅ Mark token as used and update request status
        $this->tokenModel->markUsed($token);
$this->requestModel->update($request['id'], [
    'status'        => 'downloaded',           // must match enum
    'downloaded_at' => date('Y-m-d H:i:s')
]);


        if (ob_get_level()) ob_end_clean();

        return $this->response
            ->download($file['file_path'], null)
            ->setFileName($file['file_name']);
    }
}
