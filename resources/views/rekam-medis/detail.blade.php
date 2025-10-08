@extends('layouts.app')

@section('page-title', 'Detail Rekam Medis')

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .print-area, .print-area * {
            visibility: visible;
        }
        .print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
        .bg-gradient-to-r, .bg-gradient-to-br {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
@endpush

@section('content')
<div class="p-6 bg-gray-50 min-h-screen print-area">
    <!-- Header Section -->
    <div class="mb-6 no-print">
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
    <div class="bg-white rounded-xl shadow-md border border-gray-200 mb-6 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
                <h2 class="text-xl font-bold text-white">Informasi Pasien</h2>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">No Rekam Medis</div>
                    <div class="text-lg font-bold text-blue-600">{{ ($rekamMedis->keluarga->karyawan->nik_karyawan ?? '') . '-' . ($rekamMedis->keluarga->kode_hubungan ?? '') }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Nama Pasien</div>
                    <div class="text-lg font-bold text-gray-900">{{ $rekamMedis->keluarga->nama_keluarga ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">NIK Karyawan</div>
                    <div class="text-lg font-bold text-gray-900">{{ $rekamMedis->keluarga->karyawan->nik_karyawan ?? '-' }}</div>
                </div>
            </div>
            <div class="border-t pt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <div class="text-xs text-gray-500 font-medium">Hubungan</div>
                        <div class="text-sm font-semibold text-gray-900">{{ $rekamMedis->keluarga->kode_hubungan ?? '' }}. {{ $rekamMedis->keluarga->hubungan->hubungan ?? '-' }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <div class="text-xs text-gray-500 font-medium">Tanggal Lahir</div>
                        <div class="text-sm font-semibold text-gray-900">{{ $rekamMedis->keluarga->tanggal_lahir ? $rekamMedis->keluarga->tanggal_lahir->format('d F Y') : '-' }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($rekamMedis->keluarga->jenis_kelamin == 'Laki-Laki')
                        <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-pink-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                    <div>
                        <div class="text-xs text-gray-500 font-medium">Jenis Kelamin</div>
                        <div class="text-sm font-semibold text-gray-900">{{ $rekamMedis->keluarga->jenis_kelamin ?? '-' }}</div>
                    </div>
                </div>
            </div>
            <div class="border-t pt-4 mt-4">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <div class="text-xs text-gray-500 font-medium mb-1">Alamat Lengkap</div>
                        <div class="text-sm text-gray-900">{{ $rekamMedis->keluarga->alamat ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Kunjungan Section -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 mb-6 overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                    <h2 class="text-xl font-bold text-white">Riwayat Kunjungan Pasien</h2>
                </div>
                <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-white text-sm font-semibold">{{ $riwayatKunjungan->count() }} Kunjungan</span>
            </div>
        </div>

        <div class="p-6 space-y-6">
            @forelse($riwayatKunjungan as $index => $kunjungan)
            <div class="border-l-4 border-blue-500 bg-gray-50 rounded-r-lg p-5">
                <div class="flex items-center justify-between mb-4 pb-3 border-b">
                    <div class="flex items-center gap-3">
                        <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-bold">Kunjungan #{{ $riwayatKunjungan->count() - $index }}</span>
                        <div class="flex items-center gap-2 text-gray-700">
                            <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">{{ $kunjungan->tanggal_periksa->format('d F Y') }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium">{{ $kunjungan->user->nama_lengkap ?? '-' }}</span>
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach($kunjungan->keluhans as $keluhanIndex => $keluhan)
                    <div class="bg-white rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="bg-red-500 text-white px-2 py-0.5 rounded text-xs font-bold">{{ $keluhanIndex + 1 }}</span>
                                <span class="font-bold text-gray-900">{{ $keluhan->diagnosa->nama_diagnosa ?? '-' }}</span>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $keluhan->terapi == 'Obat' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ $keluhan->terapi == 'Lab' ? 'bg-orange-100 text-orange-700' : '' }}
                                {{ $keluhan->terapi == 'Istirahat' ? 'bg-green-100 text-green-700' : '' }}">
                                {{ $keluhan->terapi }}
                            </span>
                        </div>

                        @if($keluhan->keterangan)
                        <div class="mb-3 text-sm text-gray-600 bg-blue-50 p-3 rounded border-l-4 border-blue-400">
                            <span class="font-semibold text-blue-700">Catatan:</span> {{ $keluhan->keterangan }}
                        </div>
                        @endif

                        @if($keluhan->obat)
                        <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-bold text-green-800">Resep Obat</span>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                <div>
                                    <div class="text-xs text-green-600 font-medium">Nama Obat</div>
                                    <div class="font-semibold text-gray-900">{{ $keluhan->obat->nama_obat }}</div>
                                </div>
                                @if($keluhan->jumlah_obat)
                                <div>
                                    <div class="text-xs text-green-600 font-medium">Jumlah</div>
                                    <div class="font-semibold text-gray-900">{{ $keluhan->jumlah_obat }} {{ $keluhan->obat->satuan_obat->satuan ?? '' }}</div>
                                </div>
                                @endif
                                @if($keluhan->aturan_pakai)
                                <div>
                                    <div class="text-xs text-green-600 font-medium">Aturan Pakai</div>
                                    <div class="font-semibold text-gray-900">{{ $keluhan->aturan_pakai }}</div>
                                </div>
                                @endif
                                @if($keluhan->waktu_pakai)
                                <div>
                                    <div class="text-xs text-green-600 font-medium">Durasi</div>
                                    <div class="font-semibold text-gray-900">{{ $keluhan->waktu_pakai }} Hari</div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="text-center py-3 text-gray-400 text-sm border border-dashed border-gray-300 rounded">
                            Tidak ada resep obat
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
                <p class="text-gray-500 font-medium">Belum ada riwayat kunjungan</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden no-print">
        <div class="p-6">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                <a href="{{ route('rekam-medis.index') }}"
                   class="w-full sm:w-auto px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all inline-flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                    </svg>
                    <span>Kembali</span>
                </a>

                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <button onclick="window.print()"
                            class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all inline-flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Cetak</span>
                    </button>

                    <a href="{{ route('rekam-medis.edit', $rekamMedis->id_rekam) }}"
                       class="w-full sm:w-auto px-6 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg transition-all inline-flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                        </svg>
                        <span>Edit</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection