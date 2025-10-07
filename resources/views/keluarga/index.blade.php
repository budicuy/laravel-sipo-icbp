@extends('layouts.app')

@section('page-title', 'Data Keluarga')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            Data Keluarga Karyawan
        </h1>
        <p class="text-gray-600 mt-2 ml-1">Manajemen data keluarga karyawan perusahaan</p>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Action Buttons Section -->
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-purple-50">
            <div class="flex flex-wrap gap-3 items-center">
                <a href="{{ route('keluarga.create') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Data Keluarga
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
        <div class="p-6 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <h3 class="text-sm font-semibold text-gray-800">Filter & Pencarian</h3>
            </div>

            <form method="GET" class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari berdasarkan NIK Karyawan, Nama Karyawan, Nama Keluarga, atau No KTP</label>
                    <div class="flex gap-2">
                        <input type="text" name="q" value="{{ request('q') }}" class="flex-1 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm" placeholder="Masukkan kata kunci pencarian...">
                        <button class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('keluarga.index') }}" class="px-5 py-2.5 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table Controls -->
        <div class="p-5 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
            <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-gray-700">Tampilkan</label>
                <form method="GET" id="perPageForm" class="inline">
                    @if(request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif
                    <select name="per_page" onchange="document.getElementById('perPageForm').submit()" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm bg-white shadow-sm">
                        <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                        <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                    </select>
                </form>
                <span class="text-sm font-medium text-gray-700">data per halaman</span>
            </div>
            <div class="text-sm text-gray-600">
                Total: <span class="font-semibold text-gray-900">{{ $keluargas->total() }}</span> data keluarga
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gradient-to-r from-purple-800 to-pink-800">
                        <th class="px-4 py-4 text-left">
                            <input type="checkbox" onclick="toggleAll(this)" class="rounded border-gray-400 text-purple-600 focus:ring-2 focus:ring-purple-500">
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">No</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            <a href="{{ route('keluarga.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'id_karyawan', 'direction' => (request('sort') == 'id_karyawan' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" 
                               class="flex items-center justify-between group hover:text-purple-300 transition-colors">
                                <span>NIK Karyawan</span>
                                <span class="ml-2">
                                    @if(request('sort') == 'id_karyawan')
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
                            <a href="{{ route('keluarga.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'nama_keluarga', 'direction' => (request('sort') == 'nama_keluarga' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" 
                               class="flex items-center justify-between group hover:text-purple-300 transition-colors">
                                <span>Nama Keluarga</span>
                                <span class="ml-2">
                                    @if(request('sort') == 'nama_keluarga')
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
                            <a href="{{ route('keluarga.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'kode_hubungan', 'direction' => (request('sort') == 'kode_hubungan' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" 
                               class="flex items-center justify-between group hover:text-purple-300 transition-colors">
                                <span>Hubungan</span>
                                <span class="ml-2">
                                    @if(request('sort') == 'kode_hubungan')
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
                            <a href="{{ route('keluarga.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'jenis_kelamin', 'direction' => (request('sort') == 'jenis_kelamin' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" 
                               class="flex items-center justify-between group hover:text-purple-300 transition-colors">
                                <span>JK</span>
                                <span class="ml-2">
                                    @if(request('sort') == 'jenis_kelamin')
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
                            <a href="{{ route('keluarga.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'tanggal_lahir', 'direction' => (request('sort') == 'tanggal_lahir' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" 
                               class="flex items-center justify-between group hover:text-purple-300 transition-colors">
                                <span>Tanggal Lahir</span>
                                <span class="ml-2">
                                    @if(request('sort') == 'tanggal_lahir')
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
                            <a href="{{ route('keluarga.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'alamat', 'direction' => (request('sort') == 'alamat' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" 
                               class="flex items-center justify-between group hover:text-purple-300 transition-colors">
                                <span>Alamat</span>
                                <span class="ml-2">
                                    @if(request('sort') == 'alamat')
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
                        <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($keluargas as $keluarga)
                    <tr class="hover:bg-purple-50 transition-colors">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <input name="ids[]" value="{{ $keluarga->id_keluarga }}" type="checkbox" class="row-checkbox rounded border-gray-300 text-purple-600 focus:ring-2 focus:ring-purple-500">
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ ($keluargas->currentPage() - 1) * $keluargas->perPage() + $loop->iteration }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                <div class="font-semibold text-purple-600">{{ optional($keluarga->karyawan)->nik_karyawan }}</div>
                                <div class="text-xs text-gray-500">{{ optional($keluarga->karyawan)->nama_karyawan }}</div>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">{{ $keluarga->nama_keluarga }}</span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ optional($keluarga->hubungan)->hubungan }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-pink-100 text-pink-800">
                                {{ $keluarga->jenis_kelamin }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-1 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ optional($keluarga->tanggal_lahir)->format('d-m-Y') }}
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-900 max-w-xs truncate" title="{{ $keluarga->alamat }}">{{ $keluarga->alamat }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('keluarga.edit', $keluarga->id_keluarga) }}" class="inline-flex items-center justify-center w-9 h-9 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-all shadow-sm hover:shadow-md" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('keluarga.destroy', $keluarga->id_keluarga) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)" class="inline-flex items-center justify-center w-9 h-9 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all shadow-sm hover:shadow-md" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada data keluarga</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Custom Pagination -->
        @isset($keluargas)
        @if($keluargas->hasPages())
        <div class="px-6 py-5 border-t border-gray-200 bg-white">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-600">
                    Halaman <span class="font-semibold text-gray-900">{{ $keluargas->currentPage() }}</span>
                    dari <span class="font-semibold text-gray-900">{{ $keluargas->lastPage() }}</span>
                    <span class="mx-2 text-gray-400">•</span>
                    Total <span class="font-semibold text-gray-900">{{ $keluargas->total() }}</span> data
                </div>

                <nav class="flex items-center gap-2" role="navigation">
                    @if($keluargas->onFirstPage())
                        <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $keluargas->appends(request()->except('page'))->url(1) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    @if($keluargas->onFirstPage())
                        <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>
                    @else
                        <a href="{{ $keluargas->appends(request()->except('page'))->previousPageUrl() }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">Previous</a>
                    @endif

                    <div class="flex items-center gap-1">
                        @php
                            $start = max($keluargas->currentPage() - 2, 1);
                            $end = min($keluargas->currentPage() + 2, $keluargas->lastPage());
                        @endphp

                        @if($start > 1)
                            <a href="{{ $keluargas->appends(request()->except('page'))->url(1) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">1</a>
                            @if($start > 2)
                                <span class="px-2 text-gray-500">...</span>
                            @endif
                        @endif

                        @for($i = $start; $i <= $end; $i++)
                            @if($i == $keluargas->currentPage())
                                <span class="px-3 py-2 text-sm font-bold text-white bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg shadow-md">{{ $i }}</span>
                            @else
                                <a href="{{ $keluargas->appends(request()->except('page'))->url($i) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">{{ $i }}</a>
                            @endif
                        @endfor

                        @if($end < $keluargas->lastPage())
                            @if($end < $keluargas->lastPage() - 1)
                                <span class="px-2 text-gray-500">...</span>
                            @endif
                            <a href="{{ $keluargas->appends(request()->except('page'))->url($keluargas->lastPage()) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">{{ $keluargas->lastPage() }}</a>
                        @endif
                    </div>

                    @if($keluargas->hasMorePages())
                        <a href="{{ $keluargas->appends(request()->except('page'))->nextPageUrl() }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">Next</a>
                    @else
                        <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>
                    @endif

                    @if($keluargas->currentPage() == $keluargas->lastPage())
                        <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $keluargas->appends(request()->except('page'))->url($keluargas->lastPage()) }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">
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

