<aside class="bg-white shadow-lg flex-shrink-0 transition-all duration-300" :class="sidebarOpen ? 'w-64' : 'w-20'">
    <div class="h-full flex flex-col">
        <!-- Logo -->
        <div class="flex items-center justify-center h-20 border-b border-gray-200 px-4">
            <img src="{{ asset('logo.png') }}" alt="ICBP Logo" class="transition-all duration-300" :class="sidebarOpen ? 'h-12' : 'h-10'" x-show="sidebarOpen">
            <img src="{{ asset('logo.png') }}" alt="ICBP Logo" class="h-8 w-auto" x-show="!sidebarOpen">
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-1 px-3">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center px-4 py-3 {{ request()->routeIs('dashboard') ? 'text-gray-700 bg-blue-50 border-l-4 border-blue-600 rounded-r-md font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md' }} transition-colors"
                       :title="!sidebarOpen ? 'Dashboard' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Dashboard</span>
                    </a>
                </li>

                <!-- Master Data (Dropdown) -->
                <li x-data="{ open: false }">
                    <button @click="sidebarOpen && (open = !open)"
                            class="flex items-center justify-between w-full px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md transition-colors"
                            :title="!sidebarOpen ? 'Master Data' : ''">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Master Data</span>
                        </div>
                        <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <ul x-show="open && sidebarOpen" x-transition class="mt-1 ml-8 space-y-1">
                        <li>
                            <a href="{{ route('karyawan.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md transition-colors">
                                Data Karyawan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('keluarga.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md transition-colors">
                                Data Keluarga
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('obat.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md transition-colors">
                                Data Obat
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('diagnosa.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md transition-colors">
                                Data Diagnosa
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md transition-colors">
                                Data User
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Rekam Medis (Dropdown) -->
                <li x-data="{ open: false }">
                    <button @click="sidebarOpen && (open = !open)"
                            class="flex items-center justify-between w-full px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md transition-colors"
                            :title="!sidebarOpen ? 'Rekam Medis' : ''">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Rekam Medis</span>
                        </div>
                        <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <ul x-show="open && sidebarOpen" x-transition class="mt-1 ml-8 space-y-1">
                        <li>
                            <a href="{{ route('rekam-medis.create') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md transition-colors">
                                Tambah Rekam Medis
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('rekam-medis.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md transition-colors">
                                Daftar Rekam Medis
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('surat-sakit.create') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md transition-colors">
                                Surat Sakit
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Report (Dropdown) -->
                <li x-data="{ open: false }">
                    <button @click="sidebarOpen && (open = !open)"
                            class="flex items-center justify-between w-full px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md transition-colors"
                            :title="!sidebarOpen ? 'Report' : ''">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Report</span>
                        </div>
                        <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <ul x-show="open && sidebarOpen" x-transition class="mt-1 ml-8 space-y-1">
                        <li>
                            <a href="{{ route('kunjungan.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md transition-colors">
                                Daftar Kunjungan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('laporan.transaksi') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md transition-colors">
                                Laporan Transaksi
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>
