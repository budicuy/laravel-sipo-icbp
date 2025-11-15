@extends('layouts.app')

@section('title', 'Permintaan Token Emergency')

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
                <h1 class="text-3xl font-bold text-gray-900">Permintaan Token Emergency</h1>
                <p class="text-gray-600 mt-1">Ajukan permintaan token untuk keperluan medis emergency</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
        <div class="mb-6">
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Informasi Permintaan Token</h3>
                        <p class="text-sm text-blue-700 mt-1">
                            Permintaan token Anda akan ditinjau oleh admin. Jika disetujui, token akan digenerate dan
                            dapat digunakan untuk membuat rekam medis emergency.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('token-emergency.request') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">
                        Jumlah Token <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select id="quantity" name="quantity"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent appearance-none bg-white"
                            required>
                            <option value="">Pilih Jumlah</option>
                            <option value="1">1 Token</option>
                            <option value="2">2 Token</option>
                            <option value="3">3 Token</option>
                            <option value="5" selected>5 Token</option>
                            <option value="10">10 Token</option>
                            <option value="15">15 Token</option>
                            <option value="20">20 Token</option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Pilih jumlah token yang Anda butuhkan (maksimal 20)</p>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                        Alasan Permintaan
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                        placeholder="Jelaskan alasan Anda membutuhkan token emergency..."></textarea>
                    <p class="mt-2 text-sm text-gray-500">Opsional: Jelaskan alasan Anda membutuhkan token</p>
                </div>
            </div>

            <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Informasi Penting:</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Permintaan token akan ditinjau oleh admin</li>
                                <li>Proses persetujuan biasanya memakan waktu 1-2 hari kerja</li>
                                <li>Token yang disetujui akan tersedia di akun Anda</li>
                                <li>Token hanya dapat digunakan untuk rekam medis emergency</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('dashboard') }}"
                    class="px-6 py-3 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-medium rounded-lg transition-all hover:shadow-md">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-linear-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Ajukan Permintaan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus pada select pertama
    document.getElementById('quantity').focus();
});
</script>
@endsection