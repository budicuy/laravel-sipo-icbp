@extends('layouts.app')

@section('title', 'Monitoring Token Emergency')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Monitoring Token Emergency</h1>
                <p class="text-gray-600 mt-1">Pantau status token dan permintaan secara real-time</p>
            </div>
        </div>
    </div>

    <!-- Warning for Low Token Users -->
    @if($usersWithLowTokens->count() > 0)
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.964-5.964A6 6 0 0112 8.257v4.18m0 0a.75.75 0 01.75-.75V8.257m0 0h-.005z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Peringatan: Token Rendah</h3>
                <p class="text-sm text-red-700 mt-1">
                    Ada {{ $usersWithLowTokens->count() }} pengguna dengan token kurang dari 5.
                </p>
            </div>
        </div>
    </div>
    @endif
    <!-- Quick Actions -->
        <div class="flex mb-5 gap-3">
            <a href="{{ route('token-emergency.create') }}"
               class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Generate Token
            </a>

            <button onclick="showAllRequestsModal()"
               class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Kelola Permintaan
                @if($pendingRequestsCount > 0)
                <span class="ml-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                    {{ $pendingRequestsCount }}
                </span>
                @endif
            </button>

            <button onclick="exportAuditTrail()"
               class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Riwayat
            </button>
        </div>


    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="showTab('overview')" id="overview-tab" class="tab-button py-4 px-6 border-b-2 font-medium text-sm border-indigo-500 text-indigo-600 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Ringkasan
                </button>
                <button onclick="showTab('manage')" id="manage-tab" class="tab-button py-4 px-6 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    Kelola Token
                </button>
                <button onclick="showTab('audit')" id="audit-tab" class="tab-button py-4 px-6 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Riwayat Token
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Overview Tab Content -->
            <div id="overview-tab-content" class="tab-content">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-400 rounded-lg mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium opacity-90">Total Token</p>
                                <p class="text-2xl font-bold">{{ \App\Models\TokenEmergency::count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-400 rounded-lg mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium opacity-90">Token Tersedia</p>
                                <p class="text-2xl font-bold">{{ \App\Models\TokenEmergency::where('status', 'available')->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg p-4 text-white">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-400 rounded-lg mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium opacity-90">Permintaan</p>
                                <p class="text-2xl font-bold">{{ $pendingRequestsCount }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-4 text-white">
                        <div class="flex items-center">
                            <div class="p-3 bg-red-400 rounded-lg mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium opacity-90">Token Rendah</p>
                                <p class="text-2xl font-bold">{{ $usersWithLowTokens->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Low Token Users -->
                    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Pengguna dengan Token Rendah</h3>
                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                < 5 Token
                            </span>
                        </div>

                        @if($usersWithLowTokens->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Token</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($usersWithLowTokens as $user)
                                        <tr>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $user->nama_lengkap }}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    {{ $user->available_tokens }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm">
                                                <a href="{{ route('token-emergency.create') }}?user_id={{ $user->id_user }}"
                                                   class="text-purple-600 hover:text-purple-900 font-medium">
                                                    Generate
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-gray-500">Semua pengguna memiliki token yang cukup</p>
                            </div>
                        @endif
                    </div>

                    <!-- Pending Requests -->
                    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Permintaan Token Menunggu</h3>
                            @if($pendingRequestsCount > 0)
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                {{ $pendingRequestsCount }} Permintaan
                            </span>
                            @endif
                        </div>

                        @if($pendingRequests->count() > 0)
                            <div class="space-y-3">
                                @foreach($pendingRequests as $request)
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $request->requester->nama_lengkap }}</p>
                                            <p class="text-xs text-gray-500">Meminta {{ $request->request_quantity }} token</p>
                                            <p class="text-xs text-gray-400">{{ $request->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <form action="{{ route('token-emergency.approve-request', $request->id_token) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-green-600 text-white text-xs rounded px-2 py-1 hover:bg-green-700">
                                                    Setujui
                                                </button>
                                            </form>
                                            <button onclick="showRejectModal({{ $request->id_token }})"
                                                    class="bg-red-600 text-white text-xs rounded px-2 py-1 hover:bg-red-700">
                                                Tolak
                                            </button>
                                        </div>
                                    </div>
                                    @if($request->notes)
                                    <p class="text-xs text-gray-600 mt-2">Catatan: {{ $request->notes }}</p>
                                    @endif
                                </div>
                                @endforeach
                            </div>

                            @if($pendingRequestsCount > $pendingRequests->count())
                            <div class="mt-3 text-center">
                                <button onclick="showAllRequestsModal()" class="text-purple-600 hover:text-purple-900 text-sm font-medium">
                                    Lihat semua permintaan ({{ $pendingRequestsCount }})
                                </button>
                            </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-gray-500">Tidak ada permintaan token yang menunggu</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Manage Token Tab Content -->
            <div id="manage-tab-content" class="tab-content hidden">
                <!-- Table Header -->
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4 rounded-t-lg">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            Data Token Emergency
                        </h3>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('token-emergency.create') }}" class="px-3 py-1.5 bg-white text-purple-600 hover:bg-purple-50 text-sm font-medium rounded-lg shadow transition-all">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Generate Token
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-b-lg shadow border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-800">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">No</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Token</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Pengguna</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Digunakan Pada</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Dibuat Pada</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="manageTokensTableBody">
                                <!-- Will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50" id="manageTokensPagination">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Audit Trail Tab Content -->
            <div id="audit-tab-content" class="tab-content hidden">
                <!-- Table Header -->
                <div class="bg-gradient-to-r from-blue-600 to-teal-600 px-6 py-4 rounded-t-lg">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Riwayat Token
                        </h3>
                        <div class="flex items-center gap-2">
                            <select id="auditFilter" onchange="filterAuditTrail()" class="text-sm border-gray-300 rounded-md focus:ring-white focus:border-white bg-white text-black font-semibold px-3 py-1.5">
                                <option class="text-black" value="">Semua Status</option>
                                <option class="text-black" value="available">Tersedia</option>
                                <option class="text-black" value="used">Digunakan</option>
                                <option class="text-black" value="expired">Kadaluarsa</option>
                            </select>
                            <div class="relative">
                                <input type="text" id="searchToken" placeholder="Cari token atau pengguna..."
                                       class="text-sm border-gray-300 rounded-md focus:ring-white focus:border-white bg-white text-black placeholder-gray-300 pl-8 pr-4 py-1.5 w-64"
                                       onkeyup="filterAuditTrail()">
                                <svg class="w-4 h-4 absolute left-2.5 top-2.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <button onclick="exportAuditTrail()" class="px-3 py-1.5 bg-white text-blue-600 hover:bg-blue-50 text-sm font-medium rounded-lg shadow transition-all">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-b-lg shadow border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-blue-500 to-teal-500">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-blue-400">No</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-blue-400">Token</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-blue-400">Pemilik</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-blue-400">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-blue-400">Generator</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-blue-400">Dibuat</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-blue-400">Digunakan</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-blue-100" id="auditTableBody">
                                <!-- Will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-blue-200 bg-blue-50" id="auditPagination">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Tolak Permintaan Token</h3>
            <div class="mt-2 px-7 py-3">
                <form id="rejectForm" action="" method="POST">
                    @csrf
                    <input type="hidden" name="request_id" id="request_id">
                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                                  placeholder="Jelaskan alasan penolakan..."></textarea>
                    </div>
                    <div class="flex justify-center space-x-3">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Tolak
                        </button>
                        <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- All Requests Modal -->
<div id="allRequestsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Semua Permintaan Token</h3>
                <button onclick="closeAllRequestsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">No</th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Pemohon</th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Jumlah</th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Catatan</th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-2 text-center text-xs font-bold text-gray-900 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="allRequestsTableBody">
                        <!-- Will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function showRejectModal(requestId) {
    document.getElementById('request_id').value = requestId;
    document.getElementById('rejectForm').action = `/token-emergency/reject-request/${requestId}`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function showAllRequestsModal() {
    // Fetch all pending requests
    fetch(`/api/token-emergency/pending-requests`)
        .then(response => response.json())
        .then(data => {
            let tbody = document.getElementById('allRequestsTableBody');
            tbody.innerHTML = '';

            if (data.requests && data.requests.length > 0) {
                data.requests.forEach((request, index) => {
                    let row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${index + 1}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                    <span class="text-sm font-medium text-purple-600">
                                        ${request.requester.nama_lengkap.charAt(0)}
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">${request.requester.nama_lengkap}</div>
                                    <div class="text-xs text-gray-500">${request.requester.username}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                ${request.request_quantity} Token
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 max-w-xs">
                            <div class="truncate" title="${request.notes || '-'}">
                                ${request.notes || '-'}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            <div>
                                <div class="text-sm">${request.created_at_formatted}</div>
                                <div class="text-xs text-gray-500">${request.time_ago}</div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <form action="/token-emergency/approve-request/${request.id_token}" method="POST" class="inline">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button type="submit" class="bg-green-600 text-white text-xs rounded px-3 py-1.5 hover:bg-green-700 transition-colors">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Setujui
                                    </button>
                                </form>
                                <button onclick="showRejectModal(${request.id_token})"
                                        class="bg-red-600 text-white text-xs rounded px-3 py-1.5 hover:bg-red-700 transition-colors">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Tolak
                                </button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                let row = document.createElement('tr');
                row.innerHTML = `
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-lg font-medium">Tidak ada permintaan token yang menunggu</p>
                        <p class="text-sm mt-1">Semua permintaan telah diproses</p>
                    </td>
                `;
                tbody.appendChild(row);
            }

            document.getElementById('allRequestsModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error fetching requests:', error);
        });
}

function closeAllRequestsModal() {
    document.getElementById('allRequestsModal').classList.add('hidden');
}

function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-indigo-500', 'text-indigo-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab content
    document.getElementById(tabName + '-tab-content').classList.remove('hidden');

    // Add active state to selected tab
    const activeTab = document.getElementById(tabName + '-tab');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-indigo-500', 'text-indigo-600');

    // Load data based on selected tab
    if (tabName === 'audit') {
        // Check if there are URL parameters when switching to audit tab
        const currentUrl = new URL(window.location.href);
        const params = currentUrl.searchParams;

        if (params.toString()) {
            loadAuditTrail(currentUrl.toString());
        } else {
            loadAuditTrail();
        }
    } else if (tabName === 'manage') {
        // Check if there are URL parameters when switching to manage tab
        const currentUrl = new URL(window.location.href);
        const params = currentUrl.searchParams;

        if (params.toString()) {
            loadManageTokens(currentUrl.toString());
        } else {
            loadManageTokens();
        }
    }
}

function loadManageTokens(customUrl = null) {
    // Show loading state
    const tbody = document.getElementById('manageTokensTableBody');
    const pagination = document.getElementById('manageTokensPagination');

    tbody.innerHTML = `
        <tr>
            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                <svg class="w-8 h-8 mx-auto text-gray-400 mb-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <p class="text-sm">Memuat data token...</p>
            </td>
        </tr>
    `;

    pagination.innerHTML = `
        <div class="flex justify-center">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-purple-500 mx-auto"></div>
        </div>
    `;

    // Determine the URL to use
    let fetchUrl = '/api/token-emergency/manage-tokens';

    if (customUrl) {
        // Extract query parameters from the custom URL
        const urlObj = new URL(customUrl, window.location.origin);
        const params = urlObj.searchParams;

        // Build the API URL with parameters
        if (params.toString()) {
            fetchUrl += '?' + params.toString();
        }
    } else {
        // Check current URL for parameters
        const currentUrl = new URL(window.location.href);
        const params = currentUrl.searchParams;

        // Build the API URL with parameters
        if (params.toString()) {
            fetchUrl += '?' + params.toString();
        }
    }

    // Fetch tokens data
    fetch(fetchUrl)
        .then(response => response.json())
        .then(data => {
            // Populate table
            tbody.innerHTML = '';

            if (data.tokens && data.tokens.data.length > 0) {
                data.tokens.data.forEach((token, index) => {
                    let row = document.createElement('tr');
                    row.className = 'hover:bg-purple-50 transition-colors';
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            ${data.tokens.from + index}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                ${token.token}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            ${token.status_badge}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            ${token.user ? token.user.nama_lengkap : '-'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            ${token.used_at_formatted || '-'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            ${token.created_at_formatted}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            ${token.status === 'available' ? `
                                <button type="button"
                                        class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded delete-btn"
                                        data-id="${token.id_token}"
                                        data-token="${token.token}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            ` : '<span class="text-gray-400">-</span>'}
                        </td>
                    `;
                    tbody.appendChild(row);
                });

                // Populate pagination
                if (data.tokens.links && data.tokens.links.length > 3) {
                    pagination.innerHTML = generatePagination(data.tokens);
                } else {
                    pagination.innerHTML = `
                        <div class="text-sm text-gray-600 text-center">
                            Menampilkan ${data.tokens.from} hingga ${data.tokens.to} dari ${data.tokens.total} data
                        </div>
                    `;
                }

                // Attach delete event listeners
                attachDeleteListeners();
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            <p class="text-lg font-medium">Tidak ada data token</p>
                            <p class="text-sm mt-1">Silakan generate token untuk mulai menggunakan fitur emergency</p>
                        </td>
                    </tr>
                `;
                pagination.innerHTML = '';
            }
        })
        .catch(error => {
            console.error('Error loading tokens:', error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-red-500">
                        <svg class="w-16 h-16 mx-auto text-red-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-lg font-medium">Gagal memuat data token</p>
                        <p class="text-sm mt-1">Terjadi kesalahan saat mengambil data</p>
                    </td>
                </tr>
            `;
            pagination.innerHTML = '';
        });
}

function generatePagination(data) {
    let html = `
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-sm text-gray-600">
                Halaman <span class="font-semibold text-gray-900">${data.current_page}</span>
                dari <span class="font-semibold text-gray-900">${data.last_page}</span>
                <span class="mx-2 text-gray-400">•</span>
                Total <span class="font-semibold text-gray-900">${data.total}</span> data
            </div>

            <nav class="flex items-center gap-2" role="navigation">
    `;

    // Previous button
    if (data.prev_page_url) {
        html += `<a href="javascript:void(0)" onclick="loadManageTokensPage('${data.prev_page_url}')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">Previous</a>`;
    } else {
        html += `<span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>`;
    }

    // Page numbers
    html += '<div class="flex items-center gap-1">';

    // Calculate visible page range
    let start = Math.max(1, data.current_page - 2);
    let end = Math.min(data.last_page, data.current_page + 2);

    // First page
    if (start > 1) {
        html += `<a href="javascript:void(0)" onclick="loadManageTokensPage('${data.first_page_url}')" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">1</a>`;
        if (start > 2) {
            html += `<span class="px-2 text-gray-500">...</span>`;
        }
    }

    // Page numbers
    for (let i = start; i <= end; i++) {
        let url = data.links.find(link => link.label == i.toString())?.url || '';
        if (i == data.current_page) {
            html += `<span class="px-3 py-2 text-sm font-bold text-white bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg shadow-md">${i}</span>`;
        } else {
            html += `<a href="javascript:void(0)" onclick="loadManageTokensPage('${url}')" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">${i}</a>`;
        }
    }

    // Last page
    if (end < data.last_page) {
        if (end < data.last_page - 1) {
            html += `<span class="px-2 text-gray-500">...</span>`;
        }
        html += `<a href="javascript:void(0)" onclick="loadManageTokensPage('${data.last_page_url}')" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">${data.last_page}</a>`;
    }

    html += '</div>';

    // Next button
    if (data.next_page_url) {
        html += `<a href="javascript:void(0)" onclick="loadManageTokensPage('${data.next_page_url}')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition-all">Next</a>`;
    } else {
        html += `<span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>`;
    }

    html += `
            </nav>
        </div>
    `;

    return html;
}

function loadManageTokensPage(url) {
    // Update the URL with the page parameter
    const newUrl = new URL(url, window.location.origin);
    window.history.pushState({}, '', newUrl);

    // Reload the tokens with the new URL
    loadManageTokens(url);
}

function attachDeleteListeners() {
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const token = this.getAttribute('data-token');

            Swal.fire({
                title: 'Konfirmasi Hapus Token',
                html: `Apakah Anda yakin ingin menghapus token <strong>${token}</strong>?<br><small class="text-red-500">Tindakan ini tidak dapat dibatalkan.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                showLoaderOnConfirm: true,
                preConfirm: function () {
                    return new Promise(function(resolve) {
                        // Create form element
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/token-emergency/${id}`;

                        // Add CSRF token
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        form.appendChild(csrfToken);

                        // Add DELETE method
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        form.appendChild(methodInput);

                        // Submit form
                        document.body.appendChild(form);
                        form.submit();
                    });
                }
            });
        });
    });
}

function loadAuditTrail(customUrl = null) {
    // Show loading state
    const tbody = document.getElementById('auditTableBody');
    const pagination = document.getElementById('auditPagination');

    tbody.innerHTML = `
        <tr>
            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                <svg class="w-8 h-8 mx-auto text-gray-400 mb-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <p class="text-sm">Memuat riwayat token...</p>
            </td>
        </tr>
    `;

    pagination.innerHTML = `
        <div class="flex justify-center">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500 mx-auto"></div>
        </div>
    `;

    // Determine the URL to use
    let fetchUrl = '/api/token-emergency/audit-trail';

    if (customUrl) {
        // Extract query parameters from the custom URL
        const urlObj = new URL(customUrl, window.location.origin);
        const params = urlObj.searchParams;

        // Build the API URL with parameters
        if (params.toString()) {
            fetchUrl += '?' + params.toString();
        }
    } else {
        // Check current URL for parameters
        const currentUrl = new URL(window.location.href);
        const params = currentUrl.searchParams;

        // Build the API URL with parameters
        if (params.toString()) {
            fetchUrl += '?' + params.toString();
        }
    }

    // Fetch audit trail data
    fetch(fetchUrl)
        .then(response => response.json())
        .then(data => {
            tbody.innerHTML = '';

            if (data.tokens && data.tokens.data.length > 0) {
                data.tokens.data.forEach((token, index) => {
                    let row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${data.tokens.from + index}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                ${token.token}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${token.user ? token.user.nama_lengkap : '-'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${token.status_badge}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${token.generator ? token.generator.nama_lengkap : '-'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>
                                <div class="text-sm">${token.created_at_formatted}</div>
                                <div class="text-xs text-gray-500">${token.time_ago}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${token.used_at_formatted || '-'}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                            <div class="truncate" title="${token.notes || '-'}">
                                ${token.notes || '-'}
                            </div>
                        </td>
                    `;
                    tbody.appendChild(row);
                });

                // Populate pagination
                if (data.tokens.links && data.tokens.links.length > 3) {
                    pagination.innerHTML = generateAuditPagination(data.tokens);
                } else {
                    pagination.innerHTML = `
                        <div class="text-sm text-gray-600 text-center">
                            Menampilkan ${data.tokens.from} hingga ${data.tokens.to} dari ${data.tokens.total} data
                        </div>
                    `;
                }
            } else {
                let row = document.createElement('tr');
                row.innerHTML = `
                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-lg font-medium">Tidak ada riwayat token</p>
                        <p class="text-sm mt-1">Belum ada aktivitas token yang tercatat</p>
                    </td>
                `;
                tbody.appendChild(row);
                pagination.innerHTML = '';
            }
        })
        .catch(error => {
            console.error('Error loading audit trail:', error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-red-500">
                        <svg class="w-16 h-16 mx-auto text-red-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-lg font-medium">Gagal memuat riwayat token</p>
                        <p class="text-sm mt-1">Terjadi kesalahan saat mengambil data</p>
                    </td>
                </tr>
            `;
            pagination.innerHTML = '';
        });
}

function generateAuditPagination(data) {
    let html = `
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-sm text-gray-600">
                Halaman <span class="font-semibold text-gray-900">${data.current_page}</span>
                dari <span class="font-semibold text-gray-900">${data.last_page}</span>
                <span class="mx-2 text-gray-400">•</span>
                Total <span class="font-semibold text-gray-900">${data.total}</span> data
            </div>

            <nav class="flex items-center gap-2" role="navigation">
    `;

    // Previous button
    if (data.prev_page_url) {
        html += `<a href="javascript:void(0)" onclick="loadAuditTrailPage('${data.prev_page_url}')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">Previous</a>`;
    } else {
        html += `<span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>`;
    }

    // Page numbers
    html += '<div class="flex items-center gap-1">';

    // Calculate visible page range
    let start = Math.max(1, data.current_page - 2);
    let end = Math.min(data.last_page, data.current_page + 2);

    // First page
    if (start > 1) {
        html += `<a href="javascript:void(0)" onclick="loadAuditTrailPage('${data.first_page_url}')" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">1</a>`;
        if (start > 2) {
            html += `<span class="px-2 text-gray-500">...</span>`;
        }
    }

    // Page numbers
    for (let i = start; i <= end; i++) {
        let url = data.links.find(link => link.label == i.toString())?.url || '';
        if (i == data.current_page) {
            html += `<span class="px-3 py-2 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-teal-600 rounded-lg shadow-md">${i}</span>`;
        } else {
            html += `<a href="javascript:void(0)" onclick="loadAuditTrailPage('${url}')" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">${i}</a>`;
        }
    }

    // Last page
    if (end < data.last_page) {
        if (end < data.last_page - 1) {
            html += `<span class="px-2 text-gray-500">...</span>`;
        }
        html += `<a href="javascript:void(0)" onclick="loadAuditTrailPage('${data.last_page_url}')" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">${data.last_page}</a>`;
    }

    html += '</div>';

    // Next button
    if (data.next_page_url) {
        html += `<a href="javascript:void(0)" onclick="loadAuditTrailPage('${data.next_page_url}')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-all">Next</a>`;
    } else {
        html += `<span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>`;
    }

    html += `
            </nav>
        </div>
    `;

    return html;
}

function loadAuditTrailPage(url) {
    // Update the URL with the page parameter
    const newUrl = new URL(url, window.location.origin);
    window.history.pushState({}, '', newUrl);

    // Reload the audit trail
    loadAuditTrail(url);
}

function filterAuditTrail() {
    const status = document.getElementById('auditFilter').value;
    const searchTerm = document.getElementById('searchToken').value;

    // Show loading state
    loadAuditTrail();

    // Apply filter after loading
    setTimeout(() => {
        const rows = document.querySelectorAll('#auditTableBody tr');
        rows.forEach(row => {
            let showRow = true;

            // Filter by status
            if (status !== '') {
                const statusCell = row.querySelector('td:nth-child(4)');
                if (statusCell && !statusCell.textContent.toLowerCase().includes(status.toLowerCase())) {
                    showRow = false;
                }
            }

            // Filter by search term
            if (searchTerm !== '') {
                const rowText = row.textContent.toLowerCase();
                if (!rowText.includes(searchTerm.toLowerCase())) {
                    showRow = false;
                }
            }

            row.style.display = showRow ? '' : 'none';
        });
    }, 500);
}

function exportAuditTrail() {
    // This would typically trigger a download of the audit trail data
    // For now, we'll just show a notification
    Swal.fire({
        icon: 'info',
        title: 'Fitur Export',
        text: 'Fitur export CSV akan segera tersedia.',
        confirmButtonText: 'OK'
    });
}

// Initialize with overview tab
document.addEventListener('DOMContentLoaded', function() {
    showTab('overview');
});
</script>
@endsection
