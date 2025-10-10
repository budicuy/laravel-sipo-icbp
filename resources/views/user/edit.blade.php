@extends('layouts.app')

@section('page-title', 'Edit Data User')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-3">
            <a href="{{ route('user.index') }}" class="p-2 hover:bg-white rounded-lg transition-colors">
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
                    Edit Data User
                </h1>
                <p class="text-gray-600 mt-1 ml-1">Perbarui informasi data pengguna</p>
            </div>
        </div>
    </div>

    <form action="{{ route('user.update', $user->id_user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-600 to-orange-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Informasi Data User
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                        </div>
                    </div>

                    <div>
                        <label for="nama_lengkap" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                        </div>
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="role" name="role" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 appearance-none bg-white" required>
                                <option value="Super Admin" {{ old('role', $user->role) == 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
                                <option value="Admin" {{ old('role', $user->role) == 'Admin' ? 'selected' : '' }}>Admin</option>
                                <option value="User" {{ old('role', $user->role) == 'User' ? 'selected' : '' }}>User</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                            <p class="text-sm text-blue-800 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Info:</strong> Kosongkan field password jika tidak ingin mengubah password</span>
                            </p>
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            Password Baru
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input type="password" id="password" name="password" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" placeholder="Kosongkan jika tidak ingin ubah">
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                            Konfirmasi Password Baru
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" placeholder="Ulangi password baru">
                        </div>
                    </div>

                    <!-- Status Aktif -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Status Aktif
                        </label>
                        <div class="flex items-center gap-3">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="is_active" value="1" {{ old('is_active', $user->is_active ?? 1) == 1 ? 'checked' : '' }} class="w-4 h-4 text-green-600 focus:ring-green-500">
                                <span class="ml-2 text-sm text-gray-700">Aktif</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="is_active" value="0" {{ old('is_active', $user->is_active ?? 1) == 0 ? 'checked' : '' }} class="w-4 h-4 text-red-600 focus:ring-red-500">
                                <span class="ml-2 text-sm text-gray-700">Nonaktif</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-between items-center">
                <button type="button" onclick="document.getElementById('deleteForm').submit();" class="px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus User
                </button>
                <div class="flex gap-3">
                    <button type="button" onclick="window.location.href='{{ route('user.index') }}'" class="px-6 py-2.5 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-medium rounded-lg transition-all hover:shadow-md">
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

    <!-- Form Delete Terpisah -->
    <form id="deleteForm" action="{{ route('user.destroy', $user->id_user) }}" method="POST" style="display: none;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
