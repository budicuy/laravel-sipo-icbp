@extends('layouts.app')

@section('page-title', 'Daftar Rekam Medis')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Daftar Rekam Medis</h1>
                    <p class="text-gray-600 mt-1">Kelola data rekam medis pasien</p>
                </div>
            </div>
            <div class="flex gap-3">
                <button onclick="showAddModal()" class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-3 rounded-lg font-medium shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Rekam Medis
                </button>
                <button type="button" onclick="openImportModal()" id="import-excel-btn" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-lg font-medium shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Import Excel
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4 mb-6">
        <form action="{{ route('rekam-medis.index') }}" method="GET">
            <div class="flex flex-wrap items-center gap-3">
                <!-- Dari Tanggal -->
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Dari:</label>
                    <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <!-- Sampai Tanggal -->
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Sampai:</label>
                    <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <!-- Search -->
                <div class="flex items-center gap-2 flex-1 min-w-[200px]">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, NIK, atau No RM..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <!-- Status Filter -->
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Status:</label>
                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Semua</option>
                        <option value="On Progress" {{ request('status') == 'On Progress' ? 'selected' : '' }}>On Progress</option>
                        <option value="Close" {{ request('status') == 'Close' ? 'selected' : '' }}>Close</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-medium px-4 py-2 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('rekam-medis.index') }}" class="bg-white hover:bg-gray-50 border-2 border-gray-300 text-gray-700 font-medium px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button type="button" onclick="switchTab('regular')" id="regular-tab" class="tab-button py-4 px-6 border-b-2 font-medium text-sm rounded-tl-lg focus:outline-none transition-colors border-green-500 text-green-600 bg-green-50">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Rekam Medis Reguler
                    </div>
                </button>
                <button type="button" onclick="switchTab('emergency')" id="emergency-tab" class="tab-button py-4 px-6 border-b-2 font-medium text-sm rounded-tr-lg focus:outline-none transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Rekam Medis Emergency
                    </div>
                </button>
            </nav>
        </div>
    </div>

    <!-- Tab Content -->
    <!-- Regular Tab -->
    <div id="regular-content" class="tab-content">
        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <!-- Table Header -->
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Data Rekam Medis Reguler
                    </h2>
                    <form action="{{ route('rekam-medis.index') }}" method="GET" class="flex items-center gap-2">
                        <input type="hidden" name="tab" value="regular">
                        <label class="text-white text-sm">Show</label>
                        <select name="per_page" onchange="this.form.submit()" class="px-3 py-1.5 bg-white border border-white rounded-lg text-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-white">
                            <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                        </select>
                        <span class="text-white text-sm">entries</span>
                    </form>
                </div>
            </div>

            <!-- Table Content -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-800">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Tgl / Hari</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Waktu</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">NIK</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Nama Karyawan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Kode RM</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Nama Pasien</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Diagnosa</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Terapi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Obat</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Anamnesa</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Detail</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Petugas Medis</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($rekamMedis as $index => $rm)
                        <tr class="hover:bg-green-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                {{ $rekamMedis->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                {{ is_string($rm->tanggal_periksa) ? $rm->tanggal_periksa : $rm->tanggal_periksa->format('d-m-Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                {{ $rm->waktu_periksa ? (is_string($rm->waktu_periksa) ? $rm->waktu_periksa : $rm->waktu_periksa->format('H:i')) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                {{ $rm->keluarga->karyawan->nik_karyawan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                {{ $rm->keluarga->karyawan->nama_karyawan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200 font-medium">
                                {{ ($rm->keluarga->karyawan->nik_karyawan ?? '') . '-' . ($rm->keluarga->kode_hubungan ?? '') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200 font-medium">
                                {{ $rm->keluarga->nama_keluarga ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">
                                @php
                                    $uniqueDiagnosa = [];
                                    foreach($rm->keluhans as $keluhan) {
                                        $diagnosaName = $keluhan->diagnosa->nama_diagnosa ?? '-';
                                        if (!in_array($diagnosaName, $uniqueDiagnosa)) {
                                            $uniqueDiagnosa[] = $diagnosaName;
                                        }
                                    }
                                @endphp
                                {{ implode(', ', $uniqueDiagnosa) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">
                                @php
                                    $uniqueTerapi = [];
                                    foreach($rm->keluhans as $keluhan) {
                                        if (!in_array($keluhan->terapi, $uniqueTerapi)) {
                                            $uniqueTerapi[] = $keluhan->terapi;
                                        }
                                    }
                                @endphp
                                @foreach($uniqueTerapi as $terapi)
                                    <span class="px-2 py-1
                                        @if($terapi == 'Obat') bg-purple-100 text-purple-800
                                        @elseif($terapi == 'Lab') bg-orange-100 text-orange-800
                                        @else bg-green-100 text-green-800
                                        @endif
                                        rounded-full text-xs font-medium mr-1">
                                        {{ $terapi }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">
                                @php
                                    $uniqueObat = [];
                                    foreach($rm->keluhans as $keluhan) {
                                        if($keluhan->obat) {
                                            $obatName = $keluhan->obat->nama_obat;
                                            if (!in_array($obatName, $uniqueObat)) {
                                                $uniqueObat[] = $obatName;
                                            }
                                        }
                                    }
                                @endphp
                                {{ !empty($uniqueObat) ? implode(', ', $uniqueObat) : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">
                                @php
                                    $keteranganText = '';
                                    foreach($rm->keluhans as $keluhan) {
                                        if(!empty($keluhan->keterangan)) {
                                            $keteranganText = $keluhan->keterangan;
                                            break;
                                        }
                                    }
                                @endphp
                                {{ !empty($keteranganText) ? Str::limit($keteranganText, 50) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                <div class="status-dropdown" data-id="{{ $rm->id_rekam }}">
                                    <select class="status-select px-3 py-1 rounded-full text-xs font-medium border-0 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500
                                        @if($rm->status == 'On Progress') bg-yellow-100 text-yellow-800
                                        @elseif($rm->status == 'Close') bg-green-100 text-green-800
                                        @endif"
                                        data-id="{{ $rm->id_rekam }}"
                                        data-current-status="{{ $rm->status ?? 'On Progress' }}">
                                        <option value="On Progress" {{ ($rm->status ?? 'On Progress') == 'On Progress' ? 'selected' : '' }}>On Progress</option>
                                        <option value="Close" {{ ($rm->status ?? 'On Progress') == 'Close' ? 'selected' : '' }}>Close</option>
                                    </select>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center border-r border-gray-200">
                                <a href="{{ route('rekam-medis.show', $rm->id_rekam) }}" class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:shadow-lg transition-all inline-block">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                {{ $rm->user->nama_lengkap ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('rekam-medis.edit', $rm->id_rekam) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white p-1.5 rounded">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button type="button" class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded delete-btn"
                                            data-id="{{ $rm->id_rekam }}"
                                            data-nama="{{ $rm->keluarga->nama_keluarga ?? '' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="16" class="px-6 py-8 text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-lg font-medium">Tidak ada data rekam medis</p>
                                <p class="text-sm mt-1">Silakan tambahkan data rekam medis baru</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom Pagination -->
            @if($rekamMedis->hasPages())
            <div class="px-6 py-5 border-t border-gray-200 bg-white">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-sm text-gray-600">
                        Halaman <span class="font-semibold text-gray-900">{{ $rekamMedis->currentPage() }}</span>
                        dari <span class="font-semibold text-gray-900">{{ $rekamMedis->lastPage() }}</span>
                        <span class="mx-2 text-gray-400">•</span>
                        Total <span class="font-semibold text-gray-900">{{ $rekamMedis->total() }}</span> data
                    </div>

                    <nav class="flex items-center gap-2" role="navigation">
                        @if($rekamMedis->onFirstPage())
                            <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                                </svg>
                            </span>
                        @else
                            <a href="{{ $rekamMedis->appends(request()->except('page'))->url(1) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-green-50 hover:border-green-400 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                                </svg>
                            </a>
                        @endif

                        @if($rekamMedis->onFirstPage())
                            <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>
                        @else
                            <a href="{{ $rekamMedis->appends(request()->except('page'))->previousPageUrl() }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-green-50 hover:border-green-400 transition-all">Previous</a>
                        @endif

                        <div class="flex items-center gap-1">
                            @php
                                $start = max($rekamMedis->currentPage() - 2, 1);
                                $end = min($rekamMedis->currentPage() + 2, $rekamMedis->lastPage());
                            @endphp

                            @if($start > 1)
                                <a href="{{ $rekamMedis->appends(request()->except('page'))->url(1) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-green-50 hover:border-green-400 transition-all">1</a>
                                @if($start > 2)
                                    <span class="px-2 text-gray-500">...</span>
                                @endif
                            @endif

                            @for($i = $start; $i <= $end; $i++)
                                @if($i == $rekamMedis->currentPage())
                                    <span class="px-3 py-2 text-sm font-bold text-white bg-gradient-to-r from-green-600 to-emerald-600 rounded-lg shadow-md">{{ $i }}</span>
                                @else
                                    <a href="{{ $rekamMedis->appends(request()->except('page'))->url($i) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-green-50 hover:border-green-400 transition-all">{{ $i }}</a>
                                @endif
                            @endfor

                            @if($end < $rekamMedis->lastPage())
                                @if($end < $rekamMedis->lastPage() - 1)
                                    <span class="px-2 text-gray-500">...</span>
                                @endif
                                <a href="{{ $rekamMedis->appends(request()->except('page'))->url($rekamMedis->lastPage()) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-green-50 hover:border-green-400 transition-all">{{ $rekamMedis->lastPage() }}</a>
                            @endif
                        </div>

                        @if($rekamMedis->hasMorePages())
                            <a href="{{ $rekamMedis->appends(request()->except('page'))->nextPageUrl() }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-green-50 hover:border-green-400 transition-all">Next</a>
                        @else
                            <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>
                        @endif

                        @if($rekamMedis->currentPage() == $rekamMedis->lastPage())
                            <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                </svg>
                            </span>
                        @else
                            <a href="{{ $rekamMedis->appends(request()->except('page'))->url($rekamMedis->lastPage()) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-green-50 hover:border-green-400 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @endif
                    </nav>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Emergency Tab -->
    <div id="emergency-content" class="tab-content hidden">
        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <!-- Table Header -->
            <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Data Rekam Medis Emergency
                    </h2>
                    <form action="{{ route('rekam-medis.index') }}" method="GET" class="flex items-center gap-2">
                        <input type="hidden" name="tab" value="emergency">
                        <label class="text-white text-sm">Show</label>
                        <select name="per_page" onchange="this.form.submit()" class="px-3 py-1.5 bg-white border border-white rounded-lg text-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-white">
                            <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                        </select>
                        <span class="text-white text-sm">entries</span>
                    </form>
                </div>
            </div>

            <!-- Table Content -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-800">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Tgl / Hari</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Waktu</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">NIK</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Nama Karyawan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Kode RM</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Nama Pasien</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Diagnosa</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Terapi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Obat</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Anamnesa</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Detail</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Petugas Medis</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($rekamMedisEmergency as $index => $rm)
                        <tr class="hover:bg-red-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                {{ $rekamMedisEmergency->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                {{ is_string($rm->tanggal_periksa) ? $rm->tanggal_periksa : $rm->tanggal_periksa->format('d-m-Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                {{ $rm->waktu_periksa ? $rm->waktu_periksa->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                {{ $rm->externalEmployee->nik_employee ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200 font-medium">
                                {{ $rm->externalEmployee->nama_employee ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200 font-medium">
                                {{ $rm->externalEmployee->kode_rm ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                {{ $rm->externalEmployee->nama_employee ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">
                                @php
                                    $uniqueDiagnosaEmergency = [];
                                    foreach($rm->keluhans as $keluhan) {
                                        if($keluhan->diagnosaEmergency) {
                                            $diagnosaName = $keluhan->diagnosaEmergency->nama_diagnosa_emergency;
                                            if (!in_array($diagnosaName, $uniqueDiagnosaEmergency)) {
                                                $uniqueDiagnosaEmergency[] = $diagnosaName;
                                            }
                                        }
                                    }
                                @endphp
                                {{ !empty($uniqueDiagnosaEmergency) ? implode(', ', $uniqueDiagnosaEmergency) : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">
                                @php
                                    $uniqueTerapi = [];
                                    foreach($rm->keluhans as $keluhan) {
                                        if (!in_array($keluhan->terapi, $uniqueTerapi)) {
                                            $uniqueTerapi[] = $keluhan->terapi;
                                        }
                                    }
                                @endphp
                                @foreach($uniqueTerapi as $terapi)
                                    <span class="px-2 py-1
                                        @if($terapi == 'Obat') bg-purple-100 text-purple-800
                                        @elseif($terapi == 'Lab') bg-orange-100 text-orange-800
                                        @else bg-green-100 text-green-800
                                        @endif
                                        rounded-full text-xs font-medium mr-1">
                                        {{ $terapi }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">
                                @php
                                    $uniqueObat = [];
                                    foreach($rm->keluhans as $keluhan) {
                                        if($keluhan->obat) {
                                            $obatName = $keluhan->obat->nama_obat;
                                            if (!in_array($obatName, $uniqueObat)) {
                                                $uniqueObat[] = $obatName;
                                            }
                                        }
                                    }
                                @endphp
                                {{ !empty($uniqueObat) ? implode(', ', $uniqueObat) : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">
                                @php
                                    $keteranganText = '';
                                    foreach($rm->keluhans as $keluhan) {
                                        if(!empty($keluhan->keterangan)) {
                                            $keteranganText = $keluhan->keterangan;
                                            break;
                                        }
                                    }
                                @endphp
                                {{ !empty($keteranganText) ? Str::limit($keteranganText, 50) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                <div class="status-dropdown" data-id="{{ $rm->id_emergency }}">
                                    <select class="status-select-emergency px-3 py-1 rounded-full text-xs font-medium border-0 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500
                                        @if($rm->status == 'On Progress') bg-yellow-100 text-yellow-800
                                        @elseif($rm->status == 'Close') bg-green-100 text-green-800
                                        @endif"
                                        data-id="{{ $rm->id_emergency }}"
                                        data-current-status="{{ $rm->status }}">
                                        <option value="On Progress" {{ $rm->status == 'On Progress' ? 'selected' : '' }}>On Progress</option>
                                        <option value="Close" {{ $rm->status == 'Close' ? 'selected' : '' }}>Close</option>
                                    </select>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center border-r border-gray-200">
                                <a href="{{ route('rekam-medis-emergency.show', $rm->id_emergency) }}" class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:shadow-lg transition-all inline-block">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                {{ $rm->user->nama_lengkap ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('rekam-medis-emergency.edit', $rm->id_emergency) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white p-1.5 rounded">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button type="button" class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded delete-btn-emergency"
                                            data-id="{{ $rm->id_emergency }}"
                                            data-nama="{{ $rm->externalEmployee->nama_employee ?? 'Pasien' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="16" class="px-6 py-8 text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="text-lg font-medium">Tidak ada data rekam medis emergency</p>
                                <p class="text-sm mt-1">Silakan tambahkan data rekam medis emergency baru</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom Pagination -->
            @if($rekamMedisEmergency->hasPages())
            <div class="px-6 py-5 border-t border-gray-200 bg-white">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-sm text-gray-600">
                        Halaman <span class="font-semibold text-gray-900">{{ $rekamMedisEmergency->currentPage() }}</span>
                        dari <span class="font-semibold text-gray-900">{{ $rekamMedisEmergency->lastPage() }}</span>
                        <span class="mx-2 text-gray-400">•</span>
                        Total <span class="font-semibold text-gray-900">{{ $rekamMedisEmergency->total() }}</span> data
                    </div>

                    <nav class="flex items-center gap-2" role="navigation">
                        @if($rekamMedisEmergency->onFirstPage())
                            <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                                </svg>
                            </span>
                        @else
                            <a href="{{ $rekamMedisEmergency->appends(request()->except('page'))->url(1) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-red-50 hover:border-red-400 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                                </svg>
                            </a>
                        @endif

                        @if($rekamMedisEmergency->onFirstPage())
                            <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>
                        @else
                            <a href="{{ $rekamMedisEmergency->appends(request()->except('page'))->previousPageUrl() }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-red-50 hover:border-red-400 transition-all">Previous</a>
                        @endif

                        <div class="flex items-center gap-1">
                            @php
                                $start = max($rekamMedisEmergency->currentPage() - 2, 1);
                                $end = min($rekamMedisEmergency->currentPage() + 2, $rekamMedisEmergency->lastPage());
                            @endphp

                            @if($start > 1)
                                <a href="{{ $rekamMedisEmergency->appends(request()->except('page'))->url(1) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-red-50 hover:border-red-400 transition-all">1</a>
                                @if($start > 2)
                                    <span class="px-2 text-gray-500">...</span>
                                @endif
                            @endif

                            @for($i = $start; $i <= $end; $i++)
                                @if($i == $rekamMedisEmergency->currentPage())
                                    <span class="px-3 py-2 text-sm font-bold text-white bg-gradient-to-r from-red-600 to-pink-600 rounded-lg shadow-md">{{ $i }}</span>
                                @else
                                    <a href="{{ $rekamMedisEmergency->appends(request()->except('page'))->url($i) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-red-50 hover:border-red-400 transition-all">{{ $i }}</a>
                                @endif
                            @endfor

                            @if($end < $rekamMedisEmergency->lastPage())
                                @if($end < $rekamMedisEmergency->lastPage() - 1)
                                    <span class="px-2 text-gray-500">...</span>
                                @endif
                                <a href="{{ $rekamMedisEmergency->appends(request()->except('page'))->url($rekamMedisEmergency->lastPage()) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-red-50 hover:border-red-400 transition-all">{{ $rekamMedisEmergency->lastPage() }}</a>
                            @endif
                        </div>

                        @if($rekamMedisEmergency->hasMorePages())
                            <a href="{{ $rekamMedisEmergency->appends(request()->except('page'))->nextPageUrl() }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-red-50 hover:border-red-400 transition-all">Next</a>
                        @else
                            <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>
                        @endif

                        @if($rekamMedisEmergency->currentPage() == $rekamMedisEmergency->lastPage())
                            <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                </svg>
                            </span>
                        @else
                            <a href="{{ $rekamMedisEmergency->appends(request()->except('page'))->url($rekamMedisEmergency->lastPage()) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-red-50 hover:border-red-400 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @endif
                    </nav>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tab based on URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam === 'emergency') {
        switchTab('emergency');
    }

    // Handle delete confirmation with SweetAlert for regular records
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');

            Swal.fire({
                title: 'Konfirmasi Hapus Data',
                html: `Apakah Anda yakin ingin menghapus data rekam medis untuk pasien <strong>${nama}</strong>?<br><small class="text-red-500">Tindakan ini tidak dapat dibatalkan.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                showLoaderOnConfirm: true,
                preConfirm: function () {
                    return new Promise(function(resolve) {
                        // Create form element
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `{{ route('rekam-medis.destroy', ':id') }}`.replace(':id', id);

                        // Add CSRF token
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);

                        // Add DELETE method
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        form.appendChild(methodInput);

                        // Submit form
                        document.body.appendChild(form);
                        form.submit();
                    });
                }
            });
        });
    });

    // Handle delete confirmation with SweetAlert for emergency records
    document.querySelectorAll('.delete-btn-emergency').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');

            Swal.fire({
                title: 'Konfirmasi Hapus Data',
                html: `Apakah Anda yakin ingin menghapus data rekam medis emergency untuk pasien <strong>${nama}</strong>?<br><small class="text-red-500">Tindakan ini tidak dapat dibatalkan.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                showLoaderOnConfirm: true,
                preConfirm: function () {
                    return new Promise(function(resolve) {
                        // Create form element
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `{{ route('rekam-medis-emergency.destroy', ':id') }}`.replace(':id', id);

                        // Add CSRF token
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);

                        // Add DELETE method
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        form.appendChild(methodInput);

                        // Submit form
                        document.body.appendChild(form);
                        form.submit();
                    });
                }
            });
        });
    });

    // Handle status change for regular records
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const newStatus = this.value;
            const currentStatus = this.getAttribute('data-current-status');

            // Show loading state
            this.disabled = true;
            this.classList.add('opacity-50');

            // Send AJAX request
            fetch(`{{ route('rekam-medis.updateStatus', ':id') }}`.replace(':id', id), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update status attribute
                    this.setAttribute('data-current-status', newStatus);

                    // Update styling based on new status
                    this.classList.remove('bg-yellow-100', 'text-yellow-800', 'bg-green-100', 'text-green-800');

                    if (newStatus === 'On Progress') {
                        this.classList.add('bg-yellow-100', 'text-yellow-800');
                    } else if (newStatus === 'Close') {
                        this.classList.add('bg-green-100', 'text-green-800');
                    }

                    // Show success notification
                    showNotification('Status berhasil diperbarui', 'success');
                } else {
                    // Revert to original status
                    this.value = currentStatus;
                    showNotification(data.message || 'Gagal memperbarui status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert to original status
                this.value = currentStatus;
                showNotification('Terjadi kesalahan saat memperbarui status', 'error');
            })
            .finally(() => {
                // Remove loading state
                this.disabled = false;
                this.classList.remove('opacity-50');
            });
        });
    });

    // Handle status change for emergency records
    document.querySelectorAll('.status-select-emergency').forEach(select => {
        select.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const newStatus = this.value;
            const currentStatus = this.getAttribute('data-current-status');

            // Show loading state
            this.disabled = true;
            this.classList.add('opacity-50');

            // Send AJAX request
            fetch(`{{ route('rekam-medis-emergency.updateStatus', ':id') }}`.replace(':id', id), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update status attribute
                    this.setAttribute('data-current-status', newStatus);

                    // Update styling based on new status
                    this.classList.remove('bg-yellow-100', 'text-yellow-800', 'bg-green-100', 'text-green-800');

                    if (newStatus === 'On Progress') {
                        this.classList.add('bg-yellow-100', 'text-yellow-800');
                    } else if (newStatus === 'Close') {
                        this.classList.add('bg-green-100', 'text-green-800');
                    }

                    // Show success notification
                    showNotification('Status berhasil diperbarui', 'success');
                } else {
                    // Revert to original status
                    this.value = currentStatus;
                    showNotification(data.message || 'Gagal memperbarui status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert to original status
                this.value = currentStatus;
                showNotification('Terjadi kesalahan saat memperbarui status', 'error');
            })
            .finally(() => {
                // Remove loading state
                this.disabled = false;
                this.classList.remove('opacity-50');
            });
        });
    });

    // Function to show notification
    function showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;

        // Set styling based on type
        if (type === 'success') {
            notification.classList.add('bg-green-500', 'text-white');
        } else if (type === 'error') {
            notification.classList.add('bg-red-500', 'text-white');
        }

        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    ${type === 'success'
                        ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
                        : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>'
                    }
                </svg>
                <span>${message}</span>
            </div>
        `;

        // Add to document
        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
            notification.classList.add('translate-x-0');
        }, 100);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
});

// Tab switching function
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-green-500', 'text-green-600', 'bg-green-50', 'border-red-500', 'text-red-600', 'bg-red-50');
        button.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab content
    document.getElementById(tabName + '-content').classList.remove('hidden');

    // Set active state for selected tab
    const activeTab = document.getElementById(tabName + '-tab');
    if (tabName === 'regular') {
        activeTab.classList.remove('border-transparent', 'text-gray-500');
        activeTab.classList.add('border-green-500', 'text-green-600', 'bg-green-50');
        // Show import button for regular tab
        document.getElementById('import-excel-btn').style.display = 'flex';
    } else if (tabName === 'emergency') {
        activeTab.classList.remove('border-transparent', 'text-gray-500');
        activeTab.classList.add('border-red-500', 'text-red-600', 'bg-red-50');
        // Hide import button for emergency tab
        document.getElementById('import-excel-btn').style.display = 'none';
    }

    // Update URL parameter
    const url = new URL(window.location);
    if (tabName === 'regular') {
        url.searchParams.delete('tab');
    } else {
        url.searchParams.set('tab', tabName);
    }
    window.history.replaceState({}, '', url);
}

// Show add modal for selecting record type
function showAddModal() {
    Swal.fire({
        title: 'Pilih Jenis Rekam Medis',
        html: `
            <div class="text-left">
                <p class="mb-4 text-gray-600">Silakan pilih jenis rekam medis yang ingin ditambahkan:</p>
                <div class="space-y-3">
                    <button onclick="window.location.href='{{ route('rekam-medis.create') }}'" 
                            class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-3 rounded-lg font-medium shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Rekam Medis Reguler
                    </button>
                    <button onclick="window.location.href='{{ route('token-emergency.validate') }}'" 
                            class="w-full bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white px-6 py-3 rounded-lg font-medium shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Rekam Medis Emergency
                    </button>
                </div>
            </div>
        `,
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: 'Batal',
        cancelButtonColor: '#6b7280',
        width: '500px'
    });
}

// Import Excel Modal Functions
function openImportModal() {
    Swal.fire({
        title: 'Import Data Rekam Medis dari Excel',
        html: `
            <div class="text-left">
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="font-semibold text-blue-900 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Informasi Penting
                    </h4>
                    <ul class="text-sm text-blue-800 space-y-1 ml-7">
                        <li>• Format file: Excel (.xlsx atau .xls)</li>
                        <li>• Maksimal ukuran file: 5MB</li>
                        <li>• Download template terlebih dahulu</li>
                        <li>• Format Tanggal: DD/MM/YYYY (contoh: 01/08/2025)</li>
                        <li>• NIK Karyawan harus ada di tabel karyawan</li>
                        <li>• Kode RM format: NIK-KodeHubungan</li>
                        <li>• Status: "Close" atau "On Progress"</li>
                        <li>• Obat 1-5: isi dengan nama obat atau "-" jika tidak ada</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <a href="{{ route('rekam-medis.template') }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all w-full justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Template Excel
                    </a>
                </div>

                <form id="importForm" action="{{ route('rekam-medis.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel</label>
                        <input type="file"
                               name="file"
                               id="importFile"
                               accept=".xlsx,.xls"
                               required
                               class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2">
                        <p class="mt-1 text-xs text-gray-500">File Excel (.xlsx atau .xls), maksimal 5MB</p>
                    </div>
                </form>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload & Import',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        width: '600px',
        customClass: {
            confirmButton: 'px-5 py-2.5 rounded-lg font-medium',
            cancelButton: 'px-5 py-2.5 rounded-lg font-medium'
        },
        preConfirm: () => {
            const fileInput = document.getElementById('importFile');
            if (!fileInput.files || fileInput.files.length === 0) {
                Swal.showValidationMessage('Silakan pilih file Excel terlebih dahulu');
                return false;
            }

            const file = fileInput.files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB

            if (file.size > maxSize) {
                Swal.showValidationMessage('Ukuran file maksimal 5MB');
                return false;
            }

            const allowedExtensions = ['xlsx', 'xls'];
            const fileExtension = file.name.split('.').pop().toLowerCase();

            if (!allowedExtensions.includes(fileExtension)) {
                Swal.showValidationMessage('Format file harus .xlsx atau .xls');
                return false;
            }

            return true;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const fileInput = document.getElementById('importFile');
            const file = fileInput.files[0];

            // Create FormData
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            // Show loading
            Swal.fire({
                title: 'Sedang Mengimport...',
                html: 'Mohon tunggu, data sedang diproses',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Submit via AJAX
            fetch('{{ route("rekam-medis.import") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    html: data.message,
                    confirmButtonColor: '#3b82f6'
                }).then(() => {
                    // Reload page to show new data
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000); // 2-second delay
                });
            })
            .catch(error => {
                console.error('Import error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Import',
                    html: error.message || 'Terjadi kesalahan saat mengimport data',
                    confirmButtonColor: '#3b82f6'
                });
            });
        }
    });
}
</script>
@endpush
@endsection
