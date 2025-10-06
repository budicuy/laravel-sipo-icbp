@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Header with Back Button -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('obat.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h2 class="text-2xl font-bold text-gray-800">Edit Obat</h2>
            </div>
            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                Kode: OBT001
            </span>
        </div>

        <!-- Import Section -->
        <div class="mb-8 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border-l-4 border-blue-500">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Import Data Obat
            </h3>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="flex-1 w-full">
                    <input
                        type="file"
                        id="file-import"
                        accept=".xlsx,.xls,.csv"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-500 file:text-white hover:file:bg-blue-600 file:cursor-pointer cursor-pointer"
                    />
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <button
                        type="button"
                        class="flex-1 sm:flex-none px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg whitespace-nowrap"
                    >
                        Import
                    </button>
                    <button
                        type="button"
                        class="flex-1 sm:flex-none px-6 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg whitespace-nowrap"
                    >
                        Download Template
                    </button>
                </div>
            </div>
        </div>

        <!-- Manual Form Section -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Manual
            </h3>
            <p class="text-sm text-gray-500 mt-1">Update informasi obat sesuai kebutuhan</p>
        </div>

        <form action="{{ route('obat.update', 1) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Informasi Dasar Section -->
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-4 flex items-center">
                    <span class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center mr-2 text-sm">1</span>
                    Informasi Dasar Obat
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Obat -->
                    <div class="md:col-span-2">
                        <label for="nama_obat" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Obat <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="nama_obat"
                            name="nama_obat"
                            value="Paracetamol 500mg"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            placeholder="Masukkan nama obat"
                            required
                        />
                    </div>

                    <!-- Keterangan -->
                    <div class="md:col-span-2">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                            Keterangan
                        </label>
                        <textarea
                            id="keterangan"
                            name="keterangan"
                            rows="3"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                            placeholder="Masukkan keterangan obat"
                        >Obat pereda nyeri dan penurun panas</textarea>
                    </div>

                    <!-- Jenis Obat -->
                    <div>
                        <label for="jenis_obat" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Obat <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="jenis_obat"
                            name="jenis_obat"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            required
                        >
                            <option value="">-- Pilih Jenis Obat --</option>
                            <option value="Tablet" selected>Tablet</option>
                            <option value="Kapsul">Kapsul</option>
                            <option value="Sirup">Sirup</option>
                            <option value="Salep">Salep</option>
                            <option value="Injeksi">Injeksi</option>
                        </select>
                    </div>

                    <!-- Satuan -->
                    <div>
                        <label for="satuan" class="block text-sm font-medium text-gray-700 mb-2">
                            Satuan <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="satuan"
                            name="satuan"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            required
                        >
                            <option value="">-- Pilih Satuan --</option>
                            <option value="Strip" selected>Strip</option>
                            <option value="Box">Box</option>
                            <option value="Botol">Botol</option>
                            <option value="Tube">Tube</option>
                            <option value="Ampul">Ampul</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Stok Management Section -->
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-4 flex items-center">
                    <span class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center mr-2 text-sm">2</span>
                    Manajemen Stok
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Stok Awal -->
                    <div>
                        <label for="stok_awal" class="block text-sm font-medium text-gray-700 mb-2">
                            Stok Awal
                        </label>
                        <div class="relative">
                            <input
                                type="number"
                                id="stok_awal"
                                name="stok_awal"
                                value="0"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed"
                                readonly
                            />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 italic">Otomatis dihitung</p>
                    </div>

                    <!-- Stok Masuk -->
                    <div>
                        <label for="stok_masuk" class="block text-sm font-medium text-gray-700 mb-2">
                            Stok Masuk
                        </label>
                        <div class="relative">
                            <input
                                type="number"
                                id="stok_masuk"
                                name="stok_masuk"
                                value="0"
                                min="0"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                                oninput="hitungStok()"
                            />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Stok Keluar -->
                    <div>
                        <label for="stok_keluar" class="block text-sm font-medium text-gray-700 mb-2">
                            Stok Keluar
                        </label>
                        <div class="relative">
                            <input
                                type="number"
                                id="stok_keluar"
                                name="stok_keluar"
                                value="0"
                                min="0"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all"
                                oninput="hitungStok()"
                            />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Stok Akhir -->
                    <div>
                        <label for="stok_akhir" class="block text-sm font-medium text-gray-700 mb-2">
                            Stok Akhir
                        </label>
                        <div class="relative">
                            <input
                                type="number"
                                id="stok_akhir"
                                name="stok_akhir"
                                value="0"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-blue-50 text-blue-700 font-semibold cursor-not-allowed"
                                readonly
                            />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 italic">Otomatis dihitung</p>
                    </div>
                </div>
            </div>

            <!-- Harga Section -->
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                <h4 class="font-semibold text-gray-700 mb-4 flex items-center">
                    <span class="w-8 h-8 bg-yellow-500 text-white rounded-full flex items-center justify-center mr-2 text-sm">3</span>
                    Informasi Harga
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Jumlah per Kemasan -->
                    <div>
                        <label for="jumlah_per_kemasan" class="block text-sm font-medium text-gray-700 mb-2">
                            Jumlah per Kemasan <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="jumlah_per_kemasan"
                            name="jumlah_per_kemasan"
                            value="1"
                            min="1"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            oninput="hitungHargaSatuan()"
                            required
                        />
                    </div>

                    <!-- Harga per Kemasan -->
                    <div>
                        <label for="harga_per_kemasan" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga per Kemasan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-500 font-medium">Rp.</span>
                            <input
                                type="number"
                                id="harga_per_kemasan"
                                name="harga_per_kemasan"
                                value="0"
                                min="0"
                                class="w-full pl-12 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                oninput="hitungHargaSatuan()"
                                required
                            />
                        </div>
                    </div>

                    <!-- Harga per Satuan -->
                    <div>
                        <label for="harga_per_satuan" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga per Satuan
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-500 font-medium">Rp.</span>
                            <input
                                type="number"
                                id="harga_per_satuan"
                                name="harga_per_satuan"
                                value="0"
                                class="w-full pl-12 pr-4 py-2.5 border border-gray-300 rounded-lg bg-yellow-50 text-yellow-700 font-semibold cursor-not-allowed"
                                readonly
                            />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 italic">Dihitung otomatis</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <button
                    type="submit"
                    class="flex-1 sm:flex-none px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Perubahan
                </button>
                <a
                    href="{{ route('obat.index') }}"
                    class="flex-1 sm:flex-none px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    // Fungsi untuk menghitung stok akhir
    function hitungStok() {
        const stokAwal = parseInt(document.getElementById('stok_awal').value) || 0;
        const stokMasuk = parseInt(document.getElementById('stok_masuk').value) || 0;
        const stokKeluar = parseInt(document.getElementById('stok_keluar').value) || 0;

        const stokAkhir = stokAwal + stokMasuk - stokKeluar;
        document.getElementById('stok_akhir').value = stokAkhir;

        // Tambahkan visual feedback untuk stok akhir
        const stokAkhirInput = document.getElementById('stok_akhir');
        if (stokAkhir < 0) {
            stokAkhirInput.classList.remove('bg-blue-50', 'text-blue-700', 'bg-green-50', 'text-green-700');
            stokAkhirInput.classList.add('bg-red-50', 'text-red-700');
        } else if (stokAkhir > 0) {
            stokAkhirInput.classList.remove('bg-blue-50', 'text-blue-700', 'bg-red-50', 'text-red-700');
            stokAkhirInput.classList.add('bg-green-50', 'text-green-700');
        } else {
            stokAkhirInput.classList.remove('bg-red-50', 'text-red-700', 'bg-green-50', 'text-green-700');
            stokAkhirInput.classList.add('bg-blue-50', 'text-blue-700');
        }
    }

    // Fungsi untuk menghitung harga per satuan
    function hitungHargaSatuan() {
        const hargaKemasan = parseInt(document.getElementById('harga_per_kemasan').value) || 0;
        const jumlahKemasan = parseInt(document.getElementById('jumlah_per_kemasan').value) || 1;

        const hargaSatuan = jumlahKemasan > 0 ? Math.round(hargaKemasan / jumlahKemasan) : 0;
        document.getElementById('harga_per_satuan').value = hargaSatuan;

        // Format dengan pemisah ribuan
        formatCurrency();
    }

    // Fungsi untuk format currency (opsional, bisa diaktifkan jika diinginkan)
    function formatCurrency() {
        // Bisa ditambahkan format ribuan jika diperlukan
    }

    // Inisialisasi perhitungan saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        hitungStok();
        hitungHargaSatuan();

        // Tambahkan animasi smooth scroll
        document.querySelectorAll('input, select, textarea').forEach(element => {
            element.addEventListener('focus', function() {
                this.classList.add('ring-2');
            });
            element.addEventListener('blur', function() {
                this.classList.remove('ring-2');
            });
        });
    });
</script>
@endpush
@endsection
