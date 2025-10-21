<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Dashboard') - SIPO ICBP</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700|quicksand:300,400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- SweetAlert2 -->
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Custom Styles for Token Modal -->
        <style>
            /* SweetAlert2 Custom Animations */
            .swal2-popup {
                animation: swal2-show 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            }

            .swal2-hide {
                animation: swal2-hide 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            }

            @keyframes swal2-show {
                0% {
                    transform: scale(0.8) translateY(-20px);
                    opacity: 0;
                }
                100% {
                    transform: scale(1) translateY(0);
                    opacity: 1;
                }
            }

            @keyframes swal2-hide {
                0% {
                    transform: scale(1) translateY(0);
                    opacity: 1;
                }
                100% {
                    transform: scale(0.8) translateY(-20px);
                    opacity: 0;
                }
            }

            /* Custom Toast Animations */
            .colored-toast.swal2-toast-show {
                animation: slideInRight 0.3s ease-out;
            }

            .colored-toast.swal2-toast-hide {
                animation: slideOutRight 0.3s ease-in;
            }

            @keyframes slideInRight {
                0% {
                    transform: translateX(100%);
                    opacity: 0;
                }
                100% {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            @keyframes slideOutRight {
                0% {
                    transform: translateX(0);
                    opacity: 1;
                }
                100% {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }

            /* Sidebar Emergency Menu Animation */
            .emergency-menu-enter {
                animation: emergencyMenuEnter 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            }

            @keyframes emergencyMenuEnter {
                0% {
                    transform: translateX(-20px) scale(0.9);
                    opacity: 0;
                }
                100% {
                    transform: translateX(0) scale(1);
                    opacity: 1;
                }
            }

            /* Token Input Focus Effect */
            .token-input-focus {
                box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
                border-color: rgb(6, 182, 212);
            }

            /* Button Hover Effects */
            .btn-emergency {
                position: relative;
                overflow: hidden;
            }

            .btn-emergency::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                transition: left 0.5s;
            }

            .btn-emergency:hover::before {
                left: 100%;
            }
        </style>

        @stack('styles')
    </head>
    <body class="bg-gray-100">
        <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">
            <!-- Sidebar -->
            @include('components.sidebar')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Top Header/Navbar -->
                @include('components.navbar')

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto bg-gray-100 p-5">
                    @yield('content')
                </main>
            </div>
        </div>

        <!-- SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

        <!-- SweetAlert2 Notifications -->
        @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                toast: true,
                position: 'top-end',
                background: '#f0fdf4',
                iconColor: '#22c55e',
                customClass: {
                    popup: 'colored-toast'
                }
            });
        </script>
        @endif

        @if(session('warning'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: '{{ session('warning') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                toast: true,
                position: 'top-end',
                background: '#fef3c7',
                iconColor: '#f59e0b',
                customClass: {
                    popup: 'colored-toast'
                }
            });
        </script>
        @endif

        @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                toast: true,
                position: 'top-end',
                background: '#fee2e2',
                iconColor: '#ef4444',
                customClass: {
                    popup: 'colored-toast'
                }
            });
        </script>
        @endif

        @if($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan!',
                html: '<ul style="text-align: left; padding-left: 20px;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3b82f6'
            });
        </script>
        @endif

        @stack('scripts')
    </body>
</html>
