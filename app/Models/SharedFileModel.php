<?php
namespace App\Models;

use CodeIgniter\Model;

class SharedFileModel extends Model
{
    protected $table = 'shared_files';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'file_id',
        'file_name',
        'file_path',
        'uploaded_by',
        'shared_by',
        'shared_role',
        'created_at'
    ];

    protected $useTimestamps = false;
}
