<?php

namespace App\Models;

use CodeIgniter\Model;

class DeletedFileModel extends Model
{
    protected $table = 'deleted_files';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'file_id',
        'file_name',
        'category_name',
        'expired_at',
        'deleted_by',
        'deleted_at',
        'reason'
    ];

    protected $useTimestamps = false;

    // Automatically cast date fields for JSON/API responses
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    /**
     * 🔹 Fetch all deleted files with optional filters
     *    Example: $model->getDeletedFiles(['deleted_by' => 2]);
     */
    public function getDeletedFiles(array $filters = [])
    {
        $builder = $this->select('
                deleted_files.*,
                users.username AS deleted_by_name
            ')
            ->join('users', 'users.id = deleted_files.deleted_by', 'left')
            ->orderBy('deleted_files.deleted_at', 'DESC');

        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                $builder->where('deleted_files.' . $key, $value);
            }
        }

        return $builder->findAll();
    }

    /**
     * 🔹 Fetch single deleted file by original file_id
     */
    public function getByFileId($fileId)
    {
        return $this->select('
                deleted_files.*,
                users.username AS deleted_by_name
            ')
            ->join('users', 'users.id = deleted_files.deleted_by', 'left')
            ->where('deleted_files.file_id', $fileId)
            ->first();
    }

    /**
     * 🔹 Log a deleted file (for controller use)
     */
    public function logDeletion(array $fileData, $deletedBy = null, $reason = 'Manual deletion')
    {
        return $this->insert([
            'file_id'        => $fileData['id'] ?? null,
            'file_name'      => $fileData['file_name'] ?? '(Unknown)',
            'category_name'  => $fileData['category_name'] ?? 'Uncategorized',
            'expired_at'     => $fileData['expired_at'] ?? null,
            'deleted_by'     => $deletedBy,
            'reason'         => $reason,
            'deleted_at'     => date('Y-m-d H:i:s'),
        ]);
    }
}
