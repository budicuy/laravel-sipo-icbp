@extends('layouts.app')

@section('page-title', 'Daftar Kunjungan')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
                <div class="bg-gradient-to-r from-orange-500 to-red-600 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Daftar Kunjungan</h1>
                    <p class="text-gray-600 mt-1">Kelola data kunjungan pasien</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 mb-6">
        <form action="{{ route('kunjungan.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Dari Tanggal -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Dari Tanggal</label>
                    <div class="relative">
                        <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Sampai Tanggal -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Sampai Tanggal</label>
                    <div class="relative">
                        <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Cari Data</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, NIK, atau No RM..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex items-end gap-3">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-medium px-6 py-2.5 rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('kunjungan.index') }}" class="flex-1 bg-white hover:bg-gray-50 border-2 border-gray-300 text-gray-700 font-medium px-6 py-2.5 rounded-lg transition-all text-center">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Table Header -->
        <div class="bg-gradient-to-r from-orange-500 to-red-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Data Kunjungan Pasien
                </h2>
                <form action="{{ route('kunjungan.index') }}" method="GET" class="flex items-center gap-2">
                    <label class="text-white text-sm">Show</label>
                    <select name="per_page" onchange="this.form.submit()" class="px-3 py-1.5 bg-white border border-white rounded-lg text-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-white">
                        <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                    </select>
                    <span class="text-white text-sm">entries</span>
                </form>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700 cursor-pointer hover:bg-gray-700" onclick="sortTable(0)">
                            No
                            <span class="ml-1">
                                <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700 cursor-pointer hover:bg-gray-700" onclick="sortTable(1)">
                            No RM
                            <span class="ml-1">
                                <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700 cursor-pointer hover:bg-gray-700" onclick="sortTable(2)">
                            Nama Pasien
                            <span class="ml-1">
                                <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700 cursor-pointer hover:bg-gray-700" onclick="sortTable(3)">
                            Hubungan
                            <span class="ml-1">
                                <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700 cursor-pointer hover:bg-gray-700" onclick="sortTable(4)">
                            Total Kunjungan
                            <span class="ml-1">
                                <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        // Group kunjungan by No RM untuk menampilkan setiap pasien sekali saja
                        $groupedKunjungans = [];
                        $currentYear = date('Y');
                        
                        // Gunakan semua data kunjungan, bukan hanya yang di-paginate
                        foreach($allKunjungans as $kunjungan) {
                            $noRM = $kunjungan->no_rm;
                            // Ensure NO RM is a string for consistent array key handling
                            $noRMKey = (string)$noRM;
                            if(!isset($groupedKunjungans[$noRMKey])) {
                                $groupedKunjungans[$noRMKey] = [
                                    'no_rm' => $noRM,
                                    'nama_pasien' => $kunjungan->nama_pasien,
                                    'hubungan' => $kunjungan->hubungan,
                                    'kunjungans' => [],
                                    'latest_visit' => $kunjungan // Store latest visit for action button
                                ];
                            }
                            $groupedKunjungans[$noRMKey]['kunjungans'][] = $kunjungan;
                            // Update latest visit if current visit is newer
                            if($kunjungan->tanggal_kunjungan > $groupedKunjungans[$noRMKey]['latest_visit']->tanggal_kunjungan) {
                                $groupedKunjungans[$noRMKey]['latest_visit'] = $kunjungan;
                            }
                        }
                        
                        // Convert to array and sort by No RM to avoid issues with uksort on associative arrays
                        $groupedArray = array_values($groupedKunjungans);
                        usort($groupedArray, function($a, $b) {
                            return strcmp($a['no_rm'], $b['no_rm']);
                        });
                    @endphp
                    
                    @forelse($groupedArray as $index => $patientGroup)
                        <tr class="hover:bg-orange-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                {{ $kunjunganCollection->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200 font-medium">
                                {{ $patientGroup['no_rm'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200 font-medium">
                                {{ $patientGroup['nama_pasien'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                    {{ $patientGroup['hubungan'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                    {{ count($patientGroup['kunjungans']) }} kunjungan
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="{{ route('kunjungan.detail', $patientGroup['latest_visit']->id_kunjungan) }}" class="bg-gradient-to-r
                                    @if($patientGroup['latest_visit']->tipe == 'emergency') from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600
                                    @else from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600
                                    @endif text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:shadow-lg transition-all inline-block">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-lg font-medium">Tidak ada data kunjungan</p>
                            <p class="text-sm mt-1">Silakan tambah data rekam medis untuk membuat kunjungan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Custom Pagination -->
        @if($kunjunganCollection->hasPages())
        <div class="px-6 py-5 border-t border-gray-200 bg-white">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-600">
                    Halaman <span class="font-semibold text-gray-900">{{ $kunjunganCollection->currentPage() }}</span>
                    dari <span class="font-semibold text-gray-900">{{ $kunjunganCollection->lastPage() }}</span>
                    <span class="mx-2 text-gray-400">•</span>
                    Total <span class="font-semibold text-gray-900">{{ $kunjunganCollection->total() }}</span> data
                </div>

                <nav class="flex items-center gap-2" role="navigation">
                    @if($kunjunganCollection->onFirstPage())
                        <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $kunjunganCollection->appends(request()->except('page'))->url(1) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-orange-50 hover:border-orange-400 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    @if($kunjunganCollection->onFirstPage())
                        <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>
                    @else
                        <a href="{{ $kunjunganCollection->appends(request()->except('page'))->previousPageUrl() }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-orange-50 hover:border-orange-400 transition-all">Previous</a>
                    @endif

                    <div class="flex items-center gap-1">
                        @php
                            $start = max($kunjunganCollection->currentPage() - 2, 1);
                            $end = min($kunjunganCollection->currentPage() + 2, $kunjunganCollection->lastPage());
                        @endphp

                        @if($start > 1)
                            <a href="{{ $kunjunganCollection->appends(request()->except('page'))->url(1) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-orange-50 hover:border-orange-400 transition-all">1</a>
                            @if($start > 2)
                                <span class="px-2 text-gray-500">...</span>
                            @endif
                        @endif

                        @for($i = $start; $i <= $end; $i++)
                            @if($i == $kunjunganCollection->currentPage())
                                <span class="px-3 py-2 text-sm font-bold text-white bg-gradient-to-r from-orange-500 to-red-600 rounded-lg shadow-md">{{ $i }}</span>
                            @else
                                <a href="{{ $kunjunganCollection->appends(request()->except('page'))->url($i) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-orange-50 hover:border-orange-400 transition-all">{{ $i }}</a>
                            @endif
                        @endfor

                        @if($end < $kunjunganCollection->lastPage())
                            @if($end < $kunjunganCollection->lastPage() - 1)
                                <span class="px-2 text-gray-500">...</span>
                            @endif
                            <a href="{{ $kunjunganCollection->appends(request()->except('page'))->url($kunjunganCollection->lastPage()) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-orange-50 hover:border-orange-400 transition-all">{{ $kunjunganCollection->lastPage() }}</a>
                        @endif
                    </div>

                    @if($kunjunganCollection->hasMorePages())
                        <a href="{{ $kunjunganCollection->appends(request()->except('page'))->nextPageUrl() }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-orange-50 hover:border-orange-400 transition-all">Next</a>
                    @else
                        <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>
                    @endif

                    @if($kunjunganCollection->currentPage() == $kunjunganCollection->lastPage())
                        <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $kunjunganCollection->appends(request()->except('page'))->url($kunjunganCollection->lastPage()) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-orange-50 hover:border-orange-400 transition-all">
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
    let sortDirection = {};
    let originalData = [];
    
    // Store original data when page loads
    document.addEventListener('DOMContentLoaded', function() {
        const tbody = document.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.forEach(row => {
            if (row.cells.length >= 5) { // Skip empty rows
                originalData.push({
                    no: row.cells[0].textContent.trim(),
                    noRM: row.cells[1].textContent.trim(),
                    namaPasien: row.cells[2].textContent.trim(),
                    hubungan: row.cells[3].textContent.trim(),
                    totalKunjungan: row.cells[4].textContent.trim(),
                    element: row
                });
            }
        });
    });
    
    function sortTable(columnIndex) {
        const tbody = document.querySelector('tbody');
        
        // Toggle sort direction
        sortDirection[columnIndex] = sortDirection[columnIndex] === 'asc' ? 'desc' : 'asc';
        
        // Sort data
        originalData.sort((a, b) => {
            let aValue, bValue;
            let comparison = 0;
            
            switch(columnIndex) {
                case 0: // No column
                    aValue = parseInt(a.no) || 0;
                    bValue = parseInt(b.no) || 0;
                    comparison = aValue - bValue;
                    break;
                case 1: // No RM column
                    aValue = a.noRM;
                    bValue = b.noRM;
                    comparison = aValue.localeCompare(bValue);
                    break;
                case 2: // Nama Pasien column
                    aValue = a.namaPasien;
                    bValue = b.namaPasien;
                    comparison = aValue.localeCompare(bValue);
                    break;
                case 3: // Hubungan column
                    aValue = a.hubungan;
                    bValue = b.hubungan;
                    comparison = aValue.localeCompare(bValue);
                    break;
                case 4: // Total Kunjungan column
                    aValue = parseInt(a.totalKunjungan) || 0;
                    bValue = parseInt(b.totalKunjungan) || 0;
                    comparison = aValue - bValue;
                    break;
            }
            
            return sortDirection[columnIndex] === 'asc' ? comparison : -comparison;
        });
        
        // Clear and re-append sorted rows
        tbody.innerHTML = '';
        originalData.forEach(item => {
            tbody.appendChild(item.element);
        });
        
        // Update sort icons
        updateSortIcons(columnIndex);
    }
    
    function updateSortIcons(activeColumn) {
        const headers = document.querySelectorAll('th');
        headers.forEach((header, index) => {
            const icon = header.querySelector('svg');
            if (icon && index !== 5) { // Don't update action column
                if (index === activeColumn) {
                    if (sortDirection[index] === 'asc') {
                        icon.innerHTML = '<path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />';
                    } else {
                        icon.innerHTML = '<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />';
                    }
                } else {
                    icon.innerHTML = '<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />';
                }
            }
        });
    }
</script>
@endsection
