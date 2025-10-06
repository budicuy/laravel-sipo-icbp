@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md p-6 max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8 pb-4 border-b-2 border-blue-500">
            <h2 class="text-2xl font-bold text-gray-800">Detail Riwayat Rekam Medis Karyawan</h2>
            <p class="text-sm text-gray-600 mt-1">Informasi lengkap pasien dan riwayat kunjungan</p>
        </div>

        <!-- Informasi Pasien Section -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Informasi Pasien
            </h3>

            <div class="bg-gray-50 rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start">
                        <span class="font-semibold text-gray-700 w-36 flex-shrink-0">No RM</span>
                        <span class="text-gray-600 mr-2">:</span>
                        <span class="text-gray-900">0001/NDL/BJM/10/2025</span>
                    </div>
                    <div class="flex items-start">
                        <span class="font-semibold text-gray-700 w-36 flex-shrink-0">Nama Pasien</span>
                        <span class="text-gray-600 mr-2">:</span>
                        <span class="text-gray-900">Awang Rio</span>
                    </div>
                    <div class="flex items-start">
                        <span class="font-semibold text-gray-700 w-36 flex-shrink-0">Hubungan</span>
                        <span class="text-gray-600 mr-2">:</span>
                        <span class="text-gray-900">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                Karyawan
                            </span>
                        </span>
                    </div>
                    <div class="flex items-start">
                        <span class="font-semibold text-gray-700 w-36 flex-shrink-0">Tanggal Lahir</span>
                        <span class="text-gray-600 mr-2">:</span>
                        <span class="text-gray-900">01-09-1998</span>
                    </div>
                    <div class="flex items-start">
                        <span class="font-semibold text-gray-700 w-36 flex-shrink-0">Jenis Kelamin</span>
                        <span class="text-gray-600 mr-2">:</span>
                        <span class="text-gray-900">Laki - Laki</span>
                    </div>
                    <div class="flex items-start">
                        <span class="font-semibold text-gray-700 w-36 flex-shrink-0">Alamat</span>
                        <span class="text-gray-600 mr-2">:</span>
                        <span class="text-gray-900">Jl. Belitung Darat GG. BDA Banjarmasin Utara</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Kunjungan & Resep Obat Section -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Riwayat Kunjungan & Resep Obat
            </h3>

            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-700 to-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider border-r border-gray-600">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider border-r border-gray-600">Tanggal Kunjungan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider border-r border-gray-600">Diagnosa/Penyakit</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider border-r border-gray-600">Nama Obat</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider border-r border-gray-600">Jumlah</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider border-r border-gray-600">Keterangan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider border-r border-gray-600">Harga Satuan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Row 1 - Obat 1 -->
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200 font-medium" rowspan="3">1</td>
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200" rowspan="3">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    03-10-2025
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm border-r border-gray-200" rowspan="3">
                                <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                    Demam Berdarah Dengue (DBD)
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                    </svg>
                                    Paracetamol
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900 border-r border-gray-200 font-medium">12</td>
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">3 kali sehari</td>
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200 text-right">Rp 400</td>
                            <td class="px-4 py-3 text-sm text-gray-900 font-semibold text-right">Rp 4.800</td>
                        </tr>
                        <!-- Row 1 - Obat 2 -->
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                    </svg>
                                    Piracetam
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900 border-r border-gray-200 font-medium">12</td>
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">3 kali sehari</td>
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200 text-right">Rp 1.000</td>
                            <td class="px-4 py-3 text-sm text-gray-900 font-semibold text-right">Rp 12.000</td>
                        </tr>
                        <!-- Row 1 - Obat 3 -->
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                    </svg>
                                    Vit C
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900 border-r border-gray-200 font-medium">4</td>
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">1 kali sehari</td>
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200 text-right">Rp 800</td>
                            <td class="px-4 py-3 text-sm text-gray-900 font-semibold text-right">Rp 3.200</td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-gradient-to-r from-yellow-50 to-yellow-100">
                        <tr>
                            <td colspan="7" class="px-4 py-4 text-right text-base font-bold text-gray-800 border-r border-gray-300">
                                <div class="flex items-center justify-end">
                                    <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Total Biaya
                                </div>
                            </td>
                            <td class="px-4 py-4 text-base font-bold text-green-700 text-right">
                                Rp 20.000
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Button Kembali -->
        <div class="flex justify-start pt-6 border-t border-gray-200 mt-8">
            <a
                href="{{ route('rekam-medis.index') }}"
                class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>
</div>
@endsection
