<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KeluargaController;
use App\Http\Controllers\DiagnosaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RekamMedisController;
use App\Http\Controllers\KunjunganController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\DashboardController;

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

    // Dashboard API Routes
    Route::get('/api/dashboard/statistics', [DashboardController::class, 'getStatistics'])->name('api.dashboard.statistics');
    Route::get('/api/dashboard/visit-analysis', [DashboardController::class, 'getVisitAnalysis'])->name('api.dashboard.visit-analysis');
    Route::get('/api/dashboard/realtime', [DashboardController::class, 'getRealtimeData'])->name('api.dashboard.realtime');

    // Karyawan Routes - Custom routes BEFORE resource routes
    Route::get('/karyawan/template', [KaryawanController::class, 'downloadTemplate'])->name('karyawan.template');
    Route::post('/karyawan/import', [KaryawanController::class, 'import'])->name('karyawan.import');
    Route::post('/karyawan/bulk-delete', [KaryawanController::class, 'bulkDelete'])->name('karyawan.bulkDelete');

    // Karyawan Resource Routes
    Route::resource('karyawan', KaryawanController::class)->parameters([
        'karyawan' => 'karyawan'
    ]);

    // Keluarga Routes - Custom routes BEFORE resource routes
    Route::get('/keluarga/search-karyawan', [KeluargaController::class, 'searchKaryawan'])->name('keluarga.searchKaryawan');
    Route::get('/keluarga/download-template', [KeluargaController::class, 'downloadTemplate'])->name('keluarga.downloadTemplate');
    Route::post('/keluarga/import', [KeluargaController::class, 'import'])->name('keluarga.import');
    Route::post('/keluarga/bulk-delete', [KeluargaController::class, 'bulkDelete'])->name('keluarga.bulkDelete');

    // Keluarga Resource Routes
    Route::resource('keluarga', KeluargaController::class)->parameters([
        'keluarga' => 'id_keluarga'
    ]);

    // Obat Routes - Custom routes BEFORE resource routes
    Route::get('/obat/template', [ObatController::class, 'downloadTemplate'])->name('obat.template');
    Route::post('/obat/import', [ObatController::class, 'import'])->name('obat.import');
    Route::post('/obat/bulk-delete', [ObatController::class, 'bulkDelete'])->name('obat.bulkDelete');

    // Obat Resource Routes
    Route::resource('obat', ObatController::class)->parameters([
        'obat' => 'id_obat'
    ]);

    // Diagnosa Routes - Custom routes BEFORE resource routes
    Route::get('/diagnosa/template', [DiagnosaController::class, 'downloadTemplate'])->name('diagnosa.template');
    Route::post('/diagnosa/import', [DiagnosaController::class, 'import'])->name('diagnosa.import');
    Route::post('/diagnosa/bulk-delete', [DiagnosaController::class, 'bulkDelete'])->name('diagnosa.bulkDelete');

    // Diagnosa Resource Routes
    Route::resource('diagnosa', DiagnosaController::class)->parameters([
        'diagnosa' => 'id_diagnosa'
    ]);

    // User Routes - Resource Routes
    Route::resource('user', UserController::class)->parameters([
        'user' => 'id_user'
    ]);

    // Kunjungan Routes
    Route::get('/kunjungan', [KunjunganController::class, 'index'])->name('kunjungan.index');
    Route::get('/kunjungan/{id}/detail', [KunjunganController::class, 'show'])->name('kunjungan.detail');

    // Rekam Medis Routes - Custom routes BEFORE resource routes
    Route::get('/rekam-medis/search-karyawan', [RekamMedisController::class, 'searchKaryawan'])->name('rekam-medis.searchKaryawan');
    Route::get('/rekam-medis/get-family-members', [RekamMedisController::class, 'getFamilyMembers'])->name('rekam-medis.getFamilyMembers');
    Route::get('/rekam-medis/search-pasien', [RekamMedisController::class, 'searchPasien'])->name('rekam-medis.searchPasien');
    Route::get('/rekam-medis/get-obat-by-diagnosa', [RekamMedisController::class, 'getObatByDiagnosa'])->name('rekam-medis.getObatByDiagnosa');
    Route::patch('/rekam-medis/{id}/update-status', [RekamMedisController::class, 'updateStatus'])->name('rekam-medis.updateStatus');

    // Rekam Medis Resource Routes
    Route::resource('rekam-medis', RekamMedisController::class)->parameters([
        'rekam-medis' => 'id_rekam'
    ]);

    // Surat Sakit Routes
    Route::get('/surat-sakit', function () {
        return view('surat-sakit.create');
    })->name('surat-sakit.create');

    Route::post('/surat-sakit', function () {
        return redirect()->route('surat-sakit.create');
    })->name('surat-sakit.store');

    // Laporan Routes
    Route::get('/laporan/transaksi', [LaporanController::class, 'transaksi'])->name('laporan.transaksi');
    Route::get('/laporan/transaksi/{id}/detail', [LaporanController::class, 'detailTransaksi'])->name('laporan.detail');
    Route::post('/laporan/transaksi/export', [LaporanController::class, 'exportTransaksi'])->name('laporan.export');

    // Routes untuk Super Admin
    Route::middleware('role:Super Admin')->group(function () {
        // Tambahkan routes khusus Super Admin di sini
    });

    // Routes untuk Admin
    Route::middleware('role:Admin,Super Admin')->group(function () {
        // Tambahkan routes khusus Admin di sini
    });
});
