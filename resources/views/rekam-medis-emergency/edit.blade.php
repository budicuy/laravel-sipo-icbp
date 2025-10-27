@extends('layouts.app')

@section('page-title', 'Edit Rekam Medis Emergency')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <div class="bg-gradient-to-r from-red-600 to-pink-600 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Rekam Medis Emergency</h1>
                <p class="text-gray-600 mt-1">Perbarui data rekam medis pasien emergency</p>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
        <form action="{{ route('rekam-medis-emergency.update', $rekamMedisEmergency->id_emergency) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Hidden input untuk external_employee_id -->
            <input type="hidden" name="external_employee_id" value="{{ $rekamMedisEmergency->id_external_employee }}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Employee Info Display (Read-only) -->
                <div class="md:col-span-2 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 p-5 rounded-lg">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-800">Informasi Karyawan Emergency</h3>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600 block mb-1">NIK Karyawan:</span>
                            <p class="font-semibold text-gray-900">{{ $rekamMedisEmergency->externalEmployee->nik_employee ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 block mb-1">Nama Lengkap:</span>
                            <p class="font-semibold text-gray-900">{{ $rekamMedisEmergency->externalEmployee->nama_employee ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 block mb-1">No. Rekam Medis:</span>
                            <p class="font-semibold text-gray-900">{{ $rekamMedisEmergency->externalEmployee->kode_rm ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 block mb-1">Jenis Kelamin:</span>
                            <p class="font-semibold text-gray-900">{{ $rekamMedisEmergency->externalEmployee->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-red-200">
                        <p class="text-xs text-gray-600 italic">
                            <svg class="w-4 h-4 inline text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            Data karyawan tidak dapat diubah saat edit. Jika perlu mengubah karyawan, silakan buat rekam medis baru.
                        </p>
                    </div>
                </div>

                <!-- Tanggal Periksa -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Periksa <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_periksa" value="{{ old('tanggal_periksa', $rekamMedisEmergency->tanggal_periksa->format('Y-m-d')) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    @error('tanggal_periksa')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Waktu Periksa -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Waktu Periksa
                    </label>
                    <input type="time" name="waktu_periksa" value="{{ old('waktu_periksa', $rekamMedisEmergency->waktu_periksa ? $rekamMedisEmergency->waktu_periksa->format('H:i:s') : '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    @error('waktu_periksa')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Rekam Medis -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Status Rekam Medis <span class="text-red-500">*</span>
                    </label>
                    <select name="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="On Progress" {{ old('status', $rekamMedisEmergency->status) == 'On Progress' ? 'selected' : '' }}>On Progress</option>
                        <option value="Close" {{ old('status', $rekamMedisEmergency->status) == 'Close' ? 'selected' : '' }}>Close</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keluhan -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Keluhan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="keluhan" rows="3" required
                              class="w-full px-4 py-2 border @error('keluhan') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Deskripsikan keluhan pasien (minimal 10 karakter)">{{ old('keluhan', $rekamMedisEmergency->keluhan) }}</textarea>
                    @error('keluhan')
                        <div class="mt-2 flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-1.5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-red-600 font-medium">{{ $message }}</p>
                        </div>
                    @enderror
                </div>

                <!-- Diagnosa Emergency -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Diagnosa Emergency <span class="text-red-500">*</span>
                    </label>
                    <select name="id_diagnosa_emergency" required
                            class="w-full px-4 py-2 border @error('id_diagnosa_emergency') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">-- Pilih Diagnosa Emergency --</option>
                        @foreach($diagnosaEmergency as $diagnosa)
                            <option value="{{ $diagnosa->id_diagnosa_emergency }}"
                                    {{ old('id_diagnosa_emergency', $rekamMedisEmergency->keluhans->first()->id_diagnosa_emergency ?? null) == $diagnosa->id_diagnosa_emergency ? 'selected' : '' }}>
                                {{ $diagnosa->nama_diagnosa_emergency }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_diagnosa_emergency')
                        <div class="mt-2 flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-1.5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-red-600 font-medium">{{ $message }}</p>
                        </div>
                    @enderror
                </div>

                <!-- Terapi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Terapi <span class="text-red-500">*</span>
                    </label>
                    <select name="terapi" required
                            class="w-full px-4 py-2 border @error('terapi') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">-- Pilih Terapi --</option>
                        <option value="Obat" {{ old('terapi', $rekamMedisEmergency->keluhans->first()->terapi ?? null) == 'Obat' ? 'selected' : '' }}>Obat</option>
                        <option value="Lab" {{ old('terapi', $rekamMedisEmergency->keluhans->first()->terapi ?? null) == 'Lab' ? 'selected' : '' }}>Lab</option>
                        <option value="Istirahat" {{ old('terapi', $rekamMedisEmergency->keluhans->first()->terapi ?? null) == 'Istirahat' ? 'selected' : '' }}>Istirahat</option>
                        <option value="Emergency" {{ old('terapi', $rekamMedisEmergency->keluhans->first()->terapi ?? null) == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                    </select>
                    @error('terapi')
                        <div class="mt-2 flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-1.5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-red-600 font-medium">{{ $message }}</p>
                        </div>
                    @enderror
                </div>

                <!-- Catatan -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan
                    </label>
                    <textarea name="catatan" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Masukkan catatan tambahan (opsional)">{{ old('catatan', $rekamMedisEmergency->catatan) }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>


            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 mt-6">
                <a href="{{ route('rekam-medis-emergency.show', $rekamMedisEmergency->id_emergency) }}"
                   class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Data Emergency
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Note: Employee selection removed from edit page
// Employee information is now read-only and cannot be changed during edit
// If you need to change the employee, please create a new emergency medical record
</script>
@endpush
@endsection
