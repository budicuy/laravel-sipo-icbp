@extends('layouts.app')

@section('page-title', 'Tambah Rekam Medis')

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
                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-3 rounded-lg shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    Tambah Rekam Medis
                </h1>
                <p class="text-gray-600 mt-1 ml-1">Buat rekam medis baru untuk pasien</p>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('rekam-medis.store') }}" method="POST">
        @csrf

        <!-- Data Pasien Section -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Data Pasien
                </h2>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pilih Pasien dengan Search -->
                    <div class="md:col-span-2">
                        <label for="id_keluarga" class="block text-sm font-semibold text-gray-700 mb-2">
                            Pilih Pasien <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="hidden" id="id_keluarga" name="id_keluarga" required>
                            <input type="text" id="search_pasien"
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                   placeholder="Cari pasien berdasarkan nama atau NIK..."
                                   autocomplete="off">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <!-- Search Results Dropdown -->
                            <div id="search_results" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <!-- Results will be populated by JavaScript -->
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
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                </svg>
                            </div>
                            <input type="text" id="no_rm" name="no_rm" class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-gray-600" placeholder="Otomatis terisi" readonly>
                        </div>
                    </div>

                    <!-- Nama Pasien (Auto-filled) -->
                    <div>
                        <label for="nama_pasien" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Pasien
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" id="nama_pasien" class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-gray-600" placeholder="Otomatis terisi" readonly>
                        </div>
                    </div>

                    <!-- NIK Karyawan (Auto-filled) -->
                    <div>
                        <label for="nik_karyawan" class="block text-sm font-semibold text-gray-700 mb-2">
                            NIK Karyawan
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                            </div>
                            <input type="text" id="nik_karyawan" class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-gray-600" placeholder="Otomatis terisi" readonly>
                        </div>
                    </div>

                    <!-- Hubungan (Auto-filled) -->
                    <div>
                        <label for="hubungan" class="block text-sm font-semibold text-gray-700 mb-2">
                            Hubungan
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <input type="text" id="hubungan" class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-gray-600" placeholder="Otomatis terisi" readonly>
                        </div>
                    </div>

                    <!-- Jenis Kelamin (Auto-filled) -->
                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-semibold text-gray-700 mb-2">
                            Jenis Kelamin
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" id="jenis_kelamin" class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-gray-600" placeholder="Otomatis terisi" readonly>
                        </div>
                    </div>

                    <!-- Tanggal Periksa -->
                    <div>
                        <label for="tanggal_periksa" class="block text-sm font-semibold text-gray-700 mb-2">
                            Tanggal Periksa <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="date" id="tanggal_periksa" name="tanggal_periksa" value="{{ old('tanggal_periksa', date('Y-m-d')) }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                        </div>
                    </div>

                    <!-- Status Rekam Medis -->
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                            Status Rekam Medis <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="status" name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white" required>
                                <option value="On Orogres" {{ old('status', 'On Orogres') == 'On Orogres' ? 'selected' : '' }}>On Orogres</option>
                                <option value="Close" {{ old('status') == 'Close' ? 'selected' : '' }}>Close</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
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
                            <select id="jumlah_keluhan" name="jumlah_keluhan" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white" required onchange="updateKeluhanSections(this.value)">
                                <option value="1" {{ old('jumlah_keluhan', 1) == 1 ? 'selected' : '' }}>1 Keluhan</option>
                                <option value="2" {{ old('jumlah_keluhan') == 2 ? 'selected' : '' }}>2 Keluhan</option>
                                <option value="3" {{ old('jumlah_keluhan') == 3 ? 'selected' : '' }}>3 Keluhan</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Combined Diagnosa & Resep Section -->
        <div id="keluhan-container">
            <!-- Keluhan 1 (Template) -->
            <div class="keluhan-section bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6" data-keluhan-index="0">
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
                                        @foreach($diagnosas as $diagnosa)
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
                                    Keterangan / Catatan
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
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" onclick="window.location.href='{{ route('rekam-medis.index') }}'" class="px-6 py-2.5 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-medium rounded-lg transition-all hover:shadow-md">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
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
let searchTimeout;

