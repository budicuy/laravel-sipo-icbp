<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function showLoginForm()
    {
        // Redirect jika sudah login
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        // Cari user berdasarkan username
        $user = User::where('username', $request->username)->first();

        // Cek apakah user ada dan password cocok
        if ($user && Hash::check($request->password, $user->password)) {
            // Login user
            Auth::login($user, $request->filled('remember'));

            // Regenerate session untuk keamanan
            $request->session()->regenerate();

            // Redirect berdasarkan role
            return $this->redirectBasedOnRole($user);
        }

        // Jika gagal login
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->withInput($request->only('username'));
    }

    /**
     * Redirect berdasarkan role user
     */
    protected function redirectBasedOnRole($user)
    {
        if ($user->isSuperAdmin()) {
            return redirect()->route('dashboard')->with('success', 'Selamat datang, Super Admin ' . $user->nama_lengkap);
        } elseif ($user->isAdmin()) {
            return redirect()->route('dashboard')->with('success', 'Selamat datang, Admin ' . $user->nama_lengkap);
        } else {
            return redirect()->route('dashboard')->with('success', 'Selamat datang, ' . $user->nama_lengkap);
        }
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout');
    }

    /**
     * Menampilkan dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();

        return view('dashboard', compact('user'));
    }
}
