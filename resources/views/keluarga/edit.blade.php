@extends('layouts.app')

@section('page-title', 'Edit Data Keluarga')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-3">
            <a href="{{ route('keluarga.index') }}" class="p-2 hover:bg-white rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-3 rounded-lg shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    Edit Data Keluarga Karyawan
                </h1>
                <p class="text-gray-600 mt-1 ml-1">Perbarui informasi data keluarga karyawan</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('keluarga.update', $keluarga->id_keluarga) }}" id="keluargaForm">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Informasi Data Keluarga
                </h2>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- NIK Karyawan dengan Search -->
                    <div class="md:col-span-2">
                        <label for="nik_search" class="block text-sm font-semibold text-gray-700 mb-2">
                            NIK Karyawan <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" id="nik_search" autocomplete="off"
                                   class="w-full px-4 py-2.5 border @error('id_karyawan') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                                   placeholder="Ketik NIK (hanya angka) atau nama karyawan...">
                            <div id="search_results" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-64 overflow-y-auto">
                                <!-- Search results will be displayed here -->
                            </div>
                        </div>
                        <input type="hidden" name="id_karyawan" id="id_karyawan" value="{{ old('id_karyawan', $keluarga->id_karyawan) }}">
                        <div id="selected_karyawan" class="mt-2">
                            <div class="flex items-center gap-3 p-3 bg-purple-50 border border-purple-200 rounded-lg">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900" id="selected_nama">{{ optional($keluarga->karyawan)->nama_karyawan }}</p>
                                    <p class="text-xs text-gray-600" id="selected_nik">NIK: {{ optional($keluarga->karyawan)->nik_karyawan }}</p>
                                </div>
                                <button type="button" onclick="enableSearch()" class="text-purple-600 hover:text-purple-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @error('id_karyawan')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hubungan -->
                    <div>
                        <label for="kode_hubungan" class="block text-sm font-semibold text-gray-700 mb-2">
                            Hubungan dengan Karyawan <span class="text-red-600">*</span>
                        </label>
                        <select name="kode_hubungan" id="kode_hubungan" required onchange="handleHubunganChange()"
                                class="w-full px-4 py-2.5 border @error('kode_hubungan') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm">
                            <option value="">-- Pilih Hubungan --</option>
                            <option value="A" {{ old('kode_hubungan', $keluarga->kode_hubungan) == 'A' ? 'selected' : '' }}>Diri Sendiri</option>
                            <option value="B" {{ old('kode_hubungan', $keluarga->kode_hubungan) == 'B' ? 'selected' : '' }}>Suami/Istri</option>
                            <option value="C" {{ old('kode_hubungan', $keluarga->kode_hubungan) == 'C' ? 'selected' : '' }}>Anak Ke-1</option>
                            <option value="D" {{ old('kode_hubungan', $keluarga->kode_hubungan) == 'D' ? 'selected' : '' }}>Anak Ke-2</option>
                            <option value="E" {{ old('kode_hubungan', $keluarga->kode_hubungan) == 'E' ? 'selected' : '' }}>Anak Ke-3</option>
                        </select>
                        @error('kode_hubungan')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Keluarga -->
                    <div>
                        <label for="nama_keluarga" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Keluarga <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="nama_keluarga" id="nama_keluarga" value="{{ old('nama_keluarga', $keluarga->nama_keluarga) }}" required
                               class="w-full px-4 py-2.5 border @error('nama_keluarga') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                               placeholder="Masukkan nama lengkap keluarga">
                        @error('nama_keluarga')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- BPJS ID -->
                    <div>
                        <label for="bpjs_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            BPJS ID
                        </label>
                        <input type="text" name="bpjs_id" id="bpjs_id" value="{{ old('bpjs_id', $keluarga->bpjs_id) }}"
                               class="w-full px-4 py-2.5 border @error('bpjs_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                               placeholder="Masukkan BPJS ID (hanya angka)" maxlength="50">
                        <p class="mt-1 text-xs text-gray-500">Hanya angka, maksimal 50 karakter</p>
                        @error('bpjs_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-semibold text-gray-700 mb-2">
                            Jenis Kelamin <span class="text-red-600">*</span>
                        </label>
                        <select name="jenis_kelamin" id="jenis_kelamin" required
                                class="w-full px-4 py-2.5 border @error('jenis_kelamin') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L" {{ old('jenis_kelamin', $keluarga->jenis_kelamin) == 'L' || old('jenis_kelamin', $keluarga->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $keluarga->jenis_kelamin) == 'P' || old('jenis_kelamin', $keluarga->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        <input type="hidden" id="jenis_kelamin_hidden" name="jenis_kelamin_backup" value="">
                        @error('jenis_kelamin')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-semibold text-gray-700 mb-2">
                            Tanggal Lahir <span class="text-red-600">*</span>
                        </label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', optional($keluarga->tanggal_lahir)->format('Y-m-d')) }}" required
                               class="w-full px-4 py-2.5 border @error('tanggal_lahir') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm">
                        @error('tanggal_lahir')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Alamat (Full Width) -->
                <div class="mt-6">
                    <label for="alamat" class="block text-sm font-semibold text-gray-700 mb-2">
                        Alamat <span class="text-red-600">*</span>
                    </label>
                    <textarea name="alamat" id="alamat" rows="3" required
                              class="w-full px-4 py-2.5 border @error('alamat') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm resize-none"
                              placeholder="Masukkan alamat lengkap">{{ old('alamat', $keluarga->alamat) }}</textarea>
                    @error('alamat')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="window.location.href='{{ route('keluarga.index') }}'" class="px-6 py-2.5 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-medium rounded-lg transition-all hover:shadow-md">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Perbarui Data Keluarga
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let debounceTimer;
let selectedKaryawanData = null; // Store selected karyawan data

// NIK Search functionality
document.getElementById('nik_search').addEventListener('input', function() {
    const query = this.value;
    clearTimeout(debounceTimer);

    if (query.length < 2) {
        document.getElementById('search_results').classList.add('hidden');
        return;
    }

    debounceTimer = setTimeout(() => {
        fetch(`/keluarga/search-karyawan?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                const resultsDiv = document.getElementById('search_results');

                if (data.length === 0) {
                    resultsDiv.innerHTML = '<div class="p-3 text-sm text-gray-500 text-center">Tidak ada hasil</div>';
                    resultsDiv.classList.remove('hidden');
                    return;
                }

                resultsDiv.innerHTML = data.map(karyawan => `
                    <div class="p-3 hover:bg-purple-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                         onclick='selectKaryawan(${JSON.stringify(karyawan)})'>
                        <p class="text-sm font-semibold text-gray-900">${karyawan.nama_karyawan}</p>
                        <p class="text-xs text-gray-600">NIK: ${karyawan.nik_karyawan}</p>
                    </div>
                `).join('');

                resultsDiv.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }, 300);
});

function selectKaryawan(karyawan) {
    selectedKaryawanData = karyawan; // Store the complete data

    document.getElementById('id_karyawan').value = karyawan.id_karyawan;
    document.getElementById('selected_nik').textContent = 'NIK: ' + karyawan.nik_karyawan;
    document.getElementById('selected_nama').textContent = karyawan.nama_karyawan;
    document.getElementById('selected_karyawan').classList.remove('hidden');
    document.getElementById('nik_search').value = '';
    document.getElementById('search_results').classList.add('hidden');

    // Check if "Diri Sendiri" is selected and auto-fill
    handleHubunganChange();
}

function enableSearch() {
    document.getElementById('selected_karyawan').classList.add('hidden');
    document.getElementById('nik_search').focus();
}

function clearSelection() {
    selectedKaryawanData = null;
    document.getElementById('id_karyawan').value = '';
    document.getElementById('selected_karyawan').classList.add('hidden');
    document.getElementById('nik_search').value = '';

    // Clear all fields when clearing selection
    document.getElementById('nama_keluarga').value = '';
    document.getElementById('jenis_kelamin').value = '';
    document.getElementById('tanggal_lahir').value = '';
    document.getElementById('alamat').value = '';
}

// Hide search results when clicking outside
document.addEventListener('click', function(event) {
    const searchBox = document.getElementById('nik_search');
    const resultsDiv = document.getElementById('search_results');

    if (!searchBox.contains(event.target) && !resultsDiv.contains(event.target)) {
        resultsDiv.classList.add('hidden');
    }
});

// Handle hubungan change - Auto-fill if "Diri Sendiri"
function handleHubunganChange() {
    const hubungan = document.getElementById('kode_hubungan').value;

    // Get form fields
    const namaField = document.getElementById('nama_keluarga');
    const jenisKelaminField = document.getElementById('jenis_kelamin');
    const tanggalLahirField = document.getElementById('tanggal_lahir');
    const alamatField = document.getElementById('alamat');

    // Auto-fill and disable fields if "Diri Sendiri"
    if (hubungan === 'A') {
        // Auto-fill from selected karyawan if "Diri Sendiri"
        if (selectedKaryawanData) {
            // Fill nama keluarga
            namaField.value = selectedKaryawanData.nama_karyawan || '';

            // Set jenis kelamin
            const jenisKelamin = selectedKaryawanData.jenis_kelamin;
            if (jenisKelamin === 'L' || jenisKelamin === 'Laki - Laki' || jenisKelamin === 'Laki-laki') {
                jenisKelaminField.value = 'L';
            } else if (jenisKelamin === 'P' || jenisKelamin === 'Perempuan' || jenisKelamin === 'J') {
                jenisKelaminField.value = 'P';
            }

            // Set tanggal lahir - handle various date formats
            if (selectedKaryawanData.tanggal_lahir) {
                let tglLahir = selectedKaryawanData.tanggal_lahir;

                console.log('Original tanggal_lahir:', tglLahir); // Debug

                // Handle ISO 8601 format (2024-01-15T10:30:00.000000Z)
                if (tglLahir.includes('T')) {
                    tglLahir = tglLahir.split('T')[0];
                }
                // If date contains time with space, extract date part only
                else if (tglLahir.includes(' ')) {
                    tglLahir = tglLahir.split(' ')[0];
                }

                // Convert DD-MM-YYYY to YYYY-MM-DD
                if (tglLahir.includes('-') && tglLahir.indexOf('-') < 4) {
                    const parts = tglLahir.split('-');
                    tglLahir = `${parts[2]}-${parts[1]}-${parts[0]}`;
                }
                // Convert DD/MM/YYYY to YYYY-MM-DD
                else if (tglLahir.includes('/')) {
                    const parts = tglLahir.split('/');
                    tglLahir = `${parts[2]}-${parts[1]}-${parts[0]}`;
                }

                console.log('Processed tanggal_lahir:', tglLahir); // Debug
                tanggalLahirField.value = tglLahir;
            }

            // Set alamat
            alamatField.value = selectedKaryawanData.alamat || '';

            // Disable fields for "Diri Sendiri"
            namaField.setAttribute('readonly', 'readonly');
            namaField.classList.add('bg-gray-100', 'cursor-not-allowed');

            // For select, use pointer-events instead of disabled to preserve value submission
            jenisKelaminField.classList.add('bg-gray-100', 'cursor-not-allowed', 'pointer-events-none');

            tanggalLahirField.setAttribute('readonly', 'readonly');
            tanggalLahirField.classList.add('bg-gray-100', 'cursor-not-allowed');
            alamatField.setAttribute('readonly', 'readonly');
            alamatField.classList.add('bg-gray-100', 'cursor-not-allowed');

            // Show notification
            Swal.fire({
                icon: 'success',
                title: 'Data Terisi Otomatis',
                text: 'Data keluarga telah diisi dengan data karyawan',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        }
    } else {
        // Enable all fields for other relationships
        namaField.removeAttribute('readonly');
        namaField.classList.remove('bg-gray-100', 'cursor-not-allowed');
        jenisKelaminField.classList.remove('bg-gray-100', 'cursor-not-allowed', 'pointer-events-none');
        tanggalLahirField.removeAttribute('readonly');
        tanggalLahirField.classList.remove('bg-gray-100', 'cursor-not-allowed');
        alamatField.removeAttribute('readonly');
        alamatField.classList.remove('bg-gray-100', 'cursor-not-allowed');
    }
}


// Initialize KTP field visibility on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize selectedKaryawanData with existing karyawan data
    @if($keluarga->karyawan)
        selectedKaryawanData = {
            id_karyawan: {{ $keluarga->karyawan->id_karyawan }},
            nama_karyawan: "{{ $keluarga->karyawan->nama_karyawan }}",
            nik_karyawan: "{{ $keluarga->karyawan->nik_karyawan }}",
            jenis_kelamin: "{{ $keluarga->karyawan->jenis_kelamin }}",
            tanggal_lahir: "{{ optional($keluarga->karyawan->tanggal_lahir)->format('Y-m-d') }}",
            alamat: "{{ $keluarga->karyawan->alamat }}"
        };
    @endif

    handleHubunganChange();

    // BPJS ID - Only allow numbers
    const bpjsIdInput = document.getElementById('bpjs_id');
    if (bpjsIdInput) {
        bpjsIdInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    // NIK Search - Allow numbers and letters for search, but show validation message
    const nikSearchInput = document.getElementById('nik_search');
    if (nikSearchInput) {
        nikSearchInput.addEventListener('input', function(e) {
            // Tidak membatasi input karena user bisa mencari dengan nama juga
            // Tapi menambahkan pesan informatif
            const searchValue = this.value.trim();
            if (searchValue.length > 0 && /^\d+$/.test(searchValue)) {
                // Jika input hanya angka, pastikan tidak ada karakter non-angka
                this.value = this.value.replace(/[^0-9]/g, '');
            }
        });
    }
});
</script>
@endpush
@endsection
