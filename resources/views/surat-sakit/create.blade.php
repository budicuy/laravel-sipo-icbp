@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md p-6 max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-8 pb-4 border-b-2 border-green-500">
            <h2 class="text-2xl font-bold text-gray-800">Buat Surat Sakit</h2>
            <p class="text-sm text-gray-600 mt-1">Formulir pembuatan surat keterangan sakit untuk karyawan</p>
        </div>

        <form action="{{ route('surat-sakit.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Pilih NIK Karyawan -->
            <div>
                <label for="nik_karyawan" class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih NIK Karyawan <span class="text-red-500">*</span>
                </label>
                <select
                    id="nik_karyawan"
                    name="nik_karyawan"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                    required
                    onchange="loadKaryawanInfo(this)"
                >
                    <option value="">-- Pilih NIK Karyawan --</option>
                    <option value="KRY001" data-nama="Awang Rio" data-departemen="IT">KRY001 - Awang Rio</option>
                    <option value="KRY002" data-nama="Budi Santoso" data-departemen="HRD">KRY002 - Budi Santoso</option>
                    <option value="KRY003" data-nama="Citra Dewi" data-departemen="Finance">KRY003 - Citra Dewi</option>
                    <option value="KRY004" data-nama="Dedi Kurniawan" data-departemen="Marketing">KRY004 - Dedi Kurniawan</option>
                    <option value="KRY005" data-nama="Eka Putri" data-departemen="Produksi">KRY005 - Eka Putri</option>
                </select>
            </div>

            <!-- Info Karyawan (Auto-filled) -->
            <div id="info-karyawan" class="hidden">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-blue-800 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Informasi Karyawan
                    </h3>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-600">Nama:</span>
                            <span id="display-nama" class="ml-2 font-medium text-gray-900"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Departemen:</span>
                            <span id="display-departemen" class="ml-2 font-medium text-gray-900"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lama Istirahat -->
            <div>
                <label for="lama_istirahat" class="block text-sm font-medium text-gray-700 mb-2">
                    Lama Istirahat (hari) <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    id="lama_istirahat"
                    name="lama_istirahat"
                    min="1"
                    max="30"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                    placeholder="Masukkan jumlah hari istirahat"
                    required
                />
                <p class="mt-1 text-xs text-gray-500 italic">
                    Masukkan jumlah hari istirahat yang dibutuhkan (1-30 hari)
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <button
                    type="submit"
                    class="flex-1 sm:flex-none px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Surat Sakit
                </button>
                <a
                    href="{{ route('rekam-medis.index') }}"
                    class="flex-1 sm:flex-none px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function loadKaryawanInfo(select) {
        const selectedOption = select.options[select.selectedIndex];
        const nama = selectedOption.getAttribute('data-nama');
        const departemen = selectedOption.getAttribute('data-departemen');

        const infoDiv = document.getElementById('info-karyawan');
        const displayNama = document.getElementById('display-nama');
        const displayDepartemen = document.getElementById('display-departemen');

        if (select.value) {
            displayNama.textContent = nama || '';
            displayDepartemen.textContent = departemen || '';
            infoDiv.classList.remove('hidden');
        } else {
            infoDiv.classList.add('hidden');
        }
    }
</script>
@endpush
@endsection
