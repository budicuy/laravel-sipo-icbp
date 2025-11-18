<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIChatHistory extends Model
{
    use HasFactory;

    protected $table = 'ai_chat_histories';

    protected $fillable = [
        'nik',
        'nama_karyawan',
        'departemen',
        'login_count',
        'last_login_at',
        'last_ai_chat_access_at',
        'ai_chat_access_count',
    ];

    protected $casts = [
        'login_count' => 'integer',
        'ai_chat_access_count' => 'integer',
        'last_login_at' => 'datetime',
        'last_ai_chat_access_at' => 'datetime',
    ];

    /**
     * Record or update user login
     */
    public static function recordLogin($nik, $namaKaryawan, $departemen = null)
    {
        $history = self::where('nik', $nik)->first();

        if ($history) {
            // Update existing record
            $history->increment('login_count');
            $history->last_login_at = now();
            $history->save();
        } else {
            // Create new record
            self::create([
                'nik' => $nik,
                'nama_karyawan' => $namaKaryawan,
                'departemen' => $departemen,
                'login_count' => 1,
                'last_login_at' => now(),
                'ai_chat_access_count' => 0,
            ]);
        }

        return $history;
    }

    /**
     * Record AI chat access
     */
    public static function recordAIChatAccess($nik)
    {
        $history = self::where('nik', $nik)->first();

        if ($history) {
            $history->increment('ai_chat_access_count');
            $history->last_ai_chat_access_at = now();
            $history->save();
        } else {
            // This should not happen if login is recorded first, but handle it anyway
            $karyawan = \App\Models\Karyawan::where('nik_karyawan', $nik)->first();
            if ($karyawan) {
                self::create([
                    'nik' => $nik,
                    'nama_karyawan' => $karyawan->nama_karyawan,
                    'departemen' => $karyawan->departemen->nama_departemen ?? null,
                    'login_count' => 0,
                    'last_login_at' => null,
                    'ai_chat_access_count' => 1,
                    'last_ai_chat_access_at' => now(),
                ]);
            }
        }

        return $history;
    }

    /**
     * Get statistics for admin dashboard
     */
    public static function getStatistics()
    {
        $totalUsers = self::count();
        $totalLogins = self::sum('login_count');
        $totalAIChatAccess = self::sum('ai_chat_access_count');
        $activeUsersToday = self::whereDate('last_ai_chat_access_at', today())->count();
        $activeUsersThisWeek = self::whereBetween('last_ai_chat_access_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();
        $activeUsersThisMonth = self::whereMonth('last_ai_chat_access_at', now()->month)
            ->whereYear('last_ai_chat_access_at', now()->year)
            ->count();

        // Top users by AI chat access
        $topUsers = self::orderBy('ai_chat_access_count', 'desc')
            ->limit(10)
            ->get(['nik', 'nama_karyawan', 'departemen', 'ai_chat_access_count', 'last_ai_chat_access_at']);

        // Recent activity
        $recentActivity = self::orderBy('last_ai_chat_access_at', 'desc')
            ->limit(10)
            ->get(['nik', 'nama_karyawan', 'departemen', 'last_ai_chat_access_at', 'ai_chat_access_count']);

        return [
            'total_users' => $totalUsers,
            'total_logins' => $totalLogins,
            'total_ai_chat_access' => $totalAIChatAccess,
            'active_users_today' => $activeUsersToday,
            'active_users_this_week' => $activeUsersThisWeek,
            'active_users_this_month' => $activeUsersThisMonth,
            'top_users' => $topUsers,
            'recent_activity' => $recentActivity,
        ];
    }

    /**
     * Get user history with pagination
     */
    public static function getUserHistory($perPage = 10)
    {
        return self::orderBy('last_ai_chat_access_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Search users by name or NIK
     */
    public static function searchUsers($query)
    {
        return self::where('nama_karyawan', 'like', "%{$query}%")
            ->orWhere('nik', 'like', "%{$query}%")
            ->orderBy('last_ai_chat_access_at', 'desc')
            ->get();
    }

    /**
     * Get user detail by NIK
     */
    public static function getUserDetail($nik)
    {
        return self::where('nik', $nik)->first();
    }
}
