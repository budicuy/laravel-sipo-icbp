<?php

namespace App\Http\Middleware;

use App\Models\AIChatHistory;
use App\Models\Karyawan;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackUserLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only track login for authenticated users via NIK (not regular admin login)
        if (Auth::check() && $request->is('api/auth/check-nik')) {
            $user = Auth::user();

            // For NIK-based login, get employee data
            $nik = $request->input('nik');
            if ($nik) {
                // Check if this is a family member login (contains dash)
                if (strpos($nik, '-') !== false) {
                    $this->recordFamilyLogin($nik);
                } else {
                    $this->recordEmployeeLogin($nik);
                }
            }
        }

        return $response;
    }


    // Rekam login karyawan
    private function recordEmployeeLogin($nik)
    {
        $karyawan = Karyawan::where('nik_karyawan', $nik)->first();

        if ($karyawan) {
            // Record login in AI chat history
            AIChatHistory::recordLogin(
                $nik,
                $karyawan->nama_karyawan,
                $karyawan->departemen->nama_departemen ?? null
            );
        }
    }
    // Rekam login anggota Hubungan keluarga
    private function recordFamilyLogin($nik)
    {
        // Pisah NIK karyawan dan kode hubungan
        $parts = explode('-', $nik, 2);
        if (count($parts) !== 2) {
            return;
        }

        $employeeNik = $parts[0];
        $kodeHubungan = $parts[1];

        // Cari karyawan berdasarkan NIK
        $karyawan = Karyawan::where('nik_karyawan', $employeeNik)->first();

        if (!$karyawan) {
            return;
        }

        // Cari anggota keluarga berdasarkan kode hubungan
        $keluarga = \App\Models\Keluarga::where('id_karyawan', $karyawan->id_karyawan)
            ->where('kode_hubungan', $kodeHubungan)
            ->first();

        if ($keluarga) {
            // Rekam login di riwayat chat AI untuk anggota keluarga
            AIChatHistory::recordFamilyLogin(
                $employeeNik,
                $kodeHubungan,
                $keluarga->nama_keluarga,
                $karyawan->departemen->nama_departemen ?? null
            );
        }
    }
}