// Search pasien dengan AJAX
document.getElementById('search_pasien').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const searchValue = this.value.trim();

    if (searchValue.length < 2) {
        document.getElementById('search_results').classList.add('hidden');
        return;
    }

    searchTimeout = setTimeout(function() {
        fetch(`{{ route('rekam-medis.searchPasien') }}?q=${encodeURIComponent(searchValue)}`)
            .then(response => response.json())
            .then(data => {
                const resultsDiv = document.getElementById('search_results');

                if (data.length === 0) {
                    resultsDiv.innerHTML = '<div class="px-4 py-3 text-gray-500 text-sm">Tidak ada pasien ditemukan</div>';
                } else {
                    resultsDiv.innerHTML = data.map(pasien => `
                        <div class="px-4 py-3 hover:bg-green-50 cursor-pointer border-b border-gray-100 transition-colors" onclick="selectPasien(${JSON.stringify(pasien).replace(/"/g, '&quot;')})">
                            <div class="font-medium text-gray-900">${pasien.nama}</div>
                            <div class="text-sm text-gray-600">NIK Karyawan (Penanggung Jawab): ${pasien.nik_karyawan || '-'}</div>
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

// Select pasien from dropdown
function selectPasien(pasien) {
    // Set values
    document.getElementById('id_keluarga').value = pasien.id;
    document.getElementById('search_pasien').value = pasien.nama;
    // Auto-fill No RM dengan format: NIK_KARYAWAN-KODE_HUBUNGAN
    document.getElementById('no_rm').value = `${pasien.nik_karyawan}-${pasien.kode_hubungan}`;
    document.getElementById('nama_pasien').value = pasien.nama;
    document.getElementById('nik_karyawan').value = pasien.nik_karyawan;
    document.getElementById('hubungan').value = `${pasien.kode_hubungan}. ${pasien.hubungan}`;
    document.getElementById('jenis_kelamin').value = pasien.jenis_kelamin;

    // Hide results
    document.getElementById('search_results').classList.add('hidden');
}

// Hide search results when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#search_pasien') && !e.target.closest('#search_results')) {
        document.getElementById('search_results').classList.add('hidden');
    }
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
            obatContainer.querySelector('.obat-list').innerHTML = '<p class="text-sm text-gray-500 italic">Pilih diagnosa terlebih dahulu untuk menampilkan daftar obat yang sesuai.</p>';
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
    const obatContainer = keluhanSection.querySelector('.obat-checkbox-container[data-keluhan-index="' + keluhanIndex + '"]');
    const obatList = obatContainer.querySelector('.obat-list');
    const detailsContainer = keluhanSection.querySelector('.selected-obat-details[data-keluhan-index="' + keluhanIndex + '"]');

    if (!diagnosaId) {
        // Reset obat list if no diagnosa selected
        obatList.innerHTML = '<p class="text-sm text-gray-500 italic">Pilih diagnosa terlebih dahulu untuk menampilkan daftar obat yang sesuai.</p>';
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
                obatList.innerHTML = '<p class="text-sm text-gray-500 italic">Tidak ada obat terkait dengan diagnosa ini.</p>';
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
    const checkedBoxes = keluhanSection.querySelectorAll(`.obat-checkbox[data-keluhan-index="${keluhanIndex}"]:checked`);
    const detailsContainer = keluhanSection.querySelector(`.selected-obat-details[data-keluhan-index="${keluhanIndex}"]`);
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
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                               placeholder="Masukkan jumlah obat">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3" />
                            </svg>
                            Aturan Pakai
                        </label>
                        <input type="text"
                               name="keluhan[${keluhanIndex}][obat_list][${index}][aturan_pakai]"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                               placeholder="Contoh: 3x sehari setelah makan">
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
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    attachDiagnosaChangeListeners();
});
</script>
@endpush
@endsection
