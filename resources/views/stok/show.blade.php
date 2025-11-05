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
        @if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Super Admin')
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Stok Masuk
                    </h2>
                </div>


                <div class="p-6">
                    <form action="{{ route('stok.masuk.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="obat_id" value="{{ $obat->id_obat }}">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="jumlah_stok_masuk" class="block text-sm font-medium text-gray-700 mb-2">Jumlah
                                    Stok
                                    Masuk</label>
                                <div class="relative">
                                    <input type="number"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 pr-16"
                                        id="jumlah_stok_masuk" name="jumlah_stok_masuk" min="1" required
                                        placeholder="Masukkan jumlah stok masuk">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span
                                            class="text-sm text-gray-500">{{ $obat->satuanObat->nama_satuan ?? '' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="periode" class="block text-sm font-medium text-gray-700 mb-2">Pilih
                                    Periode:</label>
                                <input type="month" name="periode" id="periode" value="{{ now()->format('Y-m') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                    required>
                            </div>
                            <div class="flex items-end">
                                <button type="submit"
                                    class="w-full px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l3 3m-3-3v12" />
                                    </svg>
                                    Tambah Stok
                                </button>
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
                                        Stok masuk bulan ini ({{ date('F Y') }}):
                                        <strong>{{ number_format($stokBulananIni->stok_masuk) }}
                                            {{ $obat->satuanObat->nama_satuan ?? '' }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        @endif

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
                <span class="px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm text-purple-600 font-medium">
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

                @if ($riwayatStok->count() > 0)
                    <div class="mt-8 mb-5">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0h2a2 2 0 012 2h2a2 2 0 012-2V5a2 2 0 00-2-2h-2a2 2 0 00-2 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
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
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
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
                                                d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                                                d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-2xl font-bold text-gray-900">{{ number_format($sisaStok) }}</div>
                                        <div class="text-sm text-gray-600">Sisa Stok Akhir</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($riwayatStok->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Periode
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stok Masuk
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stok Pakai
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stok Akhir
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    @if (auth()->user()->role === 'Super Admin')
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($riwayatStok as $index => $stok)
                                    @php
                                        // Hitung stok akhir bulanan dengan rumus: stok_awal + total_stok_masuk - total_stok_pakai
                                        $totalStokMasuk = \App\Models\StokBulanan::where('obat_id', $obat->id_obat)
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

                                        $totalStokPakai = \App\Models\StokBulanan::where('obat_id', $obat->id_obat)
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

                                        $stokAkhirBulanan = $obat->stok_awal + $totalStokMasuk - $totalStokPakai;
                                    @endphp
                                    <tr
                                        class="hover:bg-gray-50 {{ $stokAkhirBulanan <= 0 ? 'bg-red-50' : ($stokAkhirBulanan <= 10 ? 'bg-yellow-50' : '') }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $stok->periode }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="text-sm font-bold text-green-600">
                                                {{ number_format($stok->stok_masuk) }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $obat->satuanObat->nama_satuan ?? '' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="text-sm font-bold text-red-600">
                                                {{ number_format($stok->stok_pakai) }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $obat->satuanObat->nama_satuan ?? '' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div
                                                class="text-sm font-bold {{ $stokAkhirBulanan <= 0 ? 'text-red-600' : ($stokAkhirBulanan <= 10 ? 'text-yellow-600' : 'text-green-600') }}">
                                                {{ number_format($stokAkhirBulanan) }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $obat->satuanObat->nama_satuan ?? '' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if ($stokAkhirBulanan <= 0)
                                                <span
                                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Habis
                                                </span>
                                            @elseif($stokAkhirBulanan <= 10)
                                                <span
                                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Rendah
                                                </span>
                                            @else
                                                <span
                                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Tersedia
                                                </span>
                                            @endif
                                        </td>
                                        @if (auth()->user()->role === 'Super Admin')
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <button
                                                    onclick="openEditModal({{ $stok->id }}, '{{ $stok->periode }}', {{ $stok->stok_masuk }}, {{ $stok->stok_pakai }})"
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-all shadow-sm hover:shadow-md"
                                                    title="Edit Stok">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
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
                @endif

            </div>
        </div>
    </div>
    </div>
    </div>

    <!-- Modal Edit Stok -->
    <div id="editStokModal" class="fixed inset-0 bg-black/25 backdrop-blur-sm overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-60 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Stok Bulanan</h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="px-7 py-3">
                    <form id="editStokForm" method="POST"
                        action="{{ route('stok.bulanan.update', ['id' => '__ID__']) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="obat_id" value="{{ $obat->id_obat }}">

                        <div class="mb-4 text-left">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Periode
                            </label>
                            <input type="text" id="edit_periode" readonly
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700">
                        </div>

                        <div class="mb-4 text-left">
                            <label for="edit_stok_masuk" class="block text-sm font-medium text-gray-700 mb-2">
                                Stok Masuk
                            </label>
                            <div class="relative">
                                <input type="number" id="edit_stok_masuk" name="stok_masuk" min="0" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                                    placeholder="Masukkan stok masuk">
                            </div>
                        </div>

                        <div class="mb-4 text-left">
                            <label for="edit_stok_pakai" class="block text-sm font-medium text-gray-700 mb-2">
                                Stok Pakai
                            </label>
                            <div class="relative">
                                <input type="number" id="edit_stok_pakai" name="stok_pakai" min="0" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                                    placeholder="Masukkan stok pakai">
                            </div>
                        </div>

                        <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-md mb-4 text-left">
                            <div class="flex">
                                <svg class="w-5 h-5 text-yellow-600 mr-2 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div class="text-xs text-yellow-800">
                                    <strong>Perhatian:</strong> Perubahan stok akan mempengaruhi perhitungan stok akhir
                                    periode ini dan periode berikutnya.
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-center space-x-3">
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Simpan
                            </button>
                            <button type="button" onclick="closeEditModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                Batal
                            </button>
                        </div>
                    </form>
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

        // Fungsi untuk membuka modal edit
        function openEditModal(id, periode, stokMasuk, stokPakai) {
            document.getElementById('edit_periode').value = periode;
            document.getElementById('edit_stok_masuk').value = stokMasuk;
            document.getElementById('edit_stok_pakai').value = stokPakai;

            // Update form action dengan ID yang benar
            const form = document.getElementById('editStokForm');
            form.action = form.action.replace('__ID__', id);

            document.getElementById('editStokModal').classList.remove('hidden');
        }

        // Fungsi untuk menutup modal edit
        function closeEditModal() {
            document.getElementById('editStokModal').classList.add('hidden');
            // Reset form action
            const form = document.getElementById('editStokForm');
            form.action = form.action.replace(/\/\d+$/, '/__ID__');
        }

        // Tutup modal jika klik di luar modal
        document.getElementById('editStokModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Tutup modal dengan tombol ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditModal();
            }
        });
    </script>
@endpush
