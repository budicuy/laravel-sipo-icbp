@extends('layouts.app')

@section('page-title', 'Tambah Karyawan')

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Tambah Karyawan</h2>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="#" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Import Data Section -->
            <div class="mb-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Import Data Karyawan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Import Data Karyawan</label>
                    <div class="flex gap-2">
                        <input type="file" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <button type="button" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
                            Import
                        </button>
                        <button type="button" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-md transition-colors">
                            Download Template
                        </button>
                    </div>
                </div>

                <!-- Foto Karyawan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Karyawan (untuk Tambah Manual)</label>
                    <div class="flex gap-4">
                        <input type="file" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <div class="w-32 h-32 border-2 border-dashed border-gray-300 rounded-md flex items-center justify-center bg-gray-50">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-xs text-gray-500 mt-1">Max 2MB</p>
                                <p class="text-xs text-gray-500">Format: jpg,jpeg,png,webp</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-6">

            <!-- Tambah Manual Section -->
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tambah Manual</h3>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <!-- NIK -->
                    <div>
                        <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                        <input type="text" id="nik" name="nik" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <!-- Nama Karyawan -->
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Karyawan</label>
                        <input type="text" id="nama" name="nama" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <!-- Departemen -->
                    <div>
                        <label for="departemen" class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                        <select id="departemen" name="departemen" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">-- Pilih Departemen --</option>
                            <option value="ADM HR">ADM HR</option>
                            <option value="ADM Financial & Accounting">ADM Financial & Accounting</option>
                            <option value="MFG Production">MFG Production</option>
                            <option value="MKT Marketing">MKT Marketing</option>
                            <option value="MFG Technical">MFG Technical</option>
                        </select>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <select id="jenis_kelamin" name="jenis_kelamin" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki - Laki">Laki - Laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>

                    <!-- No HP -->
                    <div>
                        <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
                        <input type="text" id="no_hp" name="no_hp" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <!-- Foto Preview -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Foto Preview</label>
                        <div class="w-full h-48 border-2 border-dashed border-gray-300 rounded-md flex items-center justify-center bg-gray-50">
                            <div class="text-center">
                                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-sm text-gray-500 mt-2">Preview foto akan muncul di sini</p>
                            </div>
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors">
                    Simpan
                </button>
                <button type="button" onclick="window.location.href='{{ route('karyawan.index') }}'" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-md transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Preview image before upload
    document.querySelector('input[type="file"][name="foto"]')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.querySelector('.foto-preview');
                if (preview) {
                    preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded-md" alt="Preview">`;
                }
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection
