<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Dashboard - SIPO ICBP</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 dark:bg-gray-900">
        <!-- Navbar -->
        <nav class="bg-white dark:bg-gray-800 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white">SIPO - ICBP</h1>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ route('dashboard') }}" class="border-blue-500 text-gray-900 dark:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Dashboard
                            </a>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="flex items-center">
                        <div class="ml-3 relative">
                            <div class="flex items-center space-x-4">
                                <div class="text-right hidden sm:block">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $user->nama_lengkap }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $user->role }}
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md">
                        <p class="text-sm text-green-600 dark:text-green-400">{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md">
                        <p class="text-sm text-red-600 dark:text-red-400">{{ session('error') }}</p>
                    </div>
                @endif

                <!-- Dashboard Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Card 1 -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5">
                                    <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Pasien</h3>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="ml-5">
                                    <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">Rekam Medis</h3>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                    </svg>
                                </div>
                                <div class="ml-5">
                                    <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">Stok Obat</h3>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">53</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Welcome Section -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            Selamat Datang, {{ $user->nama_lengkap }}!
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Anda login sebagai <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $user->role }}</span>
                        </p>

                        <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Informasi Akun</h3>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Username</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->username }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Lengkap</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->nama_lengkap }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Role</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                            @if($user->isSuperAdmin()) bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                            @elseif($user->isAdmin()) bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @endif">
                                            {{ $user->role }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Terdaftar Sejak</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('d F Y') : '-' }}</dd>
                                </div>
                            </dl>
                        </div>

                        @if($user->isSuperAdmin())
                            <div class="mt-6 p-4 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-md">
                                <p class="text-sm text-purple-700 dark:text-purple-300">
                                    <strong>Super Admin:</strong> Anda memiliki akses penuh ke seluruh sistem.
                                </p>
                            </div>
                        @elseif($user->isAdmin())
                            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md">
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    <strong>Admin:</strong> Anda dapat mengelola data pasien, rekam medis, dan obat.
                                </p>
                            </div>
                        @else
                            <div class="mt-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md">
                                <p class="text-sm text-green-700 dark:text-green-300">
                                    <strong>User:</strong> Anda dapat melihat dan mengelola data pasien.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-800 shadow-sm mt-8">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                    &copy; {{ date('Y') }} SIPO - ICBP. All rights reserved.
                </p>
            </div>
        </footer>
    </body>
</html>
