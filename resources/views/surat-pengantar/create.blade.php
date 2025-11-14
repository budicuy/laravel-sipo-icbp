@extends('layouts.app')

@section('page-title', 'Buat Surat Pengantar Istirahat')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                Buat Surat Pengantar Istirahat
            </h1>
            <p class="text-gray-600 mt-2 ml-1">Lengkapi form berikut untuk membuat surat pengantar istirahat</p>
        </div>

        <!-- Patient Info Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pasien</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-600">Nama Pasien</label>
                    <p class="text-gray-900 font-medium">{{ $rekamMedis->keluarga->nama_keluarga }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">NIK Penanggung Jawab</label>
                    <p class="text-gray-900 font-medium">{{ $rekamMedis->keluarga->karyawan->nik_karyawan ?? 'N/A' }}
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">No. Rekam Medis</label>
                    <p class="text-gray-900 font-medium">{{ $rekamMedis->keluarga->no_rm }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Tanggal Periksa</label>
                    <p class="text-gray-900 font-medium">{{
                        \Carbon\Carbon::parse($rekamMedis->tanggal_periksa)->format('d M Y') }}</p>
                </div>
                <div class="col-span-2">
                    <label class="text-sm font-medium text-gray-600">Diagnosa</label>
                    <p class="text-gray-900 font-medium">
                        @php
                        $diagnosaList = $rekamMedis->keluhans->map(function($keluhan) {
                        return $keluhan->diagnosa->nama_diagnosa ?? null;
                        })->filter()->unique()->toArray();
                        @endphp
                        {{ !empty($diagnosaList) ? implode(', ', $diagnosaList) : 'Tidak ada diagnosa' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <form action="{{ route('surat-pengantar.store') }}" method="POST">
                @csrf
                <input type="hidden" name="rekam_medis_id" value="{{ $rekamMedis->id_rekam }}">

                <div class="space-y-6">
                    <!-- Lama Istirahat -->
                    <div>
                        <label for="lama_istirahat" class="block text-sm font-medium text-gray-700 mb-2">
                            Lama Istirahat (Hari) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="lama_istirahat" id="lama_istirahat" min="1" max="10"
                            value="{{ old('lama_istirahat', 1) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('lama_istirahat') border-red-500 @enderror"
                            required>
                        @error('lama_istirahat')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">Maksimal 10 hari istirahat</p>
                    </div>

                    <!-- Tanggal Mulai Istirahat -->
                    <div>
                        <label for="tanggal_mulai_istirahat" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Mulai Istirahat <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_mulai_istirahat" id="tanggal_mulai_istirahat"
                            value="{{ old('tanggal_mulai_istirahat', date('Y-m-d')) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('tanggal_mulai_istirahat') border-red-500 @enderror"
                            required>
                        @error('tanggal_mulai_istirahat')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan (Opsional)
                        </label>
                        <textarea name="catatan" id="catatan" rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('catatan') border-red-500 @enderror"
                            placeholder="Tambahkan catatan tambahan jika diperlukan...">{{ old('catatan') }}</textarea>
                        @error('catatan')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <a href="{{ route('rekam-medis.index') }}"
                            class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-all">
                            Batal
                        </a>
                        <button type="submit"
                            class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-2.5 rounded-lg font-medium shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Buat & Cetak Surat
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection