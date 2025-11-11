@extends('layouts.app')

@section('page-title', 'Tambah Rekam Medis')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen" x-data="rekamMedisFingerprint()">
        <!-- Fingerprint Verification Modal -->
        <div x-show="showFingerprintModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto bg-black/10 backdrop-blur-sm"
            style="display: none;">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div x-show="showFingerprintModal" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-2xl bg-white rounded-xl shadow-2xl">

                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 rounded-t-xl">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-white flex items-center gap-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                                </svg>
                                Verifikasi Sidik Jari Karyawan
                            </h3>
                            <button onclick="window.location.href='{{ route('rekam-medis.index') }}'"
                                class="bg-white text-blue-600 px-3 py-2 rounded-lg hover:bg-gray-100 font-semibold transition-colors duration-200 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Kembali
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6">
                        <!-- Instructions -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-sm text-blue-800">
                                    <p class="font-semibold mb-2">Verifikasi Identitas Karyawan:</p>
                                    <ul class="list-disc list-inside space-y-1 text-xs">
                                        <li>Silakan tempelkan <strong>jempol</strong> Anda pada sensor fingerprint</li>
                                        <li>Pastikan jari dalam kondisi bersih dan kering</li>
                                        <li>Tekan jari dengan cukup kuat pada sensor</li>
                                        <li>Tahan posisi jari hingga proses verifikasi selesai</li>
                                        <li>Verifikasi berhasil diperlukan untuk melanjutkan pendaftaran rekam medis</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Verification Status -->
                        <div x-show="!verifyResult" class="text-center py-8">
                            <div class="mb-6">
                                <svg class="w-20 h-20 mx-auto text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                                </svg>
                            </div>

                            <button @click="verifyFingerprint()"
                                :disabled="isCapturing || fingerprintTemplates.length === 0"
                                class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 disabled:bg-gray-400 font-semibold transition-colors duration-200 flex items-center justify-center gap-2 mx-auto">
                                <svg x-show="!isCapturing" class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <svg x-show="isCapturing" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span x-text="isCapturing ? 'Memverifikasi...' : 'Verifikasi Sidik Jari'"></span>
                            </button>

                        </div>

                        <!-- Verification Result -->
                        <div x-show="verifyResult" class="space-y-4">
                            <div x-show="verifyResult?.success"
                                class="p-4 bg-green-50 border-2 border-green-500 rounded-lg">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-bold text-green-800">Verifikasi Berhasil!</span>
                                </div>

                                <div class="flex gap-4">
                                    <div class="w-24 flex-shrink-0">
                                        <img :src="verifyResult?.image ? `data:image/bmp;base64,${verifyResult.image}` : ''"
                                            alt="Fingerprint"
                                            class="w-full h-auto border-2 border-green-500 rounded-lg shadow-sm">
                                    </div>

                                    <div class="flex-1">
                                        <div
                                            class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg border border-green-200 shadow-sm">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white text-lg font-bold shadow-lg">
                                                    <span
                                                        x-text="verifyResult?.data?.nama_karyawan ? verifyResult.data.nama_karyawan.charAt(0).toUpperCase() : ''"></span>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-green-600 font-medium mb-1">Karyawan
                                                        Terverifikasi</p>
                                                    <p class="text-xl font-bold text-gray-900"
                                                        x-text="verifyResult?.data?.nama_karyawan || ''"></p>
                                                    <p class="text-sm text-gray-600"
                                                        x-text="verifyResult?.data?.nik_karyawan || ''"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div x-show="!verifyResult?.success" class="p-4 bg-red-50 border-2 border-red-500 rounded-lg">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-bold text-red-800">Verifikasi Gagal!</span>
                                </div>

                                <div class="flex gap-4">
                                    <div class="w-24 flex-shrink-0">
                                        <img :src="verifyResult?.image ? `data:image/bmp;base64,${verifyResult.image}` : ''"
                                            alt="Fingerprint"
                                            class="w-full h-auto border-2 border-red-500 rounded-lg shadow-sm">
                                    </div>

                                    <div class="flex-1">
                                        <div
                                            class="bg-gradient-to-r from-red-50 to-pink-50 p-4 rounded-lg border border-red-200 shadow-sm">
                                            <div class="flex items-start gap-3">
                                                <div
                                                    class="w-12 h-12 flex-shrink-0 bg-gradient-to-br from-red-500 to-pink-600 rounded-full flex items-center justify-center text-white shadow-lg">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-xs text-red-600 font-medium mb-1">Sidik Jari Tidak
                                                        Dikenali</p>
                                                    <p class="text-sm text-gray-700">Tidak ada data fingerprint yang cocok.
                                                        Silakan coba lagi atau gunakan jari yang sama saat pendaftaran.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <button x-show="verifyResult" @click="resetVerification()"
                                    class="flex-1 bg-gray-500 text-white py-2.5 rounded-lg hover:bg-gray-600 font-semibold transition-colors duration-200 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Coba Lagi
                                </button>

                                <button x-show="verifyResult?.success" @click="proceedWithVerifiedEmployee()"
                                    class="flex-1 bg-green-600 text-white py-2.5 rounded-lg hover:bg-green-700 font-semibold transition-colors duration-200 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                    Lanjutkan Pendaftaran
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-3">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-3 rounded-lg shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        Tambah Rekam Medis
                    </h1>
                    <p class="text-gray-600 mt-1 ml-1">Buat rekam medis baru untuk pasien</p>
                </div>
            </div>
        </div>

        <!-- Enhanced Error Messages -->
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-md animate-shake"
                id="error-container">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-semibold text-red-800 mb-2">
                            ⚠️ Terdapat {{ count($errors->all()) }} kesalahan yang perlu diperbaiki:
                        </h3>
                        <div class="mt-2 space-y-1">
                            @foreach ($errors->all() as $index => $error)
                                <div class="flex items-start py-1.5 bg-white bg-opacity-50 rounded px-2">
                                    <span
                                        class="flex-shrink-0 inline-flex items-center justify-center h-5 w-5 rounded-full bg-red-100 text-red-600 text-xs font-bold mr-2 mt-0.5">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="text-sm text-red-700 flex-1">{{ $error }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3 pt-3 border-t border-red-200">
                            <p class="text-xs text-red-600 italic">
                                💡 Tip: Periksa form di bawah, field yang error ditandai dengan border merah dan pesan error
                                di bawah masing-masing field.
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

        <form action="{{ route('rekam-medis.store') }}" method="POST" id="rekam-medis-form"
            onsubmit="return validateForm()">
            @csrf

            <!-- Hidden field for kunjungan_id -->
            <input type="hidden" id="kunjungan_id" name="kunjungan_id" value="{{ old('kunjungan_id') }}">

            <!-- Hidden field for id_karyawan -->
            <input type="hidden" id="id_karyawan" name="id_karyawan" value="{{ old('id_karyawan') }}" required>

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
                                    <span class="text-xs text-gray-500">Foto Karyawan</span>
                                    <div id="info_foto" class="mt-1">
                                        <img id="foto_karyawan" src="" alt="Foto Karyawan"
                                            class="w-20 h-24 object-cover rounded-lg border border-gray-300 hidden"
                                            onerror="this.src='https://ui-avatars.com/api/?name=Unknown&background=6b7280&color=fff&size=80'">
                                        <div id="no_foto"
                                            class="w-20 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
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
                                    class="w-full px-4 py-2.5 border @error('id_keluarga') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white"
                                    required disabled>
                                    <option value="">-- Pilih karyawan terlebih dahulu --</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            @error('id_keluarga')
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
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
                                    class="w-full pl-10 pr-4 py-2.5 border @error('tanggal_periksa') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                    required>
                            </div>
                            @error('tanggal_periksa')
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
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
                                    class="w-full pl-10 pr-4 py-2.5 border @error('waktu_periksa') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                            @error('waktu_periksa')
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Status Rekam Medis -->
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                                Status Rekam Medis <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="status" name="status"
                                    class="w-full px-4 py-2.5 border @error('status') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white"
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
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Jumlah Keluhan -->
                        <div>
                            <label for="jumlah_keluhan" class="block text-sm font-semibold text-gray-700 mb-2">
                                Jumlah Keluhan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="jumlah_keluhan" name="jumlah_keluhan"
                                    class="w-full px-4 py-2.5 border @error('jumlah_keluhan') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white"
                                    required onchange="updateKeluhanSections(this.value)">
                                    <option value="1" {{ old('jumlah_keluhan', 1) == 1 ? 'selected' : '' }}>1 Keluhan
                                    </option>
                                    <option value="2" {{ old('jumlah_keluhan') == 2 ? 'selected' : '' }}>2 Keluhan
                                    </option>
                                    <option value="3" {{ old('jumlah_keluhan') == 3 ? 'selected' : '' }}>3 Keluhan
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
                            @error('jumlah_keluhan')
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Combined Diagnosa & Resep Section -->
            <div id="keluhan-container">
                <!-- Keluhan 1 (Template) -->
                <div class="keluhan-section bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6"
                    data-keluhan-index="0">
                    <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-4">
                        <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
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
                                        <select name="keluhan[0][id_diagnosa]"
                                            class="diagnosa-select w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white"
                                            required data-keluhan-index="0">
                                            <option value="">-- Pilih Diagnosa --</option>
                                            @foreach ($diagnosas as $diagnosa)
                                                <option value="{{ $diagnosa->id_diagnosa }}">
                                                    {{ $diagnosa->nama_diagnosa }}</option>
                                            @endforeach
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
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
                                        <select name="keluhan[0][terapi]"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white"
                                            required>
                                            <option value="">-- Pilih Terapi --</option>
                                            <option value="Obat">Obat</option>
                                            <option value="Lab">Konsul Faskes Lanjutan</option>
                                            <option value="Istirahat">Istirahat</option>
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Keterangan -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Anamnesa
                                    </label>
                                    <textarea name="keluhan[0][keterangan]" rows="3"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                        placeholder="Masukkan catatan medis, anjuran dokter, atau informasi penting lainnya..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Resep Obat Section -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Resep Obat (Opsional)</h3>

                            <!-- Obat Checkbox List Container -->
                            <div class="obat-checkbox-container mb-4" data-keluhan-index="0">
                                <div
                                    class="obat-list bg-gray-50 border border-gray-300 rounded-lg p-4 max-h-60 overflow-y-auto">
                                    <p class="text-sm text-gray-500 italic">Pilih diagnosa terlebih dahulu untuk
                                        menampilkan daftar obat yang sesuai.</p>
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
                </div>
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
                        class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Rekam Medis
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            // Fingerprint verification functionality for Rekam Medis
            function rekamMedisFingerprint() {
                return {
                    showFingerprintModal: true,
                    isCapturing: false,
                    fingerprintTemplates: [],
                    verifyResult: null,
                    verifiedEmployee: null,

                    init() {
                        this.loadFingerprintTemplates();
                    },

                    async loadFingerprintTemplates() {
                        try {
                            const url = new URL('/fingerprint/templates', window.location.origin);
                            const response = await fetch(url.toString());
                            this.fingerprintTemplates = await response.json();
                        } catch (error) {
                            // Error handling for fingerprint templates
                        }
                    },

                    getErrorDescription(code) {
                        const errors = {
                            51: 'System file load failure',
                            52: 'Sensor chip initialization failed',
                            53: 'Device not found',
                            54: 'Fingerprint image capture timeout',
                            55: 'No device available',
                            56: 'Driver load failed',
                            57: 'Wrong Image',
                            58: 'Lack of bandwidth',
                            59: 'Device Busy',
                            60: 'Cannot get serial number',
                            61: 'Unsupported device',
                            63: 'SgiBioSrv tidak berjalan'
                        };
                        return errors[code] || 'Unknown error';
                    },

                    async verifyFingerprint() {
                        if (this.fingerprintTemplates.length === 0) {
                            this.showMessage('Belum ada fingerprint terdaftar!', 'error');
                            return;
                        }

                        this.isCapturing = true;

                        try {
                            const params = new URLSearchParams({
                                Timeout: '10000',
                                Quality: '50',
                                licstr: '',
                                templateFormat: 'ISO',
                                imageWSQRate: '0.75'
                            });

                            const response = await fetch('https://localhost:8443/SGIFPCapture', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: params.toString()
                            });

                            if (!response.ok) {
                                throw new Error(`HTTP ${response.status}`);
                            }

                            const data = await response.json();

                            if (data.ErrorCode !== 0) {
                                this.showMessage(`Error: ${data.ErrorCode} - ${this.getErrorDescription(data.ErrorCode)}`,
                                    'error');
                                return;
                            }

                            const template = data.TemplateBase64;

                            let bestMatch = null;
                            let bestScore = 0;

                            for (const user of this.fingerprintTemplates) {
                                try {
                                    const matchParams = new URLSearchParams({
                                        Template1: template,
                                        Template2: user.fingerprint_template,
                                        licstr: '',
                                        templateFormat: 'ISO'
                                    });

                                    const matchResponse = await fetch('https://localhost:8443/SGIMatchScore', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded',
                                        },
                                        body: matchParams.toString()
                                    });

                                    const matchData = await matchResponse.json();

                                    if (matchData.ErrorCode === 0 && matchData.MatchingScore > bestScore) {
                                        bestScore = matchData.MatchingScore;
                                        bestMatch = user;
                                    }
                                } catch (error) {
                                    // Error handling for fingerprint matching
                                }
                            }

                            if (bestScore > 100 && bestMatch) {
                                this.verifyResult = {
                                    success: true,
                                    data: bestMatch,
                                    score: bestScore,
                                    image: data.BMPBase64 || null
                                };
                                this.verifiedEmployee = bestMatch;
                                this.showMessage(
                                    `✓ Verifikasi Berhasil! ${bestMatch.nama_karyawan} - ${bestMatch.nik_karyawan || ''}`,
                                    'success');
                            } else {
                                this.verifyResult = {
                                    success: false,
                                    data: null,
                                    score: bestScore,
                                    image: data.BMPBase64 || null
                                };
                                this.showMessage('✗ Sidik jari tidak cocok', 'error');
                            }
                        } catch (error) {
                            this.showMessage(`Error koneksi: ${error.message}. Pastikan SGIBIOSRV berjalan di port 8443`,
                                'error');
                        } finally {
                            this.isCapturing = false;
                        }
                    },

                    resetVerification() {
                        this.verifyResult = null;
                        this.isCapturing = false;
                    },

                    closeFingerprintModal() {
                        if (this.verifiedEmployee) {
                            this.showFingerprintModal = false;
                        } else {
                            this.showMessage('Anda harus melakukan verifikasi fingerprint terlebih dahulu!', 'warning');
                        }
                    },

                    proceedWithVerifiedEmployee() {
                        if (this.verifiedEmployee) {
                            // Auto-fill form with verified employee data
                            this.selectKaryawan(this.verifiedEmployee);
                            this.showFingerprintModal = false;
                            this.showMessage(
                                `Selamat datang, ${this.verifiedEmployee.nama_karyawan}! Silakan pilih anggota keluarga untuk melanjutkan.`,
                                'success');
                        }
                    },

                    selectKaryawan(karyawan) {
                        // Set karyawan values
                        document.getElementById('id_karyawan').value = karyawan.id_karyawan;

                        // Update info karyawan
                        document.getElementById('info_nik').textContent = karyawan.nik_karyawan;
                        document.getElementById('info_nama').textContent = karyawan.nama_karyawan;
                        document.getElementById('info_departemen').textContent = karyawan.departemen?.nama_departemen || '-';

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

                        // Load family members for this employee
                        loadFamilyMembersLegacy(karyawan.id_karyawan);
                    },

                    async loadFamilyMembers(karyawanId) {
                        try {
                            const url = new URL('/rekam-medis/get-family-members', window.location.origin);
                            url.searchParams.set('karyawan_id', karyawanId);
                            const response = await fetch(url.toString());
                            const data = await response.json();

                            const selectElement = document.getElementById('id_keluarga');
                            selectElement.innerHTML = '<option value="">-- Pilih Anggota Keluarga --</option>';

                            if (data.length > 0) {
                                data.forEach(member => {
                                    const option = document.createElement('option');
                                    option.value = member.id_keluarga;
                                    option.textContent = `${member.nama_keluarga} (${member.hubungan})`;
                                    option.setAttribute('data-no-rm', member.kode_hubungan || '');
                                    option.setAttribute('data-jenis-kelamin', member.jenis_kelamin || '');
                                    option.setAttribute('data-hubungan', member.hubungan || '');
                                    selectElement.appendChild(option);
                                });
                                selectElement.disabled = false;
                            } else {
                                selectElement.innerHTML = '<option value="">-- Tidak ada anggota keluarga --</option>';
                                selectElement.disabled = true;
                            }
                        } catch (error) {
                            // Error handling for family members loading
                            const selectElement = document.getElementById('id_keluarga');
                            selectElement.innerHTML = '<option value="">-- Error memuat data --</option>';
                            selectElement.disabled = true;
                        }
                    },

                    showMessage(msg, type = 'info') {
                        // Use SweetAlert if available
                        if (typeof window.showSweetAlert === 'function') {
                            window.showSweetAlert(msg, type);
                        } else {
                            // Fallback to
                        }
                    }
                }
            }

            // Original form functionality

            function loadFamilyMembersLegacy(karyawanId) {
                const url = new URL(`{{ route('rekam-medis.getFamilyMembers') }}`, window.location.origin);
                url.searchParams.set('karyawan_id', karyawanId);
                fetch(url.toString())
                    .then(response => response.json())
                    .then(data => {
                        const selectElement = document.getElementById('id_keluarga');
                        selectElement.innerHTML = '<option value="">-- Pilih Anggota Keluarga --</option>';

                        if (data.length > 0) {
                            data.forEach(member => {
                                const option = document.createElement('option');
                                option.value = member.id_keluarga;
                                option.textContent = `${member.nama_keluarga} (${member.hubungan})`;
                                option.setAttribute('data-no-rm', member.kode_hubungan || '');
                                option.setAttribute('data-jenis-kelamin', member.jenis_kelamin || '');
                                option.setAttribute('data-hubungan', member.hubungan || '');
                                selectElement.appendChild(option);
                            });
                            selectElement.disabled = false;
                        } else {
                            selectElement.innerHTML = '<option value="">-- Tidak ada anggota keluarga --</option>';
                            selectElement.disabled = true;
                        }
                    })
                    .catch(error => {
                        // Error handling for legacy family members loading
                        const selectElement = document.getElementById('id_keluarga');
                        selectElement.innerHTML = '<option value="">-- Error memuat data --</option>';
                        selectElement.disabled = true;
                    });
            }

            function selectKeluarga() {
                const selectElement = document.getElementById('id_keluarga');
                const selectedOption = selectElement.options[selectElement.selectedIndex];

                if (selectedOption && selectedOption.value) {
                    // Ambil data dari pilihan keluarga
                    const namaPasien = selectedOption.textContent.split(' (')[0];
                    const kodeHubungan = selectedOption.getAttribute('data-no-rm') || '';
                    const hubungan = selectedOption.getAttribute('data-hubungan') || '';
                    const jenisKelamin = selectedOption.getAttribute('data-jenis-kelamin') || '';

                    // Ambil NIK karyawan dari info di atas form
                    const nikKaryawan = document.getElementById('info_nik').textContent.trim();

                    // Gabungkan NIK + KodeHubungan
                    const noRM = nikKaryawan && kodeHubungan ? `${nikKaryawan}-${kodeHubungan.replace('#','').trim()}` : '';

                    // Isi otomatis field
                    document.getElementById('nama_pasien').value = namaPasien;
                    document.getElementById('no_rm').value = noRM;
                    document.getElementById('hubungan').value = hubungan;
                    document.getElementById('jenis_kelamin').value = jenisKelamin;
                } else {
                    document.getElementById('nama_pasien').value = '';
                    document.getElementById('no_rm').value = '';
                    document.getElementById('hubungan').value = '';
                    document.getElementById('jenis_kelamin').value = '';
                }
            }

            // Add event listener for keluarga dropdown
            document.getElementById('id_keluarga').addEventListener('change', function() {
                selectKeluarga();
            });

            // Handle multiple keluhan sections
            function updateKeluhanSections(value) {
                const container = document.getElementById('keluhan-container');
                const template = container.querySelector('.keluhan-section');

                // Remove existing sections except the first one
                while (container.children.length > 1) {
                    container.removeChild(container.lastChild);
                }

                // Clone and add new sections based on selected value
                for (let i = 1; i < value; i++) {
                    const newSection = template.cloneNode(true);
                    newSection.setAttribute('data-keluhan-index', i);

                    // Update section title
                    const title = newSection.querySelector('.keluhan-number');
                    title.textContent = `(Keluhan ${i + 1})`;

                    // Update form field names with proper index
                    newSection.querySelectorAll('select, input, textarea').forEach(element => {
                        if (element.name && element.name.includes('keluhan[')) {
                            element.name = element.name.replace(/keluhan\[\d+\]/, `keluhan[${i}]`);
                            element.value = '';
                        }
                        // Update data-keluhan-index for diagnosa selects
                        if (element.classList.contains('diagnosa-select')) {
                            element.setAttribute('data-keluhan-index', i);
                        }
                    });

                    // Update data-keluhan-index for obat containers
                    const obatContainer = newSection.querySelector('.obat-checkbox-container');
                    if (obatContainer) {
                        obatContainer.setAttribute('data-keluhan-index', i);
                        obatContainer.querySelector('.obat-list').innerHTML =
                            '<p class="text-sm text-gray-500 italic">Pilih diagnosa terlebih dahulu untuk menampilkan daftar obat yang sesuai.</p>';
                    }

                    const detailsContainer = newSection.querySelector('.selected-obat-details');
                    if (detailsContainer) {
                        detailsContainer.setAttribute('data-keluhan-index', i);
                        detailsContainer.style.display = 'none';
                    }

                    container.appendChild(newSection);
                }

                // Re-attach event listeners for all diagnosa selects
                attachDiagnosaChangeListeners();
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
                const url = new URL(`{{ route('rekam-medis.getObatByDiagnosa') }}`, window.location.origin);
                url.searchParams.set('diagnosa_id', diagnosaId);
                fetch(url.toString())
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            // Build checkbox list
                            let checkboxHTML = '<div class="space-y-2">';
                            data.forEach(obat => {
                                const stokHabis = obat.stok_akhir <= 0;
                                const disabledAttr = stokHabis ? 'disabled' : '';
                                const labelClass = stokHabis ? 'opacity-50 cursor-not-allowed' :
                                    'hover:bg-white cursor-pointer';
                                const stokBadge = stokHabis ?
                                    '<span class="ml-2 px-2 py-1 bg-red-500 text-white text-xs font-semibold rounded">STOK HABIS</span>' :
                                    `<span class="ml-2 px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded">Stok: ${obat.stok_akhir}</span>`;

                                checkboxHTML += `
                        <label class="flex items-start space-x-3 p-2 rounded transition-colors ${labelClass}">
                            <input type="checkbox"
                                   class="obat-checkbox mt-1 w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                   value="${obat.id_obat}"
                                   data-obat-name="${obat.nama_obat}"
                                   data-obat-stok="${obat.stok_akhir}"
                                   data-keluhan-index="${keluhanIndex}"
                                   ${disabledAttr}
                                   onchange="updateObatDetails(${keluhanIndex})">
                            <span class="text-sm text-gray-700 flex-1">
                                ${obat.nama_obat}
                                ${stokBadge}
                            </span>
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
                        // Error handling for obat fetching
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
                    const obatStok = parseInt(checkbox.getAttribute('data-obat-stok')) || 0;

                    detailsHTML += `
            <div class="border border-gray-300 rounded-lg p-3 bg-white">
                <div class="flex items-center justify-between mb-2">
                    <h5 class="font-semibold text-sm text-gray-800">${obatName}</h5>
                    <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded">Stok: ${obatStok}</span>
                </div>
                <input type="hidden" name="keluhan[${keluhanIndex}][obat_list][${index}][id_obat]" value="${obatId}">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                            Jumlah Obat <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="keluhan[${keluhanIndex}][obat_list][${index}][jumlah_obat]"
                               min="1"
                               max="${obatStok}"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                               placeholder="Maks: ${obatStok}"
                               onchange="validateStok(this, ${obatStok}, '${obatName}')"
                               oninput="validateStok(this, ${obatStok}, '${obatName}')"
                               required>
                        <p class="text-xs text-gray-500 mt-1">Maksimal: ${obatStok} unit</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                            Diskon
                        </label>
                        <select name="keluhan[${keluhanIndex}][obat_list][${index}][diskon]"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                            <option value="0">0%</option>
                            <option value="20">20%</option>
                            <option value="40">40%</option>
                            <option value="50">50%</option>
                            <option value="80">80%</option>
                            <option value="100">100%</option>
                        </select>
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
                const keluhanIndex = Array.from(keluhanSection.parentElement.children).indexOf(keluhanSection);

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

            // Function to validate stock quantity
            function validateStok(input, maxStok, obatName) {
                const value = parseInt(input.value);

                if (value > maxStok) {
                    input.value = maxStok;
                    // Show error message
                    alert(`Jumlah obat ${obatName} tidak boleh melebihi stok yang tersedia (${maxStok} unit)`);
                    input.classList.add('border-red-500', 'bg-red-50');
                } else if (value < 1 && input.value !== '') {
                    input.value = 1;
                    input.classList.add('border-red-500', 'bg-red-50');
                } else {
                    input.classList.remove('border-red-500', 'bg-red-50');
                }
            }

            // Form validation function
            function validateForm() {
                let isValid = true;
                let errorMessages = [];

                // Clear previous error states
                clearValidationErrors();

                // Check if fingerprint verification was completed
                // Access Alpine component data correctly
                const alpineElement = document.querySelector('[x-data="rekamMedisFingerprint()"]');
                const fingerprintComponent = alpineElement ? alpineElement._x_dataStack?.[0] : null;
                const fingerprintVerified = fingerprintComponent?.verifiedEmployee;

                if (!fingerprintVerified) {
                    errorMessages.push('Anda harus melakukan verifikasi fingerprint terlebih dahulu');
                    showValidationSummary(errorMessages);
                    return false;
                }


                // Validate karyawan selection (now through fingerprint verification)
                const idKaryawan = document.getElementById('id_karyawan').value;
                if (!idKaryawan) {
                    errorMessages.push('Verifikasi fingerprint karyawan belum dilakukan');
                    isValid = false;
                }

                // Validate keluarga selection
                const idKeluarga = document.getElementById('id_keluarga').value;
                if (!idKeluarga) {
                    showFieldError('id_keluarga', 'Silakan pilih anggota keluarga terlebih dahulu');
                    errorMessages.push('Anggota keluarga harus dipilih');
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
                    const selectedDate = new Date(tanggalPeriksa);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    if (selectedDate > today) {
                        showFieldError('tanggal_periksa', 'Tanggal periksa tidak boleh melebihi hari ini');
                        errorMessages.push('Tanggal periksa tidak boleh melebihi hari ini');
                        isValid = false;
                    }
                }

                // Validate keluhan sections
                const keluhanSections = document.querySelectorAll('.keluhan-section');
                let keluhanIndex = 0;

                keluhanSections.forEach(section => {
                    const diagnosaSelect = section.querySelector(
                        `select[name="keluhan[${keluhanIndex}][id_diagnosa]"]`);
                    const terapiSelect = section.querySelector(`select[name="keluhan[${keluhanIndex}][terapi]"]`);

                    if (!diagnosaSelect || !diagnosaSelect.value) {
                        showFieldError(diagnosaSelect, `Diagnosa untuk keluhan ${keluhanIndex + 1} harus dipilih`);
                        errorMessages.push(`Diagnosa untuk keluhan ${keluhanIndex + 1} harus dipilih`);
                        isValid = false;
                    }

                    if (!terapiSelect || !terapiSelect.value) {
                        showFieldError(terapiSelect, `Terapi untuk keluhan ${keluhanIndex + 1} harus dipilih`);
                        errorMessages.push(`Terapi untuk keluhan ${keluhanIndex + 1} harus dipilih`);
                        isValid = false;
                    }

                    // Validate obat details if terapi is "Obat"
                    if (terapiSelect && terapiSelect.value === 'Obat') {
                        const checkedBoxes = section.querySelectorAll(
                            `.obat-checkbox[data-keluhan-index="${keluhanIndex}"]:checked`);

                        if (checkedBoxes.length === 0) {
                            showSectionError(section,
                                `Jika terapi adalah "Obat", minimal satu obat harus dipilih untuk keluhan ${keluhanIndex + 1}`
                            );
                            errorMessages.push(
                                `Jika terapi adalah "Obat", minimal satu obat harus dipilih untuk keluhan ${keluhanIndex + 1}`
                            );
                            isValid = false;
                        } else {
                            // Validate obat details
                            checkedBoxes.forEach((checkbox, index) => {
                                const jumlahInput = section.querySelector(
                                    `input[name="keluhan[${keluhanIndex}][obat_list][${index}][jumlah_obat]"]`
                                );

                                if (jumlahInput && (!jumlahInput.value || parseInt(jumlahInput.value) < 1)) {
                                    showFieldError(jumlahInput, `Jumlah obat harus diisi dan minimal 1`);
                                    errorMessages.push(`Jumlah obat harus diisi dan minimal 1`);
                                    isValid = false;
                                } else if (jumlahInput && parseInt(jumlahInput.value) > 10000) {
                                    showFieldError(jumlahInput, `Jumlah obat tidak boleh lebih dari 10.000`);
                                    errorMessages.push(`Jumlah obat tidak boleh lebih dari 10.000`);
                                    isValid = false;
                                }
                            });
                        }
                    }

                    keluhanIndex++;
                });

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
            function showFieldError(field, message) {
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

            // Show section error
            function showSectionError(section, message) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'section-error bg-red-50 border-l-4 border-red-500 p-3 mb-3 rounded';
                errorDiv.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span class="text-sm text-red-700">${message}</span>
        </div>
    `;

                // Insert at the beginning of the section content
                const sectionContent = section.querySelector('.p-6');
                sectionContent.insertBefore(errorDiv, sectionContent.firstChild);
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
                attachDiagnosaChangeListeners();

                // Add real-time validation for critical fields
                document.getElementById('id_keluarga').addEventListener('blur', function() {
                    const idKeluarga = this.value;
                    if (!idKeluarga) {
                        showFieldError(this, 'Silakan pilih anggota keluarga');
                    } else {
                        clearFieldError(this);
                    }
                });

                document.getElementById('tanggal_periksa').addEventListener('change', function() {
                    const selectedDate = new Date(this.value);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    if (selectedDate > today) {
                        showFieldError(this, 'Tanggal periksa tidak boleh melebihi hari ini');
                    } else {
                        clearFieldError(this);
                    }
                });

                // Add confirmation before leaving page if form has changes
                let formChanged = false;
                const form = document.getElementById('rekam-medis-form');

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
            });
        </script>
    @endpush
@endsection
