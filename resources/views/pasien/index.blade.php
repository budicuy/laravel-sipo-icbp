@extends('layouts.app')

@section('page-title', 'Data Pasien')

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">DATA PASIEN</h2>
    </div>

    <!-- Action Buttons -->
    <div class="mb-4">
        <button class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Pasien
        </button>
    </div>

    <!-- Filter Section -->
    <div class="mb-4 bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>-- Semua Hubungan --</option>
                    <option>Karyawan</option>
                    <option>Anak</option>
                    <option>Istri</option>
                    <option>Suami</option>
                </select>
            </div>
            <div class="md:col-span-1 lg:col-span-2"></div>
            <div class="flex gap-2">
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                    Filter
                </button>
                <button class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-md transition-colors">
                    Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-700">Show</label>
                <select class="px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <option>50</option>
                    <option>100</option>
                    <option>200</option>
                </select>
                <span class="text-sm text-gray-700">entries</span>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-700">Search:</label>
                <input type="text" class="px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" placeholder="">
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">No RM</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Kode Transaksi (Terakhir)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">NIK</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Nama Pasien</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Hubungan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Tanggal Lahir</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Jenis Kelamin</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Alamat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Tanggal Kunjungan Terakhir</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Row 1 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">1</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">0001/NDL/B/M/10/2025</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">KRY001-03102025</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">KRY001</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Awang Rio</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Karyawan</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">01-09-1998</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Laki - Laki</td>
                        <td class="px-4 py-3 text-sm text-gray-900">Jl. Belitung Darat GG. BKIA Banjarmasin Utara</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">03-10-2025</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <button class="inline-flex items-center justify-center w-8 h-8 bg-yellow-500 hover:bg-yellow-600 text-white rounded transition-colors mr-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button class="inline-flex items-center justify-center w-8 h-8 bg-red-600 hover:bg-red-700 text-white rounded transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <!-- Row 2 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">2</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">0002/NDL/B/M/10/2025</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">KRY001-03102025</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">KRY001</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Ronggo</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Anak</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">03-05-2025</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Laki - Laki</td>
                        <td class="px-4 py-3 text-sm text-gray-900">sambang lihum</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">03-10-2025</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <button class="inline-flex items-center justify-center w-8 h-8 bg-yellow-500 hover:bg-yellow-600 text-white rounded transition-colors mr-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button class="inline-flex items-center justify-center w-8 h-8 bg-red-600 hover:bg-red-700 text-white rounded transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="text-sm text-gray-700">
                Showing 1 to 2 of 2 entries
            </div>
            <div class="flex gap-2">
                <button class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                    Previous
                </button>
                <button class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition-colors">
                    1
                </button>
                <button class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                    Next
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
