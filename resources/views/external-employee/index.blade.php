@extends('layouts.app')

@section('page-title', 'Data External Employee')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Header Section -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                Data External Employee
            </h1>
            <p class="text-gray-600 mt-2 ml-1">Manajemen data karyawan eksternal</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <!-- Action Buttons Section -->
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-purple-50">
                <div class="flex flex-wrap gap-3 items-center">
                    <a href="{{ route('external-employee.create') }}"
                        class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah External Employee
                    </a>

                    @if (auth()->user()->role === 'Super Admin')
                        <button type="button" onclick="openImportModal()"
                            class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            Import Excel
                        </button>
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
            <x-filter-section theme="purple-indigo" action="{{ route('external-employee.index') }}"
                reset-url="{{ route('external-employee.index') }}" :fields="[
                    [
                        'type' => 'text',
                        'name' => 'search',
                        'label' => 'Cari External Employee',
                        'placeholder' => 'Nama employee...',
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
                        'name' => 'id_vendor',
                        'label' => 'Vendor',
                        'options' => array_merge(
                            [['value' => '', 'label' => '-- Semua Vendor --']],
                            $vendors
                                ->map(fn($vendor) => ['value' => $vendor->id_vendor, 'label' => $vendor->nama_vendor])
                                ->toArray(),
                        ),
                        'colSpan' => 'md:col-span-1',
                    ],
                    [
                        'type' => 'select',
                        'name' => 'id_kategori',
                        'label' => 'Kategori',
                        'options' => array_merge(
                            [['value' => '', 'label' => '-- Semua Kategori --']],
                            $kategoris
                                ->map(
                                    fn($kategori) => [
                                        'value' => $kategori->id_kategori,
                                        'label' => $kategori->nama_kategori,
                                    ],
                                )
                                ->toArray(),
                        ),
                        'colSpan' => 'md:col-span-1',
                    ],
                ]" />

            <!-- Table Controls -->
            <div
                class="p-5 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
                <div class="flex items-center gap-3">
                    <label class="text-sm font-medium text-gray-700">Tampilkan</label>
                    <form method="GET" id="perPageForm" class="inline">
                        @if (request('id_vendor'))
                            <input type="hidden" name="id_vendor" value="{{ request('id_vendor') }}">
                        @endif
                        @if (request('id_kategori'))
                            <input type="hidden" name="id_kategori" value="{{ request('id_kategori') }}">
                        @endif
                        @if (request('jenis_kelamin'))
                            <input type="hidden" name="jenis_kelamin" value="{{ request('jenis_kelamin') }}">
                        @endif
                        @if (request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        <select name="per_page" onchange="document.getElementById('perPageForm').submit()"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm bg-white shadow-sm">
                            <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            <option value="150" {{ request('per_page') == 150 ? 'selected' : '' }}>150</option>
                            <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                        </select>
                    </form>
                    <span class="text-sm font-medium text-gray-700">data per halaman</span>
                </div>
                <div class="text-sm text-gray-600">
                    Total: <span class="font-semibold text-gray-900">{{ $externalEmployees->total() }}</span> external
                    employee
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto" x-data="{ sortField: '{{ request('sort', '') }}', sortDirection: '{{ request('direction', 'asc') }}' }">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-800 to-gray-900">
                            @if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Super Admin')
                                <th class="px-4 py-4 text-left">
                                    <input type="checkbox" onclick="toggleAll(this)"
                                        class="rounded border-gray-400 text-purple-600 focus:ring-2 focus:ring-purple-500">
                                </th>
                            @endif
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">No</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                <a href="{{ route('external-employee.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'nik_employee', 'direction' => request('sort') == 'nik_employee' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-purple-300 transition-colors">
                                    <span>NIK</span>
                                    <span class="ml-2">
                                        @if (request('sort') == 'nik_employee')
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
                                <a href="{{ route('external-employee.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'nama_employee', 'direction' => request('sort') == 'nama_employee' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-purple-300 transition-colors">
                                    <span>Nama</span>
                                    <span class="ml-2">
                                        @if (request('sort') == 'nama_employee')
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
                                <a href="{{ route('external-employee.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'jenis_kelamin', 'direction' => request('sort') == 'jenis_kelamin' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-purple-300 transition-colors">
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
                                <a href="{{ route('external-employee.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'id_vendor', 'direction' => request('sort') == 'id_vendor' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-purple-300 transition-colors">
                                    <span>Vendor</span>
                                    <span class="ml-2">
                                        @if (request('sort') == 'id_vendor')
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
                                <a href="{{ route('external-employee.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'id_kategori', 'direction' => request('sort') == 'id_kategori' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-purple-300 transition-colors">
                                    <span>Kategori</span>
                                    <span class="ml-2">
                                        @if (request('sort') == 'id_kategori')
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
                                <a href="{{ route('external-employee.index', array_merge(request()->except(['page', 'sort', 'direction']), ['sort' => 'no_hp', 'direction' => request('sort') == 'no_hp' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center justify-between group hover:text-purple-300 transition-colors">
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
                                <span>Status</span>
                            </th>
                            @if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Super Admin')
                                <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Aksi
                                </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($externalEmployees as $employee)
                            <tr class="hover:bg-purple-50 transition-colors">
                                @if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Super Admin')
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <input name="ids[]" value="{{ $employee->id }}" type="checkbox"
                                            class="row-checkbox rounded border-gray-300 text-purple-600 focus:ring-2 focus:ring-purple-500"
                                            data-id="{{ $employee->id }}">
                                    </td>
                                @endif
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ ($externalEmployees->currentPage() - 1) * $externalEmployees->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span
                                        class="text-sm font-semibold text-purple-600">{{ $employee->nik_employee }}</span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        @if ($employee->foto)
                                            <img src="{{ asset('storage/' . $employee->foto) }}"
                                                alt="{{ $employee->nama_employee }}"
                                                class="w-10 h-10 rounded-full object-cover border-2 border-purple-200 shadow-sm"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full items-center justify-center text-white text-xs font-bold hidden">
                                                {{ strtoupper(substr($employee->nama_employee, 0, 1)) }}
                                            </div>
                                        @else
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                                {{ strtoupper(substr($employee->nama_employee, 0, 1)) }}
                                            </div>
                                        @endif
                                        <span
                                            class="text-sm font-medium text-gray-900">{{ $employee->nama_employee }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if ($employee->jenis_kelamin == 'Laki-laki')
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
                                    {{ $employee->vendor->nama_vendor }}</td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @switch($employee->kategori->kode_kategori)
                                        @case('x')
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                {{ $employee->kategori->nama_kategori }}
                                            </span>
                                        @break

                                        @case('y')
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $employee->kategori->nama_kategori }}
                                            </span>
                                        @break

                                        @case('z')
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $employee->kategori->nama_kategori }}
                                            </span>
                                        @break
                                    @endswitch
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->no_hp }}</td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if ($employee->status == 'aktif')
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                @if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Super Admin')
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('external-employee.edit', $employee->id) }}"
                                                class="inline-flex items-center justify-center w-9 h-9 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-all shadow-sm hover:shadow-md"
                                                title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('external-employee.destroy', $employee->id) }}"
                                                method="POST" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete(this)"
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all shadow-sm hover:shadow-md"
                                                    title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>

                            @empty
                                <tr>
                                    <td colspan="11" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Custom Pagination -->
                @isset($externalEmployees)
                    @if ($externalEmployees->hasPages())
                        <div class="px-6 py-5 border-t border-gray-200 bg-white">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                <!-- Info Text -->
                                <div class="text-sm text-gray-600">
                                    Halaman <span
                                        class="font-semibold text-gray-900">{{ $externalEmployees->currentPage() }}</span>
                                    dari <span class="font-semibold text-gray-900">{{ $externalEmployees->lastPage() }}</span>
                                    <span class="mx-2 text-gray-400">•</span>
                                    Total <span class="font-semibold text-gray-900">{{ $externalEmployees->total() }}</span> data
                                </div>

                                <!-- Pagination Buttons -->
                                <nav class="flex items-center gap-2" role="navigation" aria-label="Pagination Navigation">
                                    {{-- First Page --}}
                                    @if ($externalEmployees->onFirstPage())
                                        <span
                                            class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                                            </svg>
                                        </span>
                                    @else
                                        <a href="{{ $externalEmployees->appends(request()->except('page'))->url(1) }}"
                                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                                            </svg>
                                        </a>
                                    @endif

                                    {{-- Previous Page --}}
                                    @if ($externalEmployees->onFirstPage())
                                        <span
                                            class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                            Previous
                                        </span>
                                    @else
                                        <a href="{{ $externalEmployees->appends(request()->except('page'))->previousPageUrl() }}"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">
                                            Previous
                                        </a>
                                    @endif

                                    {{-- Page Numbers --}}
                                    <div class="flex items-center gap-1">
                                        @php
                                            $start = max($externalEmployees->currentPage() - 2, 1);
                                            $end = min(
                                                $externalEmployees->currentPage() + 2,
                                                $externalEmployees->lastPage(),
                                            );
                                        @endphp

                                        @if ($start > 1)
                                            <a href="{{ $externalEmployees->appends(request()->except('page'))->url(1) }}"
                                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">
                                                1
                                            </a>
                                            @if ($start > 2)
                                                <span class="px-2 text-gray-500">...</span>
                                            @endif
                                        @endif

                                        @for ($i = $start; $i <= $end; $i++)
                                            @if ($i == $externalEmployees->currentPage())
                                                <span
                                                    class="px-3 py-2 text-sm font-bold text-white bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg shadow-md">
                                                    {{ $i }}
                                                </span>
                                            @else
                                                <a href="{{ $externalEmployees->appends(request()->except('page'))->url($i) }}"
                                                    class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">
                                                    {{ $i }}
                                                </a>
                                            @endif
                                        @endfor

                                        @if ($end < $externalEmployees->lastPage())
                                            @if ($end < $externalEmployees->lastPage() - 1)
                                                <span class="px-2 text-gray-500">...</span>
                                            @endif
                                            <a href="{{ $externalEmployees->appends(request()->except('page'))->url($externalEmployees->lastPage()) }}"
                                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">
                                                {{ $externalEmployees->lastPage() }}
                                            </a>
                                        @endif
                                    </div>

                                    {{-- Next Page --}}
                                    @if ($externalEmployees->hasMorePages())
                                        <a href="{{ $externalEmployees->appends(request()->except('page'))->nextPageUrl() }}"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">
                                            Next
                                        </a>
                                    @else
                                        <span
                                            class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                            Next
                                        </span>
                                    @endif

                                    {{-- Last Page --}}
                                    @if ($externalEmployees->currentPage() == $externalEmployees->lastPage())
                                        <span
                                            class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                            </svg>
                                        </span>
                                    @else
                                        <a href="{{ $externalEmployees->appends(request()->except('page'))->url($externalEmployees->lastPage()) }}"
                                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">
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
                        title: 'Hapus External Employee?',
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
                            text: 'Pilih minimal satu external employee untuk dihapus',
                            confirmButtonColor: '#8b5cf6',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Hapus ' + ids.length + ' External Employee?',
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
                            form.action = '{{ route('external-employee.bulkDelete') }}';

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
                        title: 'Import Data External Employee dari Excel/CSV',
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
                        <li>• Format file: Excel (.xlsx, .xls) atau CSV (.csv)</li>
                        <li>• Maksimal ukuran file: 5MB</li>
                        <li>• Download template terlebih dahulu</li>
                        <li>• NIK Employee harus unik</li>
                        <li>• Format Tanggal Lahir: YYYY-MM-DD</li>
                        <li>• Jenis Kelamin: L atau P</li>
                        <li>• No HP harus diawali dengan 08</li>
                        <li>• BPJS ID hanya boleh angka (opsional)</li>
                        <li>• Kategori: X (Guest), Y (Outsourcing), Z (Supporting)</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <a href="{{ route('external-employee.template') }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all w-full justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Template Excel
                    </a>
                </div>

                <form id="importForm" action="{{ route('external-employee.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel/CSV</label>
                        <input type="file"
                               name="file"
                               id="importFile"
                               accept=".xlsx,.xls,.csv"
                               required
                               class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2">
                        <p class="mt-1 text-xs text-gray-500">File Excel (.xlsx, .xls) atau CSV (.csv), maksimal 5MB</p>
                    </div>
                </form>
            </div>
        `,
                        showCancelButton: true,
                        confirmButtonText: 'Upload & Import',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#8b5cf6',
                        cancelButtonColor: '#6b7280',
                        width: '600px',
                        customClass: {
                            confirmButton: 'px-5 py-2.5 rounded-lg font-medium',
                            cancelButton: 'px-5 py-2.5 rounded-lg font-medium'
                        },
                        preConfirm: () => {
                            const fileInput = document.getElementById('importFile');
                            if (!fileInput.files || fileInput.files.length === 0) {
                                Swal.showValidationMessage('Silakan pilih file Excel/CSV terlebih dahulu');
                                return false;
                            }

                            const file = fileInput.files[0];
                            const maxSize = 5 * 1024 * 1024; // 5MB

                            if (file.size > maxSize) {
                                Swal.showValidationMessage('Ukuran file maksimal 5MB');
                                return false;
                            }

                            const allowedExtensions = ['xlsx', 'xls', 'csv'];
                            const fileExtension = file.name.split('.').pop().toLowerCase();

                            if (!allowedExtensions.includes(fileExtension)) {
                                Swal.showValidationMessage('Format file harus .xlsx, .xls, atau .csv');
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
                            fetch('{{ route('external-employee.import') }}', {
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
                                        confirmButtonColor: '#8b5cf6'
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
                                        confirmButtonColor: '#8b5cf6'
                                    });
                                });
                        }
                    });
                }
            </script>
        @endpush
    @endsection
