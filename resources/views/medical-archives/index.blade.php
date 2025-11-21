@extends('layouts.app')

@section('page-title', 'Medical Archives')

@push('styles')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <div class="flex items-center gap-3">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Medical Archives</h1>
                    <p class="text-gray-600 mt-1">Kelola data rekam medis karyawan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <h3 class="text-sm font-semibold text-gray-800">Filter Medical Archives</h3>
        </div>

        <form action="{{ route('medical-archives.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Data</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-white"
                            placeholder="Cari NIK, nama karyawan, atau nama pasien...">
                    </div>
                </div>

                <!-- Department Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Departemen</label>
                    <div class="relative">
                        <select name="department"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none bg-white pr-10">
                            <option value="">Semua Departemen</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id_departemen }}" {{ request('department') == $department->id_departemen ? 'selected' : '' }}>
                                    {{ $department->nama_departemen }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="relative">
                        <select name="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none bg-white pr-10">
                            <option value="">Semua Status</option>
                            @foreach($statusOptions as $key => $value)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Year Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                    <div class="relative">
                        <select name="year"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none bg-white pr-10">
                            <option value="">Semua Tahun</option>
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Buttons in separate row, aligned with filters -->
            <div class="flex gap-3 justify-end">
                <button type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Filter
                </button>
                <a href="{{ route('medical-archives.index') }}"
                    class="px-6 py-2.5 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Charts Section -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
                <h3 class="text-lg font-semibold text-gray-800">Grafik Statistik Kesehatan Karyawan</h3>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Chart Kondisi Kesehatan -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-lg p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span style="display: inline-block; width: 16px; height: 16px; background-color: #8B5CF6; border-radius: 50%; box-shadow: 0 1px 2px rgba(0,0,0,0.1);"></span>
                        <h4 class="text-sm font-semibold text-gray-700">Gangguan Kesehatan</h4>
                    </div>
                    <span class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200">Total Kasus</span>
                </div>
                <div style="height: 250px;">
                    <canvas id="chartKondisiKesehatan"></canvas>
                </div>
            </div>

            <!-- Chart Keterangan BMI -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-blue-500 rounded-full shadow-sm"></div>
                        <h4 class="text-sm font-semibold text-gray-700">BMI Category</h4>
                    </div>
                    <span class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200">Distribusi BMI</span>
                </div>
                <div style="height: 250px;">
                    <canvas id="chartKeteranganBmi"></canvas>
                </div>
            </div>

            <!-- Chart Catatan -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-green-500 rounded-full shadow-sm"></div>
                        <h4 class="text-sm font-semibold text-gray-700">Status Kesehatan</h4>
                    </div>
                    <span class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200">Status Fit</span>
                </div>
                <div style="height: 250px;">
                    <canvas id="chartCatatan"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Table Header -->
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Data Medical Archives Karyawan
                </h2>
                <div class="flex items-center gap-3">
                    <form action="{{ route('medical-archives.index') }}" method="GET" class="flex items-center gap-2">
                        @foreach(request()->only(['q', 'department', 'status', 'year']) as $key => $value)
                            @if($value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
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
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                            No
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                            NIK
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                            Nama Karyawan
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                            Departemen
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                            Periode
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                            BMI
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                            BMI Category
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                            Gangguan Kesehatan
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                            Status Kesehatan
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($medicalArchives as $index => $record)
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                {{ $medicalArchives->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200 font-medium">
                                {{ $record['nik_karyawan'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200 font-medium">
                                {{ $record['nama_karyawan'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                {{ $record['nama_departemen'] ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                @if($record['periode_terakhir'])
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-lg text-xs font-medium">
                                        {{ $record['periode_terakhir'] }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200 font-medium">
                                {{ $record['bmi'] ? number_format($record['bmi'], 2) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                @if($record['keterangan_bmi'])
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                    @if($record['keterangan_bmi'] == 'Normal') bg-green-100 text-green-800
                                    @elseif($record['keterangan_bmi'] == 'Underweight') bg-yellow-100 text-yellow-800
                                    @elseif($record['keterangan_bmi'] == 'Overweight') bg-orange-100 text-orange-800
                                    @elseif(str_contains($record['keterangan_bmi'], 'Obesitas')) bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                        {{ $record['keterangan_bmi'] }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">
                                @if(!empty($record['kondisi_kesehatan']))
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($record['kondisi_kesehatan'] as $kondisi)
                                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">
                                                {{ $kondisi }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                @if($record['catatan'])
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                    @if($record['catatan'] == 'Fit') bg-green-100 text-green-800
                                    @elseif($record['catatan'] == 'Fit dengan Catatan') bg-yellow-100 text-yellow-800
                                    @elseif($record['catatan'] == 'Fit dalam Pengawasan') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                        {{ $record['catatan'] }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="{{ route('medical-archives.show', $record['id_karyawan']) }}" class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:shadow-lg transition-all inline-block">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-8 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-lg font-medium">Tidak ada data medical archives</p>
                            <p class="text-sm mt-1">Silakan tambah data rekam medis untuk karyawan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Custom Pagination -->
        @if($medicalArchives->hasPages())
        <div class="px-6 py-5 border-t border-gray-200 bg-white">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-600">
                    Halaman <span class="font-semibold text-gray-900">{{ $medicalArchives->currentPage() }}</span>
                    dari <span class="font-semibold text-gray-900">{{ $medicalArchives->lastPage() }}</span>
                    <span class="mx-2 text-gray-400">•</span>
                    Total <span class="font-semibold text-gray-900">{{ $medicalArchives->total() }}</span> data
                </div>

                <nav class="flex items-center gap-2" role="navigation">
                    @if($medicalArchives->onFirstPage())
                        <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $medicalArchives->appends(request()->except('page'))->url(1) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    @if($medicalArchives->onFirstPage())
                        <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>
                    @else
                        <a href="{{ $medicalArchives->appends(request()->except('page'))->previousPageUrl() }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">Previous</a>
                    @endif

                    <div class="flex items-center gap-1">
                        @php
                            $start = max($medicalArchives->currentPage() - 2, 1);
                            $end = min($medicalArchives->currentPage() + 2, $medicalArchives->lastPage());
                        @endphp

                        @if($start > 1)
                            <a href="{{ $medicalRecords->appends(request()->except('page'))->url(1) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">1</a>
                            @if($start > 2)
                                <span class="px-2 text-gray-500">...</span>
                            @endif
                        @endif

                        @for($i = $start; $i <= $end; $i++)
                            @if($i == $medicalArchives->currentPage())
                                <span class="px-3 py-2 text-sm font-bold text-white bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-md">{{ $i }}</span>
                            @else
                                <a href="{{ $medicalArchives->appends(request()->except('page'))->url($i) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">{{ $i }}</a>
                            @endif
                        @endfor

                        @if($end < $medicalArchives->lastPage())
                            @if($end < $medicalArchives->lastPage() - 1)
                                <span class="px-2 text-gray-500">...</span>
                            @endif
                            <a href="{{ $medicalArchives->appends(request()->except('page'))->url($medicalArchives->lastPage()) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">{{ $medicalArchives->lastPage() }}</a>
                        @endif
                    </div>

                    @if($medicalArchives->hasMorePages())
                        <a href="{{ $medicalArchives->appends(request()->except('page'))->nextPageUrl() }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">Next</a>
                    @else
                        <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>
                    @endif

                    @if($medicalArchives->currentPage() == $medicalArchives->lastPage())
                        <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $medicalArchives->appends(request()->except('page'))->url($medicalArchives->lastPage()) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js is not loaded!');
            return;
        }
        
        const chartData = @json($chartData);
        
        // Color Palettes berdasarkan gambar yang diberikan (lebih vibrant)
        const gradients = {
            // Warna lebih vibrant untuk Kondisi Kesehatan
            health: [
                '#C084FC', '#A855F7', '#8B5CF6', '#7C3AED',
                '#FF9BCF', '#F472B6', '#EC4899', '#E879F9',
                '#60A5FA', '#3B82F6', '#2563EB', '#1D4ED8',
                '#22D3EE', '#06B6D4', '#0891B2', '#A78BFA',
                '#818CF8', '#6366F1', '#4F46E5', '#10B981'
            ],
            // BMI gradient dengan gradasi: Kuning → Biru/Hijau → Oren → Oren Kemerahan → Merah → Merah Menyala
            bmi: {
                'Underweight': '#FCD34D',      // Kuning (Yellow 400)
                'Normal': '#10B981',           // Hijau/Biru (Green 500)
                'Overweight': '#FB923C',       // Oren (Orange 400)
                'Obesitas Tk 1': '#F97316',    // Oren Kemerahan (Orange 600)
                'Obesitas Tk 2': '#EF4444',    // Merah (Red 500)
                'Obesitas Tk 3': '#DC2626'     // Merah Menyala (Red 600)
            },
            // Status Kesehatan: Hijau → Kuning → Merah dengan gradasi proporsional
            status: {
                'Fit': {
                    background: '#10B981',           // Green 500 (Hijau Medium)
                    hover: '#047857',                // Green 700 (Hijau Tua)
                    border: '#059669'                // Green 600
                },
                'Fit dengan Catatan': {
                    background: '#F59E0B',           // Amber 500 (Kuning Medium)
                    hover: '#D97706',                // Amber 600 (Kuning Sedikit Lebih Tua)
                    border: '#D97706'                // Amber 600
                },
                'Fit dalam Pengawasan': {
                    background: '#EF4444',           // Red 500 (Merah Medium)
                    hover: '#B91C1C',                // Red 700 (Merah Tua)
                    border: '#DC2626'                // Red 600
                }
            }
        };

        // Chart Kondisi Kesehatan - Pie Chart
        const ctxKondisiKesehatan = document.getElementById('chartKondisiKesehatan');
        if (ctxKondisiKesehatan) {
            const labels = Object.keys(chartData.kondisiKesehatan);
            
            const healthColors = [
                '#C084FC', '#FF9BCF', '#60A5FA', '#22D3EE',
                '#A855F7', '#F472B6', '#3B82F6', '#06B6D4',
                '#8B5CF6', '#EC4899', '#2563EB', '#0891B2',
                '#A78BFA', '#E879F9', '#818CF8', '#6366F1',
                '#7C3AED', '#4F46E5', '#10B981', '#1D4ED8'
            ];

            new Chart(ctxKondisiKesehatan.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: Object.values(chartData.kondisiKesehatan),
                        backgroundColor: labels.map((_, index) => {
                            return healthColors[index % healthColors.length];
                        }),
                        borderColor: '#FFFFFF',
                        borderWidth: 2,
                        hoverOffset: 15,
                        hoverBorderWidth: 4,
                        hoverBorderColor: '#F9FAFB'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                padding: 12,
                                font: { size: 10, weight: '500' },  // Ubah dari '600' ke '500'
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 6,          // Perkecil dari 8 ke 6
                                boxHeight: 6,         // Perkecil dari 8 ke 6
                                color: '#374151',
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map((label, i) => {
                                            return {
                                                text: label,
                                                fillStyle: healthColors[i % healthColors.length],
                                                strokeStyle: healthColors[i % healthColors.length],
                                                lineWidth: 1,   // Tambahkan ini untuk stroke yang lebih tipis
                                                hidden: false,
                                                index: i
                                            };
                                        });
                                    }
                                    return [];
                                }
                            }
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(255, 255, 255, 0.98)',
                            titleColor: '#1F2937',
                            bodyColor: '#374151',
                            borderColor: '#E5E7EB',
                            borderWidth: 2,
                            cornerRadius: 12,
                            displayColors: true,
                            padding: 16,
                            boxPadding: 6,
                            titleFont: {
                                size: 13,
                                weight: 'bold',
                                family: "'Inter', sans-serif"
                            },
                            bodyFont: {
                                size: 12,
                                family: "'Inter', sans-serif"
                            },
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return ' ' + context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    animation: {
                        animateRotate: true,
                        animateScale: true,
                        duration: 1800,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        }

        // Chart Keterangan BMI - Hover warna sedikit lebih gelap
        const ctxKeteranganBmi = document.getElementById('chartKeteranganBmi');
        if (ctxKeteranganBmi) {
            const bmiLabels = Object.keys(chartData.keteranganBmi);

            new Chart(ctxKeteranganBmi.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: bmiLabels,
                    datasets: [{
                        label: 'Jumlah Karyawan',
                        data: Object.values(chartData.keteranganBmi),
                        backgroundColor: bmiLabels.map(label => gradients.bmi[label] || '#9CA3AF'),
                        borderColor: bmiLabels.map(label => {
                            const borderMap = {
                                'Underweight': '#F59E0B',      // Amber 500
                                'Normal': '#059669',           // Green 600
                                'Overweight': '#EA580C',       // Orange 600
                                'Obesitas Tk 1': '#DC2626',    // Red 600
                                'Obesitas Tk 2': '#B91C1C',    // Red 700
                                'Obesitas Tk 3': '#991B1B'     // Red 800
                            };
                            return borderMap[label] || '#6B7280';
                        }),
                        borderWidth: 2,
                        borderRadius: 8,
                        hoverBackgroundColor: bmiLabels.map(label => {
                            // Hover warna sedikit lebih gelap (subtle difference)
                            const hoverMap = {
                                'Underweight': '#F59E0B',      // Amber 500 (sedikit lebih gelap dari Yellow 400)
                                'Normal': '#047857',           // Green 600 (sedikit lebih gelap dari Green 500)
                                'Overweight': '#EA580C',       // Orange 600 (sedikit lebih gelap dari Orange 400)
                                'Obesitas Tk 1': '#DC2626',    // Red 600 (sedikit lebih gelap dari Orange 600)
                                'Obesitas Tk 2': '#DC2626',    // Red 600 (sedikit lebih gelap dari Red 500)
                                'Obesitas Tk 3': '#B91C1C'     // Red 700 (sedikit lebih gelap dari Red 600)
                            };
                            return hoverMap[label] || '#4B5563';
                        })
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(255, 255, 255, 0.98)',
                            titleColor: '#1F2937',
                            bodyColor: '#374151',
                            borderColor: (context) => {
                                const label = context.tooltip.dataPoints[0].label;
                                return gradients.bmi[label] || '#9CA3AF';
                            },
                            borderWidth: 2,
                            cornerRadius: 10,
                            padding: 14,
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed.x / total) * 100).toFixed(1);
                                    return context.dataset.label + ': ' + context.parsed.x + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                stepSize: 1,
                                callback: function(value) {
                                    return Number.isInteger(value) ? value : '';
                                },
                                font: { size: 11 },
                                color: '#6B7280'
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 12, weight: '500' },
                                color: '#374151'
                            }
                        }
                    },
                    animation: {
                        duration: 1200,
                        easing: 'easeInOutQuart',
                        delay: (context) => {
                            return context.type === 'data' ? context.dataIndex * 100 : 0;
                        }
                    }
                }
            });
        }

        // Chart Status Kesehatan - Hijau → Kuning → Merah (dengan gradasi proporsional)
        const ctxCatatan = document.getElementById('chartCatatan');
        if (ctxCatatan) {
            const catatanLabels = ['Fit', 'Fit dengan Catatan', 'Fit dalam Pengawasan'];
            const catatanData = [
                chartData.catatan['Fit'] || 0,
                chartData.catatan['Fit dengan Catatan'] || 0,
                chartData.catatan['Fit dalam Pengawasan'] || 0
            ];

            new Chart(ctxCatatan.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: catatanLabels,
                    datasets: [{
                        data: catatanData,
                        backgroundColor: catatanLabels.map(label => gradients.status[label].background),
                        borderColor: catatanLabels.map(label => gradients.status[label].border),
                        borderWidth: 2,
                        borderRadius: 8,
                        hoverBackgroundColor: catatanLabels.map(label => gradients.status[label].hover)
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(255, 255, 255, 0.98)',
                            titleColor: '#1F2937',
                            bodyColor: '#374151',
                            borderColor: (context) => {
                                const label = context.tooltip.dataPoints[0].label;
                                return gradients.status[label].background;
                            },
                            borderWidth: 2,
                            cornerRadius: 10,
                            padding: 14,
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((context.parsed.x / total) * 100).toFixed(1) : 0;
                                    return context.label + ': ' + context.parsed.x + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                stepSize: 1,
                                callback: function(value) {
                                    return Number.isInteger(value) ? value : '';
                                },
                                font: { size: 11 },
                                color: '#6B7280'
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 12, weight: '500' },
                                color: '#374151'
                            }
                        }
                    },
                    animation: {
                        duration: 1200,
                        easing: 'easeInOutQuart',
                        delay: (context) => {
                            return context.type === 'data' ? context.dataIndex * 100 : 0;
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection