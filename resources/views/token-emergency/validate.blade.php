@extends('layouts.app')

@section('title', 'Validasi Token Emergency')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-linear-to-r from-red-500 to-pink-600 p-6 text-white">
                <div class="flex items-center justify-center mb-4">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                        </path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-center">Validasi Token Emergency</h1>
                <p class="text-center mt-2 text-red-100">Masukkan token emergency untuk mengakses mode darurat</p>
            </div>

            <div class="p-6">
                <form id="validateTokenForm" class="space-y-4">
                    @csrf
                    <div>
                        <label for="token" class="block text-sm font-medium text-gray-700 mb-2">
                            Token Emergency
                        </label>
                        <input type="text" id="token" name="token"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-center text-lg font-mono tracking-widest uppercase"
                            placeholder="Masukkan token" required maxlength="6" pattern="[A-Z0-9]{6}"
                            autocomplete="off">
                        <p class="mt-2 text-sm text-gray-500">Token biasanya 6 karakter kombinasi huruf dan angka</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('dashboard') }}"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Kembali
                        </a>
                        <button type="submit" id="validateBtn"
                            class="px-6 py-3 bg-linear-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Validasi Token
                        </button>
                    </div>
                </form>

                <!-- Alert Container -->
                <div id="alertContainer" class="mt-4 hidden">
                    <div class="p-4 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span id="alertMessage"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-blue-800">
                    <p class="font-medium mb-1">Informasi Token Emergency:</p>
                    <ul class="list-disc list-inside space-y-1 text-blue-700">
                        <li>Token emergency hanya dapat digunakan sekali</li>
                        <li>Token berlaku selama 24 jam setelah dibuat</li>
                        <li>Token yang sudah digunakan tidak dapat digunakan kembali</li>
                        <li>Jika token tidak valid, hubungi administrator</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('validateTokenForm');
    const tokenInput = document.getElementById('token');
    const validateBtn = document.getElementById('validateBtn');
    const alertContainer = document.getElementById('alertContainer');
    const alertMessage = document.getElementById('alertMessage');

    // Auto-format token input
    tokenInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const token = tokenInput.value.trim();
        if (!token) {
            showAlert('Silakan masukkan token emergency', 'error');
            return;
        }

        // Show loading state
        validateBtn.disabled = true;
        validateBtn.innerHTML = `
            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memvalidasi...
        `;

        // Send AJAX request
        fetch('{{ route("token-emergency.validate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                token: token
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                setTimeout(() => {
                    window.location.href = '{{ route("rekam-medis-emergency.create") }}';
                }, 1500);
            } else {
                showAlert(data.message, 'error');
                validateBtn.disabled = false;
                validateBtn.innerHTML = `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Validasi Token
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan saat memvalidasi token. Silakan coba lagi.', 'error');
            validateBtn.disabled = false;
            validateBtn.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Validasi Token
            `;
        });
    });

    function showAlert(message, type) {
        alertContainer.classList.remove('hidden');
        alertMessage.textContent = message;

        // Remove existing classes
        alertContainer.firstElementChild.classList.remove('bg-green-50', 'text-green-800', 'border-green-200', 'bg-red-50', 'text-red-800', 'border-red-200');

        // Add appropriate classes based on type
        if (type === 'success') {
            alertContainer.firstElementChild.classList.add('bg-green-50', 'text-green-800', 'border', 'border-green-200');
        } else {
            alertContainer.firstElementChild.classList.add('bg-red-50', 'text-red-800', 'border', 'border-red-200');
        }
    }
});
</script>
@endsection