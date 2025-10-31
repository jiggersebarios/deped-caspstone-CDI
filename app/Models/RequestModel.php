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
        return $this->insert([
            'file_id'      => $fileId,
            'user_id'      => $userId,
            'reason'       => $reason,
            'status'       => 'pending',
            'requested_at' => date('Y-m-d H:i:s')
        ]);
    }

// Fetch requests that are pending or approved (not downloaded)
public function getUserActiveRequests($userId)
{
    return $this->select('file_requests.*, files.file_name')
                ->join('files', 'files.id = file_requests.file_id', 'left')
                ->where('file_requests.user_id', $userId)
                ->whereIn('file_requests.status', ['pending', 'approved'])
                ->orderBy('file_requests.requested_at', 'DESC')
                ->findAll();
}


  // Fetch all completed/downloaded files for a user
// RequestModel.php
public function getUserCompletedFiles($userId)
{
    return $this->select('file_requests.*, files.file_name')
                ->join('files', 'files.id = file_requests.file_id', 'left')
                ->where('file_requests.user_id', $userId)
                ->where('file_requests.status', 'downloaded') // now valid
                ->orderBy('file_requests.downloaded_at', 'DESC')
                ->findAll();
}


    // --------------------------
    // APPROVE REQUEST
    // --------------------------
    public function approveRequest($id)
    {
        return $this->update($id, [
            'status'      => 'approved',
            'approved_at' => date('Y-m-d H:i:s')
        ]);
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
    // FETCH ALL REQUESTS (ADMIN)
    // --------------------------
    public function getAllRequestWithFiles()
    {
        return $this->select('file_requests.*, files.file_name')
                    ->join('files', 'files.id = file_requests.file_id', 'left')
                    ->orderBy('file_requests.requested_at', 'DESC')
                    ->findAll();
    }
}
