@extends('layouts.app')

@section('page-title', 'Detail Medical Archives')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Breadcrumb Navigation -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('medical-archives.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Medical Archives</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Detail</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('medical-archives.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detail Medical Archives</h1>
                    <p class="text-gray-600 mt-1">Informasi lengkap rekam medis karyawan</p>
                </div>
            </div>
        </div>

        <!-- Section 1: Patient Information Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Informasi Pasien
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- NO RM -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">NO RM</label>
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                            {{ $employeeInfo->nik_karyawan ?? '-' }}-{{ $detailedRecords[0]['family_member']->kode_hubungan ?? '-' }}
                        </span>
                    </div>
                    
                    <!-- NIK Karyawan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">NIK Karyawan</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $employeeInfo->nik_karyawan ?? '-' }}</p>
                    </div>
                    
                    <!-- Nama Karyawan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nama Karyawan</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $detailedRecords[0]['family_member']->nama_keluarga ?? '-' }}</p>
                    </div>
                    
                    <!-- Departemen -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Departemen</label>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                            {{ $employeeInfo->nama_departemen ?? '-' }}
                        </span>
                    </div>
                    
                    @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin()))
                    <!-- Tanggal Lahir -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Lahir</label>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $detailedRecords[0]['family_member']->tanggal_lahir ? \Carbon\Carbon::parse($detailedRecords[0]['family_member']->tanggal_lahir)->format('d-m-Y') : '-' }}
                        </p>
                    </div>
                    
                    <!-- Usia -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Usia</label>
                        <p class="text-lg font-semibold text-gray-900">
                            @if($detailedRecords[0]['family_member']->tanggal_lahir)
                                {{ \Carbon\Carbon::parse($detailedRecords[0]['family_member']->tanggal_lahir)->age }} tahun
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    @endif
                    
                    <!-- Hubungan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Hubungan</label>
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-medium">
                            {{ $detailedRecords[0]['family_member']->hubungan_nama ?? '-' }}
                        </span>
                    </div>
                    
                    <!-- Status Karyawan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status Karyawan</label>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                            {{ $employeeInfo->karyawan_status ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Visit History Table -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Riwayat Kunjungan
                </h2>
            </div>
            <div class="p-6">
                @if($detailedRecords && count($detailedRecords) > 0 && collect($detailedRecords)->pluck('visits')->flatten()->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-800">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                                        Tanggal
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                                        Nomor Registrasi
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                                        Diagnosa
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                                        Petugas
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($detailedRecords as $familyRecord)
                                    @foreach($familyRecord['visits'] as $visit)
                                        <tr class="hover:bg-blue-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                                {{ \Carbon\Carbon::parse($visit->tanggal_periksa)->format('d-m-Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200 font-medium">
                                                @php
                                                    // Generate nomor registrasi yang konsisten dengan KunjunganController
                                                    $bulan = \Carbon\Carbon::parse($visit->tanggal_periksa)->format('m');
                                                    $tahun = \Carbon\Carbon::parse($visit->tanggal_periksa)->format('Y');
                                                    
                                                    // Gabungkan rekam medis reguler dan emergency untuk hitungan global
                                                    $allReguler = \App\Models\RekamMedis::where('tanggal_periksa', '>=', '2025-08-01')
                                                        ->orderBy('tanggal_periksa')
                                                        ->orderBy('waktu_periksa')
                                                        ->get()
                                                        ->map(function($record) {
                                                            return [
                                                                'id' => $record->id_rekam,
                                                                'tanggal' => $record->tanggal_periksa,
                                                                'waktu' => $record->waktu_periksa,
                                                                'tipe' => 'reguler'
                                                            ];
                                                        });
                                                        
                                                    $allEmergency = \App\Models\RekamMedisEmergency::where('tanggal_periksa', '>=', '2025-08-01')
                                                        ->orderBy('tanggal_periksa')
                                                        ->orderBy('waktu_periksa')
                                                        ->get()
                                                        ->map(function($record) {
                                                            return [
                                                                'id' => $record->id_emergency,
                                                                'tanggal' => $record->tanggal_periksa,
                                                                'waktu' => $record->waktu_periksa,
                                                                'tipe' => 'emergency'
                                                            ];
                                                        });
                                                    
                                                    // Gabungkan dan urutkan semua record
                                                    $allRecords = $allReguler->concat($allEmergency)
                                                        ->sortBy(function($record) {
                                                            return $record['tanggal'].' '.$record['waktu'];
                                                        })
                                                        ->values();
                                                    
                                                    // Cari posisi record saat ini
                                                    $visitCount = 0;
                                                    foreach ($allRecords as $index => $record) {
                                                        if ($record['id'] == $visit->id_rekam && $record['tipe'] === 'reguler') {
                                                            $visitCount = $index + 1;
                                                            break;
                                                        }
                                                    }
                                                    
                                                    // Format nomor registrasi dengan 4 digit leading zeros
                                                    $formattedVisitCount = str_pad($visitCount, 4, '0', STR_PAD_LEFT);
                                                    $nomorRegistrasi = "{$formattedVisitCount}/NDL/BJM/{$bulan}/{$tahun}";
                                                @endphp
                                                <a href="{{ route('rekam-medis.show', $visit->id_rekam) }}" class="text-blue-600 hover:text-blue-900 hover:underline">
                                                    {{ $nomorRegistrasi }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">
                                                @if($visit->complaints && $visit->complaints->count() > 0)
                                                    @foreach($visit->complaints->take(2) as $complaint)
                                                        <span class="inline-block">{{ $complaint->nama_diagnosa ?? 'Tidak ada diagnosa' }}</span>
                                                        @if(!$loop->last), @endif
                                                    @endforeach
                                                    @if($visit->complaints->count() > 2)
                                                        <span class="text-gray-500">... (+{{ $visit->complaints->count() - 2 }} lainnya)</span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-500">Tidak ada diagnosa</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                                <div class="flex items-center">
                                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                                                        <span class="text-xs font-medium text-gray-600">
                                                            {{ strtoupper(substr($visit->petugas ?? '-', 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    {{ $visit->petugas ?? '-' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <a href="{{ route('rekam-medis.show', $visit->id_rekam) }}"
                                                   class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-4 flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Menampilkan <span class="font-medium">1</span> hingga <span class="font-medium">{{ collect($detailedRecords)->pluck('visits')->flatten()->count() }}</span> dari <span class="font-medium">{{ collect($detailedRecords)->pluck('visits')->flatten()->count() }}</span> hasil
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 disabled:opacity-50" disabled>
                                Previous
                            </button>
                            <button class="px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                1
                            </button>
                            <button class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 disabled:opacity-50" disabled>
                                Next
                            </button>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-lg font-medium">Tidak ada riwayat kunjungan</p>
                        <p class="text-sm mt-1">Belum ada kunjungan medis untuk karyawan ini</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Section 3: Medical Archive Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                    Arsip Medis
                </h2>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                                    Kategori
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                                    Total Dokumen
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr class="hover:bg-purple-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Surat Rekomendasi Medis
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                        {{ $suratRekomendasiCount ?? 0 }} dokumen
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <a href="{{ route('medical-archives.surat-rekomendasi-medis', $detailedRecords[0]['family_member']->id_karyawan ?? $employeeInfo->id_karyawan) }}"
                                       class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                        </svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            <tr class="hover:bg-purple-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                        Medical Check Up
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                        {{ $medicalCheckUpCount ?? 0 }} dokumen
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <a href="{{ route('medical-archives.medical-check-up', $detailedRecords[0]['family_member']->id_karyawan ?? $employeeInfo->id_karyawan) }}"
                                       class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                        </svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State Script -->
    <script>
        // Show loading state for API calls
        function showLoadingState() {
            const loadingOverlay = document.createElement('div');
            loadingOverlay.id = 'loading-overlay';
            loadingOverlay.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50';
            loadingOverlay.innerHTML = `
                <div class="bg-white p-6 rounded-lg shadow-xl">
                    <div class="flex items-center">
                        <div class="spinner-border animate-spin inline-block w-8 h-8 border-4 rounded-full border-blue-600 border-t-transparent" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <span class="ml-3 text-gray-700">Memuat data...</span>
                    </div>
                </div>
            `;
            document.body.appendChild(loadingOverlay);
        }

        // Hide loading state
        function hideLoadingState() {
            const loadingOverlay = document.getElementById('loading-overlay');
            if (loadingOverlay) {
                loadingOverlay.remove();
            }
        }

        // Add click handlers for detail buttons
        document.addEventListener('DOMContentLoaded', function() {
            const detailButtons = document.querySelectorAll('button[class*="bg-blue-600"]');
            detailButtons.forEach(button => {
                button.addEventListener('click', function() {
                    showLoadingState();
                    // Simulate API call
                    setTimeout(() => {
                        hideLoadingState();
                        // Here you would typically navigate to the detail page
                        console.log('Navigate to detail page');
                    }, 1000);
                });
            });
        });
    </script>
@endsection