<?php

namespace App\Models;

use CodeIgniter\Model;

class FileModel extends Model
{
    protected $table = 'files';
    protected $primaryKey = 'id';
    protected $storagePath = 'C:\\xampp\\depedfiles\\'; // change as needed


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

    
     //Get files with category and uploader info
     
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

    
     //Get active or pending files
     
    public function getActiveFilesByFolder($folderId, $search = null)
    {
        $builder = $this->select('
            files.*,
            categories.category_name,
            users.username AS uploader_name
        ')
        ->join('categories', 'categories.id = files.category_id', 'left')
        ->join('users', 'users.id = files.uploaded_by', 'left')
        ->where('files.folder_id', $folderId)
        ->where('files.is_archived', 0)
        ->whereIn('files.status', ['active', 'pending']);

        if ($search) {
            $builder->like('files.file_name', $search);
        }

        return $builder->orderBy('files.uploaded_at', 'DESC')->findAll();
    }

    
     //Get archived files
     
    public function getArchivedFilesByFolder($folderId, $search = null)
    {
        $builder = $this->select('
            files.*,
            categories.category_name,
            users.username AS uploader_name
        ')
        ->join('categories', 'categories.id = files.category_id', 'left')
        ->join('users', 'users.id = files.uploaded_by', 'left')
        ->where('files.folder_id', $folderId)
        ->where('files.is_archived', 1)
        ->where('files.status', 'archived');

        if ($search) {
            $builder->like('files.file_name', $search);
        }

        return $builder->orderBy('files.archived_at', 'DESC')->findAll();
    }

    
     // Activate a file: set archive & expire dates and move file
     
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

    ///Automatically archive or expire files

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

    public function getExpiredFilesByFolder($folderId, $search = null)
{
    $builder = $this->select('
            files.*,
            categories.category_name,
            users.username AS uploader_name
        ')
        ->join('categories', 'categories.id = files.category_id', 'left')
        ->join('users', 'users.id = files.uploaded_by', 'left')
        ->where('files.folder_id', $folderId)
        ->where('files.status', 'expired');

    if ($search) {
        $builder->like('files.file_name', $search);
    }

    return $builder->orderBy('files.expired_at', 'DESC')->findAll();
}

public function deleteFileWithAudit($fileId, $deletedBy, $reason = null)
{
    $file = $this->find($fileId);
    if (!$file) return false;

    // Log deletion
    $deletedFileModel = new \App\Models\DeletedFileModel();
    $categoryModel = new \App\Models\CategoryModel();
    $category = $categoryModel->find($file['category_id']);

    $deletedFileModel->insert([
        'file_id'       => $file['id'],
        'file_name'     => $file['file_name'],
        'category_name' => $category['category_name'] ?? 'Uncategorized',
        'deleted_by'    => $deletedBy,
        'reason'        => $reason
    ]);

    // Physically remove the file if it exists
    $filePath = $this->storagePath . $file['file_path'];
if (file_exists($filePath)) {
    unlink($filePath);
}


    // Remove from main files table
    return $this->delete($fileId);
}

    
      //Move a file physically based on status
    
public function moveFileByStatus($fileId, $newStatus)
{
    $file = $this->find($fileId);
    if (!$file) return false;

    $currentPath = $this->storagePath . $file['file_path']; // use storagePath

    switch ($newStatus) {
        case 'active':
            $destFolder = $this->storagePath . 'active/';
            break;
        case 'archived':
            $destFolder = $this->storagePath . 'archive/';
            break;
        case 'expired':
            $destFolder = $this->storagePath . 'expired/';
            break;
        case 'pending':
        default:
            $destFolder = $this->storagePath . 'pending/';
    }

    if (!is_dir($destFolder)) mkdir($destFolder, 0777, true);

    $filename = basename($currentPath);
    $newPath = $destFolder . $filename;

    if (!rename($currentPath, $newPath)) {
        log_message('error', "Failed to move file {$filename} to {$destFolder}");
        return false;
    }

    $this->update($fileId, [
        'file_path' => str_replace($this->storagePath, '', $newPath),
        'status'    => $newStatus
    ]);

    return true;
}


    
 //Rename a file on disk and in database
public function renameFile($fileId, $newName)
{
    $file = $this->find($fileId);
    if (!$file) {
        throw new \Exception("File not found.");
    }

    // Prevent renaming immutable files
    if (in_array($file['status'], ['archived', 'expired'])) {
        throw new \Exception("This file is immutable and cannot be renamed.");
    }

    // Use storagePath instead of FCPATH
    $oldPath = $this->storagePath . $file['file_path'];
    if (!file_exists($oldPath)) {
        throw new \Exception("File does not exist on the server.");
    }

    // Sanitize and rebuild new filename (keep same extension)
    $extension = pathinfo($file['file_name'], PATHINFO_EXTENSION);
    $newName = preg_replace('/[^A-Za-z0-9_\-]/', '_', trim($newName)); // safety
    $newFileName = $newName . '.' . $extension;

    $folderPath = dirname($oldPath);
    $newPath = $folderPath . '/' . $newFileName;

    if (file_exists($newPath)) {
        throw new \Exception("A file with the same name already exists.");
    }

    // Perform rename operation
    if (!rename($oldPath, $newPath)) {
        throw new \Exception("Failed to rename the file on the server.");
    }

    // Update database with new file name and path (relative to storagePath)
    $this->update($fileId, [
        'file_name' => $newFileName,
        'file_path' => str_replace($this->storagePath, '', $newPath)
    ]);

    return true;
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
