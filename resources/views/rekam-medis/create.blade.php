@extends('layouts.app')

@section('page-title', 'Tambah Rekam Medis')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-3">
            <a href="{{ route('rekam-medis.index') }}" class="p-2 hover:bg-white rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-3 rounded-lg shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    Tambah Rekam Medis
                </h1>
                <p class="text-gray-600 mt-1 ml-1">Buat rekam medis baru untuk pasien</p>
            </div>
        </div>
    </div>

    <form action="{{ route('rekam-medis.store') }}" method="POST">
        @csrf

        <!-- Data Pasien Section -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Data Pasien
                </h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- NO RM -->
                    <div>
                        <label for="no_rm" class="block text-sm font-semibold text-gray-700 mb-2">
                            NO RM <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                </svg>
                            </div>
                            <input type="text" id="no_rm" name="no_rm" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Masukkan NO RM" required>
                        </div>
                    </div>

                    <!-- Pilih Pasien -->
                    <div>
                        <label for="pilih_pasien" class="block text-sm font-semibold text-gray-700 mb-2">
                            Pilih Pasien <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="pilih_pasien" name="pilih_pasien" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white" required onchange="updatePasienInfo(this)">
                                <option value="">-- Pilih Pasien --</option>
                                <option value="1" data-nama="Awang Rio" data-tanggal="01/09/1998" data-nik="KRY001">0001/HCL/RM/10/2025 - Awang Rio</option>
                                <option value="2" data-nama="Ronggo" data-tanggal="15/05/2020" data-nik="KRY001">0002/HCL/RM/10/2025 - Ronggo</option>
                                <option value="3" data-nama="Siti Nurhaliza" data-tanggal="10/03/1995" data-nik="KRY002">0003/HCL/RM/10/2025 - Siti Nurhaliza</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- NIK Karyawan -->
                    <div>
                        <label for="nik_karyawan" class="block text-sm font-semibold text-gray-700 mb-2">
                            NIK Karyawan
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                            </div>
                            <input type="text" id="nik_karyawan" name="nik_karyawan" class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-gray-600" placeholder="Otomatis terisi" readonly>
                        </div>
                    </div>

                    <!-- Nama Pasien -->
                    <div>
                        <label for="nama_pasien" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Pasien
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" id="nama_pasien" name="nama_pasien" class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-gray-600" placeholder="Otomatis terisi" readonly>
                        </div>
                    </div>

                    <!-- Kode Hubungan -->
                    <div>
                        <label for="kode_hubungan" class="block text-sm font-semibold text-gray-700 mb-2">
                            Kode Hubungan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="kode_hubungan" name="kode_hubungan" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white" required onchange="updateHubungan(this)">
                                <option value="">-- Pilih Kode --</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="E">E</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Hubungan -->
                    <div>
                        <label for="hubungan" class="block text-sm font-semibold text-gray-700 mb-2">
                            Hubungan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="hubungan" name="hubungan" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white" required>
                                <option value="">-- Pilih Hubungan --</option>
                                <option value="Karyawan">Karyawan</option>
                                <option value="Pasangan">Pasangan</option>
                                <option value="Anak 1">Anak 1</option>
                                <option value="Anak 2">Anak 2</option>
                                <option value="Anak 3">Anak 3</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-semibold text-gray-700 mb-2">
                            Tanggal Lahir
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="text" id="tanggal_lahir" name="tanggal_lahir" class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-gray-600" placeholder="dd/mm/yyyy" readonly>
                        </div>
                    </div>

                    <!-- Tanggal Periksa -->
                    <div>
                        <label for="tanggal_periksa" class="block text-sm font-semibold text-gray-700 mb-2">
                            Tanggal Periksa <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="date" id="tanggal_periksa" name="tanggal_periksa" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                        </div>
                    </div>

                    <!-- Jumlah Keluhan -->
                    <div>
                        <label for="jumlah_keluhan" class="block text-sm font-semibold text-gray-700 mb-2">
                            Jumlah Keluhan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="jumlah_keluhan" name="jumlah_keluhan" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white" required onchange="updateKeluhanSections(this.value)">
                                <option value="1">1 Keluhan</option>
                                <option value="2">2 Keluhan</option>
                                <option value="3">3 Keluhan</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Combined Diagnosa & Resep Section -->
        <div id="keluhan-container">
            <div class="keluhan-section bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Diagnosa & Resep Obat
                        <span class="keluhan-number">(Keluhan 1)</span>
                    </h2>
                </div>
                
                <div class="p-6">
                    <!-- Diagnosa Section -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Diagnosa</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Diagnosa / Penyakit -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Diagnosa / Penyakit <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="diagnosa[]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white" required>
                                        <option value="">-- Pilih Diagnosa --</option>
                                        <option value="1">Demam Berdarah</option>
                                        <option value="2">Hipertensi</option>
                                        <option value="3">Diabetes Mellitus</option>
                                        <option value="4">ISPA (Infeksi Saluran Pernapasan Atas)</option>
                                        <option value="5">Gastritis</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Terapi -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Terapi <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="terapi[]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white" required>
                                        <option value="">-- Pilih Terapi --</option>
                                        <option value="Obat">Obat</option>
                                        <option value="Lab">Lab</option>
                                        <option value="Istirahat dirumah">Istirahat dirumah</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Keterangan -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Keterangan / Catatan
                                </label>
                                <textarea name="keterangan[]" rows="4" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Masukkan catatan medis, anjuran dokter, atau informasi penting lainnya..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Resep Obat Section -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Resep Obat</h3>
                        <div class="obat-container">
                            <div class="obat-row grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nama Obat <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <select name="obat[0][]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white" required>
                                            <option value="">-- Pilih Obat --</option>
                                            <option value="1">Paracetamol 500mg</option>
                                            <option value="2">Amoxicillin 500mg</option>
                                            <option value="3">Vitamin C 1000mg</option>
                                            <option value="4">Antasida</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Jumlah <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="jumlah[0][]" min="1" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="0" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Aturan Pakai <span class="text-red-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="relative">
                                            <select name="jumlah_pakai[0][]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white" required>
                                                <option value="">-- Jumlah --</option>
                                                <option value="1x sehari">1x sehari</option>
                                                <option value="2x sehari">2x sehari</option>
                                                <option value="3x sehari">3x sehari</option>
                                                <option value="4x sehari">4x sehari</option>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="relative">
                                            <select name="waktu_pakai[0][]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 appearance-none bg-white" required>
                                                <option value="">-- Waktu --</option>
                                                <option value="Sebelum makan">Sebelum makan</option>
                                                <option value="Sesudah makan">Sesudah makan</option>
                                                <option value="Saat makan">Saat makan</option>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="tambah-obat-btn mt-4 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-6 py-2.5 rounded-lg font-medium shadow-md hover:shadow-lg transition-all">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Obat
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" onclick="window.location.href='{{ route('rekam-medis.index') }}'" class="px-6 py-2.5 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-medium rounded-lg transition-all hover:shadow-md">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Rekam Medis
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Handle hubungan selection based on kode_hubungan
function updateHubungan(select) {
    const kodeHubungan = select.value;
    const hubunganSelect = document.getElementById('hubungan');
    
    // Define mapping of kode to hubungan
    const hubunganMapping = {
        'A': 'Karyawan',
        'B': 'Pasangan',
        'C': 'Anak 1',
        'D': 'Anak 2',
        'E': 'Anak 3'
    };
    
    // Set the hubungan value based on kode
    if (kodeHubungan in hubunganMapping) {
        hubunganSelect.value = hubunganMapping[kodeHubungan];
    } else {
        hubunganSelect.value = '';
    }
}
// Handle hubungan selection based on kode_hubungan
function updateHubungan(select) {
    const kodeHubungan = select.value;
    const hubunganSelect = document.getElementById('hubungan');
    
    // Define mapping of kode to hubungan
    const hubunganMapping = {
        'A': 'Karyawan',
        'B': 'Pasangan',
        'C': 'Anak 1',
        'D': 'Anak 2',
        'E': 'Anak 3'
    };
    
    // Set the hubungan value based on kode
    if (kodeHubungan in hubunganMapping) {
        hubunganSelect.value = hubunganMapping[kodeHubungan];
    } else {
        hubunganSelect.value = '';
    }
}

