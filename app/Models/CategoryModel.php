<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'category_name',
        'archive_after_years',
        'retention_years',
        'archive_after_seconds',
        'retention_seconds'
    ];
}
