<?php

namespace App\Models;

use CodeIgniter\Model;

class FileModel extends Model
{
    protected $table = 'files';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'folder_id',
        'category_id',
        'file_name',
        'file_path',
        'uploaded_by',
        'uploaded_at',
        'archived_at',
        'expired_at',
        'deleted_at',
        'is_archived',
        'status'
    ];

    public $timestamps = false;

    /**
     * ✅ Fetch all files (with category + uploader info)
     */
    public function getFilesWithDetails($folderId = null)
    {
        return $this->select('
                files.*,
                categories.category_name,
                users.username AS uploader_name
            ')
            ->join('categories', 'categories.id = files.category_id', 'left')
            ->join('users', 'users.id = files.uploaded_by', 'left')
            ->where('files.folder_id', $folderId)
            ->orderBy('files.uploaded_at', 'DESC')
            ->findAll();
    }

    /**
     * ✅ Automatically set archive and expiry when file becomes ACTIVE
     */
    public function activateFile($fileId)
    {
        $file = $this->find($fileId);
        if (!$file) return false;

        $categoryModel = new \App\Models\CategoryModel();
        $category = $categoryModel->find($file['category_id']);
        if (!$category) return false;

        $now = date('Y-m-d H:i:s');

        // Calculate archive and expiry dates based on category
        $archiveYears   = (int)($category['archive_after_years'] ?? 0);
        $archiveSeconds = (int)($category['archive_after_seconds'] ?? 0);
        $retentionYears = (int)($category['retention_years'] ?? 0);
        $retentionSeconds = (int)($category['retention_seconds'] ?? 0);

        $archivedAt = date('Y-m-d H:i:s', strtotime($now . " + {$archiveYears} years + {$archiveSeconds} seconds"));
        $expiredAt  = date('Y-m-d H:i:s', strtotime($archivedAt . " + {$retentionYears} years + {$retentionSeconds} seconds"));

        // Update the file record
        return $this->update($fileId, [
            'status' => 'active',
            'archived_at' => $archivedAt,
            'expired_at' => $expiredAt,
            'uploaded_at' => $file['uploaded_at'] ?? $now,
            'is_archived' => 0
        ]);
    }

    /**
     * ✅ Fill in missing archived_at and expired_at for active files
     */
    public function ensureArchiveDates()
    {
        $categoryModel = new \App\Models\CategoryModel();
        $now = date('Y-m-d H:i:s');

        // Include files missing either archived_at or expired_at
        $activeFiles = $this->where('status', 'active')
            ->groupStart()
                ->where('archived_at IS NULL')
                ->orWhere('archived_at', '')
                ->orWhere('expired_at IS NULL')
                ->orWhere('expired_at', '')
            ->groupEnd()
            ->findAll();

        foreach ($activeFiles as $file) {
            $category = $categoryModel->find($file['category_id']);
            if (!$category) continue;

            $archiveYears   = (int)($category['archive_after_years'] ?? 0);
            $archiveSeconds = (int)($category['archive_after_seconds'] ?? 0);
            $retentionYears = (int)($category['retention_years'] ?? 0);
            $retentionSeconds = (int)($category['retention_seconds'] ?? 0);

            $baseTime = $file['uploaded_at'] ?? $now;

            $archivedAt = date('Y-m-d H:i:s', strtotime($baseTime . " + {$archiveYears} years + {$archiveSeconds} seconds"));
            $expiredAt  = date('Y-m-d H:i:s', strtotime($archivedAt . " + {$retentionYears} years + {$retentionSeconds} seconds"));

            $this->update($file['id'], [
                'archived_at' => $archivedAt,
                'expired_at'  => $expiredAt,
                'is_archived' => 0,
            ]);
        }
    }

    /**
     * ✅ Automatically move files to ARCHIVED or EXPIRED when due
     */
    public function autoArchiveAndExpire()
    {
        $now = date('Y-m-d H:i:s');

        // 🔹 1. Archive due files
        $toArchive = $this->where('status', 'active')
            ->where('archived_at <=', $now)
            ->findAll();

        foreach ($toArchive as $file) {
            $this->update($file['id'], [
                'status' => 'archived',
                'is_archived' => 1
            ]);
        }

        // 🔹 2. Expire due files (after retention period)
        $toExpire = $this->whereIn('status', ['archived', 'active'])
            ->where('expired_at <=', $now)
            ->findAll();

        foreach ($toExpire as $file) {
            $this->update($file['id'], [
                'status' => 'expired',
                'is_archived' => 1
            ]);
        }
    }

    /**
     * ✅ Fetch active (non-archived) files for a folder
     */
    public function getActiveFilesByFolder($folderId)
    {
        return $this->select('
                files.*,
                categories.category_name,
                users.username AS uploader_name
            ')
            ->join('categories', 'categories.id = files.category_id', 'left')
            ->join('users', 'users.id = files.uploaded_by', 'left')
            ->where('files.folder_id', $folderId)
            ->where('files.is_archived', 0)
            ->where('files.status !=', 'expired')
            ->orderBy('files.uploaded_at', 'DESC')
            ->findAll();
    }

    /**
     * ✅ Fetch archived files for a folder
     */
public function getArchivedFilesByFolder($folderId)
{
    return $this->select('
            files.*,
            categories.category_name,
            users.username AS uploader_name
        ')
        ->join('categories', 'categories.id = files.category_id', 'left')
        ->join('users', 'users.id = files.uploaded_by', 'left')
        ->where('files.folder_id', $folderId)
        ->where('files.is_archived', 1)
        // ✅ include both archived and expired
        ->whereIn('files.status', ['archived', 'expired'])
        ->orderBy('files.archived_at', 'DESC')
        ->findAll();
}




    
     //Fetch expired files for a folder
     
    public function getExpiredFilesByFolder($folderId)
    {
        return $this->select('
                files.*,
                categories.category_name,
                users.username AS uploader_name
            ')
            ->join('categories', 'categories.id = files.category_id', 'left')
            ->join('users', 'users.id = files.uploaded_by', 'left')
            ->where('files.folder_id', $folderId)
            ->where('files.status', 'expired')
            ->orderBy('files.expired_at', 'DESC')
            ->findAll();
    }
}
