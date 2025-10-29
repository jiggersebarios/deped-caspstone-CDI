<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

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
     * ✅ Fetch active or pending files (not archived)
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
     * ✅ Fetch archived and expired files
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
        ->whereIn('files.status', ['archived', 'expired'])
        ->orderBy('files.archived_at', 'DESC')
        ->findAll();
    }

    /**
     * ✅ Activate file — calculate archive/expire dates based on category
     */
    public function activateFile($fileId)
    {
        $file = $this->find($fileId);
        if (!$file) return false;

        $categoryModel = new \App\Models\CategoryModel();
        $category = $categoryModel->find($file['category_id']);
        if (!is_array($category)) return false;

        $now = date('Y-m-d H:i:s');

        // ✅ Calculate archive & expire times from category setup
        $archivedAt = $this->calculateDate($now, (int)$category['archive_after_value'], $category['archive_after_unit']);
        $expiredAt  = $this->calculateDate($archivedAt, (int)$category['retention_value'], $category['retention_unit']);

        return $this->update($fileId, [
            'status'      => 'active',
            'archived_at' => $archivedAt,
            'expired_at'  => $expiredAt,
            'uploaded_at' => $file['uploaded_at'] ?? $now,
            'is_archived' => 0
        ]);
    }

    /**
     * ✅ Automatically archive or expire files when due
     */
    public function autoArchiveAndExpire()
    {
        $now = date('Y-m-d H:i:s');

        // 🔹 Move active → archived (archive time reached)
        $toArchive = $this->where('status', 'active')
            ->where('archived_at <=', $now)
            ->findAll();

        foreach ($toArchive as $file) {
            $this->update($file['id'], [
                'status'      => 'archived',
                'is_archived' => 1
            ]);
        }

        // 🔹 Move archived → expired (expiration time reached)
        $toExpire = $this->whereIn('status', ['archived', 'active'])
            ->where('expired_at <=', $now)
            ->findAll();

        foreach ($toExpire as $file) {
            $this->update($file['id'], [
                'status'      => 'expired',
                'is_archived' => 1
            ]);
        }
    }

    /**
     * ✅ Helper: Add time to a date (now fully supports hours, minutes, seconds)
     */
    private function calculateDate($baseDate, $value, $unit)
    {
        if (!$value || !$unit) return $baseDate;

        $date = new \DateTime($baseDate, new \DateTimeZone('Asia/Manila'));

        switch (strtolower($unit)) {
            case 'year':
            case 'years':
                $date->modify("+{$value} year");
                break;

            case 'month':
            case 'months':
                $date->modify("+{$value} month");
                break;

            case 'day':
            case 'days':
                $date->modify("+{$value} day");
                break;

            case 'hour':
            case 'hours':
                $date->modify("+{$value} hour");
                break;

            case 'minute':
            case 'minutes':
                $date->modify("+{$value} minute");
                break;

            case 'second':
            case 'seconds':
                $date->modify("+{$value} second");
                break;
        }

        return $date->format('Y-m-d H:i:s');
    }

    /**
 * ✅ Rename a file (both in filesystem and database)
 */
public function renameFile($fileId, $newName)
{
    // Get file info
    $file = $this->find($fileId);
    if (!$file) {
        throw new \Exception("File not found.");
    }

    $oldPath = WRITEPATH . 'uploads/' . $file['file_path']; // adjust if stored elsewhere

    // Ensure file exists in storage
    if (!file_exists($oldPath)) {
        throw new \Exception("File does not exist on server.");
    }

    // Get file extension
    $extension = pathinfo($file['file_name'], PATHINFO_EXTENSION);
    $newFileName = $newName . '.' . $extension;

    // Keep the same folder path, rename only the filename
    $folderPath = dirname($oldPath);
    $newPath = $folderPath . '/' . $newFileName;

    // Prevent overwriting existing files
    if (file_exists($newPath)) {
        throw new \Exception("A file with the same name already exists.");
    }

    // Rename file in storage
    if (!rename($oldPath, $newPath)) {
        throw new \Exception("Failed to rename file on server.");
    }

    // Update database record
    return $this->update($fileId, [
        'file_name' => $newFileName,
        'file_path' => str_replace(WRITEPATH . 'uploads/', '', $newPath) // relative path
    ]);
}

}
