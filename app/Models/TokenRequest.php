<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requested_by',
        'quantity',
        'notes',
        'status',
        'approved_by',
        'rejection_reason',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the user who made the request.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by', 'id_user');
    }

    /**
     * Get the user who approved the request.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id_user');
    }

    /**
     * Get the status badge HTML.
     */
    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Menunggu</span>';
            case self::STATUS_APPROVED:
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Disetujui</span>';
            case self::STATUS_REJECTED:
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>';
            default:
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Tidak Diketahui</span>';
        }
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope a query to only include rejected requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Get the count of pending requests.
     */
    public static function getPendingRequestsCount()
    {
        return self::pending()->count();
    }

    /**
     * Get users with low tokens.
     */
    public static function getUsersWithLowTokens($threshold = 5)
    {
        return User::where('role', 'User')
            ->whereHas('tokens', function ($query) use ($threshold) {
                $query->where('status', TokenEmergency::STATUS_AVAILABLE);
            })
            ->withCount(['tokens' => function ($query) {
                $query->where('status', TokenEmergency::STATUS_AVAILABLE);
            }])
            ->having('tokens_count', '<', $threshold)
            ->get();
    }
}
