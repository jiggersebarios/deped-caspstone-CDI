<?php

namespace App\Models;

use CodeIgniter\Model;

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
    // USER: Active + Completed
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

    // --------------------------
    // ADMIN ACTIONS
    // --------------------------
    public function approveRequest($id)
    {
        $this->update($id, [
            'status'      => 'approved',
            'approved_at' => date('Y-m-d H:i:s')
        ]);

        // Auto-generate token for approved request
        $tokenModel = new \App\Models\RequestTokenModel();
        $tokenModel->createToken($id);
    }

    public function denyRequest($id)
    {
        return $this->update($id, [
            'status'      => 'denied',
            'approved_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function markAsDownloaded($id)
    {
        return $this->update($id, [
            'status'        => 'downloaded',
            'downloaded_at' => date('Y-m-d H:i:s')
        ]);
    }

    // --------------------------
    // GENERIC FETCHERS
    // --------------------------
    public function getRequestsByStatus(array $statuses)
    {
        return $this->select('file_requests.*, files.file_name, users.username')
                    ->join('files', 'files.id = file_requests.file_id', 'left')
                    ->join('users', 'users.id = file_requests.user_id', 'left')
                    ->whereIn('file_requests.status', $statuses)
                    ->orderBy('file_requests.requested_at', 'DESC')
                    ->findAll();
    }
}
