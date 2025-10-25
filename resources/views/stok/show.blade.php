@extends('layouts.app')

@section('page-title', 'Detail Stok Obat - ' . $obat->nama_obat)

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('stok.index') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-3 h-3 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                            </path>
                        </svg>
                        Manajemen Stok
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <a href="#" class="ml-1 text-sm font-medium text-gray-700 md:ml-2">{{ $obat->nama_obat }}</a>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Info Obat -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
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
                    <div>
                        <table class="w-full">
                            <tr>
                                <td class="py-2 text-sm font-medium text-gray-500 w-1/3">Nama Obat:</td>
                                <td class="py-2 text-sm text-gray-900">{{ $obat->nama_obat }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-sm font-medium text-gray-500">Satuan:</td>
                                <td class="py-2 text-sm text-gray-900">{{ $obat->satuanObat->nama_satuan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-sm font-medium text-gray-500">Stok Awal:</td>
                                <td class="py-2 text-sm text-gray-900">{{ number_format($obat->stok_awal) }}
                                    {{ $obat->satuanObat->nama_satuan ?? '' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table class="w-full">
                            <tr>
                                <td class="py-2 text-sm font-medium text-gray-500 w-1/3">Sisa Stok:</td>
                                <td class="py-2">
                                    <span
                                        class="text-xl font-bold {{ $sisaStok <= 0 ? 'text-red-600' : ($sisaStok <= 10 ? 'text-yellow-600' : 'text-green-600') }}">
                                        {{ number_format($sisaStok) }} {{ $obat->satuanObat->nama_satuan ?? '' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 text-sm font-medium text-gray-500">Status:</td>
                                <td class="py-2">
                                    @if ($sisaStok <= 0)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Habis
                                        </span>
                                    @elseif($sisaStok <= 10)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Rendah
                                        </span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Tersedia
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @if ($obat->keterangan)
                                <tr>
                                    <td class="py-2 text-sm font-medium text-gray-500">Keterangan:</td>
                                    <td class="py-2 text-sm text-gray-900">{{ $obat->keterangan }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Tambah Stok Masuk -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
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
                            <label for="jumlah_stok_masuk" class="block text-sm font-medium text-gray-700 mb-2">
                                Jumlah Stok Masuk <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" id="jumlah_stok_masuk" name="jumlah_stok_masuk" min="1"
                                    required
                                    class="w-full pr-16 pl-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                    placeholder="0">
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-gray-500 text-sm">{{ $obat->satuanObat->nama_satuan ?? '' }}</span>
                                </span>
                            </div>
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                class="px-4 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Tambah Stok
                            </button>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Periode:</label>
                            <p class="py-2.5 text-sm text-gray-900 font-medium">{{ date('F Y') }}</p>
                        </div>
                    </div>

                    @if ($stokBulananIni && $stokBulananIni->stok_masuk > 0)
                        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-800">
                                        Stok masuk untuk bulan ini:
                                        <strong>{{ number_format($stokBulananIni->stok_masuk) }}
                                            {{ $obat->satuanObat->nama_satuan ?? '' }}</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Riwayat Stok Bulanan -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Riwayat Stok Bulanan
                </h2>
            </div>
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
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stok Masuk
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stok Pakai
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stok Akhir
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Keterangan
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($riwayatStok as $index => $stok)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $stok->periode }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if ($stok->stok_masuk > 0)
                                        <span
                                            class="text-green-600 font-semibold">+{{ number_format($stok->stok_masuk) }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if ($stok->stok_pakai > 0)
                                        <span
                                            class="text-red-600 font-semibold">-{{ number_format($stok->stok_pakai) }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <span
                                        class="{{ $stok->stok_akhir <= 0 ? 'text-red-600' : ($stok->stok_akhir <= 10 ? 'text-yellow-600' : 'text-green-600') }}">
                                        {{ number_format($stok->stok_akhir) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if ($stok->stok_masuk > 0)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Stok Ditambah
                                        </span>
                                    @endif
                                    @if ($stok->stok_pakai > 0)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Stok Terpakai
                                        </span>
                                    @endif
                                    @if ($stok->stok_masuk == 0 && $stok->stok_pakai == 0)
                                        <span class="text-gray-400 text-xs">Tidak ada aktivitas</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button
                                        onclick="openEditStokModal({{ $stok->id }}, '{{ $stok->periode }}', {{ $stok->tahun }}, {{ $stok->bulan }}, {{ $stok->stok_masuk }}, {{ $stok->stok_pakai }})"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all shadow-sm hover:shadow-md"
                                        title="Edit Riwayat Stok">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada riwayat stok</h3>
                                        <p class="text-gray-500">Belum ada riwayat stok untuk obat ini</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary Statistics -->
        @if ($riwayatStok->count() > 0)
            <div class="mt-8">
                <div
                    class="bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 rounded-2xl shadow-xl border border-white/20 backdrop-blur-sm overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-white/20 p-2 rounded-lg backdrop-blur-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white">Ringkasan Stok Obat</h3>
                            <div class="ml-auto bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm">
                                <span class="text-sm font-medium text-white">{{ $riwayatStok->count() }} periode</span>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Total Stok Masuk -->
                            <div class="group relative">
                                <div
                                    class="absolute -inset-1 bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur">
                                </div>
                                <div class="relative bg-white rounded-xl border border-green-100 p-6 h-full">
                                    <div class="flex items-center justify-between mb-4">
                                        <div
                                            class="bg-gradient-to-r from-green-500 to-emerald-500 p-3 rounded-xl shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                            </svg>
                                        </div>
                                        <div class="bg-green-100 px-2 py-1 rounded-full">
                                            <span
                                                class="text-xs font-semibold text-green-800">+{{ $riwayatStok->sum('stok_masuk') > 0 ? number_format($riwayatStok->sum('stok_masuk')) : '0' }}</span>
                                        </div>
                                    </div>
                                    <p class="text-sm font-medium text-gray-600 mb-2">Total Stok Masuk</p>
                                    <p class="text-3xl font-bold text-gray-900">
                                        {{ number_format($riwayatStok->sum('stok_masuk')) }}</p>
                                    <div class="mt-3 flex items-center text-xs text-green-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                        <span>Stok ditambahkan</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Stok Pakai -->
                            <div class="group relative">
                                <div
                                    class="absolute -inset-1 bg-gradient-to-r from-red-600 to-rose-600 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur">
                                </div>
                                <div class="relative bg-white rounded-xl border border-red-100 p-6 h-full">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="bg-gradient-to-r from-red-500 to-rose-500 p-3 rounded-xl shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                                            </svg>
                                        </div>
                                        <div class="bg-red-100 px-2 py-1 rounded-full">
                                            <span
                                                class="text-xs font-semibold text-red-800">-{{ $riwayatStok->sum('stok_pakai') > 0 ? number_format($riwayatStok->sum('stok_pakai')) : '0' }}</span>
                                        </div>
                                    </div>
                                    <p class="text-sm font-medium text-gray-600 mb-2">Total Stok Pakai</p>
                                    <p class="text-3xl font-bold text-gray-900">
                                        {{ number_format($riwayatStok->sum('stok_pakai')) }}</p>
                                    <div class="mt-3 flex items-center text-xs text-red-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                        </svg>
                                        <span>Stok terpakai</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Stok Awal -->
                            <div class="group relative">
                                <div
                                    class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur">
                                </div>
                                <div class="relative bg-white rounded-xl border border-blue-100 p-6 h-full">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="bg-gradient-to-r from-blue-500 to-cyan-500 p-3 rounded-xl shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="bg-blue-100 px-2 py-1 rounded-full">
                                            <span class="text-xs font-semibold text-blue-800">Awal</span>
                                        </div>
                                    </div>
                                    <p class="text-sm font-medium text-gray-600 mb-2">Stok Awal</p>
                                    <p class="text-3xl font-bold text-gray-900">{{ number_format($obat->stok_awal) }}</p>
                                    <div class="mt-3 flex items-center text-xs text-blue-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Stok awal periode</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Sisa Stok Akhir -->
                            <div class="group relative">
                                <div
                                    class="absolute -inset-1 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur">
                                </div>
                                <div class="relative bg-white rounded-xl border border-purple-100 p-6 h-full">
                                    <div class="flex items-center justify-between mb-4">
                                        <div
                                            class="bg-gradient-to-r {{ $sisaStok <= 0 ? 'from-red-500 to-rose-500' : ($sisaStok <= 10 ? 'from-yellow-500 to-orange-500' : 'from-green-500 to-emerald-500') }} p-3 rounded-xl shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div
                                            class="{{ $sisaStok <= 0 ? 'bg-red-100 text-red-800' : ($sisaStok <= 10 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }} px-2 py-1 rounded-full">
                                            <span
                                                class="text-xs font-semibold">{{ $sisaStok <= 0 ? 'Habis' : ($sisaStok <= 10 ? 'Rendah' : 'Aman') }}</span>
                                        </div>
                                    </div>
                                    <p class="text-sm font-medium text-gray-600 mb-2">Sisa Stok Akhir</p>
                                    <p
                                        class="text-3xl font-bold {{ $sisaStok <= 0 ? 'text-red-600' : ($sisaStok <= 10 ? 'text-yellow-600' : 'text-green-600') }}">
                                        {{ number_format($sisaStok) }}</p>
                                    <div
                                        class="mt-3 flex items-center text-xs {{ $sisaStok <= 0 ? 'text-red-600' : ($sisaStok <= 10 ? 'text-yellow-600' : 'text-green-600') }}">
                                        @if ($sisaStok <= 0)
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>Stok habis, segera isi</span>
                                        @elseif($sisaStok <= 10)
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z" />
                                            </svg>
                                            <span>Stok rendah, perlu perhatian</span>
                                        @else
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>Stok aman</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

    <!-- Edit Stok Modal -->
    <div id="editStokModal"
        class="fixed inset-0 bg-black/25 backdrop-blur-sm bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden transition-all duration-300">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div
                class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Riwayat Stok Masuk</h3>
                            <div class="mt-2">
                                <form id="editStokForm" action="{{ route('stok.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" id="edit_stok_id" name="id">
                                    <input type="hidden" id="edit_obat_id" name="obat_id"
                                        value="{{ $obat->id_obat }}">
                                    <input type="hidden" id="edit_tahun" name="tahun">
                                    <input type="hidden" id="edit_bulan" name="bulan">
                                    <input type="hidden" id="edit_stok_pakai" name="stok_pakai"
                                        value="{{ $stok->stok_pakai ?? 0 }}">

                                    <div class="mb-4">
                                        <label for="edit_periode"
                                            class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                                        <input type="text" id="edit_periode" name="periode" readonly
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700">
                                    </div>

                                    <div class="mb-4">
                                        <label for="edit_stok_masuk"
                                            class="block text-sm font-medium text-gray-700 mb-2">Stok Masuk</label>
                                        <div class="relative">
                                            <input type="number" id="edit_stok_masuk" name="stok_masuk" min="0"
                                                class="w-full pr-16 pl-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <span
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-500 text-sm">
                                                {{ $obat->satuanObat->nama_satuan ?? '' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex justify-end space-x-3 mt-6">
                                        <button type="button" onclick="closeEditStokModal()"
                                            class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                            Batal
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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

            // Konfirmasi sebelum submit form edit
            const editForm = document.getElementById('editStokForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    if (!confirm('Apakah Anda yakin ingin menyimpan perubahan riwayat stok?')) {
                        e.preventDefault();
                    }
                });
            }
        });

        function openEditStokModal(id, periode, tahun, bulan, stokMasuk, stokPakai) {
            document.getElementById('edit_stok_id').value = id;
            document.getElementById('edit_periode').value = periode;
            document.getElementById('edit_tahun').value = tahun;
            document.getElementById('edit_bulan').value = bulan;
            document.getElementById('edit_stok_masuk').value = stokMasuk;
            document.getElementById('edit_stok_pakai').value = stokPakai;

            document.getElementById('editStokModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Mencegah scroll di background
        }

        function closeEditStokModal() {
            document.getElementById('editStokModal').classList.add('hidden');
            document.body.style.overflow = 'auto'; // Mengembalikan scroll
        }
    </script>
@endpush
