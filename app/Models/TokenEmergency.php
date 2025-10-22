<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TokenEmergency extends Model
{
    use HasFactory;

    protected $table = 'token_emergency';
    protected $primaryKey = 'id_token';
    protected $fillable = [
        'token',
        'status',
        'id_user',
        'used_at',
        'used_by',
        'generated_by',
        'notes'
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    // Status constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_USED = 'used';
    const STATUS_EXPIRED = 'expired';

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
    public static function generateMultipleTokens($count = 10, $length = 6, $userId = null, $generatedBy = null)
    {
        $tokens = [];
        for ($i = 0; $i < $count; $i++) {
            $token = self::generateToken($length);
            $tokens[] = self::create([
                'token' => $token,
                'status' => self::STATUS_AVAILABLE,
                'id_user' => $userId,
                'generated_by' => $generatedBy ?? Auth::id()
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
                  ->where('status', self::STATUS_AVAILABLE)
                  ->first();
    }

    /**
     * Cek apakah token valid dan dapat digunakan oleh user tertentu
     */
    public static function isValidTokenForUser($token, $userId)
    {
        return self::where('token', $token)
                  ->where('status', self::STATUS_AVAILABLE)
                  ->where(function($query) use ($userId) {
                      $query->whereNull('id_user') // Token umum
                            ->orWhere('id_user', $userId); // Token milik user
                  })
                  ->first();
    }

    /**
     * Cek apakah token milik user tertentu
     */
    public function isOwnedBy($userId)
    {
        return $this->id_user == $userId;
    }

    /**
     * Cek apakah token bersifat umum (tidak dimiliki oleh user tertentu)
     */
    public function isGeneralToken()
    {
        return is_null($this->id_user);
    }

    /**
     * Cek apakah token dapat digunakan oleh user tertentu
     */
    public function canBeUsedBy($userId)
    {
        return $this->isGeneralToken() || $this->isOwnedBy($userId);
    }

    /**
     * Gunakan token (ubah status menjadi used)
     */
    public function useToken($userId)
    {
        $this->status = self::STATUS_USED;
        $this->id_user = $userId;
        $this->used_at = now();
        $this->save();
    }

    /**
     * Relasi ke user (token owner)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Relasi ke user (admin yang generated token)
     */
    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by', 'id_user');
    }


    /**
     * Relasi ke user (user yang menggunakan token)
     */
    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by', 'id_user');
    }

    /**
     * Get available tokens count for a user
     */
    public static function getAvailableTokensCount($userId = null)
    {
        $userId = $userId ?? Auth::id();
        return self::where('id_user', $userId)
            ->where('status', self::STATUS_AVAILABLE)
            ->count();
    }

    /**
     * Get all tokens for a user
     */
    public static function getUserTokens($userId = null)
    {
        $userId = $userId ?? Auth::id();
        return self::where('id_user', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }


    /**
     * Get users with low token count (less than 5)
     */
    public static function getUsersWithLowTokens($threshold = 5)
    {
        return User::select('user.id_user', 'user.nama_lengkap', 'user.username', 'user.role')
            ->selectRaw('COUNT(token_emergency.id_token) as available_tokens')
            ->leftJoin('token_emergency', function ($join) {
                $join->on('user.id_user', '=', 'token_emergency.id_user')
                    ->where('token_emergency.status', self::STATUS_AVAILABLE);
            })
            ->whereNotIn('user.role', ['Admin', 'Super Admin'])
            ->groupBy('user.id_user', 'user.nama_lengkap', 'user.username', 'user.role')
            ->havingRaw('available_tokens < ?', [$threshold])
            ->get();
    }

    /**
     * Get token audit trail
     */
    public static function getAuditTrail()
    {
        return self::with(['user', 'generator', 'usedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Scope to get tokens by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }


    /**
     * Check if token is expired (older than 30 days)
     */
    public function isExpired()
    {
        return $this->created_at->diffInDays(now()) > 30;
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_AVAILABLE => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Tersedia</span>',
            self::STATUS_USED => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Digunakan</span>',
            self::STATUS_EXPIRED => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Kadaluarsa</span>',
        ];

        return $badges[$this->status] ?? '';
    }

}
