@extends('layouts.app')

@section('content')
    <div class="p-6">
        <div class="bg-white rounded-lg shadow-md p-6 max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8 pb-4 border-b-2 border-green-500">
                <h2 class="text-2xl font-bold text-gray-800">Edit Surat Pengantar Istirahat</h2>
                <p class="text-sm text-gray-600 mt-1">Perbarui informasi surat pengantar istirahat</p>
            </div>

            <form id="formSuratIstirahat" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Info Pasien (Read-only) -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-blue-800 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Informasi Pasien
                        @if ($surat->tipe_pasien === 'emergency')
                            <span class="ml-2 px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Emergency</span>
                        @else
                            <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Regular</span>
                        @endif
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        @if ($surat->tipe_pasien === 'emergency')
                            <div>
                                <span class="text-gray-600">NIK Pasien:</span>
                                <span
                                    class="ml-2 font-medium text-gray-900">{{ $surat->rekamMedisEmergency->nik_pasien ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Nama Pasien:</span>
                                <span
                                    class="ml-2 font-medium text-gray-900">{{ $surat->rekamMedisEmergency->nama_pasien ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Status:</span>
                                <span class="ml-2 font-medium text-gray-900">Emergency</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Tanggal Periksa:</span>
                                <span
                                    class="ml-2 font-medium text-gray-900">{{ $surat->rekamMedisEmergency->tanggal_periksa->format('d/m/Y') ?? '-' }}</span>
                            </div>
                        @else
                            <div>
                                <span class="text-gray-600">NIK Karyawan:</span>
                                <span class="ml-2 font-medium text-gray-900">{{ $surat->nik_karyawan ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Nama Karyawan:</span>
                                <span
                                    class="ml-2 font-medium text-gray-900">{{ $surat->nama_karyawan ?? 'External' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Nama Pasien:</span>
                                <span class="ml-2 font-medium text-gray-900">{{ $surat->nama_pasien }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Departemen:</span>
                                <span class="ml-2 font-medium text-gray-900">{{ $surat->departemen ?? '-' }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Hidden fields -->
                <input type="hidden" id="tipe_pasien" name="tipe_pasien" value="{{ $surat->tipe_pasien }}" />
                @if ($surat->tipe_pasien === 'emergency')
                    <input type="hidden" id="id_emergency" name="id_emergency" value="{{ $surat->id_emergency }}" />
                @else
                    <input type="hidden" id="id_rekam" name="id_rekam" value="{{ $surat->id_rekam }}" />
                    <input type="hidden" id="id_keluarga" name="id_keluarga" value="{{ $surat->id_keluarga }}" />
                @endif

                <!-- Nomor Surat (Read-only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Surat
                    </label>
                    <div class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg">
                        <span class="text-gray-700 font-medium">{{ $surat->nomor_surat }}</span>
                    </div>
                </div>

                <!-- Tanggal Surat (Read-only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Surat
                    </label>
                    <div class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg">
                        <span
                            class="text-gray-700">{{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d F Y') }}</span>
                    </div>
                </div>

                <!-- Lama Istirahat -->
                <div>
                    <label for="lama_istirahat" class="block text-sm font-medium text-gray-700 mb-2">
                        Lama Istirahat (hari) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="lama_istirahat" name="lama_istirahat" min="1" max="30"
                        value="{{ $surat->lama_istirahat }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                        placeholder="Masukkan jumlah hari istirahat" required />
                    <p class="mt-1 text-xs text-gray-500 italic">
                        Masukkan jumlah hari istirahat yang dibutuhkan (1-30 hari)
                    </p>
                </div>

                <!-- Tanggal Mulai Istirahat -->
                <div>
                    <label for="tanggal_mulai_istirahat" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Mulai Istirahat <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal_mulai_istirahat" name="tanggal_mulai_istirahat"
                        value="{{ $surat->tanggal_mulai_istirahat->format('Y-m-d') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                        required />
                    <p class="mt-1 text-xs text-gray-500 italic">
                        Tanggal mulai istirahat
                    </p>
                </div>

                <!-- Tanggal Selesai Istirahat (Auto-calculated) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Selesai Istirahat
                    </label>
                    <div class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg">
                        <span id="tanggal_selesai_istirahat"
                            class="text-gray-700">{{ \Carbon\Carbon::parse($surat->tanggal_selesai_istirahat)->format('d F Y') }}</span>
                    </div>
                </div>

                <!-- Diagnosa Utama -->
                <div>
                    <label for="diagnosa_utama" class="block text-sm font-medium text-gray-700 mb-2">
                        Diagnosa Utama <span class="text-red-500">*</span>
                    </label>
                    <textarea id="diagnosa_utama" name="diagnosa_utama" rows="3"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                        placeholder="Masukkan diagnosa utama pasien" maxlength="500" required>{{ $surat->diagnosa_utama }}</textarea>
                    <p class="mt-1 text-xs text-gray-500 italic">
                        Maksimal 500 karakter
                    </p>
                </div>

                <!-- Keterangan Tambahan -->
                <div>
                    <label for="keterangan_tambahan" class="block text-sm font-medium text-gray-700 mb-2">
                        Keterangan Tambahan
                    </label>
                    <textarea id="keterangan_tambahan" name="keterangan_tambahan" rows="3"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                        placeholder="Masukkan keterangan tambahan (opsional)" maxlength="1000">{{ $surat->keterangan_tambahan }}</textarea>
                    <p class="mt-1 text-xs text-gray-500 italic">
                        Maksimal 1000 karakter (opsional)
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                    <button type="submit" id="btnSubmit"
                        class="flex-1 sm:flex-none px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span id="btnText">Update Surat Pengantar Istirahat</span>
                    </button>
                    <a href="{{ route('surat-pengantar-istirahat.show', $surat->id_surat) }}"
                        class="flex-1 sm:flex-none px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Perhitungan tanggal selesai istirahat
            document.getElementById('lama_istirahat').addEventListener('input', calculateTanggalSelesai);
            document.getElementById('tanggal_mulai_istirahat').addEventListener('change', calculateTanggalSelesai);

            function calculateTanggalSelesai() {
                const lamaIstirahat = parseInt(document.getElementById('lama_istirahat').value);
                const tanggalMulai = document.getElementById('tanggal_mulai_istirahat').value;

                if (lamaIstirahat && tanggalMulai) {
                    const mulai = new Date(tanggalMulai);
                    const selesai = new Date(mulai);
                    selesai.setDate(mulai.getDate() + lamaIstirahat - 1);

                    const options = {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    };
                    document.getElementById('tanggal_selesai_istirahat').textContent = selesai.toLocaleDateString('id-ID',
                        options);
                } else {
                    document.getElementById('tanggal_selesai_istirahat').textContent = '-';
                }
            }

            // Submit form
            document.getElementById('formSuratIstirahat').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const btnSubmit = document.getElementById('btnSubmit');
                const btnText = document.getElementById('btnText');

                // Disable button dan tampilkan loading
                btnSubmit.disabled = true;
                btnText.textContent = 'Menyimpan...';

                fetch('{{ route('surat-pengantar-istirahat.update', $surat->id_surat) }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message, 'success');
                            setTimeout(() => {
                                window.location.href = data.redirect_url;
                            }, 1500);
                        } else {
                            showNotification(data.message || 'Terjadi kesalahan', 'error');
                            if (data.errors) {
                                // Tampilkan error validation
                                Object.keys(data.errors).forEach(key => {
                                    showNotification(data.errors[key][0], 'error');
                                });
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Terjadi kesalahan saat memperbarui data', 'error');
                    })
                    .finally(() => {
                        // Enable button kembali
                        btnSubmit.disabled = false;
                        btnText.textContent = 'Update Surat Pengantar Istirahat';
                    });
            });

            // Fungsi notifikasi
            function showNotification(message, type = 'info') {
                // Buat elemen notifikasi
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
                notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success' ?
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' :
                    type === 'error' ?
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>' :
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                }
            </svg>
            <span>${message}</span>
        </div>
    `;

                document.body.appendChild(notification);

                // Animasi masuk
                setTimeout(() => {
                    notification.classList.add('translate-x-0');
                }, 100);

                // Hapus setelah 3 detik
                setTimeout(() => {
                    notification.classList.add('translate-x-full', 'opacity-0');
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 3000);
            }
        </script>
    @endpush
@endsection
