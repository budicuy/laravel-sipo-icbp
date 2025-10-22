@extends('layouts.app')

@section('page-title', 'Tambah Stok Obat')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <div class="bg-gradient-to-r from-green-600 to-green-700 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </div>
            Tambah Stok Obat
        </h1>
        <p class="text-gray-600 mt-2 ml-1">Tambah stok masuk untuk obat pada periode tertentu</p>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <form id="stokForm" action="{{ route('stok-obat.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Form Content -->
            <div class="p-6">
                <!-- Obat Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="id_obat" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Obat <span class="text-red-500">*</span>
                        </label>
                        <select name="id_obat" id="id_obat" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                            <option value="">-- Pilih Obat --</option>
                            @foreach($obats as $obat)
                                <option value="{{ $obat->id_obat }}" data-satuan="{{ $obat->satuanObat->nama_satuan ?? '' }}">
                                    {{ $obat->nama_obat }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_obat')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="periode" class="block text-sm font-medium text-gray-700 mb-2">
                            Periode <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="periode" 
                               id="periode" 
                               required 
                               maxlength="5"
                               placeholder="Contoh: 10-25" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                        <p class="mt-1 text-xs text-gray-500">Format: MM-YY (contoh: 10-25 untuk Oktober 2025)</p>
                        @error('periode')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Stok Information -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                       placeholder="0" 
                                       class="w-full px-4 py-2 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                                <span id="satuanLabel" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-sm text-gray-500">-</span>
                            </div>
                            @error('stok_masuk')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Stok Awal (Otomatis)
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       id="stok_awal_display" 
                                       readonly 
                                       placeholder="0" 
                                       class="w-full px-4 py-2 pr-16 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
                                <span id="satuanLabelAwal" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-sm text-gray-500">-</span>
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
                                       readonly 
                                       placeholder="0" 
                                       class="w-full px-4 py-2 pr-16 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
                                <span id="satuanLabelPakai" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-sm text-gray-500">-</span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Diambil dari data keluhan periode ini</p>
                        </div>
                    </div>

                    <!-- Stok Akhir Preview -->
                    <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Perkiraan Stok Akhir
                                </label>
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl font-bold text-green-600" id="stok_akhir_preview">0</span>
                                    <span id="satuanLabelAkhir" class="text-sm text-gray-500">-</span>
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
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all resize-none"></textarea>
                    @error('keterangan')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Informasi Penting:</p>
                            <ul class="list-disc list-inside space-y-1 text-blue-700">
                                <li>Jika ini adalah stok pertama kali untuk obat tersebut, sistem akan menandainya sebagai "Stok Awal"</li>
                                <li>Stok awal akan diambil otomatis dari stok akhir bulan sebelumnya</li>
                                <li>Stok pakai akan dihitung otomatis dari data keluhan pada periode yang sama</li>
                                <li>Periode format: MM-YY (contoh: 10-25 untuk Oktober 2025)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('stok-obat.index') }}" class="px-6 py-2.5 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    Batal
                </a>
                <button type="submit" id="submitBtn" class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Stok
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const obatSelect = document.getElementById('id_obat');
    const periodeInput = document.getElementById('periode');
    const stokMasukInput = document.getElementById('stok_masuk');
    const stokAwalDisplay = document.getElementById('stok_awal_display');
    const stokPakaiDisplay = document.getElementById('stok_pakai_display');
    const stokAkhirPreview = document.getElementById('stok_akhir_preview');
    const satuanLabel = document.getElementById('satuanLabel');
    const satuanLabelAwal = document.getElementById('satuanLabelAwal');
    const satuanLabelPakai = document.getElementById('satuanLabelPakai');
    const satuanLabelAkhir = document.getElementById('satuanLabelAkhir');
    const submitBtn = document.getElementById('submitBtn');
    const stokForm = document.getElementById('stokForm');

    // Update satuan label when obat is selected
    obatSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const satuan = selectedOption.getAttribute('data-satuan') || '-';
        
        satuanLabel.textContent = satuan;
        satuanLabelAwal.textContent = satuan;
        satuanLabelPakai.textContent = satuan;
        satuanLabelAkhir.textContent = satuan;
        
        updateStokPreview();
    });

    // Update stok preview when any input changes
    periodeInput.addEventListener('input', updateStokPreview);
    stokMasukInput.addEventListener('input', updateStokPreview);

    function updateStokPreview() {
        const idObat = obatSelect.value;
        const periode = periodeInput.value;
        const stokMasuk = parseInt(stokMasukInput.value) || 0;

        if (!idObat || !periode) {
            stokAwalDisplay.value = '0';
            stokPakaiDisplay.value = '0';
            stokAkhirPreview.textContent = '0';
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memuat...
        `;

        // Fetch stok data via AJAX
        fetch(`/api/stok-obat/preview?id_obat=${idObat}&periode=${periode}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    stokAwalDisplay.value = data.stok_awal || 0;
                    stokPakaiDisplay.value = data.stok_pakai || 0;
                    
                    const stokAwal = parseInt(data.stok_awal) || 0;
                    const stokPakai = parseInt(data.stok_pakai) || 0;
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
            })
            .catch(error => {
                console.error('Error fetching stok data:', error);
                stokAwalDisplay.value = '0';
                stokPakaiDisplay.value = '0';
                stokAkhirPreview.textContent = stokMasuk;
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Stok
                `;
            });
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
            Menyimpan...
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
                    confirmButtonColor: '#059669',
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
                text: 'Gagal menyimpan data stok obat',
                confirmButtonColor: '#dc2626'
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Simpan Stok
            `;
        });
    });

    // Format periode input
    periodeInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^\d-]/g, '');
        
        // Auto-format to MM-YY
        if (value.length >= 2 && !value.includes('-')) {
            value = value.slice(0, 2) + '-' + value.slice(2, 4);
        }
        
        e.target.value = value;
    });
});
</script>
@endsection