@extends('layouts.app')

@section('title', 'Token Saya')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Token Saya</h1>
                <p class="text-gray-600 mt-1">Kelola dan pantau token emergency Anda</p>
            </div>
        </div>
    </div>

    <!-- Low Token Warning -->
    @if($availableTokensCount < 3)
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.964-5.964A6 6 0 0112 8.257v4.18m0 0a.75.75 0 01.75-.75V8.257m0 0h-.005z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Token Anda Hampir Habis</h3>
                <p class="text-sm text-yellow-700 mt-1">
                    Anda hanya memiliki {{ $availableTokensCount }} token tersedia.
                    <button onclick="showRequestTokenModal()" class="underline font-medium text-yellow-800 hover:text-yellow-900">Ajukan permintaan token baru</button> untuk menghindari kehabisan token.
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <button onclick="showRequestTokenModal()"
               class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Ajukan Permintaan Token
            </button>

            <a href="{{ route('rekam-medis.chooseType') }}"
               class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-green-500 to-teal-500 hover:from-green-600 hover:to-teal-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Rekam Medis Emergency
            </a>
        </div>
    </div>

    <!-- Token Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Token Tersedia</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $availableTokensCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Token</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $tokens->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Token Digunakan</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $tokens->where('status', 'used')->count() }}</p>
                </div>
            </div>
        </div>
    </div>


    <!-- Token List -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Table Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
                Daftar Token Emergency
            </h2>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider border-r border-gray-200">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider border-r border-gray-200">Token</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider border-r border-gray-200">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider border-r border-gray-200">Dibuat</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider border-r border-gray-200">Digunakan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Catatan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tokens as $index => $token)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-200">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $token->token }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            {!! $token->status_badge !!}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            <div>
                                <div class="text-sm">{{ $token->created_at->format('d/m/Y H:i') }}</div>
                                <div class="text-xs text-gray-500">{{ $token->created_at->diffForHumans() }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            @if($token->used_at)
                            <div>
                                <div class="text-sm">{{ $token->used_at->format('d/m/Y H:i') }}</div>
                                <div class="text-xs text-gray-500">{{ $token->used_at->diffForHumans() }}</div>
                            </div>
                            @else
                            <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                            <div class="truncate" title="{{ $token->notes }}">
                                {{ $token->notes ?: '-' }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            <p class="text-lg font-medium">Anda belum memiliki token</p>
                            <p class="text-sm mt-1">
                                <button onclick="showRequestTokenModal()" class="text-blue-600 hover:text-blue-800 font-medium">
                                    Ajukan permintaan token
                                </button>
                                untuk mulai menggunakan layanan ini
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Request Token Modal -->
<div id="requestTokenModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Ajukan Permintaan Token</h3>
            <div class="mt-2 px-7 py-3">
                <form id="requestTokenForm" action="{{ route('token-emergency.storeRequest') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Token</label>
                        <select id="quantity" name="quantity" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-yellow-500 focus:border-yellow-500" required>
                            <option value="">Pilih Jumlah</option>
                            <option value="1">1 Token</option>
                            <option value="2">2 Token</option>
                            <option value="3">3 Token</option>
                            <option value="5" selected>5 Token</option>
                            <option value="10">10 Token</option>
                            <option value="15">15 Token</option>
                            <option value="20">20 Token</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Alasan Permintaan</label>
                        <textarea id="notes" name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-yellow-500 focus:border-yellow-500" placeholder="Jelaskan alasan Anda membutuhkan token emergency..."></textarea>
                    </div>
                    <div class="flex justify-center space-x-3">
                        <button type="submit" class="px-4 py-2 bg-yellow-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            Ajukan
                        </button>
                        <button type="button" onclick="closeRequestTokenModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showRequestTokenModal() {
    document.getElementById('requestTokenModal').classList.remove('hidden');
}

function closeRequestTokenModal() {
    document.getElementById('requestTokenModal').classList.add('hidden');
}

// Handle form submission with SweetAlert
document.getElementById('requestTokenForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                showConfirmButton: false,
                timer: 3000
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message || 'Terjadi kesalahan saat mengajukan permintaan.',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Terjadi kesalahan saat mengajukan permintaan.',
            confirmButtonText: 'OK'
        });
    });
});
</script>
@endsection
