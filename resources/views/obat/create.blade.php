@extends('layouts.app')@extends('layouts.app')



@section('page-title', 'Tambah Data Obat')@section('page-title', 'Tambah Data Obat')



@section('content')@section('content')

<div class="p-6 bg-gray-50 min-h-screen" <div class="p-6 bg-gray-50 min-h-screen">

     x-data="{    <div class="mb-6">

         id_satuan: '{{ old('id_satuan') }}',        <div class="flex items-center gap-3 mb-3">

         jumlah_per_kemasan: {{ old('jumlah_per_kemasan', 1) }},            <a href="{{ route('obat.index') }}" class="p-2 hover:bg-white rounded-lg transition-colors">

         harga_per_kemasan: {{ old('harga_per_kemasan', 0) }},                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

         harga_per_satuan: {{ old('harga_per_satuan', 0) }},                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />

         stok_awal: {{ old('stok_awal', 0) }},                </svg>

         stok_masuk: {{ old('stok_masuk', 0) }},            </a>

         stok_keluar: {{ old('stok_keluar', 0) }},            <div>

         stok_akhir: {{ old('stok_akhir', 0) }},                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">

         satuanPerUnit: ['Ampul', 'Botol', 'Injek'],                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-3 rounded-lg shadow-lg">

                                 <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">

         init() {                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />

             this.$watch('id_satuan', value => this.updateJumlahKemasan());                        </svg>

             this.$watch('jumlah_per_kemasan', () => this.calculateHargaPerSatuan());                    </div>

             this.$watch('harga_per_kemasan', () => this.calculateHargaPerSatuan());                    Tambah Data Obat Baru

             this.$watch('stok_awal', () => this.calculateStokAkhir());                </h1>

             this.$watch('stok_masuk', () => this.calculateStokAkhir());                <p class="text-gray-600 mt-1 ml-1">Tambahkan obat baru ke persediaan farmasi</p>

             this.$watch('stok_keluar', () => this.calculateStokAkhir());            </div>

         },        </div>

             </div>

         updateJumlahKemasan() {

             const satuanSelect = document.getElementById('id_satuan');    <form action="{{ route('obat.store') }}" method="POST">

             if (!satuanSelect.selectedIndex) return;        @csrf

             const selectedOption = satuanSelect.options[satuanSelect.selectedIndex];

             const namaSatuan = selectedOption.text;        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">

                         <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">

             if (this.satuanPerUnit.includes(namaSatuan)) {                <h2 class="text-lg font-semibold text-white flex items-center gap-2">

                 this.jumlah_per_kemasan = 1;                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

             }                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />

         },                    </svg>

                             Informasi Data Obat

         calculateHargaPerSatuan() {                </h2>

             if (this.jumlah_per_kemasan > 0) {            </div>

                 this.harga_per_satuan = (this.harga_per_kemasan / this.jumlah_per_kemasan).toFixed(2);            

             } else {            <div class="p-6">

                 this.harga_per_satuan = 0;                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

             }                    <!-- Nama Obat -->

         },                    <div>

                                 <label for="nama_obat" class="block text-sm font-semibold text-gray-700 mb-2">

         calculateStokAkhir() {                            Nama Obat <span class="text-red-500">*</span>

             this.stok_akhir = (parseInt(this.stok_awal) || 0) + (parseInt(this.stok_masuk) || 0) - (parseInt(this.stok_keluar) || 0);                        </label>

         },                        <div class="relative">

                                     <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">

         isSatuanPerUnit() {                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">

             const satuanSelect = document.getElementById('id_satuan');                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />

             if (!satuanSelect.selectedIndex) return false;                                </svg>

             const selectedOption = satuanSelect.options[satuanSelect.selectedIndex];                            </div>

             const namaSatuan = selectedOption.text;                            <input type="text" id="nama_obat" name="nama_obat" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Nama obat" required>

             return this.satuanPerUnit.includes(namaSatuan);                        </div>

         }                    </div>

     }">

                        <!-- Jenis Obat -->

    <div class="mb-6">                    <div>

        <div class="flex items-center gap-3 mb-3">                        <label for="jenis_obat" class="block text-sm font-semibold text-gray-700 mb-2">

            <a href="{{ route('obat.index') }}" class="p-2 hover:bg-white rounded-lg transition-colors">                            Jenis Obat <span class="text-red-500">*</span>

                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">                        </label>

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />                        <div class="relative">

                </svg>                            <select id="jenis_obat" name="jenis_obat" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 appearance-none bg-white" required>

            </a>                                <option value="">-- Pilih Jenis Obat --</option>

            <div>                                <option value="Tablet">Tablet</option>

                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">                                <option value="Kapsul">Kapsul</option>

                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-3 rounded-lg shadow-lg">                                <option value="Sirup">Sirup</option>

                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">                                <option value="Salep">Salep</option>

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />                                <option value="Injeksi">Injeksi</option>

                        </svg>                            </select>

                    </div>                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">

                    Tambah Data Obat Baru                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                </h1>                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />

                <p class="text-gray-600 mt-1 ml-1">Tambahkan obat baru ke persediaan farmasi</p>                                </svg>

            </div>                            </div>

        </div>                        </div>

    </div>                    </div>



    <form action="{{ route('obat.store') }}" method="POST">                    <!-- Satuan Obat -->

        @csrf                    <div>

                        <label for="satuan_obat" class="block text-sm font-semibold text-gray-700 mb-2">

        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">                            Satuan Obat <span class="text-red-500">*</span>

            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">                        </label>

                <h2 class="text-lg font-semibold text-white flex items-center gap-2">                        <div class="relative">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">                            <select id="satuan_obat" name="satuan_obat" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 appearance-none bg-white" required>

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />                                <option value="">-- Pilih Satuan --</option>

                    </svg>                                <option value="Strip">Strip</option>

                    Informasi Data Obat                                <option value="Botol">Botol</option>

                </h2>                                <option value="Box">Box</option>

            </div>                                <option value="Tube">Tube</option>

                                            <option value="Ampul">Ampul</option>

            <div class="p-6">                            </select>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">

                    <!-- Nama Obat -->                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <div>                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />

                        <label for="nama_obat" class="block text-sm font-semibold text-gray-700 mb-2">                                </svg>

                            Nama Obat <span class="text-red-500">*</span>                            </div>

                        </label>                        </div>

                        <div class="relative">                    </div>

                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">

                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">                    <!-- Harga -->

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />                    <div>

                                </svg>                        <label for="harga" class="block text-sm font-semibold text-gray-700 mb-2">

                            </div>                            Harga <span class="text-red-500">*</span>

                            <input type="text" id="nama_obat" name="nama_obat" value="{{ old('nama_obat') }}" required                         </label>

                                   class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('nama_obat') border-red-500 @enderror"                         <div class="relative">

                                   placeholder="Contoh: Paracetamol 500mg">                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">

                        </div>                                <span class="text-gray-500 font-medium">Rp</span>

                        @error('nama_obat')                            </div>

                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>                            <input type="number" id="harga" name="harga" class="w-full pl-12 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="0" required>

                        @enderror                        </div>

                    </div>                    </div>



                    <!-- Jenis Obat -->                    <!-- Stok -->

                    <div>                    <div>

                        <label for="id_jenis_obat" class="block text-sm font-semibold text-gray-700 mb-2">                        <label for="stok" class="block text-sm font-semibold text-gray-700 mb-2">

                            Jenis Obat <span class="text-red-500">*</span>                            Stok <span class="text-red-500">*</span>

                        </label>                        </label>

                        <div class="relative">                        <div class="relative">

                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">

                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />

                                </svg>                                </svg>

                            </div>                            </div>

                            <select id="id_jenis_obat" name="id_jenis_obat" required                             <input type="number" id="stok" name="stok" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Jumlah stok" required>

                                    class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 bg-white @error('id_jenis_obat') border-red-500 @enderror">                        </div>

                                <option value="">Pilih Jenis Obat</option>                    </div>

                                @foreach($jenisObats as $jenis)

                                    <option value="{{ $jenis->id_jenis_obat }}" {{ old('id_jenis_obat') == $jenis->id_jenis_obat ? 'selected' : '' }}>                    <!-- Tanggal Kadaluarsa -->

                                        {{ $jenis->nama_jenis }}                    <div>

                                    </option>                        <label for="tanggal_kadaluarsa" class="block text-sm font-semibold text-gray-700 mb-2">

                                @endforeach                            Tanggal Kadaluarsa

                            </select>                        </label>

                        </div>                        <div class="relative">

                        @error('id_jenis_obat')                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">

                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        @enderror                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />

                    </div>                                </svg>

                            </div>

                    <!-- Satuan Obat -->                            <input type="date" id="tanggal_kadaluarsa" name="tanggal_kadaluarsa" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">

                    <div>                        </div>

                        <label for="id_satuan" class="block text-sm font-semibold text-gray-700 mb-2">                    </div>

                            Satuan Obat <span class="text-red-500">*</span>

                        </label>                    <!-- Keterangan (Full Width) -->

                        <div class="relative">                    <div class="md:col-span-2">

                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">                        <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">

                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">                            Keterangan

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />                        </label>

                                </svg>                        <textarea id="keterangan" name="keterangan" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Deskripsi atau keterangan tambahan obat (opsional)"></textarea>

                            </div>                    </div>

                            <select id="id_satuan" name="id_satuan" required x-model="id_satuan"                </div>

                                    class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 bg-white @error('id_satuan') border-red-500 @enderror">            </div>

                                <option value="">Pilih Satuan Obat</option>

                                @foreach($satuanObats as $satuan)            <!-- Form Actions -->

                                    <option value="{{ $satuan->id_satuan }}" {{ old('id_satuan') == $satuan->id_satuan ? 'selected' : '' }}>            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">

                                        {{ $satuan->nama_satuan }}                <button type="button" onclick="window.location.href='{{ route('obat.index') }}'" class="px-6 py-2.5 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-medium rounded-lg transition-all hover:shadow-md">

                                    </option>                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                @endforeach                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />

                            </select>                    </svg>

                        </div>                    Batal

                        @error('id_satuan')                </button>

                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">

                        @enderror                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    </div>                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />

                    </svg>

                    <!-- Jumlah Per Kemasan -->                    Simpan Data Obat

                    <div>                </button>

                        <label for="jumlah_per_kemasan" class="block text-sm font-semibold text-gray-700 mb-2">            </div>

                            Jumlah Per Kemasan <span class="text-red-500">*</span>        </div>

                        </label>    </form>

                        <div class="relative"></div>

                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">@endsection

                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <select id="jumlah_per_kemasan" name="jumlah_per_kemasan" x-model="jumlah_per_kemasan" required
                                    :disabled="isSatuanPerUnit()"
                                    :class="isSatuanPerUnit() ? 'bg-gray-100 cursor-not-allowed' : 'bg-white'"
                                    class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('jumlah_per_kemasan') border-red-500 @enderror">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="4">4</option>
                                <option value="6">6</option>
                                <option value="10">10</option>
                            </select>
                        </div>
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
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z" />
                                </svg>
                            </div>
                            <input type="number" id="stok_awal" name="stok_awal" value="{{ old('stok_awal', 0) }}" required min="0" x-model="stok_awal"
                                   class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('stok_awal') border-red-500 @enderror" 
                                   placeholder="0">
                        </div>
                        @error('stok_awal')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stok Masuk -->
                    <div>
                        <label for="stok_masuk" class="block text-sm font-semibold text-gray-700 mb-2">
                            Stok Masuk <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                </svg>
                            </div>
                            <input type="number" id="stok_masuk" name="stok_masuk" value="{{ old('stok_masuk', 0) }}" required min="0" x-model="stok_masuk"
                                   class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('stok_masuk') border-red-500 @enderror" 
                                   placeholder="0">
                        </div>
                        @error('stok_masuk')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stok Keluar -->
                    <div>
                        <label for="stok_keluar" class="block text-sm font-semibold text-gray-700 mb-2">
                            Stok Keluar <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                                </svg>
                            </div>
                            <input type="number" id="stok_keluar" name="stok_keluar" value="{{ old('stok_keluar', 0) }}" required min="0" x-model="stok_keluar"
                                   class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('stok_keluar') border-red-500 @enderror" 
                                   placeholder="0">
                        </div>
                        @error('stok_keluar')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stok Akhir (Read Only) -->
                    <div>
                        <label for="stok_akhir" class="block text-sm font-semibold text-gray-700 mb-2">
                            Stok Akhir <span class="text-blue-500">(Otomatis)</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="text" id="stok_akhir" x-model="stok_akhir" readonly
                                   class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" 
                                   placeholder="0">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Formula: Stok Awal + Stok Masuk - Stok Keluar</p>
                    </div>

                    <!-- Harga Per Kemasan -->
                    <div>
                        <label for="harga_per_kemasan" class="block text-sm font-semibold text-gray-700 mb-2">
                            Harga Per Kemasan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-medium">Rp</span>
                            </div>
                            <input type="number" id="harga_per_kemasan" name="harga_per_kemasan" value="{{ old('harga_per_kemasan', 0) }}" required min="0" step="0.01" x-model="harga_per_kemasan"
                                   class="pl-12 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('harga_per_kemasan') border-red-500 @enderror" 
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
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-medium">Rp</span>
                            </div>
                            <input type="text" id="harga_per_satuan" name="harga_per_satuan" x-model="harga_per_satuan" readonly
                                   class="pl-12 w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" 
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
