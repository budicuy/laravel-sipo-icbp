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
                            @php
                                $selectedObat = null;
                                if (!empty($obatId)) {
                                    $selectedObat = $obats->firstWhere('id_obat', $obatId);
                                }
                            @endphp
                            <div class="obat-item flex items-center gap-3">
                                <div class="flex-1 relative">
                                    <input type="text"
                                           name="obat_rekomendasi_text[]"
                                           value="{{ $selectedObat ? $selectedObat->nama_obat : '' }}"
                                           class="obat-autocomplete w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-sm"
                                           placeholder="Ketik nama obat untuk mencari..."
                                           autocomplete="off">
                                    <input type="hidden" name="obat_rekomendasi[]" class="obat-id" value="{{ $obatId }}">
                                    <svg class="absolute right-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <div class="obat-suggestions absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden"></div>
                                </div>
                                <div class="obat-description text-sm text-gray-600 italic max-w-md @if($selectedObat && $selectedObat->deskripsi_obat) @else hidden @endif">
                                    @if($selectedObat && $selectedObat->deskripsi_obat)
                                        {{ $selectedObat->deskripsi_obat }}
                                    @endif
                                </div>
                                @if($index == 0)
                                    <button type="button" onclick="addObatField()" class="px-3 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                @else
                                    <button type="button" onclick="removeObatField(this)" class="px-3 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
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
// Data obat dari server
const obatsData = @json($obats);

function addObatField() {
    const container = document.getElementById('obat-container');
    const obatItem = document.createElement('div');
    obatItem.className = 'obat-item flex items-start gap-3';
    
    obatItem.innerHTML = `
        <div class="flex-1 relative">
            <input type="text"
                   name="obat_rekomendasi_text[]"
                   class="obat-autocomplete w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-sm"
                   placeholder="Ketik nama obat untuk mencari..."
                   autocomplete="off">
            <input type="hidden" name="obat_rekomendasi[]" class="obat-id">
            <svg class="absolute right-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <div class="obat-suggestions absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden"></div>
        </div>
        <div class="obat-description text-sm text-gray-600 italic max-w-md hidden"></div>
        <button type="button" onclick="removeObatField(this)" class="px-3 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </button>
    `;
    
    container.appendChild(obatItem);
    initializeAutocomplete(obatItem);
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

function initializeAutocomplete(container) {
    const input = container.querySelector('.obat-autocomplete');
    const hiddenInput = container.querySelector('.obat-id');
    const suggestionsDiv = container.querySelector('.obat-suggestions');
    const descriptionDiv = container.querySelector('.obat-description');
    
    let currentFocus = -1;
    
    input.addEventListener('input', function() {
        const value = this.value.trim();
        closeAllLists();
        
        if (!value) {
            hiddenInput.value = '';
            descriptionDiv.classList.add('hidden');
            return;
        }
        
        // Filter obats based on input
        const filteredObats = obatsData.filter(obat =>
            obat.nama_obat.toLowerCase().includes(value.toLowerCase())
        );
        
        if (filteredObats.length === 0) {
            return;
        }
        
        // Create suggestions
        filteredObats.forEach((obat, index) => {
            const suggestionItem = document.createElement('div');
            suggestionItem.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer flex items-center border-b border-gray-100 last:border-b-0';
            suggestionItem.innerHTML = `
                <div class="flex-1">
                    <div class="font-medium text-sm">${obat.nama_obat}</div>
                    ${obat.deskripsi_obat ? `<div class="text-xs text-gray-500 mt-1">${obat.deskripsi_obat.substring(0, 100)}${obat.deskripsi_obat.length > 100 ? '...' : ''}</div>` : ''}
                </div>
            `;
            
            suggestionItem.addEventListener('click', function() {
                input.value = obat.nama_obat;
                hiddenInput.value = obat.id_obat;
                
                // Show description
                if (obat.deskripsi_obat) {
                    // Truncate description to reasonable length
                    const truncatedDesc = obat.deskripsi_obat.length > 100 ?
                        obat.deskripsi_obat.substring(0, 100) + '...' :
                        obat.deskripsi_obat;
                    descriptionDiv.textContent = truncatedDesc;
                    descriptionDiv.classList.remove('hidden');
                } else {
                    descriptionDiv.classList.add('hidden');
                }
                
                closeAllLists();
            });
            
            // Show description on hover
            suggestionItem.addEventListener('mouseenter', function() {
                if (obat.deskripsi_obat) {
                    // Truncate description to reasonable length
                    const truncatedDesc = obat.deskripsi_obat.length > 100 ?
                        obat.deskripsi_obat.substring(0, 100) + '...' :
                        obat.deskripsi_obat;
                    descriptionDiv.textContent = truncatedDesc;
                    descriptionDiv.classList.remove('hidden');
                }
            });
            
            suggestionsDiv.appendChild(suggestionItem);
        });
        
        suggestionsDiv.classList.remove('hidden');
    });
    
    input.addEventListener('keydown', function(e) {
        const items = suggestionsDiv.getElementsByTagName('div');
        if (e.keyCode === 40) { // Down arrow
            currentFocus++;
            addActive(items);
        } else if (e.keyCode === 38) { // Up arrow
            currentFocus--;
            addActive(items);
        } else if (e.keyCode === 13) { // Enter
            e.preventDefault();
            if (currentFocus > -1 && items[currentFocus]) {
                items[currentFocus].click();
            }
        } else if (e.keyCode === 27) { // Escape
            closeAllLists();
        }
    });
    
    function addActive(items) {
        if (!items) return false;
        removeActive(items);
        if (currentFocus >= items.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = items.length - 1;
        items[currentFocus].classList.add('bg-gray-100');
        return true;
    }
    
    function removeActive(items) {
        for (let i = 0; i < items.length; i++) {
            items[i].classList.remove('bg-gray-100');
        }
    }
    
    function closeAllLists() {
        suggestionsDiv.innerHTML = '';
        suggestionsDiv.classList.add('hidden');
        currentFocus = -1;
    }
    
    // Close suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!container.contains(e.target)) {
            closeAllLists();
        }
    });
}

// Initialize autocomplete on page load
document.addEventListener('DOMContentLoaded', function() {
    const obatItems = document.querySelectorAll('.obat-item');
    obatItems.forEach(item => {
        initializeAutocomplete(item);
    });
});
</script>
@endpush
@endsection