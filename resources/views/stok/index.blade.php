@extends('layouts.app')

@section('page-title', 'Manajemen Stok Obat')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Header Section -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                Manajemen Stok Obat
            </h1>
            <p class="text-gray-600 mt-2 ml-1">Pantau dan kelola persediaan obat</p>
        </div>

        <!-- Summary Statistics Cards -->
        @if ($obatsWithStok->count() > 0)
            <div class="mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Obat Card -->
                    <div
                        class="relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                        <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-blue-300 opacity-20"></div>
                        <div class="absolute -right-4 -bottom-4 h-24 w-24 rounded-full bg-blue-400 opacity-10"></div>
                        <div class="relative p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100 text-sm font-medium">Total Obat</p>
                                    <p class="text-white text-3xl font-bold mt-1">{{ $obatsWithStok->count() }}</p>
                                    <p class="text-blue-200 text-xs mt-2">Jenis obat terdaftar</p>
                                </div>
                                <div class="bg-blue-400 bg-opacity-30 rounded-lg p-3">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stok Habis Card -->
                    <div
                        class="relative overflow-hidden rounded-xl bg-gradient-to-br from-red-500 to-red-600 shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                        <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-red-300 opacity-20"></div>
                        <div class="absolute -right-4 -bottom-4 h-24 w-24 rounded-full bg-red-400 opacity-10"></div>
                        <div class="relative p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-red-100 text-sm font-medium">Stok Habis</p>
                                    <p class="text-white text-3xl font-bold mt-1">
                                        {{ $obatsWithStok->where('sisa_stok', '<=', 0)->count() }}</p>
                                    <p class="text-red-200 text-xs mt-2">Perlu segera diisi</p>
                                </div>
                                <div class="bg-red-400 bg-opacity-30 rounded-lg p-3">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stok Rendah Card -->
                    <div
                        class="relative overflow-hidden rounded-xl bg-gradient-to-br from-yellow-500 to-orange-500 shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                        <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-yellow-300 opacity-20"></div>
                        <div class="absolute -right-4 -bottom-4 h-24 w-24 rounded-full bg-yellow-400 opacity-10"></div>
                        <div class="relative p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-yellow-100 text-sm font-medium">Stok Rendah</p>
                                    <p class="text-white text-3xl font-bold mt-1">
                                        {{ $obatsWithStok->where('sisa_stok', '>', 0)->where('sisa_stok', '<=', 10)->count() }}
                                    </p>
                                    <p class="text-yellow-200 text-xs mt-2">Kurang dari 10 unit</p>
                                </div>
                                <div class="bg-yellow-400 bg-opacity-30 rounded-lg p-3">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stok Tersedia Card -->
                    <div
                        class="relative overflow-hidden rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                        <div class="absolute -right-2 -top-2 h-16 w-16 rounded-full bg-green-300 opacity-20"></div>
                        <div class="absolute -right-4 -bottom-4 h-24 w-24 rounded-full bg-green-400 opacity-10"></div>
                        <div class="relative p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-100 text-sm font-medium">Stok Tersedia</p>
                                    <p class="text-white text-3xl font-bold mt-1">
                                        {{ $obatsWithStok->where('sisa_stok', '>', 10)->count() }}</p>
                                    <p class="text-green-200 text-xs mt-2">Stok aman</p>
                                </div>
                                <div class="bg-green-400 bg-opacity-30 rounded-lg p-3">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <!-- Action Buttons Section -->
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-blue-50">
                <div class="flex flex-wrap gap-3 items-center">
                    <a href="{{ route('obat.create') }}"
                        class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Obat Baru
                    </a>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-800">Filter & Pencarian</h3>
                </div>

                <form method="GET" action="{{ route('stok.index') }}" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[300px]">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cari Nama Obat</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                placeholder="Masukkan nama obat...">
                        </div>
                    </div>

                    <div class="min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Stok</label>
                        <select name="stok_status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-white">
                            <option value="">Semua Status</option>
                            <option value="habis" {{ request('stok_status') == 'habis' ? 'selected' : '' }}>Stok Habis (≤
                                0)</option>
                            <option value="rendah" {{ request('stok_status') == 'rendah' ? 'selected' : '' }}>Stok Rendah
                                (1-10)</option>
                            <option value="tersedia" {{ request('stok_status') == 'tersedia' ? 'selected' : '' }}>Stok
                                Tersedia (> 10)</option>
                        </select>
                    </div>

                    <div class="min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                        <select name="sort"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-white">
                            <option value="nama_obat" {{ request('sort') == 'nama_obat' ? 'selected' : '' }}>Nama Obat
                            </option>
                            <option value="sisa_stok" {{ request('sort') == 'sisa_stok' ? 'selected' : '' }}>Sisa Stok
                            </option>
                        </select>
                    </div>

                    <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('stok.index') }}"
                        class="px-5 py-2.5 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset
                    </a>
                </form>
            </div>

            <!-- Table Controls -->
            <div
                class="p-5 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
                <div class="text-sm text-gray-600">
                    Total: <span class="font-semibold text-gray-900">{{ $obatsWithStok->count() }}</span> obat
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gradient-to-r from-blue-700 to-indigo-700">
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">No</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider w-2/5">
                                <a href="{{ route('stok.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'nama_obat', 'direction' => request('sort') == 'nama_obat' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-blue-300 transition-colors">
                                    <span>Nama Obat</span>
                                    <span class="ml-2">
                                        @if (request('sort') == 'nama_obat')
                                            @if (request('direction') == 'asc')
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        @else
                                            <svg class="w-4 h-4 text-white opacity-40 group-hover:opacity-100 transition-opacity"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                            </svg>
                                        @endif
                                    </span>
                                </a>
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider w-1/6">
                                Satuan</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider w-1/6">
                                <a href="{{ route('stok.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'sisa_stok', 'direction' => request('sort') == 'sisa_stok' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-blue-300 transition-colors">
                                    <span>Sisa Stok</span>
                                    <span class="ml-2">
                                        @if (request('sort') == 'sisa_stok')
                                            @if (request('direction') == 'asc')
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        @else
                                            <svg class="w-4 h-4 text-white opacity-40 group-hover:opacity-100 transition-opacity"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                            </svg>
                                        @endif
                                    </span>
                                </a>
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Status
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($obatsWithStok as $index => $obat)
                            <tr class="hover:bg-blue-50 transition-colors">
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($obat->nama_obat, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <a href="{{ route('stok.show', $obat->id_obat) }}"
                                                class="text-sm font-medium text-gray-900 hover:text-blue-600 truncate block"
                                                title="{{ $obat->nama_obat }}">
                                                {{ $obat->nama_obat }}
                                            </a>
                                            @if ($obat->keterangan)
                                                <p class="text-xs text-gray-500 truncate"
                                                    title="{{ $obat->keterangan }}">
                                                    {{ Str::limit($obat->keterangan, 30) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $obat->satuanObat->nama_satuan ?? '-' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span
                                        class="text-lg font-bold {{ $obat->sisa_stok <= 0 ? 'text-red-600' : ($obat->sisa_stok <= 10 ? 'text-yellow-600' : 'text-green-600') }}">
                                        {{ number_format($obat->sisa_stok) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if ($obat->sisa_stok <= 0)
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Habis
                                        </span>
                                    @elseif($obat->sisa_stok <= 10)
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Rendah
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Tersedia
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('stok.show', $obat->id_obat) }}"
                                            class="inline-flex items-center justify-center w-9 h-9 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all shadow-sm hover:shadow-md"
                                            title="Detail Stok">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <p class="text-sm">Tidak ada data obat</p>
                                        <a href="{{ route('obat.create') }}"
                                            class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                                            Tambah Obat Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary Statistics -->
    </div>
@endsection
@section('scripts')
    <script>
        // Auto-refresh setiap 30 detik untuk update stok real-time
        setInterval(function() {
            window.location.reload();
        }, 30000);
    </script>
@endsection
