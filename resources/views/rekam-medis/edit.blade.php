@extends('layouts.app')

@section('page-title', 'Edit Rekam Medis')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-3">
                <a href="{{ route('rekam-medis.index') }}" class="p-2 hover:bg-white rounded-lg transition-colors">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 p-3 rounded-lg shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        Edit Rekam Medis
                    </h1>
                    <p class="text-gray-600 mt-1 ml-1">Ubah data rekam medis pasien</p>
                </div>
            </div>
        </div>

        <!-- Enhanced Error Messages -->
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-md" id="error-container">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm text-red-800 font-semibold">Mohon perbaiki kesalahan berikut:</h3>
                        <div class="mt-2">
                            @foreach ($errors->all() as $error)
                                <div class="flex items-center py-1">
                                    <svg class="h-4 w-4 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm text-red-700">{{ $error }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="document.getElementById('error-container').style.display='none'"
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

        <form action="{{ route('rekam-medis.update', $rekamMedis->id_rekam) }}" method="POST" id="rekamMedisForm">
            @csrf
            @method('PUT')

            <!-- Hidden field for kunjungan_id -->
            <input type="hidden" id="kunjungan_id" name="kunjungan_id"
                value="{{ old('kunjungan_id', $rekamMedis->kunjungan_id) }}">

            <!-- Data Pasien Section -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Data Pasien
                    </h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Pilih Karyawan dengan Search -->
                        <div class="md:col-span-2">
                            <label for="search_karyawan" class="block text-sm font-semibold text-gray-700 mb-2">
                                Pilih Karyawan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="hidden" id="id_karyawan" name="id_karyawan"
                                    value="{{ old('id_karyawan', $rekamMedis->keluarga->id_karyawan) }}" required>
                                <input type="text" id="search_karyawan"
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                    placeholder="Cari karyawan (Format: NIK-Nama Karyawan)..." autocomplete="off"
                                    value="{{ ($rekamMedis->keluarga->karyawan->nik_karyawan ?? '') . '-' . ($rekamMedis->keluarga->karyawan->nama_karyawan ?? '') }}">
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
                        </div>

                        <!-- Info Karyawan (Auto-filled) -->
                        <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Informasi Karyawan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <span class="text-xs text-gray-500">NIK Karyawan</span>
                                    <p id="info_nik" class="font-medium text-gray-900">
                                        {{ $rekamMedis->keluarga->karyawan->nik_karyawan ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">Nama Karyawan</span>
                                    <p id="info_nama" class="font-medium text-gray-900">
                                        {{ $rekamMedis->keluarga->karyawan->nama_karyawan ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">Departemen</span>
                                    <p id="info_departemen" class="font-medium text-gray-900">
                                        {{ $rekamMedis->keluarga->karyawan->departemen->nama_departemen ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">Foto Karyawan</span>
                                    <div id="info_foto" class="mt-1">
                                        @if ($rekamMedis->keluarga->karyawan->foto)
                                            <img id="foto_karyawan"
                                                src="/storage/{{ $rekamMedis->keluarga->karyawan->foto }}"
                                                alt="Foto Karyawan"
                                                class="w-20 h-24 object-cover rounded-lg border border-gray-300"
                                                onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($rekamMedis->keluarga->karyawan->nama_karyawan ?? 'Unknown') }}&background=6b7280&color=fff&size=80'">
                                        @else
                                            <div id="no_foto"
                                                class="w-20 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pilih Anggota Keluarga -->
                        <div class="md:col-span-2">
                            <label for="id_keluarga" class="block text-sm font-semibold text-gray-700 mb-2">
                                Pilih Anggota Keluarga <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="id_keluarga" name="id_keluarga"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white"
                                    required>
                                    <option value="">-- Pilih karyawan terlebih dahulu --</option>
                                    @if ($rekamMedis->keluarga)
                                        <option value="{{ $rekamMedis->keluarga->id_keluarga }}" selected
                                            data-no-rm="{{ $rekamMedis->keluarga->no_rm ?? '' }}"
                                            data-jenis-kelamin="{{ $rekamMedis->keluarga->jenis_kelamin ?? '' }}"
                                            data-hubungan="{{ $rekamMedis->keluarga->hubungan->hubungan ?? '' }}"
                                            data-kode-hubungan="{{ $rekamMedis->keluarga->kode_hubungan ?? '' }}">
                                            {{ $rekamMedis->keluarga->nama_keluarga }}
                                            ({{ $rekamMedis->keluarga->hubungan->hubungan ?? '' }})
                                        </option>
                                    @endif
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- NO RM (Auto-filled & Disabled) -->
                        <div>
                            <label for="no_rm" class="block text-sm font-semibold text-gray-700 mb-2">
                                NO RM
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                    </svg>
                                </div>
                                <input type="text" id="no_rm" name="no_rm"
                                    class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-gray-600"
                                    placeholder="Otomatis terisi" readonly
                                    value="{{ $rekamMedis->keluarga->no_rm ?? '' }}">
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
                                    placeholder="Otomatis terisi" readonly
                                    value="{{ $rekamMedis->keluarga->nama_keluarga ?? '' }}">
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
                                    placeholder="Otomatis terisi" readonly
                                    value="{{ $rekamMedis->keluarga->hubungan->hubungan ?? '' }}">
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
                                    placeholder="Otomatis terisi" readonly
                                    value="{{ $rekamMedis->keluarga->jenis_kelamin ?? '' }}">
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
                                    value="{{ old('tanggal_periksa', $rekamMedis->tanggal_periksa->format('Y-m-d')) }}"
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                    required>
                            </div>
                        </div>

                        <!-- Waktu Periksa -->
                        <div>
                            <label for="waktu_periksa" class="block text-sm font-semibold text-gray-700 mb-2">
                                Waktu Periksa <span class="text-red-500">*</span>
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
                                    value="{{ old('waktu_periksa', $rekamMedis->waktu_periksa ? date('H:i', strtotime($rekamMedis->waktu_periksa)) : '') }}"
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                    required>
                            </div>
                        </div>

                        <!-- Status Rekam Medis -->
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                                Status Rekam Medis <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="status" name="status"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white"
                                    required>
                                    <option value="On Progress"
                                        {{ old('status', $rekamMedis->status) == 'On Progress' ? 'selected' : '' }}>On
                                        Progress</option>
                                    <option value="Close"
                                        {{ old('status', $rekamMedis->status) == 'Close' ? 'selected' : '' }}>Close
                                    </option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Jumlah Keluhan -->
                        <div>
                            <label for="jumlah_keluhan" class="block text-sm font-semibold text-gray-700 mb-2">
                                Jumlah Keluhan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="jumlah_keluhan" name="jumlah_keluhan"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white"
                                    required onchange="updateKeluhanSections(this.value)">
                                    <option value="1"
                                        {{ old('jumlah_keluhan', $rekamMedis->jumlah_keluhan) == 1 ? 'selected' : '' }}>1
                                        Keluhan</option>
                                    <option value="2"
                                        {{ old('jumlah_keluhan', $rekamMedis->jumlah_keluhan) == 2 ? 'selected' : '' }}>2
                                        Keluhan</option>
                                    <option value="3"
                                        {{ old('jumlah_keluhan', $rekamMedis->jumlah_keluhan) == 3 ? 'selected' : '' }}>3
                                        Keluhan</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Combined Diagnosa & Resep Section -->
            <div id="keluhan-container">
                <!-- Keluhan sections will be populated by JavaScript based on existing data -->
            </div>

            <!-- Form Actions -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                    <button type="button" onclick="window.location.href='{{ route('rekam-medis.index') }}'"
                        class="px-6 py-2.5 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-medium rounded-lg transition-all hover:shadow-md">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Rekam Medis
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            let searchTimeout;

            // Data keluhan existing dari server
            const existingKeluhans = @json($rekamMedis->keluhans ?? []);

            // Search karyawan dengan AJAX
            document.getElementById('search_karyawan').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const searchValue = this.value.trim();

                if (searchValue.length < 2) {
                    document.getElementById('karyawan_search_results').classList.add('hidden');
                    return;
                }

                searchTimeout = setTimeout(function() {
                    fetch(`{{ route('rekam-medis.searchKaryawan') }}?q=${encodeURIComponent(searchValue)}`)
                        .then(response => response.json())
                        .then(data => {
                            const resultsDiv = document.getElementById('karyawan_search_results');

                            if (data.length === 0) {
                                resultsDiv.innerHTML =
                                    '<div class="px-4 py-3 text-gray-500 text-sm">Tidak ada karyawan ditemukan</div>';
                            } else {
                                resultsDiv.innerHTML = data.map(karyawan => `
                        <div class="px-4 py-3 hover:bg-green-50 cursor-pointer border-b border-gray-100 transition-colors" onclick="selectKaryawan(${JSON.stringify(karyawan).replace(/"/g, '"')})">
                            <div class="font-medium text-gray-900">${karyawan.nik_karyawan} - ${karyawan.nama_karyawan}</div>
                            <div class="text-sm text-gray-600">Departemen: ${karyawan.nama_departemen || '-'}</div>
                        </div>
                    `).join('');
                            }

                            resultsDiv.classList.remove('hidden');
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }, 300);
            });

            // Select karyawan from dropdown
            function selectKaryawan(karyawan) {
                // Set karyawan values
                document.getElementById('id_karyawan').value = karyawan.id_karyawan;
                document.getElementById('search_karyawan').value = `${karyawan.nik_karyawan}-${karyawan.nama_karyawan}`;

                // Update info karyawan
                document.getElementById('info_nik').textContent = karyawan.nik_karyawan;
                document.getElementById('info_nama').textContent = karyawan.nama_karyawan;
                document.getElementById('info_departemen').textContent = karyawan.nama_departemen;

                // Update foto karyawan
                const fotoElement = document.getElementById('foto_karyawan');
                const noFotoElement = document.getElementById('no_foto');

                if (karyawan.foto) {
                    fotoElement.src = `/storage/${karyawan.foto}`;
                    fotoElement.classList.remove('hidden');
                    noFotoElement.classList.add('hidden');
                } else {
                    fotoElement.classList.add('hidden');
                    noFotoElement.classList.remove('hidden');
                }

                // Reset patient info fields when karyawan changes
                document.getElementById('nama_pasien').value = '';
                document.getElementById('no_rm').value = '';
                document.getElementById('jenis_kelamin').value = '';
                document.getElementById('hubungan').value = '';

                // Load family members for this employee
                loadFamilyMembers(karyawan.id_karyawan);

                // Hide results
                document.getElementById('karyawan_search_results').classList.add('hidden');
            }

            function loadFamilyMembers(karyawanId) {
                fetch(`{{ route('rekam-medis.getFamilyMembers') }}?karyawan_id=${karyawanId}`)
                    .then(response => response.json())
                    .then(data => {
                        const selectElement = document.getElementById('id_keluarga');
                        const currentValue = selectElement.value;
                        selectElement.innerHTML = '<option value="">-- Pilih Anggota Keluarga --</option>';

                        if (data.length > 0) {
                            data.forEach(member => {
                                const option = document.createElement('option');
                                option.value = member.id_keluarga;
                                option.textContent = `${member.nama_keluarga} (${member.hubungan})`;
                                option.setAttribute('data-no-rm', member.no_rm || '');
                                option.setAttribute('data-jenis-kelamin', member.jenis_kelamin || '');
                                option.setAttribute('data-hubungan', member.hubungan || '');
                                // Tambahkan atribut untuk kode hubungan yang akan digunakan generate NO RM
                                option.setAttribute('data-kode-hubungan', member.kode_hubungan || '');

                                // Keep current selection if it matches
                                if (member.id_keluarga == currentValue) {
                                    option.selected = true;
                                }

                                selectElement.appendChild(option);
                            });
                            selectElement.disabled = false;

                            // If there's a current selection, update the patient info
                            if (currentValue) {
                                selectKeluarga();
                            }
                        } else {
                            selectElement.innerHTML = '<option value="">-- Tidak ada anggota keluarga --</option>';
                            selectElement.disabled = true;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const selectElement = document.getElementById('id_keluarga');
                        selectElement.innerHTML = '<option value="">-- Error memuat data --</option>';
                        selectElement.disabled = true;
                    });
            }

            // Fungsi untuk menghasilkan NO RM dari NIK dan Kode Hubungan
            function generateNoRM(nik, kodeHubungan) {
                if (nik && kodeHubungan) {
                    return `${nik}-${kodeHubungan}`;
                }
                return '';
            }

            function selectKeluarga() {
                const selectElement = document.getElementById('id_keluarga');
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                const nikKaryawan = document.getElementById('info_nik').textContent;

                if (selectedOption.value) {
                    // Update patient information
                    document.getElementById('nama_pasien').value = selectedOption.textContent.split(' (')[0];
                    document.getElementById('jenis_kelamin').value = selectedOption.getAttribute('data-jenis-kelamin') || '';
                    document.getElementById('hubungan').value = selectedOption.getAttribute('data-hubungan') || '';

                    // Generate NO RM otomatis dari NIK-Kode Hubungan
                    const kodeHubungan = selectedOption.getAttribute('data-kode-hubungan') || '';
                    const noRM = generateNoRM(nikKaryawan, kodeHubungan);
                    document.getElementById('no_rm').value = noRM;
                } else {
                    // Clear patient information
                    document.getElementById('nama_pasien').value = '';
                    document.getElementById('no_rm').value = '';
                    document.getElementById('jenis_kelamin').value = '';
                    document.getElementById('hubungan').value = '';
                }
            }

            // Hide search results when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#search_karyawan') && !e.target.closest('#karyawan_search_results')) {
                    document.getElementById('karyawan_search_results').classList.add('hidden');
                }
            });

            // Add event listener for keluarga dropdown
            document.getElementById('id_keluarga').addEventListener('change', selectKeluarga);

            // Handle multiple keluhan sections
            function updateKeluhanSections(value) {
                const container = document.getElementById('keluhan-container');

                // Clear container
                container.innerHTML = '';

                // Create template for keluhan section
                const template = createKeluhanTemplate();

                // Group existing keluhans by diagnosa to avoid duplicates
                const processedDiagnosas = new Set();
                let keluhanIndex = 0;

                // Create sections based on selected value or existing data
                existingKeluhans.forEach((keluhanData, index) => {
                    // Skip if we've already processed this diagnosa or reached the limit
                    if (processedDiagnosas.has(keluhanData.id_diagnosa) || keluhanIndex >= value) {
                        return;
                    }

                    processedDiagnosas.add(keluhanData.id_diagnosa);

                    const newSection = template.cloneNode(true);
                    newSection.setAttribute('data-keluhan-index', keluhanIndex);

                    // Update section title
                    const title = newSection.querySelector('.keluhan-number');
                    title.textContent = `(Keluhan ${keluhanIndex + 1})`;

                    // Update form field names with proper index
                    newSection.querySelectorAll('select, input, textarea').forEach(element => {
                        if (element.name && element.name.includes('keluhan[')) {
                            element.name = element.name.replace(/keluhan\[\d+\]/, `keluhan[${keluhanIndex}]`);
                        }
                        // Update data-keluhan-index for diagnosa selects
                        if (element.classList.contains('diagnosa-select')) {
                            element.setAttribute('data-keluhan-index', keluhanIndex);
                        }
                    });

                    // Update data-keluhan-index for obat containers
                    const obatContainer = newSection.querySelector('.obat-checkbox-container');
                    if (obatContainer) {
                        obatContainer.setAttribute('data-keluhan-index', keluhanIndex);
                    }

                    const detailsContainer = newSection.querySelector('.selected-obat-details');
                    if (detailsContainer) {
                        detailsContainer.setAttribute('data-keluhan-index', keluhanIndex);
                    }

                    // Fill with existing data
                    fillKeluhanWithData(newSection, keluhanData, keluhanIndex);

                    container.appendChild(newSection);
                    keluhanIndex++;
                });

                // Add empty sections if we need more
                while (keluhanIndex < value) {
                    const newSection = template.cloneNode(true);
                    newSection.setAttribute('data-keluhan-index', keluhanIndex);

                    // Update section title
                    const title = newSection.querySelector('.keluhan-number');
                    title.textContent = `(Keluhan ${keluhanIndex + 1})`;

                    // Update form field names with proper index
                    newSection.querySelectorAll('select, input, textarea').forEach(element => {
                        if (element.name && element.name.includes('keluhan[')) {
                            element.name = element.name.replace(/keluhan\[\d+\]/, `keluhan[${keluhanIndex}]`);
                        }
                        // Update data-keluhan-index for diagnosa selects
                        if (element.classList.contains('diagnosa-select')) {
                            element.setAttribute('data-keluhan-index', keluhanIndex);
                        }
                    });

                    // Update data-keluhan-index for obat containers
                    const obatContainer = newSection.querySelector('.obat-checkbox-container');
                    if (obatContainer) {
                        obatContainer.setAttribute('data-keluhan-index', keluhanIndex);
                    }

                    const detailsContainer = newSection.querySelector('.selected-obat-details');
                    if (detailsContainer) {
                        detailsContainer.setAttribute('data-keluhan-index', keluhanIndex);
                    }

                    container.appendChild(newSection);
                    keluhanIndex++;
                }

                // Re-attach event listeners for all diagnosa selects and terapi selects
                attachDiagnosaChangeListeners();
            }

            function createKeluhanTemplate() {
                const templateDiv = document.createElement('div');
                templateDiv.className =
                    'keluhan-section bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6';
                templateDiv.innerHTML = `
        <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-4">
            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Diagnosa & Resep Obat
                <span class="keluhan-number">(Keluhan 1)</span>
            </h2>
        </div>

        <div class="p-6">
            <!-- Diagnosa Section -->
            <div class="mb-6 pb-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Diagnosa</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Diagnosa / Penyakit -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Diagnosa / Penyakit <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="keluhan[0][id_diagnosa]" class="diagnosa-select w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white" required data-keluhan-index="0">
                                <option value="">-- Pilih Diagnosa --</option>
                                @foreach ($diagnosas as $diagnosa)
                                    <option value="{{ $diagnosa->id_diagnosa }}">{{ $diagnosa->nama_diagnosa }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Terapi -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Terapi <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="keluhan[0][terapi]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white" required>
                                <option value="">-- Pilih Terapi --</option>
                                <option value="Obat">Obat</option>
                                <option value="Lab">Lab</option>
                                <option value="Istirahat">Istirahat</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Anamnesa
                        </label>
                        <textarea name="keluhan[0][keterangan]" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Masukkan catatan medis, anjuran dokter, atau informasi penting lainnya..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Resep Obat Section -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Resep Obat (Opsional)</h3>

                <!-- Obat Checkbox List Container -->
                <div class="obat-checkbox-container mb-4" data-keluhan-index="0">
                    <div class="obat-list bg-gray-50 border border-gray-300 rounded-lg p-4 max-h-60 overflow-y-auto">
                        <p class="text-sm text-gray-500 italic">Pilih diagnosa terlebih dahulu untuk menampilkan daftar obat yang sesuai.</p>
                    </div>
                </div>

                <!-- Details for selected obat (will be shown when obat is selected) -->
                <div class="selected-obat-details mt-4" data-keluhan-index="0" style="display: none;">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-blue-900 mb-3">Detail Obat yang Dipilih</h4>
                        <div class="obat-details-list space-y-3">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

                return templateDiv;
            }

            function fillKeluhanWithData(section, keluhanData, index) {
                const diagnosaSelect = section.querySelector(`select[name="keluhan[${index}][id_diagnosa]"]`);
                const terapiSelect = section.querySelector(`select[name="keluhan[${index}][terapi]"]`);
                const keteranganInput = section.querySelector(`textarea[name="keluhan[${index}][keterangan]"]`);

                if (diagnosaSelect) diagnosaSelect.value = keluhanData.id_diagnosa || '';
                if (terapiSelect) terapiSelect.value = keluhanData.terapi || '';
                if (keteranganInput && keluhanData.keterangan) keteranganInput.value = keluhanData.keterangan;

                // Jika diagnosa ada, tampilkan obat
                if (keluhanData.id_diagnosa) {
                    handleDiagnosaChange({
                        target: diagnosaSelect
                    });

                    // Tahap 1: tandai checkbox dulu setelah daftar obat termuat
                    setTimeout(() => {
                        const keluhansUntukDiagnosaIni = existingKeluhans.filter(
                            k => k.id_diagnosa === keluhanData.id_diagnosa
                        );

                        keluhansUntukDiagnosaIni.forEach(keluhanObat => {
                            const checkbox = section.querySelector(
                                `input[type="checkbox"][value="${keluhanObat.id_obat}"]`
                            );
                            if (checkbox) {
                                checkbox.checked = true;
                            }
                        });

                        // Tahap 2: tampilkan form detail obat
                        updateObatDetails(index);

                        // Tahap 3: isi ulang jumlah_obat & aturan_pakai
                        setTimeout(() => {
                            const checkedBoxes = section.querySelectorAll(
                                `.obat-checkbox[data-keluhan-index="${index}"]:checked`);
                            let obatIndex = 0;

                            // Urutkan keluhansUntukDiagnosaIni agar sesuai dengan urutan checkbox yang dicentang
                            const sortedKeluhans = [];
                            checkedBoxes.forEach(checkbox => {
                                const obatId = checkbox.value;
                                const matchingKeluhan = keluhansUntukDiagnosaIni.find(k => k.id_obat ==
                                    obatId);
                                if (matchingKeluhan) {
                                    sortedKeluhans.push(matchingKeluhan);
                                }
                            });

                            sortedKeluhans.forEach(keluhanObat => {
                                const jumlahInput = section.querySelector(
                                    `input[name="keluhan[${index}][obat_list][${obatIndex}][jumlah_obat]"]`
                                );
                                const aturanInput = section.querySelector(
                                    `input[name="keluhan[${index}][obat_list][${obatIndex}][aturan_pakai]"]`
                                );

                                if (jumlahInput && keluhanObat.jumlah_obat) {
                                    jumlahInput.value = keluhanObat.jumlah_obat;
                                }
                                if (aturanInput && keluhanObat.aturan_pakai) {
                                    aturanInput.value = keluhanObat.aturan_pakai;
                                }

                                obatIndex++;
                            });
                        }, 500); // tunggu hingga detail obat muncul
                    }, 600); // tunggu daftar obat muncul dulu
                }
            }




            // Function to handle diagnosa change and show obat checkboxes
            function handleDiagnosaChange(event) {
                const diagnosaSelect = event.target;
                const diagnosaId = diagnosaSelect.value;
                const keluhanIndex = diagnosaSelect.getAttribute('data-keluhan-index');

                // Find the corresponding obat container in the same keluhan section
                const keluhanSection = diagnosaSelect.closest('.keluhan-section');
                const obatContainer = keluhanSection.querySelector('.obat-checkbox-container[data-keluhan-index="' +
                    keluhanIndex + '"]');
                const obatList = obatContainer.querySelector('.obat-list');
                const detailsContainer = keluhanSection.querySelector('.selected-obat-details[data-keluhan-index="' +
                    keluhanIndex + '"]');

                // Find the terapi select in the same section
                const terapiSelect = keluhanSection.querySelector(`select[name="keluhan[${keluhanIndex}][terapi]"]`);
                const terapiValue = terapiSelect ? terapiSelect.value : '';

                if (!diagnosaId) {
                    // Reset obat list if no diagnosa selected
                    obatList.innerHTML =
                        '<p class="text-sm text-gray-500 italic">Pilih diagnosa terlebih dahulu untuk menampilkan daftar obat yang sesuai.</p>';
                    detailsContainer.style.display = 'none';
                    return;
                }

                // Only show obat recommendations if terapi is "Obat"
                if (terapiValue !== 'Obat') {
                    obatList.innerHTML =
                        '<p class="text-sm text-gray-500 italic">Rekomendasi obat hanya tersedia untuk terapi "Obat".</p>';
                    detailsContainer.style.display = 'none';
                    return;
                }

                // Show loading state
                obatList.innerHTML = '<p class="text-sm text-gray-500 italic">Memuat daftar obat...</p>';

                // Fetch obat based on selected diagnosa
                fetch(`{{ route('rekam-medis.getObatByDiagnosa') }}?diagnosa_id=${diagnosaId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            // Build checkbox list
                            let checkboxHTML = '<div class="space-y-2">';
                            data.forEach(obat => {
                                checkboxHTML += `
                        <label class="flex items-start space-x-3 p-2 hover:bg-white rounded cursor-pointer transition-colors">
                            <input type="checkbox"
                                   class="obat-checkbox mt-1 w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                   value="${obat.id_obat}"
                                   data-obat-name="${obat.nama_obat}"
                                   data-keluhan-index="${keluhanIndex}"
                                   onchange="updateObatDetails(${keluhanIndex})">
                            <span class="text-sm text-gray-700 flex-1">${obat.nama_obat}</span>
                        </label>
                    `;
                            });
                            checkboxHTML += '</div>';
                            obatList.innerHTML = checkboxHTML;
                        } else {
                            obatList.innerHTML =
                                '<p class="text-sm text-gray-500 italic">Tidak ada obat terkait dengan diagnosa ini.</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching obat:', error);
                        obatList.innerHTML = '<p class="text-sm text-red-500 italic">Error memuat daftar obat.</p>';
                    });
            }

            // Function to update obat details when checkbox is selected
            function updateObatDetails(keluhanIndex) {
                const keluhanSection = document.querySelector(`.keluhan-section[data-keluhan-index="${keluhanIndex}"]`);
                const checkedBoxes = keluhanSection.querySelectorAll(
                    `.obat-checkbox[data-keluhan-index="${keluhanIndex}"]:checked`);
                const detailsContainer = keluhanSection.querySelector(
                    `.selected-obat-details[data-keluhan-index="${keluhanIndex}"]`);
                const detailsList = detailsContainer.querySelector('.obat-details-list');

                if (checkedBoxes.length === 0) {
                    detailsContainer.style.display = 'none';
                    detailsList.innerHTML = '';
                    return;
                }

                // Show details container
                detailsContainer.style.display = 'block';

                // Build details for each selected obat
                let detailsHTML = '';
                checkedBoxes.forEach((checkbox, index) => {
                    const obatId = checkbox.value;
                    const obatName = checkbox.getAttribute('data-obat-name');

                    detailsHTML += `
            <div class="border border-gray-300 rounded-lg p-3 bg-white">
                <h5 class="font-semibold text-sm text-gray-800 mb-2">${obatName}</h5>
                <input type="hidden" name="keluhan[${keluhanIndex}][obat_list][${index}][id_obat]" value="${obatId}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                            Jumlah Obat
                        </label>
                        <input type="number"
                               name="keluhan[${keluhanIndex}][obat_list][${index}][jumlah_obat]"
                               min="1"
                               max="10000"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                               placeholder="Masukkan jumlah obat (maks 10.000)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3" />
                            </svg>
                            Aturan Pakai
                        </label>
                        <select name="keluhan[${keluhanIndex}][obat_list][${index}][aturan_pakai]"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
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

            // Attach event listeners to all diagnosa selects
            function attachDiagnosaChangeListeners() {
                document.querySelectorAll('.diagnosa-select').forEach(select => {
                    // Remove old listener if exists (to prevent duplicate)
                    select.removeEventListener('change', handleDiagnosaChange);
                    // Add new listener
                    select.addEventListener('change', handleDiagnosaChange);
                });

                // Also add event listeners to terapi selects
                document.querySelectorAll('select[name$="[terapi]"]').forEach(select => {
                    // Remove old listener if exists (to prevent duplicate)
                    select.removeEventListener('change', handleTerapiChange);
                    // Add new listener
                    select.addEventListener('change', handleTerapiChange);
                });
            }

            // Function to handle terapi change
            function handleTerapiChange(event) {
                const terapiSelect = event.target;
                const terapiValue = terapiSelect.value;
                const keluhanSection = terapiSelect.closest('.keluhan-section');
                const keluhanIndex = keluhanSection.getAttribute('data-keluhan-index');

                // Find the diagnosa select in the same section
                const diagnosaSelect = keluhanSection.querySelector('.diagnosa-select');

                // Trigger diagnosa change to refresh obat list based on new terapi value
                if (diagnosaSelect && diagnosaSelect.value) {
                    handleDiagnosaChange({
                        target: diagnosaSelect
                    });
                } else if (!diagnosaSelect || !diagnosaSelect.value) {
                    // If no diagnosa selected, show appropriate message
                    const obatContainer = keluhanSection.querySelector('.obat-checkbox-container[data-keluhan-index="' +
                        keluhanIndex + '"]');
                    const obatList = obatContainer.querySelector('.obat-list');
                    const detailsContainer = keluhanSection.querySelector('.selected-obat-details[data-keluhan-index="' +
                        keluhanIndex + '"]');

                    if (terapiValue === 'Obat') {
                        obatList.innerHTML =
                            '<p class="text-sm text-gray-500 italic">Pilih diagnosa terlebih dahulu untuk menampilkan daftar obat yang sesuai.</p>';
                    } else {
                        obatList.innerHTML =
                            '<p class="text-sm text-gray-500 italic">Rekomendasi obat hanya tersedia untuk terapi "Obat".</p>';
                    }
                    detailsContainer.style.display = 'none';
                }
            }

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize keluhan sections with existing data
                updateKeluhanSections({{ $rekamMedis->jumlah_keluhan ?? 1 }});

                // Initialize NO RM on page load
                initializeNoRM();

                // Form validation
                const form = document.getElementById('rekamMedisForm');
                form.addEventListener('submit', validateForm);

                // Real-time validation
                form.addEventListener('input', function(e) {
                    validateField(e.target);
                });

                // Auto-hide success messages after 5 seconds
                setTimeout(function() {
                    const successAlert = document.querySelector('#success-container');
                    if (successAlert) {
                        successAlert.style.transition = 'opacity 0.5s';
                        successAlert.style.opacity = '0';
                        setTimeout(() => successAlert.remove(), 500);
                    }
                }, 5000);
            });

            // Fungsi untuk menginisialisasi NO RM saat halaman dimuat
            function initializeNoRM() {
                const nikKaryawan = document.getElementById('info_nik').textContent;
                const selectedKeluarga = document.getElementById('id_keluarga');

                if (selectedKeluarga.value && nikKaryawan) {
                    const selectedOption = selectedKeluarga.options[selectedKeluarga.selectedIndex];
                    const kodeHubungan = selectedOption.getAttribute('data-kode-hubungan') || '';

                    // Generate NO RM otomatis dari NIK-Kode Hubungan
                    const noRM = generateNoRM(nikKaryawan, kodeHubungan);
                    document.getElementById('no_rm').value = noRM;
                }
            }

            function validateForm(e) {
                const form = document.getElementById('rekamMedisForm');
                const isValid = performValidation();

                if (!isValid) {
                    e.preventDefault();

                    // Show custom error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-md';
                    errorDiv.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">Mohon lengkapi semua field yang wajib diisi dengan benar.</p>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;

                    form.parentNode.insertBefore(errorDiv, form);

                    // Scroll to top of form
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });

                    // Auto-remove after 5 seconds
                    setTimeout(() => {
                        if (errorDiv.parentNode) {
                            errorDiv.style.transition = 'opacity 0.5s';
                            errorDiv.style.opacity = '0';
                            setTimeout(() => errorDiv.remove(), 500);
                        }
                    }, 5000);
                }
            }

            function performValidation() {
                let isValid = true;
                const form = document.getElementById('rekamMedisForm');

                // Reset all error states
                form.querySelectorAll('.border-red-500').forEach(el => {
                    el.classList.remove('border-red-500', 'bg-red-50');
                    el.classList.add('border-gray-300');
                });

                // Validate kunjungan (skip validation for hidden field)
                // const kunjunganInput = form.querySelector('#kunjungan_id');
                // if (!kunjunganInput.value) {
                //     showFieldError(kunjunganInput);
                //     isValid = false;
                // }

                // Validate pasien
                const pasienInput = form.querySelector('#id_keluarga');
                if (!pasienInput.value) {
                    showFieldError(pasienInput);
                    isValid = false;
                }

                // Validate tensi darah if exists
                const tensiInput = form.querySelector('#tensi_darah');
                if (tensiInput && !tensiInput.value.trim()) {
                    showFieldError(tensiInput);
                    isValid = false;
                }

                // Validate tanggal periksa
                const tanggalInput = form.querySelector('#tanggal_periksa');
                if (!tanggalInput.value) {
                    showFieldError(tanggalInput);
                    isValid = false;
                }

                // Validate waktu periksa
                const waktuInput = form.querySelector('#waktu_periksa');
                if (!waktuInput.value) {
                    showFieldError(waktuInput);
                    isValid = false;
                }

                // Validate status
                const statusSelect = form.querySelector('#status');
                if (!statusSelect.value) {
                    showFieldError(statusSelect);
                    isValid = false;
                }

                // Validate keluhan sections
                const keluhanSections = form.querySelectorAll('.keluhan-section');
                let hasAtLeastOneKeluhan = false;

                keluhanSections.forEach(section => {
                    const diagnosaSelect = section.querySelector('[name^="keluhan"][name$="[id_diagnosa]"]');
                    const terapiSelect = section.querySelector('[name^="keluhan"][name$="[terapi]"]');

                    if (diagnosaSelect.value && terapiSelect.value) {
                        hasAtLeastOneKeluhan = true;
                    }
                });

                if (!hasAtLeastOneKeluhan) {
                    // Show error for all empty keluhan sections
                    keluhanSections.forEach(section => {
                        const diagnosaSelect = section.querySelector('[name^="keluhan"][name$="[id_diagnosa]"]');
                        const terapiSelect = section.querySelector('[name^="keluhan"][name$="[terapi]"]');
                        if (!diagnosaSelect.value) {
                            showFieldError(diagnosaSelect);
                        }
                        if (!terapiSelect.value) {
                            showFieldError(terapiSelect);
                        }
                    });

                    isValid = false;
                }

                return isValid;
            }

            function validateField(field) {
                // Skip validation for hidden fields and non-required fields
                if (field.type === 'hidden' || !field.hasAttribute('required')) {
                    return;
                }

                // Remove existing error state
                field.classList.remove('border-red-500', 'bg-red-50');
                field.classList.add('border-gray-300');

                // Check if field has value
                if (field.type === 'select-one' || field.type === 'select-multiple') {
                    if (!field.value) {
                        showFieldError(field);
                    }
                } else if (field.type === 'number') {
                    // Special validation for number fields (like jumlah_obat)
                    if (!field.value.trim()) {
                        showFieldError(field);
                    } else if (field.name && field.name.includes('jumlah_obat')) {
                        const value = parseInt(field.value);
                        if (value < 1) {
                            showFieldError(field);
                        } else if (value > 10000) {
                            showFieldError(field);
                        }
                    }
                } else {
                    if (!field.value.trim()) {
                        showFieldError(field);
                    }
                }
            }

            function showFieldError(field) {
                field.classList.remove('border-gray-300');
                field.classList.add('border-red-500', 'bg-red-50');

                // Add shake animation
                field.classList.add('animate-pulse');
                setTimeout(() => {
                    field.classList.remove('animate-pulse');
                }, 1000);
            }
        </script>
    @endpush
@endsection
