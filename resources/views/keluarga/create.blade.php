@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Tambah keluarga</h2>

        <form action="{{ route('keluarga.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- NIK Karyawan (Penanggung Jawab) -->
            <div>
                <label for="nik_karyawan" class="block text-sm font-medium text-gray-700 mb-2">
                    NIK Karyawan (Penanggung Jawab)
                </label>
                <select
                    id="nik_karyawan"
                    name="nik_karyawan"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                    <option value="">-- Pilih Karyawan --</option>
                    <option value="KRY001">KRY001 - Awang Rio</option>
                    <option value="KRY002">KRY002 - Budi Santoso</option>
                    <option value="KRY003">KRY003 - Citra Dewi</option>
                    <option value="KRY004">KRY004 - Dedi Kurniawan</option>
                    <option value="KRY005">KRY005 - Eka Putri</option>
                </select>
            </div>

            <!-- Hubungan -->
            <div>
                <label for="hubungan" class="block text-sm font-medium text-gray-700 mb-2">
                    Hubungan
                </label>
                <select
                    id="hubungan"
                    name="hubungan"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                    <option value="">-- Pilih Hubungan --</option>
                    <option value="Suami">Suami</option>
                    <option value="Istri">Istri</option>
                    <option value="Anak">Anak</option>
                    <option value="Orang Tua">Orang Tua</option>
                    <option value="Saudara Kandung">Saudara Kandung</option>
                </select>
            </div>

            <!-- Nama keluarga -->
            <div>
                <label for="nama_keluarga" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama keluarga
                </label>
                <input
                    type="text"
                    id="nama_keluarga"
                    name="nama_keluarga"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Masukkan nama keluarga"
                    required
                />
            </div>

            <!-- Jenis Kelamin -->
            <div>
                <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis Kelamin
                </label>
                <select
                    id="jenis_kelamin"
                    name="jenis_kelamin"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="Laki - Laki">Laki - Laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>

            <!-- Tanggal Lahir -->
            <div>
                <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Lahir
                </label>
                <input
                    type="date"
                    id="tanggal_lahir"
                    name="tanggal_lahir"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="dd/mm/yyyy"
                    required
                />
            </div>

            <!-- Alamat -->
            <div>
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                    Alamat
                </label>
                <textarea
                    id="alamat"
                    name="alamat"
                    rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Masukkan alamat lengkap"
                    required
                ></textarea>
            </div>

            <!-- Tanggal Daftar -->
            <div>
                <label for="tanggal_daftar" class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Daftar
                </label>
                <input
                    type="date"
                    id="tanggal_daftar"
                    name="tanggal_daftar"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    value="{{ date('Y-m-d') }}"
                    required
                />
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 pt-4">
                <button
                    type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors"
                >
                    Simpan
                </button>
                <a
                    href="{{ route('keluarga.index') }}"
                    class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors"
                >
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
