@extends('layouts.app')

@section('page-title', 'Data Obat')

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">DATA OBAT</h2>
    </div>

    <!-- Action Buttons -->
    <div class="mb-4 flex flex-wrap gap-2">
        <button class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah
        </button>
        <button class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-md transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit Terpilih
        </button>
        <button class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Hapus Terpilih
        </button>
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
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">
                            <input type="checkbox" class="rounded border-gray-300">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Nama Obat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Satuan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Keterangan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Stok Awal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Stok Masuk</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Stok Keluar</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Stok Akhir</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Jumlah/Kemasan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Harga Satuan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Harga Kemasan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Tanggal Update</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Row 1 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">1</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">ABC</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Tablet</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Tablet</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">ABC</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">0</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">10</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">0</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">10</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">10</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Rp 0</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Rp 0</td>
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
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">2</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Allopurinol</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Tablet</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Tablet</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Obat asam urat</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">100</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">100</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">0</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">200</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">10</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Rp 800</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Rp 8,000</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">22-09-2025</td>
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
                    <!-- Row 3 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">3</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Ambroxol</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Tablet</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Tablet</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Obat batuk</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">100</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">0</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">0</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">100</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">10</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Rp 800</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Rp 8,000</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">22-09-2025</td>
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
                    <!-- Row 4 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">4</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Amlodipine 10mg</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Tablet</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Tablet</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Obat hipertensi</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">100</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">0</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">0</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">100</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">10</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Rp 1,500</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Rp 15,000</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">22-09-2025</td>
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
                    <!-- Row 5 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">5</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Amlodipine 5mg</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Tablet</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Tablet</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Obat hipertensi</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">100</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">0</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">0</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">100</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">10</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Rp 1,200</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Rp 12,000</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">22-09-2025</td>
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
                Showing 1 to 5 of 5 entries
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
