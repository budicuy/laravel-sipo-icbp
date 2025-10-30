@extends('layouts.app')

@section('page-title', 'Data Obat')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Header Section -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                Data Obat
            </h1>
            <p class="text-gray-600 mt-2 ml-1">Manajemen data obat dan persediaan farmasi</p>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: '{{ session('success') }}',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
            </script>
        @endif

        <!-- Error Message -->
        @if (session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: '{{ session('error') }}',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                });
            </script>
        @endif

        <!-- Warning Message -->
        @if (session('warning'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'warning',
                        title: '{{ session('warning') }}',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                });
            </script>
        @endif

        <!-- Main Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <!-- Action Buttons Section -->
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-green-50">
                <div class="flex flex-wrap gap-3 items-center">
                    <a href="{{ route('obat.create') }}"
                        class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Obat
                    </a>

                    @if (auth()->user()->role === 'Super Admin')
                        <button type="button" onclick="openImportModal()"
                            class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            Import Excel
                        </button>

                        <a href="{{ route('obat.export', request()->query()) }}"
                            class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export Excel
                        </a>
                    @endif

                    @if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Super Admin')
                        <button type="button" onclick="submitBulkDelete()"
                            class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus Terpilih
                        </button>
                    @endif
                </div>
            </div>

            <!-- Filter Section -->
            <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-800">Filter & Pencarian</h3>
                </div>

                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cari Nama Obat</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                                placeholder="Masukkan nama obat...">
                        </div>
                    </div>


                    <div class="min-w-[150px]">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Satuan Obat</label>
                        <select name="satuan_obat"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm bg-white shadow-sm">
                            <option value="">Semua Satuan</option>
                            @foreach ($satuanObats as $satuan)
                                <option value="{{ $satuan->id_satuan }}"
                                    {{ request('satuan_obat') == $satuan->id_satuan ? 'selected' : '' }}>
                                    {{ $satuan->nama_satuan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('obat.index') }}"
                            class="px-5 py-2.5 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Table Controls -->
            <div
                class="p-5 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
                <div class="flex items-center gap-3">
                    <label class="text-sm font-medium text-gray-700">Tampilkan</label>
                    <form method="GET" id="perPageForm" class="inline">
                        @if (request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        @if (request('satuan_obat'))
                            <input type="hidden" name="satuan_obat" value="{{ request('satuan_obat') }}">
                        @endif
                        <select name="per_page" onchange="document.getElementById('perPageForm').submit()"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm bg-white shadow-sm">
                            <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                            <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                        </select>
                    </form>
                    <span class="text-sm font-medium text-gray-700">data per halaman</span>
                </div>
                <div class="text-sm text-gray-600">
                    Total: <span class="font-semibold text-gray-900">{{ $obats->total() }}</span> obat
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gradient-to-r from-green-700 to-emerald-700">
                            @if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Super Admin')
                                <th class="px-4 py-4 text-left">
                                    <input type="checkbox" onclick="toggleAll(this)"
                                        class="rounded border-gray-400 text-green-600 focus:ring-2 focus:ring-green-500">
                                </th>
                            @endif
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">No</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                <a href="{{ route('obat.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'nama_obat', 'direction' => request('sort') == 'nama_obat' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-green-300 transition-colors">
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
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                <a href="{{ route('obat.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'satuan_obat', 'direction' => request('sort') == 'satuan_obat' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-green-300 transition-colors">
                                    <span>Satuan</span>
                                    <span class="ml-2">
                                        @if (request('sort') == 'satuan_obat')
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
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                <a href="{{ route('obat.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'keterangan', 'direction' => request('sort') == 'keterangan' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-green-300 transition-colors">
                                    <span>Keterangan</span>
                                    <span class="ml-2">
                                        @if (request('sort') == 'keterangan')
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
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                <a href="{{ route('obat.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'tanggal_update', 'direction' => request('sort') == 'tanggal_update' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-green-300 transition-colors">
                                    <span>Tanggal Update</span>
                                    <span class="ml-2">
                                        @if (request('sort') == 'tanggal_update')
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
                            @if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Super Admin')
                                <th class="px-4 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">
                                    Aksi
                                </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($obats as $index => $obat)
                            <tr class="hover:bg-green-50 transition-colors">
                                @if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Super Admin')
                                    <td class="px-4 py-3">
                                        <input type="checkbox" name="selected_ids[]" value="{{ $obat->id_obat }}"
                                            class="row-checkbox rounded border-gray-300 text-green-600 focus:ring-2 focus:ring-green-500">
                                    </td>
                                @endif
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $obats->firstItem() + $index }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $obat->nama_obat }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $obat->satuanObat->nama_satuan ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate"
                                    title="{{ $obat->keterangan }}">{{ $obat->keterangan ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $obat->tanggal_update ? $obat->tanggal_update->format('d-m-Y') : '-' }}
                                    </div>
                                </td>
                                @if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Super Admin')
                                    <td class="px-4 py-3 text-sm font-medium text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('obat.edit', $obat->id_obat) }}"
                                                class="inline-flex items-center justify-center w-9 h-9 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-all shadow-sm hover:shadow-md"
                                                title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <button onclick="deleteObat({{ $obat->id_obat }}, '{{ $obat->nama_obat }}')"
                                                class="inline-flex items-center justify-center w-9 h-9 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all shadow-sm hover:shadow-md"
                                                title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-lg font-medium">Tidak ada data obat</p>
                                    <p class="text-sm mt-1">Mulai dengan menambahkan obat baru</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom Pagination -->
            @isset($obats)
                @if ($obats->hasPages())
                    <div class="px-6 py-5 border-t border-gray-200 bg-white">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                            <div class="text-sm text-gray-600">
                                Halaman <span class="font-semibold text-gray-900">{{ $obats->currentPage() }}</span>
                                dari <span class="font-semibold text-gray-900">{{ $obats->lastPage() }}</span>
                                <span class="mx-2 text-gray-400">•</span>
                                Total <span class="font-semibold text-gray-900">{{ $obats->total() }}</span> data
                            </div>

                            <nav class="flex items-center gap-2" role="navigation">
                                @if ($obats->onFirstPage())
                                    <span
                                        class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                                        </svg>
                                    </span>
                                @else
                                    <a href="{{ $obats->appends(request()->except('page'))->url(1) }}"
                                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-green-50 hover:border-green-400 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                                        </svg>
                                    </a>
                                @endif

                                @if ($obats->onFirstPage())
                                    <span
                                        class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>
                                @else
                                    <a href="{{ $obats->appends(request()->except('page'))->previousPageUrl() }}"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-green-50 hover:border-green-400 transition-all">Previous</a>
                                @endif

                                <div class="flex items-center gap-1">
                                    @php
                                        $start = max($obats->currentPage() - 2, 1);
                                        $end = min($obats->currentPage() + 2, $obats->lastPage());
                                    @endphp

                                    @if ($start > 1)
                                        <a href="{{ $obats->appends(request()->except('page'))->url(1) }}"
                                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-green-50 hover:border-green-400 transition-all">1</a>
                                        @if ($start > 2)
                                            <span class="px-2 text-gray-500">...</span>
                                        @endif
                                    @endif

                                    @for ($i = $start; $i <= $end; $i++)
                                        @if ($i == $obats->currentPage())
                                            <span
                                                class="px-3 py-2 text-sm font-bold text-white bg-gradient-to-r from-green-600 to-green-700 rounded-lg shadow-md">{{ $i }}</span>
                                        @else
                                            <a href="{{ $obats->appends(request()->except('page'))->url($i) }}"
                                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-green-50 hover:border-green-400 transition-all">{{ $i }}</a>
                                        @endif
                                    @endfor

                                    @if ($end < $obats->lastPage())
                                        @if ($end < $obats->lastPage() - 1)
                                            <span class="px-2 text-gray-500">...</span>
                                        @endif
                                        <a href="{{ $obats->appends(request()->except('page'))->url($obats->lastPage()) }}"
                                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-green-50 hover:border-green-400 transition-all">{{ $obats->lastPage() }}</a>
                                    @endif
                                </div>

                                @if ($obats->hasMorePages())
                                    <a href="{{ $obats->appends(request()->except('page'))->nextPageUrl() }}"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-green-50 hover:border-green-400 transition-all">Next</a>
                                @else
                                    <span
                                        class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>
                                @endif

                                @if ($obats->currentPage() == $obats->lastPage())
                                    <span
                                        class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                        </svg>
                                    </span>
                                @else
                                    <a href="{{ $obats->appends(request()->except('page'))->url($obats->lastPage()) }}"
                                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-green-50 hover:border-green-400 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                @endif
                            </nav>
                        </div>
                    </div>
                @endif
            @endisset
        </div>
    </div>

    <script>
        function toggleAll(checkbox) {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
        }

        function deleteObat(id, nama) {
            Swal.fire({
                title: 'Hapus Data Obat?',
                html: `Apakah Anda yakin ingin menghapus obat <strong>${nama}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/obat/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: data.message,
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data', 'error');
                        });
                }
            });
        }

        function submitBulkDelete() {
            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);

            if (ids.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Ada Data Dipilih',
                    text: 'Pilih minimal satu data untuk dihapus',
                    confirmButtonColor: '#16a34a'
                });
                return;
            }

            Swal.fire({
                title: 'Hapus Data Terpilih?',
                html: `Apakah Anda yakin ingin menghapus <strong>${ids.length}</strong> obat yang dipilih?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/obat/bulk-delete', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                ids: ids
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: data.message,
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data', 'error');
                        });
                }
            });
        }

        // Fungsi untuk memastikan parameter filter tetap terjaga saat mengurutkan
        function updateSortParams(field, direction) {
            const url = new URL(window.location);
            url.searchParams.set('sort', field);
            url.searchParams.set('direction', direction);
            window.location.href = url.toString();
        }

        function openImportModal() {
            Swal.fire({
                title: 'Import Data Obat dari Excel',
                html: `
            <div class="text-left">
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <h4 class="font-semibold text-green-900 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Informasi Penting
                    </h4>
                    <ul class="text-sm text-green-800 space-y-1 ml-7">
                        <li>• Format file: Excel (.xlsx atau .xls)</li>
                        <li>• Maksimal ukuran file: 5MB</li>
                        <li>• Download template terlebih dahulu</li>
                        <li>• Format: Nama Obat | Satuan | Keterangan</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <a href="{{ route('obat.template') }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all w-full justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Template Obat
                    </a>
                </div>

                <form id="importForm" action="{{ route('obat.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel Obat</label>
                        <input type="file"
                               name="file"
                               id="importFile"
                               accept=".xlsx,.xls"
                               required
                               class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2">
                        <p class="mt-1 text-xs text-gray-500">File Excel (.xlsx atau .xls), maksimal 5MB</p>
                    </div>
                </form>
            </div>
        `,
                showCancelButton: true,
                confirmButtonText: 'Upload & Import',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                width: '600px',
                customClass: {
                    confirmButton: 'px-5 py-2.5 rounded-lg font-medium',
                    cancelButton: 'px-5 py-2.5 rounded-lg font-medium'
                },
                preConfirm: () => {
                    const fileInput = document.getElementById('importFile');
                    if (!fileInput.files || fileInput.files.length === 0) {
                        Swal.showValidationMessage('Silakan pilih file Excel terlebih dahulu');
                        return false;
                    }

                    const file = fileInput.files[0];
                    const maxSize = 5 * 1024 * 1024; // 5MB

                    if (file.size > maxSize) {
                        Swal.showValidationMessage('Ukuran file maksimal 5MB');
                        return false;
                    }

                    const allowedExtensions = ['xlsx', 'xls'];
                    const fileExtension = file.name.split('.').pop().toLowerCase();

                    if (!allowedExtensions.includes(fileExtension)) {
                        Swal.showValidationMessage('Format file harus .xlsx atau .xls');
                        return false;
                    }

                    return true;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const fileInput = document.getElementById('importFile');
                    const file = fileInput.files[0];

                    // Create FormData
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content'));

                    // Show loading
                    Swal.fire({
                        title: 'Sedang Mengimport...',
                        html: 'Mohon tunggu, data obat sedang diproses',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit via AJAX
                    fetch('{{ route('obat.import') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => Promise.reject(err));
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                html: data.message,
                                confirmButtonColor: '#16a34a'
                            }).then(() => {
                                // Reload page to show new data
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000); // 2-second delay
                            });
                        })
                        .catch(error => {
                            console.error('Import error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Import',
                                html: error.message || 'Terjadi kesalahan saat mengimport data obat',
                                confirmButtonColor: '#16a34a'
                            });
                        });
                }
            });
        }
    </script>
@endsection
