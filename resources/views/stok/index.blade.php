@extends('layouts.app')

@section('title', 'Manajemen Stok Obat')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <div class="bg-linear-to-r from-blue-600 to-cyan-600 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>
            Manajemen Stok Obat
        </h1>
        <p class="text-gray-600 mt-2 ml-1">Monitor dan kelola stok obat di klinik</p>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Action Buttons Section -->
        <div class="p-6 border-b border-gray-200 bg-linear-to-r from-gray-50 to-blue-50">
            <div class="flex flex-wrap gap-3 items-center">
                <a href="{{ route('obat.create') }}"
                    class="inline-flex items-center px-5 py-2.5 bg-linear-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Obat Baru
                </a>

                @if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Super Admin')
                <button type="button" onclick="openImportModal()"
                    class="inline-flex items-center px-5 py-2.5 bg-linear-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Import Stok Masuk
                </button>

                <button type="button" onclick="openImportPakaiModal()"
                    class="inline-flex items-center px-5 py-2.5 bg-linear-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                    </svg>
                    Import Stok Pakai
                </button>
                @endif

                @if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Super Admin')
                <button type="button" onclick="openExportStockOpnameModal()"
                    class="inline-flex items-center px-5 py-2.5 bg-linear-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Stock Opname
                </button>
                @endif

                <button type="button" id="refreshBtn"
                    class="inline-flex items-center px-5 py-2.5 bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
            </div>
        </div>

        @if ($obatsWithStok->count() > 0)
        <div class="px-4 py-2 bg-linear-to-r from-blue-50 to-cyan-50 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v8m5-4h.01M9 16h.01" />
                </svg>
                Ringkasan Stok Obat
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">{{ $obatsWithStok->count() }}</div>
                            <div class="text-sm text-gray-600">Total Obat</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center">
                        <div class="p-3 bg-red-100 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-red-600">
                                {{ $obatsWithStok->where('sisa_stok', '<=', 0)->count() }}</div>
                            <div class="text-sm text-gray-600">Stok Habis</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-yellow-600">
                                {{ $obatsWithStok->where('sisa_stok', '>', 0)->where('sisa_stok', '<=', 10)->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Stok Rendah</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Filter Section -->
        <div class="p-6 bg-linear-to-r from-blue-50 to-cyan-50 border-b border-gray-200">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <h3 class="text-sm font-semibold text-gray-800">Filter & Pencarian</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Nama Obat</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="searchInput" value="{{ request('search') }}"
                            class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                            placeholder="Masukkan nama obat...">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Stok</label>
                    <select id="stokStatusFilter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-white shadow-sm">
                        <option value="">Semua Status</option>
                        <option value="habis" {{ request('stok_status')=='habis' ? 'selected' : '' }}>Stok Habis (≤
                            0)</option>
                        <option value="rendah" {{ request('stok_status')=='rendah' ? 'selected' : '' }}>Stok Rendah
                            (1-10)</option>
                        <option value="warning" {{ request('stok_status')=='warning' ? 'selected' : '' }}>Warning Stok (≤ 10)</option>
                        <option value="tersedia" {{ request('stok_status')=='tersedia' ? 'selected' : '' }}>Stok
                            Tersedia (> 10)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                    <select id="sortBy"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-white shadow-sm">
                        <option value="nama_obat" {{ request('sort')=='nama_obat' ? 'selected' : '' }}>Urutkan: Nama
                            Obat</option>
                        <option value="sisa_stok" {{ request('sort')=='sisa_stok' ? 'selected' : '' }}>Urutkan: Sisa
                            Stok</option>
                    </select>
                </div>

            </div>
        </div>
        <!-- Tabel Stok Obat -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-linear-to-r from-blue-700 to-cyan-700">
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">No</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            <a href="{{ route('stok.index', array_merge(request()->query(), ['sort' => 'nama_obat', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
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
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Satuan
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Stok Awal
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            <a href="{{ route('stok.index', array_merge(request()->query(), ['sort' => 'sisa_stok', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
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
                    <tr class="stok-row hover:bg-blue-50 transition-colors"
                        data-obat-name="{{ strtolower($obat->nama_obat) }}"
                        data-stok-status="{{ $obat->sisa_stok <= 0 ? 'habis' : ($obat->sisa_stok <= 10 ? 'rendah' : 'tersedia') }}">
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-8 h-8 bg-linear-to-br from-blue-500 to-cyan-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($obat->nama_obat, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $obat->nama_obat }}</div>
                                    @if ($obat->keterangan)
                                    <div class="text-xs text-gray-500">{{ Str::limit($obat->keterangan, 30) }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $obat->satuanObat->nama_satuan ?? '-' }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ number_format($obat->stok_awal) }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span
                                class="text-sm font-bold {{ $obat->sisa_stok <= 0 ? 'text-red-600' : ($obat->sisa_stok <= 10 ? 'text-yellow-600' : 'text-green-600') }}">
                                {{ number_format($obat->sisa_stok) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @if ($obat->sisa_stok <= 0) <span
                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Habis</span>
                                @elseif($obat->sisa_stok <= 10) <span
                                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Rendah</span>
                                    @else
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Tersedia</span>
                                    @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('stok.show', $obat->id_obat) }}"
                                    class="inline-flex items-center justify-center w-9 h-9 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all shadow-sm hover:shadow-md"
                                    title="Detail Stok">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                <p class="text-sm">Tidak ada data obat yang ditemukan</p>
                                <a href="{{ route('obat.create') }}"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-all">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
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
</div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
            // Fungsi untuk filter dan search
            function filterObats() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase();
                const statusFilter = document.getElementById('stokStatusFilter').value;

                const stokRows = document.querySelectorAll('.stok-row');

                // Filter table rows
                stokRows.forEach(function(row) {
                    const obatName = row.dataset.obatName;
                    const stokStatus = row.dataset.stokStatus;

                    const matchesSearch = obatName.includes(searchTerm);
                    const matchesStatus = !statusFilter || stokStatus === statusFilter;

                    if (matchesSearch && matchesStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Event listeners
            document.getElementById('searchInput').addEventListener('keyup', filterObats);
            document.getElementById('stokStatusFilter').addEventListener('change', filterObats);

            // Sort functionality
            document.getElementById('sortBy').addEventListener('change', function() {
                const sortBy = this.value;
                const currentUrl = new URL(window.location);
                currentUrl.searchParams.set('sort', sortBy);
                window.location.href = currentUrl.toString();
            });

            // Refresh button
            document.getElementById('refreshBtn').addEventListener('click', function() {
                const icon = this.querySelector('svg');
                icon.classList.add('animate-spin');
                window.location.reload();
            });

            // Auto-refresh setiap 60 detik untuk update stok real-time
            setInterval(function() {
                const refreshBtn = document.getElementById('refreshBtn');
                const icon = refreshBtn.querySelector('svg');
                icon.classList.add('animate-spin');
                window.location.reload();
            }, 60000);
        });

        // Import Stok Modal Functions
        function openImportModal() {
            Swal.fire({
                title: 'Import Stok Obat dari Excel',
                html: `
            <div class="text-left">
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="font-semibold text-blue-900 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Informasi Penting
                    </h4>
                    <ul class="text-sm text-blue-800 space-y-1 ml-7">
                        <li>• Format file: Excel (.xlsx)</li>
                        <li>• Maksimal ukuran file: 5MB</li>
                        <li>• Download template terlebih dahulu</li>
                        <li>• Format: Nama Obat | MM-YYYY | MM-YYYY | MM-YYYY</li>
                        <li>• Contoh: Allopurinol | 50 | 0 | 42</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <a href="{{ route('stok.template') }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all w-full justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Template Excel
                    </a>
                </div>

                <form id="importForm" action="{{ route('stok.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel</label>
                        <input type="file"
                               name="file"
                               id="importFile"
                               accept=".xlsx"
                               required
                               class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2">
                        <p class="mt-1 text-xs text-gray-500">File Excel (.xlsx), maksimal 5MB</p>
                    </div>
                </form>
            </div>
        `,
                showCancelButton: true,
                confirmButtonText: 'Upload & Import',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#7c3aed',
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

                    const allowedExtensions = ['xlsx'];
                    const fileExtension = file.name.split('.').pop().toLowerCase();

                    if (!allowedExtensions.includes(fileExtension)) {
                        Swal.showValidationMessage('Format file harus .xlsx');
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
                        html: 'Mohon tunggu, data stok sedang diproses',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit via AJAX
                    fetch('{{ route('stok.import') }}', {
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
                                confirmButtonColor: '#7c3aed'
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
                                html: error.message || 'Terjadi kesalahan saat mengimport data stok',
                                confirmButtonColor: '#7c3aed'
                            });
                        });
                }
            });
        }

        // Import Stok Pakai Modal Functions
        function openImportPakaiModal() {
            Swal.fire({
                title: 'Import Stok Pakai Obat dari Excel',
                html: `
            <div class="text-left">
                <div class="mb-4 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                    <h4 class="font-semibold text-orange-900 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Informasi Penting
                    </h4>
                    <ul class="text-sm text-orange-800 space-y-1 ml-7">
                        <li>• Format file: Excel (.xlsx)</li>
                        <li>• Maksimal ukuran file: 5MB</li>
                        <li>• Download template terlebih dahulu</li>
                        <li>• Format: Nama Obat | MM-YYYY | MM-YYYY | MM-YYYY</li>
                        <li>• Contoh: Allopurinol | 25 | 15 | 30</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <a href="{{ route('stok.template-pakai') }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all w-full justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Template Excel
                    </a>
                </div>

                <form id="importPakaiForm" action="{{ route('stok.import-pakai') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel</label>
                        <input type="file"
                               name="file"
                               id="importPakaiFile"
                               accept=".xlsx"
                               required
                               class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2">
                        <p class="mt-1 text-xs text-gray-500">File Excel (.xlsx), maksimal 5MB</p>
                    </div>
                </form>
            </div>
        `,
                showCancelButton: true,
                confirmButtonText: 'Upload & Import',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ea580c',
                cancelButtonColor: '#6b7280',
                width: '600px',
                customClass: {
                    confirmButton: 'px-5 py-2.5 rounded-lg font-medium',
                    cancelButton: 'px-5 py-2.5 rounded-lg font-medium'
                },

                preConfirm: () => {
                    const fileInput = document.getElementById('importPakaiFile');
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

                    const allowedExtensions = ['xlsx'];
                    const fileExtension = file.name.split('.').pop().toLowerCase();

                    if (!allowedExtensions.includes(fileExtension)) {
                        Swal.showValidationMessage('Format file harus .xlsx');
                        return false;
                    }

                    return true;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const fileInput = document.getElementById('importPakaiFile');
                    const file = fileInput.files[0];

                    // Create FormData
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content'));

                    // Show loading
                    Swal.fire({
                        title: 'Sedang Mengimport...',
                        html: 'Mohon tunggu, data stok pakai sedang diproses',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit via AJAX
                    fetch('{{ route('stok.import-pakai') }}', {
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
                                confirmButtonColor: '#ea580c'
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
                                html: error.message ||
                                    'Terjadi kesalahan saat mengimport data stok pakai',
                                confirmButtonColor: '#ea580c'
                            });
                        });
                }
            });
        }

        // Export Stock Opname Modal Functions
        function openExportStockOpnameModal() {
            Swal.fire({
                title: 'Export Laporan Stock Opname',
                html: `
            <div class="text-left">
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <h4 class="font-semibold text-green-900 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Informasi Export
                    </h4>
                    <ul class="text-sm text-green-800 space-y-1 ml-7">
                        <li>• Format file: Excel (.xlsx)</li>
                        <li>• Struktur kolom: Nama Obat, Bulan dan Tahun, Stok Akhir</li>
                        <li>• Filter berdasarkan bulan dan tahun</li>
                        <li>• Default: bulan dan tahun saat ini</li>
                        <li>• Stok Akhir = Stok Awal + Total Stok Masuk - Total Stok Pakai</li>
                    </ul>
                </div>

                <form id="exportForm" action="{{ route('stok.export.stock-opname') }}" method="GET">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                        <select name="bulan" id="exportBulan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm bg-white shadow-sm">
                            <option value="">Pilih Bulan</option>
                            <option value="1" {{ now()->month == 1 ? 'selected' : '' }}>Januari</option>
                            <option value="2" {{ now()->month == 2 ? 'selected' : '' }}>Februari</option>
                            <option value="3" {{ now()->month == 3 ? 'selected' : '' }}>Maret</option>
                            <option value="4" {{ now()->month == 4 ? 'selected' : '' }}>April</option>
                            <option value="5" {{ now()->month == 5 ? 'selected' : '' }}>Mei</option>
                            <option value="6" {{ now()->month == 6 ? 'selected' : '' }}>Juni</option>
                            <option value="7" {{ now()->month == 7 ? 'selected' : '' }}>Juli</option>
                            <option value="8" {{ now()->month == 8 ? 'selected' : '' }}>Agustus</option>
                            <option value="9" {{ now()->month == 9 ? 'selected' : '' }}>September</option>
                            <option value="10" {{ now()->month == 10 ? 'selected' : '' }}>Oktober</option>
                            <option value="11" {{ now()->month == 11 ? 'selected' : '' }}>November</option>
                            <option value="12" {{ now()->month == 12 ? 'selected' : '' }}>Desember</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                        <select name="tahun" id="exportTahun" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm bg-white shadow-sm">
                            <option value="">Pilih Tahun</option>
                            @for($year = now()->year; $year >= now()->year - 5; $year--)
                                <option value="{{ $year }}" {{ now()->year == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                </form>
            </div>
        `,
                showCancelButton: true,
                confirmButtonText: 'Export Excel',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#059669',
                cancelButtonColor: '#6b7280',
                width: '500px',
                customClass: {
                    confirmButton: 'px-5 py-2.5 rounded-lg font-medium',
                    cancelButton: 'px-5 py-2.5 rounded-lg font-medium'
                },
                preConfirm: () => {
                    const bulan = document.getElementById('exportBulan').value;
                    const tahun = document.getElementById('exportTahun').value;

                    if (!bulan || !tahun) {
                        Swal.showValidationMessage('Silakan pilih bulan dan tahun');
                        return false;
                    }

                    // Submit form
                    document.getElementById('exportForm').submit();
                    return true;
                }
            });
        }
</script>
@endpush