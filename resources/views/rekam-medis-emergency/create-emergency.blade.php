@extends('layouts.app')

@section('page-title', 'Tambah Rekam Medis Emergency')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <div class="bg-gradient-to-r from-red-600 to-pink-600 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tambah Rekam Medis Emergency</h1>
                <p class="text-gray-600 mt-1">Formulir pendaftaran pasien emergency</p>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
        <form action="{{ route('rekam-medis-emergency.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- NIK Pasien -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        NIK Pasien <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nik_pasien" value="{{ old('nik_pasien') }}" required
                           maxlength="16" pattern="[0-9]{1,16}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                           placeholder="Masukkan NIK pasien (angka saja)">
                    @error('nik_pasien')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Pasien -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Pasien <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_pasien" value="{{ old('nama_pasien') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                           placeholder="Masukkan nama lengkap pasien">
                    @error('nama_pasien')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- No RM -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        No RM <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="text" name="no_rm" value="{{ old('no_rm') }}" required
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                               placeholder="Contoh: 123123-F">
                        <button type="button" onclick="generateNoRM()" 
                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors"
                                title="Generate No RM">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Format: NIK + Kode Hubungan (F untuk Emergency)</p>
                    @error('no_rm')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin') == 'L' ? 'checked' : '' }} required
                                   class="mr-2 text-red-600 focus:ring-red-500">
                            <span>Laki-laki</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin') == 'P' ? 'checked' : '' }} required
                                   class="mr-2 text-red-600 focus:ring-red-500">
                            <span>Perempuan</span>
                        </label>
                    </div>
                    @error('jenis_kelamin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Periksa -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Periksa <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_periksa" value="{{ old('tanggal_periksa', now()->format('Y-m-d')) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    @error('tanggal_periksa')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Rekam Medis -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Status Rekam Medis <span class="text-red-500">*</span>
                    </label>
                    <select name="status_rekam_medis" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="On Progress" {{ old('status_rekam_medis', 'On Progress') == 'On Progress' ? 'selected' : '' }}>On Progress</option>
                        <option value="Close" {{ old('status_rekam_medis') == 'Close' ? 'selected' : '' }}>Close</option>
                    </select>
                    @error('status_rekam_medis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keluhan -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Keluhan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="keluhan" rows="3" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Deskripsikan keluhan pasien">{{ old('keluhan') }}</textarea>
                    @error('keluhan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Diagnosa -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Diagnosa
                    </label>
                    <textarea name="diagnosa" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Masukkan diagnosa awal (opsional)">{{ old('diagnosa') }}</textarea>
                    @error('diagnosa')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catatan -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan
                    </label>
                    <textarea name="catatan" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Masukkan catatan tambahan (opsional)">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Hidden fields -->
            <input type="hidden" name="hubungan" value="Emergency">

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 mt-6">
                <a href="{{ route('rekam-medis.index') }}" 
                   class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Simpan Data Emergency
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function generateNoRM() {
    const nik = document.querySelector('input[name="nik_pasien"]').value;
    if (nik) {
        document.querySelector('input[name="no_rm"]').value = nik + '-F';
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Silakan isi NIK pasien terlebih dahulu',
            confirmButtonColor: '#ef4444'
        });
    }
}

// Auto-generate No RM when NIK changes
document.querySelector('input[name="nik_pasien"]').addEventListener('input', function() {
    if (this.value) {
        document.querySelector('input[name="no_rm"]').value = this.value + '-F';
    }
});

// Validate NIK input (only numbers)
document.querySelector('input[name="nik_pasien"]').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
@endpush
@endsection