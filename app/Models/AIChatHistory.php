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
        'kode_hubungan',
        'tipe_pengguna',
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
    public static function recordLogin($nik, $namaKaryawan, $departemen = null, $kodeHubungan = null, $tipePengguna = 'karyawan')
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
                'kode_hubungan' => $kodeHubungan,
                'tipe_pengguna' => $tipePengguna,
                'login_count' => 1,
                'last_login_at' => now(),
                'ai_chat_access_count' => 0,
            ]);
        }

        return $history;
    }

    /**
     * Record or update family member login
     */
    public static function recordFamilyLogin($nik, $kodeHubungan, $namaKeluarga, $departemen = null)
    {
        $familyNik = $nik . '-' . $kodeHubungan;

        $result = self::recordLogin($familyNik, $namaKeluarga, $departemen, $kodeHubungan, 'keluarga');

        // Debug logging
        \Log::info('Family login recorded', [
            'family_nik' => $familyNik,
            'employee_nik' => $nik,
            'kode_hubungan' => $kodeHubungan,
            'nama_keluarga' => $namaKeluarga,
            'result' => $result ? 'success' : 'failed'
        ]);

        return $result;
    }

    /**
     * Record AI chat access
     */
    public static function recordAIChatAccess($nik)
    {
        // Debug logging
        \Log::info('recordAIChatAccess called', [
            'nik' => $nik,
            'is_family' => strpos($nik, '-') !== false
        ]);

        $history = self::where('nik', $nik)->first();

        if ($history) {
            $history->increment('ai_chat_access_count');
            $history->last_ai_chat_access_at = now();
            $history->save();

            \Log::info('AI Chat access updated for existing user', [
                'nik' => $nik,
                'new_count' => $history->ai_chat_access_count
            ]);
        } else {
            // This should not happen if login is recorded first, but handle it anyway

            // Check if it's a family member (contains dash)
            if (strpos($nik, '-') !== false) {
                list($employeeNik, $kodeHubungan) = explode('-', $nik, 2);

                $karyawan = \App\Models\Karyawan::where('nik_karyawan', $employeeNik)->first();
                if ($karyawan) {
                    $keluarga = \App\Models\Keluarga::where('id_karyawan', $karyawan->id_karyawan)
                        ->where('kode_hubungan', $kodeHubungan)
                        ->first();

                    if ($keluarga) {
                        $newRecord = self::create([
                            'nik' => $nik,
                            'nama_karyawan' => $keluarga->nama_keluarga,
                            'departemen' => $karyawan->departemen->nama_departemen ?? null,
                            'kode_hubungan' => $kodeHubungan,
                            'tipe_pengguna' => 'keluarga',
                            'login_count' => 0,
                            'last_login_at' => null,
                            'ai_chat_access_count' => 1,
                            'last_ai_chat_access_at' => now(),
                        ]);

                        \Log::info('New family AI Chat access record created', [
                            'nik' => $nik,
                            'kode_hubungan' => $kodeHubungan,
                            'nama_keluarga' => $keluarga->nama_keluarga,
                            'record_id' => $newRecord->id
                        ]);
                    }
                }
            } else {
                // Regular employee
                $karyawan = \App\Models\Karyawan::where('nik_karyawan', $nik)->first();
                if ($karyawan) {
                    self::create([
                        'nik' => $nik,
                        'nama_karyawan' => $karyawan->nama_karyawan,
                        'departemen' => $karyawan->departemen->nama_departemen ?? null,
                        'tipe_pengguna' => 'karyawan',
                        'login_count' => 0,
                        'last_login_at' => null,
                        'ai_chat_access_count' => 1,
                        'last_ai_chat_access_at' => now(),
                    ]);
                }
            }
        }

        return $history;
    }

    /**
     * Record AI chat access for family member
     */
    public static function recordFamilyAIChatAccess($nik, $kodeHubungan)
    {
        $familyNik = $nik . '-' . $kodeHubungan;
        return self::recordAIChatAccess($familyNik);
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
            ->get(['nik', 'nama_karyawan', 'departemen', 'kode_hubungan', 'tipe_pengguna', 'ai_chat_access_count', 'last_ai_chat_access_at']);

        // Recent activity
        $recentActivity = self::orderBy('last_ai_chat_access_at', 'desc')
            ->limit(10)
            ->get(['nik', 'nama_karyawan', 'departemen', 'kode_hubungan', 'tipe_pengguna', 'last_ai_chat_access_at', 'ai_chat_access_count']);

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
     * Get user type label
     */
    public function getTipePenggunaLabelAttribute()
    {
        return $this->tipe_pengguna === 'keluarga' ? 'Keluarga' : 'Karyawan';
    }

    /**
     * Get hubungan label if user is family member
     */
    public function getHubunganLabelAttribute()
    {
        if ($this->tipe_pengguna === 'keluarga' && $this->kode_hubungan) {
            // Find hubungan by kode_hubungan field, not by ID
            $hubungan = \App\Models\Hubungan::where('kode_hubungan', $this->kode_hubungan)->first();
            return $hubungan ? $hubungan->hubungan : $this->kode_hubungan;
        }
        return '-';
    }

    /**
     * Get formatted NIK with hubungan for family members
     */
    public function getFormattedNikAttribute()
    {
        if ($this->tipe_pengguna === 'keluarga') {
            return $this->nik;
        }
        return $this->nik;
    }

    /**
     * Get display name with type indicator
     */
    public function getDisplayNameAttribute()
    {
        if ($this->tipe_pengguna === 'keluarga') {
            return $this->nama_karyawan . ' (Keluarga)';
        }
        return $this->nama_karyawan;
    }

    /**
     * Get user detail by NIK
     */
    public static function getUserDetail($nik)
    {
        return self::where('nik', $nik)->first();
    }
}
