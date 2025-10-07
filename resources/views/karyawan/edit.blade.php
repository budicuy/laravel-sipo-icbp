@extends('layouts.app')

@section('page-title', 'Edit Karyawan')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-3">
            <a href="{{ route('karyawan.index') }}" class="p-2 hover:bg-white rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="bg-gradient-to-r from-yellow-600 to-orange-600 p-3 rounded-lg shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    Edit Data Karyawan
                </h1>
                <p class="text-gray-600 mt-1 ml-1">Perbarui informasi data karyawan</p>
            </div>
        </div>
    </div>

    <form action="{{ route('karyawan.update', $karyawan->id_karyawan) }}" method="POST" enctype="multipart/form-data" id="formKaryawan" onsubmit="return confirmUpdate(event)">
        @csrf
        @method('PUT')

        <!-- Manual Input Section Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-600 to-orange-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Informasi Data Karyawan
                </h2>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Form Fields -->
                    <div class="lg:col-span-2 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- NIK -->
                            <div>
                                <label for="nik" class="block text-sm font-semibold text-gray-700 mb-2">
                                    NIK <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                        </svg>
                                    </div>
                                    <input type="text" id="nik" name="nik" maxlength="16" value="{{ old('nik', $karyawan->nik_karyawan) }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" placeholder="Masukkan NIK" required>
                                </div>
                                @error('nik')
                                    <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nama Karyawan -->
                            <div>
                                <label for="nama" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nama Karyawan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="nama" name="nama" value="{{ old('nama', $karyawan->nama_karyawan) }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" placeholder="Nama lengkap karyawan" required>
                                </div>
                                @error('nama')
                                    <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Departemen -->
                            <div>
                                <label for="departemen" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Departemen <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select id="departemen" name="departemen" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 appearance-none bg-white" required>
                                        <option value="">-- Pilih Departemen --</option>
                                        @foreach($departemens as $dept)
                                            <option value="{{ $dept->id_departemen }}" {{ (int)old('departemen', $karyawan->id_departemen) === (int)$dept->id_departemen ? 'selected' : '' }}>{{ $dept->nama_departemen }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                @error('departemen')
                                    <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Jenis Kelamin -->
                            <div>
                                <label for="jenis_kelamin" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Jenis Kelamin <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select id="jenis_kelamin" name="jenis_kelamin" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 appearance-none bg-white" required>
                                        <option value="">-- Pilih Jenis Kelamin --</option>
                                        <option value="L" {{ old('jenis_kelamin', $karyawan->jenis_kelamin_short) == 'L' || $karyawan->jenis_kelamin == 'Laki - Laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin', $karyawan->jenis_kelamin_short) == 'P' || $karyawan->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                @error('jenis_kelamin')
                                    <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal Lahir -->
                            <div>
                                <label for="tanggal_lahir" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tanggal Lahir <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', optional($karyawan->tanggal_lahir)->format('Y-m-d')) }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" required>
                                </div>
                                @error('tanggal_lahir')
                                    <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- No HP -->
                            <div>
                                <label for="no_hp" class="block text-sm font-semibold text-gray-700 mb-2">
                                    No HP <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $karyawan->no_hp) }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" placeholder="08xxxxxxxxxx" required>
                                </div>
                                @error('no_hp')
                                    <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Alamat (Full Width) -->
                        <div>
                            <label for="alamat" class="block text-sm font-semibold text-gray-700 mb-2">
                                Alamat <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <textarea id="alamat" name="alamat" rows="4" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" placeholder="Masukkan alamat lengkap karyawan" required>{{ old('alamat', $karyawan->alamat) }}</textarea>
                            </div>
                            @error('alamat')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column - Photo Upload -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Karyawan</label>
                            <div class="space-y-3">
                                <!-- Upload Area -->
                                <div class="relative">
                                    <input type="file" id="foto" name="foto" accept="image/*" class="hidden" onchange="previewImage(event)">
                                    <label for="foto" class="flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                        <div id="preview-container" class="flex flex-col items-center justify-center w-full h-full">
                                            @if ($karyawan->foto)
                                                <div class="relative w-full h-full">
                                                    <img src="{{ asset('storage/' . $karyawan->foto) }}"
                                                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($karyawan->nama_karyawan) }}&background=0D8ABC&color=fff&size=256';"
                                                         class="w-full h-full object-cover rounded-lg"
                                                         alt="Foto saat ini">
                                                </div>
                                            @else
                                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                    <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <p class="mb-2 text-sm text-gray-500 font-semibold">Klik untuk upload foto</p>
                                                    <p class="text-xs text-gray-500">PNG, JPG, JPEG (MAX. 30KB)</p>
                                                </div>
                                            @endif
                                        </div>
                                    </label>
                                </div>
                                @error('foto')
                                    <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror

                                <!-- Info -->
                                <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-3">
                                    <p class="text-xs text-yellow-800">
                                        <span class="font-semibold">Info:</span> Abaikan jika tidak ingin mengubah foto. Klik pada area foto untuk memilih file baru. Ukuran maksimal 30KB.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end items-center">
                <div class="flex gap-3">
                    <button type="button" onclick="window.location.href='{{ route('karyawan.index') }}'" class="px-6 py-2.5 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-medium rounded-lg transition-all hover:shadow-md">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-yellow-600 to-orange-600 hover:from-yellow-700 hover:to-orange-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
const originalPreview = document.getElementById('preview-container').innerHTML;

function confirmUpdate(event) {
    event.preventDefault();
    
    Swal.fire({
        title: 'Update Data Karyawan?',
        text: "Pastikan perubahan data sudah benar!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Update!',
        cancelButtonText: 'Cek Lagi',
        reverseButtons: true,
        customClass: {
            confirmButton: 'px-5 py-2.5 rounded-lg font-medium',
            cancelButton: 'px-5 py-2.5 rounded-lg font-medium'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formKaryawan').submit();
        }
    });
    
    return false;
}

function previewImage(event) {
    const file = event.target.files[0];
    const container = document.getElementById('preview-container');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            container.innerHTML = `
                <div class="relative w-full h-full">
                    <img src="${e.target.result}" class="w-full h-full object-cover rounded-lg" alt="Preview Foto Baru">
                    <button type="button" onclick="clearImage()" class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 shadow-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            `;
        }
        reader.readAsDataURL(file);
    }
}

function clearImage() {
    document.getElementById('foto').value = '';
    document.getElementById('preview-container').innerHTML = originalPreview;
}
</script>
@endpush
@endsection
