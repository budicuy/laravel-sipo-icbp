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

    Route::get('/karyawan/create', function () {
        return view('karyawan.create');
    })->name('karyawan.create');

    Route::get('/karyawan/{id}/edit', function ($id) {
        return view('karyawan.edit');
    })->name('karyawan.edit');

    Route::get('/keluarga', function () {
        return view('keluarga.index');
    })->name('keluarga.index');

    Route::get('/keluarga/create', function () {
        return view('keluarga.create');
    })->name('keluarga.create');

    Route::post('/keluarga', function () {
        return redirect()->route('keluarga.index');
    })->name('keluarga.store');

    Route::get('/keluarga/{id}/edit', function ($id) {
        return view('keluarga.edit');
    })->name('keluarga.edit');

    Route::put('/keluarga/{id}', function ($id) {
        return redirect()->route('keluarga.index');
    })->name('keluarga.update');

    Route::get('/obat', function () {
        return view('obat.index');
    })->name('obat.index');

    Route::get('/obat/create', function () {
        return view('obat.create');
    })->name('obat.create');

    Route::post('/obat', function () {
        return redirect()->route('obat.index');
    })->name('obat.store');

    Route::get('/obat/{id}/edit', function ($id) {
        return view('obat.edit');
    })->name('obat.edit');

    Route::put('/obat/{id}', function ($id) {
        return redirect()->route('obat.index');
    })->name('obat.update');

    Route::get('/diagnosa', function () {
        return view('diagnosa.index');
    })->name('diagnosa.index');

    Route::get('/diagnosa/create', function () {
        return view('diagnosa.create');
    })->name('diagnosa.create');

    Route::post('/diagnosa', function () {
        return redirect()->route('diagnosa.index');
    })->name('diagnosa.store');

    Route::get('/diagnosa/{id}/edit', function ($id) {
        return view('diagnosa.edit');
    })->name('diagnosa.edit');

    Route::put('/diagnosa/{id}', function ($id) {
        return redirect()->route('diagnosa.index');
    })->name('diagnosa.update');

    Route::get('/user', function () {
        return view('user.index');
    })->name('user.index');

    Route::get('/user/create', function () {
        return view('user.create');
    })->name('user.create');

    Route::post('/user', function () {
        return redirect()->route('user.index');
    })->name('user.store');

    Route::get('/user/{id}/edit', function ($id) {
        return view('user.edit');
    })->name('user.edit');

    Route::put('/user/{id}', function ($id) {
        return redirect()->route('user.index');
    })->name('user.update');

    // Kunjungan Routes
    Route::get('/kunjungan', function () {
        return view('kunjungan.index');
    })->name('kunjungan.index');

    Route::get('/kunjungan/{id}/detail', function ($id) {
        return view('kunjungan.detail');
    })->name('kunjungan.detail');

    // Rekam Medis Routes
    Route::get('/rekam-medis', function () {
        return view('rekam-medis.index');
    })->name('rekam-medis.index');

    Route::get('/rekam-medis/create', function () {
        return view('rekam-medis.create');
    })->name('rekam-medis.create');

    Route::post('/rekam-medis', function () {
        return redirect()->route('rekam-medis.index');
    })->name('rekam-medis.store');

    Route::get('/rekam-medis/{id}', function ($id) {
        return view('rekam-medis.detail');
    })->name('rekam-medis.detail');

    // Surat Sakit Routes
    Route::get('/surat-sakit', function () {
        return view('surat-sakit.create');
    })->name('surat-sakit.create');

    Route::post('/surat-sakit', function () {
        return redirect()->route('surat-sakit.create');
    })->name('surat-sakit.store');

    // Laporan Routes
    Route::get('/laporan/transaksi', function () {
        return view('laporan.transaksi');
    })->name('laporan.transaksi');

    // Routes untuk Super Admin
    Route::middleware('role:Super Admin')->group(function () {
        // Tambahkan routes khusus Super Admin di sini
    });

    // Routes untuk Admin
    Route::middleware('role:Admin,Super Admin')->group(function () {
        // Tambahkan routes khusus Admin di sini
    });
});
