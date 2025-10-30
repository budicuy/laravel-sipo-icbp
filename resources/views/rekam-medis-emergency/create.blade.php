@extends('layouts.app')

@section('page-title', 'Tambah Rekam Medis Emergency')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-3">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <div class="bg-gradient-to-r from-red-600 to-pink-600 p-3 rounded-lg shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        Tambah Rekam Medis Emergency
                    </h1>
                    <p class="text-gray-600 mt-1 ml-1">Buat rekam medis emergency untuk karyawan external</p>
                </div>
            </div>
        </div>

        <!-- Enhanced Error Messages -->
        @if ($errors->any())
            <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 p-5 rounded-lg shadow-md"
                id="error-container">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-base font-bold text-red-800">
                            Terdapat {{ $errors->count() }} kesalahan yang perlu diperbaiki
                        </h3>
                        <div class="mt-3 text-sm text-red-700 space-y-2">
                            @foreach ($errors->all() as $error)
                                <div class="flex items-start py-1">
                                    <svg class="h-5 w-5 text-red-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="leading-relaxed">{{ $error }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-3 border-t border-red-200">
                            <p class="text-xs text-red-600 italic flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                                Silakan periksa dan perbaiki semua field yang ditandai dengan border merah di bawah ini.
                            </p>
                        </div>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="document.getElementById('error-container').style.display='none'"
                            class="text-red-400 hover:text-red-600 transition-colors">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Success Messages -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-md" id="success-container">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="document.getElementById('success-container').style.display='none'"
                            class="text-green-400 hover:text-green-600">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Error Messages -->
        @if (session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-md" id="error-session-container">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="document.getElementById('error-session-container').style.display='none'"
                            class="text-red-400 hover:text-red-600">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('rekam-medis-emergency.store') }}" method="POST" id="rekam-medis-emergency-form"
            onsubmit="return validateForm()">
            @csrf

            <!-- Data Pasien Section -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Data Karyawan External
                    </h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Pilih Karyawan External dengan Search -->
                        <div class="md:col-span-2">
                            <label for="search_karyawan" class="block text-sm font-semibold text-gray-700 mb-2">
                                Pilih Karyawan External <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="hidden" id="id_external_employee" name="external_employee_id" required>
                                <input type="text" id="search_karyawan"
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                                    placeholder="Cari karyawan external (Format: NIK-Nama Karyawan)..."
                                    autocomplete="off">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <!-- Search Results Dropdown for Karyawan -->
                                <div id="karyawan_search_results"
                                    class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                    <!-- Results will be populated by JavaScript -->
                                </div>
                            </div>
                            @error('external_employee_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Info Karyawan (Auto-filled) -->
                        <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Informasi Karyawan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <span class="text-xs text-gray-500">NIK Karyawan</span>
                                    <p id="info_nik" class="font-medium text-gray-900">-</p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">Nama Karyawan</span>
                                    <p id="info_nama" class="font-medium text-gray-900">-</p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">Departemen</span>
                                    <p id="info_departemen" class="font-medium text-gray-900">-</p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">Jenis Kelamin</span>
                                    <p id="info_jk" class="font-medium text-gray-900">-</p>
                                </div>
                            </div>
                        </div>

                        <!-- KODE RM (Auto-filled & Disabled) -->
                        <div>
                            <label for="kode_rm" class="block text-sm font-semibold text-gray-700 mb-2">
                                KODE RM
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                    </svg>
                                </div>
                                <input type="text" id="kode_rm" name="kode_rm"
                                    class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-gray-600"
                                    placeholder="Otomatis terisi" readonly>
                            </div>
                        </div>

                        <!-- Nama Pasien (Auto-filled) -->
                        <div>
                            <label for="nama_pasien" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Pasien
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <input type="text" id="nama_pasien"
                                    class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-gray-600"
                                    placeholder="Otomatis terisi" readonly>
                            </div>
                        </div>

                        <!-- Hubungan (Auto-filled) -->
                        <div>
                            <label for="hubungan" class="block text-sm font-semibold text-gray-700 mb-2">
                                Hubungan
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <input type="text" id="hubungan"
                                    class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-gray-600"
                                    placeholder="Otomatis terisi" readonly>
                            </div>
                        </div>

                        <!-- Jenis Kelamin (Auto-filled) -->
                        <div>
                            <label for="jenis_kelamin" class="block text-sm font-semibold text-gray-700 mb-2">
                                Jenis Kelamin
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <input type="text" id="jenis_kelamin"
                                    class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-gray-600"
                                    placeholder="Otomatis terisi" readonly>
                            </div>
                        </div>

                        <!-- Tanggal Periksa -->
                        <div>
                            <label for="tanggal_periksa" class="block text-sm font-semibold text-gray-700 mb-2">
                                Tanggal Periksa <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="date" id="tanggal_periksa" name="tanggal_periksa"
                                    value="{{ old('tanggal_periksa', date('Y-m-d')) }}"
                                    class="w-full pl-10 pr-4 py-2.5 border @error('tanggal_periksa') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                                    required>
                                @error('tanggal_periksa')
                                    <div class="mt-2 flex items-start">
                                        <svg class="w-5 h-5 text-red-500 mr-1.5 flex-shrink-0 mt-0.5" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <p class="text-sm text-red-600 font-medium">{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Waktu Periksa -->
                        <div>
                            <label for="waktu_periksa" class="block text-sm font-semibold text-gray-700 mb-2">
                                Waktu Periksa
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <input type="time" id="waktu_periksa" name="waktu_periksa"
                                    value="{{ old('waktu_periksa', \Carbon\Carbon::now('Asia/Makassar')->format('H:i')) }}"
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                                @error('waktu_periksa')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Status Rekam Medis -->
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                                Status Rekam Medis <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="status" name="status"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 appearance-none bg-white"
                                    required>
                                    <option value="On Progress"
                                        {{ old('status', 'On Progress') == 'On Progress' ? 'selected' : '' }}>On Progress
                                    </option>
                                    <option value="Close" {{ old('status') == 'Close' ? 'selected' : '' }}>Close</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diagnosa & Keluhan Section -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Diagnosa & Keluhan
                    </h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Diagnosa Emergency -->
                        <div>
                            <label for="id_diagnosa_emergency" class="block text-sm font-semibold text-gray-700 mb-2">
                                Diagnosa Emergency <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="id_diagnosa_emergency" name="id_diagnosa_emergency"
                                    class="w-full px-4 py-2.5 border @error('id_diagnosa_emergency') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 appearance-none bg-white"
                                    required onchange="loadObatByDiagnosaEmergency(this.value)">
                                    <option value="">-- Pilih Diagnosa Emergency --</option>
                                    @foreach ($diagnosaEmergency as $diagnosa)
                                        <option value="{{ $diagnosa->id_diagnosa_emergency }}"
                                            data-obats="{{ json_encode($diagnosa->obats->map(function ($obat) {return ['id_obat' => $obat->id_obat, 'nama_obat' => $obat->nama_obat];})) }}"
                                            {{ old('id_diagnosa_emergency') == $diagnosa->id_diagnosa_emergency ? 'selected' : '' }}>
                                            {{ $diagnosa->nama_diagnosa_emergency }}
                                        </option>
                                    @endforeach
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            @error('id_diagnosa_emergency')
                                <div class="mt-2 flex items-start">
                                    <svg class="w-5 h-5 text-red-500 mr-1.5 flex-shrink-0 mt-0.5" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <p class="text-sm text-red-600 font-medium">{{ $message }}</p>
                                </div>
                            @enderror
                        </div>

                        <!-- Terapi -->
                        <div>
                            <label for="terapi" class="block text-sm font-semibold text-gray-700 mb-2">
                                Terapi <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="terapi" name="terapi"
                                    class="w-full px-4 py-2.5 border @error('terapi') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 appearance-none bg-white"
                                    required onchange="toggleObatSection()">
                                    <option value="">-- Pilih Terapi --</option>
                                    <option value="Obat" {{ old('terapi') == 'Obat' ? 'selected' : '' }}>Obat</option>
                                    <option value="Lab" {{ old('terapi') == 'Lab' ? 'selected' : '' }}>Konsul Faskes
                                        Lanjutan</option>
                                    <option value="Istirahat" {{ old('terapi') == 'Istirahat' ? 'selected' : '' }}>
                                        Istirahat</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            @error('terapi')
                                <div class="mt-2 flex items-start">
                                    <svg class="w-5 h-5 text-red-500 mr-1.5 flex-shrink-0 mt-0.5" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <p class="text-sm text-red-600 font-medium">{{ $message }}</p>
                                </div>
                            @enderror
                        </div>

                        <!-- Keluhan -->
                        <div class="md:col-span-2">
                            <label for="keluhan" class="block text-sm font-semibold text-gray-700 mb-2">
                                Anamnesa <span class="text-red-500">*</span>
                            </label>
                            <textarea id="keluhan" name="keluhan" rows="4"
                                class="w-full px-4 py-2.5 border @error('keluhan') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                                placeholder="Deskripsikan keluhan pasien secara detail (minimal 10 karakter)..." required>{{ old('keluhan') }}</textarea>
                            @error('keluhan')
                                <div class="mt-2 flex items-start">
                                    <svg class="w-5 h-5 text-red-500 mr-1.5 flex-shrink-0 mt-0.5" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <p class="text-sm text-red-600 font-medium">{{ $message }}</p>
                                </div>
                            @enderror
                        </div>

                        <!-- Obat Section (Conditional) -->
                        <div class="md:col-span-2" id="obat-section" style="display: none;">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Obat yang Direkomendasikan
                            </label>
                            <div class="bg-gray-50 border border-gray-300 rounded-lg p-4">
                                <div id="obat-list-container">
                                    <p class="text-sm text-gray-500 italic">Pilih diagnosa emergency terlebih dahulu untuk
                                        menampilkan obat yang sesuai.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div class="md:col-span-2">
                            <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-2">
                                Catatan
                            </label>
                            <textarea id="catatan" name="catatan" rows="3"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                                placeholder="Masukkan catatan tambahan (opsional)">{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                    <button type="button" onclick="window.location.href='{{ route('rekam-medis-emergency.index') }}'"
                        class="px-6 py-2.5 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-medium rounded-lg transition-all hover:shadow-md">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Rekam Medis Emergency
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            let searchTimeout;

            // Function to load obat based on diagnosa emergency
            function loadObatByDiagnosaEmergency(diagnosaId) {
                const obatSection = document.getElementById('obat-section');
                const obatListContainer = document.getElementById('obat-list-container');
                const terapiSelect = document.getElementById('terapi');
                const diagnosaSelect = document.getElementById('id_diagnosa_emergency');

                // Reset obat list
                obatListContainer.innerHTML = '<p class="text-sm text-gray-500 italic">Memuat daftar obat...</p>';

                if (!diagnosaId) {
                    obatSection.style.display = 'none';
                    return;
                }

                // Check if terapi is "Obat"
                if (terapiSelect.value !== 'Obat') {
                    obatSection.style.display = 'none';
                    return;
                }

                // Get obat data directly from the selected option's data attribute
                const selectedOption = diagnosaSelect.options[diagnosaSelect.selectedIndex];
                const obatData = JSON.parse(selectedOption.getAttribute('data-obats') || '[]');

                if (obatData.length === 0) {
                    obatListContainer.innerHTML =
                        '<p class="text-sm text-gray-500 italic">Tidak ada obat yang tersedia untuk diagnosa ini.</p>';
                } else {
                    let checkboxHTML = '<div class="space-y-2">';
                    obatData.forEach(obat => {
                        checkboxHTML += `
                <label class="flex items-start space-x-3 p-2 hover:bg-white rounded cursor-pointer transition-colors">
                    <input type="checkbox"
                           class="obat-checkbox mt-1 w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                           value="${obat.id_obat}"
                           data-obat-name="${obat.nama_obat}"
                           onchange="updateSelectedObatList()">
                    <span class="text-sm text-gray-700 flex-1">${obat.nama_obat}</span>
                </label>
            `;
                    });
                    checkboxHTML += '</div>';

                    // Add selected obat details section
                    checkboxHTML += `
            <div id="selected-obat-details" class="mt-4 hidden">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-blue-900 mb-3">Detail Obat yang Dipilih</h4>
                    <div id="selected-obat-list" class="space-y-3">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        `;

                    obatListContainer.innerHTML = checkboxHTML;
                }
                obatSection.style.display = 'block';
            }

            // Function to toggle obat section based on terapi selection
            function toggleObatSection() {
                const terapiSelect = document.getElementById('terapi');
                const diagnosaSelect = document.getElementById('id_diagnosa_emergency');
                const obatSection = document.getElementById('obat-section');

                if (terapiSelect.value === 'Obat' && diagnosaSelect.value) {
                    loadObatByDiagnosaEmergency(diagnosaSelect.value);
                } else {
                    obatSection.style.display = 'none';
                }
            }

            // Function to update selected obat list with details
            function updateSelectedObatList() {
                const checkedBoxes = document.querySelectorAll('.obat-checkbox:checked');
                const detailsContainer = document.getElementById('selected-obat-details');
                const detailsList = document.getElementById('selected-obat-list');

                if (checkedBoxes.length === 0) {
                    detailsContainer.classList.add('hidden');
                    return;
                }

                detailsContainer.classList.remove('hidden');

                let detailsHTML = '';
                checkedBoxes.forEach((checkbox, index) => {
                    const obatId = checkbox.value;
                    const obatName = checkbox.getAttribute('data-obat-name');

                    detailsHTML += `
            <div class="border border-gray-300 rounded-lg p-3 bg-white">
                <h5 class="font-semibold text-sm text-gray-800 mb-2">${obatName}</h5>
                <input type="hidden" name="obat_list[${index}][id_obat]" value="${obatId}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                            Jumlah Obat
                        </label>
                        <input type="number"
                               name="obat_list[${index}][jumlah_obat]"
                               min="1"
                               max="10000"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
                               placeholder="Masukkan jumlah obat (maks 10.000)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3" />
                            </svg>
                            Aturan Pakai
                        </label>
                        <select name="obat_list[${index}][aturan_pakai]"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                            <option value="">-- Pilih Aturan Pakai --</option>
                            <option value="1 x sehari sebelum makan">1 x sehari sebelum makan</option>
                            <option value="1 x sehari sesudah makan">1 x sehari sesudah makan</option>
                            <option value="2 x sehari sebelum makan">2 x sehari sebelum makan</option>
                            <option value="2 x sehari setelah makan">2 x sehari setelah makan</option>
                            <option value="3 x sehari sebelum makan">3 x sehari sebelum makan</option>
                            <option value="3 x sehari sesudah makan">3 x sehari sesudah makan</option>
                            <option value="1 x pakai">1 x pakai</option>
                        </select>
                    </div>
                </div>
            </div>
        `;
                });

                detailsList.innerHTML = detailsHTML;
            }

            // Search karyawan external dengan AJAX
            document.getElementById('search_karyawan').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const searchValue = this.value.trim();

                // Clear hidden field when user types
                document.getElementById('id_external_employee').value = '';

                if (searchValue.length < 2) {
                    document.getElementById('karyawan_search_results').classList.add('hidden');
                    return;
                }

                searchTimeout = setTimeout(function() {
                    // Filter local data instead of AJAX for now
                    const employees = @json($externalEmployees);
                    console.log('All employees loaded:', employees); // Debug log

                    const filteredEmployees = employees.filter(employee =>
                        employee.nik_employee.toLowerCase().includes(searchValue.toLowerCase()) ||
                        employee.nama_employee.toLowerCase().includes(searchValue.toLowerCase())
                    );

                    console.log('Filtered employees:', filteredEmployees); // Debug log

                    const resultsDiv = document.getElementById('karyawan_search_results');

                    if (filteredEmployees.length === 0) {
                        resultsDiv.innerHTML =
                            '<div class="px-4 py-3 text-gray-500 text-sm">Tidak ada karyawan external ditemukan</div>';
                    } else {
                        resultsDiv.innerHTML = filteredEmployees.map(employee => {
                            // Use 'id' as primary key
                            const employeeId = employee.id;
                            console.log('Employee ID being set:', employeeId, 'from employee:',
                                employee); // Debug log

                            return `
                <div class="px-4 py-3 hover:bg-red-50 cursor-pointer border-b border-gray-100 transition-colors search-result-item"
                     data-id="${employeeId}"
                     data-nik="${employee.nik_employee}"
                     data-nama="${employee.nama_employee}"
                     data-vendor="${employee.vendor ? employee.vendor.nama_vendor : 'Tidak ada vendor'}"
                     data-kategori="${employee.kategori ? employee.kategori.nama_kategori : 'Tidak ada kategori'}"
                     data-kode-rm="${employee.kode_rm || ''}"
                     data-jenis-kelamin="${employee.jenis_kelamin}"
                     onclick="selectKaryawanFromSearch(this)">
                    <div class="font-medium text-gray-900">${employee.nik_employee} - ${employee.nama_employee}</div>
                    <div class="text-sm text-gray-600">Vendor: ${employee.vendor ? employee.vendor.nama_vendor : 'Tidak ada vendor'} | Kategori: ${employee.kategori ? employee.kategori.nama_kategori : 'Tidak ada kategori'}</div>
                </div>
            `;
                        }).join('');
                    }

                    resultsDiv.classList.remove('hidden');
                }, 300);
            });

            // Select karyawan external from dropdown
            function selectKaryawan(employee) {
                console.log('Selecting employee:', employee); // Debug log

                // Set karyawan values
                document.getElementById('id_external_employee').value = employee
                    .id; // Use correct primary key 'id'
                document.getElementById('search_karyawan').value = `${employee.nik_employee}-${employee.nama_employee}`;

                // Update info karyawan
                document.getElementById('info_nik').textContent = employee.nik_employee;
                document.getElementById('info_nama').textContent = employee.nama_employee;
                document.getElementById('info_departemen').textContent = employee.vendor ? employee.vendor.nama_vendor :
                    'Tidak ada vendor';
                document.getElementById('info_jk').textContent = employee.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';

                // Update form fields
                document.getElementById('kode_rm').value = employee.kode_rm || '';
                document.getElementById('nama_pasien').value = employee.nama_employee;
                document.getElementById('hubungan').value = 'Emergency';
                document.getElementById('jenis_kelamin').value = employee.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';

                // Clear any validation errors
                clearFieldError(document.getElementById('search_karyawan'));

                // Hide results
                document.getElementById('karyawan_search_results').classList.add('hidden');

                console.log('Employee selected successfully'); // Debug log
            }

            // Select karyawan from search results
            function selectKaryawanFromSearch(element) {
                // Get employee data from the clicked element
                const employeeData = {
                    id: element.getAttribute('data-id'),
                    nik_employee: element.getAttribute('data-nik'),
                    nama_employee: element.getAttribute('data-nama'),
                    vendor: {
                        nama_vendor: element.getAttribute('data-vendor')
                    },
                    kategori: {
                        nama_kategori: element.getAttribute('data-kategori')
                    },
                    kode_rm: element.getAttribute('data-kode-rm'),
                    jenis_kelamin: element.getAttribute('data-jenis-kelamin')
                };

                console.log('Employee data from search:', employeeData); // Debug log

                // Validate that id is not empty or undefined
                if (!employeeData.id || employeeData.id === 'undefined' || employeeData.id === '') {
                    console.error('Employee ID is empty or undefined:', employeeData.id);
                    showFieldError('search_karyawan', 'Data karyawan tidak valid. Silakan coba lagi.');
                    return;
                }

                // Set karyawan values
                document.getElementById('id_external_employee').value = employeeData.id;
                document.getElementById('search_karyawan').value = `${employeeData.nik_employee}-${employeeData.nama_employee}`;

                console.log('Hidden field value set to:', document.getElementById('id_external_employee').value); // Debug log

                // Update info karyawan
                document.getElementById('info_nik').textContent = employeeData.nik_employee;
                document.getElementById('info_nama').textContent = employeeData.nama_employee;
                document.getElementById('info_departemen').textContent = employeeData.vendor.nama_vendor;
                document.getElementById('info_jk').textContent = employeeData.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';

                // Update form fields
                document.getElementById('kode_rm').value = employeeData.kode_rm || '';
                document.getElementById('nama_pasien').value = employeeData.nama_employee;
                document.getElementById('hubungan').value = 'Emergency';
                document.getElementById('jenis_kelamin').value = employeeData.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';

                // Clear any validation errors
                clearFieldError(document.getElementById('search_karyawan'));

                // Hide results
                document.getElementById('karyawan_search_results').classList.add('hidden');

                console.log('Employee selected successfully from search'); // Debug log
            }

            // Hide search results when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#search_karyawan') && !e.target.closest('#karyawan_search_results')) {
                    document.getElementById('karyawan_search_results').classList.add('hidden');
                }
            });

            // Prevent event propagation on search results
            document.addEventListener('click', function(e) {
                if (e.target.closest('#karyawan_search_results > div')) {
                    e.stopPropagation();
                }
            });

            // Form validation function
            function validateForm() {
                let isValid = true;
                let errorMessages = [];

                // Clear previous error states
                clearValidationErrors();

                // Validate external employee selection
                const idExternalEmployee = document.getElementById('id_external_employee').value;
                if (!idExternalEmployee || idExternalEmployee === 'undefined') {
                    showFieldError('search_karyawan', 'Silakan pilih karyawan external terlebih dahulu');
                    errorMessages.push('Karyawan external harus dipilih');
                    isValid = false;
                }

                // Validate diagnosa emergency selection
                const idDiagnosaEmergency = document.getElementById('id_diagnosa_emergency').value;
                if (!idDiagnosaEmergency) {
                    showFieldError('id_diagnosa_emergency', 'Silakan pilih diagnosa emergency terlebih dahulu');
                    errorMessages.push('Diagnosa emergency harus dipilih');
                    isValid = false;
                }

                // Validate terapi selection
                const terapi = document.getElementById('terapi').value;
                if (!terapi) {
                    showFieldError('terapi', 'Silakan pilih terapi terlebih dahulu');
                    errorMessages.push('Terapi harus dipilih');
                    isValid = false;
                }

                // Validate keluhan
                const keluhan = document.getElementById('keluhan').value;
                if (!keluhan || keluhan.trim().length < 3) {
                    showFieldError('keluhan', 'Keluhan harus diisi dan minimal 3 karakter');
                    errorMessages.push('Keluhan harus diisi dan minimal 3 karakter');
                    isValid = false;
                }

                // Validate tanggal periksa
                const tanggalPeriksa = document.getElementById('tanggal_periksa').value;
                if (!tanggalPeriksa) {
                    showFieldError('tanggal_periksa', 'Tanggal periksa harus diisi');
                    errorMessages.push('Tanggal periksa harus diisi');
                    isValid = false;
                } else {
                    // Check if date is not in the future
                    const [year, month, day] = tanggalPeriksa.split('-');
                    const selectedDate = new Date(year, month - 1, day);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    if (selectedDate > today) {
                        showFieldError('tanggal_periksa', 'Tanggal periksa tidak boleh melebihi hari ini');
                        errorMessages.push('Tanggal periksa tidak boleh melebihi hari ini');
                        isValid = false;
                    }
                }

                // Show validation summary if there are errors
                if (!isValid) {
                    showValidationSummary(errorMessages);
                    // Scroll to first error
                    const firstError = document.querySelector('.field-error, .section-error');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }

                return isValid;
            }

            // Show field error
            function showFieldError(fieldId, message) {
                const field = document.getElementById(fieldId);
                if (!field) return;

                field.classList.add('border-red-500', 'bg-red-50');

                // Create or update error message
                let errorDiv = field.parentNode.querySelector('.field-error-message');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'field-error-message text-sm text-red-600 mt-1 flex items-center';
                    errorDiv.innerHTML = `
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span>${message}</span>
        `;
                    field.parentNode.appendChild(errorDiv);
                } else {
                    errorDiv.querySelector('span').textContent = message;
                }

                // Add focus event to clear error when user starts typing
                field.addEventListener('focus', function() {
                    clearFieldError(field);
                }, {
                    once: true
                });
            }

            // Show validation summary
            function showValidationSummary(errors) {
                // Remove existing summary if any
                const existingSummary = document.getElementById('validation-summary');
                if (existingSummary) {
                    existingSummary.remove();
                }

                // Create new summary
                const summaryDiv = document.createElement('div');
                summaryDiv.id = 'validation-summary';
                summaryDiv.className = 'mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-md';
                summaryDiv.innerHTML = `
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-red-800 font-semibold">Mohon perbaiki kesalahan berikut:</h3>
                <div class="mt-2">
                    ${errors.map(error => `
                                                                                                <div class="flex items-center py-1">
                                                                                                    <svg class="h-4 w-4 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                                                                    </svg>
                                                                                                    <span class="text-sm text-red-700">${error}</span>
                                                                                                </div>
                                                                                            `).join('')}
                </div>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="document.getElementById('validation-summary').remove()" class="text-red-400 hover:text-red-600">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    `;

                // Insert after the header section
                const headerSection = document.querySelector('.mb-6');
                headerSection.parentNode.insertBefore(summaryDiv, headerSection.nextSibling);

                // Scroll to summary
                summaryDiv.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }

            // Clear field error
            function clearFieldError(field) {
                if (!field) return;

                field.classList.remove('border-red-500', 'bg-red-50');

                const errorDiv = field.parentNode.querySelector('.field-error-message');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }

            // Clear all validation errors
            function clearValidationErrors() {
                // Remove field errors
                document.querySelectorAll('.field-error-message').forEach(el => el.remove());
                document.querySelectorAll('.border-red-500').forEach(el => {
                    el.classList.remove('border-red-500', 'bg-red-50');
                });

                // Remove section errors
                document.querySelectorAll('.section-error').forEach(el => el.remove());

                // Remove validation summary
                const summary = document.getElementById('validation-summary');
                if (summary) {
                    summary.remove();
                }
            }

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                // Preserve token session when validation errors occur
                @if ($errors->any())
                    // If there are validation errors, ensure token is still in session
                    // This prevents the token from being lost when form validation fails
                    console.log('Validation errors detected, preserving token session');

                    // Show notification that token is still active
                    setTimeout(() => {
                        showNotification(
                            'Token emergency Anda masih aktif. Silakan perbaiki kesalahan dan coba lagi.',
                            'info');
                    }, 1000);
                @endif
                // Add real-time validation for critical fields
                document.getElementById('search_karyawan').addEventListener('blur', function() {
                    const idExternalEmployee = document.getElementById('id_external_employee').value;
                    if (!idExternalEmployee) {
                        showFieldError('search_karyawan', 'Silakan pilih karyawan external dari daftar');
                    } else {
                        clearFieldError(this);
                    }
                });

                // Add change event listener to clear validation when employee is selected
                document.getElementById('id_external_employee').addEventListener('change', function() {
                    if (this.value) {
                        clearFieldError(document.getElementById('search_karyawan'));
                    }
                });

                document.getElementById('id_diagnosa_emergency').addEventListener('blur', function() {
                    const idDiagnosaEmergency = this.value;
                    if (!idDiagnosaEmergency) {
                        showFieldError('id_diagnosa_emergency', 'Silakan pilih diagnosa emergency');
                    } else {
                        clearFieldError(this);
                    }
                });

                document.getElementById('keluhan').addEventListener('blur', function() {
                    const keluhan = this.value;
                    if (!keluhan || keluhan.trim().length < 3) {
                        showFieldError('keluhan', 'Keluhan harus diisi dan minimal 3 karakter');
                    } else {
                        clearFieldError(this);
                    }
                });

                document.getElementById('tanggal_periksa').addEventListener('change', function() {
                    const [year, month, day] = this.value.split('-');
                    const selectedDate = new Date(year, month - 1, day);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    if (selectedDate > today) {
                        showFieldError('tanggal_periksa', 'Tanggal periksa tidak boleh melebihi hari ini');
                    } else {
                        clearFieldError(this);
                    }
                });

                // Initialize employee info if there's a selected value
                const idEmployee = document.getElementById('id_external_employee').value;
                if (idEmployee) {
                    // Find the employee data from the externalEmployees array
                    const employees = @json($externalEmployees);
                    const selectedEmployee = employees.find(emp => emp.id ==
                        idEmployee); // Use correct primary key 'id'
                    if (selectedEmployee) {
                        selectKaryawanFromSearch({
                            getAttribute: (attr) => {
                                switch (attr) {
                                    case 'data-id':
                                        return selectedEmployee.id;
                                    case 'data-nik':
                                        return selectedEmployee.nik_employee;
                                    case 'data-nama':
                                        return selectedEmployee.nama_employee;
                                    case 'data-vendor':
                                        return selectedEmployee.vendor ? selectedEmployee.vendor
                                            .nama_vendor : 'Tidak ada vendor';
                                    case 'data-kategori':
                                        return selectedEmployee.kategori ? selectedEmployee.kategori
                                            .nama_kategori : 'Tidak ada kategori';
                                    case 'data-kode-rm':
                                        return selectedEmployee.kode_rm || '';
                                    case 'data-jenis-kelamin':
                                        return selectedEmployee.jenis_kelamin;
                                    default:
                                        return '';
                                }
                            }
                        });
                    }
                }

                // Initialize obat section if there's a selected diagnosa
                const selectedDiagnosaId = document.getElementById('id_diagnosa_emergency').value;
                const selectedTerapi = document.getElementById('terapi').value;
                if (selectedDiagnosaId && selectedTerapi === 'Obat') {
                    loadObatByDiagnosaEmergency(selectedDiagnosaId);
                }

                // Add confirmation before leaving page if form has changes
                let formChanged = false;
                const form = document.getElementById('rekam-medis-emergency-form');

                form.addEventListener('change', function() {
                    formChanged = true;
                });

                window.addEventListener('beforeunload', function(e) {
                    if (formChanged) {
                        e.preventDefault();
                        e.returnValue = '';
                    }
                });

                // Reset form changed flag on submit
                form.addEventListener('submit', function() {
                    formChanged = false;
                });

                // Function to show notification (if not already defined)
                if (typeof showNotification === 'undefined') {
                    function showNotification(message, type) {
                        // Create notification element
                        const notification = document.createElement('div');
                        notification.className =
                            `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;

                        // Set styling based on type
                        if (type === 'success') {
                            notification.classList.add('bg-green-500', 'text-white');
                        } else if (type === 'error') {
                            notification.classList.add('bg-red-500', 'text-white');
                        } else if (type === 'info') {
                            notification.classList.add('bg-blue-500', 'text-white');
                        }

                        notification.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        ${type === 'success'
                            ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
                            : type === 'error'
                            ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>'
                            : '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>'
                        }
                    </svg>
                    <span>${message}</span>
                </div>
            `;

                        // Add to document
                        document.body.appendChild(notification);

                        // Animate in
                        setTimeout(() => {
                            notification.classList.remove('translate-x-full');
                            notification.classList.add('translate-x-0');
                        }, 100);

                        // Remove after 5 seconds for info, 3 seconds for others
                        const removeTime = type === 'info' ? 5000 : 3000;
                        setTimeout(() => {
                            notification.classList.add('translate-x-full');
                            setTimeout(() => {
                                if (document.body.contains(notification)) {
                                    document.body.removeChild(notification);
                                }
                            }, 300);
                        }, removeTime);
                    }
                }
            });
        </script>
    @endpush
@endsection
