<?php 
namespace App\Models;

use CodeIgniter\Model;

class FolderModel extends Model
{
    protected $table = 'folders';   // your table name
    protected $primaryKey = 'id';
    protected $useTimestamps = true; // since you have created_at, updated_at

    protected $allowedFields = [
        'folder_name',
        'parent_folder_id',
        'created_at',
        'updated_at'
    ];
}
