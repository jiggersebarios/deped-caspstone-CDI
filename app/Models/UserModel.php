<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

protected $allowedFields = [
    'username',
    'email',
    'password',
    'role',
    'main_folder',
    'main_folder_id',
    'created_at',
    'updated_at'
];


    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
