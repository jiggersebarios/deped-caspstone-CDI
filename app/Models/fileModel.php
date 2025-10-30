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
        'file_size',
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
     * Get files with category and uploader info
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
     * Get active or pending files
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
        ->whereIn('files.status', ['active', 'pending'])
        ->orderBy('files.uploaded_at', 'DESC')
        ->findAll();
    }

    /**
     * Get archived files
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
        ->where('files.status', 'archived')
        ->orderBy('files.archived_at', 'DESC')
        ->findAll();
    }

    /**
     * Activate a file: set archive & expire dates and move file
     */
    public function activateFile($fileId)
    {
        $file = $this->find($fileId);
        if (!$file) return false;

        $categoryModel = new \App\Models\CategoryModel();
        $category = $categoryModel->find($file['category_id']);
        if (!is_array($category)) return false;

        $now = date('Y-m-d H:i:s');

        $archivedAt = $this->calculateDate($now, (int)$category['archive_after_value'], $category['archive_after_unit']);
        $expiredAt  = $this->calculateDate($archivedAt, (int)$category['retention_value'], $category['retention_unit']);

        $this->update($fileId, [
            'status'      => 'active',
            'archived_at' => $archivedAt,
            'expired_at'  => $expiredAt,
            'uploaded_at' => $file['uploaded_at'] ?? $now,
            'is_archived' => 0
        ]);

        // Move file to active folder
        $this->moveFileByStatus($fileId, 'active');

        return true;
    }

    /**
     * Automatically archive or expire files
     */
    public function autoArchiveAndExpire()
    {
        $now = date('Y-m-d H:i:s');

        // Active → Archived
        $toArchive = $this->where('status', 'active')
            ->where('archived_at <=', $now)
            ->findAll();

        foreach ($toArchive as $file) {
            $this->update($file['id'], [
                'status'      => 'archived',
                'is_archived' => 1
            ]);
            $this->moveFileByStatus($file['id'], 'archived');
        }

        // Archived/Active → Expired
        $toExpire = $this->whereIn('status', ['archived', 'active'])
            ->where('expired_at <=', $now)
            ->findAll();

        foreach ($toExpire as $file) {
            $this->update($file['id'], [
                'status'      => 'expired',
                'is_archived' => 1
            ]);
            $this->moveFileByStatus($file['id'], 'expired');
        }
    }

    /**
     * Move a file physically based on status
     */
    public function moveFileByStatus($fileId, $newStatus)
    {
        $file = $this->find($fileId);
        if (!$file) return false;

        $currentPath = FCPATH . $file['file_path'];

        switch ($newStatus) {
            case 'active':
                $destFolder = FCPATH . 'uploads/active/';
                break;
            case 'archived':
                $destFolder = FCPATH . 'uploads/archive/';
                break;
            case 'expired':
                $destFolder = FCPATH . 'uploads/expired/';
                break;
            case 'pending':
            default:
                $destFolder = FCPATH . 'uploads/pending/';
        }

        if (!is_dir($destFolder)) mkdir($destFolder, 0777, true);

        $filename = basename($currentPath);
        $newPath = $destFolder . $filename;

        if (!rename($currentPath, $newPath)) {
            log_message('error', "Failed to move file {$filename} to {$destFolder}");
            return false;
        }

        // Update database path and status
        $this->update($fileId, [
            'file_path' => str_replace(FCPATH, '', $newPath),
            'status'    => $newStatus
        ]);

        return true;
    }

    /**
     * Rename a file on disk and in database
     */
    public function renameFile($fileId, $newName)
    {
        $file = $this->find($fileId);
        if (!$file) throw new \Exception("File not found.");

        $oldPath = FCPATH . $file['file_path'];
        if (!file_exists($oldPath)) throw new \Exception("File does not exist on server.");

        $extension = pathinfo($file['file_name'], PATHINFO_EXTENSION);
        $newFileName = $newName . '.' . $extension;
        $folderPath = dirname($oldPath);
        $newPath = $folderPath . '/' . $newFileName;

        if (file_exists($newPath)) throw new \Exception("A file with the same name already exists.");

        if (!rename($oldPath, $newPath)) throw new \Exception("Failed to rename file on server.");

        return $this->update($fileId, [
            'file_name' => $newFileName,
            'file_path' => str_replace(FCPATH, '', $newPath)
        ]);
    }

    /**
     * Helper: add time to date
     */
    private function calculateDate($baseDate, $value, $unit)
    {
        if (!$value || !$unit) return $baseDate;

        $date = new \DateTime($baseDate, new \DateTimeZone('Asia/Manila'));

        switch (strtolower($unit)) {
            case 'year':
            case 'years':
                $date->modify("+{$value} year"); break;
            case 'month':
            case 'months':
                $date->modify("+{$value} month"); break;
            case 'day':
            case 'days':
                $date->modify("+{$value} day"); break;
            case 'hour':
            case 'hours':
                $date->modify("+{$value} hour"); break;
            case 'minute':
            case 'minutes':
                $date->modify("+{$value} minute"); break;
            case 'second':
            case 'seconds':
                $date->modify("+{$value} second"); break;
        }

        return $date->format('Y-m-d H:i:s');
    }
}
