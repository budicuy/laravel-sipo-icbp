@extends('layouts.app')

@section('page-title', 'Detail Rekam Medis')

@push('styles')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .print-area,
            .print-area * {
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

            .bg-gradient-to-r,
            .bg-gradient-to-br {
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
                <a href="{{ route('rekam-medis.index') }}"
                    class="p-3 bg-white hover:bg-gray-50 rounded-xl shadow-md transition-all">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="flex-1">
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 flex items-center gap-3 mb-2">
                        <div class="bg-gradient-to-br from-blue-600 to-cyan-600 p-3 rounded-xl shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
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
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl lg:text-3xl font-bold text-white drop-shadow-md">Informasi Pasien</h2>
                            <p class="text-blue-100 text-sm lg:text-base mt-1">Data identitas dan kontak pasien</p>
                        </div>
                    </div>
                    <div class="bg-blue-800 px-5 py-2.5 rounded-full shadow-lg border-2 border-white">
                        <span class="text-white font-bold text-lg">RM:
                            {{ ($rekamMedis->keluarga->karyawan->nik_karyawan ?? '') . '-' . ($rekamMedis->keluarga->kode_hubungan ?? '') }}</span>
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
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                        clip-rule="evenodd" />
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
                                    <path fill-rule="evenodd"
                                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="data-label">Tanggal Lahir</div>
                        </div>
                        <div class="data-value text-xl">
                            {{ $rekamMedis->keluarga->tanggal_lahir ? $rekamMedis->keluarga->tanggal_lahir->format('d F Y') : '-' }}
                        </div>
                    </div>

                    <div class="info-item p-4 rounded-xl bg-gray-50">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="bg-purple-100 p-2 rounded-lg">
                                @if ($rekamMedis->keluarga->jenis_kelamin == 'Laki-Laki')
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd" />
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
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="data-label mb-1">Hubungan dengan Karyawan</div>
                            <div class="data-value text-lg">{{ $rekamMedis->keluarga->kode_hubungan ?? '' }}.
                                {{ $rekamMedis->keluarga->hubungan->hubungan ?? '-' }}</div>
                            @if (($rekamMedis->keluarga->hubungan->hubungan ?? '') == 'Emergency')
                                <div class="mt-2">
                                    <span
                                        class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        Emergency Case
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 rounded-xl bg-red-50 border border-red-200">
                        <div class="bg-red-100 p-2 rounded-lg mt-1">
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                    clip-rule="evenodd" />
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

        <!-- Enhanced Medical Record Detail Section -->
        <div class="bg-white rounded-2xl shadow-lg modern-card mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 lg:px-8 py-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="bg-green-800 p-3 rounded-xl shadow-lg border-2 border-white">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                <path fill-rule="evenodd"
                                    d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl lg:text-3xl font-bold text-white drop-shadow-md">Detail Pemeriksaan</h2>
                            <p class="text-green-100 text-sm lg:text-base mt-1">Catatan pemeriksaan dan treatment</p>
                        </div>
                    </div>
                    <div class="bg-green-800 px-5 py-2.5 rounded-full shadow-lg border-2 border-white">
                        <span
                            class="text-white font-bold text-lg">{{ $rekamMedis->tanggal_periksa->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="p-6 lg:p-8">
                <!-- Visit Information -->
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl p-6 border border-blue-100 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="bg-white p-3 rounded-lg shadow-sm">
                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <div class="data-label">Tanggal Periksa</div>
                                <div class="data-value text-lg">{{ $rekamMedis->tanggal_periksa->format('d F Y') }}</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="bg-white p-3 rounded-lg shadow-sm">
                                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <div class="data-label">Dokter Pemeriksa</div>
                                <div class="data-value text-lg">{{ $rekamMedis->user->nama_lengkap ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="bg-white p-3 rounded-lg shadow-sm">
                                <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <div class="data-label">Status Pemeriksaan</div>
                                <div>
                                    <span
                                        class="px-4 py-1.5
                                    @if ($rekamMedis->status == 'On Progress') bg-yellow-100 text-yellow-800
                                    @elseif($rekamMedis->status == 'Close') bg-green-100 text-green-800 @endif
                                    rounded-full text-sm font-bold inline-flex items-center gap-2">
                                        @if ($rekamMedis->status == 'On Progress')
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                        {{ $rekamMedis->status ?? 'On Progress' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Diagnosa & Treatment Cards -->
                <div class="space-y-4">
                    @forelse($rekamMedis->keluhans as $keluhanIndex => $keluhan)
                        <div
                            class="bg-white rounded-xl p-6 shadow-md border-2 border-gray-100 hover:border-blue-200 transition-all">
                            <div class="flex flex-col lg:flex-row lg:items-start justify-between mb-5">
                                <div class="flex items-start gap-4 mb-4 lg:mb-0">
                                    <div
                                        class="bg-gradient-to-br from-red-500 to-pink-600 text-white w-12 h-12 rounded-xl flex items-center justify-center font-bold text-lg shadow-lg">
                                        {{ $keluhanIndex + 1 }}
                                    </div>
                                    <div>
                                        <div class="data-label mb-2">Diagnosa</div>
                                        <div class="data-value text-xl font-bold text-gray-900">
                                            {{ $keluhan->diagnosa->nama_diagnosa ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div
                                        class="status-badge px-4 py-2 rounded-xl text-sm font-bold shadow-sm
                                {{ $keluhan->terapi == 'Obat' ? 'bg-purple-100 text-purple-700 border-2 border-purple-300' : '' }}
                                {{ $keluhan->terapi == 'Lab' ? 'bg-orange-100 text-orange-700 border-2 border-orange-300' : '' }}
                                {{ $keluhan->terapi == 'Istirahat' ? 'bg-green-100 text-green-700 border-2 border-green-300' : '' }}">
                                        <div class="flex items-center gap-2">
                                            @if ($keluhan->terapi == 'Obat')
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            @elseif($keluhan->terapi == 'Lab')
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M7 2a1 1 0 00-.707 1.707L7 4.414v3.758a1 1 0 01-.293.707l-4 4C.817 14.769 2.156 18 4.828 18h10.343c2.673 0 4.012-3.231 2.122-5.121l-4-4A1 1 0 0113 8.172V4.414l.707-.707A1 1 0 0013 2H7zm2 6.172V4h2v4.172a3 3 0 00.879 2.12l1.027 1.028a4 4 0 00-2.171.102l-.47.156a4 4 0 01-2.53 0l-.563-.187a1.993 1.993 0 00-.114-.035l1.063-1.063A3 3 0 009 8.172z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                            <span>{{ $keluhan->terapi }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($keluhan->keterangan)
                                <div
                                    class="mb-5 p-5 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border-l-4 border-blue-500 shadow-sm">
                                    <div class="flex items-start gap-3">
                                        <div class="bg-blue-500 p-2 rounded-lg mt-0.5">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-bold text-blue-800 text-sm mb-2 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Catatan Dokter
                                            </div>
                                            <div class="text-gray-800 leading-relaxed">{{ $keluhan->keterangan }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($keluhan->obat)
                                <div
                                    class="prescription-card border-2 border-green-300 rounded-xl p-5 shadow-sm bg-gradient-to-br from-green-50 to-emerald-50">
                                    <div class="flex items-center gap-3 mb-5 pb-4 border-b-2 border-green-200">
                                        <div
                                            class="bg-gradient-to-br from-green-600 to-emerald-600 p-3 rounded-xl shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="data-label mb-1">Resep Obat</div>
                                            <div class="font-bold text-green-800 text-xl">{{ $keluhan->obat->nama_obat }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                        @if ($keluhan->jumlah_obat)
                                            <div class="bg-white p-4 rounded-xl shadow-sm border border-green-200">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <svg class="w-5 h-5 text-green-600" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <div class="data-label">Jumlah</div>
                                                </div>
                                                <div class="data-value text-2xl text-green-700">
                                                    {{ $keluhan->jumlah_obat }} <span
                                                        class="text-base">{{ $keluhan->obat->satuanObat->nama_satuan ?? '' }}</span>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($keluhan->aturan_pakai)
                                            <div class="bg-white p-4 rounded-xl shadow-sm border border-green-200">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <svg class="w-5 h-5 text-green-600" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <div class="data-label">Aturan Pakai</div>
                                                </div>
                                                <div class="data-value text-lg text-green-700">
                                                    {{ $keluhan->aturan_pakai }}</div>
                                            </div>
                                        @endif
                                        @if ($keluhan->waktu_pakai)
                                            <div class="bg-white p-4 rounded-xl shadow-sm border border-green-200">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <svg class="w-5 h-5 text-green-600" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <div class="data-label">Durasi</div>
                                                </div>
                                                <div class="data-value text-2xl text-green-700">
                                                    {{ $keluhan->waktu_pakai }} <span class="text-base">Hari</span></div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div
                                    class="text-center py-8 text-gray-400 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                                    <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <p class="font-semibold text-lg">Tidak ada resep obat</p>
                                    <p class="text-sm mt-1">Terapi tidak memerlukan obat</p>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="empty-state py-12">
                            <svg class="w-24 h-24 mx-auto mb-4 opacity-30" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                <path fill-rule="evenodd"
                                    d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                    clip-rule="evenodd" />
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak Ada Data Diagnosa</h3>
                            <p class="text-gray-500">Belum ada catatan diagnosa dan treatment untuk pemeriksaan ini</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
@endsection
