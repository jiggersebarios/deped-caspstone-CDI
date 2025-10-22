<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'category_name',
        'description',
        'archive_after_value',
        'archive_after_unit',
        'retention_value',
        'retention_unit',
        'created_at',
        'updated_at'
    ];

    /**
     * Helper function to calculate future datetime
     * Example: addTime('2025-10-18 12:00:00', 10, 'days')
     * Result:  2025-10-28 12:00:00
     */
    public function addTime(string $baseDate, int $value, string $unit): string
    {
        $unit = strtolower(trim($unit));
        $validUnits = ['years', 'months', 'days', 'hours', 'minutes', 'seconds'];

        if (!in_array($unit, $validUnits)) {
            $unit = 'days'; // default fallback
        }

        return date('Y-m-d H:i:s', strtotime("$baseDate +{$value} {$unit}"));
    }
}
