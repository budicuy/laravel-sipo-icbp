@extends('layouts.app')

@section('page-title', 'Data Diagnosa Emergency')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Header Section -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <div class="bg-gradient-to-r from-red-600 to-pink-600 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                Data Diagnosa Emergency
            </h1>
            <p class="text-gray-600 mt-2 ml-1">Manajemen data diagnosis penyakit dan kondisi medis emergency</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <!-- Action Buttons Section -->
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-red-50">
                <div class="flex flex-wrap gap-3 items-center">
                    <a href="{{ route('diagnosa-emergency.create') }}"
                        class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Diagnosa Emergency
                    </a>

                    <a href="{{ route('diagnosa.index') }}"
                        class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Diagnosa Reguler
                    </a>

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
            <div class="p-6 bg-gradient-to-r from-red-50 to-pink-50 border-b border-gray-200">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-800">Filter & Pencarian</h3>
                </div>

                <form method="GET" action="{{ route('diagnosa-emergency.index') }}"
                    class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[300px]">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cari Nama Diagnosa Emergency</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-sm"
                                placeholder="Masukkan nama diagnosa emergency...">
                        </div>
                    </div>

                    <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('diagnosa-emergency.index') }}"
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
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700">Tampilkan</label>
                    <form method="GET" action="{{ route('diagnosa-emergency.index') }}" class="inline-flex">
                        @foreach (request()->except(['page', 'per_page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <select name="per_page" onchange="this.form.submit()"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-sm bg-white shadow-sm">
                            <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                            <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                        </select>
                    </form>
                </div>
                <div class="text-sm text-gray-600">
                    Total: <span class="font-semibold text-gray-900">{{ $diagnosaEmergencies->total() }}</span> diagnosa
                    emergency
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gradient-to-r from-red-700 to-pink-700">
                            @if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Super Admin')
                                <th class="px-4 py-4 text-left">
                                    <input type="checkbox" onclick="toggleAll(this)"
                                        class="rounded border-gray-400 text-red-600 focus:ring-2 focus:ring-red-500">
                                </th>
                            @endif
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">No</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                <a href="{{ route('diagnosa-emergency.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'nama_diagnosa_emergency', 'direction' => request('sort') == 'nama_diagnosa_emergency' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-red-300 transition-colors">
                                    <span>Nama Diagnosa Emergency</span>
                                    <span class="ml-2">
                                        @if (request('sort') == 'nama_diagnosa_emergency')
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
                                <a href="{{ route('diagnosa-emergency.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'deskripsi', 'direction' => request('sort') == 'deskripsi' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-red-300 transition-colors">
                                    <span>Deskripsi</span>
                                    <span class="ml-2">
                                        @if (request('sort') == 'deskripsi')
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
                                <a href="{{ route('diagnosa-emergency.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'status', 'direction' => request('sort') == 'status' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-red-300 transition-colors">
                                    <span>Status</span>
                                    <span class="ml-2">
                                        @if (request('sort') == 'status')
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
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Obat
                                Rekomendasi</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($diagnosaEmergencies as $index => $diagnosa)
                            <tr class="hover:bg-red-50 transition-colors">
                                @if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Super Admin')
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <input name="ids[]" value="{{ $diagnosa->id_diagnosa_emergency }}"
                                            type="checkbox"
                                            class="row-checkbox rounded border-gray-300 text-red-600 focus:ring-2 focus:ring-red-500">
                                    </td>
                                @endif
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ ($diagnosaEmergencies->currentPage() - 1) * $diagnosaEmergencies->perPage() + $index + 1 }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 bg-gradient-to-br from-red-500 to-pink-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                            {{ strtoupper(substr($diagnosa->nama_diagnosa_emergency, 0, 2)) }}
                                        </div>
                                        <span
                                            class="text-sm font-medium text-gray-900">{{ $diagnosa->nama_diagnosa_emergency }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600 max-w-xs">
                                    <div class="line-clamp-2" title="{{ $diagnosa->deskripsi }}">
                                        {{ $diagnosa->deskripsi ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if ($diagnosa->status === 'aktif')
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Non-Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600">
                                    @if ($diagnosa->obats->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($diagnosa->obats->take(3) as $obat)
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                                    {{ $obat->nama_obat }}
                                                </span>
                                            @endforeach
                                            @if ($diagnosa->obats->count() > 3)
                                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">
                                                    +{{ $diagnosa->obats->count() - 3 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('diagnosa-emergency.edit', $diagnosa->id_diagnosa_emergency) }}"
                                            class="inline-flex items-center justify-center w-9 h-9 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-all shadow-sm hover:shadow-md"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <button
                                            onclick="deleteDiagnosa({{ $diagnosa->id_diagnosa_emergency }}, '{{ $diagnosa->nama_diagnosa_emergency }}')"
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <p class="text-sm">Tidak ada data diagnosa emergency</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom Pagination -->
            @isset($diagnosaEmergencies)
                @if ($diagnosaEmergencies->hasPages())
                    <div class="px-6 py-5 border-t border-gray-200 bg-white">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                            <div class="text-sm text-gray-600">
                                Halaman <span
                                    class="font-semibold text-gray-900">{{ $diagnosaEmergencies->currentPage() }}</span>
                                dari <span class="font-semibold text-gray-900">{{ $diagnosaEmergencies->lastPage() }}</span>
                                <span class="mx-2 text-gray-400">•</span>
                                Total <span class="font-semibold text-gray-900">{{ $diagnosaEmergencies->total() }}</span>
                                data
                            </div>

                            <nav class="flex items-center gap-2" role="navigation">
                                @if ($diagnosaEmergencies->onFirstPage())
                                    <span
                                        class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                                        </svg>
                                    </span>
                                @else
                                    <a href="{{ $diagnosaEmergencies->appends(request()->except('page'))->url(1) }}"
                                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-red-50 hover:border-red-400 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                                        </svg>
                                    </a>
                                @endif

                                @if ($diagnosaEmergencies->onFirstPage())
                                    <span
                                        class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>
                                @else
                                    <a href="{{ $diagnosaEmergencies->appends(request()->except('page'))->previousPageUrl() }}"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-red-50 hover:border-red-400 transition-all">Previous</a>
                                @endif

                                <div class="flex items-center gap-1">
                                    @php
                                        $start = max($diagnosaEmergencies->currentPage() - 2, 1);
                                        $end = min(
                                            $diagnosaEmergencies->currentPage() + 2,
                                            $diagnosaEmergencies->lastPage(),
                                        );
                                    @endphp

                                    @if ($start > 1)
                                        <a href="{{ $diagnosaEmergencies->appends(request()->except('page'))->url(1) }}"
                                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-red-50 hover:border-red-400 transition-all">1</a>
                                        @if ($start > 2)
                                            <span class="px-2 text-gray-500">...</span>
                                        @endif
                                    @endif

                                    @for ($i = $start; $i <= $end; $i++)
                                        @if ($i == $diagnosaEmergencies->currentPage())
                                            <span
                                                class="px-3 py-2 text-sm font-bold text-white bg-gradient-to-r from-red-600 to-pink-700 rounded-lg shadow-md">{{ $i }}</span>
                                        @else
                                            <a href="{{ $diagnosaEmergencies->appends(request()->except('page'))->url($i) }}"
                                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-red-50 hover:border-red-400 transition-all">{{ $i }}</a>
                                        @endif
                                    @endfor

                                    @if ($end < $diagnosaEmergencies->lastPage())
                                        @if ($end < $diagnosaEmergencies->lastPage() - 1)
                                            <span class="px-2 text-gray-500">...</span>
                                        @endif
                                        <a href="{{ $diagnosaEmergencies->appends(request()->except('page'))->url($diagnosaEmergencies->lastPage()) }}"
                                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-red-50 hover:border-red-400 transition-all">{{ $diagnosaEmergencies->lastPage() }}</a>
                                    @endif
                                </div>

                                @if ($diagnosaEmergencies->hasMorePages())
                                    <a href="{{ $diagnosaEmergencies->appends(request()->except('page'))->nextPageUrl() }}"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-red-50 hover:border-red-400 transition-all">Next</a>
                                @else
                                    <span
                                        class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>
                                @endif

                                @if ($diagnosaEmergencies->currentPage() == $diagnosaEmergencies->lastPage())
                                    <span
                                        class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                        </svg>
                                    </span>
                                @else
                                    <a href="{{ $diagnosaEmergencies->appends(request()->except('page'))->url($diagnosaEmergencies->lastPage()) }}"
                                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-red-50 hover:border-red-400 transition-all">
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

    @push('scripts')
        <script>
            function toggleAll(source) {
                const checkboxes = document.querySelectorAll('.row-checkbox');
                checkboxes.forEach(cb => cb.checked = source.checked);
            }

            function deleteDiagnosa(id, nama) {
                console.log('deleteDiagnosa called with id:', id, 'nama:', nama);

                Swal.fire({
                    title: 'Hapus Data Diagnosa Emergency?',
                    html: `Apakah Anda yakin ingin menghapus diagnosa emergency <strong>${nama}</strong>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    console.log('Swal result:', result);

                    if (result.isConfirmed) {
                        console.log('Sending DELETE request to /diagnosa-emergency/' + id);

                        fetch(`/diagnosa-emergency/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => {
                                console.log('Response received:', response);
                                return response.json();
                            })
                            .then(data => {
                                console.log('Data received:', data);
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
                                    Swal.fire('Error!', data.message || 'Gagal menghapus data', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data', 'error');
                            });
                    }
                });
            }

            function submitBulkDelete() {
                const checkboxes = document.querySelectorAll('.row-checkbox:checked');
                const ids = Array.from(checkboxes).map(cb => cb.value);

                console.log('submitBulkDelete called with ids:', ids);

                if (ids.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Ada Data Dipilih',
                        text: 'Pilih minimal satu data untuk dihapus',
                        confirmButtonColor: '#dc2626'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Hapus Data Terpilih?',
                    html: `Apakah Anda yakin ingin menghapus <strong>${ids.length}</strong> diagnosa emergency yang dipilih?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus Semua!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    console.log('Bulk delete Swal result:', result);

                    if (result.isConfirmed) {
                        console.log('Sending POST request to /diagnosa-emergency/bulk-delete with ids:', ids);

                        fetch('/diagnosa-emergency/bulk-delete', {
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
                            .then(response => {
                                console.log('Bulk delete response received:', response);
                                return response.json();
                            })
                            .then(data => {
                                console.log('Bulk delete data received:', data);
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
                                    Swal.fire('Error!', data.message || 'Gagal menghapus data', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Bulk delete error:', error);
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

            // Auto-refresh saat per_page berubah
            document.addEventListener('DOMContentLoaded', function() {
                const perPageSelect = document.querySelector('select[name="per_page"]');
                if (perPageSelect) {
                    perPageSelect.addEventListener('change', function() {
                        const form = this.closest('form');
                        form.submit();
                    });
                }
            });
        </script>
    @endpush
@endsection
