<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table      = 'notifications';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'file_id',
        'type',
        'title',
        'message',
        'is_read',
        'created_at',
        'updated_at',
        'reason'
    ];

    protected $useTimestamps = true;
}
