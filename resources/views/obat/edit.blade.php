@extends('layouts.app')

@section('page-title', 'Edit Data Obat')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen" x-data="{
        id_satuan: '{{ old('id_satuan', $obat->id_satuan) }}'
    }">

        <div class="mb-6">
            <div class="flex items-center gap-3 mb-3">
                <a href="{{ route('obat.index') }}" class="p-2 hover:bg-white rounded-lg transition-colors">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-3 rounded-lg shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        Edit Data Obat
                    </h1>
                    <p class="text-gray-600 mt-1 ml-1">Perbarui informasi obat {{ $obat->nama_obat }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('obat.update', $obat->id_obat) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        Informasi Data Obat
                    </h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Obat -->
                        <div>
                            <label for="nama_obat" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Obat <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nama_obat" name="nama_obat"
                                value="{{ old('nama_obat', $obat->nama_obat) }}" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 @error('nama_obat') border-red-500 @enderror"
                                placeholder="Contoh: Paracetamol 500mg">
                            @error('nama_obat')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Satuan Obat -->
                        <div>
                            <label for="id_satuan" class="block text-sm font-semibold text-gray-700 mb-2">
                                Satuan Obat <span class="text-red-500">*</span>
                            </label>
                            <select id="id_satuan" name="id_satuan" required x-model="id_satuan"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white @error('id_satuan') border-red-500 @enderror">
                                <option value="">Pilih Satuan Obat</option>
                                @foreach ($satuanObats as $satuan)
                                    <option value="{{ $satuan->id_satuan }}"
                                        {{ old('id_satuan', $obat->id_satuan) == $satuan->id_satuan ? 'selected' : '' }}>
                                        {{ $satuan->nama_satuan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_satuan')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Keterangan (Full Width) -->
                        <div class="md:col-span-2">
                            <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">
                                Keterangan
                            </label>
                            <textarea id="keterangan" name="keterangan" rows="4"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 @error('keterangan') border-red-500 @enderror"
                                placeholder="Masukkan keterangan atau catatan obat (opsional)">{{ old('keterangan', $obat->keterangan) }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Lokasi / Bind -->
                        <div class="md:col-span-2">
                            <label for="lokasi" class="block text-sm font-semibold text-gray-700 mb-2">
                                Lokasi / Bind
                            </label>
                            <input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi', $obat->lokasi) }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 @error('lokasi') border-red-500 @enderror"
                                placeholder="Contoh: Gudang A / Rak 2">
                            @error('lokasi')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">(Opsional) Lokasi penyimpanan obat atau label bind</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                    <a href="{{ route('obat.index') }}"
                        class="px-6 py-2.5 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        Update Data
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
