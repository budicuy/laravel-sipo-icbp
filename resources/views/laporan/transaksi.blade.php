@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Dashboard Laporan Transaksi</h2>
        </div>

        <!-- Filter Tahun -->
        <div class="mb-6 flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Oktober</label>
                <select class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
            </div>
            <div>
                <input type="number" value="2025" class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tahun">
            </div>
            <div>
                <button class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors">
                    Filter
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Total Pemeriksaan -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium mb-1">Total Pemeriksaan</p>
                        <h3 class="text-4xl font-bold">1</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Biaya -->
            <div class="bg-gradient-to-r from-cyan-400 to-cyan-500 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-cyan-100 text-sm font-medium mb-1">Total Biaya</p>
                        <h3 class="text-4xl font-bold">Rp20.000</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Chart Jumlah Pemeriksaan -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="w-3 h-3 bg-teal-500 rounded-full mr-2"></div>
                    <h3 class="text-sm font-medium text-gray-700">Jumlah Pemeriksaan</h3>
                </div>
                <div style="height: 250px;">
                    <canvas id="chartPemeriksaan"></canvas>
                </div>
            </div>

            <!-- Chart Total Biaya -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="w-3 h-3 bg-red-400 rounded-full mr-2"></div>
                    <h3 class="text-sm font-medium text-gray-700">Total Biaya</h3>
                </div>
                <div style="height: 250px;">
                    <canvas id="chartBiaya"></canvas>
                </div>
            </div>
        </div>
            </div>
        </div>

        <!-- Export Button -->
        <div class="mb-4">
            <button class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export ke Excel
            </button>
        </div>

        <!-- Laporan Harian Section -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Laporan Harian 📊
            </h3>

            <!-- Date Filter -->
            <div class="mb-4 flex flex-wrap items-end gap-4 bg-gray-50 p-4 rounded-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                    <input type="date" value="2025-10-01" class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                    <input type="date" value="2025-10-31" class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <button class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors">
                        Sort
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Kode Transaksi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">No RM</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">NIK</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Diagnosa</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Obat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Biaya</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">KRY001-03102025</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">0001/NDL/BJM/10/2025</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">03-10-2025</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">KRY001</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Awang Rio</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Karyawan</td>
                            <td class="px-4 py-3 text-sm text-gray-900">Demam Berdarah Dengue (DBD)</td>
                            <td class="px-4 py-3 text-sm text-gray-900">Paracetamol, Piracetam, Vit C</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">Rp20.000</td>
                        </tr>
                    </tbody>
                </table>
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
