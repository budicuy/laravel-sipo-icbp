<header class="bg-white shadow-sm h-16 flex items-center justify-between px-6">
    <div class="flex items-center space-x-4">
        <!-- Toggle Sidebar Button -->
        <button @click="sidebarOpen = !sidebarOpen"
            class="text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md p-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <h1 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
    </div>

    <!-- Notifications & User Info & Logout -->
    <div class="flex items-center space-x-4" x-data="{ userMenuOpen: false, notificationOpen: false }">
        <!-- Token Request Notifications (Admin/Super Admin only) -->
        @if(auth()->user()->role === 'Super Admin' || auth()->user()->role === 'Admin')
        @php
        $pendingRequestsCount = \App\Models\TokenRequest::getPendingRequestsCount();
        @endphp
        <div class="relative">
            <button @click="notificationOpen = !notificationOpen" @click.away="notificationOpen = false"
                class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500 rounded-lg transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                @if($pendingRequestsCount > 0)
                <span
                    class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">
                    {{ $pendingRequestsCount }}
                </span>
                @endif
            </button>

            <!-- Notification Dropdown -->
            <div x-show="notificationOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50 overflow-hidden">

                <!-- Notification Header -->
                <div class="bg-linear-to-r from-purple-600 to-indigo-600 px-4 py-3 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-white">Notifikasi Token</h3>
                    <a href="{{ route('token-emergency.monitoring') }}"
                        class="text-xs text-white/80 hover:text-white transition-colors">
                        Lihat Semua
                    </a>
                </div>

                <!-- Notification Content -->
                @if($pendingRequestsCount > 0)
                <div class="max-h-64 overflow-y-auto">
                    @php
                    $pendingRequests = \App\Models\TokenRequest::with('requester')
                    ->where('status', \App\Models\TokenRequest::STATUS_PENDING)
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
                    @endphp
                    @foreach($pendingRequests as $request)
                    <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start">
                            <div class="shrink-0">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-gray-900">
                                    <span class="font-medium">{{ $request->requester->nama_lengkap }}</span>
                                    meminta {{ $request->request_quantity }} token
                                </p>
                                <p class="text-xs text-gray-500 mt-1">{{ $request->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="px-4 py-3 bg-gray-50 text-center">
                    <a href="{{ route('token-emergency.monitoring') }}"
                        class="text-sm font-medium text-purple-600 hover:text-purple-700">
                        Kelola Permintaan ({{ $pendingRequestsCount }})
                    </a>
                </div>
                @else
                <div class="px-4 py-6 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-gray-500">Tidak ada permintaan token yang menunggu</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- User Profile Dropdown -->
        <div class="relative">
            <button @click="userMenuOpen = !userMenuOpen" @click.away="userMenuOpen = false"
                class="flex items-center space-x-3 text-sm rounded-lg hover:bg-gray-50 p-2 pr-4 transition-all duration-200 group">
                <!-- User Avatar -->
                <div class="relative">
                    <div
                        class="w-10 h-10 rounded-full bg-linear-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-md group-hover:shadow-lg transition-shadow">
                        {{ substr(auth()->user()->nama_lengkap ?? 'A', 0, 1) }}
                    </div>
                    <div
                        class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 border-2 border-white rounded-full">
                    </div>
                </div>

                <!-- User Info -->
                <div class="text-left hidden md:block">
                    <div class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                        {{ auth()->user()->nama_lengkap }}
                    </div>
                    <div class="text-xs text-gray-500 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M6 3a1 1 0 011-1h.01a1 1 0 010 2H7a1 1 0 01-1-1zm2 3a1 1 0 00-2 0v1a2 2 0 00-2 2v1a2 2 0 00-2 2v.683a3.7 3.7 0 011.055.485 1.704 1.704 0 001.89 0 3.704 3.704 0 014.11 0 1.704 1.704 0 001.89 0 3.704 3.704 0 014.11 0 1.704 1.704 0 001.89 0A3.7 3.7 0 0118 12.683V12a2 2 0 00-2-2V9a2 2 0 00-2-2H6a2 2 0 00-2 2v1a2 2 0 00-2 2v.683a3.7 3.7 0 011.055.485 1.704 1.704 0 001.89 0 3.704 3.704 0 014.11 0 1.704 1.704 0 001.89 0 3.704 3.704 0 014.11 0 1.704 1.704 0 001.89 0A3.7 3.7 0 0118 12.683V12a2 2 0 00-2-2V9a2 2 0 00-2-2H6a2 2 0 00-2 2v1a2 2 0 00-2 2v.683a3.7 3.7 0 011.055.485 1.704 1.704 0 001.89 0 3.704 3.704 0 014.11 0 1.704 1.704 0 001.89 0 3.704 3.704 0 014.11 0 1.704 1.704 0 001.89 0A3.7 3.7 0 0118 12.683V12a2 2 0 00-2-2V9a2 2 0 00-2-2H6a2 2 0 00-2 2v1a2 2 0 00-2 2v.683a3.7 3.7 0 011.055.485 1.704 1.704 0 001.89 0 3.704 3.704 0 014.11 0 1.704 1.704 0 001.89 0 3.704 3.704 0 014.11 0 1.704 1.704 0 001.89 0A3.7 3.7 0 0118 12.683V12a2 2 0 00-2-2V9a2 2 0 00-2-2H6z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ auth()->user()->role }}
                    </div>
                </div>

                <!-- Dropdown Arrow -->
                <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-colors"
                    :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="userMenuOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50 overflow-hidden">

                <!-- User Profile Header -->
                <div class="bg-linear-to-r from-blue-500 to-purple-600 px-4 py-3">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-white font-bold">
                            {{ substr(auth()->user()->nama_lengkap ?? 'A', 0, 1) }}
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white">
                                {{ auth()->user()->nama_lengkap }}
                            </div>
                            <div class="text-xs text-white/80">
                                {{ auth()->user()->email ?? 'admin@sipo.com' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Items -->
                <div class="p-2">

                    <a href="/"
                        class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-50 transition-colors group">
                        <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-600" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Kembali ke Beranda
                    </a>
                    <div class="border-t border-gray-100 my-2"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center px-3 py-2 text-sm text-red-600 rounded-lg hover:bg-red-50 transition-colors group">
                            <svg class="w-5 h-5 mr-3 text-red-500 group-hover:text-red-600" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>