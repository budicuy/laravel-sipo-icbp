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
        'requested_by',
        'request_quantity',
        'request_status',
        'request_approved_at',
        'request_approved_by',
        'notes'
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'request_approved_at' => 'datetime'
    ];

    // Status constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_USED = 'used';
    const STATUS_EXPIRED = 'expired';

    // Request status constants
    const REQUEST_STATUS_PENDING = 'pending';
    const REQUEST_STATUS_APPROVED = 'approved';
    const REQUEST_STATUS_REJECTED = 'rejected';

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
     * Relasi ke user (user yang requested token)
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by', 'id_user');
    }

    /**
     * Relasi ke user (admin yang approved request)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'request_approved_by', 'id_user');
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
     * Get pending token requests count
     */
    public static function getPendingRequestsCount()
    {
        return self::where('request_status', self::REQUEST_STATUS_PENDING)
            ->count();
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
        return self::with(['user', 'generator', 'requester', 'approver', 'usedBy'])
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
     * Scope to get tokens by request status
     */
    public function scopeByRequestStatus($query, $requestStatus)
    {
        return $query->where('request_status', $requestStatus);
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

    /**
     * Get request status badge HTML
     */
    public function getRequestStatusBadgeAttribute()
    {
        $badges = [
            self::REQUEST_STATUS_PENDING => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Menunggu</span>',
            self::REQUEST_STATUS_APPROVED => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Disetujui</span>',
            self::REQUEST_STATUS_REJECTED => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>',
        ];

        return $badges[$this->request_status] ?? '';
    }
}
