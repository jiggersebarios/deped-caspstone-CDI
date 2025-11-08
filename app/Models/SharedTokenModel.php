<?php
namespace App\Models;

use CodeIgniter\Model;

class SharedTokenModel extends Model
{
    protected $table = 'shared_tokens';
    protected $primaryKey = 'id';
    protected $allowedFields = ['shared_id', 'token', 'created_at', 'expires_at'];
}
