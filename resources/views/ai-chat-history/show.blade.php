@extends('layouts.app')

@section('title', 'Detail Riwayat AI Chat - ' . $userDetail->nama_karyawan)

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-200">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('ai-chat-history.index') }}"
                    class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $userDetail->nama_karyawan }}</h1>
                    <p class="text-gray-600 mt-1">NIK: {{ $userDetail->nik }} • {{ $userDetail->departemen ?? 'Tidak Ada
                        Departemen' }}</p>
                </div>
            </div>
            <span
                class="px-4 py-2 bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 rounded-full text-sm font-semibold shadow-sm">
                {{ $userDetail->ai_chat_access_count }} AI Chat
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-sm p-6 border border-blue-200">
            <div class="flex items-center justify-between">
                <div class="p-3 bg-blue-600 rounded-lg shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-blue-700">Total Login</p>
                    <p class="text-3xl font-bold text-blue-900">{{ number_format($userDetail->login_count) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-sm p-6 border border-purple-200">
            <div class="flex items-center justify-between">
                <div class="p-3 bg-purple-600 rounded-lg shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-purple-700">Akses AI Chat</p>
                    <p class="text-3xl font-bold text-purple-900">{{ number_format($userDetail->ai_chat_access_count) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-sm p-6 border border-green-200">
            <div class="flex items-center justify-between">
                <div class="p-3 bg-green-600 rounded-lg shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-green-700">Login Terakhir</p>
                    <p class="text-base font-bold text-green-900">
                        {{ $userDetail->last_login_at ? $userDetail->last_login_at->format('d/m/Y H:i') : 'Belum Pernah'
                        }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl shadow-sm p-6 border border-orange-200">
            <div class="flex items-center justify-between">
                <div class="p-3 bg-orange-600 rounded-lg shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-orange-700">AI Chat Terakhir</p>
                    <p class="text-base font-bold text-orange-900">
                        {{ $userDetail->last_ai_chat_access_at ? $userDetail->last_ai_chat_access_at->format('d/m/Y
                        H:i') : 'Belum Pernah' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Timeline Aktivitas</h2>

        <div class="space-y-6">
            @if ($userDetail->last_ai_chat_access_at)
            <div class="flex items-start gap-4">
                <div
                    class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <div
                        class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-5 border border-purple-200 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-900 text-lg">Akses AI Chat</h3>
                                <p class="text-sm text-gray-600">Terakhir mengakses fitur AI Chat</p>
                            </div>
                            <span class="text-sm font-medium text-purple-700 bg-purple-200 px-3 py-1 rounded-full">
                                {{ $userDetail->last_ai_chat_access_at->diffForHumans() }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            Total akses AI Chat: <strong>{{ number_format($userDetail->ai_chat_access_count) }}</strong>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 ml-1">
                        {{ $userDetail->last_ai_chat_access_at->translatedFormat('l, d F Y H:i') }}
                    </p>
                </div>
            </div>
            @endif

            @if ($userDetail->last_login_at)
            <div class="flex items-start gap-4">
                <div
                    class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                </div>
                <div class="flex-1">
                    <div
                        class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-5 border border-blue-200 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-900 text-lg">Aktivitas Login</h3>
                                <p class="text-sm text-gray-600">Login terakhir ke sistem</p>
                            </div>
                            <span class="text-sm font-medium text-blue-700 bg-blue-200 px-3 py-1 rounded-full">
                                {{ $userDetail->last_login_at->diffForHumans() }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Total login: <strong>{{ number_format($userDetail->login_count) }}</strong>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 ml-1">
                        {{ $userDetail->last_login_at->translatedFormat('l, d F Y H:i') }}
                    </p>
                </div>
            </div>
            @endif

            <div class="flex items-start gap-4">
                <div
                    class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <div
                        class="bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-5 border border-green-200 shadow-sm">
                        <div class="mb-3">
                            <h3 class="font-semibold text-gray-900 text-lg">Informasi Pengguna</h3>
                            <p class="text-sm text-gray-600">Detail informasi dasar</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                                <strong>NIK:</strong> {{ $userDetail->nik }}
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <strong>Departemen:</strong> {{ $userDetail->departemen ?? 'Belum Ditugaskan' }}
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <strong>Tercatat Sejak:</strong> {{ $userDetail->created_at->format('d/m/Y') }}
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <strong>Tingkat Engagement:</strong>
                                {{ $userDetail->login_count > 0 ? round(($userDetail->ai_chat_access_count /
                                $userDetail->login_count) * 100, 1) : 0 }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Metrik Engagement</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                    Pola Penggunaan AI Chat
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                        <span class="text-sm text-gray-700">AI Chat per Login</span>
                        <span class="font-bold text-lg text-gray-900">
                            {{ $userDetail->login_count > 0 ? round($userDetail->ai_chat_access_count /
                            $userDetail->login_count, 2) : 0 }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                        <span class="text-sm text-gray-700">Frekuensi Penggunaan</span>
                        <span class="px-3 py-1.5 rounded-full text-xs font-bold
                            @if ($userDetail->ai_chat_access_count >= 20) bg-green-100 text-green-800
                            @elseif ($userDetail->ai_chat_access_count >= 10) bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            @if ($userDetail->ai_chat_access_count >= 20) Tinggi
                            @elseif ($userDetail->ai_chat_access_count >= 10) Sedang
                            @else Rendah
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Status Aktivitas
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                        <span class="text-sm text-gray-700">Aktivitas Terakhir</span>
                        <span class="font-bold text-sm text-gray-900">
                            @if ($userDetail->last_ai_chat_access_at)
                            {{ $userDetail->last_ai_chat_access_at->diffForHumans() }}
                            @elseif ($userDetail->last_login_at)
                            {{ $userDetail->last_login_at->diffForHumans() }}
                            @else
                            Belum Pernah
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                        <span class="text-sm text-gray-700">Status Akun</span>
                        <span class="px-3 py-1.5 rounded-full text-xs font-bold
                            @if ($userDetail->last_ai_chat_access_at && $userDetail->last_ai_chat_access_at->gt(now()->subDays(7))) bg-green-100 text-green-800
                            @elseif ($userDetail->last_ai_chat_access_at && $userDetail->last_ai_chat_access_at->gt(now()->subDays(30))) bg-yellow-100 text-yellow-800
                            @else bg-gray-300 text-gray-800
                            @endif">
                            @if ($userDetail->last_ai_chat_access_at &&
                            $userDetail->last_ai_chat_access_at->gt(now()->subDays(7))) Aktif
                            @elseif ($userDetail->last_ai_chat_access_at &&
                            $userDetail->last_ai_chat_access_at->gt(now()->subDays(30))) Tidak Aktif
                            @else Tidak Aktif
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection