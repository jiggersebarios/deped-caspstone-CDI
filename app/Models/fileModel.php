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
/**
 * ✅ Activate file — calculate archive/expire dates based on category
 */
public function activateFile($fileId)
{
    $file = $this->find($fileId);
    if (!$file) return false;

    // ✅ Use CodeIgniter CategoryModel (not Eloquent)
    $categoryModel = new \App\Models\CategoryModel();
    $category = $categoryModel->find($file['category_id']); // returns array
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
     * ✅ Helper: Add time to a date (based on unit)
     */
    private function calculateDate($baseDate, $value, $unit)
    {
        if (!$value || !$unit) return $baseDate;

        $intervalSpec = match (strtolower($unit)) {
            'years'   => "P{$value}Y",
            'months'  => "P{$value}M",
            'days'    => "P{$value}D",
            'hours'   => "PT{$value}H",
            'minutes' => "PT{$value}M",
            'seconds' => "PT{$value}S",
            default   => "P0D",
        };

        $date = new \DateTime($baseDate, new \DateTimeZone('Asia/Manila'));
        $date->add(new \DateInterval($intervalSpec));

        return $date->format('Y-m-d H:i:s');
    }
}
