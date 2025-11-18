<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AIChatHistoryController;
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
use App\Http\Controllers\PostController;
use App\Http\Controllers\RekamMedisController;
use App\Http\Controllers\RekamMedisEmergencyController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\StokMasukController;
use App\Http\Controllers\StokObatController;
use App\Http\Controllers\TokenEmergencyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\SuratPengantarController;
use App\Http\Controllers\MedicalArchivesController;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Posts Index Route
Route::get('/blog', [LandingPageController::class, 'indexPosts'])->name('landing.posts.index');

// Public Post Detail Route
Route::get('/blog/{post}', [LandingPageController::class, 'showPost'])->name('landing.posts.show');

// Landing Page Route
Route::get('/', [LandingPageController::class, 'index'])->name('landing');

// AI Chat Page Route
Route::get('/ai-chat', [LandingPageController::class, 'aiChat'])->name('ai-chat');

// Public Surat Pengantar Verification Route
Route::get('/verify/{token}', [SuratPengantarController::class, 'verifyPublic'])->name('surat-pengantar.verify');

// AI Chat API Route (for landing page)
Route::post('/api/chat', [LandingPageController::class, 'chat'])->name('api.chat');
Route::post('/api/auth/check-nik', [LandingPageController::class, 'checkNik'])->name('api.auth.check-nik')->middleware('track.login');
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
    Route::get('/karyawan/export', [KaryawanController::class, 'export'])->name('karyawan.export')->middleware('role:Admin,Super Admin');
    Route::post('/karyawan/import', [KaryawanController::class, 'import'])->name('karyawan.import')->middleware('role:Super Admin');
    Route::post('/karyawan/bulk-delete', [KaryawanController::class, 'bulkDelete'])->name('karyawan.bulkDelete');
    Route::post('/karyawan/verify-manual', [KaryawanController::class, 'verifyManual'])->name('karyawan.verifyManual');

    // Karyawan Resource Routes
    Route::resource('karyawan', KaryawanController::class)->parameters([
        'karyawan' => 'karyawan',
    ]);

    // Keluarga Routes - Custom routes BEFORE resource routes
    Route::get('/keluarga/search-karyawan', [KeluargaController::class, 'searchKaryawan'])->name('keluarga.searchKaryawan');
    Route::get('/keluarga/download-template', [KeluargaController::class, 'downloadTemplate'])->name('keluarga.downloadTemplate')->middleware('role:Super Admin');
    Route::get('/keluarga/export', [KeluargaController::class, 'export'])->name('keluarga.export')->middleware('role:Admin,Super Admin');
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

    // Stok Opname Export Routes (diletakkan sebelum route dengan parameter)
    Route::get('/stok/export-stock-opname', [StokController::class, 'exportStockOpname'])->name('stok.export.stock-opname');

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

    // Stok Obat Routes
    Route::get('/stok-obat', [StokObatController::class, 'index'])->name('stok-obat.index');

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
    Route::get('/diagnosa/export', [DiagnosaController::class, 'export'])->name('diagnosa.export')->middleware('role:Admin,Super Admin');
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
    Route::get('/rekam-medis/export', [RekamMedisController::class, 'export'])->name('rekam-medis.export')->middleware('role:Admin,Super Admin');
    Route::get('/rekam-medis/choose-type', [RekamMedisController::class, 'chooseType'])->name('rekam-medis.chooseType');
    Route::get('/rekam-medis/create-emergency', [RekamMedisController::class, 'createEmergency'])->name('rekam-medis.createEmergency')->middleware('role:User,Admin,Super Admin');
    Route::post('/rekam-medis/store-emergency', [RekamMedisController::class, 'storeEmergency'])->name('rekam-medis.storeEmergency')->middleware('role:User,Admin,Super Admin');
    // Rekam Medis Resource Routes
    Route::resource('rekam-medis', RekamMedisController::class)->parameters([
        'rekam-medis' => 'id_rekam',
    ]);

    // Surat Pengantar Routes
    Route::get('/surat-pengantar/create', [SuratPengantarController::class, 'create'])->name('surat-pengantar.create');
    Route::post('/surat-pengantar', [SuratPengantarController::class, 'store'])->name('surat-pengantar.store');
    Route::get('/surat-pengantar/{suratPengantar}/print', [SuratPengantarController::class, 'print'])->name('surat-pengantar.print');
    Route::resource('surat-pengantar', SuratPengantarController::class)->except(['create', 'store']);

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
    Route::get('/rekam-medis-emergency/export', [RekamMedisEmergencyController::class, 'export'])->name('rekam-medis-emergency.export')->middleware('role:Admin,Super Admin');

    // External Employee Routes - Custom routes BEFORE resource routes
    Route::get('/external-employee', [ExternalEmployeeController::class, 'index'])->name('external-employee.index');
    Route::get('/external-employee/create', [ExternalEmployeeController::class, 'create'])->name('external-employee.create');
    Route::post('/external-employee', [ExternalEmployeeController::class, 'store'])->name('external-employee.store');
    Route::post('/external-employee/import', [ExternalEmployeeController::class, 'import'])->name('external-employee.import')->middleware('role:Super Admin');
    Route::get('/external-employee/export', [ExternalEmployeeController::class, 'export'])->name('external-employee.export')->middleware('role:Admin,Super Admin');
    Route::post('/external-employee/bulk-delete', [ExternalEmployeeController::class, 'bulkDelete'])->name('external-employee.bulkDelete');
    Route::get('/external-employee/template', [ExternalEmployeeController::class, 'downloadTemplate'])->name('external-employee.template')->middleware('role:Super Admin');

    // External Employee Resource Routes
    Route::get('/external-employee/{id}', [ExternalEmployeeController::class, 'show'])->name('external-employee.show');
    Route::get('/external-employee/{id}/edit', [ExternalEmployeeController::class, 'edit'])->name('external-employee.edit');
    Route::put('/external-employee/{id}', [ExternalEmployeeController::class, 'update'])->name('external-employee.update');
    Route::delete('/external-employee/{id}', [ExternalEmployeeController::class, 'destroy'])->name('external-employee.destroy');

    // Laporan Routes
    Route::get('/laporan/transaksi', [LaporanController::class, 'transaksi'])->name('laporan.transaksi');
    Route::get('/laporan/transaksi/{id}/detail', [LaporanController::class, 'detailTransaksi'])->name('laporan.detail');
    Route::get('/laporan/transaksi/emergency/{id}/detail', [LaporanController::class, 'detailTransaksiEmergency'])->name('laporan.detail-emergency');
    Route::get('/laporan/transaksi/emergency/{id}/cetak', [LaporanController::class, 'cetakDetailTransaksiEmergency'])->name('laporan.cetak.detail-emergency');
    Route::get('/laporan/transaksi/{id}/cetak', [LaporanController::class, 'cetakDetailTransaksi'])->name('laporan.cetak.detail');
    Route::get('/laporan/transaksi/export', [LaporanController::class, 'exportTransaksi'])->name('laporan.export.transaksi');

    // Medical Archives Routes
    Route::get('/medical-archives', [MedicalArchivesController::class, 'index'])->name('medical-archives.index');
    Route::get('/medical-archives/{id_karyawan}', [MedicalArchivesController::class, 'show'])->name('medical-archives.show');
    Route::get('/medical-archives/create', [MedicalArchivesController::class, 'create'])->name('medical-archives.create');
    Route::post('/medical-archives', [MedicalArchivesController::class, 'store'])->name('medical-archives.store');
    Route::get('/medical-archives/{id_karyawan}/edit', [MedicalArchivesController::class, 'edit'])->name('medical-archives.edit');
    Route::put('/medical-archives/{id_karyawan}', [MedicalArchivesController::class, 'update'])->name('medical-archives.update');
    Route::delete('/medical-archives/{id_karyawan}', [MedicalArchivesController::class, 'destroy'])->name('medical-archives.destroy');
    Route::get('/api/medical-archives/search-employees', [MedicalArchivesController::class, 'searchEmployees'])->name('medical-archives.search-employees');

    // Medical Archive Routes
    Route::get('/medical-archives/{id_karyawan}/surat-rekomendasi-medis', [MedicalArchivesController::class, 'suratRekomendasiMedis'])->name('medical-archives.surat-rekomendasi-medis');
    Route::post('/medical-archives/{id_karyawan}/surat-rekomendasi-medis/upload', [MedicalArchivesController::class, 'uploadSuratRekomendasi'])->name('medical-archives.surat-rekomendasi-medis.upload');
    Route::get('/medical-archives/{id_karyawan}/surat-rekomendasi-medis/{id}/edit', [MedicalArchivesController::class, 'editSuratRekomendasi'])->name('medical-archives.surat-rekomendasi-medis.edit');
    Route::put('/medical-archives/{id_karyawan}/surat-rekomendasi-medis/{id}', [MedicalArchivesController::class, 'updateSuratRekomendasi'])->name('medical-archives.surat-rekomendasi-medis.update');
    Route::get('/medical-archives/{id_karyawan}/surat-rekomendasi-medis/{id}/download', [MedicalArchivesController::class, 'downloadSuratRekomendasi'])->name('medical-archives.surat-rekomendasi-medis.download');
    Route::delete('/medical-archives/{id_karyawan}/surat-rekomendasi-medis/{id}', [MedicalArchivesController::class, 'deleteSuratRekomendasi'])->name('medical-archives.surat-rekomendasi-medis.delete');
    Route::get('/medical-archives/{id_karyawan}/medical-check-up', [MedicalArchivesController::class, 'medicalCheckUp'])->name('medical-archives.medical-check-up');

    // Monitoring Harga Routes
    Route::get('/monitoring/harga', [MonitoringHargaController::class, 'index'])->name('monitoring.harga.index');
    Route::get('/monitoring/harga/export', [MonitoringHargaController::class, 'exportMonitoring'])->name('monitoring.harga.export')->middleware('role:Admin,Super Admin');
    Route::post('/monitoring/harga/validate-continuity', [MonitoringHargaController::class, 'validateHargaContinuity'])->name('monitoring.harga.validate-continuity')->middleware('role:Admin,Super Admin');
    Route::get('/monitoring/harga/recommendations', [MonitoringHargaController::class, 'generateRecommendations'])->name('monitoring.harga.recommendations')->middleware('role:Admin,Super Admin');
    Route::post('/monitoring/harga/bulk-create', [MonitoringHargaController::class, 'bulkCreateHarga'])->name('monitoring.harga.bulk-create')->middleware('role:Admin,Super Admin');
    Route::get('/monitoring/harga/history/{idObat}', [MonitoringHargaController::class, 'getHargaHistory'])->name('monitoring.harga.history')->middleware('role:Admin,Super Admin');


    // Posts Routes
    Route::resource('posts', PostController::class)->middleware('role:Admin,Super Admin');

    // Fingerprint Routes
    Route::get('/fingerprint', [FingerprintController::class, 'index'])->name('fingerprint.index');
    Route::get('/fingerprint/employees', [FingerprintController::class, 'getEmployees'])->name('fingerprint.employees');
    Route::get('/fingerprint/search-employees', [FingerprintController::class, 'searchEmployees'])->name('fingerprint.search-employees');
    Route::get('/fingerprint/templates', [FingerprintController::class, 'getFingerprintTemplates'])->name('fingerprint.templates');
    Route::post('/fingerprint/save', [FingerprintController::class, 'saveFingerprint'])->name('fingerprint.save');
    Route::post('/fingerprint/delete', [FingerprintController::class, 'deleteFingerprint'])->name('fingerprint.delete');

    // AI Chat History Routes (Admin & Super Admin only)
    Route::middleware(['auth', 'role:Admin,Super Admin'])->group(function () {
        Route::get('/ai-chat-history', [AIChatHistoryController::class, 'index'])->name('ai-chat-history.index');
        Route::get('/ai-chat-history/{nik}', [AIChatHistoryController::class, 'show'])->name('ai-chat-history.show');
        Route::post('/ai-chat-history/search', [AIChatHistoryController::class, 'search'])->name('ai-chat-history.search');
        Route::get('/ai-chat-history/export', [AIChatHistoryController::class, 'export'])->name('ai-chat-history.export');
        Route::get('/api/ai-chat-history/statistics', [AIChatHistoryController::class, 'getStatistics'])->name('api.ai-chat-history.statistics');
        Route::get('/api/ai-chat-history/chart-data', [AIChatHistoryController::class, 'getChartData'])->name('api.ai-chat-history.chart-data');
    });
});
