@extends('layouts.app')

@section('page-title', 'Edit External Employee')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-3">
            <a href="{{ route('external-employee.index') }}" class="p-2 hover:bg-white rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="bg-linear-to-r from-orange-600 to-orange-700 p-3 rounded-lg shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    Edit External Employee
                </h1>
                <p class="text-gray-600 mt-1 ml-1">Ubah data karyawan eksternal yang ada</p>
            </div>
        </div>
    </div>

    <form action="{{ route('external-employee.update', $externalEmployee->id) }}" method="POST"
        enctype="multipart/form-data" id="formExternalEmployee">
        @csrf
        @method('PUT')
        <!-- Manual Input Section Card -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="bg-linear-to-r from-orange-600 to-orange-700 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Data External Employee
                </h2>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Form Fields -->
                    <div class="lg:col-span-2 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- NIK Employee -->
                            <div>
                                <label for="nik_employee" class="block text-sm font-semibold text-gray-700 mb-2">
                                    NIK Employee <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                        </svg>
                                    </div>
                                    <input type="text" id="nik_employee" name="nik_employee"
                                        value="{{ old('nik_employee', $externalEmployee->nik_employee) }}"
                                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        placeholder="Masukkan NIK Employee" required>
                                </div>
                                @error('nik_employee')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nama Employee -->
                            <div>
                                <label for="nama_employee" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nama Employee <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="nama_employee" name="nama_employee"
                                        value="{{ old('nama_employee', $externalEmployee->nama_employee) }}"
                                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        placeholder="Nama lengkap employee" required>
                                </div>
                                @error('nama_employee')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kode RM -->
                            <div>
                                <label for="kode_rm" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Kode RM <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="kode_rm" name="kode_rm"
                                        value="{{ old('kode_rm', $externalEmployee->kode_rm) }}"
                                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        placeholder="Kode RM" readonly required>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Kode RM otomatis dengan kode hubungan F</p>
                                @error('kode_rm')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Jenis Kelamin -->
                            <div>
                                <label for="jenis_kelamin" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Jenis Kelamin <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select id="jenis_kelamin" name="jenis_kelamin"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 appearance-none bg-white"
                                        required>
                                        <option value="">-- Pilih Jenis Kelamin --</option>
                                        <option value="L" {{ old('jenis_kelamin', $externalEmployee->
                                            getRawOriginal('jenis_kelamin') ?? '') == 'L' ? 'selected' : '' }}>
                                            Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin', $externalEmployee->
                                            getRawOriginal('jenis_kelamin') ?? '') == 'P' ? 'selected' : '' }}>
                                            Perempuan</option>
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                @error('jenis_kelamin')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal Lahir -->
                            <div>
                                <label for="tanggal_lahir" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tanggal Lahir <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                                        value="{{ old('tanggal_lahir', $externalEmployee->tanggal_lahir->format('Y-m-d')) }}"
                                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        required>
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
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="no_hp" name="no_hp"
                                        value="{{ old('no_hp', $externalEmployee->no_hp) }}"
                                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        placeholder="08xxxxxxxxxx" required>
                                </div>
                                @error('no_hp')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Vendor and Kategori Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Vendor -->
                            <div>
                                <label for="id_vendor" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Vendor <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select id="id_vendor" name="id_vendor"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 appearance-none bg-white"
                                        required>
                                        <option value="">-- Pilih Vendor --</option>
                                        @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id_vendor }}" {{ old('id_vendor', $externalEmployee->
                                            id_vendor) == $vendor->id_vendor ? 'selected' : '' }}>
                                            {{ $vendor->nama_vendor }}</option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                @error('id_vendor')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kategori -->
                            <div>
                                <label for="id_kategori" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Kategori <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select id="id_kategori" name="id_kategori"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 appearance-none bg-white"
                                        required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach ($kategoris as $kategori)
                                        <option value="{{ $kategori->id_kategori }}" {{ old('id_kategori',
                                            $externalEmployee->id_kategori) == $kategori->id_kategori ? 'selected' : ''
                                            }}>
                                            {{ $kategori->nama_kategori }} ({{ $kategori->kode_kategori }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                @error('id_kategori')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Additional Info Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- No KTP -->
                            <div>
                                <label for="no_ktp" class="block text-sm font-semibold text-gray-700 mb-2">
                                    No KTP
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                        </svg>
                                    </div>
                                    <input type="text" id="no_ktp" name="no_ktp"
                                        value="{{ old('no_ktp', $externalEmployee->no_ktp) }}"
                                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        placeholder="Nomor KTP">
                                </div>
                                @error('no_ktp')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- BPJS ID -->
                            <div>
                                <label for="bpjs_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    BPJS ID
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                        </svg>
                                    </div>
                                    <input type="text" id="bpjs_id" name="bpjs_id"
                                        value="{{ old('bpjs_id', $externalEmployee->bpjs_id) }}" maxlength="50"
                                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        placeholder="Contoh: 0001234567890">
                                </div>
                                @error('bpjs_id')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="status" name="status"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 appearance-none bg-white"
                                    required>
                                    <option value="aktif" {{ old('status', $externalEmployee->status) == 'aktif' ?
                                        'selected' : '' }}>
                                        Aktif</option>
                                    <option value="nonaktif" {{ old('status', $externalEmployee->status) == 'nonaktif' ?
                                        'selected' : '' }}>
                                        Nonaktif</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            @error('status')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Alamat (Full Width) -->
                        <div>
                            <label for="alamat" class="block text-sm font-semibold text-gray-700 mb-2">
                                Alamat <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <textarea id="alamat" name="alamat" rows="4"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="Masukkan alamat lengkap employee"
                                    required>{{ old('alamat', $externalEmployee->alamat) }}</textarea>
                            </div>
                            @error('alamat')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column - Photo Upload -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 text-center">Foto
                                Employee</label>
                            <div class="space-y-3">
                                <!-- Upload Area -->
                                <div class="relative w-full max-w-xs mx-auto">
                                    <div class="aspect-3/4 w-full">
                                        <input type="file" id="foto" name="foto" accept="image/*" class="hidden"
                                            onchange="previewImage(event)">
                                        <label for="foto"
                                            class="absolute inset-0 flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                            <div id="preview-container"
                                                class="flex flex-col items-center justify-center w-full h-full p-3">
                                                @if ($externalEmployee->foto)
                                                <img src="{{ asset('storage/' . $externalEmployee->foto) }}"
                                                    class="absolute inset-0 w-full h-full object-contain rounded-lg"
                                                    alt="Current photo">
                                                <button type="button" onclick="clearImage()"
                                                    class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow-lg transition-colors z-10">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                                @else
                                                <svg class="w-8 h-8 text-gray-400 mb-2" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <p class="mb-1 text-xs text-gray-500 font-semibold">Klik untuk
                                                    upload</p>
                                                <p class="text-xs text-gray-500">PNG, JPG, JPEG</p>
                                                <p class="text-xs text-gray-400">(MAX. 2MB)</p>
                                                @endif
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                @error('foto')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror

                                <!-- Info -->
                                <div class="bg-orange-50 border border-orange-100 rounded-lg p-3">
                                    <p class="text-xs text-orange-800">
                                        <span class="font-semibold">Tips:</span> Maksimal ukuran foto 2MB. Kosongkan
                                        jika tidak ingin mengubah foto.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Form Actions -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="window.location.href='{{ route('external-employee.index') }}'"
                    class="px-6 py-2.5 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-medium rounded-lg transition-all hover:shadow-md">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </button>
                <button type="submit"
                    class="px-6 py-2.5 bg-linear-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Data External Employee
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Auto-generate Kode RM with hubungan F
            document.addEventListener('DOMContentLoaded', function() {
                const nikEmployeeInput = document.getElementById('nik_employee');
                const kodeRmInput = document.getElementById('kode_rm');

                // Function to generate Kode RM
                function generateKodeRM() {
                    const nik = nikEmployeeInput.value.trim();
                    if (nik) {
                        kodeRmInput.value = nik + '-F';
                    } else {
                        kodeRmInput.value = '';
                    }
                }

                // Event listener for NIK input
                nikEmployeeInput.addEventListener('input', generateKodeRM);

                // Generate initial Kode RM if NIK is pre-filled
                generateKodeRM();
            });

            function previewImage(event) {
                const file = event.target.files[0];
                const container = document.getElementById('preview-container');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        container.innerHTML = `
                <div class="relative w-full h-full">
                    <img src="${e.target.result}" class="absolute inset-0 w-full h-full object-contain rounded-lg" alt="Preview">
                    <button type="button" onclick="clearImage()" class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow-lg transition-colors z-10">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                document.getElementById('preview-container').innerHTML = `
        <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <p class="mb-1 text-xs text-gray-500 font-semibold">Klik untuk upload</p>
        <p class="text-xs text-gray-500">PNG, JPG, JPEG</p>
        <p class="text-xs text-gray-400">(MAX. 2MB)</p>
    `;
            }
</script>
@endpush
@endsection