<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (Requires Authentication)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Master Data Routes
    Route::get('/karyawan', function () {
        return view('karyawan.index');
    })->name('karyawan.index');

    Route::get('/keluarga', function () {
        return view('keluarga.index');
    })->name('keluarga.index');

    Route::get('/obat', function () {
        return view('obat.index');
    })->name('obat.index');

    Route::get('/diagnosa', function () {
        return view('diagnosa.index');
    })->name('diagnosa.index');

    Route::get('/user', function () {
        return view('user.index');
    })->name('user.index');

    // Routes untuk Super Admin
    Route::middleware('role:Super Admin')->group(function () {
        // Tambahkan routes khusus Super Admin di sini
    });

    // Routes untuk Admin
    Route::middleware('role:Admin,Super Admin')->group(function () {
        // Tambahkan routes khusus Admin di sini
    });
});
