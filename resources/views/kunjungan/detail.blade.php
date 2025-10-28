@extends('layouts.app')

@section('page-title', 'Detail Kunjungan')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('kunjungan.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div class="bg-gradient-to-r from-orange-500 to-red-600 p-3 rounded-lg shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detail Kunjungan</h1>
                    <p class="text-gray-600 mt-1">Informasi lengkap kunjungan pasien</p>
                </div>
            </div>
        </div>

        <!-- Patient Information Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
            <div
                class="bg-gradient-to-r
            @if ($kunjungan->tipe == 'emergency') from-red-500 to-pink-600
            @else from-orange-500 to-red-600 @endif px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    @if ($kunjungan->tipe == 'emergency')
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        Informasi Pasien Emergency
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Informasi Pasien
                    @endif
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nomor Registrasi</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $kunjungan->nomor_registrasi }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">No RM</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $kunjungan->no_rm }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nama Pasien</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $kunjungan->nama_pasien }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Hubungan</label>
                        <span
                            class="px-3 py-1
                        @if ($kunjungan->tipe == 'emergency') bg-red-100 text-red-800
                        @else bg-blue-100 text-blue-800 @endif rounded-full text-sm font-medium">
                            {{ $kunjungan->hubungan }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Kunjungan</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $kunjungan->tanggal_kunjungan->format('d-m-Y') }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        <span
                            class="px-3 py-1 rounded-full text-sm font-medium
                        @if ($kunjungan->status == 'On Progress') bg-yellow-100 text-yellow-800
                        @elseif($kunjungan->status == 'Close') bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-800 @endif">
                            {{ $kunjungan->status }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Petugas</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $kunjungan->user->nama_lengkap ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">NIK</label>
                        <p class="text-lg font-semibold text-gray-900">
                            @if ($kunjungan->tipe == 'emergency')
                                {{ $kunjungan->externalEmployee->nik_employee ?? '-' }}
                            @else
                                {{ $kunjungan->keluarga->karyawan->nik_karyawan ?? '-' }}
                            @endif
                        </p>
                    </div>
                    @if ($kunjungan->tipe == 'emergency')
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Waktu Periksa</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $kunjungan->waktu_periksa ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Jenis Kelamin</label>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $kunjungan->externalEmployee->jenis_kelamin ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Alamat</label>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $kunjungan->externalEmployee->alamat ?? '-' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Emergency Complaint Information -->
        @if ($kunjungan->tipe == 'emergency')
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        Keluhan Emergency
                    </h2>
                </div>
                <div class="p-6">
                    @if ($kunjungan->keluhan)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-500 mb-2">Keluhan Utama</label>
                            <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $kunjungan->keluhan }}</p>
                        </div>
                    @endif
                    @if ($kunjungan->catatan)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Catatan</label>
                            <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $kunjungan->catatan }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Medical Information Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
            <div
                class="bg-gradient-to-r
            @if ($kunjungan->tipe == 'emergency') from-red-600 to-pink-600
            @else from-green-600 to-emerald-600 @endif px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    @if ($kunjungan->tipe == 'emergency')
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        Informasi Medis Emergency
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Informasi Medis
                    @endif
                </h2>
            </div>
            <div class="p-6">
                @if ($kunjungan->keluhans && $kunjungan->keluhans->count() > 0)
                    <div class="space-y-4">
                        @foreach ($kunjungan->keluhans as $index => $keluhan)
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-semibold text-gray-900">Keluhan {{ $index + 1 }}</h4>
                                    <span
                                        class="px-2 py-1
                                @if ($keluhan->terapi == 'Obat') bg-purple-100 text-purple-800
                                @elseif($keluhan->terapi == 'Lab') bg-orange-100 text-orange-800
                                @else bg-green-100 text-green-800 @endif
                                rounded-full text-xs font-medium">
                                        {{ $keluhan->terapi }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-1">Diagnosa</label>
                                        <p class="text-gray-900">
                                            @if ($kunjungan->tipe == 'emergency')
                                                {{ $keluhan->diagnosaEmergency->nama_diagnosa_emergency ?? '-' }}
                                            @else
                                                {{ $keluhan->diagnosa->nama_diagnosa ?? '-' }}
                                            @endif
                                        </p>
                                    </div>
                                    @if ($keluhan->obat)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500 mb-1">Obat</label>
                                            <p class="text-gray-900">{{ $keluhan->obat->nama_obat ?? '-' }}</p>
                                        </div>
                                    @endif
                                    @if ($keluhan->jumlah_obat)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500 mb-1">Jumlah Obat</label>
                                            <p class="text-gray-900">{{ $keluhan->jumlah_obat }}</p>
                                        </div>
                                    @endif
                                    @if ($keluhan->aturan_pakai)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500 mb-1">Aturan
                                                Pakai</label>
                                            <p class="text-gray-900">{{ $keluhan->aturan_pakai }}</p>
                                        </div>
                                    @endif
                                    @if ($keluhan->keterangan)
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-500 mb-1">Keterangan</label>
                                            <p class="text-gray-900">{{ $keluhan->keterangan }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-lg font-medium">Tidak ada data medis</p>
                        <p class="text-sm mt-1">Belum ada informasi medis untuk kunjungan ini</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Visit History Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div
                class="bg-gradient-to-r
            @if ($kunjungan->tipe == 'emergency') from-red-600 to-pink-600
            @else from-blue-600 to-cyan-600 @endif px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    @if ($kunjungan->tipe == 'emergency')
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        Riwayat Kunjungan Emergency
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Riwayat Kunjungan
                    @endif
                </h2>
            </div>
            <div class="p-6">
                @if ($riwayatKunjungan && $riwayatKunjungan->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nomor Registrasi</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Petugas</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($riwayatKunjungan as $riwayat)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            {{ $riwayat->tanggal_kunjungan->format('d-m-Y') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-medium">
                                            {{ $riwayat->nomor_registrasi }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            <span
                                                class="px-2 py-1 rounded-full text-xs font-medium
                                        @if ($riwayat->status == 'On Progress') bg-yellow-100 text-yellow-800
                                        @elseif($riwayat->status == 'Close') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                                {{ $riwayat->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            {{ $riwayat->user->nama_lengkap ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <a href="{{ route('kunjungan.detail', $riwayat->id_kunjungan) }}"
                                                class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                                Lihat Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-lg font-medium">Tidak ada riwayat kunjungan</p>
                        <p class="text-sm mt-1">Belum ada riwayat kunjungan untuk pasien ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
