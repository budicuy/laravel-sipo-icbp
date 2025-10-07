@extends('layouts.app')

@section('page-title', 'Tambah Data Obat')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen"
     x-data="{
         id_satuan: '{{ old('id_satuan') }}',
         jumlah_per_kemasan: {{ old('jumlah_per_kemasan', 1) }},
         harga_per_kemasan: {{ old('harga_per_kemasan', 0) }},
         harga_per_satuan: {{ old('harga_per_satuan', 0) }},
         stok_awal: {{ old('stok_awal', 0) }},
         stok_masuk: {{ old('stok_masuk', 0) }},
         stok_keluar: {{ old('stok_keluar', 0) }},
         stok_akhir: {{ old('stok_akhir', 0) }},
         satuanPerUnit: ['Ampul', 'Botol', 'Injek'],

         init() {
             this.$watch('id_satuan', value => this.updateJumlahKemasan());
             this.$watch('jumlah_per_kemasan', () => this.calculateHargaPerSatuan());
             this.$watch('harga_per_kemasan', () => this.calculateHargaPerSatuan());
             this.$watch('stok_awal', () => this.calculateStokAkhir());
             this.$watch('stok_masuk', () => this.calculateStokAkhir());
             this.$watch('stok_keluar', () => this.calculateStokAkhir());
         },

         updateJumlahKemasan() {
             const satuanSelect = document.getElementById('id_satuan');
             if (!satuanSelect || !satuanSelect.selectedIndex) return;
             const selectedOption = satuanSelect.options[satuanSelect.selectedIndex];
             const namaSatuan = selectedOption.text;

             if (this.satuanPerUnit.includes(namaSatuan)) {
                 this.jumlah_per_kemasan = 1;
             }
         },

         calculateHargaPerSatuan() {
             if (this.jumlah_per_kemasan > 0) {
                 this.harga_per_satuan = (this.harga_per_kemasan / this.jumlah_per_kemasan).toFixed(2);
             } else {
                 this.harga_per_satuan = 0;
             }
         },

         calculateStokAkhir() {
             this.stok_akhir = (parseInt(this.stok_awal) || 0) + (parseInt(this.stok_masuk) || 0) - (parseInt(this.stok_keluar) || 0);
         },

         isSatuanPerUnit() {
             const satuanSelect = document.getElementById('id_satuan');
             if (!satuanSelect || !satuanSelect.selectedIndex) return false;
             const selectedOption = satuanSelect.options[satuanSelect.selectedIndex];
             const namaSatuan = selectedOption.text;
             return this.satuanPerUnit.includes(namaSatuan);
         }
     }">

    <div class="mb-6">
        <div class="flex items-center gap-3 mb-3">
            <a href="{{ route('obat.index') }}" class="p-2 hover:bg-white rounded-lg transition-colors">
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
                    Tambah Data Obat Baru
                </h1>
                <p class="text-gray-600 mt-1 ml-1">Tambahkan obat baru ke persediaan farmasi</p>
            </div>
        </div>
    </div>

    <form action="{{ route('obat.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    Informasi Data Obat
                </h2>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Obat -->
                    <div>
                        <label for="nama_obat" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Obat <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_obat" name="nama_obat" value="{{ old('nama_obat') }}" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('nama_obat') border-red-500 @enderror"
                               placeholder="Contoh: Paracetamol 500mg">
                        @error('nama_obat')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Obat -->
                    <div>
                        <label for="id_jenis_obat" class="block text-sm font-semibold text-gray-700 mb-2">
                            Jenis Obat <span class="text-red-500">*</span>
                        </label>
                        <select id="id_jenis_obat" name="id_jenis_obat" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 bg-white @error('id_jenis_obat') border-red-500 @enderror">
                            <option value="">Pilih Jenis Obat</option>
                            @foreach($jenisObats as $jenis)
                                <option value="{{ $jenis->id_jenis_obat }}" {{ old('id_jenis_obat') == $jenis->id_jenis_obat ? 'selected' : '' }}>
                                    {{ $jenis->nama_jenis }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_jenis_obat')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Satuan Obat -->
                    <div>
                        <label for="id_satuan" class="block text-sm font-semibold text-gray-700 mb-2">
                            Satuan Obat <span class="text-red-500">*</span>
                        </label>
                        <select id="id_satuan" name="id_satuan" required x-model="id_satuan"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 bg-white @error('id_satuan') border-red-500 @enderror">
                            <option value="">Pilih Satuan Obat</option>
                            @foreach($satuanObats as $satuan)
                                <option value="{{ $satuan->id_satuan }}" {{ old('id_satuan') == $satuan->id_satuan ? 'selected' : '' }}>
                                    {{ $satuan->nama_satuan }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_satuan')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jumlah Per Kemasan -->
                    <div>
                        <label for="jumlah_per_kemasan" class="block text-sm font-semibold text-gray-700 mb-2">
                            Jumlah Per Kemasan <span class="text-red-500">*</span>
                        </label>
                        <select id="jumlah_per_kemasan" name="jumlah_per_kemasan" x-model="jumlah_per_kemasan" required
                                :disabled="isSatuanPerUnit()"
                                :class="isSatuanPerUnit() ? 'bg-gray-100 cursor-not-allowed' : 'bg-white'"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('jumlah_per_kemasan') border-red-500 @enderror">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="4">4</option>
                            <option value="6">6</option>
                            <option value="10">10</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Untuk satuan per unit (Ampul, Botol, Injek) otomatis = 1</p>
                        @error('jumlah_per_kemasan')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stok Awal -->
                    <div>
                        <label for="stok_awal" class="block text-sm font-semibold text-gray-700 mb-2">
                            Stok Awal <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="stok_awal" name="stok_awal" value="{{ old('stok_awal', 0) }}" required min="0" x-model="stok_awal"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('stok_awal') border-red-500 @enderror"
                               placeholder="0">
                        @error('stok_awal')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stok Masuk -->
                    <div>
                        <label for="stok_masuk" class="block text-sm font-semibold text-gray-700 mb-2">
                            Stok Masuk <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="stok_masuk" name="stok_masuk" value="{{ old('stok_masuk', 0) }}" required min="0" x-model="stok_masuk"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('stok_masuk') border-red-500 @enderror"
                               placeholder="0">
                        @error('stok_masuk')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stok Keluar -->
                    <div>
                        <label for="stok_keluar" class="block text-sm font-semibold text-gray-700 mb-2">
                            Stok Keluar <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="stok_keluar" name="stok_keluar" value="{{ old('stok_keluar', 0) }}" required min="0" x-model="stok_keluar"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('stok_keluar') border-red-500 @enderror"
                               placeholder="0">
                        @error('stok_keluar')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stok Akhir (Read Only) -->
                    <div>
                        <label for="stok_akhir" class="block text-sm font-semibold text-gray-700 mb-2">
                            Stok Akhir <span class="text-blue-500">(Otomatis)</span>
                        </label>
                        <input type="text" id="stok_akhir" x-model="stok_akhir" readonly
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                               placeholder="0">
                        <p class="mt-1 text-xs text-gray-500">Formula: Stok Awal + Stok Masuk - Stok Keluar</p>
                    </div>

                    <!-- Harga Per Kemasan -->
                    <div>
                        <label for="harga_per_kemasan" class="block text-sm font-semibold text-gray-700 mb-2">
                            Harga Per Kemasan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                            <input type="number" id="harga_per_kemasan" name="harga_per_kemasan" value="{{ old('harga_per_kemasan', 0) }}" required min="0" step="0.01" x-model="harga_per_kemasan"
                                   class="w-full pl-12 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('harga_per_kemasan') border-red-500 @enderror"
                                   placeholder="0">
                        </div>
                        @error('harga_per_kemasan')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Harga Per Satuan (Read Only) -->
                    <div>
                        <label for="harga_per_satuan" class="block text-sm font-semibold text-gray-700 mb-2">
                            Harga Per Satuan <span class="text-blue-500">(Otomatis)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                            <input type="text" id="harga_per_satuan" name="harga_per_satuan" x-model="harga_per_satuan" readonly
                                   class="w-full pl-12 pr-4 py-2.5 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                                   placeholder="0">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Formula: Harga Per Kemasan / Jumlah Per Kemasan</p>
                    </div>

                    <!-- Keterangan (Full Width) -->
                    <div class="md:col-span-2">
                        <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">
                            Keterangan
                        </label>
                        <textarea id="keterangan" name="keterangan" rows="4"
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('keterangan') border-red-500 @enderror"
                                  placeholder="Masukkan keterangan atau catatan obat (opsional)">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('obat.index') }}" class="px-6 py-2.5 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    Simpan Data
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
