<aside class="bg-gradient-to-b from-white to-gray-50 flex-shrink-0 transition-all duration-300 border-r border-gray-200" :class="sidebarOpen ? 'w-64' : 'w-20'">
    <div class="h-full flex flex-col">
        <!-- Logo & Toggle -->
        <div class="flex items-center justify-between h-20 border-b border-gray-200 px-4 bg-white">
            <div class="flex items-center gap-3" x-show="sidebarOpen">
                <img src="{{ asset('logo.png') }}" alt="ICBP Logo" class="h-10 w-auto transition-all duration-300">
            </div>
            <img src="{{ asset('logo.png') }}" alt="ICBP Logo" class="h-8 w-auto mx-auto" x-show="!sidebarOpen">
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto py-6 px-3" x-data="{ activeMenu: '{{ request()->segment(1) ?? 'dashboard' }}' }">
            <ul class="space-y-2">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}"
                       class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}"
                       :title="!sidebarOpen ? 'Dashboard' : ''"
                       @click="activeMenu = 'dashboard'">
                        <div class="relative">
                            <svg class="w-6 h-6 flex-shrink-0 transition-transform group-hover:scale-110" :class="sidebarOpen ? 'mr-3' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            @if(request()->routeIs('dashboard'))
                            @endif
                        </div>
                        <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Dashboard</span>
                    </a>
                </li>

                <!-- Master Data (Dropdown) -->
                <li x-data="{ open: {{ request()->is('karyawan*') || request()->is('keluarga*') || request()->is('obat*') || request()->is('diagnosa*') || request()->is('user*') ? 'true' : 'false' }} }">
                    <button @click="if (!sidebarOpen) { sidebarOpen = true; open = true; } else { open = !open; }"
                            class="group flex items-center justify-between w-full px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('karyawan*') || request()->is('keluarga*') || request()->is('obat*') || request()->is('diagnosa*') || request()->is('user*') ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}"
                            :title="!sidebarOpen ? 'Master Data' : ''">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 flex-shrink-0 transition-transform group-hover:scale-110" :class="sidebarOpen ? 'mr-3' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                            <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Master Data</span>
                        </div>
                        <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <ul x-show="open && sidebarOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="mt-2 ml-12 space-y-1 border-l-2 border-purple-200 pl-4">
                        <li>
                            <a href="{{ route('karyawan.index') }}"
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-all {{ request()->is('karyawan*') ? 'text-purple-600 bg-purple-50 font-semibold' : 'text-gray-600 hover:text-purple-600 hover:bg-purple-50' }}">
                                Data Karyawan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('keluarga.index') }}"
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-all {{ request()->is('keluarga*') ? 'text-purple-600 bg-purple-50 font-semibold' : 'text-gray-600 hover:text-purple-600 hover:bg-purple-50' }}">
                                Data Keluarga
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('obat.index') }}"
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-all {{ request()->is('obat*') ? 'text-purple-600 bg-purple-50 font-semibold' : 'text-gray-600 hover:text-purple-600 hover:bg-purple-50' }}">
                                Data Obat
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('diagnosa.index') }}"
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-all {{ request()->is('diagnosa*') ? 'text-purple-600 bg-purple-50 font-semibold' : 'text-gray-600 hover:text-purple-600 hover:bg-purple-50' }}">
                                Data Diagnosa
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.index') }}"
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-all {{ request()->is('user*') ? 'text-purple-600 bg-purple-50 font-semibold' : 'text-gray-600 hover:text-purple-600 hover:bg-purple-50' }}">
                                Data User
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Stok Obat (Main Menu) -->
                <li>
                    <a href="{{ route('stok-obat.index') }}"
                       class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('stok-obat*') ? 'bg-gradient-to-r from-teal-500 to-cyan-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}"
                       :title="!sidebarOpen ? 'Stok Obat' : ''"
                       @click="activeMenu = 'stok-obat'">
                        <div class="relative">
                            <svg class="w-6 h-6 flex-shrink-0 transition-transform group-hover:scale-110" :class="sidebarOpen ? 'mr-3' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            @if(request()->is('stok-obat*'))
                            @endif
                        </div>
                        <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Stok Obat</span>
                    </a>
                </li>

                <!-- Rekam Medis (Dropdown) -->
                <li x-data="{ open: {{ request()->is('rekam-medis*') || request()->is('surat-sakit*') ? 'true' : 'false' }} }">
                    <button @click="if (!sidebarOpen) { sidebarOpen = true; open = true; } else { open = !open; }"
                            class="group flex items-center justify-between w-full px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('rekam-medis*') || request()->is('surat-sakit*') ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}"
                            :title="!sidebarOpen ? 'Rekam Medis' : ''">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 flex-shrink-0 transition-transform group-hover:scale-110" :class="sidebarOpen ? 'mr-3' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Rekam Medis</span>
                        </div>
                        <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <ul x-show="open && sidebarOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="mt-2 ml-12 space-y-1 border-l-2 border-green-200 pl-4">
                        <li>
                            <a href="{{ route('rekam-medis.create') }}"
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-all {{ request()->routeIs('rekam-medis.create') ? 'text-green-600 bg-green-50 font-semibold' : 'text-gray-600 hover:text-green-600 hover:bg-green-50' }}">
                                Tambah Rekam Medis
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('rekam-medis.index') }}"
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-all {{ request()->routeIs('rekam-medis.index') || request()->routeIs('rekam-medis.detail') ? 'text-green-600 bg-green-50 font-semibold' : 'text-gray-600 hover:text-green-600 hover:bg-green-50' }}">
                                Daftar Rekam Medis
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('surat-sakit.create') }}"
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-all {{ request()->is('surat-sakit*') ? 'text-green-600 bg-green-50 font-semibold' : 'text-gray-600 hover:text-green-600 hover:bg-green-50' }}">
                                Surat Sakit
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Report (Dropdown) -->
                <li x-data="{ open: {{ request()->is('kunjungan*') || request()->is('laporan*') ? 'true' : 'false' }} }">
                    <button @click="if (!sidebarOpen) { sidebarOpen = true; open = true; } else { open = !open; }"
                            class="group flex items-center justify-between w-full px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('kunjungan*') || request()->is('laporan*') ? 'bg-gradient-to-r from-orange-500 to-red-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}"
                            :title="!sidebarOpen ? 'Report' : ''">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 flex-shrink-0 transition-transform group-hover:scale-110" :class="sidebarOpen ? 'mr-3' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Report</span>
                        </div>
                        <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <ul x-show="open && sidebarOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="mt-2 ml-12 space-y-1 border-l-2 border-orange-200 pl-4">
                        <li>
                            <a href="{{ route('kunjungan.index') }}"
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-all {{ request()->is('kunjungan*') ? 'text-orange-600 bg-orange-50 font-semibold' : 'text-gray-600 hover:text-orange-600 hover:bg-orange-50' }}">
                                Daftar Kunjungan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('laporan.transaksi') }}"
                               class="flex items-center px-3 py-2 text-sm rounded-lg transition-all {{ request()->is('laporan*') ? 'text-orange-600 bg-orange-50 font-semibold' : 'text-gray-600 hover:text-orange-600 hover:bg-orange-50' }}">
                                Laporan Transaksi
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>

        <!-- Copyright Footer -->
        <div class="border-t border-gray-200 p-4 bg-white" x-show="sidebarOpen">
            <div class="text-center">
                <p class="text-xs text-gray-500 font-medium">© {{ date('Y') }} SIPO ICBP</p>
                <p class="text-xs text-gray-400 mt-1">Sistem Informasi Poliklinik</p>
            </div>
        </div>
    </div>
</aside>
