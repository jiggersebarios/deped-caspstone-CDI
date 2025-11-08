<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\RequestTokenModel;

class RequestModel extends Model
{
    protected $table = 'file_requests';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'file_id',
        'user_id',
        'reason',
        'status',
        'requested_at',
        'approved_at',
        'downloaded_at',
        'download_token'
    ];

    protected $useTimestamps = false;

    // --------------------------
    // CREATE NEW REQUEST
    // --------------------------
    public function createRequest($fileId, $userId, $reason)
    {
        $existing = $this->where('file_id', $fileId)
                         ->where('user_id', $userId)
                         ->whereIn('status', ['pending', 'approved'])
                         ->first();

        if ($existing) {
            return false; // Prevent duplicate requests
        }

        return $this->insert([
            'file_id'      => $fileId,
            'user_id'      => $userId,
            'reason'       => $reason,
            'status'       => 'pending',
            'requested_at' => date('Y-m-d H:i:s')
        ]);
    }

    // --------------------------
    // APPROVE REQUEST + TOKEN
    // --------------------------
    public function approveRequest($id)
    {
        // 1️⃣ Approve the request
        $this->update($id, [
            'status'      => 'approved',
            'approved_at' => date('Y-m-d H:i:s')
        ]);

        // 2️⃣ Generate token via RequestTokenModel
        $tokenModel = new RequestTokenModel();
        $token = $tokenModel->createToken($id);

        // 3️⃣ Save the same token in file_requests.download_token
        $this->update($id, [
            'download_token' => $token
        ]);

        return $token;
    }

    // --------------------------
    // DENY REQUEST
    // --------------------------
    public function denyRequest($id)
    {
        return $this->update($id, [
            'status'      => 'denied',
            'approved_at' => date('Y-m-d H:i:s')
        ]);
    }

    // --------------------------
    // MARK AS DOWNLOADED
    // --------------------------
    public function markAsDownloaded($id)
    {
        return $this->update($id, [
            'status'        => 'downloaded',
            'downloaded_at' => date('Y-m-d H:i:s')
        ]);
    }

    // --------------------------
    // FETCH USER REQUESTS
    // --------------------------
    public function getUserActiveRequests($userId)
    {
        return $this->select('file_requests.*, files.file_name')
                    ->join('files', 'files.id = file_requests.file_id', 'left')
                    ->where('file_requests.user_id', $userId)
                    ->whereIn('file_requests.status', ['pending', 'approved'])
                    ->orderBy('file_requests.requested_at', 'DESC')
                    ->findAll();
    }

    public function getUserCompletedFiles($userId)
    {
        return $this->select('file_requests.*, files.file_name')
                    ->join('files', 'files.id = file_requests.file_id', 'left')
                    ->where('file_requests.user_id', $userId)
                    ->where('file_requests.status', 'downloaded')
                    ->orderBy('file_requests.downloaded_at', 'DESC')
                    ->findAll();
    }
}
