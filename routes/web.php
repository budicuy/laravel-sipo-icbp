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

    // Routes untuk Super Admin
    Route::middleware('role:Super Admin')->group(function () {
        // Tambahkan routes khusus Super Admin di sini
    });

    // Routes untuk Admin
    Route::middleware('role:Admin,Super Admin')->group(function () {
        // Tambahkan routes khusus Admin di sini
    });

    // Routes untuk semua user yang authenticated
    // Tambahkan routes umum di sini
});
