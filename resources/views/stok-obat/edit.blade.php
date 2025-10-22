@extends('layouts.app')

@section('page-title', 'Edit Stok Obat')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            Edit Stok Obat
        </h1>
        <p class="text-gray-600 mt-2 ml-1">Edit stok masuk untuk {{ $stokObat->obat->nama_obat }} periode {{ $stokObat->periode }}</p>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <form id="stokForm" action="{{ route('stok-obat.update', $stokObat->id_stok_obat) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Form Content -->
            <div class="p-6">
                <!-- Obat Information (Readonly) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Obat
                        </label>
                        <input type="text" 
                               value="{{ $stokObat->obat->nama_obat }}" 
                               readonly 
                               class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Periode
                        </label>
                        <input type="text" 
                               value="{{ $stokObat->periode }}" 
                               readonly 
                               class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
                    </div>
                </div>

                <!-- Stok Information -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Informasi Stok
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="stok_masuk" class="block text-sm font-medium text-gray-700 mb-2">
                                Jumlah Stok Masuk <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       name="stok_masuk" 
                                       id="stok_masuk" 
                                       required 
                                       min="0"
                                       value="{{ $stokObat->stok_masuk }}"
                                       class="w-full px-4 py-2 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-sm text-gray-500">{{ $stokObat->obat->satuanObat->nama_satuan ?? '' }}</span>
                            </div>
                            @error('stok_masuk')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Stok Awal
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       value="{{ $stokObat->stok_awal }}" 
                                       readonly 
                                       class="w-full px-4 py-2 pr-16 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
                                <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-sm text-gray-500">{{ $stokObat->obat->satuanObat->nama_satuan ?? '' }}</span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Diambil dari stok akhir bulan sebelumnya</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Stok Pakai (Otomatis)
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       id="stok_pakai_display" 
                                       value="{{ $stokObat->stok_pakai }}" 
                                       readonly 
                                       class="w-full px-4 py-2 pr-16 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
                                <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-sm text-gray-500">{{ $stokObat->obat->satuanObat->nama_satuan ?? '' }}</span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Diambil dari data keluhan periode ini</p>
                        </div>
                    </div>

                    <!-- Stok Akhir Preview -->
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Stok Akhir (Akan Diperbarui)
                                </label>
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl font-bold text-blue-600" id="stok_akhir_preview">{{ $stokObat->stok_akhir }}</span>
                                    <span class="text-sm text-gray-500">{{ $stokObat->obat->satuanObat->nama_satuan ?? '' }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-600 mb-1">Rumus:</p>
                                <p class="text-xs font-mono text-gray-700">Stok Awal + Stok Masuk - Stok Pakai</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="mb-6">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                        Keterangan
                    </label>
                    <textarea name="keterangan" 
                              id="keterangan" 
                              rows="3" 
                              placeholder="Masukkan keterangan untuk stok masuk (opsional)"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none">{{ old('keterangan', $stokObat->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Box -->
                @if($stokObat->is_initial_stok)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-sm text-green-800">
                                <p class="font-semibold mb-1">Stok Awal Pertama:</p>
                                <p class="text-green-700">Ini adalah stok awal pertama kali untuk obat {{ $stokObat->obat->nama_obat }}. Anda dapat mengubah jumlah stok masuk yang akan menjadi stok awal.</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-semibold mb-1">Informasi:</p>
                                <ul class="list-disc list-inside space-y-1 text-blue-700">
                                    <li>Stok awal tidak dapat diubah karena diambil dari stok akhir bulan sebelumnya</li>
                                    <li>Stok pakai akan dihitung otomatis dari data keluhan pada periode yang sama</li>
                                    <li>Stok akhir akan diperbarui otomatis sesuai rumus perhitungan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('stok-obat.index') }}" class="px-6 py-2.5 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    Batal
                </a>
                <button type="submit" id="submitBtn" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Stok
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stokMasukInput = document.getElementById('stok_masuk');
    const stokPakaiDisplay = document.getElementById('stok_pakai_display');
    const stokAkhirPreview = document.getElementById('stok_akhir_preview');
    const submitBtn = document.getElementById('submitBtn');
    const stokForm = document.getElementById('stokForm');
    
    // Values from server
    const stokAwal = {{ $stokObat->stok_awal }};
    const stokPakai = {{ $stokObat->stok_pakai }};

    // Update stok akhir preview when stok masuk changes
    stokMasukInput.addEventListener('input', updateStokAkhirPreview);

    function updateStokAkhirPreview() {
        const stokMasuk = parseInt(stokMasukInput.value) || 0;
        const stokAkhir = stokAwal + stokMasuk - stokPakai;
        
        stokAkhirPreview.textContent = stokAkhir;
        
        // Update color based on stok akhir
        if (stokAkhir <= 0) {
            stokAkhirPreview.className = 'text-2xl font-bold text-red-600';
        } else if (stokAkhir <= 10) {
            stokAkhirPreview.className = 'text-2xl font-bold text-yellow-600';
        } else {
            stokAkhirPreview.className = 'text-2xl font-bold text-green-600';
        }
    }

    // Form submission with AJAX
    stokForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memperbarui...
        `;

        fetch(this.action, {
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
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    confirmButtonColor: '#2563eb',
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href = '{{ route("stok-obat.index") }}';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message,
                    confirmButtonColor: '#dc2626'
                });
                
                if (data.errors) {
                    // Handle validation errors
                    let errorMessages = '';
                    for (const [field, messages] of Object.entries(data.errors)) {
                        errorMessages += `${messages.join(', ')}\n`;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: errorMessages,
                        confirmButtonColor: '#dc2626'
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: 'Gagal memperbarui data stok obat',
                confirmButtonColor: '#dc2626'
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Update Stok
            `;
        });
    });

    // Initialize stok akhir preview
    updateStokAkhirPreview();
});
</script>
@endsection