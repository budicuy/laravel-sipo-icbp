@extends('layouts.app')

@section('page-title', 'Import Harga Obat')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('harga-obat.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <div class="bg-linear-to-r from-indigo-600 to-purple-600 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                </div>
                Import Harga Obat
            </h1>
        </div>
        <p class="text-gray-600 mt-2 ml-1">Import data harga obat dari file Excel</p>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="p-6 bg-linear-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Import Data Harga Obat
            </h2>
            <p class="text-sm text-gray-600 mt-1">Upload file Excel yang berisi data harga obat</p>
        </div>

        <div class="p-6">
            <!-- Instructions -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h3 class="text-sm font-semibold text-blue-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Petunjuk Import
                </h3>
                <div class="text-sm text-blue-800 space-y-2">
                    <p>1. Download template Excel terlebih dahulu untuk format yang benar</p>
                    <p>2. Isi data harga obat sesuai format yang telah disediakan</p>
                    <p>3. Pastikan nama obat sudah terdaftar di sistem</p>
                    <p>4. Format periode harus MM-YY (contoh: 08-25 untuk Agustus 2025)</p>
                    <p>5. Harga obat diisi dengan angka tanpa format (contoh: 410000)</p>
                    <p>6. Upload file Excel yang sudah diisi</p>
                </div>
            </div>

            <!-- Download Template Section -->
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <h3 class="text-sm font-semibold text-green-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download Template
                </h3>
                <p class="text-sm text-green-800 mb-3">Download template Excel untuk format import yang benar:</p>
                <a href="{{ route('harga-obat.template') }}"
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download Template Excel
                </a>
            </div>

            <!-- Upload Form -->
            <form id="importForm" action="{{ route('harga-obat.process-import') }}" method="POST"
                enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div>
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih File Excel <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-1 text-xs text-gray-500">Format file: .xlsx, .xls, .csv (Maksimal 10MB)</p>
                    </div>
                    @error('file')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- File Preview -->
                <div id="filePreview" class="hidden p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">File yang dipilih:</h4>
                    <div class="flex items-center gap-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900" id="fileName">-</p>
                            <p class="text-xs text-gray-500" id="fileSize">-</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('harga-obat.index') }}"
                        class="px-5 py-2.5 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        Batal
                    </a>
                    <button type="submit" id="submitBtn"
                        class="px-5 py-2.5 bg-linear-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const importForm = document.getElementById('importForm');
    const submitBtn = document.getElementById('submitBtn');

    // File input change handler
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];

        if (file) {
            // Show file preview
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            filePreview.classList.remove('hidden');

            // Validate file
            validateFile(file);
        } else {
            filePreview.classList.add('hidden');
        }
    });

    // Form submit handler
    importForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const file = fileInput.files[0];
        if (!file) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Pilih file terlebih dahulu',
                confirmButtonColor: '#dc2626'
            });
            return;
        }

        // Validate file before submit
        if (!validateFile(file)) {
            return;
        }

        // Show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Mengimport...
        `;

        // Create FormData
        const formData = new FormData(importForm);

        // Submit via AJAX
        fetch('{{ route("harga-obat.process-import") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message with details
                let message = data.message;
                if (data.errors && data.errors.length > 0) {
                    message += '<br><br><strong>Error Details:</strong><br>' +
                              data.errors.slice(0, 5).join('<br>');
                    if (data.errors.length > 5) {
                        message += '<br>... dan ' + (data.errors.length - 5) + ' error lainnya';
                    }
                }

                Swal.fire({
                    icon: data.errors && data.errors.length > 0 ? 'warning' : 'success',
                    title: data.errors && data.errors.length > 0 ? 'Import Selesai dengan Warning' : 'Import Berhasil',
                    html: message,
                    confirmButtonColor: '#16a34a',
                    width: '600px'
                }).then(() => {
                    // Redirect to index page
                    window.location.href = '{{ route("harga-obat.index") }}';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Import Gagal',
                    text: data.message,
                    confirmButtonColor: '#dc2626'
                });
            }
        })
        .catch(error => {
            console.error('Import error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Import Gagal',
                text: 'Terjadi kesalahan saat import data',
                confirmButtonColor: '#dc2626'
            });
        })
        .finally(() => {
            // Reset button
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Import Data
            `;
        });
    });

    function validateFile(file) {
        const allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                             'application/vnd.ms-excel',
                             'text/csv'];
        const maxSize = 10 * 1024 * 1024; // 10MB

        // Check file type
        if (!allowedTypes.includes(file.type) && !file.name.match(/\.(xlsx|xls|csv)$/i)) {
            Swal.fire({
                icon: 'error',
                title: 'Format File Tidak Valid',
                text: 'Format file harus Excel (.xlsx, .xls) atau CSV',
                confirmButtonColor: '#dc2626'
            });
            return false;
        }

        // Check file size
        if (file.size > maxSize) {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran File Terlalu Besar',
                text: 'Ukuran file maksimal 10MB',
                confirmButtonColor: '#dc2626'
            });
            return false;
        }

        return true;
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>
@endsection