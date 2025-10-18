@extends('layouts.app')

@section('page-title', 'Harga Obat Per Bulan')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            Harga Obat Per Bulan
        </h1>
        <p class="text-gray-600 mt-2 ml-1">Manajemen harga obat per periode bulan</p>
    </div>

    <!-- Success Message -->
    @if(session('success'))
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
    @if(session('error'))
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

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Action Buttons Section -->
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-indigo-50">
            <div class="flex flex-wrap gap-3 items-center">
                <a href="{{ route('harga-obat.create') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Harga Obat
                </a>

                <button type="button" onclick="openGenerateModal()" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Generate Periode
                </button>

                <a href="{{ route('harga-obat.export', request()->query()) }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Excel
                </a>

                <button type="button" onclick="submitBulkDelete()" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus Terpilih
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="p-6 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <h3 class="text-sm font-semibold text-gray-800">Filter & Pencarian</h3>
            </div>

            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Nama Obat</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="obat" value="{{ request('obat') }}" class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="Masukkan nama obat...">
                    </div>
                </div>

                <div class="min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                    <select name="periode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white shadow-sm">
                        <option value="">Semua Periode</option>
                        @foreach($availablePeriodes as $periode)
                            <option value="{{ $periode['value'] }}" {{ request('periode') == $periode['value'] ? 'selected' : '' }}>{{ $periode['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Obat</label>
                    <select name="jenis_obat" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white shadow-sm">
                        <option value="">Semua Jenis</option>
                        @foreach($jenisObats as $jenis)
                            <option value="{{ $jenis->id_jenis_obat }}" {{ request('jenis_obat') == $jenis->id_jenis_obat ? 'selected' : '' }}>{{ $jenis->nama_jenis_obat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('harga-obat.index') }}" class="px-5 py-2.5 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">Reset</a>
                </div>
            </form>
        </div>

        <!-- Table Controls -->
        <div class="p-5 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
            <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-gray-700">Tampilkan</label>
                <form method="GET" id="perPageForm" class="inline">
                    @if(request('obat'))
                        <input type="hidden" name="obat" value="{{ request('obat') }}">
                    @endif
                    @if(request('periode'))
                        <input type="hidden" name="periode" value="{{ request('periode') }}">
                    @endif
                    @if(request('jenis_obat'))
                        <input type="hidden" name="jenis_obat" value="{{ request('jenis_obat') }}">
                    @endif
                    <select name="per_page" onchange="document.getElementById('perPageForm').submit()" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white shadow-sm">
                        <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                        <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                    </select>
                </form>
                <span class="text-sm font-medium text-gray-700">data per halaman</span>
            </div>
            <div class="text-sm text-gray-600">
                Total: <span class="font-semibold text-gray-900">{{ $hargaObats->total() }}</span> harga obat
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gradient-to-r from-indigo-700 to-purple-700">
                        <th class="px-4 py-4 text-left">
                            <input type="checkbox" onclick="toggleAll(this)" class="rounded border-gray-400 text-indigo-600 focus:ring-2 focus:ring-indigo-500">
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">No</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            <a href="{{ route('harga-obat.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'nama_obat', 'direction' => (request('sort') == 'nama_obat' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}"
                               class="flex items-center justify-between group hover:text-indigo-300 transition-colors">
                                <span>Nama Obat</span>
                                <span class="ml-2">
                                    @if(request('sort') == 'nama_obat')
                                        @if(request('direction') == 'asc')
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="w-4 h-4 text-white opacity-40 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            <a href="{{ route('harga-obat.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'jenis_obat', 'direction' => (request('sort') == 'jenis_obat' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}"
                               class="flex items-center justify-between group hover:text-indigo-300 transition-colors">
                                <span>Jenis Obat</span>
                                <span class="ml-2">
                                    @if(request('sort') == 'jenis_obat')
                                        @if(request('direction') == 'asc')
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="w-4 h-4 text-white opacity-40 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Satuan</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            <a href="{{ route('harga-obat.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'periode', 'direction' => (request('sort') == 'periode' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}"
                               class="flex items-center justify-between group hover:text-indigo-300 transition-colors">
                                <span>Periode</span>
                                <span class="ml-2">
                                    @if(request('sort') == 'periode')
                                        @if(request('direction') == 'asc')
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="w-4 h-4 text-white opacity-40 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">
                            <a href="{{ route('harga-obat.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'jumlah_per_kemasan', 'direction' => (request('sort') == 'jumlah_per_kemasan' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}"
                               class="flex items-center justify-center group hover:text-indigo-300 transition-colors">
                                <span>Jml/Kemasan</span>
                                <span class="ml-2">
                                    @if(request('sort') == 'jumlah_per_kemasan')
                                        @if(request('direction') == 'asc')
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="w-4 h-4 text-white opacity-40 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th class="px-4 py-4 text-right text-xs font-bold text-white uppercase tracking-wider">
                            <a href="{{ route('harga-obat.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'harga_per_kemasan', 'direction' => (request('sort') == 'harga_per_kemasan' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}"
                               class="flex items-center justify-between group hover:text-indigo-300 transition-colors">
                                <span>Harga/Kemasan</span>
                                <span class="ml-2">
                                    @if(request('sort') == 'harga_per_kemasan')
                                        @if(request('direction') == 'asc')
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="w-4 h-4 text-white opacity-40 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th class="px-4 py-4 text-right text-xs font-bold text-white uppercase tracking-wider">
                            <a href="{{ route('harga-obat.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'harga_per_satuan', 'direction' => (request('sort') == 'harga_per_satuan' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}"
                               class="flex items-center justify-between group hover:text-indigo-300 transition-colors">
                                <span>Harga/Satuan</span>
                                <span class="ml-2">
                                    @if(request('sort') == 'harga_per_satuan')
                                        @if(request('direction') == 'asc')
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="w-4 h-4 text-white opacity-40 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Tanggal Update</th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($hargaObats as $index => $hargaObat)
                        <tr class="hover:bg-indigo-50 transition-colors">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="selected_ids[]" value="{{ $hargaObat->id_harga_obat }}" class="row-checkbox rounded border-gray-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500">
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $hargaObats->firstItem() + $index }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $hargaObat->obat->nama_obat }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $hargaObat->obat->jenisObat->nama_jenis_obat ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $hargaObat->obat->satuanObat->nama_satuan ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $hargaObat->periode }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $hargaObat->jumlah_per_kemasan }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right">Rp {{ number_format($hargaObat->harga_per_kemasan, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right">Rp {{ number_format($hargaObat->harga_per_satuan, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $hargaObat->updated_at ? $hargaObat->updated_at->format('d-m-Y') : '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('harga-obat.edit', $hargaObat->id_harga_obat) }}" class="inline-flex items-center justify-center w-9 h-9 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-all shadow-sm hover:shadow-md" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button onclick="deleteHargaObat({{ $hargaObat->id_harga_obat }}, '{{ $hargaObat->obat->nama_obat }} - {{ $hargaObat->periode }}')" class="inline-flex items-center justify-center w-9 h-9 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all shadow-sm hover:shadow-md" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-lg font-medium">Tidak ada data harga obat</p>
                                <p class="text-sm mt-1">Mulai dengan menambahkan harga obat baru</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Custom Pagination -->
        @isset($hargaObats)
        @if($hargaObats->hasPages())
        <div class="px-6 py-5 border-t border-gray-200 bg-white">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-600">
                    Halaman <span class="font-semibold text-gray-900">{{ $hargaObats->currentPage() }}</span>
                    dari <span class="font-semibold text-gray-900">{{ $hargaObats->lastPage() }}</span>
                    <span class="mx-2 text-gray-400">•</span>
                    Total <span class="font-semibold text-gray-900">{{ $hargaObats->total() }}</span> data
                </div>

                <nav class="flex items-center gap-2" role="navigation">
                    @if($hargaObats->onFirstPage())
                        <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $hargaObats->appends(request()->except('page'))->url(1) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-indigo-50 hover:border-indigo-400 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    @if($hargaObats->onFirstPage())
                        <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>
                    @else
                        <a href="{{ $hargaObats->appends(request()->except('page'))->previousPageUrl() }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-indigo-50 hover:border-indigo-400 transition-all">Previous</a>
                    @endif

                    <div class="flex items-center gap-1">
                        @php
                            $start = max($hargaObats->currentPage() - 2, 1);
                            $end = min($hargaObats->currentPage() + 2, $hargaObats->lastPage());
                        @endphp

                        @if($start > 1)
                            <a href="{{ $hargaObats->appends(request()->except('page'))->url(1) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-indigo-50 hover:border-indigo-400 transition-all">1</a>
                            @if($start > 2)
                                <span class="px-2 text-gray-500">...</span>
                            @endif
                        @endif

                        @for($i = $start; $i <= $end; $i++)
                            @if($i == $hargaObats->currentPage())
                                <span class="px-3 py-2 text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-lg shadow-md">{{ $i }}</span>
                            @else
                                <a href="{{ $hargaObats->appends(request()->except('page'))->url($i) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-indigo-50 hover:border-indigo-400 transition-all">{{ $i }}</a>
                            @endif
                        @endfor

                        @if($end < $hargaObats->lastPage())
                            @if($end < $hargaObats->lastPage() - 1)
                                <span class="px-2 text-gray-500">...</span>
                            @endif
                            <a href="{{ $hargaObats->appends(request()->except('page'))->url($hargaObats->lastPage()) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-indigo-50 hover:border-indigo-400 transition-all">{{ $hargaObats->lastPage() }}</a>
                        @endif
                    </div>

                    @if($hargaObats->hasMorePages())
                        <a href="{{ $hargaObats->appends(request()->except('page'))->nextPageUrl() }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-indigo-50 hover:border-indigo-400 transition-all">Next</a>
                    @else
                        <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>
                    @endif

                    @if($hargaObats->currentPage() == $hargaObats->lastPage())
                        <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $hargaObats->appends(request()->except('page'))->url($hargaObats->lastPage()) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-indigo-50 hover:border-indigo-400 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
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

function deleteHargaObat(id, nama) {
    Swal.fire({
        title: 'Hapus Data Harga Obat?',
        html: `Apakah Anda yakin ingin menghapus harga obat <strong>${nama}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/harga-obat/${id}`, {
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
            confirmButtonColor: '#4f46e5'
        });
        return;
    }

    Swal.fire({
        title: 'Hapus Data Terpilih?',
        html: `Apakah Anda yakin ingin menghapus <strong>${ids.length}</strong> harga obat yang dipilih?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus Semua!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/harga-obat/bulk-delete', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: ids })
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

function openGenerateModal() {
    Swal.fire({
        title: 'Generate Harga Obat Per Periode',
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
                        <li>• Generate harga obat untuk semua obat yang ada</li>
                        <li>• Harga akan di-copy dari periode sebelumnya jika ada</li>
                        <li>• Jika tidak ada, akan dibuat dengan harga default (0)</li>
                        <li>• Format periode: MM-YY (contoh: 10-25)</li>
                    </ul>
                </div>

                <form id="generateForm" action="{{ route('harga-obat.generate-for-periode') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                        <input type="text"
                                name="periode"
                                id="periode"
                                placeholder="MM-YY (contoh: 10-25)"
                                pattern="^(0[1-9]|1[0-2])-(0[1-9]|[1-9][0-9])$"
                                title="Format: MM-YY (contoh: 10-25). Bulan: 01-12, Tahun: 01-99"
                                required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        <p class="mt-1 text-xs text-gray-500">Format periode: MM-YY (contoh: 10-25 untuk Oktober 2025)</p>
                    </div>
                </form>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Generate',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        width: '600px',
        customClass: {
            confirmButton: 'px-5 py-2.5 rounded-lg font-medium',
            cancelButton: 'px-5 py-2.5 rounded-lg font-medium'
        },
        preConfirm: () => {
            const periodeInput = document.getElementById('periode');
            if (!periodeInput.value) {
                Swal.showValidationMessage('Silakan isi periode terlebih dahulu');
                return false;
            }

            const periode = periodeInput.value;
            const periodeRegex = /^(0[1-9]|1[0-2])-(0[1-9]|[1-9][0-9])$/;

            if (!periodeRegex.test(periode)) {
                Swal.showValidationMessage('Format periode harus MM-YY (contoh: 10-25). Bulan: 01-12, Tahun: 01-99');
                return false;
            }

            // Additional validation for month and year
            const month = parseInt(periode.substring(0, 2));
            const year = parseInt(periode.substring(3, 5));

            if (month < 1 || month > 12) {
                Swal.showValidationMessage('Bulan harus antara 01-12');
                return false;
            }

            if (year < 1 || year > 99) {
                Swal.showValidationMessage('Tahun harus antara 01-99');
                return false;
            }

            return true;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const periodeInput = document.getElementById('periode');
            const periode = periodeInput.value;

            // Create FormData
            const formData = new FormData();
            formData.append('periode', periode);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            // Show loading
            Swal.fire({
                title: 'Sedang Generate...',
                html: 'Mohon tunggu, harga obat sedang digenerate',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Submit via AJAX
            fetch('{{ route("harga-obat.generate-for-periode") }}', {
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
                        html: data.message,
                        confirmButtonColor: '#16a34a'
                    }).then(() => {
                        // Reload page to show new data
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Generate',
                        html: data.message,
                        confirmButtonColor: '#16a34a'
                    });
                }
            })
            .catch(error => {
                console.error('Generate error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Generate',
                    html: 'Terjadi kesalahan saat generate harga obat',
                    confirmButtonColor: '#16a34a'
                });
            });
        }
    });
}

</script>
@endsection
