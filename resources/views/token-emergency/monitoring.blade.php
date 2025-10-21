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
                    <a href="#" class="underline font-medium">Lihat daftar pengguna</a>
                </p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Token Users -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Pengguna dengan Token Rendah</h2>
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
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Token Tersedia</th>
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
                                        Generate Token
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
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Permintaan Token Menunggu</h2>
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
                    <a href="{{ route('token-emergency.pending-requests') }}"
                       class="text-purple-600 hover:text-purple-900 text-sm font-medium">
                        Lihat semua permintaan ({{ $pendingRequestsCount }})
                    </a>
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

    <!-- Quick Actions -->
    <div class="mt-6 bg-white rounded-xl shadow-md border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('token-emergency.create') }}"
               class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Generate Token
            </a>

            <a href="{{ route('token-emergency.pending-requests') }}"
               class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Kelola Permintaan
            </a>

            <a href="{{ route('token-emergency.audit-trail') }}"
               class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Riwayat Token
            </a>
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

<script>
function showRejectModal(requestId) {
    document.getElementById('request_id').value = requestId;
    document.getElementById('rejectForm').action = `/token-emergency/reject-request/${requestId}`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endsection
