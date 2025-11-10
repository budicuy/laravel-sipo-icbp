@extends('layouts.app')

@section('title', 'Manajemen Fingerprint')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen" x-data="fingerprintSystem()">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                    </svg>
                </div>
                Sistem Sidik Jari SecuGen
            </h1>
            <p class="text-gray-600 mt-2 ml-1">Manajemen fingerprint data keluarga karyawan</p>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button @click="activeTab = 'enroll'"
                        :class="activeTab === 'enroll' ? 'border-indigo-500 text-indigo-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 text-sm font-medium border-b-2 transition-colors duration-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Pendaftaran Fingerprint
                    </button>
                    <button @click="activeTab = 'verify'"
                        :class="activeTab === 'verify' ? 'border-indigo-500 text-indigo-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-6 text-sm font-medium border-b-2 transition-colors duration-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Verifikasi Fingerprint
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <div x-show="activeTab === 'enroll'" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="rounded-lg p-6 bg-gray-50 shadow">
                            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Daftar Fingerprint Baru
                            </h2>

                            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="text-sm text-blue-800">
                                        <p class="font-semibold mb-1">Rekomendasi Pendaftaran Fingerprint:</p>
                                        <ul class="list-disc list-inside space-y-1 text-xs">
                                            <li>Gunakan <strong>jempol</strong> untuk hasil terbaik</li>
                                            <li>Pastikan kualitas minimal 70% untuk dapat disimpan</li>
                                            <li>Rekomendasi kualitas optimal: 80%-100%</li>
                                            <li>Bersihkan sensor sebelum penggunaan</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Anggota Keluarga</label>
                                <div class="relative">
                                    <input type="text" x-model="searchQuery" @input="searchFamilyMembers()"
                                        @focus="showSearchResults = true" placeholder="Ketik nama atau NIK..."
                                        class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>

                                    <!-- Search Results Dropdown -->
                                    <div x-show="showSearchResults && searchResults.length > 0"
                                        @click.away="showSearchResults = false"
                                        class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                        <template x-for="member in searchResults" :key="member.id_keluarga">
                                            <div @click="selectFamilyMember(member)"
                                                class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                                <div class="font-semibold text-gray-900" x-text="member.nama_keluarga">
                                                </div>
                                                <div class="text-sm text-gray-600 mt-1">
                                                    <div class="flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                            </path>
                                                        </svg>
                                                        <span class="truncate"
                                                            x-text="member.karyawan?.nama_karyawan || 'Tidak ada penanggung jawab'"></span>
                                                    </div>
                                                    <div class="flex items-center gap-4 mt-1 text-xs">
                                                        <span class="flex items-center gap-1 flex-shrink-0">
                                                            <svg class="w-3 h-3 text-gray-400 flex-shrink-0"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2">
                                                                </path>
                                                            </svg>
                                                            <span class="truncate"
                                                                x-text="member.karyawan?.nik_karyawan || '-'"></span>
                                                        </span>
                                                        <span class="flex items-center gap-1 flex-shrink-0">
                                                            <svg class="w-3 h-3 text-gray-400 flex-shrink-0"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                                </path>
                                                            </svg>
                                                            <span class="truncate"
                                                                x-text="member.hubungan?.hubungan || '-'"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div x-show="selectedFamilyMember" class="mb-4 p-3 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-800">
                                    <strong>Terpilih:</strong> <span x-text="getSelectedMemberName()"></span>
                                </p>
                            </div>

                            <button @click="enrollFingerprint()" :disabled="!selectedFamilyMember || isCapturing"
                                class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 disabled:bg-gray-400 font-semibold transition-colors duration-200 flex items-center justify-center gap-2">
                                <svg x-show="!isCapturing" class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                <svg x-show="isCapturing" class="w-5 h-5 animate-spin" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span x-text="isCapturing ? 'Menangkap Sidik Jari...' : 'Capture Fingerprint'"></span>
                            </button>
                        </div>

                        <div class="rounded-lg p-6 bg-gray-50 shadow">
                            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Preview Sidik Jari
                            </h2>

                            <div x-show="!lastCaptured" class="text-center py-8 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                                </svg>
                                <p>Belum ada sidik jari yang ditangkap</p>
                            </div>

                            <div x-show="lastCaptured" class="flex gap-4">
                                <div class="w-48 flex-shrink-0">
                                    <img :src="`data:image/bmp;base64,${lastCaptured.image}`" alt="Fingerprint"
                                        class="w-full h-auto border rounded-lg shadow-sm">
                                </div>

                                <div class="flex-1 space-y-4">
                                    <div class="space-y-3">
                                        <div class="bg-white p-3 rounded-lg border shadow-sm">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-semibold text-gray-700">Kualitas:</span>
                                                <span x-text="lastCaptured.quality"
                                                    :class="lastCaptured.quality >= 80 ? 'text-sm font-bold text-green-600' :
                                                        lastCaptured.quality >= 70 ?
                                                        'text-sm font-bold text-amber-600' :
                                                        'text-sm font-bold text-red-600'"></span>
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500">
                                                <span class="font-medium">Rekomendasi kualitas: 80%-100%</span>
                                                <div x-show="lastCaptured.quality < 70"
                                                    class="mt-1 text-red-600 font-semibold">
                                                    ❌ Kualitas sidik jari terlalu rendah (tidak dapat disimpan)
                                                </div>
                                                <div x-show="lastCaptured.quality >= 70 && lastCaptured.quality < 80"
                                                    class="mt-1 text-amber-600">
                                                    ⚠️ Kualitas sidik jari kurang optimal
                                                </div>
                                                <div x-show="lastCaptured.quality >= 80" class="mt-1 text-green-600">
                                                    ✓ Kualitas sidik jari baik
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        <button @click="saveEnrolledFingerprint()"
                                            :disabled="isCapturing || !lastCaptured || lastCaptured.quality < 70"
                                            class="w-full bg-green-600 text-white py-2.5 rounded-lg hover:bg-green-700 disabled:bg-gray-400 font-semibold transition-colors duration-200 flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V2" />
                                            </svg>
                                            <span
                                                x-text="lastCaptured && lastCaptured.quality < 70 ? 'Kualitas Terlalu Rendah' : 'Simpan'"></span>
                                        </button>
                                        <button @click="resetEnrollment()"
                                            class="w-full bg-gray-500 text-white py-2.5 rounded-lg hover:bg-gray-600 font-semibold transition-colors duration-200 flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            Ulang
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'verify'" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="rounded-lg p-6 bg-gray-50 shadow">
                            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Verifikasi Sidik Jari
                            </h2>

                            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="text-sm text-green-800">
                                        <p class="font-semibold mb-1">Tips Verifikasi Fingerprint:</p>
                                        <ul class="list-disc list-inside space-y-1 text-xs">
                                            <li>Gunakan <strong>jempol</strong> yang sama saat pendaftaran</li>
                                            <li>Pastikan jari dalam kondisi bersih dan kering</li>
                                            <li>Tekan jari dengan cukup kuat pada sensor</li>
                                            <li>Tahan posisi jari hingga proses verifikasi selesai</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <p class="text-gray-600 mb-6">Letakkan jari di scanner untuk memverifikasi identitas</p>

                            <button @click="verifyFingerprint()"
                                :disabled="isCapturing || fingerprintTemplates.length === 0"
                                class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 disabled:bg-gray-400 font-semibold transition-colors duration-200 flex items-center justify-center gap-2">
                                <svg x-show="!isCapturing" class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <svg x-show="isCapturing" class="w-5 h-5 animate-spin" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span x-text="isCapturing ? 'Memverifikasi...' : 'Verifikasi Sekarang'"></span>
                            </button>

                            <div x-show="fingerprintTemplates.length === 0"
                                class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-sm text-yellow-800">
                                    <strong>Perhatian:</strong> Belum ada data fingerprint yang terdaftar. Silakan daftarkan
                                    fingerprint terlebih dahulu.
                                </p>
                            </div>
                        </div>

                        <div class="rounded-lg p-6 bg-gray-50 shadow">
                            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Hasil Verifikasi
                            </h2>

                            <div x-show="!verifyResult" class="text-center py-8 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                                </svg>
                                <p>Belum ada hasil verifikasi</p>
                            </div>

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
                                        <div class="w-32 flex-shrink-0">
                                            <img :src="`data:image/bmp;base64,${verifyResult.image}`" alt="Fingerprint"
                                                class="w-full h-auto border-2 border-green-500 rounded-lg shadow-sm">
                                        </div>

                                        <div class="flex-1">
                                            <!-- Nama Pasien (Utama) -->
                                            <div
                                                class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg border border-green-200 shadow-sm mb-3">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white text-lg font-bold shadow-lg">
                                                        <span
                                                            x-text="verifyResult.data?.nama_keluarga?.charAt(0).toUpperCase()"></span>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-green-600 font-medium mb-1">Pasien
                                                            Teridentifikasi</p>
                                                        <p class="text-2xl font-bold text-gray-900"
                                                            x-text="verifyResult.data?.nama_keluarga"></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Informasi Detail -->
                                            <div class="bg-white p-4 rounded-lg border shadow-sm">
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <div class="flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2">
                                                            </path>
                                                        </svg>
                                                        <div>
                                                            <p class="text-xs text-gray-500">NIK</p>
                                                            <p class="text-sm font-medium text-gray-900"
                                                                x-text="verifyResult.data?.karyawan?.nik_karyawan || '-'">
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                            </path>
                                                        </svg>
                                                        <div>
                                                            <p class="text-xs text-gray-500">Penanggung Jawab</p>
                                                            <p class="text-sm font-medium text-gray-900"
                                                                x-text="verifyResult.data?.karyawan?.nama_karyawan || '-'">
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                            </path>
                                                        </svg>
                                                        <div>
                                                            <p class="text-xs text-gray-500">Hubungan</p>
                                                            <p class="text-sm font-medium text-gray-900"
                                                                x-text="verifyResult.data?.hubungan?.hubungan || '-'"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div x-show="!verifyResult?.success"
                                    class="p-4 bg-red-50 border-2 border-red-500 rounded-lg">
                                    <div class="flex items-center gap-2 mb-3">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="font-bold text-red-800">Verifikasi Gagal!</span>
                                    </div>

                                    <div class="flex gap-4">
                                        <div class="w-32 flex-shrink-0">
                                            <img :src="`data:image/bmp;base64,${verifyResult.image}`" alt="Fingerprint"
                                                class="w-full h-auto border-2 border-red-500 rounded-lg shadow-sm">
                                        </div>

                                        <div class="flex-1">
                                            <!-- Pesan Verifikasi Gagal -->
                                            <div
                                                class="bg-gradient-to-r from-red-50 to-pink-50 p-4 rounded-lg border border-red-200 shadow-sm mb-3">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-full flex items-center justify-center text-white shadow-lg">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-red-600 font-medium mb-1">Verifikasi Gagal
                                                        </p>
                                                        <p class="text-lg font-bold text-gray-900">Sidik Jari Tidak
                                                            Dikenali</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Informasi Tambahan -->
                                            <div class="bg-white p-4 rounded-lg border shadow-sm">
                                                <div class="space-y-3">
                                                    <div class="flex items-start gap-3">
                                                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                                                            </path>
                                                        </svg>
                                                        <div>
                                                            <p class="text-sm text-gray-700 font-medium">Tidak ada data
                                                                fingerprint yang cocok</p>
                                                            <p class="text-xs text-gray-500 mt-1">Pastikan Anda menggunakan
                                                                jari yang sama saat pendaftaran</p>
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                                        <div class="flex items-center gap-2">
                                                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                                </path>
                                                            </svg>
                                                            <div>
                                                                <p class="text-xs text-gray-500">Tips</p>
                                                                <p class="text-sm font-medium text-gray-900">Gunakan jempol
                                                                    yang sama</p>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                                </path>
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                                </path>
                                                            </svg>
                                                            <div>
                                                                <p class="text-xs text-gray-500">Kualitas</p>
                                                                <p class="text-sm font-medium text-gray-900">Pastikan
                                                                    kualitas ≥ 70%</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button @click="resetVerification()"
                                    class="w-full bg-gray-500 text-white py-2.5 rounded-lg hover:bg-gray-600 font-semibold transition-colors duration-200 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Verifikasi Lagi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="message" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="m-6 p-4 rounded-lg"
                :class="messageType === 'success' ? 'bg-green-100 text-green-800' :
                    messageType === 'error' ? 'bg-red-100 text-red-800' :
                    'bg-blue-100 text-blue-800'">
                <div class="flex items-center gap-2">
                    <svg x-show="messageType === 'success'" class="w-5 h-5 text-green-600" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg x-show="messageType === 'error'" class="w-5 h-5 text-red-600" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg x-show="messageType === 'info'" class="w-5 h-5 text-blue-600" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-text="message"></span>
                </div>
            </div>

            <div class="border-t border-gray-200">
                <div
                    class="p-5 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
                    <div class="text-sm text-gray-600">
                        Total: <span class="font-semibold text-gray-900">{{ count($keluargas) }}</span> data fingerprint
                        terdaftar
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gradient-to-r from-indigo-600 to-purple-600">
                                <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">No
                                </th>
                                <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    <a href="{{ route('fingerprint.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'nama_keluarga', 'direction' => request('sort') == 'nama_keluarga' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                        class="flex items-center justify-between group hover:text-indigo-300 transition-colors">
                                        <span>Nama Keluarga</span>
                                        <span class="ml-2">
                                            @if (request('sort') == 'nama_keluarga')
                                                @if (request('direction') == 'asc')
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 15l7-7 7 7" />
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                @endif
                                            @else
                                                <svg class="w-4 h-4 text-white opacity-40 group-hover:opacity-100 transition-opacity"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0L4-4m-4 4l-4-4" />
                                                </svg>
                                            @endif
                                        </span>
                                    </a>
                                </th>
                                <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    <a href="{{ route('fingerprint.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'karyawan.nama_karyawan', 'direction' => request('sort') == 'karyawan.nama_karyawan' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                        class="flex items-center justify-between group hover:text-indigo-300 transition-colors">
                                        <span>Nama Karyawan</span>
                                        <span class="ml-2">
                                            @if (request('sort') == 'karyawan.nama_karyawan')
                                                @if (request('direction') == 'asc')
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 15l7-7 7 7" />
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                @endif
                                            @else
                                                <svg class="w-4 h-4 text-white opacity-40 group-hover:opacity-100 transition-opacity"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                                </svg>
                                            @endif
                                        </span>
                                    </a>
                                </th>
                                <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    Hubungan
                                </th>
                                <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    <a href="{{ route('fingerprint.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'fingerprint_enrolled_at', 'direction' => request('sort') == 'fingerprint_enrolled_at' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                        class="flex items-center justify-between group hover:text-indigo-300 transition-colors">
                                        <span>Tanggal Daftar</span>
                                        <span class="ml-2">
                                            @if (request('sort') == 'fingerprint_enrolled_at')
                                                @if (request('direction') == 'asc')
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 15l7-7 7 7" />
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                @endif
                                            @else
                                                <svg class="w-4 h-4 text-white opacity-40 group-hover:opacity-100 transition-opacity"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                                </svg>
                                            @endif
                                        </span>
                                    </a>
                                </th>

                                <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($keluargas as $index => $keluarga)
                                <tr class="hover:bg-indigo-50 transition-colors">
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                                {{ strtoupper(substr($keluarga->nama_keluarga, 0, 2)) }}
                                            </div>
                                            <span
                                                class="text-sm font-medium text-gray-900">{{ $keluarga->nama_keluarga }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $keluarga->karyawan->nama_karyawan ?? '-' }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                            <span>{{ $keluarga->hubungan->hubungan ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1 text-sm text-gray-700">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ optional($keluarga->fingerprint_enrolled_at)->format('d-m-Y') }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        <div class="flex items-center gap-2">
                                            <button @click="deleteFingerprint({{ $keluarga->id_keluarga }})"
                                                class="inline-flex items-center justify-center w-9 h-9 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all shadow-sm hover:shadow-md"
                                                title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                                            </svg>
                                            <p class="text-sm">Belum ada data fingerprint yang terdaftar</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function fingerprintSystem() {
                return {
                    activeTab: 'enroll',
                    familyMembers: [],
                    fingerprintTemplates: [],
                    selectedFamilyMember: '',
                    isCapturing: false,
                    message: '',
                    messageType: 'info',
                    lastCaptured: null,
                    verifyResult: null,
                    keluargas: @json($keluargas),
                    searchQuery: '',
                    searchResults: [],
                    showSearchResults: false,
                    selectedMemberData: null,

                    init() {
                        this.loadFamilyMembers();
                        this.loadFingerprintTemplates();
                    },

                    async loadFamilyMembers() {
                        try {
                            const response = await fetch('/fingerprint/family-members');
                            this.familyMembers = await response.json();
                        } catch (error) {
                            console.error('Error loading family members:', error);
                            this.showMessage('Gagal memuat data anggota keluarga', 'error');
                        }
                    },

                    async loadFingerprintTemplates() {
                        try {
                            const response = await fetch('/fingerprint/templates');
                            this.fingerprintTemplates = await response.json();
                        } catch (error) {
                            console.error('Error loading fingerprint templates:', error);
                        }
                    },

                    onFamilyMemberChange() {
                        this.lastCaptured = null;
                    },

                    async searchFamilyMembers() {
                        if (this.searchQuery.length < 2) {
                            this.searchResults = [];
                            this.showSearchResults = false;
                            return;
                        }

                        try {
                            const response = await fetch(
                                `/fingerprint/search-family-members?search=${encodeURIComponent(this.searchQuery)}`);
                            this.searchResults = await response.json();
                            this.showSearchResults = true;
                        } catch (error) {
                            console.error('Error searching family members:', error);
                            this.searchResults = [];
                        }
                    },

                    selectFamilyMember(member) {
                        this.selectedFamilyMember = member.id_keluarga;
                        this.selectedMemberData = member;
                        this.searchQuery = member.nama_keluarga;
                        this.showSearchResults = false;
                        this.onFamilyMemberChange();
                    },

                    getSelectedMemberName() {
                        if (this.selectedMemberData) {
                            return `${this.selectedMemberData.nama_keluarga} - ${this.selectedMemberData.karyawan?.nama_karyawan || ''}`;
                        }
                        const member = this.familyMembers.find(m => m.id_keluarga == this.selectedFamilyMember);
                        return member ? `${member.nama_keluarga} - ${member.karyawan?.nama_karyawan || ''}` : '';
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

                    async enrollFingerprint() {
                        if (!this.selectedFamilyMember) {
                            this.showMessage('Pilih anggota keluarga terlebih dahulu!', 'error');
                            return;
                        }

                        this.isCapturing = true;
                        this.showMessage('Menangkap sidik jari...', 'info');

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

                            if (data.ErrorCode === 0) {
                                this.lastCaptured = {
                                    template: data.TemplateBase64,
                                    image: data.BMPBase64,
                                    quality: data.ImageQuality,
                                    nfiq: data.NFIQ
                                };
                                this.showMessage(
                                    `Sidik jari berhasil ditangkap! Kualitas: ${data.ImageQuality}, NFIQ: ${data.NFIQ}`,
                                    'success');
                            } else {
                                this.showMessage(`Error: ${data.ErrorCode} - ${this.getErrorDescription(data.ErrorCode)}`,
                                    'error');
                            }
                        } catch (error) {
                            this.showMessage(`Error koneksi: ${error.message}. Pastikan SGIBIOSRV berjalan di port 8443`,
                                'error');
                        } finally {
                            this.isCapturing = false;
                        }
                    },

                    async saveEnrolledFingerprint() {
                        if (!this.selectedFamilyMember) {
                            this.showMessage('Pilih anggota keluarga terlebih dahulu!', 'error');
                            return;
                        }

                        if (!this.lastCaptured) {
                            this.showMessage('Tangkap sidik jari terlebih dahulu!', 'error');
                            return;
                        }

                        if (this.lastCaptured.quality < 70) {
                            this.showMessage(
                                'Kualitas sidik jari terlalu rendah! Silakan tangkap ulang dengan kualitas minimal 70.',
                                'error');
                            return;
                        }

                        try {
                            const response = await fetch('/fingerprint/save', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    id_keluarga: this.selectedFamilyMember,
                                    fingerprint_template: this.lastCaptured.template
                                })
                            });

                            const result = await response.json();

                            if (result.success) {
                                this.showMessage(result.message, 'success');
                                this.selectedFamilyMember = '';
                                this.selectedMemberData = null;
                                this.searchQuery = '';
                                this.searchResults = [];
                                this.showSearchResults = false;
                                this.lastCaptured = null;
                                this.loadFingerprintTemplates();
                                setTimeout(() => window.location.reload(), 2000);
                            } else {
                                this.showMessage('Gagal menyimpan fingerprint', 'error');
                            }
                        } catch (error) {
                            console.error('Error saving fingerprint:', error);
                            this.showMessage('Gagal menyimpan fingerprint', 'error');
                        }
                    },

                    resetEnrollment() {
                        this.selectedFamilyMember = '';
                        this.selectedMemberData = null;
                        this.searchQuery = '';
                        this.searchResults = [];
                        this.showSearchResults = false;
                        this.lastCaptured = null;
                        this.showMessage('Form pendaftaran direset', 'info');
                    },

                    resetVerification() {
                        this.verifyResult = null;
                        this.showMessage('Form verifikasi direset', 'info');
                    },

                    async verifyFingerprint() {
                        if (this.fingerprintTemplates.length === 0) {
                            this.showMessage('Belum ada fingerprint terdaftar!', 'error');
                            return;
                        }

                        this.isCapturing = true;
                        this.showMessage('Menangkap sidik jari...', 'info');

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
                            this.showMessage('Memverifikasi...', 'info');

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
                                    console.error('Error matching:', error);
                                }
                            }

                            if (bestScore > 100 && bestMatch) {
                                this.verifyResult = {
                                    success: true,
                                    data: bestMatch,
                                    score: bestScore,
                                    image: data.BMPBase64
                                };
                                this.showMessage(
                                    `✓ Verifikasi Berhasil! ${bestMatch.nama_keluarga} - ${bestMatch.karyawan?.nama_karyawan || ''} (Score: ${bestScore}/199)`,
                                    'success');
                            } else {
                                this.verifyResult = {
                                    success: false,
                                    data: null,
                                    score: bestScore,
                                    image: data.BMPBase64
                                };
                                this.showMessage(`✗ Sidik jari tidak cocok. Score tertinggi: ${bestScore}/199`, 'error');
                            }
                        } catch (error) {
                            this.showMessage(`Error koneksi: ${error.message}. Pastikan SGIBIOSRV berjalan di port 8443`,
                                'error');
                        } finally {
                            this.isCapturing = false;
                        }
                    },

                    async deleteFingerprint(id_keluarga) {
                        if (!confirm('Apakah Anda yakin ingin menghapus fingerprint ini?')) {
                            return;
                        }

                        try {
                            const response = await fetch('/fingerprint/delete', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    id_keluarga
                                })
                            });

                            const result = await response.json();

                            if (result.success) {
                                this.showMessage(result.message, 'success');
                                setTimeout(() => window.location.reload(), 1500);
                            } else {
                                this.showMessage('Gagal menghapus fingerprint', 'error');
                            }
                        } catch (error) {
                            console.error('Error deleting fingerprint:', error);
                            this.showMessage('Gagal menghapus fingerprint', 'error');
                        }
                    },

                    showMessage(msg, type = 'info') {
                        this.message = msg;
                        this.messageType = type;
                        setTimeout(() => {
                            this.message = '';
                        }, 5000);
                    }
                }
            }
        </script>
    @endpush
@endsection
