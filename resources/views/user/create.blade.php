@extends('layouts.app')

@section('page-title', 'Tambah Data User')

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
                    <div class="bg-gradient-to-r from-indigo-600 to-blue-600 p-3 rounded-lg shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    Tambah User Baru
                </h1>
                <p class="text-gray-600 mt-1 ml-1">Tambahkan pengguna baru ke sistem</p>
            </div>
        </div>
    </div>

    <form action="{{ route('user.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-4">
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
                    <!-- Username -->
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
                            <input type="text" id="username" name="username" value="{{ old('username') }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Username untuk login" required>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Username harus unik dan tidak boleh ada spasi</p>
                    </div>

                    <!-- Nama Lengkap -->
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
                            <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nama lengkap user" required>
                        </div>
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="role" name="role" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 appearance-none bg-white" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="Super Admin" {{ old('role') == 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
                                <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                <option value="User" {{ old('role') == 'User' ? 'selected' : '' }}>User</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input type="password" id="password" name="password" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Minimal 8 karakter" required>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Password minimal 8 karakter</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ulangi password" required>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="window.location.href='{{ route('user.index') }}'" class="px-6 py-2.5 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-medium rounded-lg transition-all hover:shadow-md">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Data User
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
