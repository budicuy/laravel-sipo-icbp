@extends('layouts.app')

@section('title', 'Tambah External Employee')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-t-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 p-3 rounded-lg backdrop-blur-sm">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white">Tambah External Employee</h1>
                            <p class="text-purple-100 mt-1">Form tambah data karyawan eksternal baru</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('external-employee.index') }}"
                           class="bg-white/20 text-white px-6 py-3 rounded-lg font-semibold hover:bg-white/30 transition-all duration-200 backdrop-blur-sm">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="bg-white rounded-b-xl shadow-lg overflow-hidden">
                <form action="{{ route('external-employee.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <div>
                                <label for="nik_employee" class="block text-sm font-medium text-gray-700 mb-2">
                                    NIK Employee <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="nik_employee"
                                       name="nik_employee"
                                       value="{{ old('nik_employee') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('nik_employee') border-red-500 @enderror"
                                       placeholder="Masukkan NIK Employee"
                                       required>
                                @error('nik_employee')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="nama_employee" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Employee <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="nama_employee"
                                       name="nama_employee"
                                       value="{{ old('nama_employee') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('nama_employee') border-red-500 @enderror"
                                       placeholder="Masukkan Nama Employee"
                                       required>
                                @error('nama_employee')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="kode_rm" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kode RM <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="kode_rm"
                                       name="kode_rm"
                                       value="{{ old('kode_rm') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('kode_rm') border-red-500 @enderror"
                                       placeholder="Masukkan Kode RM"
                                       required>
                                @error('kode_rm')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Lahir <span class="text-red-500">*</span>
                                </label>
                                <input type="date"
                                       id="tanggal_lahir"
                                       name="tanggal_lahir"
                                       value="{{ old('tanggal_lahir') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('tanggal_lahir') border-red-500 @enderror"
                                       required>
                                @error('tanggal_lahir')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jenis Kelamin <span class="text-red-500">*</span>
                                </label>
                                <select id="jenis_kelamin"
                                        name="jenis_kelamin"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('jenis_kelamin') border-red-500 @enderror"
                                        required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="id_vendor" class="block text-sm font-medium text-gray-700 mb-2">
                                    Vendor <span class="text-red-500">*</span>
                                </label>
                                <select id="id_vendor"
                                        name="id_vendor"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('id_vendor') border-red-500 @enderror"
                                        required>
                                    <option value="">Pilih Vendor</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id_vendor }}"
                                                {{ old('id_vendor') == $vendor->id_vendor ? 'selected' : '' }}>
                                            {{ $vendor->nama_vendor }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_vendor')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <div>
                                <label for="id_kategori" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kategori <span class="text-red-500">*</span>
                                </label>
                                <select id="id_kategori"
                                        name="id_kategori"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('id_kategori') border-red-500 @enderror"
                                        required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($kategoris as $kategori)
                                        <option value="{{ $kategori->id_kategori }}"
                                                {{ old('id_kategori') == $kategori->id_kategori ? 'selected' : '' }}>
                                            {{ $kategori->nama_kategori }} ({{ $kategori->kode_kategori }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_kategori')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-2">
                                    No. HP <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="no_hp"
                                       name="no_hp"
                                       value="{{ old('no_hp') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('no_hp') border-red-500 @enderror"
                                       placeholder="Masukkan No. HP"
                                       required>
                                @error('no_hp')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="no_ktp" class="block text-sm font-medium text-gray-700 mb-2">
                                    No. KTP
                                </label>
                                <input type="text"
                                       id="no_ktp"
                                       name="no_ktp"
                                       value="{{ old('no_ktp') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('no_ktp') border-red-500 @enderror"
                                       placeholder="Masukkan No. KTP">
                                @error('no_ktp')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="bpjs_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    BPJS ID
                                </label>
                                <input type="text"
                                       id="bpjs_id"
                                       name="bpjs_id"
                                       value="{{ old('bpjs_id') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('bpjs_id') border-red-500 @enderror"
                                       placeholder="Masukkan BPJS ID">
                                @error('bpjs_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select id="status"
                                        name="status"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('status') border-red-500 @enderror"
                                        required>
                                    <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                                @error('status')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">
                                    Foto
                                </label>
                                <div class="relative">
                                    <input type="file"
                                           id="foto"
                                           name="foto"
                                           accept="image/*"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('foto') border-red-500 @enderror">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                                @error('foto')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-sm text-gray-500">Format: JPEG, PNG, JPG. Max: 2MB</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                            Alamat <span class="text-red-500">*</span>
                        </label>
                        <textarea id="alamat"
                                  name="alamat"
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('alamat') border-red-500 @enderror"
                                  placeholder="Masukkan Alamat"
                                  required>{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-8 flex items-center justify-end space-x-4">
                        <a href="{{ route('external-employee.index') }}"
                           class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all duration-200">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Batal
                        </a>
                        <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg font-semibold hover:from-purple-700 hover:to-purple-800 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection