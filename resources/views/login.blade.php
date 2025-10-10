<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Login - SIPO ICBP</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50">
        <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <!-- Logo/Header -->
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900">
                        SIPO - ICBP
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Sistem Informasi Poliklinik
                    </p>
                </div>

                <!-- Login Form -->
                <div class="bg-white shadow-md rounded-lg px-8 py-10">
                    <!-- Alert Messages -->
                    @if (session('error'))
                        <x-alert type="error" dismissible>
                            {{ session('error') }}
                        </x-alert>
                    @endif

                    @if (session('success'))
                        <x-alert type="success" dismissible>
                            {{ session('success') }}
                        </x-alert>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Username/Email -->
                        <x-form-input
                            type="text"
                            name="username"
                            label="Username"
                            placeholder="Masukkan username"
                            value="{{ old('username') }}"
                            required
                            autofocus
                            :error="$errors->first('username')"
                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>'
                        />

                        <!-- Password -->
                        <x-form-input
                            type="password"
                            name="password"
                            label="Password"
                            placeholder="Masukkan password"
                            required
                            :error="$errors->first('password')"
                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>'
                        />

                        <!-- Submit Button -->
                        <div>
                            <x-button
                                type="submit"
                                variant="primary"
                                class="w-full"
                                size="lg"
                            >
                                Masuk
                            </x-button>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="text-center text-sm text-gray-500">
                    <p>&copy; {{ date('Y') }} SIPO - ICBP. All rights reserved.</p>
                </div>
            </div>
        </div>
    </body>
</html>
