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
use App\Http\Controllers\StokObatController;
use App\Http\Controllers\HargaObatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonitoringHargaController;

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
    Route::get('/obat/export', [ObatController::class, 'export'])->name('obat.export');
    Route::post('/obat/import', [ObatController::class, 'import'])->name('obat.import');
    Route::post('/obat/bulk-delete', [ObatController::class, 'bulkDelete'])->name('obat.bulkDelete');

    // Obat Resource Routes
    Route::resource('obat', ObatController::class)->parameters([
        'obat' => 'id_obat'
    ]);

    // Stok Obat Routes
    Route::get('/stok-obat', [StokObatController::class, 'index'])->name('stok-obat.index');
    Route::get('/stok-obat/export', [StokObatController::class, 'export'])->name('stok-obat.export');
    Route::get('/stok-obat/template', [StokObatController::class, 'downloadTemplateStokObat'])->name('stok-obat.template');
    Route::post('/stok-obat/import', [StokObatController::class, 'importStokObat'])->name('stok-obat.import');
    Route::delete('/stok-obat/{id}', [StokObatController::class, 'destroy'])->name('stok-obat.destroy');
    Route::post('/stok-obat/bulk-delete', [StokObatController::class, 'bulkDelete'])->name('stok-obat.bulkDelete');
    Route::post('/stok-obat/fix-consistency', [StokObatController::class, 'fixStokConsistency'])->name('stok-obat.fix-consistency');
    Route::post('/stok-obat/update-stok-awal', [StokObatController::class, 'updateStokAwalForNewPeriod'])->name('stok-obat.update-stok-awal');

    // Harga Obat Routes
    Route::get('/harga-obat', [HargaObatController::class, 'index'])->name('harga-obat.index');
    Route::get('/harga-obat/create', [HargaObatController::class, 'create'])->name('harga-obat.create');
    Route::post('/harga-obat', [HargaObatController::class, 'store'])->name('harga-obat.store');
    Route::get('/harga-obat/{id}/edit', [HargaObatController::class, 'edit'])->name('harga-obat.edit');
    Route::put('/harga-obat/{id}', [HargaObatController::class, 'update'])->name('harga-obat.update');
    Route::delete('/harga-obat/{id}', [HargaObatController::class, 'destroy'])->name('harga-obat.destroy');
    Route::post('/harga-obat/bulk-delete', [HargaObatController::class, 'bulkDelete'])->name('harga-obat.bulkDelete');
    Route::post('/harga-obat/generate-for-periode', [HargaObatController::class, 'generateForPeriode'])->name('harga-obat.generate-for-periode');
    Route::get('/harga-obat/export', [HargaObatController::class, 'export'])->name('harga-obat.export');
    Route::get('/harga-obat/import', [HargaObatController::class, 'import'])->name('harga-obat.import');
    Route::post('/harga-obat/process-import', [HargaObatController::class, 'processImport'])->name('harga-obat.process-import');
    Route::get('/harga-obat/template', [HargaObatController::class, 'downloadTemplate'])->name('harga-obat.template');

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
    Route::get('/rekam-medis/template', [RekamMedisController::class, 'downloadTemplate'])->name('rekam-medis.template');
    Route::post('/rekam-medis/import', [RekamMedisController::class, 'import'])->name('rekam-medis.import');
    Route::get('/rekam-medis/create-emergency', [RekamMedisController::class, 'createEmergency'])->name('rekam-medis.createEmergency');
    Route::post('/rekam-medis/store-emergency', [RekamMedisController::class, 'storeEmergency'])->name('rekam-medis.storeEmergency');
    // Rekam Medis Resource Routes
    Route::resource('rekam-medis', RekamMedisController::class)->parameters([
        'rekam-medis' => 'id_rekam'
    ]);

    // Rekam Medis Emergency Routes
    Route::get('/rekam-medis-emergency', [RekamMedisController::class, 'indexEmergency'])->name('rekam-medis-emergency.index');
    Route::get('/rekam-medis-emergency/create', [RekamMedisController::class, 'createEmergency'])->name('rekam-medis-emergency.create');
    Route::post('/rekam-medis-emergency', [RekamMedisController::class, 'storeEmergency'])->name('rekam-medis-emergency.store');
    Route::get('/rekam-medis-emergency/{id}', [RekamMedisController::class, 'show'])->name('rekam-medis-emergency.show');
    Route::get('/rekam-medis-emergency/{id}/edit', [RekamMedisController::class, 'edit'])->name('rekam-medis-emergency.edit');
    Route::patch('/rekam-medis-emergency/{id}/update-status', [RekamMedisController::class, 'updateStatus'])->name('rekam-medis-emergency.updateStatus');
    Route::put('/rekam-medis-emergency/{id}', [RekamMedisController::class, 'update'])->name('rekam-medis-emergency.update');
    Route::delete('/rekam-medis-emergency/{id}', [RekamMedisController::class, 'destroy'])->name('rekam-medis-emergency.destroy');

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
    Route::get('/laporan/transaksi/{id}/cetak', [LaporanController::class, 'cetakDetailTransaksi'])->name('laporan.cetak.detail');
    Route::post('/laporan/transaksi/export', [LaporanController::class, 'exportTransaksi'])->name('laporan.export');

    // Monitoring Harga Routes
    Route::get('/monitoring/harga', [MonitoringHargaController::class, 'index'])->name('monitoring.harga.index');
    Route::get('/monitoring/harga/export', [MonitoringHargaController::class, 'exportMonitoring'])->name('monitoring.harga.export');
    Route::post('/monitoring/harga/validate-continuity', [MonitoringHargaController::class, 'validateHargaContinuity'])->name('monitoring.harga.validate-continuity');
    Route::get('/monitoring/harga/recommendations', [MonitoringHargaController::class, 'generateRecommendations'])->name('monitoring.harga.recommendations');
    Route::post('/monitoring/harga/bulk-create', [MonitoringHargaController::class, 'bulkCreateHarga'])->name('monitoring.harga.bulk-create');
    Route::get('/monitoring/harga/history/{idObat}', [MonitoringHargaController::class, 'getHargaHistory'])->name('monitoring.harga.history');

    // Routes untuk Super Admin
    Route::middleware('role:Super Admin')->group(function () {
        // Tambahkan routes khusus Super Admin di sini
    });

    // Routes untuk Admin
    Route::middleware('role:Admin,Super Admin')->group(function () {
        // Tambahkan routes khusus Admin di sini
    });
});
