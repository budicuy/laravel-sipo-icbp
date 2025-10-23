@extends('layouts.app')

@section('page-title', 'Edit Diagnosa Emergency')

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
                    Edit Diagnosa Emergency
                </h1>
                <p class="text-gray-600 text-lg">Ubah data diagnosis penyakit dan kondisi medis emergency</p>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">Formulir Data Diagnosa Emergency</h2>
        </div>

        <form action="{{ route('diagnosa-emergency.update', $diagnosaEmergency->id_diagnosa_emergency) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Diagnosa -->
                <div class="md:col-span-2">
                    <label for="nama_diagnosa_emergency" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Diagnosa Emergency <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama_diagnosa_emergency" name="nama_diagnosa_emergency" required
                           value="{{ old('nama_diagnosa_emergency', $diagnosaEmergency->nama_diagnosa_emergency) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                           placeholder="Masukkan nama diagnosa emergency">
                    @error('nama_diagnosa_emergency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Masukkan deskripsi diagnosa (opsional)">{{ old('deskripsi', $diagnosaEmergency->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Obat Rekomendasi -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Obat Rekomendasi
                    </label>
                    <div id="obat-container" class="space-y-3">
                        @php
                            $selectedObats = $diagnosaEmergency->obats->pluck('id_obat')->toArray();
                            if (empty($selectedObats)) {
                                $selectedObats = [''];
                            }
                        @endphp
                        @foreach($selectedObats as $index => $obatId)
                            <div class="obat-item flex items-center gap-3">
                                <select name="obat_rekomendasi[]" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <option value="">-- Pilih Obat --</option>
                                    @foreach($obats as $obat)
                                        <option value="{{ $obat->id_obat }}" {{ $obat->id_obat == $obatId ? 'selected' : '' }}>{{ $obat->nama_obat }}</option>
                                    @endforeach
                                </select>
                                @if($index == 0)
                                    <button type="button" onclick="addObatField()" class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                @else
                                    <button type="button" onclick="removeObatField(this)" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Tambahkan obat yang direkomendasikan untuk diagnosa ini</p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('diagnosa-emergency.index') }}" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Perbarui
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function addObatField() {
    const container = document.getElementById('obat-container');
    const obatItem = document.createElement('div');
    obatItem.className = 'obat-item flex items-center gap-3';
    
    obatItem.innerHTML = `
        <select name="obat_rekomendasi[]" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
            <option value="">-- Pilih Obat --</option>
            @foreach($obats as $obat)
                <option value="{{ $obat->id_obat }}">{{ $obat->nama_obat }}</option>
            @endforeach
        </select>
        <button type="button" onclick="removeObatField(this)" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </button>
    `;
    
    container.appendChild(obatItem);
}

function removeObatField(button) {
    const container = document.getElementById('obat-container');
    if (container.children.length > 1) {
        button.parentElement.remove();
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Minimal harus ada satu field obat',
            confirmButtonColor: '#dc2626'
        });
    }
}
</script>
@endpush
@endsection