@push('scripts')
<script>
function toggleAll(source) {
  const checkboxes = document.querySelectorAll('.row-checkbox');
  checkboxes.forEach(cb => cb.checked = source.checked);
}

function confirmDelete(button) {
  Swal.fire({
    title: 'Hapus Data Keluarga?',
    text: "Data yang dihapus tidak dapat dikembalikan!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Ya, Hapus!',
    cancelButtonText: 'Batal',
    reverseButtons: true,
    customClass: {
      confirmButton: 'px-5 py-2.5 rounded-lg font-medium',
      cancelButton: 'px-5 py-2.5 rounded-lg font-medium'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      button.closest('form').submit();
    }
  });
}

function submitBulkDelete() {
  const ids = getSelectedIds();
  if (ids.length === 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Tidak Ada Data Terpilih',
      text: 'Pilih minimal satu data keluarga untuk dihapus',
      confirmButtonColor: '#3b82f6',
      confirmButtonText: 'OK'
    });
    return;
  }

  Swal.fire({
    title: 'Hapus ' + ids.length + ' Data Keluarga?',
    text: "Data yang dihapus tidak dapat dikembalikan!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Ya, Hapus Semua!',
    cancelButtonText: 'Batal',
    reverseButtons: true,
    customClass: {
      confirmButton: 'px-5 py-2.5 rounded-lg font-medium',
      cancelButton: 'px-5 py-2.5 rounded-lg font-medium'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '{{ route("keluarga.bulkDelete") }}';

      const csrfInput = document.createElement('input');
      csrfInput.type = 'hidden';
      csrfInput.name = '_token';
      csrfInput.value = '{{ csrf_token() }}';
      form.appendChild(csrfInput);

      ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        form.appendChild(input);
      });

      document.body.appendChild(form);
      form.submit();
    }
  });
}

function getSelectedIds() {
  const nodes = Array.from(document.querySelectorAll('.row-checkbox:checked'));
  return nodes.map(n => n.value);
}
</script>
@endpush
@endsection
