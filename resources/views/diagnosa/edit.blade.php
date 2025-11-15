@extends('layouts.app')

@section('page-title', 'Edit Data Diagnosa')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-3">
            <a href="{{ route('diagnosa.index') }}" class="p-2 hover:bg-white rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="bg-linear-to-r from-yellow-600 to-orange-600 p-3 rounded-lg shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    Edit Data Diagnosa
                </h1>
                <p class="text-gray-600 mt-1 ml-1">Perbarui informasi data diagnosa penyakit</p>
            </div>
        </div>
    </div>

    <form action="{{ route('diagnosa.update', $diagnosa->id_diagnosa) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="bg-linear-to-r from-yellow-600 to-orange-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Informasi Data Diagnosa
                </h2>
            </div>

            <div class="p-6">
                @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="nama_diagnosa" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Diagnosa <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <input type="text" id="nama_diagnosa" name="nama_diagnosa"
                                value="{{ old('nama_diagnosa', $diagnosa->nama_diagnosa) }}"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                required>
                        </div>
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-sm font-semibold text-gray-700 mb-2">
                            Deskripsi
                        </label>
                        <textarea id="deskripsi" name="deskripsi" rows="4"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">{{ old('deskripsi', $diagnosa->deskripsi) }}</textarea>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <select id="status" name="status"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                required>
                                <option value="aktif" {{ old('status', $diagnosa->status ?? 'aktif') == 'aktif' ?
                                    'selected' : '' }}>Aktif
                                </option>
                                <option value="non-aktif" {{ old('status', $diagnosa->status) == 'non-aktif' ?
                                    'selected' : '' }}>Non-Aktif
                                </option>
                            </select>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Diagnosa non-aktif tidak akan muncul di form rekam medis
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Obat yang Direkomendasikan
                        </label>
                        <div class="border border-gray-300 rounded-lg p-4 max-h-64 overflow-y-auto">
                            @foreach ($obats as $obat)
                            <div class="flex items-center mb-2">
                                <input type="checkbox" id="obat_{{ $obat->id_obat }}" name="obat_ids[]"
                                    value="{{ $obat->id_obat }}"
                                    class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500 mr-3" {{
                                    in_array($obat->id_obat, old('obat_ids',
                                $diagnosa->obats->pluck('id_obat')->toArray())) ? 'checked' : '' }}>
                                <label for="obat_{{ $obat->id_obat }}" class="text-sm text-gray-700">
                                    {{ $obat->nama_obat }}
                                    @if ($obat->keterangan)
                                    <span class="text-gray-500 text-xs">({{ Str::limit($obat->keterangan, 300)
                                        }})</span>
                                    @endif
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Pilih obat yang direkomendasikan untuk diagnosa ini</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-between items-center">
                <button type="button" onclick="document.getElementById('deleteForm').submit();"
                    class="px-5 py-2.5 bg-linear-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus Data
                </button>
                <div class="flex gap-3">
                    <button type="button" onclick="window.location.href='{{ route('diagnosa.index') }}'"
                        class="px-6 py-2.5 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-medium rounded-lg transition-all hover:shadow-md">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-linear-to-rrom-yellow-600 to-orange-600 hover:from-yellow-700 hover:to-orange-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Form Delete Terpisah -->
    <form id="deleteForm" action="{{ route('diagnosa.destroy', $diagnosa->id_diagnosa) }}" method="POST"
        style="display: none;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus diagnosa ini?')">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection