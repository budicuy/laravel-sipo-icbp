@extends('layouts.app')

@section('page-title', 'Edit Data Keluarga')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-3">
            <a href="{{ route('keluarga.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            Edit Data Keluarga Karyawan
        </h1>
        <p class="text-gray-600 mt-2 ml-1">Perbarui informasi data keluarga</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden max-w-3xl">
        <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-purple-600 to-pink-600">
            <h2 class="text-xl font-semibold text-white">Form Edit Data Keluarga</h2>
        </div>

        <form method="POST" action="{{ route('keluarga.update', $keluarga->id_keluarga) }}" class="p-6 space-y-6" id="keluargaForm">
            @csrf
            @method('PUT')

            <!-- NIK Karyawan dengan Search -->
            <div>
                <label for="nik_search" class="block text-sm font-semibold text-gray-700 mb-2">
                    NIK Karyawan <span class="text-red-600">*</span>
                </label>
                <div class="relative">
                    <input type="text" id="nik_search" autocomplete="off"
                           class="w-full px-4 py-2.5 border @error('id_karyawan') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                           placeholder="Ketik NIK atau nama karyawan...">
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

            <!-- No KTP (Conditional) -->
            <div id="ktp_field" class="{{ old('kode_hubungan', $keluarga->kode_hubungan) == 'A' ? 'hidden' : '' }}">
                <label for="no_ktp" class="block text-sm font-semibold text-gray-700 mb-2">
                    No KTP <span class="text-red-600" id="ktp_required">*</span>
                </label>
                <input type="text" name="no_ktp" id="no_ktp" value="{{ old('no_ktp', $keluarga->no_ktp) }}"
                       class="w-full px-4 py-2.5 border @error('no_ktp') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                       placeholder="Masukkan nomor KTP (16 digit)" maxlength="16">
                @error('no_ktp')
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

            <!-- Alamat -->
            <div>
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

            <!-- Action Buttons -->
            <div class="flex items-center gap-3 pt-6 border-t border-gray-200">
                <button type="button" onclick="confirmUpdate()"
                        class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Perbarui Data
                </button>
                <a href="{{ route('keluarga.index') }}"
                   class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </a>
            </div>
        </form>
    </div>
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

// Hide search results when clicking outside
document.addEventListener('click', function(event) {
    const searchBox = document.getElementById('nik_search');
    const resultsDiv = document.getElementById('search_results');

    if (!searchBox.contains(event.target) && !resultsDiv.contains(event.target)) {
        resultsDiv.classList.add('hidden');
    }
});

// Handle hubungan change - Toggle KTP field and auto-fill if "Diri Sendiri"
function handleHubunganChange() {
    const hubungan = document.getElementById('kode_hubungan').value;
    const ktpField = document.getElementById('ktp_field');
    const ktpInput = document.getElementById('no_ktp');
    const ktpRequired = document.getElementById('ktp_required');

    // Get form fields
    const namaField = document.getElementById('nama_keluarga');
    const jenisKelaminField = document.getElementById('jenis_kelamin');
    const tanggalLahirField = document.getElementById('tanggal_lahir');
    const alamatField = document.getElementById('alamat');

    // Toggle KTP field
    if (hubungan === 'A') {
        ktpField.classList.add('hidden');
        ktpInput.value = '';
        ktpInput.removeAttribute('required');
        ktpRequired.classList.add('hidden');

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
        // Show KTP field for other relationships
        ktpField.classList.remove('hidden');
        ktpInput.setAttribute('required', 'required');
        ktpRequired.classList.remove('hidden');

        // Enable all fields
        namaField.removeAttribute('readonly');
        namaField.classList.remove('bg-gray-100', 'cursor-not-allowed');
        jenisKelaminField.classList.remove('bg-gray-100', 'cursor-not-allowed', 'pointer-events-none');
        tanggalLahirField.removeAttribute('readonly');
        tanggalLahirField.classList.remove('bg-gray-100', 'cursor-not-allowed');
        alamatField.removeAttribute('readonly');
        alamatField.classList.remove('bg-gray-100', 'cursor-not-allowed');
    }
}

// Confirm update with SweetAlert
function confirmUpdate() {
    const form = document.getElementById('keluargaForm');

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    Swal.fire({
        title: 'Perbarui Data Keluarga?',
        text: "Pastikan semua perubahan sudah benar!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#9333ea',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Perbarui!',
        cancelButtonText: 'Cek Lagi',
        reverseButtons: true,
        customClass: {
            confirmButton: 'px-5 py-2.5 rounded-lg font-medium',
            cancelButton: 'px-5 py-2.5 rounded-lg font-medium'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}

// Initialize KTP field visibility on page load
document.addEventListener('DOMContentLoaded', function() {
    handleHubunganChange();
});
</script>
@endpush
@endsection
