@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md p-6 max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Tambah Rekam Medis</h2>
        </div>

        <form action="{{ route('rekam-medis.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Row 1: NO RM & Pilih Pasien -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- NO RM -->
                <div>
                    <label for="no_rm" class="block text-sm font-medium text-gray-700 mb-2">
                        NO RM <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="no_rm"
                        name="no_rm"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        placeholder="Masukkan NO RM"
                        required
                    />
                </div>

                <!-- Pilih Pasien -->
                <div>
                    <label for="pilih_pasien" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Pasien <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="pilih_pasien"
                        name="pilih_pasien"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        required
                        onchange="updatePasienInfo(this)"
                    >
                        <option value="">-- Pilih Pasien --</option>
                        <option value="1" data-nama="Awang Rio" data-tanggal="01/09/1998">0001/ND_ILM/10/2025 - Awang Rio</option>
                        <option value="2" data-nama="Ronggo" data-tanggal="15/05/2020">0002/ND_ILM/10/2025 - Ronggo</option>
                    </select>
                </div>
            </div>

            <!-- Row 2: Nama Pasien & Tanggal Lahir (Auto-filled) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Pasien -->
                <div>
                    <label for="nama_pasien" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Pasien <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="nama_pasien"
                        name="nama_pasien"
                        class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed"
                        placeholder="Otomatis terisi"
                        readonly
                    />
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Lahir <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="tanggal_lahir"
                        name="tanggal_lahir"
                        class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed"
                        placeholder="dd/mm/yyyy"
                        readonly
                    />
                </div>
            </div>

            <!-- Row 3: Diagnosa & Terapi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Diagnosa / Penyakit -->
                <div>
                    <label for="diagnosa" class="block text-sm font-medium text-gray-700 mb-2">
                        Diagnosa / Penyakit <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="diagnosa"
                        name="diagnosa"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        required
                    >
                        <option value="">-- Pilih Diagnosa --</option>
                        <option value="1">Anemia Defisiensi Besi</option>
                        <option value="2">Hipertensi</option>
                        <option value="3">Diabetes Mellitus</option>
                        <option value="4">ISPA (Infeksi Saluran Pernapasan Atas)</option>
                        <option value="5">Gastritis</option>
                        <option value="6">Migrain</option>
                        <option value="7">Demam Berdarah</option>
                        <option value="8">Diare Akut</option>
                        <option value="9">Asma</option>
                        <option value="10">Vertigo</option>
                    </select>
                </div>

                <!-- Terapi -->
                <div>
                    <label for="terapi" class="block text-sm font-medium text-gray-700 mb-2">
                        Terapi <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="terapi"
                        name="terapi"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        required
                    >
                        <option value="">-- Pilih Terapi --</option>
                        <option value="1">Pemberian Obat Oral</option>
                        <option value="2">Pemberian Obat Injeksi</option>
                        <option value="3">Terapi Cairan (Infus)</option>
                        <option value="4">Nebulizer</option>
                        <option value="5">Pemeriksaan Laboratorium</option>
                        <option value="6">Konseling Kesehatan</option>
                        <option value="7">Rawat Jalan</option>
                        <option value="8">Rujukan ke Rumah Sakit</option>
                    </select>
                </div>
            </div>

            <!-- Row 4: Tanggal Kunjungan (Half Width) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tanggal Kunjungan -->
                <div>
                    <label for="tanggal_kunjungan" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Kunjungan <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        id="tanggal_kunjungan"
                        name="tanggal_kunjungan"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        required
                    />
                </div>
            </div>

            <!-- Row 5: Catatan (Full Width) -->
            <div>
                <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan
                </label>
                <textarea
                    id="catatan"
                    name="catatan"
                    rows="4"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                    placeholder="Masukkan catatan tambahan (opsional)"
                ></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <button
                    type="submit"
                    class="flex-1 sm:flex-none px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan
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
    function updatePasienInfo(select) {
        const selectedOption = select.options[select.selectedIndex];
        const namaPasien = selectedOption.getAttribute('data-nama');
        const tanggalLahir = selectedOption.getAttribute('data-tanggal');

        document.getElementById('nama_pasien').value = namaPasien || '';
        document.getElementById('tanggal_lahir').value = tanggalLahir || '';
    }
</script>
@endpush
@endsection
