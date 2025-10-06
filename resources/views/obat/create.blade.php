@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Tambah Obat</h2>

        <!-- Import Section -->
        <div class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <h3 class="text-lg font-semibold mb-4 text-gray-700">Import Data Obat</h3>
            <div class="flex items-center gap-4">
                <input
                    type="file"
                    id="file-import"
                    accept=".xlsx,.xls,.csv"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-100 hover:file:bg-gray-200"
                />
                <button
                    type="button"
                    class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors whitespace-nowrap"
                >
                    Import
                </button>
                <button
                    type="button"
                    class="px-6 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition-colors whitespace-nowrap"
                >
                    Download Template
                </button>
            </div>
        </div>

        <!-- Manual Form Section -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-700">Tambah Manual</h3>
        </div>

        <form action="{{ route('obat.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Nama Obat -->
            <div>
                <label for="nama_obat" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Obat
                </label>
                <input
                    type="text"
                    id="nama_obat"
                    name="nama_obat"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Masukkan nama obat"
                    required
                />
            </div>

            <!-- Keterangan -->
            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan
                </label>
                <textarea
                    id="keterangan"
                    name="keterangan"
                    rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Masukkan keterangan obat"
                ></textarea>
            </div>

            <!-- Jenis Obat -->
            <div>
                <label for="jenis_obat" class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis Obat
                </label>
                <select
                    id="jenis_obat"
                    name="jenis_obat"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                    <option value="">-- Pilih Jenis Obat --</option>
                    <option value="Tablet">Tablet</option>
                    <option value="Kapsul">Kapsul</option>
                    <option value="Sirup">Sirup</option>
                    <option value="Salep">Salep</option>
                    <option value="Injeksi">Injeksi</option>
                </select>
            </div>

            <!-- Satuan -->
            <div>
                <label for="satuan" class="block text-sm font-medium text-gray-700 mb-2">
                    Satuan
                </label>
                <select
                    id="satuan"
                    name="satuan"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                    <option value="">-- Pilih Satuan --</option>
                    <option value="Strip">Strip</option>
                    <option value="Box">Box</option>
                    <option value="Botol">Botol</option>
                    <option value="Tube">Tube</option>
                    <option value="Ampul">Ampul</option>
                </select>
            </div>

            <!-- Stok Awal -->
            <div>
                <label for="stok_awal" class="block text-sm font-medium text-gray-700 mb-2">
                    Stok Awal
                </label>
                <input
                    type="number"
                    id="stok_awal"
                    name="stok_awal"
                    value="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-100"
                    readonly
                />
                <p class="mt-1 text-sm text-gray-500">Otomatis dihitung</p>
            </div>

            <!-- Stok Masuk & Stok Keluar in Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Stok Masuk -->
                <div>
                    <label for="stok_masuk" class="block text-sm font-medium text-gray-700 mb-2">
                        Stok Masuk
                    </label>
                    <input
                        type="number"
                        id="stok_masuk"
                        name="stok_masuk"
                        value="0"
                        min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        oninput="hitungStok()"
                    />
                </div>

                <!-- Stok Keluar -->
                <div>
                    <label for="stok_keluar" class="block text-sm font-medium text-gray-700 mb-2">
                        Stok Keluar
                    </label>
                    <input
                        type="number"
                        id="stok_keluar"
                        name="stok_keluar"
                        value="0"
                        min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        oninput="hitungStok()"
                    />
                </div>
            </div>

            <!-- Stok Akhir -->
            <div>
                <label for="stok_akhir" class="block text-sm font-medium text-gray-700 mb-2">
                    Stok Akhir
                </label>
                <input
                    type="number"
                    id="stok_akhir"
                    name="stok_akhir"
                    value="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-100"
                    readonly
                />
                <p class="mt-1 text-sm text-gray-500">Otomatis dihitung</p>
            </div>

            <!-- Jumlah per Kemasan -->
            <div>
                <label for="jumlah_per_kemasan" class="block text-sm font-medium text-gray-700 mb-2">
                    Jumlah per Kemasan
                </label>
                <input
                    type="number"
                    id="jumlah_per_kemasan"
                    name="jumlah_per_kemasan"
                    value="1"
                    min="1"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    oninput="hitungHargaSatuan()"
                    required
                />
            </div>

            <!-- Harga per Kemasan -->
            <div>
                <label for="harga_per_kemasan" class="block text-sm font-medium text-gray-700 mb-2">
                    Harga per Kemasan
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">Rp.</span>
                    <input
                        type="number"
                        id="harga_per_kemasan"
                        name="harga_per_kemasan"
                        value="0"
                        min="0"
                        class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                    <span class="absolute left-3 top-2 text-gray-500">Rp.</span>
                    <input
                        type="number"
                        id="harga_per_satuan"
                        name="harga_per_satuan"
                        value="0"
                        class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-100"
                        readonly
                    />
                </div>
                <p class="mt-1 text-sm text-gray-500">Dihitung otomatis</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 pt-4">
                <button
                    type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors"
                >
                    Simpan
                </button>
                <a
                    href="{{ route('obat.index') }}"
                    class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors"
                >
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
    }

    // Fungsi untuk menghitung harga per satuan
    function hitungHargaSatuan() {
        const hargaKemasan = parseInt(document.getElementById('harga_per_kemasan').value) || 0;
        const jumlahKemasan = parseInt(document.getElementById('jumlah_per_kemasan').value) || 1;

        const hargaSatuan = jumlahKemasan > 0 ? Math.round(hargaKemasan / jumlahKemasan) : 0;
        document.getElementById('harga_per_satuan').value = hargaSatuan;
    }

    // Inisialisasi perhitungan saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        hitungStok();
        hitungHargaSatuan();
    });
</script>
@endpush
@endsection
