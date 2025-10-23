@extends('layouts.app')

@section('page-title', 'Stok Obat')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            Manajemen Stok Obat
        </h1>
        <p class="text-gray-600 mt-2 ml-1">Kelola stok obat per periode bulanan dengan sistem revisi</p>
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
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        </script>
    @endif

    <!-- Info Message -->
    @if(session('info'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: '{{ session('info') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        </script>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Action Buttons Section -->
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-purple-50">
            <div class="flex flex-wrap gap-3 items-center">
                <a href="{{ route('stok-obat.create') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Stok
                </a>

                <button type="button" onclick="generateStokAwal()" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Generate Stok Awal
                </a>

                <button type="button" onclick="updateStokPakai()" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Update Stok Pakai
                </button>

                <button type="button" onclick="submitBulkDelete()" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus Terpilih
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="p-6 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-gray-200">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <h3 class="text-sm font-semibold text-gray-800">Filter & Pencarian</h3>
            </div>

            <form method="GET" action="{{ route('stok-obat.index') }}" class="flex flex-wrap gap-4 items-end">
                <div class="min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                    <select name="periode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm bg-white shadow-sm">
                        <option value="">Semua Periode</option>
                        @foreach($availablePeriodes as $periode)
                            <option value="{{ $periode['value'] }}" {{ request('periode') == $periode['value'] ? 'selected' : '' }}>
                                {{ $periode['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Obat</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="obat" value="{{ request('obat') }}" class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm" placeholder="Cari nama obat...">
                    </div>
                </div>

                <div class="min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Periode Awal</label>
                    <select name="periode_start" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm bg-white shadow-sm">
                        <option value="">Semua</option>
                        @foreach($availablePeriodes as $periode)
                            <option value="{{ $periode['value'] }}" {{ request('periode_start') == $periode['value'] ? 'selected' : '' }}>
                                {{ $periode['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Periode Akhir</label>
                    <select name="periode_end" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm bg-white shadow-sm">
                        <option value="">Semua</option>
                        @foreach($availablePeriodes as $periode)
                            <option value="{{ $periode['value'] }}" {{ request('periode_end') == $periode['value'] ? 'selected' : '' }}>
                                {{ $periode['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Stok</label>
                    <select name="stok_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm bg-white shadow-sm">
                        <option value="">Semua Status</option>
                        <option value="habis" {{ request('stok_status') == 'habis' ? 'selected' : '' }}>Habis (≤ 0)</option>
                        <option value="rendah" {{ request('stok_status') == 'rendah' ? 'selected' : '' }}>Rendah (1-10)</option>
                        <option value="tersedia" {{ request('stok_status') == 'tersedia' ? 'selected' : '' }}>Tersedia (> 10)</option>
                    </select>
                </div>

                <div class="min-w-[120px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data per Halaman</label>
                    <select name="per_page" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm bg-white shadow-sm">
                        <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                        <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('stok-obat.index') }}" class="px-5 py-2.5 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">Reset</a>
                </div>
            </form>
        </div>

        <!-- Table Controls -->
        <div class="p-5 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
            <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-gray-700">Terpilih:</label>
                <span id="selectedCount" class="text-sm font-semibold text-purple-600">0 data</span>
            </div>
            <div class="text-sm text-gray-600">
                Total: <span class="font-semibold text-gray-900">{{ $stokObats->total() }}</span> data stok
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gradient-to-r from-purple-700 to-indigo-700">
                        <th class="px-4 py-4 text-left">
                            <input type="checkbox" onclick="toggleAll(this)" class="rounded border-gray-400 text-purple-600 focus:ring-2 focus:ring-purple-500">
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">No</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            <a href="{{ route('stok-obat.index', array_merge(request()->query(), ['sort' => 'nama_obat', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center justify-between group hover:text-purple-300 transition-colors">
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
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Satuan</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            <a href="{{ route('stok-obat.index', array_merge(request()->query(), ['sort' => 'periode', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center justify-between group hover:text-purple-300 transition-colors">
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
                            <div class="text-xs text-purple-200 mt-1">Terbaru dulu</div>
                        </th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">
                            <a href="{{ route('stok-obat.index', array_merge(request()->query(), ['sort' => 'stok_awal', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center justify-center group hover:text-purple-300 transition-colors">
                                <span>Stok Awal</span>
                                <span class="ml-2">
                                    @if(request('sort') == 'stok_awal')
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
                            <a href="{{ route('stok-obat.index', array_merge(request()->query(), ['sort' => 'stok_masuk', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center justify-center group hover:text-purple-300 transition-colors">
                                <span>Stok Masuk</span>
                                <span class="ml-2">
                                    @if(request('sort') == 'stok_masuk')
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
                            <a href="{{ route('stok-obat.index', array_merge(request()->query(), ['sort' => 'stok_pakai', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center justify-center group hover:text-purple-300 transition-colors">
                                <span>Stok Pakai</span>
                                <span class="ml-2">
                                    @if(request('sort') == 'stok_pakai')
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
                            <a href="{{ route('stok-obat.index', array_merge(request()->query(), ['sort' => 'stok_akhir', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center justify-center group hover:text-purple-300 transition-colors">
                                <span>Stok Akhir</span>
                                <span class="ml-2">
                                    @if(request('sort') == 'stok_akhir')
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
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Keterangan</th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stokObats as $index => $stok)
                        <tr class="hover:bg-purple-50 transition-colors {{ $stok->stok_akhir <= 0 ? 'bg-red-50' : ($stok->stok_akhir <= 10 ? 'bg-yellow-50' : '') }}">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="selected_ids[]" value="{{ $stok->id_stok_obat }}" class="row-checkbox rounded border-gray-300 text-purple-600 focus:ring-2 focus:ring-purple-500" {{ $stok->is_initial_stok ? 'disabled' : '' }}>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ ($stokObats->currentPage() - 1) * $stokObats->perPage() + $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900">{{ $stok->obat->nama_obat }}</div>
                                @if($stok->obat->keterangan)
                                    <div class="text-xs text-gray-500 truncate max-w-xs" title="{{ $stok->obat->keterangan }}">{{ Str::limit($stok->obat->keterangan, 50) }}</div>
                                @endif
                                @if($stok->is_initial_stok)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                        Stok Awal
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $stok->obat->satuanObat->nama_satuan ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $stok->periode }}
                                </span>
                                <div class="text-xs text-gray-500 mt-1">{{ $stok->periode_format }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-center">{{ number_format($stok->stok_awal) }}</td>
                            <td class="px-4 py-3 text-sm {{ $stok->stok_masuk > 0 ? 'text-green-600' : 'text-gray-900' }} text-center">{{ number_format($stok->stok_masuk) }}</td>
                            <td class="px-4 py-3 text-sm {{ $stok->stok_pakai > 0 ? 'text-red-600' : 'text-gray-900' }} text-center">{{ number_format($stok->stok_pakai) }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-center {{ $stok->stok_akhir <= 0 ? 'text-red-600' : ($stok->stok_akhir <= 10 ? 'text-yellow-600' : 'text-green-600') }}">
                                {{ number_format($stok->stok_akhir) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                @if($stok->keterangan)
                                    <span class="truncate inline-block max-w-xs" title="{{ $stok->keterangan }}">{{ Str::limit($stok->keterangan, 30) }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-center">
                                <a href="{{ route('stok-obat.edit', $stok->id_stok_obat) }}" class="inline-flex items-center justify-center w-9 h-9 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all shadow-sm hover:shadow-md mr-1" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                @if(!$stok->is_initial_stok)
                                    <button onclick="deleteStok({{ $stok->id_stok_obat }}, '{{ $stok->obat->nama_obat }} - {{ $stok->periode }}')" class="inline-flex items-center justify-center w-9 h-9 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all shadow-sm hover:shadow-md" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-8 text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="text-lg font-medium">Tidak ada data stok obat</p>
                                <p class="text-sm mt-1">Tambah stok obat untuk memulai</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($stokObats->count() > 0)
                    <tfoot class="bg-gray-50">
                        <tr>
                            <th colspan="5" class="px-4 py-3 text-right text-sm font-medium text-gray-700">Total</th>
                            <th class="px-4 py-3 text-center text-sm font-bold text-gray-900">{{ number_format($stokObats->sum('stok_awal')) }}</th>
                            <th class="px-4 py-3 text-center text-sm font-bold text-green-600">{{ number_format($stokObats->sum('stok_masuk')) }}</th>
                            <th class="px-4 py-3 text-center text-sm font-bold text-red-600">{{ number_format($stokObats->sum('stok_pakai')) }}</th>
                            <th class="px-4 py-3 text-center text-sm font-bold text-gray-900">{{ number_format($stokObats->sum('stok_akhir')) }}</th>
                            <th colspan="2" class="px-4 py-3"></th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <!-- Custom Pagination -->
        @if($stokObats->hasPages())
        <div class="px-6 py-5 border-t border-gray-200 bg-white">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-600">
                    Halaman <span class="font-semibold text-gray-900">{{ $stokObats->currentPage() }}</span>
                    dari <span class="font-semibold text-gray-900">{{ $stokObats->lastPage() }}</span>
                    <span class="mx-2 text-gray-400">•</span>
                    Total <span class="font-semibold text-gray-900">{{ $stokObats->total() }}</span> data
                </div>

                <nav class="flex items-center gap-2" role="navigation">
                    @if($stokObats->onFirstPage())
                        <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $stokObats->appends(request()->except('page'))->url(1) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    @if($stokObats->onFirstPage())
                        <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>
                    @else
                        <a href="{{ $stokObats->appends(request()->except('page'))->previousPageUrl() }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">Previous</a>
                    @endif

                    <div class="flex items-center gap-1">
                        @php
                            $start = max($stokObats->currentPage() - 2, 1);
                            $end = min($stokObats->currentPage() + 2, $stokObats->lastPage());
                        @endphp

                        @if($start > 1)
                            <a href="{{ $stokObats->appends(request()->except('page'))->url(1) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">1</a>
                            @if($start > 2)
                                <span class="px-2 text-gray-500">...</span>
                            @endif
                        @endif

                        @for($i = $start; $i <= $end; $i++)
                            @if($i == $stokObats->currentPage())
                                <span class="px-3 py-2 text-sm font-bold text-white bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg shadow-md">{{ $i }}</span>
                            @else
                                <a href="{{ $stokObats->appends(request()->except('page'))->url($i) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">{{ $i }}</a>
                            @endif
                        @endfor

                        @if($end < $stokObats->lastPage())
                            @if($end < $stokObats->lastPage() - 1)
                                <span class="px-2 text-gray-500">...</span>
                            @endif
                            <a href="{{ $stokObats->appends(request()->except('page'))->url($stokObats->lastPage()) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">{{ $stokObats->lastPage() }}</a>
                        @endif
                    </div>

                    @if($stokObats->hasMorePages())
                        <a href="{{ $stokObats->appends(request()->except('page'))->nextPageUrl() }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">Next</a>
                    @else
                        <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>
                    @endif

                    @if($stokObats->currentPage() == $stokObats->lastPage())
                        <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $stokObats->appends(request()->except('page'))->url($stokObats->lastPage()) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endif
                </nav>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function toggleAll(checkbox) {
    const checkboxes = document.querySelectorAll('.row-checkbox:not([disabled])');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.row-checkbox:checked');
    document.getElementById('selectedCount').textContent = checkboxes.length + ' data dipilih';
}

// Update count when individual checkboxes are changed
document.querySelectorAll('.row-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedCount);
});

function deleteStok(id, nama) {
    Swal.fire({
        title: 'Hapus Data Stok Obat?',
        html: `Apakah Anda yakin ingin menghapus stok <strong>${nama}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/stok-obat/${id}`, {
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
                } else {
                    Swal.fire('Error!', data.message, 'error');
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
            confirmButtonColor: '#7c3aed'
        });
        return;
    }

    Swal.fire({
        title: 'Hapus Data Terpilih?',
        html: `Apakah Anda yakin ingin menghapus <strong>${ids.length}</strong> data stok obat yang dipilih?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus Semua!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/stok-obat/bulk-delete', {
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
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data', 'error');
            });
        }
    });
}

function generateStokAwal() {
    Swal.fire({
        title: 'Generate Stok Awal Periode Baru',
        html: `
            <div class="text-left">
                <p class="mb-4">Masukkan periode baru (format MM-YY) untuk membuat stok awal semua obat:</p>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Periode Baru</label>
                    <input id="newPeriode" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" placeholder="Contoh: 10-25" maxlength="5">
                    <p class="text-xs text-gray-500 mt-1">Format: MM-YY (contoh: 10-25 untuk Oktober 2025)</p>
                </div>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#0d9488',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Generate Stok Awal',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        preConfirm: () => {
            const periode = document.getElementById('newPeriode').value;

            if (!periode) {
                Swal.showValidationMessage('Periode wajib diisi');
                return false;
            }

            if (!/^\d{2}-\d{2}$/.test(periode)) {
                Swal.showValidationMessage('Format periode harus MM-YY (contoh: 10-25)');
                return false;
            }

            return periode;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/stok-obat/generate-stok-awal';

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Add periode
            const periodeInput = document.createElement('input');
            periodeInput.type = 'hidden';
            periodeInput.name = 'periode';
            periodeInput.value = result.value;
            form.appendChild(periodeInput);

            document.body.appendChild(form);
            form.submit();
        }
    });
}

function updateStokPakai() {
    Swal.fire({
        title: 'Update Stok Pakai Otomatis',
        html: `
            <div class="text-left">
                <p class="mb-4">Masukkan periode (format MM-YY) untuk mengupdate stok pakai otomatis dari data keluhan:</p>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                    <input id="periode" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Contoh: 10-25" maxlength="5">
                    <p class="text-xs text-gray-500 mt-1">Format: MM-YY (contoh: 10-25 untuk Oktober 2025)</p>
                </div>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#ea580c',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Update Stok Pakai',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        preConfirm: () => {
            const periode = document.getElementById('periode').value;

            if (!periode) {
                Swal.showValidationMessage('Periode wajib diisi');
                return false;
            }

            if (!/^\d{2}-\d{2}$/.test(periode)) {
                Swal.showValidationMessage('Format periode harus MM-YY (contoh: 10-25)');
                return false;
            }

            return periode;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/stok-obat/update-stok-pakai';

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Add periode
            const periodeInput = document.createElement('input');
            periodeInput.type = 'hidden';
            periodeInput.name = 'periode';
            periodeInput.value = result.value;
            form.appendChild(periodeInput);

            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Auto submit filter on periode change
document.addEventListener('DOMContentLoaded', function() {
    const periodeSelect = document.querySelector('select[name="periode"]');
    if (periodeSelect) {
        periodeSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
});
</script>
@endsection