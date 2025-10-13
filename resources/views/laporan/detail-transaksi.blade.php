@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('page-title', 'Detail Transaksi')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('laporan.transaksi') }}" class="inline-flex items-center px-3 py-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition-all shadow-sm border border-gray-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <div class="bg-gradient-to-r from-blue-600 to-cyan-600 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002 2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                Detail Transaksi
            </h1>
        </div>
        <p class="text-gray-600 mt-2 ml-1">Informasi lengkap transaksi pemeriksaan pasien</p>
    </div>

    <!-- Informasi Pasien, Kunjungan & Karyawan -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Card Informasi Pasien -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
            <div class="flex items-center gap-2 mb-4">
                <div class="bg-gradient-to-r from-blue-600 to-cyan-600 p-2 rounded-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Informasi Pasien</h3>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">No. RM</span>
                    <span class="text-sm font-medium text-gray-900">{{ $rekamMedis->keluarga->no_rm }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Nama Pasien</span>
                    <span class="text-sm font-medium text-gray-900">{{ $rekamMedis->keluarga->nama_keluarga }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Hubungan</span>
                    <span class="text-sm font-medium text-gray-900">{{ $rekamMedis->keluarga->hubungan->nama_hubungan ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm text-gray-600">Tanggal Lahir</span>
                    <span class="text-sm font-medium text-gray-900">{{ $rekamMedis->keluarga->tanggal_lahir->format('d-m-Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Card Informasi Kunjungan -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
            <div class="flex items-center gap-2 mb-4">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-2 rounded-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Informasi Kunjungan</h3>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">No. Registrasi</span>
                    <span class="text-sm font-medium text-blue-600">{{ $kunjungan->kode_transaksi ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Tanggal Periksa</span>
                    <span class="text-sm font-medium text-gray-900">{{ $rekamMedis->tanggal_periksa->format('d-m-Y') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Petugas</span>
                    <span class="text-sm font-medium text-gray-900">{{ $rekamMedis->user->nama_lengkap ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm text-gray-600">Status</span>
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $rekamMedis->status == 'Close' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-yellow-100 text-yellow-800 border border-yellow-200' }}">
                        {{ $rekamMedis->status }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Card Informasi Karyawan -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
            <div class="flex items-center gap-2 mb-4">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-2 rounded-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Informasi Karyawan</h3>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">NIK</span>
                    <span class="text-sm font-medium text-gray-900">{{ $rekamMedis->keluarga->karyawan->nik_karyawan ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Nama Karyawan</span>
                    <span class="text-sm font-medium text-gray-900">{{ $rekamMedis->keluarga->karyawan->nama_karyawan ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm text-gray-600">Departemen</span>
                    <span class="text-sm font-medium text-gray-900">{{ $rekamMedis->keluarga->karyawan->departemen->nama_departemen ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Diagnosa & Obat -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <div class="bg-gradient-to-r from-orange-600 to-red-600 p-2 rounded-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Detail Diagnosa & Obat</h3>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($keluhanByDiagnosa as $diagnosa => $keluhans)
            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                <div class="mb-3">
                    <h4 class="text-md font-semibold text-gray-800 mb-1">{{ $diagnosa }}</h4>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Obat</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Jumlah</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Aturan Pakai</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Harga Satuan</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($keluhans as $keluhan)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ $keluhan->obat->nama_obat ?? '-' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ $keluhan->jumlah_obat ?? 0 }} {{ $keluhan->obat->satuanObat->nama_satuan ?? '' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-600">{{ $keluhan->aturan_pakai ?: '-' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900">Rp{{ number_format($keluhan->obat->harga_per_satuan ?? 0, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-sm font-medium text-gray-900">Rp{{ number_format(($keluhan->jumlah_obat ?? 0) * ($keluhan->obat->harga_per_satuan ?? 0), 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="text-sm font-medium">Tidak ada data diagnosa dan obat</span>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Ringkasan Biaya -->
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl shadow-md p-6 border border-green-100">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-1">Total Biaya Transaksi</h3>
                <p class="text-sm text-gray-600">Total keseluruhan biaya obat untuk transaksi ini</p>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold text-green-600">Rp{{ number_format($totalBiaya, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
