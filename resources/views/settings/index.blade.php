@extends('layouts.app')

@section('page-title', 'Pengaturan Sistem')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Pengaturan Sistem</h1>
                    <p class="text-gray-600 mt-1">Kelola pengaturan aplikasi</p>
                </div>
            </div>
            <a href="{{ route('dashboard') }}"
                class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if (session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-md animate-fade-in"
        id="success-container">
        <div class="flex items-center">
            <div class="shrink-0">
                <svg class="h-6 w-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="document.getElementById('success-container').style.display='none'"
                    class="text-green-400 hover:text-green-600">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Settings Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                </svg>
                Verifikasi & Keamanan
            </h2>
        </div>

        <form action="{{ route('settings.update') }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <!-- Fingerprint Setting -->
            <div class="space-y-6">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-purple-100 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Verifikasi Sidik Jari (Fingerprint)
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    Aktifkan untuk menggunakan verifikasi sidik jari pada pembuatan rekam medis.
                                    <br>
                                    <span class="text-xs text-gray-500">Jika dinonaktifkan, sistem akan menggunakan
                                        verifikasi manual (NIK & Tanggal Lahir)</span>
                                </p>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="mt-3 flex items-center gap-2">
                            <span class="text-xs font-medium text-gray-600">Status:</span>
                            <span id="status-badge"
                                class="px-3 py-1 rounded-full text-xs font-semibold {{ $settings['fingerprint_enabled']->value == '1' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $settings['fingerprint_enabled']->value == '1' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>

                    <!-- Toggle Switch -->
                    <div class="ml-6">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <!-- Hidden input to ensure value is always sent -->
                            <input type="hidden" name="fingerprint_enabled" value="0">
                            <input type="checkbox" name="fingerprint_enabled" value="1" class="sr-only peer" {{
                                $settings['fingerprint_enabled']->value == '1' ? 'checked' : '' }}
                            onchange="toggleStatus(this)">
                            <div
                                class="w-16 h-8 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-8 peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-purple-600">
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Information Box -->
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Informasi Penting:</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li><strong>Fingerprint Aktif:</strong> Sistem akan meminta verifikasi sidik jari saat
                                    membuat rekam medis. Verifikasi manual tetap tersedia sebagai alternatif jika
                                    fingerprint gagal.</li>
                                <li><strong>Fingerprint Nonaktif:</strong> Sistem akan langsung menampilkan form
                                    verifikasi manual menggunakan NIK dan Tanggal Lahir tanpa meminta sidik jari.</li>
                                <li>Perubahan pengaturan akan berlaku segera setelah disimpan.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('dashboard') }}"
                        class="px-6 py-2.5 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200 font-semibold">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Additional Information -->
    <div class="mt-6 bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <div class="p-2 bg-purple-100 rounded-lg shrink-0">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-semibold text-purple-900 mb-1">Tips Keamanan</h4>
                <p class="text-xs text-purple-800">
                    Untuk keamanan maksimal, disarankan untuk mengaktifkan verifikasi fingerprint. Namun, jika
                    perangkat fingerprint mengalami kendala atau tidak tersedia, Anda dapat menonaktifkan fitur ini
                    dan menggunakan verifikasi manual.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleStatus(checkbox) {
        const statusBadge = document.getElementById('status-badge');
        if (checkbox.checked) {
            statusBadge.textContent = 'Aktif';
            statusBadge.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800';
        } else {
            statusBadge.textContent = 'Nonaktif';
            statusBadge.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800';
        }
    }

    // Auto-hide success message after 5 seconds
    setTimeout(function() {
        const successContainer = document.getElementById('success-container');
        if (successContainer) {
            successContainer.style.transition = 'opacity 0.5s ease-out';
            successContainer.style.opacity = '0';
            setTimeout(() => successContainer.style.display = 'none', 500);
        }
    }, 5000);
</script>
@endsection
