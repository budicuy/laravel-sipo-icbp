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
        }

        return $response;
    }
}
