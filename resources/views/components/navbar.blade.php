<header class="bg-white shadow-sm h-16 flex items-center justify-between px-6">
    <div class="flex items-center space-x-4">
        <!-- Toggle Sidebar Button -->
        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md p-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <h1 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
    </div>
    
    <!-- User Info & Logout -->
    <div class="flex items-center space-x-4">
        <div class="text-right">
            <div class="text-sm font-medium text-gray-900">
                {{ auth()->user()->nama_lengkap }}
            </div>
            <div class="text-xs text-gray-500">
                {{ auth()->user()->role }}
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                Logout
            </button>
        </form>
    </div>
</header>
