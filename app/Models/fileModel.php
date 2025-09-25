<?php

namespace App\Models;

use CodeIgniter\Model;

class FileModel extends Model
{
    protected $table = 'files';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'folder_id',
        'category_id',   // ✅ keep category_id
        'file_name',
        'file_path',
        'uploaded_by',
        'uploaded_at',
        'archived_at',
        'deleted_at'
    ];
    public $timestamps = false;

    // 🔹 Custom method: fetch files with category name
public function getFilesWithDetails($folderId = null)
{
   return $this->select('files.*, categories.category_name, users.username AS uploader_name')
            ->join('categories', 'categories.id = files.category_id', 'left')
            ->join('users', 'users.id = files.uploaded_by', 'left')
            ->where('files.folder_id', $folderId)
            ->findAll();

}



}
