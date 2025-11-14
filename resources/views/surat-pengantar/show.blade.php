@extends('layouts.app')

@section('page-title', 'Detail Surat Pengantar')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                Surat Pengantar Istirahat - Valid
            </h1>
            <p class="text-gray-600 mt-2 ml-1">Detail surat pengantar istirahat yang telah diverifikasi</p>
        </div>

        <!-- Status Card -->
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h3 class="text-green-800 font-semibold">Surat Valid & Terverifikasi</h3>
                    <p class="text-green-700 text-sm">Surat pengantar ini telah diverifikasi dan sah</p>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <!-- Header Info -->
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-6 text-white">
                <h2 class="text-xl font-semibold">Surat Pengantar Istirahat Sakit</h2>
                <p class="text-green-100 text-sm mt-1">PT. Indofood CBP Sukses Makmur Tbk</p>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- Nomor Surat -->
                <div class="mb-6 pb-4 border-b border-gray-200">
                    <label class="text-sm font-medium text-gray-600">Nomor Surat</label>
                    <p class="text-lg font-bold text-gray-900">{{ $suratPengantar->nomor_surat }}</p>
                </div>

                <!-- Patient Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Nama Pasien</label>
                        <p class="text-gray-900 font-semibold text-lg">{{ $suratPengantar->nama_pasien }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">NIK Karyawan</label>
                        <p class="text-gray-900 font-medium">{{ $suratPengantar->nik_karyawan_penanggung_jawab }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Tanggal Pengantar</label>
                        <p class="text-gray-900 font-medium">{{
                            \Carbon\Carbon::parse($suratPengantar->tanggal_pengantar)->format('d F Y') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Petugas Medis</label>
                        <p class="text-gray-900 font-medium">{{ $suratPengantar->petugas_medis }}</p>
                    </div>
                </div>

                <!-- Diagnosa -->
                <div class="mb-6">
                    <label class="text-sm font-medium text-gray-600 block mb-2">Diagnosa</label>
                    <div class="flex flex-wrap gap-2">
                        @if(is_array($suratPengantar->diagnosa))
                        @foreach($suratPengantar->diagnosa as $diagnosa)
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                            {{ $diagnosa }}
                        </span>
                        @endforeach
                        @else
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                            {{ $suratPengantar->diagnosa }}
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Rest Period -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Periode Istirahat</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Lama Istirahat</label>
                            <p class="text-2xl font-bold text-yellow-700">{{ $suratPengantar->lama_istirahat }} Hari</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Tanggal Mulai</label>
                            <p class="text-gray-900 font-semibold">{{
                                \Carbon\Carbon::parse($suratPengantar->tanggal_mulai_istirahat)->format('d F Y') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Tanggal Selesai</label>
                            <p class="text-gray-900 font-semibold">
                                {{
                                \Carbon\Carbon::parse($suratPengantar->tanggal_mulai_istirahat)->addDays($suratPengantar->lama_istirahat
                                - 1)->format('d F Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                @if($suratPengantar->catatan)
                <div class="mb-6">
                    <label class="text-sm font-medium text-gray-600 block mb-2">Catatan</label>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <p class="text-gray-900">{{ $suratPengantar->catatan }}</p>
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('rekam-medis.index') }}"
                        class="text-gray-600 hover:text-gray-900 font-medium flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </a>
                    <a href="{{ route('surat-pengantar.print', $suratPengantar->id) }}" target="_blank"
                        class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-2.5 rounded-lg font-medium shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak Surat
                    </a>
                </div>
            </div>
        </div>

        <!-- Verification Info -->
        <div class="mt-6 bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Informasi Verifikasi
            </h3>
            <p class="text-gray-600 text-sm">
                Surat ini dilindungi dengan QR Code untuk memastikan keaslian. Scan QR Code pada surat cetak untuk
                memverifikasi.
            </p>
            <div class="mt-4 text-center">
                @if($suratPengantar->qrcode_path)
                <img src="{{ Storage::url($suratPengantar->qrcode_path) }}" alt="QR Code" class="mx-auto"
                    style="width: 200px; height: 200px;">
                <p class="text-gray-500 text-sm mt-2">Scan untuk verifikasi</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection