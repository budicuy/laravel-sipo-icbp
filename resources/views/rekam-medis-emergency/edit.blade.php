@extends('layouts.app')

@section('page-title', 'Edit Rekam Medis Emergency')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex items-center gap-3">
                <div class="bg-gradient-to-r from-red-600 to-pink-600 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <h3 class="text-sm font-semibold text-gray-800">Informasi Karyawan Emergency</h3>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600 block mb-1">NIK Karyawan:</span>
                                <p class="font-semibold text-gray-900">
                                    {{ $rekamMedisEmergency->externalEmployee->nik_employee ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600 block mb-1">Nama Lengkap:</span>
                                <p class="font-semibold text-gray-900">
                                    {{ $rekamMedisEmergency->externalEmployee->nama_employee ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600 block mb-1">No. Rekam Medis:</span>
                                <p class="font-semibold text-gray-900">
                                    {{ $rekamMedisEmergency->externalEmployee->kode_rm ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600 block mb-1">Jenis Kelamin:</span>
                                <p class="font-semibold text-gray-900">
                                    {{ $rekamMedisEmergency->externalEmployee->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-red-200">
                            <p class="text-xs text-gray-600 italic">
                                <svg class="w-4 h-4 inline text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                                Data karyawan tidak dapat diubah saat edit. Jika perlu mengubah karyawan, silakan buat rekam
                                medis baru.
                            </p>
                        </div>
                    </div>

                    <!-- Tanggal Periksa -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Periksa <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_periksa"
                            value="{{ old('tanggal_periksa', $rekamMedisEmergency->tanggal_periksa->format('Y-m-d')) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        @error('tanggal_periksa')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Waktu Periksa -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Waktu Periksa
                        </label>
                        <input type="time" name="waktu_periksa"
                            value="{{ old('waktu_periksa', $rekamMedisEmergency->waktu_periksa ? $rekamMedisEmergency->waktu_periksa->format('H:i') : '') }}"
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
                            <option value="On Progress"
                                {{ old('status', $rekamMedisEmergency->status) == 'On Progress' ? 'selected' : '' }}>On
                                Progress</option>
                            <option value="Close"
                                {{ old('status', $rekamMedisEmergency->status) == 'Close' ? 'selected' : '' }}>Close
                            </option>
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

                    <!-- Diagnosa Emergency -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Diagnosa Emergency <span class="text-red-500">*</span>
                        </label>
                        <select name="id_diagnosa_emergency" required
                            class="w-full px-4 py-2 border @error('id_diagnosa_emergency') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">-- Pilih Diagnosa Emergency --</option>
                            @foreach ($diagnosaEmergency as $diagnosa)
                                <option value="{{ $diagnosa->id_diagnosa_emergency }}"
                                    {{ old('id_diagnosa_emergency', $rekamMedisEmergency->keluhans->first()->id_diagnosa_emergency ?? null) == $diagnosa->id_diagnosa_emergency ? 'selected' : '' }}>
                                    {{ $diagnosa->nama_diagnosa_emergency }}
                                </option>
                            @endforeach
                        </select>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Terapi <span class="text-red-500">*</span>
                        </label>
                        <select name="terapi" id="terapi" required
                            class="w-full px-4 py-2 border @error('terapi') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">-- Pilih Terapi --</option>
                            <option value="Obat"
                                {{ old('terapi', $rekamMedisEmergency->keluhans->first()->terapi ?? null) == 'Obat' ? 'selected' : '' }}>
                                Obat</option>
                            <option value="Lab"
                                {{ old('terapi', $rekamMedisEmergency->keluhans->first()->terapi ?? null) == 'Lab' ? 'selected' : '' }}>
                                Lab</option>
                            <option value="Istirahat"
                                {{ old('terapi', $rekamMedisEmergency->keluhans->first()->terapi ?? null) == 'Istirahat' ? 'selected' : '' }}>
                                Istirahat</option>
                            <option value="Emergency"
                                {{ old('terapi', $rekamMedisEmergency->keluhans->first()->terapi ?? null) == 'Emergency' ? 'selected' : '' }}>
                                Emergency</option>
                        </select>
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

                    <!-- Obat Section (Conditional) -->
                    <div class="md:col-span-2" id="obat-section"
                        style="display: {{ old('terapi', $rekamMedisEmergency->keluhans->first()->terapi ?? null) == 'Obat' ? 'block' : 'none' }};">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                <h3 class="text-sm font-semibold text-gray-800">Daftar Obat yang Direkomendasikan</h3>
                            </div>

                            <div id="obat-list-container" class="space-y-3">
                                @php
                                    $existingObats = old(
                                        'obat_list',
                                        $rekamMedisEmergency->keluhans
                                            ->whereNotNull('id_obat')
                                            ->map(function ($keluhan) {
                                                return [
                                                    'id_obat' => $keluhan->id_obat,
                                                    'jumlah_obat' => $keluhan->jumlah_obat,
                                                    'aturan_pakai' => $keluhan->aturan_pakai,
                                                    'nama_obat' => $keluhan->obat->nama_obat ?? '',
                                                ];
                                            })
                                            ->toArray(),
                                    );
                                @endphp

                                @if (count($existingObats) > 0)
                                    @foreach ($existingObats as $index => $obat)
                                        <div class="obat-item bg-white border border-gray-200 rounded-lg p-4"
                                            data-index="{{ $index }}">
                                            <div class="flex items-start justify-between mb-3">
                                                <div class="flex-1">
                                                    <label class="text-sm font-semibold text-gray-700">Obat
                                                        #{{ $index + 1 }}</label>
                                                    <p class="text-xs text-gray-500 mt-1">{{ $obat['nama_obat'] }}</p>
                                                </div>
                                                <button type="button" onclick="removeObat(this)"
                                                    class="text-red-500 hover:text-red-700 hover:bg-red-50 p-1.5 rounded transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <input type="hidden" name="obat_list[{{ $index }}][id_obat]"
                                                value="{{ $obat['id_obat'] }}">

                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                                        Jumlah <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="number" name="obat_list[{{ $index }}][jumlah_obat]"
                                                        value="{{ $obat['jumlah_obat'] }}" min="1" max="100"
                                                        required
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                        placeholder="Qty">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                                        Aturan Pakai
                                                    </label>
                                                    <select name="obat_list[{{ $index }}][aturan_pakai]"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        <option value="">-- Pilih Aturan Pakai --</option>
                                                        <option value="1 x sehari sebelum makan"
                                                            {{ $obat['aturan_pakai'] == '1 x sehari sebelum makan' ? 'selected' : '' }}>
                                                            1 x sehari sebelum makan</option>
                                                        <option value="1 x sehari sesudah makan"
                                                            {{ $obat['aturan_pakai'] == '1 x sehari sesudah makan' ? 'selected' : '' }}>
                                                            1 x sehari sesudah makan</option>
                                                        <option value="2 x sehari sebelum makan"
                                                            {{ $obat['aturan_pakai'] == '2 x sehari sebelum makan' ? 'selected' : '' }}>
                                                            2 x sehari sebelum makan</option>
                                                        <option value="2 x sehari setelah makan"
                                                            {{ $obat['aturan_pakai'] == '2 x sehari setelah makan' ? 'selected' : '' }}>
                                                            2 x sehari setelah makan</option>
                                                        <option value="3 x sehari sebelum makan"
                                                            {{ $obat['aturan_pakai'] == '3 x sehari sebelum makan' ? 'selected' : '' }}>
                                                            3 x sehari sebelum makan</option>
                                                        <option value="3 x sehari sesudah makan"
                                                            {{ $obat['aturan_pakai'] == '3 x sehari sesudah makan' ? 'selected' : '' }}>
                                                            3 x sehari sesudah makan</option>
                                                        <option value="1 x pakai"
                                                            {{ $obat['aturan_pakai'] == '1 x pakai' ? 'selected' : '' }}>1 x pakai
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-sm text-gray-500 italic text-center py-4">Pilih diagnosa emergency terlebih
                                        dahulu untuk menampilkan obat yang sesuai.</p>
                                @endif
                            </div>
                        </div>
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
            let obatIndex = {{ count($existingObats ?? []) }};
            let diagnosaObatMap = {};

            // Load diagnosa with obat mapping
            async function loadDiagnosaObatMapping() {
                try {
                    const response = await fetch('{{ route('rekam-medis-emergency.getDiagnosaWithObat') }}');
                    const data = await response.json();

                    data.forEach(diagnosa => {
                        diagnosaObatMap[diagnosa.id_diagnosa_emergency] = diagnosa.obats;
                    });
                } catch (error) {
                    console.error('Error loading diagnosa obat mapping:', error);
                }
            }

            // Show/hide obat section based on terapi selection
            document.getElementById('terapi').addEventListener('change', function() {
                const obatSection = document.getElementById('obat-section');
                if (this.value === 'Obat') {
                    obatSection.style.display = 'block';
                } else {
                    obatSection.style.display = 'none';
                }
            });

            // Handle diagnosa emergency change
            document.querySelector('select[name="id_diagnosa_emergency"]').addEventListener('change', function() {
                const diagnosaId = this.value;
                const terapiSelect = document.getElementById('terapi');

                if (diagnosaId && terapiSelect.value === 'Obat') {
                    loadObatListForDiagnosa(diagnosaId);
                }
            });

            // Load obat list based on diagnosa
            async function loadObatListForDiagnosa(diagnosaId) {
                const obatListContainer = document.getElementById('obat-list-container');

                if (!diagnosaId) {
                    obatListContainer.innerHTML =
                        '<p class="text-sm text-gray-500 italic text-center py-4">Pilih diagnosa emergency terlebih dahulu untuk menampilkan obat yang sesuai.</p>';
                    return;
                }

                obatListContainer.innerHTML = '<p class="text-sm text-gray-500 italic">Memuat daftar obat...</p>';

                try {
                    const obats = diagnosaObatMap[diagnosaId] || [];

                    if (obats.length === 0) {
                        obatListContainer.innerHTML =
                            '<p class="text-sm text-gray-500 italic text-center py-4">Tidak ada obat yang terkait dengan diagnosa ini.</p>';
                        return;
                    }

                    // Clear and rebuild obat list
                    obatListContainer.innerHTML = '';
                    obatIndex = 0;

                    obats.forEach((obat, index) => {
                        addObatItem(obat.id_obat, obat.nama_obat);
                    });

                } catch (error) {
                    console.error('Error loading obat list:', error);
                    obatListContainer.innerHTML =
                        '<p class="text-sm text-red-500">Gagal memuat daftar obat. Silakan coba lagi.</p>';
                }
            }

            // Add obat item to the list
            function addObatItem(obatId, obatNama) {
                const container = document.getElementById('obat-list-container');
                const index = obatIndex++;

                const obatHtml = `
                <div class="obat-item bg-white border border-gray-200 rounded-lg p-4" data-index="${index}">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <label class="text-sm font-semibold text-gray-700">Obat #${index + 1}</label>
                            <p class="text-xs text-gray-500 mt-1">${obatNama}</p>
                        </div>
                        <button type="button" onclick="removeObat(this)"
                            class="text-red-500 hover:text-red-700 hover:bg-red-50 p-1.5 rounded transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <input type="hidden" name="obat_list[${index}][id_obat]" value="${obatId}">

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                Jumlah <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="obat_list[${index}][jumlah_obat]"
                                value="1" min="1" max="100" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Qty">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                Aturan Pakai
                            </label>
                            <select name="obat_list[${index}][aturan_pakai]"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
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

                container.insertAdjacentHTML('beforeend', obatHtml);
            }

            // Remove obat item
            function removeObat(button) {
                const obatItem = button.closest('.obat-item');
                obatItem.remove();

                // Renumber remaining items
                document.querySelectorAll('.obat-item').forEach((item, idx) => {
                    item.querySelector('label').textContent = `Obat #${idx + 1}`;
                });
            }

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                loadDiagnosaObatMapping();
            });
        </script>
    @endpush
@endsection
