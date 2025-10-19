@extends('layouts.app')

@section('page-title', 'Detail Rekam Medis Emergency')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-gradient-to-r from-red-600 to-pink-600 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detail Rekam Medis Emergency</h1>
                    <p class="text-gray-600 mt-1">Informasi lengkap rekam medis pasien emergency</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('rekam-medis-emergency.edit', $rekamMedisEmergency->id_emergency) }}"
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg font-medium shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
                <a href="{{ route('rekam-medis-emergency.index') }}"
                   class="bg-white hover:bg-gray-50 border-2 border-gray-300 text-gray-700 px-6 py-3 rounded-lg font-medium transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Patient Information Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Informasi Pasien
            </h2>
            <div class="status-dropdown" data-id="{{ $rekamMedisEmergency->id_emergency }}">
                <select class="status-select px-4 py-2 rounded-full text-sm font-medium border-0 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500
                    @if($rekamMedisEmergency->status_rekam_medis == 'On Progress') bg-yellow-100 text-yellow-800
                    @elseif($rekamMedisEmergency->status_rekam_medis == 'Close') bg-green-100 text-green-800
                    @endif"
                    data-id="{{ $rekamMedisEmergency->id_emergency }}"
                    data-current-status="{{ $rekamMedisEmergency->status_rekam_medis }}">
                    <option value="On Progress" {{ $rekamMedisEmergency->status_rekam_medis == 'On Progress' ? 'selected' : '' }}>On Progress</option>
                    <option value="Close" {{ $rekamMedisEmergency->status_rekam_medis == 'Close' ? 'selected' : '' }}>Close</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">NIK Pasien</label>
                <p class="text-gray-900 font-medium">{{ $rekamMedisEmergency->nik_pasien }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Pasien</label>
                <p class="text-gray-900 font-medium">{{ $rekamMedisEmergency->nama_pasien }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">No RM</label>
                <p class="text-gray-900 font-medium">{{ $rekamMedisEmergency->no_rm }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Hubungan</label>
                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                    {{ $rekamMedisEmergency->hubungan }}
                </span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Jenis Kelamin</label>
                <p class="text-gray-900 font-medium">{{ $rekamMedisEmergency->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Periksa</label>
                <p class="text-gray-900 font-medium">{{ $rekamMedisEmergency->tanggal_periksa->format('d-m-Y') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Waktu Periksa</label>
                <p class="text-gray-900 font-medium">{{ $rekamMedisEmergency->waktu_periksa ? $rekamMedisEmergency->waktu_periksa->format('H:i') : '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Status Rekam Medis</label>
                <span class="px-3 py-1
                    @if($rekamMedisEmergency->status_rekam_medis == 'On Progress') bg-yellow-100 text-yellow-800
                    @elseif($rekamMedisEmergency->status_rekam_medis == 'Close') bg-green-100 text-green-800
                    @endif
                    rounded-full text-xs font-medium">
                    {{ $rekamMedisEmergency->status_rekam_medis }}
                </span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Dibuat Oleh</label>
                <p class="text-gray-900 font-medium">{{ $rekamMedisEmergency->user->nama_lengkap ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Medical Information Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Keluhan Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                Keluhan
            </h3>
            <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                <p class="text-gray-800 whitespace-pre-line">{{ $rekamMedisEmergency->keluhan }}</p>
            </div>
        </div>

        <!-- Diagnosa Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Diagnosa
            </h3>
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <p class="text-gray-800 whitespace-pre-line">{{ $rekamMedisEmergency->diagnosa ?? 'Belum ada diagnosa' }}</p>
            </div>
        </div>

        <!-- Catatan Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Catatan
            </h3>
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <p class="text-gray-800 whitespace-pre-line">{{ $rekamMedisEmergency->catatan ?? 'Tidak ada catatan' }}</p>
            </div>
        </div>
    </div>

    <!-- Timestamps -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Informasi Waktu
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Dibuat pada</label>
                <p class="text-gray-900">{{ $rekamMedisEmergency->created_at->format('d-m-Y H:i:s') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Terakhir diperbarui</label>
                <p class="text-gray-900">{{ $rekamMedisEmergency->updated_at->format('d-m-Y H:i:s') }}</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle status change
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const newStatus = this.value;
            const currentStatus = this.getAttribute('data-current-status');

            // Show loading state
            this.disabled = true;
            this.classList.add('opacity-50');

            // Send AJAX request
            fetch(`{{ route('rekam-medis-emergency.updateStatus', ':id') }}`.replace(':id', id), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    status_rekam_medis: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update status attribute
                    this.setAttribute('data-current-status', newStatus);

                    // Update styling based on new status
                    this.classList.remove('bg-yellow-100', 'text-yellow-800', 'bg-green-100', 'text-green-800');

                    if (newStatus === 'On Progress') {
                        this.classList.add('bg-yellow-100', 'text-yellow-800');
                    } else if (newStatus === 'Close') {
                        this.classList.add('bg-green-100', 'text-green-800');
                    }

                    // Show success notification
                    showNotification('Status berhasil diperbarui', 'success');
                } else {
                    // Revert to original status
                    this.value = currentStatus;
                    showNotification(data.message || 'Gagal memperbarui status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert to original status
                this.value = currentStatus;
                showNotification('Terjadi kesalahan saat memperbarui status', 'error');
            })
            .finally(() => {
                // Remove loading state
                this.disabled = false;
                this.classList.remove('opacity-50');
            });
        });
    });

    // Function to show notification
    function showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;

        // Set styling based on type
        if (type === 'success') {
            notification.classList.add('bg-green-500', 'text-white');
        } else if (type === 'error') {
            notification.classList.add('bg-red-500', 'text-white');
        }

        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    ${type === 'success'
                        ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
                        : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>'
                    }
                </svg>
                <span>${message}</span>
            </div>
        `;

        // Add to document
        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
            notification.classList.add('translate-x-0');
        }, 100);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
});
</script>
@endpush
@endsection
