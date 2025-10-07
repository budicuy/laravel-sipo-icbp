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
            <a href="{{ route('rekam-medis.create') }}" class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-3 rounded-lg font-medium shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Rekam Medis
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Dari Tanggal -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Dari Tanggal</label>
                <div class="relative">
                    <input type="date" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Sampai Tanggal -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sampai Tanggal</label>
                <div class="relative">
                    <input type="date" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Cari Data</label>
                <div class="relative">
                    <input type="text" placeholder="Cari nama, NIK, atau No RM..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-3">
                <button class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-medium px-6 py-2.5 rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                </button>
                <button class="flex-1 bg-white hover:bg-gray-50 border-2 border-gray-300 text-gray-700 font-medium px-6 py-2.5 rounded-lg transition-all">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Table Header -->
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Data Rekam Medis Pasien
                </h2>
                <div class="flex items-center gap-2">
                    <label class="text-white text-sm">Show</label>
                    <select class="px-3 py-1.5 bg-white border border-white rounded-lg text-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-white">
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                    </select>
                    <span class="text-white text-sm">entries</span>
                </div>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">No RM</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">NIK</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Keterangan NIK</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Nama Pasien</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Penyakit</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Terapi</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Obat</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Catatan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Detail</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Row 1 -->
                    <tr class="hover:bg-green-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">1</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200 font-medium">0001/HCL/RM/10/2025</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">KRY001</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Karyawan</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200 font-medium">Awang Rio</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">Demam Berdarah</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">Obat</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">Paracetamol, Amoxicillin</td>
                        <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">Istirahat 3 hari</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">03-10-2025</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Selesai</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:shadow-lg transition-all">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Detail
                            </button>
                        </td>
                    </tr>

                    <!-- Row 2 -->
                    <tr class="hover:bg-green-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">2</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200 font-medium">0002/HCL/RM/10/2025</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">KRY001</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Anak</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200 font-medium">Ronggo</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">Flu</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">Obat</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">Vitamin C, Obat Batuk</td>
                        <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">Banyak minum air putih</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">03-10-2025</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Selesai</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center border-r border-gray-200">
                            <button class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:shadow-lg transition-all">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Detail
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button class="bg-yellow-500 hover:bg-yellow-600 text-white p-1.5 rounded">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 3 -->
                    <tr class="hover:bg-green-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">3</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200 font-medium">0003/HCL/RM/10/2025</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">KRY002</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            <span class="px-3 py-1 bg-pink-100 text-pink-800 rounded-full text-xs font-medium">Istri</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200 font-medium">Siti Nurhaliza</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">Hipertensi</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">Lab</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">Captopril, Amlodipine</td>
                        <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">Kontrol tensi rutin</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">04-10-2025</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            <div class="relative inline-block">
                                <select onchange="updateStatus(this, '3')" class="appearance-none cursor-pointer px-3 py-1 rounded-full text-xs font-medium focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-yellow-500 transition-colors" style="min-width: 100px;">
                                    <option value="proses" selected class="bg-white text-gray-900">Proses</option>
                                    <option value="selesai" class="bg-white text-gray-900">Selesai</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center border-r border-gray-200">
                            <button class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:shadow-lg transition-all">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Detail
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button class="bg-yellow-500 hover:bg-yellow-600 text-white p-1.5 rounded">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer Pagination -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Showing <span class="font-medium">1</span> to <span class="font-medium">3</span> of <span class="font-medium">3</span> entries
                </div>
                <div class="flex items-center gap-2">
                    <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        Previous
                    </button>
                    <button class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg font-medium shadow-md">
                        1
                    </button>
                    <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
function updateStatus(selectElement, id) {
    const newStatus = selectElement.value;
    const selectContainer = selectElement.parentElement;
    
    // Update styling based on status
    if (newStatus === 'selesai') {
        selectElement.className = selectElement.className.replace('bg-yellow-100 text-yellow-800', 'bg-green-100 text-green-800');
        selectElement.className = selectElement.className.replace('ring-yellow-500', 'ring-green-500');
    } else {
        selectElement.className = selectElement.className.replace('bg-green-100 text-green-800', 'bg-yellow-100 text-yellow-800');
        selectElement.className = selectElement.className.replace('ring-green-500', 'ring-yellow-500');
    }

    // Here you would typically make an AJAX call to update the status in the backend
    fetch(`/rekam-medis/${id}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Optional: Show success message
            console.log('Status updated successfully');
        } else {
            // Handle error and possibly revert the select
            console.error('Failed to update status');
            selectElement.value = selectElement.value === 'selesai' ? 'proses' : 'selesai';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revert the select on error
        selectElement.value = selectElement.value === 'selesai' ? 'proses' : 'selesai';
    });
}
</script>
@endpush

@endsection
