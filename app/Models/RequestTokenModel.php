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

    /**
     * Create a new one-time token for a given file request.
     * Returns the token string.
     */
    public function createToken($requestId, $hours = 24)
    {
        // Check if an active token already exists
        $existing = $this->where('request_id', $requestId)
                         ->where('used', 0)
                         ->where('expires_at >=', date('Y-m-d H:i:s'))
                         ->first();
        if ($existing) {
            return $existing['token'];
        }

        $token = bin2hex(random_bytes(32)); // 64-character secure token
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$hours} hours"));

        $this->insert([
            'request_id' => $requestId,
            'token'      => $token,
            'expires_at' => $expiresAt,
            'used'       => 0
        ]);

        return $token;
    }

    /**
     * Validate a token (must be unused and not expired).
     */
    public function validateToken($token)
    {
        return $this->where('token', $token)
                    ->where('used', 0)
                    ->where('expires_at >=', date('Y-m-d H:i:s'))
                    ->first();
    }

    /**
     * Mark a token as used (single-use enforcement)
     */
    public function markUsed($token)
    {
        return $this->where('token', $token)
                    ->set('used', 1)
                    ->update();
    }
}
