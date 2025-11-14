<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FileModel;
use App\Models\RequestModel;
use App\Models\SharedFileModel;

class NotificationController extends BaseController
{
    public function getNotifications()
    {
        $session = session();
        $userId  = $session->get('id');
        $role    = $session->get('role') ?? 'user';

        $fileModel      = new FileModel();
        $requestModel   = new RequestModel();
        $sharedModel    = new SharedFileModel();

        // ===============================
        // COUNT FILES SHARED TO ME
        // ===============================
        $sharedRecords = $sharedModel->findAll();
        $sharedWithMeCount = 0;

        foreach ($sharedRecords as $share) {
            $sharedToList = explode(',', $share['shared_to'] ?? '');

            if (in_array((string) $userId, $sharedToList, true)) {
                // For users: only count files not downloaded
                if ($role === 'user' && $share['downloaded'] == 1) {
                    continue;
                }
                $sharedWithMeCount++;
            }
        }

        // ===============================
        // COUNT APPROVED REQUESTS FOR USERS
        // ===============================
        $approvedRequestsCount = 0;

        if ($role === 'user') {
            $approvedRequestsCount = $requestModel
                ->where('status', 'approved')
                ->where('user_id', $userId)
                ->countAllResults();
        }

        // Build return data
        $data = [
            'newUploadedFiles'    => $fileModel->where('status', 'pending')->countAllResults(),
            'pendingRequests'     => $role === 'user' ? $approvedRequestsCount : $requestModel->where('status', 'pending')->countAllResults(),
            'sharedWithMe'        => $sharedWithMeCount
        ];

        return $this->response->setJSON($data);
    }
}
