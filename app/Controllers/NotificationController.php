<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FileModel;
use App\Models\RequestModel;
use App\Models\SharedFileModel;
use App\Models\NotificationModel;

class NotificationController extends BaseController
{
    public function getNotifications()
    {
        $session = session();
        $userId  = $session->get('id');
        $role    = $session->get('role') ?? 'user';

        $fileModel    = new FileModel();
        $requestModel = new RequestModel();
        $sharedModel  = new SharedFileModel();

        // ===============================
        // COUNT FILES SHARED TO ME
        // ===============================
        $sharedRecords = $sharedModel->findAll();
        $sharedWithMeCount = 0;

        foreach ($sharedRecords as $share) {
            $sharedToList = explode(',', $share['shared_to'] ?? '');

            if (in_array((string) $userId, $sharedToList, true)) {
                // For USERS: only count NOT downloaded
                if ($role === 'user' && $share['downloaded'] == 1) {
                    continue;
                }
                $sharedWithMeCount++;
            }
        }

        // ===============================
        // COUNT APPROVED REQUESTS
        // ===============================
        $pendingRequestsCount = 0;

        if (in_array($role, ['user', 'admin'])) {
            $pendingRequestsCount = $requestModel
                ->where('status', 'approved')
                ->where('user_id', $userId)
                ->countAllResults();
        } else {
            $pendingRequestsCount = $requestModel
                ->where('status', 'pending')
                ->countAllResults();
        }

        // ===============================
        // COUNT USER-SPECIFIC NOTIFICATIONS
        // ===============================
        $notifModel = new NotificationModel();
        $unreadNotifications = $notifModel
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();

        // Return all counts
        $data = [
            'newUploadedFiles'  => $fileModel->where('status', 'pending')->countAllResults(),
            'pendingRequests'   => $pendingRequestsCount,
            'sharedWithMe'      => $sharedWithMeCount,
            'unreadNotifications' => $unreadNotifications
        ];

        return $this->response->setJSON($data);
    }

    // =======================================================
    // CREATE NOTIFICATION (for rejection, approval, etc.)
    // =======================================================
   public function createNotification($userId, $fileId, $type, $title, $message, $reason = null)
{
    $notifModel = new \App\Models\NotificationModel();

    return $notifModel->insert([
        'user_id' => $userId,
        'file_id' => $fileId,
        'type'    => $type,
        'title'   => $title,
        'message' => $message,
        'reason'  => $reason,
        'is_read' => 0
    ]);
}

    // =======================================================
    // GET NOTIFICATIONS FOR USER DASHBOARD
    // =======================================================
    public function fetchUserNotifications()
    {
        $session = session();
        $userId = $session->get('id');

        $notifModel = new NotificationModel();

        $notifications = $notifModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->response->setJSON($notifications);
    }


  public function delete($id)
{
    $notifModel = new NotificationModel();

    if ($notifModel->find($id)) {
        $notifModel->delete($id);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Notification removed'
        ]);
    }

    return $this->response->setJSON([
        'status'  => 'error',
        'message' => 'Notification not found'
    ]);
}


    
}
