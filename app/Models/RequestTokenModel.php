<?php

namespace App\Models;

use CodeIgniter\Model;

class RequestTokenModel extends Model
{
    protected $table = 'request_tokens';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'request_id',
        'token',
        'expires_at',
        'used'
    ];

    public function createToken($requestId, $hours = 24)
    {
        $token = bin2hex(random_bytes(16));
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$hours} hours"));

        $this->insert([
            'request_id' => $requestId,
            'token'      => $token,
            'expires_at' => $expiresAt,
            'used'       => 0
        ]);

        return $token;
    }

    public function validateToken($token)
    {
        $data = $this->where('token', $token)
                     ->where('used', 0)
                     ->where('expires_at >=', date('Y-m-d H:i:s'))
                     ->first();
        return $data;
    }

    public function markUsed($token)
    {
        return $this->where('token', $token)->set('used', 1)->update();
    }
    
}
