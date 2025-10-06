@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Header with Back Button -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('keluarga.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h2 class="text-2xl font-bold text-gray-800">Edit keluarga</h2>
            </div>
        </div>

        <form action="{{ route('keluarga.update', 1) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- NO RM (Readonly) -->
                <div>
                    <label for="no_rm" class="block text-sm font-medium text-gray-700 mb-2">
                        NO RM
                    </label>
                    <input
                        type="text"
                        id="no_rm"
                        name="no_rm"
                        value="0001/ND_ILM/10/2025"
                        class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed"
                        readonly
                    />
                </div>

                <!-- NIK Karyawan -->
                <div>
                    <label for="nik_karyawan" class="block text-sm font-medium text-gray-700 mb-2">
                        NIK Karyawan (Penanggung Jawab) <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="nik_karyawan"
                        name="nik_karyawan"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        required
                    >
                        <option value="">-- Pilih NIK Karyawan --</option>
                        <option value="KRY001" selected>KRY001 - Awang Rio</option>
                        <option value="KRY002">KRY002 - Budi Santoso</option>
                        <option value="KRY003">KRY003 - Citra Dewi</option>
                        <option value="KRY004">KRY004 - Dedi Kurniawan</option>
                        <option value="KRY005">KRY005 - Eka Putri</option>
                    </select>
                </div>

                <!-- Hubungan -->
                <div>
                    <label for="hubungan" class="block text-sm font-medium text-gray-700 mb-2">
                        Hubungan <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="hubungan"
                        name="hubungan"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        required
                    >
                        <option value="">-- Pilih Hubungan --</option>
                        <option value="Karyawan" selected>Karyawan</option>
                        <option value="Istri">Istri</option>
                        <option value="Suami">Suami</option>
                        <option value="Anak">Anak</option>
                        <option value="Orang Tua">Orang Tua</option>
                    </select>
                </div>

                <!-- Nama keluarga -->
                <div>
                    <label for="nama_keluarga" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama keluarga <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="nama_keluarga"
                        name="nama_keluarga"
                        value="Awang Rio"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        placeholder="Masukkan nama keluarga"
                        required
                    />
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="jenis_kelamin"
                        name="jenis_kelamin"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        required
                    >
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Laki - Laki" selected>Laki - Laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Lahir <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        id="tanggal_lahir"
                        name="tanggal_lahir"
                        value="1998-09-01"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        required
                    />
                </div>
            </div>

            <!-- Alamat (Full Width) -->
            <div>
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                    Alamat <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="alamat"
                    name="alamat"
                    rows="3"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                    placeholder="Masukkan alamat lengkap"
                    required
>Jl. Bambu Darat GG. BDA Banjarsasih Utara</textarea>
            </div>

            <!-- Tanggal Kunjungan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="tanggal_kunjungan" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Kunjungan <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        id="tanggal_kunjungan"
                        name="tanggal_kunjungan"
                        value="2025-10-03"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        required
                    />
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <button
                    type="submit"
                    class="flex-1 sm:flex-none px-8 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Update
                </button>
                <a
                    href="{{ route('keluarga.index') }}"
                    class="flex-1 sm:flex-none px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
