@extends('layouts.app')

@section('title', 'Permintaan Token Menunggu')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <div class="bg-linear-to-r from-yellow-500 to-orange-500 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Permintaan Token Menunggu</h1>
                <p class="text-gray-600 mt-1">Kelola permintaan token emergency dari pengguna</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Table Header -->
        <div class="bg-linear-to-r from-yellow-500 to-orange-500 px-6 py-4">
            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Daftar Permintaan Token
            </h2>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider border-r border-gray-200">
                            No</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider border-r border-gray-200">
                            Pemohon</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider border-r border-gray-200">
                            Jumlah</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider border-r border-gray-200">
                            Catatan</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider border-r border-gray-200">
                            Tanggal</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-900 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pendingRequests as $index => $request)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                    <span class="text-sm font-medium text-purple-600">
                                        {{ substr($request->requester->nama_lengkap, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $request->requester->nama_lengkap
                                        }}</div>
                                    <div class="text-xs text-gray-500">{{ $request->requester->username }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $request->quantity }} Token
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200 max-w-xs">
                            <div class="truncate" title="{{ $request->notes }}">
                                {{ $request->notes ?: '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            <div>
                                <div class="text-sm">{{ $request->created_at->format('d/m/Y H:i') }}</div>
                                <div class="text-xs text-gray-500">{{ $request->created_at->diffForHumans() }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <form action="{{ route('token-emergency.approve-request', $request->id) }}"
                                    method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="bg-green-600 text-white text-xs rounded px-3 py-1.5 hover:bg-green-700 transition-colors">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Setujui
                                    </button>
                                </form>
                                <button onclick="showRejectModal({{ $request->id }})"
                                    class="bg-red-600 text-white text-xs rounded px-3 py-1.5 hover:bg-red-700 transition-colors">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Tolak
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-lg font-medium">Tidak ada permintaan token yang menunggu</p>
                            <p class="text-sm mt-1">Semua permintaan telah diproses</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Tolak Permintaan Token</h3>
            <div class="mt-2 px-7 py-3">
                <form id="rejectForm" action="" method="POST">
                    @csrf
                    <input type="hidden" name="request_id" id="request_id">
                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">Alasan
                            Penolakan</label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="3" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                            placeholder="Jelaskan alasan penolakan..."></textarea>
                    </div>
                    <div class="flex justify-center space-x-3">
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Tolak
                        </button>
                        <button type="button" onclick="closeRejectModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
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