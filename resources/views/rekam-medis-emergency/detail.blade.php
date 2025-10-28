@extends('layouts.app')

@section('page-title', 'Detail Rekam Medis Emergency')

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
        background: linear-gradient(to bottom, #ef4444, #f97316);
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
<div class="p-4 lg:p-6 bg-gradient-to-br from-gray-50 to-red-50 min-h-screen print-area">
    <!-- Modern Header Section -->
    <div class="mb-8 no-print">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-6">
            <a href="{{ route('rekam-medis.index', ['tab' => 'emergency']) }}" class="p-3 bg-white hover:bg-gray-50 rounded-xl shadow-md transition-all">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 flex items-center gap-3 mb-2">
                    <div class="bg-gradient-to-br from-red-600 to-pink-600 p-3 rounded-xl shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    Detail Rekam Medis Emergency
                </h1>
                <p class="text-gray-600 text-lg">Informasi lengkap pasien dan riwayat medis emergency</p>
            </div>
        </div>
    </div>

    <!-- Enhanced Patient Information Section -->
    <div class="bg-white rounded-2xl shadow-lg modern-card mb-8 overflow-hidden">
        <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-red-800 p-3 rounded-xl shadow-lg border-2 border-white">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl lg:text-3xl font-bold text-white drop-shadow-md">Informasi Pasien</h2>
                        <p class="text-red-100 text-sm lg:text-base mt-1">Data identitas dan kontak pasien emergency</p>
                    </div>
                </div>
                <div class="bg-red-800 px-5 py-2.5 rounded-full shadow-lg border-2 border-white">
                    <span class="text-white font-bold text-lg">RM: {{ $rekamMedisEmergency->externalEmployee->kode_rm ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="p-6 lg:p-8">
            <!-- Primary Information -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="info-item p-4 rounded-xl bg-gray-50">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-red-100 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="data-label">Nama Lengkap</div>
                    </div>
                    <div class="data-value text-xl">{{ $rekamMedisEmergency->externalEmployee->nama_employee ?? '-' }}</div>
                </div>

                <div class="info-item p-4 rounded-xl bg-gray-50">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-green-100 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="data-label">Tanggal Periksa</div>
                    </div>
                    <div class="data-value text-xl">{{ $rekamMedisEmergency->tanggal_periksa ? $rekamMedisEmergency->tanggal_periksa->format('d F Y') : '-' }}</div>
                </div>

                <div class="info-item p-4 rounded-xl bg-gray-50">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-purple-100 p-2 rounded-lg">
                            @if($rekamMedisEmergency->externalEmployee->jenis_kelamin == 'Laki-laki')
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
                    <div class="data-value text-xl">{{ $rekamMedisEmergency->externalEmployee->jenis_kelamin }}</div>
                </div>
            </div>

            <!-- Secondary Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="flex items-start gap-4 p-4 rounded-xl bg-red-50 border border-red-200">
                    <div class="bg-red-100 p-2 rounded-lg mt-1">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="data-label mb-1">NIK Karyawan</div>
                        <div class="data-value text-lg">{{ $rekamMedisEmergency->externalEmployee->nik_employee ?? '-' }}</div>
                        <div class="mt-2">
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Emergency Case
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-4 rounded-xl bg-blue-50 border border-blue-200">
                    <div class="bg-blue-100 p-2 rounded-lg mt-1">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="data-label mb-1">Petugas Medis</div>
                        <div class="data-value text-lg">{{ $rekamMedisEmergency->user->nama_lengkap ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Medical History Section -->
    <div class="bg-white rounded-2xl shadow-lg modern-card mb-8 overflow-hidden">
        <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-orange-800 p-3 rounded-xl shadow-lg border-2 border-white">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl lg:text-3xl font-bold text-white drop-shadow-md">Riwayat Kunjungan Emergency</h2>
                        <p class="text-orange-100 text-sm lg:text-base mt-1">Catatan pemeriksaan dan treatment pasien emergency</p>
                    </div>
                </div>
                <div class="bg-orange-800 px-5 py-2.5 rounded-full shadow-lg border-2 border-white">
                    <span class="text-white font-bold text-lg">1 Kunjungan</span>
                </div>
            </div>
        </div>

        <div class="p-6 lg:p-8">
            <div class="timeline-item mb-8 last:mb-0">
                <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-xl p-6 border border-red-100">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-6 pb-4 border-b border-red-200">
                        <div class="flex items-center gap-4 mb-4 lg:mb-0">
                            <div class="bg-red-600 text-white px-4 py-2 rounded-full font-bold">
                                Kunjungan #1
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="bg-white p-2 rounded-lg shadow-sm">
                                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="data-label">Tanggal Periksa</div>
                                    <div class="data-value text-lg">{{ $rekamMedisEmergency->tanggal_periksa ? $rekamMedisEmergency->tanggal_periksa->format('d F Y') : '-' }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="bg-white p-2 rounded-lg shadow-sm">
                                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="data-label">Waktu Periksa</div>
                                    <div class="data-value text-lg">{{ $rekamMedisEmergency->waktu_periksa ? $rekamMedisEmergency->waktu_periksa->format('H:i') : '-' }}</div>
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
                                <div class="data-value">{{ $rekamMedisEmergency->user->nama_lengkap ?? '-' }}</div>
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
                                        @if($rekamMedisEmergency->status == 'On Progress') bg-yellow-100 text-yellow-800
                                        @elseif($rekamMedisEmergency->status == 'Close') bg-green-100 text-green-800
                                        @endif
                                        rounded-full text-sm font-semibold">
                                        {{ $rekamMedisEmergency->status ?? 'On Progress' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @forelse($rekamMedisEmergency->keluhans as $keluhanIndex => $keluhan)
                        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-4">
                                <div class="flex items-center gap-3 mb-3 lg:mb-0">
                                    <div class="bg-red-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm">
                                        {{ $keluhanIndex + 1 }}
                                    </div>
                                    <div>
                                        <div class="data-label mb-1">Diagnosa</div>
                                        <div class="data-value text-lg font-semibold">{{ $keluhan->diagnosaEmergency->nama_diagnosa_emergency ?? '-' }}</div>
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
                        @empty
                        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-4">
                                <div class="flex items-center gap-3 mb-3 lg:mb-0">
                                    <div class="bg-red-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm">
                                        1
                                    </div>
                                    <div>
                                        <div class="data-label mb-1">Diagnosa</div>
                                        <div class="data-value text-lg font-semibold">{{ $rekamMedisEmergency->keluhan->diagnosa->nama_diagnosa ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>

                            @if($rekamMedisEmergency->keluhan)
                            <div class="mb-4 p-4 bg-orange-50 rounded-lg border-l-4 border-orange-400">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-orange-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <div class="font-semibold text-orange-700 text-sm mb-1">Keluhan Pasien</div>
                                        <div class="text-gray-700">{{ $rekamMedisEmergency->keluhan }}</div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($rekamMedisEmergency->catatan)
                            <div class="mb-4 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <div class="font-semibold text-blue-700 text-sm mb-1">Catatan Dokter</div>
                                        <div class="text-gray-700">{{ $rekamMedisEmergency->catatan }}</div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Action Buttons -->
    <div class="bg-white rounded-2xl shadow-lg modern-card overflow-hidden no-print">
        <div class="p-6 lg:p-8">
            <div class="flex flex-col lg:flex-row justify-between items-center gap-4">
                <a href="{{ route('rekam-medis.index', ['tab' => 'emergency']) }}"
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

                    <a href="{{ route('rekam-medis-emergency.edit', $rekamMedisEmergency->id_emergency) }}"
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle status change
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const newStatus = this.value;
            const currentStatus = this.getAttribute('data-current-status');

            // Show loading state
            this.disabled = true;
            this.classList.add('opacity-50');

            // Send AJAX request
            fetch(`{{ route('rekam-medis-emergency.updateStatus', ':id') }}`.replace(':id', id), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update status attribute
                    this.setAttribute('data-current-status', newStatus);

                    // Update styling based on new status
                    this.classList.remove('bg-yellow-100', 'text-yellow-800', 'bg-green-100', 'text-green-800');

                    if (newStatus === 'On Progress') {
                        this.classList.add('bg-yellow-100', 'text-yellow-800');
                    } else if (newStatus === 'Close') {
                        this.classList.add('bg-green-100', 'text-green-800');
                    }

                    // Show success notification
                    showNotification('Status berhasil diperbarui', 'success');
                } else {
                    // Revert to original status
                    this.value = currentStatus;
                    showNotification(data.message || 'Gagal memperbarui status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert to original status
                this.value = currentStatus;
                showNotification('Terjadi kesalahan saat memperbarui status', 'error');
            })
            .finally(() => {
                // Remove loading state
                this.disabled = false;
                this.classList.remove('opacity-50');
            });
        });
    });

    // Function to show notification
    function showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;

        // Set styling based on type
        if (type === 'success') {
            notification.classList.add('bg-green-500', 'text-white');
        } else if (type === 'error') {
            notification.classList.add('bg-red-500', 'text-white');
        }

        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    ${type === 'success'
                        ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
                        : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>'
                    }
                </svg>
                <span>${message}</span>
            </div>
        `;

        // Add to document
        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
            notification.classList.add('translate-x-0');
        }, 100);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
});
</script>
@endpush
@endsection
