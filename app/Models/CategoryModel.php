<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories'; // your table name
    protected $primaryKey = 'id';
    protected $allowedFields = ['category_name', 'retention_years'];
}
