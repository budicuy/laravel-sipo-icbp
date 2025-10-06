@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="text-2xl font-bold text-gray-800">Daftar Rekam Medis</h2>
            <a href="{{ route('rekam-medis.create') }}" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Rekam Medis
            </a>
        </div>

        <!-- Date Filter -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal:</label>
                    <input type="date" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" placeholder="dd/mm/yyyy">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal:</label>
                    <input type="date" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" placeholder="dd/mm/yyyy">
                </div>
                <div class="flex gap-2">
                    <button class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors text-sm">
                        Filter
                    </button>
                    <button class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-md transition-colors text-sm">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Entries Control -->
        <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between gap-4">
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
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">NIK</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Keterangan NIK</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Nama Pasien</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Penyakit</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Terapi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Obat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Catatan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hover:bg-gray-700">
                            <div class="flex items-center gap-1">
                                Tanggal
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Row 1 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">1</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">0001/NDL/BJM/10/2025</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">KRY001</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Karyawan</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Awang Rio</td>
                        <td class="px-4 py-3 text-sm text-gray-900">Demam Berdarah Dengue (DBD)</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Obat</td>
                        <td class="px-4 py-3 text-sm text-gray-900">Vit C, Piracetam, Paracetamol</td>
                        <td class="px-4 py-3 text-sm text-gray-900 max-w-xs">Pasien mengalami lorek merah dari tubuh badah sangat tinggi</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">03-10-2025</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <span class="px-4 py-1.5 bg-green-500 text-white text-xs font-medium rounded inline-block">
                                Aktif
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <a href="{{ route('rekam-medis.detail', 1) }}" class="inline-flex items-center justify-center w-8 h-8 bg-cyan-500 hover:bg-cyan-600 text-white rounded transition-colors mr-1" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <button class="inline-flex items-center justify-center w-8 h-8 bg-yellow-500 hover:bg-yellow-600 text-white rounded transition-colors mr-1" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button class="inline-flex items-center justify-center w-8 h-8 bg-red-600 hover:bg-red-700 text-white rounded transition-colors" title="Hapus">
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
                Showing 1 to 1 of 1 entries
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
