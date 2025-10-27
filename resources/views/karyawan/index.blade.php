@extends('layouts.app')

@section('page-title', 'Data Karyawan')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Header Section -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <div class="bg-gradient-to-r from-blue-600 to-cyan-600 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                Data Karyawan
            </h1>
            <p class="text-gray-600 mt-2 ml-1">Manajemen data karyawan perusahaan</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <!-- Action Buttons Section -->
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-blue-50">
                <div class="flex flex-wrap gap-3 items-center">
                    <a href="{{ route('karyawan.create') }}"
                        class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Karyawan
                    </a>

                    @if(auth()->user()->role === 'Super Admin')
                    <button type="button" onclick="openImportModal()"
                        class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Import Excel
                    </button>
                    @endif

                    <button type="button" onclick="submitBulkDelete()"
                        class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Terpilih
                    </button>
                </div>
            </div>

            <!-- Filter Section -->
            <x-filter-section theme="blue" action="{{ route('karyawan.index') }}"
                reset-url="{{ route('karyawan.index') }}" :fields="[
                    [
                        'type' => 'text',
                        'name' => 'q',
                        'label' => 'Cari Karyawan',
                        'placeholder' => 'Nama karyawan...',
                        'withIcon' => true,
                        'colSpan' => 'md:col-span-1',
                    ],
                    [
                        'type' => 'select',
                        'name' => 'jenis_kelamin',
                        'label' => 'Jenis Kelamin',
                        'options' => [
                            ['value' => '', 'label' => '-- Semua --'],
                            ['value' => 'L', 'label' => 'Laki-laki'],
                            ['value' => 'P', 'label' => 'Perempuan'],
                        ],
                        'colSpan' => 'md:col-span-1',
                    ],
                    [
                        'type' => 'select',
                        'name' => 'departemen',
                        'label' => 'Departemen',
                        'options' => array_merge(
                            [['value' => '', 'label' => '-- Semua Departemen --']],
                            $departemens
                                ?->map(
                                    fn($dept) => ['value' => $dept->id_departemen, 'label' => $dept->nama_departemen],
                                )
                                ->toArray() ?? [],
                        ),
                        'colSpan' => 'md:col-span-1',
                    ],
                    [
                        'type' => 'select',
                        'name' => 'status',
                        'label' => 'Status',
                        'options' => [
                            ['value' => '', 'label' => '-- Semua Status --'],
                            ['value' => 'aktif', 'label' => 'Aktif'],
                            ['value' => 'nonaktif', 'label' => 'Nonaktif'],
                        ],
                        'colSpan' => 'md:col-span-1',
                    ],
                ]" />

            <!-- Table Controls -->
            <div
                class="p-5 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
                <div class="flex items-center gap-3">
                    <label class="text-sm font-medium text-gray-700">Tampilkan</label>
                    <form method="GET" id="perPageForm" class="inline">
                        @if (request('departemen'))
                            <input type="hidden" name="departemen" value="{{ request('departemen') }}">
                        @endif
                        @if (request('jenis_kelamin'))
                            <input type="hidden" name="jenis_kelamin" value="{{ request('jenis_kelamin') }}">
                        @endif
                        @if (request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        @if (request('q'))
                            <input type="hidden" name="q" value="{{ request('q') }}">
                        @endif
                        <select name="per_page" onchange="document.getElementById('perPageForm').submit()"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-white shadow-sm">
                            <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                            <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                        </select>
                    </form>
                    <span class="text-sm font-medium text-gray-700">data per halaman</span>
                </div>
                <div class="text-sm text-gray-600">
                    Total: <span class="font-semibold text-gray-900">{{ $karyawans->total() }}</span> karyawan
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto" x-data="{ sortField: '{{ request('sort', '') }}', sortDirection: '{{ request('direction', 'asc') }}' }">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-800 to-gray-900">
                            <th class="px-4 py-4 text-left">
                                <input type="checkbox" onclick="toggleAll(this)"
                                    class="rounded border-gray-400 text-blue-600 focus:ring-2 focus:ring-blue-500">
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">No</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                <a href="{{ route('karyawan.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'nik_karyawan', 'direction' => request('sort') == 'nik_karyawan' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-blue-300 transition-colors">
                                    <span>NIK</span>
                                    <span class="ml-2">
                                        @if (request('sort') == 'nik_karyawan')
                                            @if (request('direction') == 'asc')
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    < path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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
                                <a href="{{ route('karyawan.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'nama_karyawan', 'direction' => request('sort') == 'nama_karyawan' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-blue-300 transition-colors">
                                    <span>Nama</span>
                                    <span class="ml-2">
                                        @if (request('sort') == 'nama_karyawan')
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
                                <a href="{{ route('karyawan.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'jenis_kelamin', 'direction' => request('sort') == 'jenis_kelamin' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-blue-300 transition-colors">
                                    <span>Jenis Kelamin</span>
                                    <span class="ml-2">
                                        @if (request('sort') == 'jenis_kelamin')
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
                                <a href="{{ route('karyawan.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'id_departemen', 'direction' => request('sort') == 'id_departemen' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-blue-300 transition-colors">
                                    <span>Departemen</span>
                                    <span class="ml-2">
                                        @if (request('sort') == 'id_departemen')
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
                                <a href="{{ route('karyawan.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'no_hp', 'direction' => request('sort') == 'no_hp' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-blue-300 transition-colors">
                                    <span>No HP</span>
                                    <span class="ml-2">
                                        @if (request('sort') == 'no_hp')
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
                                <span>Email</span>
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                <span>BPJS ID</span>
                            </th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                <a href="{{ route('karyawan.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'tanggal_lahir', 'direction' => request('sort') == 'tanggal_lahir' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-blue-300 transition-colors">
                                    <span>Tanggal Lahir</span>
                                    <span class="ml-2">
                                        @if (request('sort') == 'tanggal_lahir')
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
                        @forelse($karyawans as $karyawan)
                            <tr class="hover:bg-blue-50 transition-colors">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <input name="ids[]" value="{{ $karyawan->id_karyawan }}" type="checkbox"
                                        class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                        data-id="{{ $karyawan->id_karyawan }}">
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ ($karyawans->currentPage() - 1) * $karyawans->perPage() + $loop->iteration }}</td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-blue-600">{{ $karyawan->nik_karyawan }}</span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        @if ($karyawan->foto)
                                            <img src="{{ asset('storage/' . $karyawan->foto) }}"
                                                alt="{{ $karyawan->nama_karyawan }}"
                                                class="w-10 h-10 rounded-full object-cover border-2 border-blue-200 shadow-sm"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full items-center justify-center text-white text-xs font-bold hidden">
                                                {{ strtoupper(Str::of($karyawan->nama_karyawan)->explode(' ')->map(fn($p) => Str::substr($p, 0, 1))->take(2)->implode('')) }}
                                            </div>
                                        @else
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                                {{ strtoupper(Str::of($karyawan->nama_karyawan)->explode(' ')->map(fn($p) => Str::substr($p, 0, 1))->take(2)->implode('')) }}
                                            </div>
                                        @endif
                                        <span
                                            class="text-sm font-medium text-gray-900">{{ $karyawan->nama_karyawan }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if (strtolower($karyawan->jenis_kelamin) == 'l' ||
                                            strtolower($karyawan->jenis_kelamin) == 'j' ||
                                            strtolower($karyawan->jenis_kelamin) == 'laki - laki')
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Laki-laki
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-pink-100 text-pink-800">
                                            Perempuan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ optional($karyawan->departemen)->nama_departemen }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $karyawan->no_hp }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $karyawan->email ?? '-' }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $karyawan->bpjs_id ?? '-' }}</td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if ($karyawan->status === 'aktif')
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1 text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ optional($karyawan->tanggal_lahir)->format('d-m-Y') }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('karyawan.edit', $karyawan->id_karyawan) }}"
                                            class="inline-flex items-center justify-center w-9 h-9 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-all shadow-sm hover:shadow-md"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('karyawan.destroy', $karyawan->id_karyawan) }}"
                                            method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete(this)"
                                                class="inline-flex items-center justify-center w-9 h-9 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all shadow-sm hover:shadow-md"
                                                title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom Pagination -->
            @isset($karyawans)
                @if ($karyawans->hasPages())
                    <div class="px-6 py-5 border-t border-gray-200 bg-white">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                            <!-- Info Text -->
                            <div class="text-sm text-gray-600">
                                Halaman <span class="font-semibold text-gray-900">{{ $karyawans->currentPage() }}</span>
                                dari <span class="font-semibold text-gray-900">{{ $karyawans->lastPage() }}</span>
                                <span class="mx-2 text-gray-400">•</span>
                                Total <span class="font-semibold text-gray-900">{{ $karyawans->total() }}</span> data
                            </div>

                            <!-- Pagination Buttons -->
                            <nav class="flex items-center gap-2" role="navigation" aria-label="Pagination Navigation">
                                {{-- First Page --}}
                                @if ($karyawans->onFirstPage())
                                    <span
                                        class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                                        </svg>
                                    </span>
                                @else
                                    <a href="{{ $karyawans->appends(request()->except('page'))->url(1) }}"
                                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                                        </svg>
                                    </a>
                                @endif

                                {{-- Previous Page --}}
                                @if ($karyawans->onFirstPage())
                                    <span
                                        class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                        Previous
                                    </span>
                                @else
                                    <a href="{{ $karyawans->appends(request()->except('page'))->previousPageUrl() }}"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">
                                        Previous
                                    </a>
                                @endif

                                {{-- Page Numbers --}}
                                <div class="flex items-center gap-1">
                                    @php
                                        $start = max($karyawans->currentPage() - 2, 1);
                                        $end = min($karyawans->currentPage() + 2, $karyawans->lastPage());
                                    @endphp

                                    @if ($start > 1)
                                        <a href="{{ $karyawans->appends(request()->except('page'))->url(1) }}"
                                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">
                                            1
                                        </a>
                                        @if ($start > 2)
                                            <span class="px-2 text-gray-500">...</span>
                                        @endif
                                    @endif

                                    @for ($i = $start; $i <= $end; $i++)
                                        @if ($i == $karyawans->currentPage())
                                            <span
                                                class="px-3 py-2 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-md">
                                                {{ $i }}
                                            </span>
                                        @else
                                            <a href="{{ $karyawans->appends(request()->except('page'))->url($i) }}"
                                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">
                                                {{ $i }}
                                            </a>
                                        @endif
                                    @endfor

                                    @if ($end < $karyawans->lastPage())
                                        @if ($end < $karyawans->lastPage() - 1)
                                            <span class="px-2 text-gray-500">...</span>
                                        @endif
                                        <a href="{{ $karyawans->appends(request()->except('page'))->url($karyawans->lastPage()) }}"
                                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">
                                            {{ $karyawans->lastPage() }}
                                        </a>
                                    @endif
                                </div>

                                {{-- Next Page --}}
                                @if ($karyawans->hasMorePages())
                                    <a href="{{ $karyawans->appends(request()->except('page'))->nextPageUrl() }}"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">
                                        Next
                                    </a>
                                @else
                                    <span
                                        class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                        Next
                                    </span>
                                @endif

                                {{-- Last Page --}}
                                @if ($karyawans->currentPage() == $karyawans->lastPage())
                                    <span
                                        class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                        </svg>
                                    </span>
                                @else
                                    <a href="{{ $karyawans->appends(request()->except('page'))->url($karyawans->lastPage()) }}"
                                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">
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

            function confirmDelete(button) {
                Swal.fire({
                    title: 'Hapus Karyawan?',
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
                        text: 'Pilih minimal satu karyawan untuk dihapus',
                        confirmButtonColor: '#3b82f6',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Hapus ' + ids.length + ' Karyawan?',
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
                        // Create form dynamically
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('karyawan.bulkDelete') }}';

                        // Add CSRF token
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = '{{ csrf_token() }}';
                        form.appendChild(csrfInput);

                        // Add IDs
                        ids.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'ids[]';
                            input.value = id;
                            form.appendChild(input);
                        });

                        // Append to body and submit
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }

            function getSelectedIds() {
                const nodes = Array.from(document.querySelectorAll('.row-checkbox:checked'));
                return nodes.map(n => n.value);
            }

            // Import Excel Modal Functions
            function openImportModal() {
                Swal.fire({
                    title: 'Import Data Karyawan dari Excel',
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
                        <li>• Format file: Excel (.xlsx atau .xls)</li>
                        <li>• Maksimal ukuran file: 5MB</li>
                        <li>• Download template terlebih dahulu</li>
                        <li>• NIK minimal 1 dan maksimal 15 karakter</li>
                        <li>• Format Tanggal Lahir: YYYY-MM-DD</li>
                        <li>• Jenis Kelamin: L, J, atau P</li>
                        <li>• No HP harus diawali dengan 08</li>
                        <li>• BPJS ID hanya boleh angka (opsional)</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <a href="{{ route('karyawan.template') }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all w-full justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Template Excel
                    </a>
                </div>

                <form id="importForm" action="{{ route('karyawan.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel</label>
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
                    confirmButtonColor: '#3b82f6',
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
                            html: 'Mohon tunggu, data sedang diproses',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit via AJAX
                        fetch('{{ route('karyawan.import') }}', {
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
                                    confirmButtonColor: '#3b82f6'
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
                                    html: error.message || 'Terjadi kesalahan saat mengimport data',
                                    confirmButtonColor: '#3b82f6'
                                });
                            });
                    }
                });
            }
        </script>
    @endpush
@endsection
