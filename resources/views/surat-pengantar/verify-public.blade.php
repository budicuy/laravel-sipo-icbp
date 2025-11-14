<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Surat Pengantar Istirahat - PT Indofood CBP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .badge-verified {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .8;
            }
        }

        .card-shadow {
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 8rem;
            font-weight: 900;
            color: rgba(16, 185, 129, 0.05);
            z-index: 0;
            pointer-events: none;
            user-select: none;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 min-h-screen py-8 px-4 relative">
    <!-- Watermark -->
    <div class="watermark">VERIFIED</div>

    <div class="max-w-4xl mx-auto relative z-10">
        <!-- Header Logo -->
        <div class="text-center mb-8">
            <div class="inline-block bg-white p-6 rounded-2xl shadow-lg mb-4">
                <svg class="w-20 h-20 mx-auto text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Verifikasi Surat Pengantar</h1>
            <p class="text-gray-600 text-lg">PT. Indofood CBP Sukses Makmur Tbk</p>
            <p class="text-gray-500 text-sm mt-1">Sistem Informasi Poliklinik</p>
        </div>

        <!-- Verification Status Banner -->
        <div
            class="bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-2xl p-6 mb-8 card-shadow badge-verified">
            <div class="flex items-center justify-center gap-4">
                <div class="bg-white/20 p-4 rounded-full">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="text-left">
                    <h2 class="text-2xl font-bold mb-1">Surat Terverifikasi</h2>
                    <p class="text-green-100">Surat pengantar ini sah dan terdaftar dalam sistem kami</p>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-2xl card-shadow overflow-hidden mb-6">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-gray-800 to-gray-900 p-6 text-white">
                <div class="flex flex-wrap items-start justify-center md:justify-between gap-4 md:gap-0">
                    <div>
                        <h3 class="text-xl font-semibold mb-1">Surat Pengantar Istirahat Sakit</h3>
                        <p class="text-gray-300 text-sm">Klinik PT. Indofood CBP Sukses Makmur Tbk</p>
                    </div>
                    <div class="text-center md:text-left">
                        <div class="bg-white/10 backdrop-blur-sm px-4 py-2 rounded-lg">
                            <p class="text-xs text-gray-300 mb-1">Nomor Surat</p>
                            <p class="font-mono font-bold text-lg">{{ $suratPengantar->nomor_surat }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Section -->
            <div class="p-8">
                <!-- Patient Info -->
                <div class="mb-8 pb-8 border-b-2 border-gray-100">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Informasi Pasien</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Nama Pasien</label>
                            <p class="text-xl font-bold text-gray-900 mt-1">{{ $suratPengantar->nama_pasien }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">NIK
                                Karyawan</label>
                            <p class="text-xl font-bold text-gray-900 mt-1">{{
                                $suratPengantar->nik_karyawan_penanggung_jawab }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tanggal
                                Pengantar</label>
                            <p class="text-lg font-semibold text-gray-900 mt-1">{{
                                \Carbon\Carbon::parse($suratPengantar->tanggal_pengantar)->format('d F Y') }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Petugas
                                Medis</label>
                            <p class="text-lg font-semibold text-gray-900 mt-1">{{ $suratPengantar->petugas_medis }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Diagnosis -->
                <div class="mb-8 pb-8 border-b-2 border-gray-100">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Diagnosa</h4>
                    <div class="flex flex-wrap gap-3">
                        @if(is_array($suratPengantar->diagnosa))
                        @foreach($suratPengantar->diagnosa as $diagnosa)
                        <span
                            class="inline-flex items-center bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-semibold border border-blue-200">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $diagnosa }}
                        </span>
                        @endforeach
                        @else
                        <span
                            class="inline-flex items-center bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-semibold border border-blue-200">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $suratPengantar->diagnosa }}
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Rest Period - Highlighted -->
                <div
                    class="bg-gradient-to-br from-yellow-50 to-amber-50 border-2 border-yellow-300 rounded-2xl p-6 mb-8">
                    <h4 class="text-sm font-semibold text-yellow-800 uppercase tracking-wide mb-5 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Periode Istirahat
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center bg-white rounded-xl p-5 shadow-sm">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Lama Istirahat</p>
                            <p class="text-5xl font-black text-yellow-600 mb-1">{{ $suratPengantar->lama_istirahat }}
                            </p>
                            <p class="text-sm font-semibold text-gray-700">Hari</p>
                        </div>
                        <div class="text-center bg-white rounded-xl p-5 shadow-sm">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Tanggal Mulai</p>
                            <p class="text-2xl font-bold text-gray-900 mb-1">
                                {{
                                \Carbon\Carbon::parse($suratPengantar->tanggal_mulai_istirahat)->format('d') }}
                            </p>
                            <p class="text-sm font-semibold text-gray-700">
                                {{
                                \Carbon\Carbon::parse($suratPengantar->tanggal_mulai_istirahat)->format('F Y') }}
                            </p>
                        </div>
                        <div class="text-center bg-white rounded-xl p-5 shadow-sm">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Tanggal Selesai
                            </p>
                            <p class="text-2xl font-bold text-gray-900 mb-1">
                                {{
                                \Carbon\Carbon::parse($suratPengantar->tanggal_mulai_istirahat)->addDays($suratPengantar->lama_istirahat
                                - 1)->format('d') }}
                            </p>
                            <p class="text-sm font-semibold text-gray-700">
                                {{
                                \Carbon\Carbon::parse($suratPengantar->tanggal_mulai_istirahat)->addDays($suratPengantar->lama_istirahat
                                - 1)->format('F Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                @if($suratPengantar->catatan)
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Catatan Medis</h4>
                    <div class="bg-gray-50 border-l-4 border-gray-400 rounded-r-xl p-5">
                        <p class="text-gray-900 leading-relaxed">{{ $suratPengantar->catatan }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Security Info Card -->
        <div class="bg-white rounded-2xl card-shadow p-6 mb-6">
            <div class="flex items-start gap-4">
                <div class="bg-green-100 p-3 rounded-xl">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Keamanan & Validitas</h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-3">
                        Dokumen ini dilindungi dengan sistem verifikasi digital. Setiap surat memiliki kode unik yang
                        terdaftar dalam database sistem kami untuk memastikan keaslian dan mencegah pemalsuan.
                    </p>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Terverifikasi pada {{ now()->format('d F Y, H:i') }} WIB</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-gray-500 text-sm">
            <p class="mb-2">&copy; {{ date('Y') }} PT. Indofood CBP Sukses Makmur Tbk</p>
            <p class="text-xs">Sistem Informasi Poliklinik - Dokumen Terverifikasi Otomatis</p>
            <p class="text-xs text-gray-400 mt-3">Halaman ini dapat diakses secara publik untuk keperluan verifikasi
            </p>
        </div>
    </div>
</body>

</html>