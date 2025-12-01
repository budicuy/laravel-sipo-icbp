@extends('layouts.app')

@section('title', 'Laporan Transaksi')

@section('page-title', 'Laporan Transaksi Per Periode')

@push('styles')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Fallback Notification -->

    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <div class="bg-linear-to-r from-blue-600 to-cyan-600 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002 2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            Dashboard Laporan Transaksi Per Periode
        </h1>
        <p class="text-gray-600 mt-2 ml-1">Monitoring dan analisis data transaksi klinik</p>
    </div>

    <!-- Filter Section with Card -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6 border border-gray-100">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-800">Filter Periode Chart</h3>
        </div>

        <form method="GET" action="{{ route('laporan.transaksi') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                    <div class="relative">
                        <select name="bulan"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none bg-white pr-10">
                            <option value="1" {{ $bulan=='1' ? 'selected' : '' }}>Januari</option>
                            <option value="2" {{ $bulan=='2' ? 'selected' : '' }}>Februari</option>
                            <option value="3" {{ $bulan=='3' ? 'selected' : '' }}>Maret</option>
                            <option value="4" {{ $bulan=='4' ? 'selected' : '' }}>April</option>
                            <option value="5" {{ $bulan=='5' ? 'selected' : '' }}>Mei</option>
                            <option value="6" {{ $bulan=='6' ? 'selected' : '' }}>Juni</option>
                            <option value="7" {{ $bulan=='7' ? 'selected' : '' }}>Juli</option>
                            <option value="8" {{ $bulan=='8' ? 'selected' : '' }}>Agustus</option>
                            <option value="9" {{ $bulan=='9' ? 'selected' : '' }}>September</option>
                            <option value="10" {{ $bulan=='10' ? 'selected' : '' }}>Oktober</option>
                            <option value="11" {{ $bulan=='11' ? 'selected' : '' }}>November</option>
                            <option value="12" {{ $bulan=='12' ? 'selected' : '' }}>Desember</option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                    <input type="number" name="tahun" value="{{ $tahun }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Tahun">
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="w-full px-6 py-2.5 bg-linear-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filter Chart
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Pemeriksaan Reguler -->
        <div
            class="relative overflow-hidden bg-linear-to-br from-blue-500 via-blue-600 to-blue-700 rounded-xl p-6 text-white">
            <!-- Decorative circles -->
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-5 rounded-full"></div>

            <div class="relative flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 bg-blue-200 rounded-full animate-pulse"></div>
                        <p class="text-blue-100 text-sm font-medium">Pemeriksaan Reguler</p>
                    </div>
                    <h3 class="text-5xl font-bold mb-1">{{ $stats['total_pemeriksaan_reguler'] ?? 0 }}</h3>
                    <p class="text-blue-200 text-xs">{{ $stats['bulan_nama'] }} {{ $stats['tahun'] }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-2xl">
                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Pemeriksaan Emergency -->
        <div
            class="relative overflow-hidden bg-linear-to-br from-red-500 via-red-600 to-red-700 rounded-xl p-6 text-white">
            <!-- Decorative circles -->
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-5 rounded-full"></div>

            <div class="relative flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 bg-red-200 rounded-full animate-pulse"></div>
                        <p class="text-red-100 text-sm font-medium">Pemeriksaan Emergency</p>
                    </div>
                    <h3 class="text-5xl font-bold mb-1">{{ $stats['total_pemeriksaan_emergency'] ?? 0 }}</h3>
                    <p class="text-red-200 text-xs">{{ $stats['bulan_nama'] }} {{ $stats['tahun'] }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-2xl">
                    <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Biaya -->
        <div
            class="relative overflow-hidden bg-linear-to-br from-cyan-400 via-cyan-500 to-cyan-600 rounded-xl p-6 text-white">
            <!-- Decorative circles -->
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-5 rounded-full"></div>

            <div class="relative flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 bg-cyan-200 rounded-full animate-pulse"></div>
                        <p class="text-cyan-100 text-sm font-medium">Total Biaya</p>
                    </div>
                    <h3 class="text-5xl font-bold mb-1">Rp{{ number_format($stats['total_biaya'], 0, ',', '.') }}</h3>
                    <p class="text-cyan-200 text-xs">{{ $stats['bulan_nama'] }} {{ $stats['tahun'] }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-2xl">
                    <svg class="w-12 h-12 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6 border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
                <h3 class="text-lg font-semibold text-gray-800">Grafik Statistik Tahun {{ $tahun }}</h3>
            </div>

            <!-- Filter Tahun untuk Chart -->
            <form method="GET" action="{{ route('laporan.transaksi') }}" class="flex items-center gap-2">
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="periode" value="{{ $periode }}">
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700">Tahun:</label>
                    <select name="tahun"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ $tahun==$y ? 'selected' : '' }}>
                            {{ $y }}</option>
                        @endfor
                    </select>
                    <button type="submit"
                        class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-all">
                        Update
                    </button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Chart Jumlah Pemeriksaan -->
            <div class="bg-linear-to-br from-gray-50 to-blue-50 border border-blue-100 rounded-lg p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-teal-500 rounded-full"></div>
                        <h4 class="text-sm font-semibold text-gray-700">Jumlah Pemeriksaan</h4>
                    </div>
                    <span
                        class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200">Bulanan</span>
                </div>
                <div style="height: 250px;">
                    <canvas id="chartPemeriksaan"></canvas>
                </div>
            </div>

            <!-- Chart Total Biaya -->
            <div class="bg-linear-to-br from-gray-50 to-red-50 border border-red-100 rounded-lg p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                        <h4 class="text-sm font-semibold text-gray-700">Total Biaya</h4>
                    </div>
                    <span
                        class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200">Bulanan</span>
                </div>
                <div style="height: 250px;">
                    <canvas id="chartBiaya"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Laporan Harian Section -->
    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <div class="bg-linear-to-r from-blue-600 to-cyan-600 p-2 rounded-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Laporan Transaksi Per Periode</h3>
            </div>

            <!-- Export Button -->
            <div class="flex gap-2">
                <a href="{{ route('laporan.export.transaksi', [
                        'bulan' => $bulan,
                        'tahun' => $tahun,
                        'periode' => $periode,
                        'search' => $search,
                        'tipe_kunjungan' => $tipeKunjungan ?? '',
                        'jenis_kelamin' => $jenisKelamin ?? '',
                        'departemen' => $departemen ?? '',
                        'range_usia' => $rangeUsia ?? '',
                        'status_rekam' => $statusRekam ?? '',
                        'min_biaya' => $minBiaya ?? '',
                        'max_biaya' => $maxBiaya ?? ''
                    ]) }}"
                    class="px-5 py-2.5 bg-linear-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Excel
                </a>
                <button type="button" onclick="resetFilters()"
                    class="px-5 py-2.5 bg-linear-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-medium rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset Filter
                </button>
            </div>
        </div>

        <!-- Enhanced Filter Section -->
        <div class="mb-6 bg-linear-to-r from-blue-50 to-cyan-50 p-6 rounded-lg border border-blue-100">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <h3 class="text-lg font-semibold text-gray-800">Filter Lanjutan</h3>
                <button type="button" onclick="toggleAdvancedFilters()"
                    class="ml-auto text-sm text-blue-600 hover:text-blue-800">
                    <span id="toggleText">Tampilkan Filter Lanjutan</span>
                    <svg id="toggleIcon" class="w-4 h-4 inline ml-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>

            <form method="GET" action="{{ route('laporan.transaksi') }}">
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">

                <!-- Basic Filters -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="flex items-center gap-1 text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Periode
                        </label>
                        <select name="periode"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                            <option value="">Semua Periode</option>
                            @php
                            // Ambil periode dari rekam medis (reguler dan emergency)
                            $periodesReguler = \App\Models\RekamMedis::selectRaw("DATE_FORMAT(tanggal_periksa, '%m-%y')
                            as periode")
                            ->distinct()
                            ->pluck('periode');

                            $periodesEmergency =
                            \App\Models\RekamMedisEmergency::selectRaw("DATE_FORMAT(tanggal_periksa, '%m-%y') as
                            periode")
                            ->distinct()
                            ->pluck('periode');

                            $allPeriodes = $periodesReguler->merge($periodesEmergency)
                            ->unique()
                            ->filter()
                            ->sortByDesc(function($periode) {
                            // Sort by year then month descending
                            $parts = explode('-', $periode);
                            if (count($parts) === 2) {
                            return ($parts[1] * 100) + $parts[0];
                            }
                            return 0;
                            })
                            ->values();

                            $bulanNames = [
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                            ];

                            $availablePeriodes = $allPeriodes->map(function($p) use ($bulanNames) {
                            $parts = explode('-', $p);
                            if (count($parts) === 2) {
                            $bulan = $bulanNames[$parts[0]] ?? $parts[0];
                            $tahun = '20' . $parts[1];
                            return [
                            'value' => $p,
                            'label' => $bulan . ' ' . $tahun
                            ];
                            }
                            return null;
                            })->filter();
                            @endphp
                            @foreach ($availablePeriodes as $periodeOption)
                            <option value="{{ $periodeOption['value'] }}" {{ $periode==$periodeOption['value']
                                ? 'selected' : '' }}>
                                {{ $periodeOption['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center gap-1 text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Cari Nama / No RM
                        </label>
                        <input type="text" name="search" value="{{ $search }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Nama pasien atau No RM">
                    </div>
                    <div>
                        <label class="flex items-center gap-1 text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                            </svg>
                            Data per Halaman
                        </label>
                        <select name="per_page"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                            <option value="50" {{ $perPage==50 ? 'selected' : '' }}>50 Data</option>
                            <option value="100" {{ $perPage==100 ? 'selected' : '' }}>100 Data</option>
                            <option value="200" {{ $perPage==200 ? 'selected' : '' }}>200 Data</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full px-6 py-2.5 bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                            </svg>
                            Filter Tabel
                        </button>
                    </div>
                </div>

                <!-- Advanced Filters -->
                <div id="advancedFilters" class="hidden">
                    <div class="border-t border-blue-200 pt-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="flex items-center gap-1 text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                    Tipe Kunjungan
                                </label>
                                <select name="tipe_kunjungan"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                    <option value="">Semua Tipe</option>
                                    <option value="reguler" {{ $tipeKunjungan=='reguler' ? 'selected' : '' }}>Reguler
                                    </option>
                                    <option value="emergency" {{ $tipeKunjungan=='emergency' ? 'selected' : '' }}>
                                        Emergency</option>
                                </select>
                            </div>
                            <div>
                                <label class="flex items-center gap-1 text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Jenis Kelamin
                                </label>
                                <select name="jenis_kelamin"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                    <option value="">Semua</option>
                                    <option value="L" {{ $jenisKelamin=='L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ $jenisKelamin=='P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="flex items-center gap-1 text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Departemen
                                </label>
                                <select name="departemen"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                    <option value="">Semua Departemen</option>
                                    @if (isset($departemens))
                                    @foreach ($departemens as $dept)
                                    <option value="{{ $dept->id_departemen }}" {{ $departemen==$dept->id_departemen ?
                                        'selected' : '' }}>
                                        {{ $dept->nama_departemen }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label class="flex items-center gap-1 text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Range Usia
                                </label>
                                <select name="range_usia"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                    <option value="">Semua Usia</option>
                                    <option value="0-17" {{ $rangeUsia=='0-17' ? 'selected' : '' }}>0-17 tahun</option>
                                    <option value="18-25" {{ $rangeUsia=='18-25' ? 'selected' : '' }}>18-25 tahun
                                    </option>
                                    <option value="26-35" {{ $rangeUsia=='26-35' ? 'selected' : '' }}>26-35 tahun
                                    </option>
                                    <option value="36-50" {{ $rangeUsia=='36-50' ? 'selected' : '' }}>36-50 tahun
                                    </option>
                                    <option value="50+" {{ $rangeUsia=='50+' ? 'selected' : '' }}>50+ tahun</option>
                                </select>
                            </div>
                            <div>
                                <label class="flex items-center gap-1 text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a2 2 0 002-2V7a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 002 2zm7 5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Status Rekam Medis
                                </label>
                                <select name="status_rekam"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                    <option value="">Semua Status</option>
                                    <option value="On Progress" {{ $statusRekam=='On Progress' ? 'selected' : '' }}>On
                                        Progress</option>
                                    <option value="Close" {{ $statusRekam=='Close' ? 'selected' : '' }}>Close</option>
                                </select>
                            </div>
                            <div>
                                <label class="flex items-center gap-1 text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Min Biaya
                                </label>
                                <input type="number" name="min_biaya" value="{{ $minBiaya }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Rp 0">
                            </div>
                            <div>
                                <label class="flex items-center gap-1 text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Max Biaya
                                </label>
                                <input type="number" name="max_biaya" value="{{ $maxBiaya }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Rp 999999">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-hidden border border-gray-200 rounded-lg shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-linear-to-r from-gray-800 to-gray-900">
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">No
                                Registrasi</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Tipe
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">No RM
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Nama
                                Pasien</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                Hubungan</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">NIK &
                                Nama Karyawan</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                Tanggal</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                Diagnosa</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Obat
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Total
                                Biaya</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($transaksi->items() as $item)
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-blue-600">{{ $item['kode_transaksi'] }}</span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if ($item['tipe'] == 'Emergency')
                                <span
                                    class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Emergency</span>
                                @else
                                <span
                                    class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Reguler</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $item['no_rm'] }}</span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    @if ($item['tipe'] == 'Emergency')
                                    <div
                                        class="w-8 h-8 bg-linear-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                        {{ substr($item['nama_pasien'], 0, 1) }}
                                    </div>
                                    @else
                                    <div
                                        class="w-8 h-8 bg-linear-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                        {{ substr($item['nama_pasien'], 0, 1) }}
                                    </div>
                                    @endif
                                    <span class="text-sm font-medium text-gray-900">{{ $item['nama_pasien'] }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $item['hubungan'] }}</span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">{{ $item['nik_karyawan'] }}</div>
                                    <div class="text-gray-600">{{ $item['nama_karyawan'] }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-1 text-sm text-gray-700">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $item['tanggal'] }}
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900 max-w-xs">
                                    @if (isset($item['diagnosa_list']) && is_array($item['diagnosa_list']))
                                    @foreach ($item['diagnosa_list'] as $index => $diagnosa)
                                    @if ($index > 0)
                                    ,
                                    @endif
                                    <span class="font-medium">{{ $diagnosa }}</span>
                                    @endforeach
                                    @else
                                    <span class="font-medium">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-700 max-w-xs">
                                    @if (isset($item['obat_details']) && is_array($item['obat_details']))
                                    @foreach ($item['obat_details'] as $index => $obat)
                                    @if ($index > 0)
                                    ,
                                    @endif
                                    <span>{{ $obat['nama_obat'] }} ({{ $obat['jumlah_obat'] }})</span>
                                    @if (isset($obat['diskon']) && $obat['diskon'] > 0)
                                    <span
                                        class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 ml-1">
                                        -{{ $obat['diskon'] }}%
                                    </span>
                                    @endif
                                    @endforeach
                                    @else
                                    <span>-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span
                                    class="text-sm font-bold text-green-600 bg-green-50 px-3 py-1 rounded-lg border border-green-200">
                                    Rp{{ number_format($item['total_biaya'], 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if ($item['tipe'] == 'Emergency')
                                <a href="{{ route('laporan.detail-emergency', $item['id_rekam']) }}?bulan={{ $bulan }}&tahun={{ $tahun }}&periode={{ $periode }}&search={{ $search }}&per_page={{ $perPage }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-all">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </a>
                                @else
                                <a href="{{ route('laporan.detail', $item['id_rekam']) }}?bulan={{ $bulan }}&tahun={{ $tahun }}&periode={{ $periode }}&search={{ $search }}&per_page={{ $perPage }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-all">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-sm font-medium">Tidak ada data transaksi</span>
                                    <span class="text-xs text-gray-400 mt-1">Pilih periode tanggal yang
                                        berbeda</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom Pagination -->
            @if ($transaksi->hasPages())
            <div class="px-6 py-5 border-t border-gray-200 bg-white">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-sm text-gray-600">
                        Halaman <span class="font-semibold text-gray-900">{{ $transaksi->currentPage() }}</span>
                        dari <span class="font-semibold text-gray-900">{{ $transaksi->lastPage() }}</span>
                        <span class="mx-2 text-gray-400">•</span>
                        Total <span class="font-semibold text-gray-900">{{ $transaksi->total() }}</span> data
                    </div>

                    <nav class="flex items-center gap-2" role="navigation">
                        @if ($transaksi->onFirstPage())
                        <span
                            class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                            </svg>
                        </span>
                        @else
                        <a href="{{ $transaksi->appends(request()->except('page'))->url(1) }}"
                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                            </svg>
                        </a>
                        @endif

                        @if ($transaksi->onFirstPage())
                        <span
                            class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>
                        @else
                        <a href="{{ $transaksi->appends(request()->except('page'))->previousPageUrl() }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">Previous</a>
                        @endif

                        <div class="flex items-center gap-1">
                            @php
                            $start = max($transaksi->currentPage() - 2, 1);
                            $end = min($transaksi->currentPage() + 2, $transaksi->lastPage());
                            @endphp

                            @if ($start > 1)
                            <a href="{{ $transaksi->appends(request()->except('page'))->url(1) }}"
                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">1</a>
                            @if ($start > 2)
                            <span class="px-2 text-gray-500">...</span>
                            @endif
                            @endif

                            @for ($i = $start; $i <= $end; $i++) @if ($i==$transaksi->currentPage())
                                <span
                                    class="px-3 py-2 text-sm font-bold text-white bg-linear-to-r from-blue-600 to-blue-700 rounded-lg shadow-md">{{
                                    $i }}</span>
                                @else
                                <a href="{{ $transaksi->appends(request()->except('page'))->url($i) }}"
                                    class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">{{
                                    $i }}</a>
                                @endif
                                @endfor

                                @if ($end < $transaksi->lastPage())
                                    @if ($end < $transaksi->lastPage() - 1)
                                        <span class="px-2 text-gray-500">...</span>
                                        @endif
                                        <a href="{{ $transaksi->appends(request()->except('page'))->url($transaksi->lastPage()) }}"
                                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">{{
                                            $transaksi->lastPage() }}</a>
                                        @endif
                        </div>

                        @if ($transaksi->hasMorePages())
                        <a href="{{ $transaksi->appends(request()->except('page'))->nextPageUrl() }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">Next</a>
                        @else
                        <span
                            class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>
                        @endif

                        @if ($transaksi->currentPage() == $transaksi->lastPage())
                        <span
                            class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                            </svg>
                        </span>
                        @else
                        <a href="{{ $transaksi->appends(request()->except('page'))->url($transaksi->lastPage()) }}"
                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 5l7 7-7 7M5 5l7 7-7 7" />
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
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Toggle Advanced Filters
            function toggleAdvancedFilters() {
                const filtersDiv = document.getElementById('advancedFilters');
                const toggleText = document.getElementById('toggleText');
                const toggleIcon = document.getElementById('toggleIcon');

                if (filtersDiv.classList.contains('hidden')) {
                    filtersDiv.classList.remove('hidden');
                    filtersDiv.classList.add('block');
                    toggleText.textContent = 'Sembunyikan Filter Lanjutan';
                    toggleIcon.style.transform = 'rotate(180deg)';
                } else {
                    filtersDiv.classList.remove('block');
                    filtersDiv.classList.add('hidden');
                    toggleText.textContent = 'Tampilkan Filter Lanjutan';
                    toggleIcon.style.transform = 'rotate(0deg)';
                }
            }

            // Reset Filters
            function resetFilters() {
                const form = document.querySelector('form[method="GET"]');
                const inputs = form.querySelectorAll('input, select');

                inputs.forEach(input => {
                    if (input.type === 'hidden') {
                        // Keep bulan and tahun
                        if (input.name !== 'bulan' && input.name !== 'tahun') {
                            input.value = '';
                        }
                    } else {
                        input.value = '';
                    }
                });

                // Submit form to reset
                form.submit();
            }

            // Auto-expand advanced filters if any advanced filter is active
            document.addEventListener('DOMContentLoaded', function() {
                const advancedFilters = ['tipe_kunjungan', 'jenis_kelamin', 'departemen', 'range_usia', 'status_rekam', 'min_biaya', 'max_biaya'];
                const urlParams = new URLSearchParams(window.location.search);

                let hasAdvancedFilter = false;
                advancedFilters.forEach(param => {
                    if (urlParams.has(param) && urlParams.get(param) !== '') {
                        hasAdvancedFilter = true;
                    }
                });

                if (hasAdvancedFilter) {
                    toggleAdvancedFilters();
                }
            });

            // Data dari controller
            const chartPemeriksaanData = @json($chartPemeriksaan);
            const chartBiayaData = @json($chartBiaya);

            // Chart Jumlah Pemeriksaan
            const ctxPemeriksaan = document.getElementById('chartPemeriksaan').getContext('2d');
            const chartPemeriksaan = new Chart(ctxPemeriksaan, {
                type: 'line',
                data: {
                    labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                        'Oktober', 'November', 'Desember'
                    ],
                    datasets: [{
                            label: 'Pemeriksaan Reguler',
                            data: chartPemeriksaanData.reguler,
                            backgroundColor: 'rgba(59, 130, 246, 0.2)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Pemeriksaan Emergency',
                            data: chartPemeriksaanData.emergency,
                            backgroundColor: 'rgba(239, 68, 68, 0.2)',
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Total Pemeriksaan',
                            data: chartPemeriksaanData.total,
                            backgroundColor: 'rgba(16, 185, 129, 0.2)',
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 3,
                            fill: false,
                            tension: 0.4,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            borderDash: [5, 5]
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + Math.round(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                callback: function(value) {
                                    return Math.round(value);
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Chart Total Biaya
            const ctxBiaya = document.getElementById('chartBiaya').getContext('2d');
            const chartBiaya = new Chart(ctxBiaya, {
                type: 'line',
                data: {
                    labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                        'Oktober', 'November', 'Desember'
                    ],
                    datasets: [{
                            label: 'Biaya Reguler',
                            data: chartBiayaData.reguler,
                            backgroundColor: 'rgba(59, 130, 246, 0.2)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Biaya Emergency',
                            data: chartBiayaData.emergency,
                            backgroundColor: 'rgba(239, 68, 68, 0.2)',
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Total Biaya',
                            data: chartBiayaData.total,
                            backgroundColor: 'rgba(16, 185, 129, 0.2)',
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 3,
                            fill: false,
                            tension: 0.4,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            borderDash: [5, 5]
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString(
                                        'id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
</script>
@endpush
@endsection