// Handle multiple keluhan sections
function updateKeluhanSections(value) {
    const container = document.getElementById('keluhan-container');
    const template = container.querySelector('.keluhan-section');
    
    // Remove existing sections except the first one
    while (container.children.length > 1) {
        container.removeChild(container.lastChild);
    }
    
    // Clone and add new sections based on selected value
    for (let i = 1; i < value; i++) {
        const newSection = template.cloneNode(true);
        const keluhanIndex = i;
        
        // Update section title
        const title = newSection.querySelector('.keluhan-number');
        title.textContent = `(Keluhan ${i + 1})`;
        
        // Update form field names with proper index
        newSection.querySelectorAll('select, input, textarea').forEach(element => {
            if (element.name && element.name.includes('[')) {
                element.name = element.name.replace(/\[\d+\]/, `[${keluhanIndex}]`);
            }
        });
        
        // Reset form values
        newSection.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
        newSection.querySelectorAll('input').forEach(input => input.value = '');
        newSection.querySelectorAll('textarea').forEach(textarea => textarea.value = '');
        
        // Set up obat button handler
        const tambahObatBtn = newSection.querySelector('.tambah-obat-btn');
        tambahObatBtn.onclick = function() { tambahObat(this); };
        
        container.appendChild(newSection);
    }
}

