@extends('layouts.app')

@section('content')
    <div class="p-6">
        <div class="bg-white rounded-lg shadow-md p-6 max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8 pb-4 border-b-2 border-green-500">
                <h2 class="text-2xl font-bold text-gray-800">Buat Surat Pengantar Istirahat</h2>
                <p class="text-sm text-gray-600 mt-1">Formulir pembuatan surat pengantar istirahat untuk karyawan dan
                    keluarga</p>
            </div>

            <form id="formSuratIstirahat" class="space-y-6">
                @csrf

                <!-- Pencarian Rekam Medis -->
                <div class="relative">
                    <label for="search_rekam_medis" class="block text-sm font-medium text-gray-700 mb-2">
                        Cari NIK Karyawan / Nama Pasien <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" id="search_rekam_medis" name="search_rekam_medis"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                            placeholder="Masukkan NIK karyawan atau nama pasien..." autocomplete="off" />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Dropdown hasil pencarian -->
                    <div id="search_results"
                        class="hidden mt-2 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto absolute z-50 w-full">
                    </div>

                    <p class="mt-1 text-xs text-gray-500 italic">
                        Hanya menampilkan rekam medis dengan status "On Progress"
                    </p>
                </div>

                <!-- Info Pasien (Auto-filled) -->
                <div id="info-pasien" class="hidden">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-blue-800 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Informasi Pasien
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-600">NIK Karyawan:</span>
                                <span id="display_nik" class="ml-2 font-medium text-gray-900"></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Nama Karyawan:</span>
                                <span id="display_nama_karyawan" class="ml-2 font-medium text-gray-900"></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Nama Pasien:</span>
                                <span id="display_nama_pasien" class="ml-2 font-medium text-gray-900"></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Departemen:</span>
                                <span id="display_departemen" class="ml-2 font-medium text-gray-900"></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Tanggal Periksa:</span>
                                <span id="display_tanggal_periksa" class="ml-2 font-medium text-gray-900"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden fields -->
                <input type="hidden" id="id_rekam" name="id_rekam" />
                <input type="hidden" id="id_keluarga" name="id_keluarga" />

                <!-- Lama Istirahat -->
                <div>
                    <label for="lama_istirahat" class="block text-sm font-medium text-gray-700 mb-2">
                        Lama Istirahat (hari) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="lama_istirahat" name="lama_istirahat" min="1" max="30"
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
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                        required />
                    <p class="mt-1 text-xs text-gray-500 italic">
                        Tanggal mulai istirahat tidak boleh kurang dari hari ini
                    </p>
                </div>

                <!-- Tanggal Selesai Istirahat (Auto-calculated) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Selesai Istirahat
                    </label>
                    <div class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg">
                        <span id="tanggal_selesai_istirahat" class="text-gray-700">-</span>
                    </div>
                </div>

                <!-- Diagnosa Utama -->
                <div>
                    <label for="diagnosa_utama" class="block text-sm font-medium text-gray-700 mb-2">
                        Diagnosa Utama <span class="text-red-500">*</span>
                    </label>
                    <textarea id="diagnosa_utama" name="diagnosa_utama" rows="3"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                        placeholder="Masukkan diagnosa utama pasien" maxlength="500" required></textarea>
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
                        placeholder="Masukkan keterangan tambahan (opsional)" maxlength="1000"></textarea>
                    <p class="mt-1 text-xs text-gray-500 italic">
                        Maksimal 1000 karakter (opsional)
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                    <button type="submit" id="btnSubmit"
                        class="flex-1 sm:flex-none px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span id="btnText">Buat Surat Pengantar Istirahat</span>
                    </button>
                    <a href="{{ route('surat-pengantar-istirahat.index') }}"
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
            let searchTimeout;
            let selectedRekamMedis = null;

            // Set minimum date untuk tanggal mulai istirahat
            document.addEventListener('DOMContentLoaded', function() {
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('tanggal_mulai_istirahat').setAttribute('min', today);
                document.getElementById('tanggal_mulai_istirahat').value = today;

                // Trigger perhitungan tanggal selesai
                calculateTanggalSelesai();
            });

            // Fungsi pencarian rekam medis
            document.getElementById('search_rekam_medis').addEventListener('input', function(e) {
                const query = e.target.value.trim();

                clearTimeout(searchTimeout);

                if (query.length < 2) {
                    hideSearchResults();
                    return;
                }

                searchTimeout = setTimeout(() => {
                    searchRekamMedis(query);
                }, 500);
            });

            // Fungsi untuk melakukan pencarian
            function searchRekamMedis(query) {
                fetch(`{{ route('surat-pengantar-istirahat.searchRekamMedis') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        displaySearchResults(data);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Terjadi kesalahan saat mencari data', 'error');
                    });
            }

            // Fungsi menampilkan hasil pencarian
            function displaySearchResults(results) {
                const resultsContainer = document.getElementById('search_results');

                if (results.length === 0) {
                    resultsContainer.innerHTML = `
                <div class="p-3 text-gray-500 text-sm">
                    Tidak ditemukan rekam medis dengan status "On Progress"
                </div>
            `;
                    resultsContainer.classList.remove('hidden');
                    return;
                }

                let html = '';
                results.forEach((result, index) => {
                    html += `
                <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-200 last:border-b-0 search-result-item"
                     data-index="${index}">
                    <div class="font-medium text-gray-900">${result.display_text}</div>
                    <div class="text-sm text-gray-600">
                        Tanggal Periksa: ${result.tanggal_periksa}
                        ${result.departemen ? ' | Departemen: ' + result.departemen : ''}
                    </div>
                </div>
            `;
                });

                resultsContainer.innerHTML = html;
                resultsContainer.classList.remove('hidden');

                // Add click event listeners to each result item
                document.querySelectorAll('.search-result-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const index = this.getAttribute('data-index');
                        const result = results[index];
                        selectRekamMedis(result);
                    });
                });
            }

            // Fungsi memilih rekam medis
            function selectRekamMedis(rekamMedis) {
                selectedRekamMedis = rekamMedis;

                // Isi hidden fields
                document.getElementById('id_rekam').value = rekamMedis.id_rekam;
                document.getElementById('id_keluarga').value = rekamMedis.id_keluarga;

                // Isi info pasien
                document.getElementById('display_nik').textContent = rekamMedis.nik_karyawan || 'Tidak ada NIK';
                document.getElementById('display_nama_karyawan').textContent = rekamMedis.nama_karyawan || 'External';
                document.getElementById('display_nama_pasien').textContent = rekamMedis.nama_pasien;
                document.getElementById('display_departemen').textContent = rekamMedis.departemen || 'Tidak ada departemen';
                document.getElementById('display_tanggal_periksa').textContent = rekamMedis.tanggal_periksa;

                // Tampilkan info pasien
                document.getElementById('info-pasien').classList.remove('hidden');

                // Sembunyikan hasil pencarian
                hideSearchResults();

                // Kosongkan search input
                document.getElementById('search_rekam_medis').value = '';

                // Ambil detail rekam medis untuk diagnosa
                fetch(`{{ route('surat-pengantar-istirahat.getRekamMedisDetail', ['id_rekam' => 'ID_REKAM']) }}`.replace(
                        'ID_REKAM', rekamMedis.id_rekam))
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('diagnosa_utama').value = data.data.diagnosa_utama;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }

            // Fungsi menyembunyikan hasil pencarian
            function hideSearchResults() {
                document.getElementById('search_results').classList.add('hidden');
            }

            // Klik di luar search results untuk menyembunyikan
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#search_rekam_medis') && !e.target.closest('#search_results')) {
                    hideSearchResults();
                }
            });

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

                // Validasi
                if (!selectedRekamMedis) {
                    showNotification('Silakan pilih pasien terlebih dahulu', 'error');
                    return;
                }

                const formData = new FormData(this);
                const btnSubmit = document.getElementById('btnSubmit');
                const btnText = document.getElementById('btnText');

                // Disable button dan tampilkan loading
                btnSubmit.disabled = true;
                btnText.textContent = 'Menyimpan...';

                fetch('{{ route('surat-pengantar-istirahat.store') }}', {
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
                        showNotification('Terjadi kesalahan saat menyimpan data', 'error');
                    })
                    .finally(() => {
                        // Enable button kembali
                        btnSubmit.disabled = false;
                        btnText.textContent = 'Buat Surat Pengantar Istirahat';
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
