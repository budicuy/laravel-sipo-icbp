<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenEmergency extends Model
{
    use HasFactory;

    protected $table = 'token_emergency';
    protected $primaryKey = 'id_token';
    protected $fillable = [
        'token',
        'status',
        'id_user',
        'used_at'
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    /**
     * Generate random token dengan panjang 4-6 digit
     */
    public static function generateToken($length = 6)
    {
        do {
            $token = '';
            for ($i = 0; $i < $length; $i++) {
                $token .= rand(0, 9);
            }
        } while (self::where('token', $token)->exists());

        return $token;
    }

    /**
     * Generate multiple tokens sekaligus
     */
    public static function generateMultipleTokens($count = 10, $length = 6)
    {
        $tokens = [];
        for ($i = 0; $i < $count; $i++) {
            $token = self::generateToken($length);
            $tokens[] = self::create([
                'token' => $token,
                'status' => 'available'
            ]);
        }
        return $tokens;
    }

    /**
     * Cek apakah token valid dan tersedia
     */
    public static function isValidToken($token)
    {
        return self::where('token', $token)
                  ->where('status', 'available')
                  ->first();
    }

    /**
     * Gunakan token (ubah status menjadi used)
     */
    public function useToken($userId)
    {
        $this->status = 'used';
        $this->id_user = $userId;
        $this->used_at = now();
        $this->save();
    }

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
