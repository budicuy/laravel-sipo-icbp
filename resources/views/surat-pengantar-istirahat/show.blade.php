@extends('layouts.app')

@section('content')
    <div class="p-6">
        <div class="bg-white rounded-lg shadow-md p-6 max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-6 pb-4 border-b-2 border-green-500">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Detail Surat Pengantar Istirahat</h2>
                        <p class="text-sm text-gray-600 mt-1">Informasi lengkap surat pengantar istirahat</p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('surat-pengantar-istirahat.cetak', $surat->id_surat) }}" target="_blank"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak
                        </a>
                        <a href="{{ route('surat-pengantar-istirahat.edit', $surat->id_surat) }}"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </a>
                    </div>
                </div>
            </div>

            <!-- Informasi Surat -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Informasi Surat
                    </h3>
                    <div class="space-y-2">
                        <div class="flex">
                            <span class="text-gray-600 w-32">Nomor Surat:</span>
                            <span class="font-medium">{{ $surat->nomor_surat }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-600 w-32">Tanggal Surat:</span>
                            <span
                                class="font-medium">{{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d F Y') }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-600 w-32">Dokter:</span>
                            <span class="font-medium">{{ Auth::user()->nama_lengkap }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Informasi Pasien
                    </h3>
                    <div class="space-y-2">
                        <div class="flex">
                            <span class="text-gray-600 w-32">NIK Karyawan:</span>
                            <span class="font-medium">{{ $surat->nik_karyawan ?? '-' }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-600 w-32">Nama Karyawan:</span>
                            <span class="font-medium">{{ $surat->nama_karyawan ?? 'External' }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-600 w-32">Nama Pasien:</span>
                            <span class="font-medium">{{ $surat->nama_pasien }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-600 w-32">Departemen:</span>
                            <span class="font-medium">{{ $surat->departemen ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Istirahat -->
            <div class="bg-yellow-50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Informasi Istirahat
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <span class="text-gray-600 block">Lama Istirahat:</span>
                        <span class="font-semibold text-lg">{{ $surat->lama_istirahat }} hari</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block">Tanggal Mulai:</span>
                        <span
                            class="font-semibold">{{ \Carbon\Carbon::parse($surat->tanggal_mulai_istirahat)->format('d F Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block">Tanggal Selesai:</span>
                        <span
                            class="font-semibold">{{ \Carbon\Carbon::parse($surat->tanggal_selesai_istirahat)->format('d F Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Informasi Medis -->
            <div class="bg-red-50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Informasi Medis
                </h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-gray-600 block">Diagnosa Utama:</span>
                        <p class="font-medium bg-white p-3 rounded border border-gray-200">{{ $surat->diagnosa_utama }}</p>
                    </div>
                    @if ($surat->keterangan_tambahan)
                        <div>
                            <span class="text-gray-600 block">Keterangan Tambahan:</span>
                            <p class="font-medium bg-white p-3 rounded border border-gray-200">
                                {{ $surat->keterangan_tambahan }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informasi Rekam Medis -->
            <div class="bg-green-50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Informasi Rekam Medis
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-gray-600 block">Tanggal Periksa:</span>
                        <span
                            class="font-medium">{{ \Carbon\Carbon::parse($surat->rekamMedis->tanggal_periksa)->format('d F Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block">Status Rekam Medis:</span>
                        <span class="font-medium">
                            <span
                                class="px-2 py-1 text-xs rounded-full {{ $surat->rekamMedis->status === 'On Progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                {{ $surat->rekamMedis->status }}
                            </span>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600 block">Jumlah Keluhan:</span>
                        <span class="font-medium">{{ $surat->rekamMedis->jumlah_keluhan }} keluhan</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('surat-pengantar-istirahat.cetak', $surat->id_surat) }}" target="_blank"
                    class="flex-1 sm:flex-none px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Surat
                </a>
                <a href="{{ route('surat-pengantar-istirahat.edit', $surat->id_surat) }}"
                    class="flex-1 sm:flex-none px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Surat
                </a>
                <form action="{{ route('surat-pengantar-istirahat.destroy', $surat->id_surat) }}" method="POST"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="flex-1 sm:flex-none px-8 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Surat
                    </button>
                </form>
                <a href="{{ route('surat-pengantar-istirahat.index') }}"
                    class="flex-1 sm:flex-none px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>
@endsection
