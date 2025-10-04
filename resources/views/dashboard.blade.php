@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@push('styles')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Title -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Selamat Datang di Sistem Informasi Klinik Indofood</h1>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
            <p class="text-sm text-green-600">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
            <p class="text-sm text-red-600">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Dashboard Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
        <!-- Card 1 - Total Pasien -->
        <div class="bg-blue-500 text-white rounded-lg shadow-md p-6">
            <h3 class="text-sm font-medium opacity-90 mb-2">Total Pasien</h3>
            <p class="text-4xl font-bold">2</p>
        </div>

        <!-- Card 2 - Total Rekam Medis -->
        <div class="bg-green-600 text-white rounded-lg shadow-md p-6">
            <h3 class="text-sm font-medium opacity-90 mb-2">Total Rekam Medis</h3>
            <p class="text-4xl font-bold">1</p>
        </div>

        <!-- Card 3 - Kunjungan Hari Ini -->
        <div class="bg-yellow-500 text-white rounded-lg shadow-md p-6">
            <h3 class="text-sm font-medium opacity-90 mb-2">Kunjungan Hari Ini</h3>
            <p class="text-4xl font-bold">0</p>
        </div>

        <!-- Card 4 - On Progress -->
        <div class="bg-red-500 text-white rounded-lg shadow-md p-6">
            <h3 class="text-sm font-medium opacity-90 mb-2">On Progress</h3>
            <p class="text-4xl font-bold">1</p>
        </div>

        <!-- Card 5 - Close -->
        <div class="bg-gray-500 text-white rounded-lg shadow-md p-6">
            <h3 class="text-sm font-medium opacity-90 mb-2">Close</h3>
            <p class="text-4xl font-bold">1</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-8">
        <div class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                <select id="monthFilter" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="10">Oktober</option>
                    <option value="9">September</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                <input type="number" id="yearFilter" value="2025" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <button onclick="filterCharts()" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Chart 1 - Kunjungan Harian -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Kunjungan Harian (Oktober 2025)</h3>
            <div class="relative h-64">
                <canvas id="dailyVisitChart"></canvas>
            </div>
        </div>

        <!-- Chart 2 - Kunjungan Mingguan -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Kunjungan Mingguan (per minggu bulan Oktober)</h3>
            <div class="relative h-64">
                <canvas id="weeklyVisitChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart 3 - Kunjungan Bulanan -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Kunjungan Bulanan (2025)</h3>
        <div class="relative h-72">
            <canvas id="monthlyVisitChart"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Data untuk charts
    const dailyData = [0, 0, 8, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    const weeklyData = [8, 0, 0, 0];
    const monthlyData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 8, 0, 0];

    // Chart 1 - Kunjungan Harian (Line Chart)
    const dailyCtx = document.getElementById('dailyVisitChart').getContext('2d');
    const dailyChart = new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: Array.from({length: 31}, (_, i) => i + 1),
            datasets: [{
                label: 'Harian',
                data: dailyData,
                borderColor: 'rgb(20, 184, 166)',
                backgroundColor: 'rgba(20, 184, 166, 0.3)',
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 2
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

    // Chart 2 - Kunjungan Mingguan (Area Chart)
    const weeklyCtx = document.getElementById('weeklyVisitChart').getContext('2d');
    const weeklyChart = new Chart(weeklyCtx, {
        type: 'line',
        data: {
            labels: ['01 - 07 Oktober', '08 - 14 Oktober', '15 - 21 Oktober', '22 - 28 Oktober', '29 - 31 Oktober'],
            datasets: [{
                label: 'Mingguan',
                data: [8, 0, 0, 0, 0],
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.3)',
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
                    display: true,
                    position: 'top',
                    align: 'end'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 2
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

    // Chart 3 - Kunjungan Bulanan (Area Chart)
    const monthlyCtx = document.getElementById('monthlyVisitChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            datasets: [{
                label: 'Bulanan',
                data: monthlyData,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.3)',
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
                    display: true,
                    position: 'top',
                    align: 'end'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 2
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

    // Filter function
    function filterCharts() {
        const month = document.getElementById('monthFilter').value;
        const year = document.getElementById('yearFilter').value;
        
        // Update chart titles
        document.querySelector('#dailyVisitChart').parentElement.parentElement.querySelector('h3').textContent = 
            `Kunjungan Harian (${getMonthName(month)} ${year})`;
        document.querySelector('#weeklyVisitChart').parentElement.parentElement.querySelector('h3').textContent = 
            `Kunjungan Mingguan (per minggu bulan ${getMonthName(month)})`;
        document.querySelector('#monthlyVisitChart').parentElement.parentElement.querySelector('h3').textContent = 
            `Kunjungan Bulanan (${year})`;
        
        // Here you would typically fetch new data from the server
        // For now, we'll just refresh the charts with existing data
        dailyChart.update();
        weeklyChart.update();
        monthlyChart.update();
    }

    function getMonthName(month) {
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return months[parseInt(month) - 1];
    }
</script>
@endpush
