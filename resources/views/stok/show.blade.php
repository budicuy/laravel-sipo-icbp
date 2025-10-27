@extends('layouts.app')

@section('title', 'Detail Stok Obat - ' . $obat->nama_obat)

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li>
                    <a href="{{ route('stok.index') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1h2a1 1 0 011 1m0 0V5a1 1 0 00-1-1H9a1 1 0 00-1 1v6a1 1 0 001 1z" />
                        </svg>
                        Manajemen Stok
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500">{{ $obat->nama_obat }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Info Obat -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    Informasi Obat
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">Nama Obat:</span>
                            <span class="text-sm font-bold text-gray-900">{{ $obat->nama_obat }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">Satuan:</span>
                            <span class="text-sm font-bold text-gray-900">{{ $obat->satuanObat->nama_satuan ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">Stok Awal:</span>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($obat->stok_awal) }}
                                {{ $obat->satuanObat->nama_satuan ?? '' }}</span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">Sisa Stok Saat Ini:</span>
                            <span
                                class="text-lg font-bold {{ $sisaStok <= 0 ? 'text-red-600' : ($sisaStok <= 10 ? 'text-yellow-600' : 'text-green-600') }}">
                                {{ number_format($sisaStok) }} {{ $obat->satuanObat->nama_satuan ?? '' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">Status Stok:</span>
                            <div>
                                @if ($sisaStok <= 0)
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Habis</span>
                                @elseif($sisaStok <= 10)
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Rendah</span>
                                @else
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Tersedia</span>
                                @endif
                            </div>
                        </div>
                        @if ($obat->keterangan)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-600">Keterangan:</span>
                                <span class="text-sm font-bold text-gray-900">{{ $obat->keterangan }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Tambah Stok Masuk -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Stok Masuk Bulan Ini
                </h2>
            </div>
            <div class="p-6">
                <form action="{{ route('stok.masuk.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="obat_id" value="{{ $obat->id_obat }}">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="jumlah_stok_masuk" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Stok
                                Masuk</label>
                            <div class="relative">
                                <input type="number"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 pr-16"
                                    id="jumlah_stok_masuk" name="jumlah_stok_masuk" min="1" required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-sm text-gray-500">{{ $obat->satuanObat->nama_satuan ?? '' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l3 3m-3-3v12" />
                                </svg>
                                Tambah Stok
                            </button>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Periode Saat Ini:</label>
                            <div class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                                <span class="text-sm font-bold text-gray-900">{{ date('F Y') }}</span>
                            </div>
                        </div>
                    </div>

                    @if ($stokBulananIni && $stokBulananIni->stok_masuk > 0)
                        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-sm text-blue-800">
                                    Stok masuk untuk bulan ini: <strong>{{ number_format($stokBulananIni->stok_masuk) }}
                                        {{ $obat->satuanObat->nama_satuan ?? '' }}</strong>
                                </div>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Riwayat Stok Bulanan -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    History Stok Obat Per Bulan
                </h2>
                <span class="px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm text-white font-medium">
                    {{ $riwayatStok->count() }} periode
                </span>
            </div>
            <div class="p-6">
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm text-blue-800">
                            <strong>Rumus Perhitungan:</strong> Stok Akhir = Stok Awal + Total Stok Masuk - Total Stok Pakai
                        </div>
                    </div>
                </div>

                @forelse($riwayatStok as $index => $stok)
                    @php
                        // Hitung stok akhir bulanan dengan rumus: stok_awal + total_stok_masuk - total_stok_pakai
                        $totalStokMasuk = \App\Models\StokBulanan::where('obat_id', $obat->id_obat)
                            ->where(function ($query) use ($stok) {
                                $query->where('tahun', '<', $stok->tahun)->orWhere(function ($query) use ($stok) {
                                    $query->where('tahun', $stok->tahun)->where('bulan', '<=', $stok->bulan);
                                });
                            })
                            ->sum('stok_masuk');

                        $totalStokPakai = \App\Models\StokBulanan::where('obat_id', $obat->id_obat)
                            ->where(function ($query) use ($stok) {
                                $query->where('tahun', '<', $stok->tahun)->orWhere(function ($query) use ($stok) {
                                    $query->where('tahun', $stok->tahun)->where('bulan', '<=', $stok->bulan);
                                });
                            })
                            ->sum('stok_pakai');

                        $stokAkhirBulanan = $obat->stok_awal + $totalStokMasuk - $totalStokPakai;
                    @endphp

                    <div
                        class="border rounded-xl overflow-hidden mb-4 {{ $stokAkhirBulanan <= 0 ? 'border-red-300' : ($stokAkhirBulanan <= 10 ? 'border-yellow-300' : 'border-gray-200') }}">
                        <div
                            class="p-4 {{ $stokAkhirBulanan <= 0 ? 'bg-red-600 text-white' : ($stokAkhirBulanan <= 10 ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-800') }} flex justify-between items-center">
                            <h3 class="font-bold text-lg">{{ $stok->periode }}</h3>
                            <div>
                                <span
                                    class="px-3 py-1 {{ $stokAkhirBulanan <= 0 ? 'bg-white text-red-600' : ($stokAkhirBulanan <= 10 ? 'bg-gray-800 text-white' : 'bg-indigo-600 text-white') }} rounded-full text-sm font-medium">
                                    Sisa: {{ number_format($stokAkhirBulanan) }}
                                </span>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="text-center p-4 bg-gray-50 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">{{ number_format($stok->stok_masuk) }}
                                    </div>
                                    <div class="text-sm text-gray-600">Stok Masuk</div>
                                </div>
                                <div class="text-center p-4 bg-gray-50 rounded-lg">
                                    <div class="text-2xl font-bold text-red-600">{{ number_format($stok->stok_pakai) }}
                                    </div>
                                    <div class="text-sm text-gray-600">Stok Pakai</div>
                                </div>
                                <div class="text-center p-4 bg-gray-50 rounded-lg">
                                    <div
                                        class="text-2xl font-bold {{ $stokAkhirBulanan <= 0 ? 'text-red-600' : ($stokAkhirBulanan <= 10 ? 'text-yellow-600' : 'text-green-600') }}">
                                        {{ number_format($stokAkhirBulanan) }}
                                    </div>
                                    <div class="text-sm text-gray-600">Stok Akhir</div>
                                </div>
                                <div class="text-center p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        @if ($stok->stok_masuk > 0)
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Stok
                                                Ditambah</span>
                                        @endif
                                        @if ($stok->stok_pakai > 0)
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Stok
                                                Terpakai</span>
                                        @endif
                                        @if ($stok->stok_masuk == 0 && $stok->stok_pakai == 0)
                                            <span class="text-gray-500 text-sm">Tidak ada aktivitas</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Progress bar untuk visualisasi stok -->
                            <div class="mt-4">
                                <div class="w-full bg-gray-200 rounded-full h-6">
                                    <div class="bg-green-500 h-6 rounded-l-full"
                                        style="width: {{ $stok->stok_masuk > 0 ? min(($stok->stok_masuk / max($stok->stok_masuk, $stok->stok_pakai, 1)) * 100, 100) : 0 }}%"
                                        title="Stok Masuk: {{ number_format($stok->stok_masuk) }}">
                                    </div>
                                    <div class="bg-red-500 h-6 rounded-r-full"
                                        style="width: {{ $stok->stok_pakai > 0 ? min(($stok->stok_pakai / max($stok->stok_masuk, $stok->stok_pakai, 1)) * 100, 100) : 0 }}%"
                                        title="Stok Pakai: {{ number_format($stok->stok_pakai) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada riwayat stok untuk obat ini</h3>
                        <p class="text-gray-600">Riwayat stok akan muncul setelah ada aktivitas stok masuk atau stok pakai
                        </p>
                    </div>
                @endforelse

                @if ($riwayatStok->count() > 0)
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v8m5-4h.01M9 16h.01" />
                            </svg>
                            Ringkasan Stok
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
                                <div class="flex items-center">
                                    <div class="p-3 bg-blue-100 rounded-lg">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-2xl font-bold text-gray-900">
                                            {{ number_format($obat->stok_awal) }}</div>
                                        <div class="text-sm text-gray-600">Stok Awal</div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
                                <div class="flex items-center">
                                    <div class="p-3 bg-green-100 rounded-lg">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l3 3m-3-3v12" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-2xl font-bold text-gray-900">
                                            {{ number_format($riwayatStok->sum('stok_masuk')) }}</div>
                                        <div class="text-sm text-gray-600">Total Stok Masuk</div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
                                <div class="flex items-center">
                                    <div class="p-3 bg-red-100 rounded-lg">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l3 3m-3-3v12" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-2xl font-bold text-gray-900">
                                            {{ number_format($riwayatStok->sum('stok_pakai')) }}</div>
                                        <div class="text-sm text-gray-600">Total Stok Pakai</div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
                                <div class="flex items-center">
                                    <div
                                        class="p-3 {{ $sisaStok <= 0 ? 'bg-red-100' : ($sisaStok <= 10 ? 'bg-yellow-100' : 'bg-green-100') }} rounded-lg">
                                        <svg class="w-6 h-6 {{ $sisaStok <= 0 ? 'text-red-600' : ($sisaStok <= 10 ? 'text-yellow-600' : 'text-green-600') }}"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v8m5-4h.01M9 16h.01" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-2xl font-bold text-gray-900">{{ number_format($sisaStok) }}</div>
                                        <div class="text-sm text-gray-600">Sisa Stok Akhir</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chart untuk visualisasi trend stok -->
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Trend Stok (6 Bulan Terakhir)</h3>
                            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                                @php
                                    $lastSixMonths = $riwayatStok->take(6)->reverse();
                                    $maxStok = max($obat->stok_awal, $sisaStok, 1);
                                @endphp

                                @foreach ($lastSixMonths as $stok)
                                    @php
                                        // Hitung stok akhir untuk setiap bulan
                                        $totalMasuk = \App\Models\StokBulanan::where('obat_id', $obat->id_obat)
                                            ->where(function ($query) use ($stok) {
                                                $query
                                                    ->where('tahun', '<', $stok->tahun)
                                                    ->orWhere(function ($query) use ($stok) {
                                                        $query
                                                            ->where('tahun', $stok->tahun)
                                                            ->where('bulan', '<=', $stok->bulan);
                                                    });
                                            })
                                            ->sum('stok_masuk');

                                        $totalPakai = \App\Models\StokBulanan::where('obat_id', $obat->id_obat)
                                            ->where(function ($query) use ($stok) {
                                                $query
                                                    ->where('tahun', '<', $stok->tahun)
                                                    ->orWhere(function ($query) use ($stok) {
                                                        $query
                                                            ->where('tahun', $stok->tahun)
                                                            ->where('bulan', '<=', $stok->bulan);
                                                    });
                                            })
                                            ->sum('stok_pakai');

                                        $stokAkhir = $obat->stok_awal + $totalMasuk - $totalPakai;
                                    @endphp

                                    <div class="text-center">
                                        <div class="text-sm text-gray-600 mb-2">
                                            {{ \Carbon\Carbon::parse($stok->tahun . '-' . $stok->bulan . '-01')->format('M') }}
                                        </div>
                                        <div class="h-32 bg-gray-100 rounded-lg relative">
                                            <div class="absolute bottom-0 left-0 right-0 {{ $stokAkhir <= 0 ? 'bg-red-500' : ($stokAkhir <= 10 ? 'bg-yellow-500' : 'bg-green-500') }} rounded-b-lg"
                                                style="height: {{ ($stokAkhir / $maxStok) * 100 }}%"
                                                title="{{ $stok->periode }}: {{ number_format($stokAkhir) }}">
                                            </div>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900 mt-2">
                                            {{ number_format($stokAkhir) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Aksi -->
        <div class="flex gap-3 mt-6">
            <a href="{{ route('stok.index') }}"
                class="inline-flex items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar Stok
            </a>
            <a href="{{ route('obat.edit', $obat->id_obat) }}"
                class="inline-flex items-center px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828L8.586-8.586z" />
                </svg>
                Edit Obat
            </a>
        </div>
    </div>
    </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus pada input jumlah stok masuk
            document.getElementById('jumlah_stok_masuk')?.focus();

            // Konfirmasi sebelum submit form
            const form = document.querySelector('form[action*="stok.masuk.store"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const jumlah = document.getElementById('jumlah_stok_masuk').value;
                    if (jumlah && jumlah > 0) {
                        if (!confirm(
                                `Apakah Anda yakin ingin menambah stok sebesar ${jumlah} {{ $obat->satuanObat->nama_satuan ?? '' }}?`
                            )) {
                            e.preventDefault();
                        }
                    }
                });
            }
        });
    </script>
@endpush
