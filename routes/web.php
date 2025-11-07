<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiagnosaController;
use App\Http\Controllers\DiagnosaEmergencyController;
use App\Http\Controllers\ExternalEmployeeController;
use App\Http\Controllers\HargaObatController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KeluargaController;
use App\Http\Controllers\KunjunganController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MonitoringHargaController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\RekamMedisController;
use App\Http\Controllers\RekamMedisEmergencyController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\StokMasukController;
use App\Http\Controllers\SuratPengantarIstirahatController;
use App\Http\Controllers\TokenEmergencyController;
use App\Http\Controllers\UserController;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Landing Page Route
Route::get('/', [LandingPageController::class, 'index'])->name('landing');

// AI Chat Page Route
Route::get('/ai-chat', [LandingPageController::class, 'aiChat'])->name('ai-chat');

// AI Chat API Route (for landing page)
Route::post('/api/chat', [LandingPageController::class, 'chat'])->name('api.chat');
Route::post('/api/auth/check-nik', [LandingPageController::class, 'checkNik'])->name('api.auth.check-nik');
Route::post('/api/medical-history', [LandingPageController::class, 'getMedicalHistory'])->name('api.medical-history');
Route::post('/api/family-list', [LandingPageController::class, 'getFamilyList'])->name('api.family-list');
Route::post('/api/preload-medical-data', [LandingPageController::class, 'preloadMedicalData'])->name('api.preload-medical-data');

