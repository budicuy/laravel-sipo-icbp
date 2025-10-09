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

    /* Modern card styling */
    .modern-card {
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }

    /* Enhanced button styles */
    .btn-modern {
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }

    /* Status badge improvements */
    .status-badge {
        font-weight: 600;
        letter-spacing: 0.025em;
    }

    /* Patient info card styling */
    .info-item {
        background-color: #f9fafb;
    }

    /* Timeline styling */
    .timeline-item {
        position: relative;
        padding-left: 1.5rem;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -1px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #3b82f6, #10b981);
    }

    /* Better typography */
    .section-title {
        font-weight: 700;
        letter-spacing: -0.025em;
    }

    /* Improved data presentation */
    .data-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .data-value {
        font-weight: 600;
        color: #111827;
    }

    /* Prescription card styling */
    .prescription-card {
        border-left: 4px solid #10b981;
        background-color: #f0fdf4;
    }

    /* Empty state styling */
    .empty-state {
        color: #9ca3af;
        text-align: center;
        padding: 3rem 1rem;
    }
</style>
@endpush

@section('content')
<div class="p-4 lg:p-6 bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen print-area">
    <!-- Modern Header Section -->
    <div class="mb-8 no-print">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-6">
            <a href="{{ route('rekam-medis.index') }}" class="p-3 bg-white hover:bg-gray-50 rounded-xl shadow-md transition-all">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 flex items-center gap-3 mb-2">
                    <div class="bg-gradient-to-br from-blue-600 to-cyan-600 p-3 rounded-xl shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    Detail Rekam Medis
                </h1>
                <p class="text-gray-600 text-lg">Informasi lengkap pasien dan riwayat medis</p>
            </div>
        </div>
    </div>

    <!-- Enhanced Patient Information Section -->
    <div class="bg-white rounded-2xl shadow-lg modern-card mb-8 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-blue-800 p-3 rounded-xl shadow-lg border-2 border-white">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl lg:text-3xl font-bold text-white drop-shadow-md">Informasi Pasien</h2>
                        <p class="text-blue-100 text-sm lg:text-base mt-1">Data identitas dan kontak pasien</p>
                    </div>
                </div>
                <div class="bg-blue-800 px-5 py-2.5 rounded-full shadow-lg border-2 border-white">
                    <span class="text-white font-bold text-lg">RM: {{ ($rekamMedis->keluarga->karyawan->nik_karyawan ?? '') . '-' . ($rekamMedis->keluarga->kode_hubungan ?? '') }}</span>
                </div>
            </div>
        </div>

        <div class="p-6 lg:p-8">
            <!-- Primary Information -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="info-item p-4 rounded-xl bg-gray-50">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="data-label">Nama Lengkap</div>
                    </div>
                    <div class="data-value text-xl">{{ $rekamMedis->keluarga->nama_keluarga ?? '-' }}</div>
                </div>

                <div class="info-item p-4 rounded-xl bg-gray-50">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-green-100 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="data-label">Tanggal Lahir</div>
                    </div>
                    <div class="data-value text-xl">{{ $rekamMedis->keluarga->tanggal_lahir ? $rekamMedis->keluarga->tanggal_lahir->format('d F Y') : '-' }}</div>
                </div>

                <div class="info-item p-4 rounded-xl bg-gray-50">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-purple-100 p-2 rounded-lg">
                            @if($rekamMedis->keluarga->jenis_kelamin == 'Laki-Laki')
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                        <div class="data-label">Jenis Kelamin</div>
                    </div>
                    <div class="data-value text-xl">{{ $rekamMedis->keluarga->jenis_kelamin ?? '-' }}</div>
                </div>
            </div>

            <!-- Secondary Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="flex items-start gap-4 p-4 rounded-xl bg-blue-50 border border-blue-200">
                    <div class="bg-blue-100 p-2 rounded-lg mt-1">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="data-label mb-1">Hubungan dengan Karyawan</div>
                        <div class="data-value text-lg">{{ $rekamMedis->keluarga->kode_hubungan ?? '' }}. {{ $rekamMedis->keluarga->hubungan->hubungan ?? '-' }}</div>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-4 rounded-xl bg-red-50 border border-red-200">
                    <div class="bg-red-100 p-2 rounded-lg mt-1">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="data-label mb-1">Alamat Lengkap</div>
                        <div class="data-value text-lg">{{ $rekamMedis->keluarga->alamat ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Medical History Section -->
    <div class="bg-white rounded-2xl shadow-lg modern-card mb-8 overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-green-800 p-3 rounded-xl shadow-lg border-2 border-white">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl lg:text-3xl font-bold text-white drop-shadow-md">Riwayat Kunjungan Medis</h2>
                        <p class="text-green-100 text-sm lg:text-base mt-1">Catatan pemeriksaan dan treatment pasien</p>
                    </div>
                </div>
                <div class="bg-green-800 px-5 py-2.5 rounded-full shadow-lg border-2 border-white">
                    <span class="text-white font-bold text-lg">{{ $riwayatKunjungan->count() }} Kunjungan</span>
                </div>
            </div>
        </div>

        <div class="p-6 lg:p-8">
            @forelse($riwayatKunjungan as $index => $kunjungan)
            <div class="timeline-item mb-8 last:mb-0">
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl p-6 border border-blue-100">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-6 pb-4 border-b border-blue-200">
                        <div class="flex items-center gap-4 mb-4 lg:mb-0">
                            <div class="bg-blue-600 text-white px-4 py-2 rounded-full font-bold">
                                Kunjungan #{{ $riwayatKunjungan->count() - $index }}
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="bg-white p-2 rounded-lg shadow-sm">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="data-label">Tanggal Periksa</div>
                                    <div class="data-value text-lg">{{ $kunjungan->tanggal_periksa->format('d F Y') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="bg-white p-2 rounded-lg shadow-sm">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <div class="data-label">Dokter</div>
                                <div class="data-value">{{ $kunjungan->user->nama_lengkap ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="bg-white p-2 rounded-lg shadow-sm">
                                <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <div class="data-label">Status</div>
                                <div>
                                    <span class="px-3 py-1
                                        @if($kunjungan->status == 'On Orogres') bg-yellow-100 text-yellow-800
                                        @elseif($kunjungan->status == 'Close') bg-green-100 text-green-800
                                        @endif
                                        rounded-full text-sm font-semibold">
                                        {{ $kunjungan->status ?? 'On Orogres' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach($kunjungan->keluhans as $keluhanIndex => $keluhan)
                        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-4">
                                <div class="flex items-center gap-3 mb-3 lg:mb-0">
                                    <div class="bg-red-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm">
                                        {{ $keluhanIndex + 1 }}
                                    </div>
                                    <div>
                                        <div class="data-label mb-1">Diagnosa</div>
                                        <div class="data-value text-lg font-semibold">{{ $keluhan->diagnosa->nama_diagnosa ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="status-badge px-3 py-1 rounded-full text-sm font-semibold
                                    {{ $keluhan->terapi == 'Obat' ? 'bg-purple-100 text-purple-700' : '' }}
                                    {{ $keluhan->terapi == 'Lab' ? 'bg-orange-100 text-orange-700' : '' }}
                                    {{ $keluhan->terapi == 'Istirahat' ? 'bg-green-100 text-green-700' : '' }}">
                                    {{ $keluhan->terapi }}
                                </div>
                            </div>

                            @if($keluhan->keterangan)
                            <div class="mb-4 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <div class="font-semibold text-blue-700 text-sm mb-1">Catatan Dokter</div>
                                        <div class="text-gray-700">{{ $keluhan->keterangan }}</div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($keluhan->obat)
                            <div class="prescription-card bg-green-50 rounded-xl p-4">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="bg-green-600 p-2 rounded-lg">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="font-bold text-green-800 text-lg">Resep Obat</div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div class="bg-white p-3 rounded-lg">
                                        <div class="data-label mb-1">Nama Obat</div>
                                        <div class="data-value font-semibold">{{ $keluhan->obat->nama_obat }}</div>
                                    </div>
                                    @if($keluhan->jumlah_obat)
                                    <div class="bg-white p-3 rounded-lg">
                                        <div class="data-label mb-1">Jumlah</div>
                                        <div class="data-value font-semibold">{{ $keluhan->jumlah_obat }} {{ $keluhan->obat->satuan_obat->satuan ?? '' }}</div>
                                    </div>
                                    @endif
                                    @if($keluhan->aturan_pakai)
                                    <div class="bg-white p-3 rounded-lg">
                                        <div class="data-label mb-1">Aturan Pakai</div>
                                        <div class="data-value font-semibold">{{ $keluhan->aturan_pakai }}</div>
                                    </div>
                                    @endif
                                    @if($keluhan->waktu_pakai)
                                    <div class="bg-white p-3 rounded-lg">
                                        <div class="data-label mb-1">Durasi</div>
                                        <div class="data-value font-semibold">{{ $keluhan->waktu_pakai }} Hari</div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @else
                            <div class="text-center py-6 text-gray-400 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
                                </svg>
                                <p class="font-medium">Tidak ada resep obat</p>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <svg class="w-20 h-20 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Riwayat Kunjungan</h3>
                <p class="text-gray-500">Pasien ini belum memiliki catatan kunjungan medis</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Modern Action Buttons -->
    <div class="bg-white rounded-2xl shadow-lg modern-card overflow-hidden no-print">
        <div class="p-6 lg:p-8">
            <div class="flex flex-col lg:flex-row justify-between items-center gap-4">
                <a href="{{ route('rekam-medis.index') }}"
                   class="btn-modern w-full lg:w-auto px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl inline-flex items-center justify-center gap-3">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                    </svg>
                    <span>Kembali ke Daftar</span>
                </a>

                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <button onclick="window.print()"
                            class="btn-modern w-full sm:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl inline-flex items-center justify-center gap-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Cetak Dokumen</span>
                    </button>

                    <a href="{{ route('rekam-medis.edit', $rekamMedis->id_rekam) }}"
                       class="btn-modern w-full sm:w-auto px-8 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-xl inline-flex items-center justify-center gap-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                        </svg>
                        <span>Edit Data</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