// Handle adding obat rows
function tambahObat(button) {
    const keluhanSection = button.closest('.keluhan-section');
    const container = keluhanSection.querySelector('.obat-container');
    const template = container.querySelector('.obat-row');
    const keluhanIndex = Array.from(document.querySelectorAll('.keluhan-section')).indexOf(keluhanSection);
    
    const newRow = template.cloneNode(true);
    
    // Reset values and update indices
    newRow.querySelectorAll('input, select').forEach(element => {
        element.value = '';
        if (element.name && element.name.includes('[')) {
            // Update array index for all form elements
            if (element.name.startsWith('jumlah_pakai[') || element.name.startsWith('waktu_pakai[')) {
                element.name = element.name.replace(/\[\d+\]/, `[${keluhanIndex}]`);
            } else {
                element.name = element.name.replace(/\[\d+\]/, `[${keluhanIndex}]`);
            }
        }
    });
    
    // Add remove button
    const removeBtn = document.createElement('div');
    removeBtn.className = 'md:col-span-4 flex justify-end';
    removeBtn.innerHTML = `
        <button type="button" onclick="this.closest('.obat-row').remove()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Hapus
        </button>
    `;
    newRow.appendChild(removeBtn);
    
    container.appendChild(newRow);
}

// Initialize the tambah obat button handlers
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.tambah-obat-btn').forEach(button => {
        button.onclick = function() { tambahObat(this); };
    });
});

function updatePasienInfo(select) {
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption.value) {
        document.getElementById('nama_pasien').value = selectedOption.getAttribute('data-nama');
        document.getElementById('tanggal_lahir').value = selectedOption.getAttribute('data-tanggal');
        document.getElementById('nik_karyawan').value = selectedOption.getAttribute('data-nik');
    } else {
        document.getElementById('nama_pasien').value = '';
        document.getElementById('tanggal_lahir').value = '';
        document.getElementById('nik_karyawan').value = '';
    }
}

function tambahObat() {
    const container = document.getElementById('obat-container');
    const newRow = document.querySelector('.obat-row').cloneNode(true);
    
    // Reset values
    newRow.querySelectorAll('input').forEach(input => input.value = '');
    newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
    
    // Add remove button
    const removeBtn = document.createElement('div');
    removeBtn.className = 'md:col-span-4 flex justify-end';
    removeBtn.innerHTML = `
        <button type="button" onclick="this.closest('.obat-row').remove()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Hapus
        </button>
    `;
    newRow.appendChild(removeBtn);
    
    container.appendChild(newRow);
}
</script>
@endpush
@endsection