// Alternative route to login
Route::get('/portal', function () {
    return redirect()->route('login');
})->name('portal');

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
    Route::get('/api/dashboard/top-diagnoses', [DashboardController::class, 'getTopDiagnoses'])->name('api.dashboard.top-diagnoses');

    // Karyawan Routes - Custom routes BEFORE resource routes
    Route::get('/karyawan/template', [KaryawanController::class, 'downloadTemplate'])->name('karyawan.template')->middleware('role:Super Admin');
    Route::post('/karyawan/import', [KaryawanController::class, 'import'])->name('karyawan.import')->middleware('role:Super Admin');
    Route::post('/karyawan/bulk-delete', [KaryawanController::class, 'bulkDelete'])->name('karyawan.bulkDelete');

    // Karyawan Resource Routes
    Route::resource('karyawan', KaryawanController::class)->parameters([
        'karyawan' => 'karyawan',
    ]);

    // Keluarga Routes - Custom routes BEFORE resource routes
    Route::get('/keluarga/search-karyawan', [KeluargaController::class, 'searchKaryawan'])->name('keluarga.searchKaryawan');
    Route::get('/keluarga/download-template', [KeluargaController::class, 'downloadTemplate'])->name('keluarga.downloadTemplate')->middleware('role:Super Admin');
    Route::post('/keluarga/import', [KeluargaController::class, 'import'])->name('keluarga.import')->middleware('role:Super Admin');
    Route::post('/keluarga/bulk-delete', [KeluargaController::class, 'bulkDelete'])->name('keluarga.bulkDelete');

    // Keluarga Resource Routes
    Route::resource('keluarga', KeluargaController::class)->parameters([
        'keluarga' => 'id_keluarga',
    ]);

    // Obat Routes - Custom routes BEFORE resource routes
    Route::get('/obat/template', [ObatController::class, 'downloadTemplate'])->name('obat.template')->middleware('role:Super Admin');
    Route::get('/obat/export', [ObatController::class, 'export'])->name('obat.export')->middleware('role:Super Admin');
    Route::post('/obat/import', [ObatController::class, 'import'])->name('obat.import')->middleware('role:Super Admin');
    Route::post('/obat/bulk-delete', [ObatController::class, 'bulkDelete'])->name('obat.bulkDelete');

    // Obat Resource Routes
    Route::resource('obat', ObatController::class)->parameters([
        'obat' => 'id_obat',
    ]);

    // Sistem Stok Baru (Automated)
    Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
    Route::get('/stok/{obat_id}', [StokController::class, 'show'])->name('stok.show');

    // Stok Bulanan Update (Super Admin only)
    Route::put('/stok/bulanan/{id}', [StokController::class, 'updateStokBulanan'])->name('stok.bulanan.update')->middleware('role:Super Admin');

    // Stok Import Routes (Admin & Super Admin only)
    Route::get('/stok/template', [StokController::class, 'downloadTemplate'])->name('stok.template')->middleware('role:Admin,Super Admin');
    Route::post('/stok/import', [StokController::class, 'import'])->name('stok.import')->middleware('role:Admin,Super Admin');
    Route::get('/stok/template-pakai', [StokController::class, 'downloadTemplatePakai'])->name('stok.template-pakai')->middleware('role:Admin,Super Admin');
    Route::post('/stok/import-pakai', [StokController::class, 'importPakai'])->name('stok.import-pakai')->middleware('role:Admin,Super Admin');

    // Stok Masuk Routes (Admin & Super Admin only)
    Route::post('/stok/masuk', [StokMasukController::class, 'store'])->name('stok.masuk.store')->middleware('role:Admin,Super Admin');

    // Harga Obat Routes
    Route::get('/harga-obat', [HargaObatController::class, 'index'])->name('harga-obat.index');
    Route::get('/harga-obat/create', [HargaObatController::class, 'create'])->name('harga-obat.create')->middleware('role:Admin,Super Admin');
    Route::post('/harga-obat', [HargaObatController::class, 'store'])->name('harga-obat.store')->middleware('role:Admin,Super Admin');
    Route::get('/harga-obat/{id}/edit', [HargaObatController::class, 'edit'])->name('harga-obat.edit')->middleware('role:Admin,Super Admin');
    Route::put('/harga-obat/{id}', [HargaObatController::class, 'update'])->name('harga-obat.update')->middleware('role:Admin,Super Admin');
    Route::delete('/harga-obat/{id}', [HargaObatController::class, 'destroy'])->name('harga-obat.destroy')->middleware('role:Admin,Super Admin');
    Route::post('/harga-obat/bulk-delete', [HargaObatController::class, 'bulkDelete'])->name('harga-obat.bulkDelete')->middleware('role:Admin,Super Admin');
    Route::post('/harga-obat/generate-for-periode', [HargaObatController::class, 'generateForPeriode'])->name('harga-obat.generate-for-periode')->middleware('role:Admin,Super Admin');
    Route::get('/harga-obat/export', [HargaObatController::class, 'export'])->name('harga-obat.export')->middleware('role:Admin,Super Admin');
    Route::get('/harga-obat/import', [HargaObatController::class, 'import'])->name('harga-obat.import')->middleware('role:Admin,Super Admin');
    Route::post('/harga-obat/process-import', [HargaObatController::class, 'processImport'])->name('harga-obat.process-import')->middleware('role:Admin,Super Admin');
    Route::get('/harga-obat/template', [HargaObatController::class, 'downloadTemplate'])->name('harga-obat.template')->middleware('role:Admin,Super Admin');

    // Diagnosa Routes - Custom routes BEFORE resource routes
    Route::get('/diagnosa/template', [DiagnosaController::class, 'downloadTemplate'])->name('diagnosa.template')->middleware('role:Super Admin');
    Route::post('/diagnosa/import', [DiagnosaController::class, 'import'])->name('diagnosa.import')->middleware('role:Super Admin');
    Route::post('/diagnosa/bulk-delete', [DiagnosaController::class, 'bulkDelete'])->name('diagnosa.bulkDelete');

    // Diagnosa Resource Routes
    Route::resource('diagnosa', DiagnosaController::class)->parameters([
        'diagnosa' => 'id_diagnosa',
    ]);

    // Diagnosa Emergency Routes - Custom routes BEFORE resource routes
    Route::post('/diagnosa-emergency/bulk-delete', [DiagnosaEmergencyController::class, 'bulkDelete'])->name('diagnosa-emergency.bulkDelete')->middleware('role:Admin,Super Admin');

    // Diagnosa Emergency Resource Routes
    Route::resource('diagnosa-emergency', DiagnosaEmergencyController::class)->parameters([
        'diagnosa-emergency' => 'id_diagnosa_emergency',
    ]);

    // API Route for Obat Search
    Route::get('/api/obat/search', function (Request $request) {
        $search = $request->get('q');
        $obats = Obat::where('nama_obat', 'like', '%'.$search.'%')
            ->select(['id_obat', 'nama_obat', 'deskripsi_obat'])
            ->limit(10)
            ->get();

        return response()->json($obats);
    })->name('api.obat.search');

    // User Routes - Resource Routes (Admin & Super Admin only)
    Route::resource('user', UserController::class)->parameters([
        'user' => 'id_user',
    ])->middleware('role:Admin,Super Admin');

    // Kunjungan Routes
    Route::get('/kunjungan', [KunjunganController::class, 'index'])->name('kunjungan.index');
    Route::get('/kunjungan/{id}/detail', [KunjunganController::class, 'show'])->name('kunjungan.detail');

    // Rekam Medis Routes - Custom routes BEFORE resource routes
    Route::get('/rekam-medis/search-karyawan', [RekamMedisController::class, 'searchKaryawan'])->name('rekam-medis.searchKaryawan');
    Route::get('/rekam-medis/get-family-members', [RekamMedisController::class, 'getFamilyMembers'])->name('rekam-medis.getFamilyMembers');
    Route::get('/rekam-medis/search-pasien', [RekamMedisController::class, 'searchPasien'])->name('rekam-medis.searchPasien');
    Route::get('/rekam-medis/get-obat-by-diagnosa', [RekamMedisController::class, 'getObatByDiagnosa'])->name('rekam-medis.getObatByDiagnosa');
    Route::patch('/rekam-medis/{id}/update-status', [RekamMedisController::class, 'updateStatus'])->name('rekam-medis.updateStatus');
    Route::get('/rekam-medis/template', [RekamMedisController::class, 'downloadTemplate'])->name('rekam-medis.template')->middleware('role:Super Admin');
    Route::post('/rekam-medis/import', [RekamMedisController::class, 'import'])->name('rekam-medis.import')->middleware('role:Super Admin');
    Route::get('/rekam-medis/choose-type', [RekamMedisController::class, 'chooseType'])->name('rekam-medis.chooseType');
    Route::get('/rekam-medis/create-emergency', [RekamMedisController::class, 'createEmergency'])->name('rekam-medis.createEmergency')->middleware('role:User,Admin,Super Admin');
    Route::post('/rekam-medis/store-emergency', [RekamMedisController::class, 'storeEmergency'])->name('rekam-medis.storeEmergency')->middleware('role:User,Admin,Super Admin');
    // Rekam Medis Resource Routes
    Route::resource('rekam-medis', RekamMedisController::class)->parameters([
        'rekam-medis' => 'id_rekam',
    ]);

    // Token Emergency Routes
    Route::get('/token-emergency', [TokenEmergencyController::class, 'index'])->name('token-emergency.index');
    Route::get('/token-emergency/create', [TokenEmergencyController::class, 'create'])->name('token-emergency.create');
    Route::post('/token-emergency/generate', [TokenEmergencyController::class, 'generate'])->name('token-emergency.generate');
    Route::post('/token-emergency/validate', [TokenEmergencyController::class, 'validateToken'])->name('token-emergency.validate');
    Route::get('/token-emergency/validate', [TokenEmergencyController::class, 'showValidateForm'])->name('token-emergency.validate.form');
    Route::delete('/token-emergency/{id}', [TokenEmergencyController::class, 'destroy'])->name('token-emergency.destroy');
    Route::post('/token-emergency/clear', [TokenEmergencyController::class, 'clearToken'])->name('token-emergency.clear');

    // Token Request Routes
    Route::get('/token-emergency/request', [TokenEmergencyController::class, 'requestForm'])->name('token-emergency.request');
    Route::post('/token-emergency/request', [TokenEmergencyController::class, 'storeRequest'])->name('token-emergency.storeRequest');

    // User Token Management Routes
    Route::get('/token-emergency/my-tokens', [TokenEmergencyController::class, 'myTokens'])->name('token-emergency.my-tokens');

    // API Routes for Token Emergency
    Route::get('/api/token-emergency/pending-requests', [TokenEmergencyController::class, 'apiPendingRequests'])->name('token-emergency.api.pending-requests');
    Route::get('/api/token-emergency/audit-trail', [TokenEmergencyController::class, 'apiAuditTrail'])->name('token-emergency.api.audit-trail');
    Route::get('/api/token-emergency/manage-tokens', [TokenEmergencyController::class, 'apiManageTokens'])->name('token-emergency.api.manage-tokens');
    Route::get('/api/token-emergency/request-history', [TokenEmergencyController::class, 'apiRequestHistory'])->name('token-emergency.api.request-history');

    // Token Management Routes (Admin/Super Admin only)
    Route::middleware(['auth', 'role:Admin,Super Admin'])->group(function () {
        Route::get('/token-emergency/pending-requests', [TokenEmergencyController::class, 'pendingRequests'])->name('token-emergency.pending-requests');
        Route::post('/token-emergency/approve-request/{id}', [TokenEmergencyController::class, 'approveRequest'])->name('token-emergency.approve-request');
        Route::post('/token-emergency/reject-request/{id}', [TokenEmergencyController::class, 'rejectRequest'])->name('token-emergency.reject-request');
        Route::get('/token-emergency/monitoring', [TokenEmergencyController::class, 'monitoring'])->name('token-emergency.monitoring');
        Route::get('/token-emergency/audit-trail', [TokenEmergencyController::class, 'auditTrail'])->name('token-emergency.audit-trail');
        Route::get('/token-emergency/user-profile/{userId}', [TokenEmergencyController::class, 'userProfile'])->name('token-emergency.user-profile');
    });

    // Rekam Medis Emergency Routes (keep individual routes for CRUD operations)
    Route::get('/rekam-medis-emergency', [RekamMedisEmergencyController::class, 'index'])->name('rekam-medis-emergency.index');
    Route::get('/rekam-medis-emergency/create', [RekamMedisEmergencyController::class, 'create'])->name('rekam-medis-emergency.create')->middleware('role:User,Admin,Super Admin');
    Route::post('/rekam-medis-emergency', [RekamMedisEmergencyController::class, 'store'])->name('rekam-medis-emergency.store')->middleware('role:User,Admin,Super Admin');
    Route::get('/rekam-medis-emergency/{id}', [RekamMedisEmergencyController::class, 'show'])->name('rekam-medis-emergency.show');
    Route::get('/rekam-medis-emergency/{id}/edit', [RekamMedisEmergencyController::class, 'edit'])->name('rekam-medis-emergency.edit')->middleware('role:Admin,Super Admin');
    Route::patch('/rekam-medis-emergency/{id}/update-status', [RekamMedisEmergencyController::class, 'updateStatus'])->name('rekam-medis-emergency.updateStatus');
    Route::put('/rekam-medis-emergency/{id}', [RekamMedisEmergencyController::class, 'update'])->name('rekam-medis-emergency.update')->middleware('role:Admin,Super Admin');
    Route::delete('/rekam-medis-emergency/{id}', [RekamMedisEmergencyController::class, 'destroy'])->name('rekam-medis-emergency.destroy')->middleware('role:Admin,Super Admin');
    Route::get('/rekam-medis-emergency/get-obat-by-diagnosa', [RekamMedisEmergencyController::class, 'getObatByDiagnosa'])->name('rekam-medis-emergency.getObatByDiagnosa');
    Route::get('/rekam-medis-emergency/get-diagnosa-with-obat', [RekamMedisEmergencyController::class, 'getDiagnosaWithObat'])->name('rekam-medis-emergency.getDiagnosaWithObat');

    // External Employee Routes
    Route::get('/external-employee', [ExternalEmployeeController::class, 'index'])->name('external-employee.index');
    Route::get('/external-employee/create', [ExternalEmployeeController::class, 'create'])->name('external-employee.create');
    Route::post('/external-employee', [ExternalEmployeeController::class, 'store'])->name('external-employee.store');
    Route::get('/external-employee/{id}', [ExternalEmployeeController::class, 'show'])->name('external-employee.show');
    Route::get('/external-employee/{id}/edit', [ExternalEmployeeController::class, 'edit'])->name('external-employee.edit');
    Route::put('/external-employee/{id}', [ExternalEmployeeController::class, 'update'])->name('external-employee.update');
    Route::delete('/external-employee/{id}', [ExternalEmployeeController::class, 'destroy'])->name('external-employee.destroy');
    Route::post('/external-employee/import', [ExternalEmployeeController::class, 'import'])->name('external-employee.import')->middleware('role:Super Admin');
    Route::get('/external-employee/export', [ExternalEmployeeController::class, 'export'])->name('external-employee.export')->middleware('role:Super Admin');
    Route::post('/external-employee/bulk-delete', [ExternalEmployeeController::class, 'bulkDelete'])->name('external-employee.bulkDelete');
    Route::get('/external-employee/template', [ExternalEmployeeController::class, 'downloadTemplate'])->name('external-employee.template')->middleware('role:Super Admin');

    // Surat Pengantar Istirahat Routes
    Route::get('/surat-pengantar-istirahat/search-rekam-medis', [SuratPengantarIstirahatController::class, 'searchRekamMedis'])->name('surat-pengantar-istirahat.searchRekamMedis');
    Route::get('/surat-pengantar-istirahat/get-rekam-medis-detail/{id_rekam}', [SuratPengantarIstirahatController::class, 'getRekamMedisDetail'])->name('surat-pengantar-istirahat.getRekamMedisDetail');
    Route::get('/surat-pengantar-istirahat/get-rekam-medis-emergency-detail/{id_emergency}', [SuratPengantarIstirahatController::class, 'getRekamMedisEmergencyDetail'])->name('surat-pengantar-istirahat.getRekamMedisEmergencyDetail');
    Route::get('/surat-pengantar-istirahat/{suratPengantarIstirahat}/cetak', [SuratPengantarIstirahatController::class, 'cetak'])->name('surat-pengantar-istirahat.cetak');

    Route::resource('surat-pengantar-istirahat', SuratPengantarIstirahatController::class)->parameters([
        'surat-pengantar-istirahat' => 'suratPengantarIstirahat',
    ]);

    // Surat Sakit Routes (Legacy - redirect to new Surat Pengantar Istirahat)
    Route::get('/surat-sakit', function () {
        return redirect()->route('surat-pengantar-istirahat.index');
    })->name('surat-sakit.create');

    Route::post('/surat-sakit', function () {
        return redirect()->route('surat-pengantar-istirahat.create');
    })->name('surat-sakit.store');

    // Laporan Routes
    Route::get('/laporan/transaksi', [LaporanController::class, 'transaksi'])->name('laporan.transaksi');
    Route::get('/laporan/transaksi/{id}/detail', [LaporanController::class, 'detailTransaksi'])->name('laporan.detail');
    Route::get('/laporan/transaksi/emergency/{id}/detail', [LaporanController::class, 'detailTransaksiEmergency'])->name('laporan.detail-emergency');
    Route::get('/laporan/transaksi/emergency/{id}/cetak', [LaporanController::class, 'cetakDetailTransaksiEmergency'])->name('laporan.cetak.detail-emergency');
    Route::get('/laporan/transaksi/{id}/cetak', [LaporanController::class, 'cetakDetailTransaksi'])->name('laporan.cetak.detail');
    Route::get('/laporan/transaksi/export', [LaporanController::class, 'exportTransaksi'])->name('laporan.export.transaksi');

    // Monitoring Harga Routes
    Route::get('/monitoring/harga', [MonitoringHargaController::class, 'index'])->name('monitoring.harga.index');
    Route::get('/monitoring/harga/export', [MonitoringHargaController::class, 'exportMonitoring'])->name('monitoring.harga.export')->middleware('role:Admin,Super Admin');
    Route::post('/monitoring/harga/validate-continuity', [MonitoringHargaController::class, 'validateHargaContinuity'])->name('monitoring.harga.validate-continuity')->middleware('role:Admin,Super Admin');
    Route::get('/monitoring/harga/recommendations', [MonitoringHargaController::class, 'generateRecommendations'])->name('monitoring.harga.recommendations')->middleware('role:Admin,Super Admin');
    Route::post('/monitoring/harga/bulk-create', [MonitoringHargaController::class, 'bulkCreateHarga'])->name('monitoring.harga.bulk-create')->middleware('role:Admin,Super Admin');
    Route::get('/monitoring/harga/history/{idObat}', [MonitoringHargaController::class, 'getHargaHistory'])->name('monitoring.harga.history')->middleware('role:Admin,Super Admin');

    // Routes untuk Super Admin
    Route::middleware('role:Super Admin')->group(function () {
        // Tambahkan routes khusus Super Admin di sini
    });

    // Routes untuk Admin
    Route::middleware('role:Admin,Super Admin')->group(function () {
        // Tambahkan routes khusus Admin di sini
    });
});
