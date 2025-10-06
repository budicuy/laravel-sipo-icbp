@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002 2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            Dashboard Laporan Transaksi
        </h1>
        <p class="text-gray-600 mt-2 ml-1">Monitoring dan analisis data transaksi klinik</p>
    </div>

    <!-- Filter Section with Card -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6 border border-gray-100">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-800">Filter Periode</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                <div class="relative">
                    <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none bg-white pr-10">
                        <option>Januari</option>
                        <option>Februari</option>
                        <option>Maret</option>
                        <option>April</option>
                        <option>Mei</option>
                        <option>Juni</option>
                        <option>Juli</option>
                        <option>Agustus</option>
                        <option>September</option>
                        <option selected>Oktober</option>
                        <option>November</option>
                        <option>Desember</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                <input type="number" value="2025" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Tahun">
            </div>

            <div class="flex items-end">
                <button class="w-full px-6 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Tampilkan Data
                </button>
            </div>
        </div>
    </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Total Pemeriksaan -->
            <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 rounded-xl p-6 text-white">
                <!-- Decorative circles -->
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
                <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-5 rounded-full"></div>

                <div class="relative flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-2 h-2 bg-blue-200 rounded-full animate-pulse"></div>
                            <p class="text-blue-100 text-sm font-medium">Total Pemeriksaan</p>
                        </div>
                        <h3 class="text-5xl font-bold mb-1">1</h3>
                        <p class="text-blue-200 text-xs">Oktober 2025</p>
                    </div>
                    <div class="bg-white rounded-2xl p-5 shadow-2xl">
                        <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Biaya -->
            <div class="relative overflow-hidden bg-gradient-to-br from-cyan-400 via-cyan-500 to-cyan-600 rounded-xl p-6 text-white">
                <!-- Decorative circles -->
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
                <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-5 rounded-full"></div>

                <div class="relative flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-2 h-2 bg-cyan-200 rounded-full animate-pulse"></div>
                            <p class="text-cyan-100 text-sm font-medium">Total Biaya</p>
                        </div>
                        <h3 class="text-5xl font-bold mb-1">Rp20.000</h3>
                        <p class="text-cyan-200 text-xs">Oktober 2025</p>
                    </div>
                    <div class="bg-white rounded-2xl p-5 shadow-2xl">
                        <svg class="w-12 h-12 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6 border border-gray-100">
            <div class="flex items-center gap-2 mb-6">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
                <h3 class="text-lg font-semibold text-gray-800">Grafik Statistik</h3>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Chart Jumlah Pemeriksaan -->
                <div class="bg-gradient-to-br from-gray-50 to-blue-50 border border-blue-100 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-teal-500 rounded-full"></div>
                            <h4 class="text-sm font-semibold text-gray-700">Jumlah Pemeriksaan</h4>
                        </div>
                        <span class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200">Bulanan</span>
                    </div>
                    <div style="height: 250px;">
                        <canvas id="chartPemeriksaan"></canvas>
                    </div>
                </div>

                <!-- Chart Total Biaya -->
                <div class="bg-gradient-to-br from-gray-50 to-red-50 border border-red-100 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                            <h4 class="text-sm font-semibold text-gray-700">Total Biaya</h4>
                        </div>
                        <span class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200">Bulanan</span>
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
                    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Laporan Transaksi Harian</h3>
                </div>

                <!-- Export Button -->
                <button class="px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Excel
                </button>
            </div>

            <!-- Date Filter -->
            <div class="mb-6 bg-gradient-to-r from-blue-50 to-cyan-50 p-4 rounded-lg border border-blue-100">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="flex items-center gap-1 text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Dari Tanggal
                        </label>
                        <input type="date" value="2025-10-01" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                    </div>
                    <div>
                        <label class="flex items-center gap-1 text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Sampai Tanggal
                        </label>
                        <input type="date" value="2025-10-31" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                    </div>
                    <div class="flex items-end">
                        <button class="w-full px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                            </svg>
                            Urutkan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-hidden border border-gray-200 rounded-lg shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-800 to-gray-900">
                                <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Kode Transaksi</th>
                                <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">No RM</th>
                                <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">NIK</th>
                                <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Nama</th>
                                <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Status</th>
                                <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Diagnosa</th>
                                <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Obat</th>
                                <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Biaya</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr class="hover:bg-blue-50 transition-colors">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-blue-600">KRY001-03102025</span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">0001/NDL/BJM/10/2025</span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1 text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        03-10-2025
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">KRY001</span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                            AR
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">Awang Rio</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                        Karyawan
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs">
                                        <span class="font-medium">Demam Berdarah Dengue (DBD)</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm text-gray-700 max-w-xs">
                                        Paracetamol, Piracetam, Vit C
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-green-600 bg-green-50 px-3 py-1 rounded-lg border border-green-200">
                                        Rp20.000
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart Jumlah Pemeriksaan
    const ctxPemeriksaan = document.getElementById('chartPemeriksaan').getContext('2d');
    const chartPemeriksaan = new Chart(ctxPemeriksaan, {
        type: 'line',
        data: {
            labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            datasets: [{
                label: 'Jumlah Pemeriksaan',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0.8, 1, 0, 0],
                backgroundColor: 'rgba(20, 184, 166, 0.2)',
                borderColor: 'rgba(20, 184, 166, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            return 'Pemeriksaan: ' + Math.round(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    min: 0,
                    max: 1.2,
                    ticks: {
                        stepSize: 0.2,
                        callback: function(value) {
                            return value.toFixed(1);
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
            labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            datasets: [{
                label: 'Total Biaya',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 15000, 20000, 0, 0],
                backgroundColor: 'rgba(248, 113, 113, 0.2)',
                borderColor: 'rgba(248, 113, 113, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            return 'Biaya: Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    min: 0,
                    max: 22000,
                    ticks: {
                        stepSize: 4000,
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
