@extends('layouts.app')

@section('page-title', 'Detail Rekam Medis')

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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    Detail Riwayat Rekam Medis
                </h1>
                <p class="text-gray-600 mt-1 ml-1">Informasi lengkap pasien dan riwayat kunjungan</p>
            </div>
        </div>
    </div>

    <!-- Informasi Pasien Section -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4">
            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Informasi Pasien
            </h2>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-start">
                    <span class="font-semibold text-gray-700 w-40 flex-shrink-0">No RM</span>
                    <span class="text-gray-600 mx-2">:</span>
                    <span class="text-gray-900 font-medium">{{ ($rekamMedis->keluarga->karyawan->nik_karyawan ?? '') . '-' . ($rekamMedis->keluarga->kode_hubungan ?? '') }}</span>
                </div>
                <div class="flex items-start">
                    <span class="font-semibold text-gray-700 w-40 flex-shrink-0">Nama Pasien</span>
                    <span class="text-gray-600 mx-2">:</span>
                    <span class="text-gray-900 font-medium">{{ $rekamMedis->keluarga->nama_keluarga ?? '-' }}</span>
                </div>
                <div class="flex items-start">
                    <span class="font-semibold text-gray-700 w-40 flex-shrink-0">NIK Karyawan</span>
                    <span class="text-gray-600 mx-2">:</span>
                    <span class="text-gray-900">{{ $rekamMedis->keluarga->karyawan->nik_karyawan ?? '-' }}</span>
                </div>
                <div class="flex items-start">
                    <span class="font-semibold text-gray-700 w-40 flex-shrink-0">Hubungan</span>
                    <span class="text-gray-600 mx-2">:</span>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                        {{ $rekamMedis->keluarga->kode_hubungan ?? '' }}. {{ $rekamMedis->keluarga->hubungan->hubungan ?? '-' }}
                    </span>
                </div>
                <div class="flex items-start">
                    <span class="font-semibold text-gray-700 w-40 flex-shrink-0">Tanggal Lahir</span>
                    <span class="text-gray-600 mx-2">:</span>
                    <span class="text-gray-900">{{ $rekamMedis->keluarga->tanggal_lahir ? $rekamMedis->keluarga->tanggal_lahir->format('d-m-Y') : '-' }}</span>
                </div>
                <div class="flex items-start">
                    <span class="font-semibold text-gray-700 w-40 flex-shrink-0">Jenis Kelamin</span>
                    <span class="text-gray-600 mx-2">:</span>
                    <span class="text-gray-900">{{ $rekamMedis->keluarga->jenis_kelamin ?? '-' }}</span>
                </div>
                <div class="flex items-start md:col-span-2">
                    <span class="font-semibold text-gray-700 w-40 flex-shrink-0">Alamat</span>
                    <span class="text-gray-600 mx-2">:</span>
                    <span class="text-gray-900">{{ $rekamMedis->keluarga->alamat ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Kunjungan Section -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Riwayat Kunjungan Pasien
                <span class="ml-2 px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm text-purple-500">
                    {{ $riwayatKunjungan->count() }} Kunjungan
                </span>
            </h2>
        </div>

        <div class="p-6">
            @forelse($riwayatKunjungan as $index => $kunjungan)
            <!-- Kunjungan Card -->
            <div class="bg-gray-50 rounded-lg border-2 border-gray-200 overflow-hidden mb-6 last:mb-0 ">
                <!-- Kunjungan Header -->
                <div class="bg-gradient-to-r from-blue-500 to-cyan-500 px-6 py-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-white font-semibold flex items-center gap-2">
                            <span class="bg-white bg-opacity-30 rounded-full px-3 py-1 text-sm text-purple-500">
                                Kunjungan #{{ $riwayatKunjungan->count() - $index }}
                            </span>
                        </h3>
                        <div class="flex items-center text-white text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $kunjungan->tanggal_periksa->format('d F Y') }}
                        </div>
                    </div>
                </div>

                <!-- Kunjungan Details -->
                <div class="p-6">
                    <div class="mb-4">
                        <div class="flex items-start mb-2">
                            <span class="font-semibold text-gray-700 w-32">Petugas</span>
                            <span class="text-gray-600 mx-2">:</span>
                            <span class="text-gray-900">{{ $kunjungan->user->nama_lengkap ?? '-' }}</span>
                        </div>
                        <div class="flex items-start">
                            <span class="font-semibold text-gray-700 w-32">Jumlah Keluhan</span>
                            <span class="text-gray-600 mx-2">:</span>
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">
                                {{ $kunjungan->jumlah_keluhan }} Keluhan
                            </span>
                        </div>
                    </div>

                    <!-- Keluhan List -->
                    @foreach($kunjungan->keluhans as $keluhanIndex => $keluhan)
                    <div class="bg-white rounded-lg border border-gray-200 p-5 mb-4 last:mb-0">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
                            <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                                <span class="bg-red-100 text-red-700 rounded-full px-3 py-1 text-sm">
                                    Keluhan {{ $keluhanIndex + 1 }}
                                </span>
                            </h4>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <div class="text-sm font-semibold text-gray-600 mb-1">Diagnosa / Penyakit</div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-gray-900 font-medium">{{ $keluhan->diagnosa->nama_diagnosa ?? '-' }}</span>
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-semibold text-gray-600 mb-1">Terapi</div>
                                <span class="px-3 py-1
                                    @if($keluhan->terapi == 'Obat') bg-purple-100 text-purple-800
                                    @elseif($keluhan->terapi == 'Lab') bg-orange-100 text-orange-800
                                    @else bg-green-100 text-green-800
                                    @endif
                                    text-sm font-medium rounded-full inline-block">
                                    {{ $keluhan->terapi }}
                                </span>
                            </div>
                        </div>

                        @if($keluhan->keterangan)
                        <div class="mb-4">
                            <div class="text-sm font-semibold text-gray-600 mb-1">Keterangan</div>
                            <div class="bg-gray-50 rounded-lg p-3 text-gray-700 text-sm">
                                {{ $keluhan->keterangan }}
                            </div>
                        </div>
                        @endif

                        <!-- Resep Obat -->
                        @if($keluhan->obat)
                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <h5 class="font-semibold text-green-800 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                Resep Obat
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                <div class="flex items-start">
                                    <span class="font-semibold text-gray-700 w-32">Nama Obat</span>
                                    <span class="text-gray-600 mx-2">:</span>
                                    <span class="text-gray-900 font-medium">{{ $keluhan->obat->nama_obat }}</span>
                                </div>
                                @if($keluhan->jumlah_obat)
                                <div class="flex items-start">
                                    <span class="font-semibold text-gray-700 w-32">Jumlah</span>
                                    <span class="text-gray-600 mx-2">:</span>
                                    <span class="text-gray-900">{{ $keluhan->jumlah_obat }}</span>
                                </div>
                                @endif
                                @if($keluhan->aturan_pakai)
                                <div class="flex items-start">
                                    <span class="font-semibold text-gray-700 w-32">Aturan Pakai</span>
                                    <span class="text-gray-600 mx-2">:</span>
                                    <span class="text-gray-900">{{ $keluhan->aturan_pakai }}</span>
                                </div>
                                @endif
                                @if($keluhan->waktu_pakai)
                                <div class="flex items-start">
                                    <span class="font-semibold text-gray-700 w-32">Waktu Pakai</span>
                                    <span class="text-gray-600 mx-2">:</span>
                                    <span class="text-gray-900">{{ $keluhan->waktu_pakai }} hari</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="bg-gray-100 rounded-lg p-3 text-center text-gray-500 text-sm">
                            Tidak ada resep obat
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-500">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-lg font-medium">Belum ada riwayat kunjungan</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
            <a href="{{ route('rekam-medis.index') }}" class="px-6 py-2.5 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-medium rounded-lg transition-all hover:shadow-md inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>

            <div class="flex gap-3">
                <a href="{{ route('rekam-medis.edit', $rekamMedis->id_rekam) }}" class="px-6 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition-all hover:shadow-md inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
