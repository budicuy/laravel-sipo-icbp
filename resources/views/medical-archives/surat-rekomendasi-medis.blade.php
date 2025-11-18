@extends('layouts.app')

@section('page-title', 'Surat Rekomendasi Medis')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Breadcrumb Navigation -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('medical-archives.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Medical Archives</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('medical-archives.show', $id_karyawan) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Detail</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Surat Rekomendasi Medis</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('medical-archives.show', $id_karyawan) }}" class="text-gray-600 hover:text-gray-800 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Surat Rekomendasi Medis</h1>
                    <p class="text-gray-600 mt-1">Data surat rekomendasi medis karyawan</p>
                </div>
            </div>
        </div>

        <!-- Section 1: Patient Information Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Informasi Pasien
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- NO RM -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">NO RM</label>
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                            {{ $employeeInfo->nik_karyawan ?? '-' }}-{{ $familyMember->kode_hubungan ?? '-' }}
                        </span>
                    </div>
                    
                    <!-- NIK Karyawan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">NIK Karyawan</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $employeeInfo->nik_karyawan ?? '-' }}</p>
                    </div>
                    
                    <!-- Nama Pasien -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nama Pasien</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $familyMember->nama_keluarga ?? '-' }}</p>
                    </div>
                    
                    <!-- Departemen -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Departemen</label>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                            {{ $employeeInfo->nama_departemen ?? '-' }}
                        </span>
                    </div>
                    
                    <!-- Tanggal Lahir -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Lahir</label>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $familyMember->tanggal_lahir ? \Carbon\Carbon::parse($familyMember->tanggal_lahir)->format('d-m-Y') : '-' }}
                        </p>
                    </div>
                    
                    <!-- Usia -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Usia</label>
                        <p class="text-lg font-semibold text-gray-900">
                            @if($familyMember->tanggal_lahir)
                                {{ \Carbon\Carbon::parse($familyMember->tanggal_lahir)->age }} tahun
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    
                    <!-- Hubungan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Hubungan</label>
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-medium">
                            {{ $familyMember->hubungan_nama ?? '-' }}
                        </span>
                    </div>
                    
                    <!-- Status Karyawan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status Karyawan</label>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                            {{ $employeeInfo->karyawan_status ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Surat Rekomendasi Medis Table -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Data-Data Surat Rekomendasi Medis
                    </h2>
                    <button onclick="openUploadModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Surat
                    </button>
                </div>
            </div>
            <div class="p-6">
                @if(isset($suratRekomendasi) && count($suratRekomendasi) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-800">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                                        NO
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                                        Tanggal
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                                        Penerbit Surat
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                                        Catatan Medis
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                                        Detail
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($suratRekomendasi as $index => $surat)
                                    <tr class="hover:bg-blue-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                            {{ $surat->tanggal ? \Carbon\Carbon::parse($surat->tanggal)->format('d-m-Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                            {{ $surat->penerbit_surat ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">
                                            <div class="max-w-xs truncate" title="{{ $surat->catatan_medis ?? '-' }}">
                                                {{ $surat->catatan_medis ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center border-r border-gray-200">
                                            <a href="{{ route('medical-archives.surat-rekomendasi-medis.download', [$id_karyawan, $surat->id]) }}"
                                               class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Lihat Detail
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <button onclick="editSurat({{ $surat->id }})" class="inline-flex items-center px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-md transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit
                                                </button>
                                                <button onclick="deleteSurat({{ $surat->id }})"
                                                        class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-lg font-medium">Tidak ada data surat rekomendasi medis</p>
                        <p class="text-sm mt-1">Belum ada surat rekomendasi medis untuk pasien ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div id="uploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Upload Surat Rekomendasi Medis</h3>
                    <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">File PDF (Max: 10MB)</label>
                        <div class="relative">
                            <input type="file" name="file" id="file" accept=".pdf" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-sm text-gray-500">Hanya file PDF yang diperbolehkan</p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Penerbit Surat</label>
                        <input type="text" name="penerbit_surat" id="penerbit_surat" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Medis (Opsional)</label>
                        <textarea name="catatan_medis" id="catatan_medis" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div id="progressContainer" class="mb-4 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Progress</label>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div id="progressBar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p id="progressText" class="mt-1 text-sm text-gray-600">0%</p>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeUploadModal()"
                                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md transition-colors">
                            Batal
                        </button>
                        <button type="submit" id="submitBtn"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors">
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Notification -->
    <div id="successNotification" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg hidden z-50">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span id="successMessage">File berhasil diunggah!</span>
        </div>
    </div>

    <!-- Error Notification -->
    <div id="errorNotification" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg hidden z-50">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <span id="errorMessage">Terjadi kesalahan!</span>
        </div>
    </div>

    <script>
        function openUploadModal() {
            // Show SweetAlert modal similar to rekam-medis import
            Swal.fire({
                title: 'Upload Surat Rekomendasi Medis',
                html: `
                    <form id="swalUploadForm" class="text-left">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">File PDF (Max: 10MB)</label>
                            <input type="file" name="file" id="swalFile" accept=".pdf" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-sm text-gray-500">Hanya file PDF yang diperbolehkan</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                            <input type="date" name="tanggal" id="swalTanggal" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Penerbit Surat</label>
                            <input type="text" name="penerbit_surat" id="swalPenerbit" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Medis (Opsional)</label>
                            <textarea name="catatan_medis" id="swalCatatan" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div id="swalProgressContainer" class="mb-4 hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Progress</label>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div id="swalProgressBar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <p id="swalProgressText" class="mt-1 text-sm text-gray-600">0%</p>
                        </div>
                    </form>
                `,
                showConfirmButton: true,
                showCancelButton: true,
                confirmButtonText: 'Upload',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#6b7280',
                width: '600px',
                customClass: {
                    popup: 'swal2-popup',
                },
                preConfirm: () => {
                    return new Promise((resolve) => {
                        const form = document.getElementById('swalUploadForm');
                        const formData = new FormData(form);
                        
                        // Validation
                        const fileInput = document.getElementById('swalFile');
                        if (!fileInput.files || fileInput.files.length === 0) {
                            Swal.showValidationMessage('Silakan pilih file PDF terlebih dahulu');
                            return false;
                        }
                        
                        const file = fileInput.files[0];
                        const maxSize = 10 * 1024 * 1024; // 10MB
                        
                        if (file.size > maxSize) {
                            Swal.showValidationMessage('Ukuran file maksimal 10MB');
                            return false;
                        }
                        
                        if (file.type !== 'application/pdf') {
                            Swal.showValidationMessage('Hanya file PDF yang diperbolehkan');
                            return false;
                        }
                        
                        // Show progress
                        document.getElementById('swalProgressContainer').classList.remove('hidden');
                        
                        // Create XMLHttpRequest for progress tracking
                        const xhr = new XMLHttpRequest();
                        
                        // Track upload progress
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percentComplete = (e.loaded / e.total) * 100;
                                document.getElementById('swalProgressBar').style.width = percentComplete + '%';
                                document.getElementById('swalProgressText').textContent = Math.round(percentComplete) + '%';
                            }
                        });
                        
                        // Handle response
                        xhr.addEventListener('load', function() {
                            if (xhr.status === 200) {
                                const response = JSON.parse(xhr.responseText);
                                if (response.success) {
                                    showSuccess(response.message);
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1500);
                                } else {
                                    Swal.showValidationMessage(response.message);
                                }
                            } else {
                                Swal.showValidationMessage('Terjadi kesalahan saat mengunggah file');
                            }
                        });
                        
                        // Handle error
                        xhr.addEventListener('error', function() {
                            Swal.showValidationMessage('Terjadi kesalahan jaringan');
                        });
                        
                        // Send request
                        xhr.open('POST', '{{ route("medical-archives.surat-rekomendasi-medis.upload", $id_karyawan) }}');
                        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                        xhr.send(formData);
                        
                        resolve(false); // Prevent SweetAlert from closing automatically
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Form was submitted successfully
                }
            });
        }
        
        function closeUploadModal() {
            // This function is not needed with SweetAlert approach
        }
        
        function showSuccess(message) {
            // Use SweetAlert for success notification
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: message,
                confirmButtonColor: '#3b82f6',
                timer: 3000,
                timerProgressBar: true
            });
        }
        
        function showError(message) {
            // Use SweetAlert for error notification
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
                confirmButtonColor: '#ef4444'
            });
        }
        
        // Delete surat function with SweetAlert
        function deleteSurat(suratId) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `Apakah Anda yakin ingin menghapus surat rekomendasi medis ini?<br><small class="text-red-500">Tindakan ini tidak dapat dibatalkan.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                showLoaderOnConfirm: true,
                preConfirm: function() {
                    return new Promise(function(resolve) {
                        // Create form element
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `{{ route('medical-archives.surat-rekomendasi-medis.delete', ['id_karyawan' => $id_karyawan, 'id' => ':id']) }}`.replace(':id', suratId);
                        
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
                        
                        resolve();
                    });
                }
            });
        }
        
        // Edit surat function
        function editSurat(suratId) {
            // Fetch surat data
            fetch(`{{ route('medical-archives.surat-rekomendasi-medis.edit', ['id_karyawan' => $id_karyawan, 'id' => ':id']) }}`.replace(':id', suratId))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const surat = data.data;
                        
                        // Show SweetAlert modal for editing
                        Swal.fire({
                            title: 'Edit Surat Rekomendasi Medis',
                            html: `
                                <form id="swalEditForm" class="text-left">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">File PDF (Max: 10MB)</label>
                                        <input type="file" name="file" id="swalEditFile" accept=".pdf"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ingin mengubah file</p>
                                        <p class="text-xs text-gray-400">File saat ini: ${surat.file_name || '-'}</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                                        <input type="date" name="tanggal" id="swalEditTanggal" required
                                               value="${surat.tanggal ? new Date(surat.tanggal).toISOString().split('T')[0] : ''}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Penerbit Surat</label>
                                        <input type="text" name="penerbit_surat" id="swalEditPenerbit" required
                                               value="${surat.penerbit_surat || ''}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Medis (Opsional)</label>
                                        <textarea name="catatan_medis" id="swalEditCatatan" rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">${surat.catatan_medis || ''}</textarea>
                                    </div>
                                    
                                    <!-- Progress Bar -->
                                    <div id="swalEditProgressContainer" class="mb-4 hidden">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Update Progress</label>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div id="swalEditProgressBar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                                        </div>
                                        <p id="swalEditProgressText" class="mt-1 text-sm text-gray-600">0%</p>
                                    </div>
                                </form>
                            `,
                            showConfirmButton: true,
                            showCancelButton: true,
                            confirmButtonText: 'Update',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#3b82f6',
                            cancelButtonColor: '#6b7280',
                            width: '600px',
                            customClass: {
                                popup: 'swal2-popup',
                            },
                            preConfirm: () => {
                                return new Promise((resolve) => {
                                    const form = document.getElementById('swalEditForm');
                                    const formData = new FormData(form);
                                    
                                    // Add PUT method for Laravel
                                    formData.append('_method', 'PUT');
                                    
                                    // Get file input
                                    const fileInput = document.getElementById('swalEditFile');
                                    
                                    // Validation
                                    const tanggalInput = document.getElementById('swalEditTanggal');
                                    const penerbitInput = document.getElementById('swalEditPenerbit');
                                    
                                    if (!tanggalInput.value) {
                                        Swal.showValidationMessage('Tanggal harus diisi');
                                        return false;
                                    }
                                    
                                    if (!penerbitInput.value.trim()) {
                                        Swal.showValidationMessage('Penerbit surat harus diisi');
                                        return false;
                                    }
                                    
                                    if (fileInput.files && fileInput.files.length > 0) {
                                        const file = fileInput.files[0];
                                        const maxSize = 10 * 1024 * 1024; // 10MB
                                        
                                        if (file.size > maxSize) {
                                            Swal.showValidationMessage('Ukuran file maksimal 10MB');
                                            return false;
                                        }
                                        
                                        if (file.type !== 'application/pdf') {
                                            Swal.showValidationMessage('Hanya file PDF yang diperbolehkan');
                                            return false;
                                        }
                                    }
                                    
                                    // Show progress
                                    document.getElementById('swalEditProgressContainer').classList.remove('hidden');
                                    
                                    // Create XMLHttpRequest for progress tracking
                                    const xhr = new XMLHttpRequest();
                                    
                                    // Track upload progress
                                    xhr.upload.addEventListener('progress', function(e) {
                                        if (e.lengthComputable) {
                                            const percentComplete = (e.loaded / e.total) * 100;
                                            document.getElementById('swalEditProgressBar').style.width = percentComplete + '%';
                                            document.getElementById('swalEditProgressText').textContent = Math.round(percentComplete) + '%';
                                        }
                                    });
                                    
                                    // Handle response
                                    xhr.addEventListener('load', function() {
                                        if (xhr.status === 200) {
                                            const response = JSON.parse(xhr.responseText);
                                            if (response.success) {
                                                showSuccess(response.message);
                                                setTimeout(() => {
                                                    window.location.reload();
                                                }, 1500);
                                            } else {
                                                Swal.showValidationMessage(response.message);
                                            }
                                        } else {
                                            Swal.showValidationMessage('Terjadi kesalahan saat memperbarui data');
                                        }
                                    });
                                    
                                    // Handle error
                                    xhr.addEventListener('error', function() {
                                        Swal.showValidationMessage('Terjadi kesalahan jaringan');
                                    });
                                    
                                    // Send request
                                    xhr.open('POST', `{{ route('medical-archives.surat-rekomendasi-medis.update', ['id_karyawan' => $id_karyawan, 'id' => ':id']) }}`.replace(':id', suratId));
                                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                                    xhr.send(formData);
                                    
                                    resolve(false); // Prevent SweetAlert from closing automatically
                                });
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Form was submitted successfully
                            }
                        });
                    } else {
                        showError('Data surat tidak ditemukan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Terjadi kesalahan saat mengambil data surat');
                });
        }
    </script>
@endsection