                                                                                                    @extends('layouts.app')

                                                                                                    @section('title',
                                                                                                        'Dashboard')

                                                                                                    @section('page-title',
                                                                                                        'Dashboard')

                                                                                                        @push('styles')
                                                                                                            <!-- Chart.js -->
                                                                                                            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                                                                                                        @endpush

                                                                                                    @section('content')
                                                                                                        <div
                                                                                                            class="p-6 bg-gray-50 min-h-screen">
                                                                                                            <!-- Header Section -->
                                                                                                            <div
                                                                                                                class="mb-6">
                                                                                                                <h1
                                                                                                                    class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                                                                                                                    <div
                                                                                                                        class="bg-gradient-to-r from-blue-600 to-cyan-600 p-3 rounded-lg shadow-lg">
                                                                                                                        <svg class="w-8 h-8 text-white"
                                                                                                                            fill="none"
                                                                                                                            stroke="currentColor"
                                                                                                                            viewBox="0 0 24 24">
                                                                                                                            <path
                                                                                                                                stroke-linecap="round"
                                                                                                                                stroke-linejoin="round"
                                                                                                                                stroke-width="2"
                                                                                                                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                                                                                                        </svg>
                                                                                                                    </div>
                                                                                                                    Selamat
                                                                                                                    Datang
                                                                                                                    <span
                                                                                                                        id="typing-name"
                                                                                                                        class="bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent"></span><span
                                                                                                                        id="cursor"
                                                                                                                        class="bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent animate-pulse">|</span>
                                                                                                                    di
                                                                                                                    Dashboard
                                                                                                                    SIPO
                                                                                                                </h1>
                                                                                                                <p
                                                                                                                    class="text-gray-600 mt-2 ml-1">
                                                                                                                    Sistem
                                                                                                                    Informasi
                                                                                                                    Klinik
                                                                                                                    Indofood
                                                                                                                    -
                                                                                                                    Monitoring
                                                                                                                    Real-time
                                                                                                                </p>
                                                                                                            </div>


                                                                                                            @if (session('error'))
                                                                                                                <div
                                                                                                                    class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg shadow-sm">
                                                                                                                    <div
                                                                                                                        class="flex items-center gap-3">
                                                                                                                        <svg class="w-5 h-5 text-red-500"
                                                                                                                            fill="none"
                                                                                                                            stroke="currentColor"
                                                                                                                            viewBox="0 0 24 24">
                                                                                                                            <path
                                                                                                                                stroke-linecap="round"
                                                                                                                                stroke-linejoin="round"
                                                                                                                                stroke-width="2"
                                                                                                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                                                                        </svg>
                                                                                                                        <p
                                                                                                                            class="text-sm text-red-700 font-medium">
                                                                                                                            {{ session('error') }}
                                                                                                                        </p>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            @endif

                                                                                                            <!-- Dashboard Statistics Cards -->
                                                                                                            <div
                                                                                                                class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8">
                                                                                                                <!-- Card 1 - Total Karyawan -->
                                                                                                                <div onclick="navigateToKaryawan()"
                                                                                                                    class="relative overflow-hidden bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 rounded-xl shadow-xl p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer">
                                                                                                                    <div
                                                                                                                        class="absolute top-0 right-0 -mt-4 -mr-4 w-20 h-20 bg-white opacity-10 rounded-full">
                                                                                                                    </div>
                                                                                                                    <div
                                                                                                                        class="absolute bottom-0 left-0 -mb-6 -ml-6 w-24 h-24 bg-white opacity-5 rounded-full">
                                                                                                                    </div>

                                                                                                                    <div
                                                                                                                        class="relative">
                                                                                                                        <div
                                                                                                                            class="flex items-center gap-2 mb-2">
                                                                                                                            <div
                                                                                                                                class="w-2 h-2 bg-blue-200 rounded-full animate-pulse">
                                                                                                                            </div>
                                                                                                                            <h3
                                                                                                                                class="text-xs font-medium text-blue-100">
                                                                                                                                Total
                                                                                                                                Karyawan
                                                                                                                                Aktif
                                                                                                                            </h3>
                                                                                                                        </div>
                                                                                                                        <div
                                                                                                                            class="flex items-end justify-between">
                                                                                                                            <p class="text-5xl font-bold"
                                                                                                                                id="totalKaryawan">
                                                                                                                                9999
                                                                                                                            </p>
                                                                                                                            <div
                                                                                                                                class="bg-white p-3 rounded-lg shadow-lg">
                                                                                                                                <svg class="w-8 h-8 text-blue-600"
                                                                                                                                    fill="none"
                                                                                                                                    stroke="currentColor"
                                                                                                                                    viewBox="0 0 24 24">
                                                                                                                                    <path
                                                                                                                                        stroke-linecap="round"
                                                                                                                                        stroke-linejoin="round"
                                                                                                                                        stroke-width="2"
                                                                                                                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                                                                                                </svg>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>

                                                                                                                <!-- Card 2 - Total Rekam Medis -->
                                                                                                                <div onclick="navigateToRekamMedis()"
                                                                                                                    class="relative overflow-hidden bg-gradient-to-br from-green-500 via-green-600 to-green-700 rounded-xl shadow-xl p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer">
                                                                                                                    <div
                                                                                                                        class="absolute top-0 right-0 -mt-4 -mr-4 w-20 h-20 bg-white opacity-10 rounded-full">
                                                                                                                    </div>
                                                                                                                    <div
                                                                                                                        class="absolute bottom-0 left-0 -mb-6 -ml-6 w-24 h-24 bg-white opacity-5 rounded-full">
                                                                                                                    </div>

                                                                                                                    <div
                                                                                                                        class="relative">
                                                                                                                        <div
                                                                                                                            class="flex items-center gap-2 mb-2">
                                                                                                                            <div
                                                                                                                                class="w-2 h-2 bg-green-200 rounded-full animate-pulse">
                                                                                                                            </div>
                                                                                                                            <h3
                                                                                                                                class="text-xs font-medium text-green-100">
                                                                                                                                Total
                                                                                                                                Rekam
                                                                                                                                Medis
                                                                                                                            </h3>
                                                                                                                        </div>
                                                                                                                        <div
                                                                                                                            class="flex items-end justify-between">
                                                                                                                            <p class="text-5xl font-bold"
                                                                                                                                id="totalRekamMedis">
                                                                                                                                9999
                                                                                                                            </p>
                                                                                                                            <div
                                                                                                                                class="bg-white p-3 rounded-lg shadow-lg">
                                                                                                                                <svg class="w-8 h-8 text-green-600"
                                                                                                                                    fill="none"
                                                                                                                                    stroke="currentColor"
                                                                                                                                    viewBox="0 0 24 24">
                                                                                                                                    <path
                                                                                                                                        stroke-linecap="round"
                                                                                                                                        stroke-linejoin="round"
                                                                                                                                        stroke-width="2"
                                                                                                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                                                                                </svg>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>

                                                                                                                <!-- Card 3 - Kunjungan Hari Ini -->
                                                                                                                <div onclick="navigateToKunjunganHariIni()"
                                                                                                                    class="relative overflow-hidden bg-gradient-to-br from-yellow-400 via-yellow-500 to-yellow-600 rounded-xl shadow-xl p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer">
                                                                                                                    <div
                                                                                                                        class="absolute top-0 right-0 -mt-4 -mr-4 w-20 h-20 bg-white opacity-10 rounded-full">
                                                                                                                    </div>
                                                                                                                    <div
                                                                                                                        class="absolute bottom-0 left-0 -mb-6 -ml-6 w-24 h-24 bg-white opacity-5 rounded-full">
                                                                                                                    </div>

                                                                                                                    <div
                                                                                                                        class="relative">
                                                                                                                        <div
                                                                                                                            class="flex items-center gap-2 mb-2">
                                                                                                                            <div
                                                                                                                                class="w-2 h-2 bg-yellow-200 rounded-full animate-pulse">
                                                                                                                            </div>
                                                                                                                            <h3
                                                                                                                                class="text-xs font-medium text-yellow-100">
                                                                                                                                Kunjungan
                                                                                                                                Hari
                                                                                                                                Ini
                                                                                                                            </h3>
                                                                                                                        </div>
                                                                                                                        <div
                                                                                                                            class="flex items-end justify-between">
                                                                                                                            <p class="text-5xl font-bold"
                                                                                                                                id="kunjunganHariIni">
                                                                                                                                9999
                                                                                                                            </p>
                                                                                                                            <div
                                                                                                                                class="bg-white p-3 rounded-lg shadow-lg">
                                                                                                                                <svg class="w-8 h-8 text-yellow-600"
                                                                                                                                    fill="none"
                                                                                                                                    stroke="currentColor"
                                                                                                                                    viewBox="0 0 24 24">
                                                                                                                                    <path
                                                                                                                                        stroke-linecap="round"
                                                                                                                                        stroke-linejoin="round"
                                                                                                                                        stroke-width="2"
                                                                                                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                                                                                </svg>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>

                                                                                                                <!-- Card 4 - On Progress -->
                                                                                                                <div onclick="navigateToOnProgress()"
                                                                                                                    class="relative overflow-hidden bg-gradient-to-br from-red-500 via-red-600 to-red-700 rounded-xl shadow-xl p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer">
                                                                                                                    <div
                                                                                                                        class="absolute top-0 right-0 -mt-4 -mr-4 w-20 h-20 bg-white opacity-10 rounded-full">
                                                                                                                    </div>
                                                                                                                    <div
                                                                                                                        class="absolute bottom-0 left-0 -mb-6 -ml-6 w-24 h-24 bg-white opacity-5 rounded-full">
                                                                                                                    </div>

                                                                                                                    <div
                                                                                                                        class="relative">
                                                                                                                        <div
                                                                                                                            class="flex items-center gap-2 mb-2">
                                                                                                                            <div
                                                                                                                                class="w-2 h-2 bg-red-200 rounded-full animate-pulse">
                                                                                                                            </div>
                                                                                                                            <h3
                                                                                                                                class="text-xs font-medium text-red-100">
                                                                                                                                On
                                                                                                                                Progress
                                                                                                                            </h3>
                                                                                                                        </div>
                                                                                                                        <div
                                                                                                                            class="flex items-end justify-between">
                                                                                                                            <p class="text-5xl font-bold"
                                                                                                                                id="onProgress">
                                                                                                                                9999
                                                                                                                            </p>
                                                                                                                            <div
                                                                                                                                class="bg-white p-3 rounded-lg shadow-lg">
                                                                                                                                <svg class="w-8 h-8 text-red-600"
                                                                                                                                    fill="none"
                                                                                                                                    stroke="currentColor"
                                                                                                                                    viewBox="0 0 24 24">
                                                                                                                                    <path
                                                                                                                                        stroke-linecap="round"
                                                                                                                                        stroke-linejoin="round"
                                                                                                                                        stroke-width="2"
                                                                                                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                                                                                </svg>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>

                                                                                                                <!-- Card 5 - Close -->
                                                                                                                <div onclick="navigateToClose()"
                                                                                                                    class="relative overflow-hidden bg-gradient-to-br from-gray-600 via-gray-700 to-gray-800 rounded-xl shadow-xl p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer">
                                                                                                                    <div
                                                                                                                        class="absolute top-0 right-0 -mt-4 -mr-4 w-20 h-20 bg-white opacity-10 rounded-full">
                                                                                                                    </div>
                                                                                                                    <div
                                                                                                                        class="absolute bottom-0 left-0 -mb-6 -ml-6 w-24 h-24 bg-white opacity-5 rounded-full">
                                                                                                                    </div>

                                                                                                                    <div
                                                                                                                        class="relative">
                                                                                                                        <div
                                                                                                                            class="flex items-center gap-2 mb-2">
                                                                                                                            <div
                                                                                                                                class="w-2 h-2 bg-gray-300 rounded-full animate-pulse">
                                                                                                                            </div>
                                                                                                                            <h3
                                                                                                                                class="text-xs font-medium text-gray-200">
                                                                                                                                Close
                                                                                                                            </h3>
                                                                                                                        </div>
                                                                                                                        <div
                                                                                                                            class="flex items-end justify-between">
                                                                                                                            <p class="text-5xl font-bold"
                                                                                                                                id="close">
                                                                                                                                9999
                                                                                                                            </p>
                                                                                                                            <div
                                                                                                                                class="bg-white p-3 rounded-lg shadow-lg">
                                                                                                                                <svg class="w-8 h-8 text-gray-700"
                                                                                                                                    fill="none"
                                                                                                                                    stroke="currentColor"
                                                                                                                                    viewBox="0 0 24 24">
                                                                                                                                    <path
                                                                                                                                        stroke-linecap="round"
                                                                                                                                        stroke-linejoin="round"
                                                                                                                                        stroke-width="2"
                                                                                                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                                                                                </svg>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>

                                                                                                            <!-- Filter Periode Section -->
                                                                                                            <div
                                                                                                                class="bg-white rounded-xl shadow-md border border-gray-100 p-6 mb-8">
                                                                                                                <div
                                                                                                                    class="flex items-center gap-2 mb-4">
                                                                                                                    <svg class="w-5 h-5 text-blue-600"
                                                                                                                        fill="none"
                                                                                                                        stroke="currentColor"
                                                                                                                        viewBox="0 0 24 24">
                                                                                                                        <path
                                                                                                                            stroke-linecap="round"
                                                                                                                            stroke-linejoin="round"
                                                                                                                            stroke-width="2"
                                                                                                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                                                                                                    </svg>
                                                                                                                    <h3
                                                                                                                        class="text-lg font-semibold text-gray-800">
                                                                                                                        Filter
                                                                                                                        Periode
                                                                                                                        Data
                                                                                                                    </h3>
                                                                                                                    <span
                                                                                                                        class="ml-auto text-xs text-gray-500 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">
                                                                                                                        Pilih
                                                                                                                        bulan
                                                                                                                        &
                                                                                                                        tahun
                                                                                                                        untuk
                                                                                                                        melihat
                                                                                                                        data
                                                                                                                    </span>
                                                                                                                </div>

                                                                                                                <div
                                                                                                                    class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                                                                                    <div>
                                                                                                                        <label
                                                                                                                            class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                                                                                                                        <div
                                                                                                                            class="relative">
                                                                                                                            <select
                                                                                                                                id="monthFilter"
                                                                                                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none bg-white pr-10">
                                                                                                                                <option
                                                                                                                                    value="1"
                                                                                                                                    {{ date('n') == 1 ? 'selected' : '' }}>
                                                                                                                                    Januari
                                                                                                                                </option>
                                                                                                                                <option
                                                                                                                                    value="2"
                                                                                                                                    {{ date('n') == 2 ? 'selected' : '' }}>
                                                                                                                                    Februari
                                                                                                                                </option>
                                                                                                                                <option
                                                                                                                                    value="3"
                                                                                                                                    {{ date('n') == 3 ? 'selected' : '' }}>
                                                                                                                                    Maret
                                                                                                                                </option>
                                                                                                                                <option
                                                                                                                                    value="4"
                                                                                                                                    {{ date('n') == 4 ? 'selected' : '' }}>
                                                                                                                                    April
                                                                                                                                </option>
                                                                                                                                <option
                                                                                                                                    value="5"
                                                                                                                                    {{ date('n') == 5 ? 'selected' : '' }}>
                                                                                                                                    Mei
                                                                                                                                </option>
                                                                                                                                <option
                                                                                                                                    value="6"
                                                                                                                                    {{ date('n') == 6 ? 'selected' : '' }}>
                                                                                                                                    Juni
                                                                                                                                </option>
                                                                                                                                <option
                                                                                                                                    value="7"
                                                                                                                                    {{ date('n') == 7 ? 'selected' : '' }}>
                                                                                                                                    Juli
                                                                                                                                </option>
                                                                                                                                <option
                                                                                                                                    value="8"
                                                                                                                                    {{ date('n') == 8 ? 'selected' : '' }}>
                                                                                                                                    Agustus
                                                                                                                                </option>
                                                                                                                                <option
                                                                                                                                    value="9"
                                                                                                                                    {{ date('n') == 9 ? 'selected' : '' }}>
                                                                                                                                    September
                                                                                                                                </option>
                                                                                                                                <option
                                                                                                                                    value="10"
                                                                                                                                    {{ date('n') == 10 ? 'selected' : '' }}>
                                                                                                                                    Oktober
                                                                                                                                </option>
                                                                                                                                <option
                                                                                                                                    value="11"
                                                                                                                                    {{ date('n') == 11 ? 'selected' : '' }}>
                                                                                                                                    November
                                                                                                                                </option>
                                                                                                                                <option
                                                                                                                                    value="12"
                                                                                                                                    {{ date('n') == 12 ? 'selected' : '' }}>
                                                                                                                                    Desember
                                                                                                                                </option>
                                                                                                                            </select>
                                                                                                                            <div
                                                                                                                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                                                                                                                <svg class="w-4 h-4"
                                                                                                                                    fill="none"
                                                                                                                                    stroke="currentColor"
                                                                                                                                    viewBox="0 0 24 24">
                                                                                                                                    <path
                                                                                                                                        stroke-linecap="round"
                                                                                                                                        stroke-linejoin="round"
                                                                                                                                        stroke-width="2"
                                                                                                                                        d="M19 9l-7 7-7-7" />
                                                                                                                                </svg>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>

                                                                                                                    <div>
                                                                                                                        <label
                                                                                                                            class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                                                                                                                        <input
                                                                                                                            type="number"
                                                                                                                            id="yearFilter"
                                                                                                                            value="{{ date('Y') }}"
                                                                                                                            min="2000"
                                                                                                                            max="2100"
                                                                                                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                                                                                    </div>

                                                                                                                    <div
                                                                                                                        class="flex items-end">
                                                                                                                        <button
                                                                                                                            onclick="filterCharts()"
                                                                                                                            class="w-full px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                                                                                                            <svg class="w-5 h-5"
                                                                                                                                fill="none"
                                                                                                                                stroke="currentColor"
                                                                                                                                viewBox="0 0 24 24">
                                                                                                                                <path
                                                                                                                                    stroke-linecap="round"
                                                                                                                                    stroke-linejoin="round"
                                                                                                                                    stroke-width="2"
                                                                                                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                                                                                            </svg>
                                                                                                                            Tampilkan
                                                                                                                            Data
                                                                                                                        </button>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>

                                                                                                            <!-- Top Diagnoses Section -->


                                                                                                            <!-- Analytics Section - Combined Card -->
                                                                                                            <div
                                                                                                                class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                                                                                                                <!-- Header -->
                                                                                                                <div
                                                                                                                    class="bg-gradient-to-r from-blue-600 to-cyan-600 p-6">
                                                                                                                    <div
                                                                                                                        class="flex items-center gap-3">
                                                                                                                        <div
                                                                                                                            class="bg-white p-3 rounded-lg shadow-lg">
                                                                                                                            <svg class="w-8 h-8 text-blue-600"
                                                                                                                                xmlns="http://www.w3.org/2000/svg"
                                                                                                                                viewBox="0 0 24 24"
                                                                                                                                fill="none"
                                                                                                                                stroke="currentColor"
                                                                                                                                stroke-width="2"
                                                                                                                                stroke-linecap="round"
                                                                                                                                stroke-linejoin="round"
                                                                                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-align-box-bottom-right">
                                                                                                                                <path
                                                                                                                                    stroke="none"
                                                                                                                                    d="M0 0h24v24H0z"
                                                                                                                                    fill="none" />
                                                                                                                                <path
                                                                                                                                    d="M3 3m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                                                                                                                                <path
                                                                                                                                    d="M11 15v2" />
                                                                                                                                <path
                                                                                                                                    d="M14 11v6" />
                                                                                                                                <path
                                                                                                                                    d="M17 13v4" />
                                                                                                                            </svg>
                                                                                                                        </div>
                                                                                                                        <div>
                                                                                                                            <h2
                                                                                                                                class="text-xl font-bold text-white">
                                                                                                                                Analisis
                                                                                                                                Data
                                                                                                                                Kunjungan
                                                                                                                            </h2>
                                                                                                                            <p
                                                                                                                                class="text-blue-100 text-sm">
                                                                                                                                Monitoring
                                                                                                                                kunjungan
                                                                                                                                pasien
                                                                                                                                secara
                                                                                                                                real-time
                                                                                                                            </p>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>

                                                                                                                <!-- Content Wrapper -->
                                                                                                                <div
                                                                                                                    class="p-6">
                                                                                                                    <!-- Charts Grid - Harian & Mingguan -->
                                                                                                                    <div
                                                                                                                        class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                                                                                                                        <!-- Chart 1 - Kunjungan Harian -->
                                                                                                                        <div
                                                                                                                            class="bg-gradient-to-br from-gray-50 to-teal-50 border border-teal-100 rounded-lg p-5">
                                                                                                                            <div
                                                                                                                                class="flex items-center justify-between mb-4">
                                                                                                                                <div
                                                                                                                                    class="flex items-center gap-2">
                                                                                                                                    <div
                                                                                                                                        class="w-3 h-3 bg-teal-500 rounded-full">
                                                                                                                                    </div>
                                                                                                                                    <h4 id="dailyChartTitle"
                                                                                                                                        class="text-sm font-semibold text-gray-700">
                                                                                                                                        Kunjungan
                                                                                                                                        Harian
                                                                                                                                    </h4>
                                                                                                                                </div>
                                                                                                                                <span
                                                                                                                                    class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200">Harian</span>
                                                                                                                            </div>
                                                                                                                            <div
                                                                                                                                style="height: 250px;">
                                                                                                                                <canvas
                                                                                                                                    id="dailyVisitChart"></canvas>
                                                                                                                            </div>
                                                                                                                        </div>

                                                                                                                        <!-- Chart 2 - Kunjungan Mingguan -->
                                                                                                                        <div
                                                                                                                            class="bg-gradient-to-br from-gray-50 to-red-50 border border-red-100 rounded-lg p-5">
                                                                                                                            <div
                                                                                                                                class="flex items-center justify-between mb-4">
                                                                                                                                <div
                                                                                                                                    class="flex items-center gap-2">
                                                                                                                                    <div
                                                                                                                                        class="w-3 h-3 bg-red-500 rounded-full">
                                                                                                                                    </div>
                                                                                                                                    <h4 id="weeklyChartTitle"
                                                                                                                                        class="text-sm font-semibold text-gray-700">
                                                                                                                                        Kunjungan
                                                                                                                                        Mingguan
                                                                                                                                    </h4>
                                                                                                                                </div>
                                                                                                                                <span
                                                                                                                                    class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200">Mingguan</span>
                                                                                                                            </div>
                                                                                                                            <div
                                                                                                                                style="height: 250px;">
                                                                                                                                <canvas
                                                                                                                                    id="weeklyVisitChart"></canvas>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>

                                                                                                                    <!-- Chart 3 - Kunjungan Bulanan (Full Width) -->
                                                                                                                    <div
                                                                                                                        class="bg-gradient-to-br from-gray-50 to-blue-50 border border-blue-100 rounded-lg p-5">
                                                                                                                        <div
                                                                                                                            class="flex items-center justify-between mb-4">
                                                                                                                            <div
                                                                                                                                class="flex items-center gap-2">
                                                                                                                                <div
                                                                                                                                    class="w-3 h-3 bg-blue-500 rounded-full">
                                                                                                                                </div>
                                                                                                                                <h4 id="monthlyChartTitle"
                                                                                                                                    class="text-sm font-semibold text-gray-700">
                                                                                                                                    Kunjungan
                                                                                                                                    Bulanan
                                                                                                                                </h4>
                                                                                                                            </div>
                                                                                                                            <span
                                                                                                                                class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200">Bulanan</span>
                                                                                                                        </div>
                                                                                                                        <div
                                                                                                                            style="height: 300px;">
                                                                                                                            <canvas
                                                                                                                                id="monthlyVisitChart"></canvas>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div
                                                                                                                class="bg-white mt-10 rounded-xl shadow-md border border-gray-100 overflow-hidden mb-8">
                                                                                                                <!-- Header -->
                                                                                                                <div
                                                                                                                    class="bg-gradient-to-r from-purple-600 to-pink-600 p-6">
                                                                                                                    <div
                                                                                                                        class="flex items-center gap-3">
                                                                                                                        <div
                                                                                                                            class="bg-white p-3 rounded-lg shadow-lg">
                                                                                                                            <svg class="w-8 h-8 text-purple-600"
                                                                                                                                fill="none"
                                                                                                                                stroke="currentColor"
                                                                                                                                viewBox="0 0 24 24">
                                                                                                                                <path
                                                                                                                                    stroke-linecap="round"
                                                                                                                                    stroke-linejoin="round"
                                                                                                                                    stroke-width="2"
                                                                                                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                                                                                                            </svg>
                                                                                                                        </div>
                                                                                                                        <div>
                                                                                                                            <h2
                                                                                                                                class="text-2xl font-bold text-white">
                                                                                                                                Diagnosa
                                                                                                                                Terbanyak
                                                                                                                            </h2>
                                                                                                                            <p class="text-purple-100 text-sm"
                                                                                                                                id="diagnosaPeriod">
                                                                                                                                Bulan
                                                                                                                                ini
                                                                                                                            </p>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>

                                                                                                                <!-- Content -->
                                                                                                                <div
                                                                                                                    class="p-6">
                                                                                                                    <div
                                                                                                                        class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                                                                                                        <!-- Chart Area - 1 kolom -->
                                                                                                                        <div
                                                                                                                            class="lg:col-span-1">
                                                                                                                            <div
                                                                                                                                class="bg-gradient-to-br from-gray-50 to-purple-50 border border-purple-100 rounded-lg p-5 h-full flex flex-col">
                                                                                                                                <h4
                                                                                                                                    class="text-sm font-semibold text-gray-700 mb-4">
                                                                                                                                    Distribusi
                                                                                                                                    Diagnosa
                                                                                                                                </h4>
                                                                                                                                <div
                                                                                                                                    class="flex-1 flex items-center justify-center">
                                                                                                                                    <div
                                                                                                                                        style="max-width: 280px; max-height: 280px;">
                                                                                                                                        <canvas
                                                                                                                                            id="diagnosisChart"></canvas>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>

                                                                                                                        <!-- List Area - 2 kolom -->
                                                                                                                        <div
                                                                                                                            class="lg:col-span-2">
                                                                                                                            <div
                                                                                                                                class="bg-gradient-to-br from-gray-50 to-pink-50 border border-pink-100 rounded-lg p-5 h-full flex flex-col">
                                                                                                                                <div
                                                                                                                                    class="flex items-center justify-between mb-4">
                                                                                                                                    <h4
                                                                                                                                        class="text-sm font-semibold text-gray-700">
                                                                                                                                        Top
                                                                                                                                        10
                                                                                                                                        Diagnosa
                                                                                                                                    </h4>
                                                                                                                                    <span
                                                                                                                                        class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200"
                                                                                                                                        id="totalCases">0
                                                                                                                                        Kasus</span>
                                                                                                                                </div>
                                                                                                                                <div id="diagnosisList"
                                                                                                                                    class="grid grid-cols-1 md:grid-cols-2 gap-3 flex-1">
                                                                                                                                    <!-- Loading state -->
                                                                                                                                    <div
                                                                                                                                        class="col-span-2 flex items-center justify-center py-8">
                                                                                                                                        <div
                                                                                                                                            class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600">
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    @endsection

                                                                                                    @push('scripts')
                                                                                                        <script>
                                                                                                            // Global variables for charts
                                                                                                            let dailyChart, weeklyChart, monthlyChart, diagnosisChart;

                                                                                                            // Load initial data
                                                                                                            document.addEventListener('DOMContentLoaded', function() {
                                                                                                                // Set current month and year
                                                                                                                const currentDate = new Date();
                                                                                                                const currentMonth = (currentDate.getMonth() + 1).toString();
                                                                                                                const currentYear = currentDate.getFullYear().toString();

                                                                                                                document.getElementById('monthFilter').value = currentMonth;
                                                                                                                document.getElementById('yearFilter').value = currentYear;

                                                                                                                // Initialize chart titles with current month/year
                                                                                                                updateChartTitles(currentMonth, currentYear);

                                                                                                                // Start typing effect
                                                                                                                typeUserName('{{ $user->nama_lengkap }}');

                                                                                                                loadStatistics();
                                                                                                                loadVisitAnalysis(currentMonth, currentYear);
                                                                                                                loadTopDiagnoses(currentMonth, currentYear);

                                                                                                                // Auto refresh every 30 seconds
                                                                                                                setInterval(loadStatistics, 30000);
                                                                                                            });

                                                                                                            // Typing effect function
                                                                                                            function typeUserName(name) {
                                                                                                                const element = document.getElementById('typing-name');
                                                                                                                const cursor = document.getElementById('cursor');
                                                                                                                let index = 0;

                                                                                                                function type() {
                                                                                                                    if (index < name.length) {
                                                                                                                        element.textContent += name.charAt(index);
                                                                                                                        index++;
                                                                                                                        setTimeout(type, 100); // Adjust typing speed here (100ms per character)
                                                                                                                    } else {
                                                                                                                        // Hide cursor after typing is complete
                                                                                                                        setTimeout(() => {
                                                                                                                            cursor.style.display = 'none';
                                                                                                                        }, 1000);
                                                                                                                    }
                                                                                                                }

                                                                                                                type();
                                                                                                            }

                                                                                                            // Load statistics data
                                                                                                            async function loadStatistics() {
                                                                                                                try {
                                                                                                                    const response = await fetch('/api/dashboard/statistics');
                                                                                                                    const data = await response.json();

                                                                                                                    // Animate statistics cards from 999 to actual values
                                                                                                                    animateCounter('totalKaryawan', data.total_karyawan);
                                                                                                                    animateCounter('totalRekamMedis', data.total_rekam_medis);
                                                                                                                    animateCounter('kunjunganHariIni', data.kunjungan_hari_ini);
                                                                                                                    animateCounter('onProgress', data.on_progress);
                                                                                                                    animateCounter('close', data.close);

                                                                                                                } catch (error) {
                                                                                                                    console.error('Error loading statistics:', error);
                                                                                                                }
                                                                                                            }

                                                                                                            // Animate counter from 9999 to target value
                                                                                                            function animateCounter(elementId, targetValue) {
                                                                                                                const element = document.getElementById(elementId);
                                                                                                                const startValue = 9999;
                                                                                                                const duration = 2000; // 2 seconds animation
                                                                                                                const startTime = performance.now();

                                                                                                                function updateCounter(currentTime) {
                                                                                                                    const elapsed = currentTime - startTime;
                                                                                                                    const progress = Math.min(elapsed / duration, 1);

                                                                                                                    // Easing function for smooth animation
                                                                                                                    const easeOutQuart = 1 - Math.pow(1 - progress, 4);

                                                                                                                    // Calculate current value
                                                                                                                    const currentValue = Math.floor(startValue - (startValue - targetValue) * easeOutQuart);

                                                                                                                    // Update element
                                                                                                                    element.textContent = currentValue;

                                                                                                                    // Continue animation if not complete
                                                                                                                    if (progress < 1) {
                                                                                                                        requestAnimationFrame(updateCounter);
                                                                                                                    }
                                                                                                                }

                                                                                                                // Start animation
                                                                                                                requestAnimationFrame(updateCounter);
                                                                                                            }

                                                                                                            // Load visit analysis data
                                                                                                            async function loadVisitAnalysis(month = null, year = null) {
                                                                                                                try {
                                                                                                                    const params = new URLSearchParams();
                                                                                                                    if (month) params.append('month', month);
                                                                                                                    if (year) params.append('year', year);

                                                                                                                    const response = await fetch(`/api/dashboard/visit-analysis?${params}`);
                                                                                                                    const data = await response.json();

                                                                                                                    // Update charts with new data
                                                                                                                    updateCharts(data);

                                                                                                                } catch (error) {
                                                                                                                    console.error('Error loading visit analysis:', error);
                                                                                                                }
                                                                                                            }

                                                                                                            // Update charts with new data
                                                                                                            function updateCharts(data) {
                                                                                                                // Update daily chart
                                                                                                                if (dailyChart) {
                                                                                                                    dailyChart.data.labels = data.daily.labels;
                                                                                                                    dailyChart.data.datasets[0].data = data.daily.data;
                                                                                                                    dailyChart.update();
                                                                                                                } else {
                                                                                                                    createDailyChart(data.daily);
                                                                                                                }

                                                                                                                // Update weekly chart
                                                                                                                if (weeklyChart) {
                                                                                                                    weeklyChart.data.labels = data.weekly.labels;
                                                                                                                    weeklyChart.data.datasets[0].data = data.weekly.data;
                                                                                                                    weeklyChart.update();
                                                                                                                } else {
                                                                                                                    createWeeklyChart(data.weekly);
                                                                                                                }

                                                                                                                // Update monthly chart
                                                                                                                if (monthlyChart) {
                                                                                                                    monthlyChart.data.datasets[0].data = data.monthly.data;
                                                                                                                    monthlyChart.update();
                                                                                                                } else {
                                                                                                                    createMonthlyChart(data.monthly);
                                                                                                                }
                                                                                                            }

                                                                                                            // Create daily chart
                                                                                                            function createDailyChart(data) {
                                                                                                                const ctx = document.getElementById('dailyVisitChart').getContext('2d');
                                                                                                                dailyChart = new Chart(ctx, {
                                                                                                                    type: 'line',
                                                                                                                    data: {
                                                                                                                        labels: data.labels,
                                                                                                                        datasets: [{
                                                                                                                            label: 'Harian',
                                                                                                                            data: data.data,
                                                                                                                            borderColor: 'rgb(20, 184, 166)',
                                                                                                                            backgroundColor: 'rgba(20, 184, 166, 0.2)',
                                                                                                                            fill: true,
                                                                                                                            tension: 0.4,
                                                                                                                            pointRadius: 4,
                                                                                                                            pointHoverRadius: 6,
                                                                                                                            borderWidth: 2
                                                                                                                        }]
                                                                                                                    },
                                                                                                                    options: {
                                                                                                                        responsive: true,
                                                                                                                        maintainAspectRatio: false,
                                                                                                                        plugins: {
                                                                                                                            legend: {
                                                                                                                                display: false
                                                                                                                            },
                                                                                                                            tooltip: {
                                                                                                                                enabled: true,
                                                                                                                                callbacks: {
                                                                                                                                    label: function(context) {
                                                                                                                                        return 'Kunjungan: ' + context.parsed.y;
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            }
                                                                                                                        },
                                                                                                                        scales: {
                                                                                                                            y: {
                                                                                                                                beginAtZero: true,
                                                                                                                                ticks: {
                                                                                                                                    stepSize: 2
                                                                                                                                }
                                                                                                                            },
                                                                                                                            x: {
                                                                                                                                grid: {
                                                                                                                                    display: false
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                });
                                                                                                            }

                                                                                                            // Create weekly chart
                                                                                                            function createWeeklyChart(data) {
                                                                                                                const ctx = document.getElementById('weeklyVisitChart').getContext('2d');
                                                                                                                weeklyChart = new Chart(ctx, {
                                                                                                                    type: 'line',
                                                                                                                    data: {
                                                                                                                        labels: data.labels,
                                                                                                                        datasets: [{
                                                                                                                            label: 'Mingguan',
                                                                                                                            data: data.data,
                                                                                                                            borderColor: 'rgb(239, 68, 68)',
                                                                                                                            backgroundColor: 'rgba(239, 68, 68, 0.2)',
                                                                                                                            fill: true,
                                                                                                                            tension: 0.4,
                                                                                                                            pointRadius: 4,
                                                                                                                            pointHoverRadius: 6,
                                                                                                                            borderWidth: 2
                                                                                                                        }]
                                                                                                                    },
                                                                                                                    options: {
                                                                                                                        responsive: true,
                                                                                                                        maintainAspectRatio: false,
                                                                                                                        plugins: {
                                                                                                                            legend: {
                                                                                                                                display: false
                                                                                                                            },
                                                                                                                            tooltip: {
                                                                                                                                enabled: true,
                                                                                                                                callbacks: {
                                                                                                                                    label: function(context) {
                                                                                                                                        return 'Kunjungan: ' + context.parsed.y;
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            }
                                                                                                                        },
                                                                                                                        scales: {
                                                                                                                            y: {
                                                                                                                                beginAtZero: true,
                                                                                                                                ticks: {
                                                                                                                                    stepSize: 2
                                                                                                                                }
                                                                                                                            },
                                                                                                                            x: {
                                                                                                                                grid: {
                                                                                                                                    display: false
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                });
                                                                                                            }

                                                                                                            // Create monthly chart
                                                                                                            function createMonthlyChart(data) {
                                                                                                                const ctx = document.getElementById('monthlyVisitChart').getContext('2d');
                                                                                                                monthlyChart = new Chart(ctx, {
                                                                                                                    type: 'line',
                                                                                                                    data: {
                                                                                                                        labels: data.labels,
                                                                                                                        datasets: [{
                                                                                                                            label: 'Bulanan',
                                                                                                                            data: data.data,
                                                                                                                            borderColor: 'rgb(59, 130, 246)',
                                                                                                                            backgroundColor: 'rgba(59, 130, 246, 0.2)',
                                                                                                                            fill: true,
                                                                                                                            tension: 0.4,
                                                                                                                            pointRadius: 4,
                                                                                                                            pointHoverRadius: 6,
                                                                                                                            borderWidth: 2
                                                                                                                        }]
                                                                                                                    },
                                                                                                                    options: {
                                                                                                                        responsive: true,
                                                                                                                        maintainAspectRatio: false,
                                                                                                                        plugins: {
                                                                                                                            legend: {
                                                                                                                                display: false
                                                                                                                            },
                                                                                                                            tooltip: {
                                                                                                                                enabled: true,
                                                                                                                                callbacks: {
                                                                                                                                    label: function(context) {
                                                                                                                                        return 'Kunjungan: ' + context.parsed.y;
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            }
                                                                                                                        },
                                                                                                                        scales: {
                                                                                                                            y: {
                                                                                                                                beginAtZero: true,
                                                                                                                                ticks: {
                                                                                                                                    stepSize: 2
                                                                                                                                }
                                                                                                                            },
                                                                                                                            x: {
                                                                                                                                grid: {
                                                                                                                                    display: false
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                });
                                                                                                            }

                                                                                                            // Filter function
                                                                                                            function filterCharts() {
                                                                                                                const month = document.getElementById('monthFilter').value;
                                                                                                                const year = document.getElementById('yearFilter').value;

                                                                                                                // Update chart titles
                                                                                                                updateChartTitles(month, year);

                                                                                                                // Load new data from API
                                                                                                                loadVisitAnalysis(month, year);
                                                                                                                loadTopDiagnoses(month, year);
                                                                                                            }

                                                                                                            // Update chart titles based on month and year
                                                                                                            function updateChartTitles(month, year) {
                                                                                                                const monthName = getMonthName(month);

                                                                                                                document.getElementById('dailyChartTitle').textContent =
                                                                                                                    `Kunjungan Harian (${monthName} ${year})`;
                                                                                                                document.getElementById('weeklyChartTitle').textContent =
                                                                                                                    `Kunjungan Mingguan (per minggu bulan ${monthName})`;
                                                                                                                document.getElementById('monthlyChartTitle').textContent =
                                                                                                                    `Kunjungan Bulanan (${year})`;
                                                                                                            }

                                                                                                            function getMonthName(month) {
                                                                                                                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                                                                                                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                                                                                                ];
                                                                                                                return months[parseInt(month) - 1];
                                                                                                            }

                                                                                                            // Function to navigate to karyawan page
                                                                                                            function navigateToKaryawan() {
                                                                                                                window.location.href = '{{ route('karyawan.index') }}';
                                                                                                            }

                                                                                                            // Function to navigate to rekam medis page
                                                                                                            function navigateToRekamMedis() {
                                                                                                                window.location.href = '{{ route('rekam-medis.index') }}';
                                                                                                            }

                                                                                                            // Function to navigate to rekam medis with today's date filter
                                                                                                            function navigateToKunjunganHariIni() {
                                                                                                                const today = new Date().toISOString().split('T')[0];
                                                                                                                const url = '{{ route('rekam-medis.index') }}?dari_tanggal=' + today + '&sampai_tanggal=' + today;
                                                                                                                window.location.href = url;
                                                                                                            }

                                                                                                            // Function to navigate to rekam medis with On Progress status filter
                                                                                                            function navigateToOnProgress() {
                                                                                                                // Since there's no status filter in the current form, we'll navigate to rekam medis page
                                                                                                                // and add a custom parameter that can be handled in the controller
                                                                                                                const url = '{{ route('rekam-medis.index') }}?status=On Progress';
                                                                                                                window.location.href = url;
                                                                                                            }

                                                                                                            // Function to navigate to rekam medis with Close status filter
                                                                                                            function navigateToClose() {
                                                                                                                // Since there's no status filter in the current form, we'll navigate to rekam medis page
                                                                                                                // and add a custom parameter that can be handled in the controller
                                                                                                                const url = '{{ route('rekam-medis.index') }}?status=Close';
                                                                                                                window.location.href = url;
                                                                                                            }

                                                                                                            // Load top diagnoses data
                                                                                                            async function loadTopDiagnoses(month = null, year = null) {
                                                                                                                try {
                                                                                                                    const params = new URLSearchParams();
                                                                                                                    if (month) params.append('month', month);
                                                                                                                    if (year) params.append('year', year);
                                                                                                                    params.append('limit', 10);

                                                                                                                    const response = await fetch(`/api/dashboard/top-diagnoses?${params}`);
                                                                                                                    const data = await response.json();

                                                                                                                    // Update diagnosis period text
                                                                                                                    const monthName = getMonthName(month || new Date().getMonth() + 1);
                                                                                                                    document.getElementById('diagnosaPeriod').textContent =
                                                                                                                        `${monthName} ${year || new Date().getFullYear()}`;

                                                                                                                    // Update total cases
                                                                                                                    document.getElementById('totalCases').textContent = `${data.total_cases} Kasus`;

                                                                                                                    // Update diagnosis list
                                                                                                                    updateDiagnosisList(data.diagnoses);

                                                                                                                    // Update diagnosis chart
                                                                                                                    updateDiagnosisChart(data.diagnoses);

                                                                                                                } catch (error) {
                                                                                                                    console.error('Error loading top diagnoses:', error);
                                                                                                                    document.getElementById('diagnosisList').innerHTML =
                                                                                                                        '<div class="text-center py-8 text-red-500">Gagal memuat data diagnosa</div>';
                                                                                                                }
                                                                                                            }

                                                                                                            // Update diagnosis list
                                                                                                            function updateDiagnosisList(diagnoses) {
                                                                                                                const listContainer = document.getElementById('diagnosisList');

                                                                                                                if (!diagnoses || diagnoses.length === 0) {
                                                                                                                    listContainer.innerHTML =
                                                                                                                        '<div class="text-center py-8 text-gray-500">Tidak ada data diagnosa</div>';
                                                                                                                    return;
                                                                                                                }

                                                                                                                const colors = ['purple', 'pink', 'indigo', 'violet', 'fuchsia'];

                                                                                                                listContainer.innerHTML = diagnoses.map((item, index) => `
                <div class="bg-white rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3 flex-1">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-${colors[index]}-100 text-${colors[index]}-600 font-bold text-sm">
                                ${index + 1}
                            </div>
                            <div class="flex-1">
                                <h5 class="font-semibold text-gray-800 text-sm line-clamp-1">${item.nama_diagnosa}</h5>
                            </div>
                        </div>
                        <div class="text-right ml-2">
                            <p class="text-xl font-bold text-${colors[index]}-600">${item.total}</p>
                            <p class="text-xs text-gray-500">${item.percentage}%</p>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-${colors[index]}-500 to-${colors[index]}-600 h-2 rounded-full transition-all duration-500"
                                style="width: ${item.percentage}%"></div>
                        </div>
                    </div>
                </div>
            `).join('');
                                                                                                            }

                                                                                                            // Update diagnosis chart
                                                                                                            function updateDiagnosisChart(diagnoses) {
                                                                                                                if (diagnosisChart) {
                                                                                                                    diagnosisChart.destroy();
                                                                                                                }

                                                                                                                if (!diagnoses || diagnoses.length === 0) {
                                                                                                                    return;
                                                                                                                }

                                                                                                                const ctx = document.getElementById('diagnosisChart').getContext('2d');
                                                                                                                const colors = [{
                                                                                                                        bg: 'rgba(147, 51, 234, 0.8)',
                                                                                                                        border: 'rgb(147, 51, 234)'
                                                                                                                    }, // purple
                                                                                                                    {
                                                                                                                        bg: 'rgba(236, 72, 153, 0.8)',
                                                                                                                        border: 'rgb(236, 72, 153)'
                                                                                                                    }, // pink
                                                                                                                    {
                                                                                                                        bg: 'rgba(99, 102, 241, 0.8)',
                                                                                                                        border: 'rgb(99, 102, 241)'
                                                                                                                    }, // indigo
                                                                                                                    {
                                                                                                                        bg: 'rgba(139, 92, 246, 0.8)',
                                                                                                                        border: 'rgb(139, 92, 246)'
                                                                                                                    }, // violet
                                                                                                                    {
                                                                                                                        bg: 'rgba(217, 70, 239, 0.8)',
                                                                                                                        border: 'rgb(217, 70, 239)'
                                                                                                                    }, // fuchsia
                                                                                                                    {
                                                                                                                        bg: 'rgba(59, 130, 246, 0.8)',
                                                                                                                        border: 'rgb(59, 130, 246)'
                                                                                                                    }, // blue
                                                                                                                    {
                                                                                                                        bg: 'rgba(168, 85, 247, 0.8)',
                                                                                                                        border: 'rgb(168, 85, 247)'
                                                                                                                    }, // purple-500
                                                                                                                    {
                                                                                                                        bg: 'rgba(244, 114, 182, 0.8)',
                                                                                                                        border: 'rgb(244, 114, 182)'
                                                                                                                    }, // pink-400
                                                                                                                    {
                                                                                                                        bg: 'rgba(167, 139, 250, 0.8)',
                                                                                                                        border: 'rgb(167, 139, 250)'
                                                                                                                    }, // violet-400
                                                                                                                    {
                                                                                                                        bg: 'rgba(192, 132, 252, 0.8)',
                                                                                                                        border: 'rgb(192, 132, 252)'
                                                                                                                    }, // purple-400
                                                                                                                ];

                                                                                                                diagnosisChart = new Chart(ctx, {
                                                                                                                    type: 'doughnut',
                                                                                                                    data: {
                                                                                                                        labels: diagnoses.map(d => d.nama_diagnosa),
                                                                                                                        datasets: [{
                                                                                                                            data: diagnoses.map(d => d.total),
                                                                                                                            backgroundColor: colors.map(c => c.bg),
                                                                                                                            borderColor: colors.map(c => c.border),
                                                                                                                            borderWidth: 2,
                                                                                                                            spacing: 2
                                                                                                                        }]
                                                                                                                    },
                                                                                                                    options: {
                                                                                                                        responsive: true,
                                                                                                                        maintainAspectRatio: true,
                                                                                                                        cutout: '65%',
                                                                                                                        layout: {
                                                                                                                            padding: {
                                                                                                                                top: 5,
                                                                                                                                bottom: 5,
                                                                                                                                left: 5,
                                                                                                                                right: 5
                                                                                                                            }
                                                                                                                        },
                                                                                                                        plugins: {
                                                                                                                            legend: {
                                                                                                                                position: 'bottom',
                                                                                                                                labels: {
                                                                                                                                    padding: 8,
                                                                                                                                    font: {
                                                                                                                                        size: 9
                                                                                                                                    },
                                                                                                                                    boxWidth: 10,
                                                                                                                                    boxHeight: 10,
                                                                                                                                    usePointStyle: true,
                                                                                                                                    pointStyle: 'circle',
                                                                                                                                    generateLabels: function(chart) {
                                                                                                                                        const data = chart.data;
                                                                                                                                        if (data.labels.length && data.datasets.length) {
                                                                                                                                            return data.labels.map((label, i) => {
                                                                                                                                                const value = data.datasets[0].data[i];
                                                                                                                                                const total = data.datasets[0].data.reduce((a, b) => a + b,
                                                                                                                                                    0);
                                                                                                                                                const percentage = ((value / total) * 100).toFixed(1);
                                                                                                                                                // Truncate label if too long
                                                                                                                                                const shortLabel = label.length > 12 ? label.substring(0,
                                                                                                                                                    12) + '...' : label;
                                                                                                                                                return {
                                                                                                                                                    text: `${shortLabel} (${percentage}%)`,
                                                                                                                                                    fillStyle: data.datasets[0].backgroundColor[i],
                                                                                                                                                    hidden: false,
                                                                                                                                                    index: i
                                                                                                                                                };
                                                                                                                                            });
                                                                                                                                        }
                                                                                                                                        return [];
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            },
                                                                                                                            tooltip: {
                                                                                                                                callbacks: {
                                                                                                                                    label: function(context) {
                                                                                                                                        const label = context.label || '';
                                                                                                                                        const value = context.parsed || 0;
                                                                                                                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                                                                                                        const percentage = ((value / total) * 100).toFixed(1);
                                                                                                                                        return `${label}: ${value} kasus (${percentage}%)`;
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                });
                                                                                                            }
                                                                                                        </script>
                                                                                                    @endpush
