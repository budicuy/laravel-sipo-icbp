@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3 mb-2">
                    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 p-3 rounded-lg shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    Detail Riwayat Pasien
                </h1>
                <p class="text-gray-600 ml-1">Informasi lengkap kunjungan dan resep obat pasien</p>
            </div>
            <a href="{{ route('kunjungan.index') }}" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Patient Info Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Informasi Pasien
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-4">
            <div class="flex flex-col">
                <label class="text-sm font-semibold text-gray-600 mb-1">No RM</label>
                <p class="text-base font-medium text-gray-900">0001/HCL/RM/10/2025</p>
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-semibold text-gray-600 mb-1">Nama Pasien</label>
                <p class="text-base font-medium text-gray-900">Awang Rio</p>
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-semibold text-gray-600 mb-1">Hubungan</label>
                <p class="text-base font-medium text-gray-900">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Karyawan</span>
                </p>
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-semibold text-gray-600 mb-1">Tanggal Lahir</label>
                <p class="text-base font-medium text-gray-900">01-09-1995</p>
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-semibold text-gray-600 mb-1">Jenis Kelamin</label>
                <p class="text-base font-medium text-gray-900">Laki - Laki</p>
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-semibold text-gray-600 mb-1">Alamat</label>
                <p class="text-base font-medium text-gray-900">Jl. Banteng Darat GG. BCA Banjarmasin Utara</p>
            </div>
        </div>
    </div>

    <!-- Medical History Table -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Table Header -->
        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4">
            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Riwayat Kunjungan & Resep Obat
            </h2>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Tanggal Kunjungan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Diagnosa/Penyakit</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Nama Obat</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Jumlah</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Keterangan</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider border-r border-gray-700">Harga Satuan</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Row 1 -->
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="px-6 py-4 text-center text-sm text-gray-900 border-r border-gray-200" rowspan="3">1</td>
                        <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200" rowspan="3">
                            <div class="font-medium">03-10-2025</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200" rowspan="3">
                            <div class="font-medium text-gray-800">Demam Berdarah Dengue (DBD)</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">
                            <div class="font-medium">Paracetamol</div>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-900 border-r border-gray-200">12</td>
                        <td class="px-6 py-4 text-sm text-gray-600 border-r border-gray-200">3 kali sehari</td>
                        <td class="px-6 py-4 text-center text-sm text-gray-900 border-r border-gray-200">Rp 400</td>
                        <td class="px-6 py-4 text-center text-sm font-semibold text-gray-900">Rp 4.800</td>
                    </tr>
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">
                            <div class="font-medium">Antosanin</div>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-900 border-r border-gray-200">12</td>
                        <td class="px-6 py-4 text-sm text-gray-600 border-r border-gray-200">3 kali sehari</td>
                        <td class="px-6 py-4 text-center text-sm text-gray-900 border-r border-gray-200">Rp 1.000</td>
                        <td class="px-6 py-4 text-center text-sm font-semibold text-gray-900">Rp 12.000</td>
                    </tr>
                    <tr class="hover:bg-blue-50 transition-colors border-b-2 border-gray-300">
                        <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">
                            <div class="font-medium">Vit C</div>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-900 border-r border-gray-200">4</td>
                        <td class="px-6 py-4 text-sm text-gray-600 border-r border-gray-200">1 kali sehari</td>
                        <td class="px-6 py-4 text-center text-sm text-gray-900 border-r border-gray-200">Rp 800</td>
                        <td class="px-6 py-4 text-center text-sm font-semibold text-gray-900">Rp 3.200</td>
                    </tr>

                    <!-- Subtotal Row for Visit 1 -->
                    <tr class="bg-blue-50">
                        <td colspan="7" class="px-6 py-3 text-right text-sm font-bold text-gray-800 border-r border-gray-300">
                            Total Biaya Kunjungan 03-10-2025
                        </td>
                        <td class="px-6 py-3 text-center text-base font-bold text-blue-700">
                            Rp 20.000
                        </td>
                    </tr>

                    <!-- Row 2 (Second Visit) -->
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="px-6 py-4 text-center text-sm text-gray-900 border-r border-gray-200" rowspan="2">2</td>
                        <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200" rowspan="2">
                            <div class="font-medium">05-10-2025</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200" rowspan="2">
                            <div class="font-medium text-gray-800">Flu & Batuk</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">
                            <div class="font-medium">OBH Combi</div>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-900 border-r border-gray-200">2</td>
                        <td class="px-6 py-4 text-sm text-gray-600 border-r border-gray-200">3 kali sehari</td>
                        <td class="px-6 py-4 text-center text-sm text-gray-900 border-r border-gray-200">Rp 15.000</td>
                        <td class="px-6 py-4 text-center text-sm font-semibold text-gray-900">Rp 30.000</td>
                    </tr>
                    <tr class="hover:bg-blue-50 transition-colors border-b-2 border-gray-300">
                        <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200">
                            <div class="font-medium">Paracetamol</div>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-900 border-r border-gray-200">10</td>
                        <td class="px-6 py-4 text-sm text-gray-600 border-r border-gray-200">3 kali sehari</td>
                        <td class="px-6 py-4 text-center text-sm text-gray-900 border-r border-gray-200">Rp 400</td>
                        <td class="px-6 py-4 text-center text-sm font-semibold text-gray-900">Rp 4.000</td>
                    </tr>

                    <!-- Subtotal Row for Visit 2 -->
                    <tr class="bg-blue-50">
                        <td colspan="7" class="px-6 py-3 text-right text-sm font-bold text-gray-800 border-r border-gray-300">
                            Total Biaya Kunjungan 05-10-2025
                        </td>
                        <td class="px-6 py-3 text-center text-base font-bold text-blue-700">
                            Rp 34.000
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Grand Total Footer -->
        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="text-white font-semibold text-lg">
                    Total Keseluruhan Biaya
                </div>
                <div class="text-white font-bold text-2xl">
                    Rp 54.000
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex gap-4 justify-end">
        <button class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2 font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak Riwayat
        </button>
    </div>
</div>
@endsection
