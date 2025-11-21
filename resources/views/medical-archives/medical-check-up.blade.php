@extends('layouts.app')

@section('page-title', 'Medical Check Up')

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
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Medical Check Up</span>
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
                <div class="bg-gradient-to-r from-green-500 to-teal-600 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Medical Check Up</h1>
                    <p class="text-gray-600 mt-1">Data medical check up karyawan</p>
                </div>
            </div>
        </div>

        <!-- Section 1: Patient Information Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4">
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

        <!-- Section 2: Medical Check Up Table -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Data-Data Medical Check Up
                    </h2>
                    <button onclick="openUploadModal()" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Check Up
                    </button>
                </div>
            </div>
            <div class="p-6">
                @if(isset($medicalCheckUp) && count($medicalCheckUp) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-800">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                                        NO
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                                        Periode
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                                        Tanggal
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                                        Dikeluarkan oleh
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                                        BMI
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                                        BMI Kategori
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                                        Gangguan Kesehatan
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                                        Status Kesehatan
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">
                                        File
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($medicalCheckUp as $index => $checkup)
                                    <tr class="hover:bg-green-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                            {{ $checkup->periode ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                            {{ $checkup->tanggal ? \Carbon\Carbon::parse($checkup->tanggal)->format('d-m-Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                            {{ $checkup->dikeluarkan_oleh ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                            @if($checkup->bmi)
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                    {{ $checkup->bmi }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                            @if($checkup->keterangan_bmi)
                                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                                    @if($checkup->keterangan_bmi == 'Underweight') bg-blue-100 text-blue-800
                                                    @elseif($checkup->keterangan_bmi == 'Normal') bg-green-100 text-green-800
                                                    @elseif($checkup->keterangan_bmi == 'Overweight') bg-yellow-100 text-yellow-800
                                                    @elseif($checkup->keterangan_bmi == 'Obesitas Tk 1') bg-orange-100 text-orange-800
                                                    @elseif($checkup->keterangan_bmi == 'Obesitas Tk 2') bg-red-100 text-red-800
                                                    @elseif($checkup->keterangan_bmi == 'Obesitas Tk 3') bg-red-200 text-red-900
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ $checkup->keterangan_bmi }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                            @if($checkup->kondisiKesehatan && $checkup->kondisiKesehatan->count() > 0)
                                                @foreach($checkup->kondisiKesehatan as $kondisi)
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800 mr-1 mb-1 inline-block">
                                                        {{ $kondisi->nama_kondisi }}
                                                    </span>
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                            @if($checkup->catatan)
                                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                                    @if($checkup->catatan == 'Fit') bg-green-100 text-green-800
                                                    @elseif($checkup->catatan == 'Fit dengan Catatan') bg-yellow-100 text-yellow-800
                                                    @elseif($checkup->catatan == 'Fit dalam Pengawasan') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ $checkup->catatan }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center border-r border-gray-200">
                                            @if($checkup->file_name)
                                                <a href="{{ route('medical-archives.medical-check-up.download', [$id_karyawan, $checkup->id_medical_check_up]) }}"
                                                   class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors"
                                                   title="{{ $checkup->file_name }}">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    Lihat
                                                </a>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <button onclick="editMedicalCheckUp({{ $checkup->id_medical_check_up }})" class="inline-flex items-center px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-md transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit
                                                </button>
                                                <button onclick="deleteMedicalCheckUp({{ $checkup->id_medical_check_up }})"
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
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <p class="text-lg font-medium">Tidak ada data medical check up</p>
                        <p class="text-sm mt-1">Belum ada data medical check up untuk pasien ini</p>
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
                    <h3 class="text-lg font-medium text-gray-900">Upload Medical Check Up</h3>
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
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                            <p class="mt-1 text-sm text-gray-500">Hanya file PDF yang diperbolehkan</p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dokter Pemeriksa</label>
                        <input type="text" name="dokter_pemeriksa" id="dokter_pemeriksa" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hasil Pemeriksaan (Opsional)</label>
                        <textarea name="hasil_pemeriksaan" id="hasil_pemeriksaan" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500"></textarea>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div id="progressContainer" class="mb-4 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Progress</label>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div id="progressBar" class="bg-green-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p id="progressText" class="mt-1 text-sm text-gray-600">0%</p>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeUploadModal()"
                                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md transition-colors">
                            Batal
                        </button>
                        <button type="submit" id="submitBtn"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition-colors">
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

    <script src="{{ asset('js/medical-checkup-kondisi-handler.js') }}"></script>
    <script>
        function openUploadModal() {
            // Show SweetAlert modal similar to surat rekomendasi medis
            Swal.fire({
                title: 'Tambah Medical Check Up',
                html: `
                    <form id="swalUploadForm" class="text-left">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Periode (Tahun)</label>
                            <input type="number" name="periode" id="swalPeriode" required
                                   min="2000" max="2100" value="{{ date('Y') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal (DD-MM-YYYY)</label>
                            <input type="date" name="tanggal" id="swalTanggal" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dikeluarkan oleh</label>
                            <input type="text" name="dikeluarkan_oleh" id="swalDikeluarkanOleh" required
                                   placeholder="Nama dokter atau institusi"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">BMI (Angka)</label>
                            <input type="number" name="bmi" id="swalBmi" step="0.1" min="0" max="999"
                                   placeholder="Masukkan angka BMI (contoh: 23.5)"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                            <p class="mt-1 text-sm text-gray-500">Masukkan angka BMI dengan desimal jika diperlukan</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">BMI Category</label>
                            <select name="keterangan_bmi" id="swalKeteranganBmi"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                                <option value="">Pilih BMI Category</option>
                                <option value="Underweight">Underweight</option>
                                <option value="Normal">Normal</option>
                                <option value="Overweight">Overweight</option>
                                <option value="Obesitas Tk 1">Obesitas Tk 1</option>
                                <option value="Obesitas Tk 2">Obesitas Tk 2</option>
                                <option value="Obesitas Tk 3">Obesitas Tk 3</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gangguan Kesehatan</label>
                            <div class="flex items-center gap-2">
                                <div id="kondisiKesehatanContainer" class="flex-1">
                                    <div class="mb-2">
                                        <select name="id_kondisi_kesehatan[]" id="swalKondisiKesehatan1"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            <option value="">Pilih Gangguan Kesehatan</option>
                                            @foreach($kondisiKesehatanList ?? [] as $kondisi)
                                                <option value="{{ $kondisi->id }}">{{ $kondisi->nama_kondisi }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <button type="button" id="addKondisiBtn"
                                        class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition-colors flex items-center justify-center"
                                        title="Tambah Gangguan Kesehatan"
                                        style="pointer-events: auto; cursor: pointer;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                                <button type="button" id="removeKondisiBtn"
                                        class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors flex items-center justify-center"
                                        title="Kurangi Gangguan Kesehatan"
                                        style="pointer-events: auto; cursor: pointer;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Klik + untuk menambah Gangguan Kesehatan (maksimal 5)</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Kesehatan</label>
                            <select name="catatan" id="swalCatatan"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                                <option value="">Pilih Status Kesehatan</option>
                                <option value="Fit">Fit</option>
                                <option value="Fit dengan Catatan">Fit dengan Catatan</option>
                                <option value="Fit dalam Pengawasan">Fit dalam Pengawasan</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">File PDF (Opsional, Max: 5MB)</label>
                            <input type="file" name="file" id="swalFile" accept=".pdf"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                            <p class="mt-1 text-sm text-gray-500">Hanya file PDF yang diperbolehkan (maksimal 5MB)</p>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div id="swalProgressContainer" class="mb-4 hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Progress</label>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div id="swalProgressBar" class="bg-green-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <p id="swalProgressText" class="mt-1 text-sm text-gray-600">0%</p>
                        </div>
                    </form>
                `,
                showConfirmButton: true,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#10b981',
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
                        const periodeInput = document.getElementById('swalPeriode');
                        const tanggalInput = document.getElementById('swalTanggal');
                        const dikeluarkanOlehInput = document.getElementById('swalDikeluarkanOleh');
                        const fileInput = document.getElementById('swalFile');
                        
                        if (!periodeInput.value) {
                            Swal.showValidationMessage('Periode harus diisi');
                            return false;
                        }
                        
                        if (!tanggalInput.value) {
                            Swal.showValidationMessage('Tanggal harus diisi');
                            return false;
                        }
                        
                        if (!dikeluarkanOlehInput.value.trim()) {
                            Swal.showValidationMessage('Dikeluarkan oleh harus diisi');
                            return false;
                        }
                        
                        // Validate file if provided
                        if (fileInput.files && fileInput.files.length > 0) {
                            const file = fileInput.files[0];
                            const maxSize = 5 * 1024 * 1024; // 5MB
                            
                            if (file.size > maxSize) {
                                Swal.showValidationMessage('Ukuran file maksimal 5MB');
                                return false;
                            }
                            
                            if (file.type !== 'application/pdf') {
                                Swal.showValidationMessage('Hanya file PDF yang diperbolehkan');
                                return false;
                            }
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
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message,
                                        confirmButtonColor: '#10b981',
                                        timer: 1500,
                                        timerProgressBar: true
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.showValidationMessage(response.message);
                                }
                            } else {
                                Swal.showValidationMessage('Terjadi kesalahan saat menyimpan data');
                            }
                        });
                        
                        // Handle error
                        xhr.addEventListener('error', function() {
                            Swal.showValidationMessage('Terjadi kesalahan jaringan');
                        });
                        
                        // Send request
                        xhr.open('POST', '{{ route("medical-archives.medical-check-up.upload", $id_karyawan) }}');
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
            
            // Setup event listeners after SweetAlert is shown
            setTimeout(() => {
                if (typeof window.KondisiKesehatanHandler !== 'undefined') {
                    // Initialize handler with kondisi kesehatan list
                    const kondisiKesehatanList = @json($kondisiKesehatanList ?? []);
                    window.KondisiKesehatanHandler.init(kondisiKesehatanList);
                    
                    // Setup event listeners
                    window.KondisiKesehatanHandler.setupCreateForm();
                }
                
                // Fallback: Direct inline event handlers
                const addBtn = document.getElementById('addKondisiBtn');
                const removeBtn = document.getElementById('removeKondisiBtn');
                
                if (addBtn) {
                    addBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Add button clicked via inline fallback');
                        
                        const container = document.getElementById('kondisiKesehatanContainer');
                        if (container) {
                            const currentFields = container.querySelectorAll('select').length;
                            
                            if (currentFields >= 5) {
                                if (typeof Swal !== 'undefined') {
                                    Swal.showValidationMessage('Maksimal 5 Gangguan Kesehatan');
                                } else {
                                    alert('Maksimal 5 Gangguan Kesehatan');
                                }
                                return;
                            }
                            
                            const fieldDiv = document.createElement('div');
                            fieldDiv.className = 'mb-2';
                            
                            let options = '<option value="">Pilih Gangguan Kesehatan</option>';
                            const kondisiList = @json($kondisiKesehatanList ?? []);
                            kondisiList.forEach(kondisi => {
                                options += `<option value="${kondisi.id}">${kondisi.nama_kondisi}</option>`;
                            });
                            
                            const fieldNumber = currentFields + 1;
                            fieldDiv.innerHTML = `
                                <select name="id_kondisi_kesehatan[]" id="swalKondisiKesehatan${fieldNumber}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                                    ${options}
                                </select>
                            `;
                            
                            container.appendChild(fieldDiv);
                            console.log('Added kondisi kesehatan field via inline fallback:', fieldNumber);
                        }
                    });
                }
                
                if (removeBtn) {
                    removeBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Remove button clicked via inline fallback');
                        
                        const container = document.getElementById('kondisiKesehatanContainer');
                        if (container) {
                            const fields = container.querySelectorAll('div');
                            
                            if (fields.length <= 1) {
                                if (typeof Swal !== 'undefined') {
                                    Swal.showValidationMessage('Minimal harus ada 1 Gangguan Kesehatan');
                                } else {
                                    alert('Minimal harus ada 1 Gangguan Kesehatan');
                                }
                                return;
                            }
                            
                            const removedField = fields[fields.length - 1];
                            container.removeChild(removedField);
                            console.log('Removed kondisi kesehatan field via inline fallback');
                        }
                    });
                }
            }, 500);
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
                confirmButtonColor: '#10b981',
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
        
        // Delete medical check up function with SweetAlert
        function deleteMedicalCheckUp(checkupId) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `Apakah Anda yakin ingin menghapus medical check up ini?<br><small class="text-red-500">Tindakan ini tidak dapat dibatalkan.</small>`,
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
                        console.log('Starting delete request for checkup ID:', checkupId);
                        console.log('CSRF Token:', '{{ csrf_token() }}');
                        console.log('Delete URL:', `{{ route('medical-archives.medical-check-up.delete', ['id_karyawan' => $id_karyawan, 'id' => ':id']) }}`.replace(':id', checkupId));
                        
                        // Use XMLHttpRequest for better consistency
                        const xhr = new XMLHttpRequest();
                        
                        // Handle response
                        xhr.addEventListener('load', function() {
                            console.log('Delete response status:', xhr.status);
                            console.log('Delete response text:', xhr.responseText);
                            
                            if (xhr.status === 200) {
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    console.log('Parsed response:', response);
                                    
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: response.message,
                                            confirmButtonColor: '#10b981',
                                            timer: 2000,
                                            timerProgressBar: true
                                        }).then(() => {
                                            window.location.reload();
                                        });
                                    } else {
                                        console.error('Delete failed:', response);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error!',
                                            text: response.message || 'Terjadi kesalahan saat menghapus data',
                                            confirmButtonColor: '#ef4444'
                                        });
                                    }
                                } catch (e) {
                                    console.error('Error parsing response:', e);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: 'Response tidak valid. Silakan coba lagi.',
                                        confirmButtonColor: '#ef4444'
                                    });
                                }
                            } else {
                                console.error('HTTP error:', xhr.status, xhr.statusText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: `HTTP Error ${xhr.status}: ${xhr.statusText}`,
                                    confirmButtonColor: '#ef4444'
                                });
                            }
                            resolve();
                        });
                        
                        // Handle error
                        xhr.addEventListener('error', function(e) {
                            console.error('Delete request failed:', e);
                            console.error('XHR readyState:', xhr.readyState);
                            console.error('XHR status:', xhr.status);
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan jaringan. Silakan coba lagi.',
                                confirmButtonColor: '#ef4444'
                            });
                            resolve();
                        });
                        
                        // Handle timeout
                        xhr.addEventListener('timeout', function() {
                            console.error('Delete request timed out');
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Request timeout. Silakan coba lagi.',
                                confirmButtonColor: '#ef4444'
                            });
                            resolve();
                        });
                        
                        // Configure and send request
                        const url = `{{ route('medical-archives.medical-check-up.delete', ['id_karyawan' => $id_karyawan, 'id' => ':id']) }}`.replace(':id', checkupId);
                        console.log('Opening request to:', url);
                        
                        xhr.open('POST', url);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                        xhr.timeout = 30000; // 30 seconds timeout
                        
                        const data = '_method=DELETE';
                        console.log('Sending data:', data);
                        
                        try {
                            xhr.send(data);
                            console.log('Request sent successfully');
                        } catch (e) {
                            console.error('Error sending request:', e);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Gagal mengirim request. Silakan coba lagi.',
                                confirmButtonColor: '#ef4444'
                            });
                            resolve();
                        }
                    });
                }
            });
        }
        
        // Edit medical check up function
        function editMedicalCheckUp(checkupId) {
            // Fetch medical check up data
            fetch(`{{ route('medical-archives.medical-check-up.edit', ['id_karyawan' => $id_karyawan, 'id' => ':id']) }}`.replace(':id', checkupId))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const checkup = data.data;
                        
                        // Get kondisi kesehatan list from server
                        const kondisiKesehatanList = @json($kondisiKesehatanList ?? []);
                        
                        // Get existing kondisi kesehatan IDs
                        const existingKondisiIds = checkup.kondisi_kesehatan_ids || [];
                        
                        // Build kondisi kesehatan options
                        let kondisiKesehatanOptions = '<option value="">Pilih Gangguan Kesehatan</option>';
                        kondisiKesehatanList.forEach(kondisi => {
                            const selected = existingKondisiIds.includes(kondisi.id) ? 'selected' : '';
                            kondisiKesehatanOptions += `<option value="${kondisi.id}" ${selected}>${kondisi.nama_kondisi}</option>`;
                        });
                        
                        // Generate initial kondisi kesehatan fields based on existing data
                        let kondisiKesehatanFields = '';
                        if (existingKondisiIds.length > 0) {
                            existingKondisiIds.forEach((kondisiId, index) => {
                                let options = '<option value="">Pilih Gangguan Kesehatan</option>';
                                kondisiKesehatanList.forEach(kondisi => {
                                    const selected = kondisi.id === kondisiId ? 'selected' : '';
                                    options += `<option value="${kondisi.id}" ${selected}>${kondisi.nama_kondisi}</option>`;
                                });
                                kondisiKesehatanFields += `
                                    <div class="mb-2">
                                        <select name="id_kondisi_kesehatan[]" id="swalEditKondisiKesehatan${index + 1}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            ${options}
                                        </select>
                                    </div>
                                `;
                            });
                        } else {
                            // Default one empty field if no existing data
                            kondisiKesehatanFields = `
                                <div class="mb-2">
                                    <select name="id_kondisi_kesehatan[]" id="swalEditKondisiKesehatan1"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                                        ${kondisiKesehatanOptions}
                                    </select>
                                </div>
                            `;
                        }
                        
                        // Show SweetAlert modal for editing
                        Swal.fire({
                            title: 'Edit Medical Check Up',
                            html: `
                                <form id="swalEditForm" class="text-left">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Periode (Tahun)</label>
                                        <input type="number" name="periode" id="swalEditPeriode" required
                                               min="2000" max="2100" value="${checkup.periode || ''}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal (DD-MM-YYYY)</label>
                                        <input type="date" name="tanggal" id="swalEditTanggal" required
                                               value="${checkup.tanggal ? new Date(checkup.tanggal).toISOString().split('T')[0] : ''}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Dikeluarkan oleh</label>
                                        <input type="text" name="dikeluarkan_oleh" id="swalEditDikeluarkanOleh" required
                                               value="${checkup.dikeluarkan_oleh || ''}"
                                               placeholder="Nama dokter atau institusi"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">BMI (Angka)</label>
                                        <input type="number" name="bmi" id="swalEditBmi" step="0.1" min="0" max="999"
                                               value="${checkup.bmi || ''}"
                                               placeholder="Masukkan angka BMI (contoh: 23.5)"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                                        <p class="mt-1 text-sm text-gray-500">Masukkan angka BMI dengan desimal jika diperlukan</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">BMI Category</label>
                                        <select name="keterangan_bmi" id="swalEditKeteranganBmi"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            <option value="">Pilih BMI Category</option>
                                            <option value="Underweight" ${checkup.keterangan_bmi === 'Underweight' ? 'selected' : ''}>Underweight</option>
                                            <option value="Normal" ${checkup.keterangan_bmi === 'Normal' ? 'selected' : ''}>Normal</option>
                                            <option value="Overweight" ${checkup.keterangan_bmi === 'Overweight' ? 'selected' : ''}>Overweight</option>
                                            <option value="Obesitas Tk 1" ${checkup.keterangan_bmi === 'Obesitas Tk 1' ? 'selected' : ''}>Obesitas Tk 1</option>
                                            <option value="Obesitas Tk 2" ${checkup.keterangan_bmi === 'Obesitas Tk 2' ? 'selected' : ''}>Obesitas Tk 2</option>
                                            <option value="Obesitas Tk 3" ${checkup.keterangan_bmi === 'Obesitas Tk 3' ? 'selected' : ''}>Obesitas Tk 3</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Gangguan Kesehatan</label>
                                        <div class="flex items-center gap-2">
                                            <div id="editKondisiKesehatanContainer" class="flex-1">
                                                ${kondisiKesehatanFields}
                                            </div>
                                            <button type="button" id="editAddKondisiBtn"
                                                    class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition-colors flex items-center justify-center"
                                                    title="Tambah Gangguan Kesehatan"
                                                    style="pointer-events: auto; cursor: pointer;">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </button>
                                            <button type="button" id="editRemoveKondisiBtn"
                                                    class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors flex items-center justify-center"
                                                    title="Kurangi Gangguan Kesehatan"
                                                    style="pointer-events: auto; cursor: pointer;">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">Klik + untuk menambah Gangguan Kesehatan (maksimal 5)</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Kesehatan</label>
                                        <select name="catatan" id="swalEditCatatan"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                                            <option value="">Pilih Status Kesehatan</option>
                                            <option value="Fit" ${checkup.catatan === 'Fit' ? 'selected' : ''}>Fit</option>
                                            <option value="Fit dengan Catatan" ${checkup.catatan === 'Fit dengan Catatan' ? 'selected' : ''}>Fit dengan Catatan</option>
                                            <option value="Fit dalam Pengawasan" ${checkup.catatan === 'Fit dalam Pengawasan' ? 'selected' : ''}>Fit dalam Pengawasan</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">File PDF (Opsional, Max: 5MB)</label>
                                        <input type="file" name="file" id="swalEditFile" accept=".pdf"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                                        <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ingin mengubah file</p>
                                        <p class="text-xs text-gray-400">File saat ini: ${checkup.file_name || '-'}</p>
                                    </div>
                                    
                                    <!-- Progress Bar -->
                                    <div id="swalEditProgressContainer" class="mb-4 hidden">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Update Progress</label>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div id="swalEditProgressBar" class="bg-green-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                                        </div>
                                        <p id="swalEditProgressText" class="mt-1 text-sm text-gray-600">0%</p>
                                    </div>
                                </form>
                            `,
                            showConfirmButton: true,
                            showCancelButton: true,
                            confirmButtonText: 'Update',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#10b981',
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
                                    const periodeInput = document.getElementById('swalEditPeriode');
                                    const tanggalInput = document.getElementById('swalEditTanggal');
                                    const dikeluarkanOlehInput = document.getElementById('swalEditDikeluarkanOleh');
                                    
                                    if (!periodeInput.value) {
                                        Swal.showValidationMessage('Periode harus diisi');
                                        return false;
                                    }
                                    
                                    if (!tanggalInput.value) {
                                        Swal.showValidationMessage('Tanggal harus diisi');
                                        return false;
                                    }
                                    
                                    if (!dikeluarkanOlehInput.value.trim()) {
                                        Swal.showValidationMessage('Dikeluarkan oleh harus diisi');
                                        return false;
                                    }
                                    
                                    if (fileInput.files && fileInput.files.length > 0) {
                                        const file = fileInput.files[0];
                                        const maxSize = 5 * 1024 * 1024; // 5MB
                                        
                                        if (file.size > maxSize) {
                                            Swal.showValidationMessage('Ukuran file maksimal 5MB');
                                            return false;
                                        }
                                        
                                        if (file.type !== 'application/pdf') {
                                            Swal.showValidationMessage('Hanya file PDF yang diperbolehkan');
                                            return false;
                                        }
                                    }
                                    
                                    // Show progress
                                    document.getElementById('swalEditProgressContainer').classList.remove('hidden');
                                    
                                    // Use fetch API with progress tracking
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
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Berhasil!',
                                                    text: response.message,
                                                    confirmButtonColor: '#10b981',
                                                    timer: 1500,
                                                    timerProgressBar: true
                                                }).then(() => {
                                                    window.location.reload();
                                                });
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
                                    xhr.open('POST', `{{ route('medical-archives.medical-check-up.update', ['id_karyawan' => $id_karyawan, 'id' => ':id']) }}`.replace(':id', checkupId));
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
                        
                        
                        // Fallback: Direct inline event handlers for EDIT form
                        const editAddBtn = document.getElementById('editAddKondisiBtn');
                        const editRemoveBtn = document.getElementById('editRemoveKondisiBtn');
                        
                        if (editAddBtn) {
                            editAddBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                console.log('Edit add button clicked via inline fallback');
                                
                                const container = document.getElementById('editKondisiKesehatanContainer');
                                if (container) {
                                    const currentFields = container.querySelectorAll('select').length;
                                        
                                    if (currentFields >= 5) {
                                        if (typeof Swal !== 'undefined') {
                                            Swal.showValidationMessage('Maksimal 5 Gangguan Kesehatan');
                                        } else {
                                            alert('Maksimal 5 Gangguan Kesehatan');
                                        }
                                        return;
                                    }
                                    
                                    const fieldDiv = document.createElement('div');
                                    fieldDiv.className = 'mb-2';
                                    
                                    let options = '<option value="">Pilih Gangguan Kesehatan</option>';
                                    const kondisiList = @json($kondisiKesehatanList ?? []);
                                    kondisiList.forEach(kondisi => {
                                        options += `<option value="${kondisi.id}">${kondisi.nama_kondisi}</option>`;
                                    });
                                    
                                    const fieldNumber = currentFields + 1;
                                    fieldDiv.innerHTML = `
                                        <select name="id_kondisi_kesehatan[]" id="swalEditKondisiKesehatan${fieldNumber}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                                                ${options}
                                            </select>
                                    `;
                                    
                                    container.appendChild(fieldDiv);
                                    console.log('Added edit kondisi kesehatan field via inline fallback:', fieldNumber);
                                }
                            });
                        }
                        
                        if (editRemoveBtn) {
                            editRemoveBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                console.log('Edit remove button clicked via inline fallback');
                                
                                const container = document.getElementById('editKondisiKesehatanContainer');
                                if (container) {
                                    const fields = container.querySelectorAll('div');
                                        
                                    if (fields.length <= 1) {
                                        if (typeof Swal !== 'undefined') {
                                            Swal.showValidationMessage('Minimal harus ada 1 Gangguan Kesehatan');
                                        } else {
                                            alert('Minimal harus ada 1 Gangguan Kesehatan');
                                        }
                                        return;
                                    }
                                    
                                    const removedField = fields[fields.length - 1];
                                    container.removeChild(removedField);
                                    console.log('Removed edit kondisi kesehatan field via inline fallback');
                                }
                            });
                        }
                        // Setup event listeners after SweetAlert is shown
                        setTimeout(() => {
                            if (typeof window.KondisiKesehatanHandler !== 'undefined') {
                                // Initialize handler with kondisi kesehatan list
                                const kondisiKesehatanList = @json($kondisiKesehatanList ?? []);
                                window.KondisiKesehatanHandler.init(kondisiKesehatanList);
                                
                                // Use existing kondisi kesehatan IDs from the relationship
                                const existingKondisiIds = existingKondisiIds || [];
                                
                                // Setup event listeners
                                window.KondisiKesehatanHandler.setupEditForm(existingKondisiIds);
                                
                                // Also setup direct onclick handlers as fallback
                                const addBtn = document.getElementById('editAddKondisiBtn');
                                const removeBtn = document.getElementById('editRemoveKondisiBtn');
                                
                                if (addBtn) {
                                    addBtn.onclick = function() {
                                        console.log('Edit add button clicked via onclick');
                                        window.KondisiKesehatanHandler.addKondisiKesehatanField('editKondisiKesehatanContainer', 'swalEdit');
                                    };
                                }
                                
                                if (removeBtn) {
                                    removeBtn.onclick = function() {
                                        console.log('Edit remove button clicked via onclick');
                                        window.KondisiKesehatanHandler.removeKondisiKesehatanField('editKondisiKesehatanContainer');
                                    };
                                }
                            }
                        }, 300);
                        
                        // Setup edit form kondisi kesehatan fields
                        if (typeof window.setupEditKondisiKesehatan === 'function') {
                            // Parse existing kondisi kesehatan IDs (for now, we'll use the single existing ID)
                            const existingKondisiIds = checkup.id_kondisi_kesehatan ? [checkup.id_kondisi_kesehatan] : [];
                            window.setupEditKondisiKesehatan(existingKondisiIds);
                        }
                    } else {
                        showError('Data medical check up tidak ditemukan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Terjadi kesalahan saat mengambil data medical check up');
                });
        }
        
        // Function to add new kondisi kesehatan field (delegated to external handler)
        function addKondisiKesehatanField(containerId, fieldPrefix, kondisiKesehatanList, existingValue = '') {
            if (typeof window.KondisiKesehatanHandler !== 'undefined') {
                window.KondisiKesehatanHandler.addKondisiKesehatanField(containerId, fieldPrefix);
                
                // Set existing value if provided
                if (existingValue) {
                    const container = document.getElementById(containerId);
                    const selects = container.querySelectorAll('select');
                    if (selects.length > 0) {
                        const lastSelect = selects[selects.length - 1];
                        if (lastSelect) {
                            lastSelect.value = existingValue;
                        }
                    }
                }
            }
        }
        
        // Function to remove last kondisi kesehatan field (delegated to external handler)
        function removeKondisiKesehatanField(containerId) {
            if (typeof window.KondisiKesehatanHandler !== 'undefined') {
                window.KondisiKesehatanHandler.removeKondisiKesehatanField(containerId);
            }
        }
        
        // Function to generate kondisi kesehatan fields (for backward compatibility)
        function generateKondisiKesehatanFields(jumlah, containerId, fieldPrefix, kondisiKesehatanList) {
            const container = document.getElementById(containerId);
            container.innerHTML = '';
            
            for (let i = 1; i <= jumlah; i++) {
                const fieldDiv = document.createElement('div');
                fieldDiv.className = 'mb-2';
                
                let options = '<option value="">Pilih Gangguan Kesehatan</option>';
                kondisiKesehatanList.forEach(kondisi => {
                    options += `<option value="${kondisi.id}">${kondisi.nama_kondisi}</option>`;
                });
                
                fieldDiv.innerHTML = `
                    <select name="id_kondisi_kesehatan[]" id="${fieldPrefix}KondisiKesehatan${i}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                        ${options}
                    </select>
                `;
                
                container.appendChild(fieldDiv);
            }
        }
        
        // Event listeners for jumlah kondisi kesehatan dropdowns
        document.addEventListener('DOMContentLoaded', function() {
            const kondisiKesehatanList = @json($kondisiKesehatanList ?? []);
            
            // Make kondisi kesehatan list available globally
            window.kondisiKesehatanList = kondisiKesehatanList;
            
            // For create form - setup after SweetAlert is shown
            window.setupCreateKondisiKesehatan = function() {
                const addBtn = document.getElementById('addKondisiBtn');
                const removeBtn = document.getElementById('removeKondisiBtn');
                
                if (addBtn && removeBtn) {
                    // Remove existing event listener to prevent duplicates
                    jumlahKondisiDropdown.removeEventListener('change', window.handleJumlahChange);
                    
                    // Create and store the event handler
                    window.handleJumlahChange = function() {
                        const jumlah = parseInt(this.value);
                        console.log('Jumlah changed to:', jumlah); // Debug log
                        generateKondisiKesehatanFields(jumlah, 'kondisiKesehatanContainer', 'swal', kondisiKesehatanList);
                    };
                    
                    // Add event listener
                    jumlahKondisiDropdown.addEventListener('change', window.handleJumlahChange);
                    
                    // Initialize with 1 field
                    generateKondisiKesehatanFields(1, 'kondisiKesehatanContainer', 'swal', kondisiKesehatanList);
                }
            };
            
            // Call setup function immediately if dropdown exists
            setTimeout(() => {
                if (document.getElementById('swalJumlahKondisiKesehatan')) {
                    window.setupCreateKondisiKesehatan();
                }
            }, 100);
            
            // For edit form - will be set when edit modal opens
            window.setupEditKondisiKesehatan = function(currentKondisiIds = []) {
                // Delegated to external handler
                if (typeof window.KondisiKesehatanHandler !== 'undefined') {
                    window.KondisiKesehatanHandler.setupEditForm(currentKondisiIds);
                }
            };
        });
    </script>
@endsection