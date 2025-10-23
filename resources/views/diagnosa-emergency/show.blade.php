@extends('layouts.app')

@section('page-title', 'Detail Diagnosa Emergency')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('diagnosa-emergency.index') }}" class="p-3 bg-white hover:bg-gray-50 rounded-xl shadow-md transition-all">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3 mb-2">
                    <div class="bg-gradient-to-br from-red-600 to-pink-600 p-3 rounded-xl shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    Detail Diagnosa Emergency
                </h1>
                <p class="text-gray-600 text-lg">Informasi lengkap diagnosa penyakit dan kondisi medis emergency</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-white">Informasi Diagnosa Emergency</h2>
                <div class="flex gap-2">
                    <a href="{{ route('diagnosa-emergency.edit', $diagnosaEmergency->id_diagnosa_emergency) }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white font-medium rounded-lg transition-all inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>
                </div>
            </div>
        </div>

        <!-- Detail Content -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Diagnosa -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-pink-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr($diagnosaEmergency->nama_diagnosa_emergency, 0, 2)) }}
                        </div>
                        {{ $diagnosaEmergency->nama_diagnosa_emergency }}
                    </h3>
                </div>

                <!-- ID Diagnosa -->
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">ID Diagnosa Emergency</h4>
                    <p class="text-lg text-gray-900">{{ $diagnosaEmergency->id_diagnosa_emergency }}</p>
                </div>

                <!-- Tanggal Dibuat -->
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Tanggal Dibuat</h4>
                    <p class="text-lg text-gray-900">{{ $diagnosaEmergency->created_at->format('d-m-Y H:i:s') }}</p>
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Deskripsi</h4>
                    <p class="text-lg text-gray-900">{{ $diagnosaEmergency->deskripsi ?? '-' }}</p>
                </div>

                <!-- Obat Rekomendasi -->
                <div class="md:col-span-2">
                    <h4 class="text-sm font-medium text-gray-500 mb-3">Obat Rekomendasi</h4>
                    @if($diagnosaEmergency->obats->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($diagnosaEmergency->obats as $obat)
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
                                    {{ $obat->nama_obat }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-lg text-gray-400 italic">Tidak ada obat rekomendasi</p>
                    @endif
                </div>

                <!-- Statistik Penggunaan -->
                <div class="md:col-span-2 bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Statistik Penggunaan</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-red-600">{{ $diagnosaEmergency->keluhans->count() }}</p>
                            <p class="text-sm text-gray-600">Total Penggunaan</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600">{{ $diagnosaEmergency->keluhans->where('created_at', '>=', now()->subDays(30))->count() }}</p>
                            <p class="text-sm text-gray-600">30 Hari Terakhir</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600">{{ $diagnosaEmergency->keluhans->where('created_at', '>=', now()->subDays(7))->count() }}</p>
                            <p class="text-sm text-gray-600">7 Hari Terakhir</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('diagnosa-emergency.index') }}" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition-colors">
                    Kembali
                </a>
                <a href="{{ route('diagnosa-emergency.edit', $diagnosaEmergency->id_diagnosa_emergency) }}" class="px-6 py-2.5 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Usage Section -->
    @if($diagnosaEmergency->keluhans->count() > 0)
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mt-6">
        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">Penggunaan Terbaru</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pasien</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($diagnosaEmergency->keluhans->take(10) as $index => $keluhan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $keluhan->created_at->format('d-m-Y H:i') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            @if($keluhan->rekamMedisEmergency)
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Emergency</span>
                            @else
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Reguler</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            @if($keluhan->rekamMedisEmergency)
                                {{ $keluhan->rekamMedisEmergency->externalEmployee->nik_employee ?? '-' }}
                            @else
                                {{ $keluhan->rekamMedis->keluarga->karyawan->nik_karyawan ?? '-' }}
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            @if($keluhan->rekamMedisEmergency)
                                {{ $keluhan->rekamMedisEmergency->externalEmployee->nama_employee ?? '-' }}
                            @else
                                {{ $keluhan->rekamMedis->keluarga->nama_keluarga ?? '-' }}
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $keluhan->user->nama_lengkap ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($diagnosaEmergency->keluhans->count() > 10)
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                Menampilkan 10 dari {{ $diagnosaEmergency->keluhans->count() }} penggunaan total
            </p>
        </div>
        @endif
    </div>
    @endif
</div>
@endsection