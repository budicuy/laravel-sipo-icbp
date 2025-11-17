<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIPO - Sistem Informasi Pelayanan Kesehatan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        /home/budicuyyy/Unduhan/Generated Image November 07,
        2025 - 9_28AM.webp/home/budicuyyy/Unduhan/Generated Image November 07,
        2025 - 9_28AM.webp @keyframes floating {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .chat-container {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }

        .chat-button {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .chat-button:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .chat-box {
            position: absolute;
            bottom: 80px;
            right: 0;
            width: 380px;
            height: 500px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            display: none;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-box.active {
            display: flex;
        }

        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            font-weight: 600;
        }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f9fafb;
        }

        .chat-input-container {
            padding: 15px;
            background: white;
            border-top: 1px solid #e5e7eb;
        }

        .message {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
        }

        .message.user {
            flex-direction: row-reverse;
        }

        .message-bubble {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            word-wrap: break-word;
        }

        .message.bot .message-bubble {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .message.user .message-bubble {
            background: #e5e7eb;
            color: #1f2937;
        }

        .typing-indicator {
            display: flex;
            gap: 4px;
            padding: 12px 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 18px;
            width: fit-content;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: white;
            animation: typing 1.4s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {

            0%,
            60%,
            100% {
                transform: translateY(0);
            }

            30% {
                transform: translateY(-10px);
            }
        }

        .scroll-smooth {
            scroll-behavior: smooth;
        }

        /* New Chat Section Styles */
        #chatMessages .message.user .flex {
            flex-direction: row-reverse;
        }

        #chatMessages .message.user .bg-linear-to-r {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 1rem;
            border-top-right-radius: 0.25rem;
        }

        #chatMessages::-webkit-scrollbar {
            width: 8px;
        }

        #chatMessages::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        #chatMessages::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }

        #chatMessages::-webkit-scrollbar-thumb:hover {
            background: #667eea;
        }

        /* AI Response Formatting */
        .message.bot .prose {
            color: #1f2937;
        }

        .message.bot .prose p {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }

        .message.bot .prose h1,
        .message.bot .prose h2,
        .message.bot .prose h3 {
            font-weight: 700;
            line-height: 1.3;
        }

        .message.bot .prose h1 {
            font-size: 1.5rem;
            color: #4c1d95;
            margin-top: 1rem;
            margin-bottom: 0.75rem;
        }

        .message.bot .prose h2 {
            font-size: 1.25rem;
            color: #5b21b6;
            margin-top: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .message.bot .prose h3 {
            font-size: 1.125rem;
            color: #6d28d9;
            margin-top: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .message.bot .prose strong {
            color: #4c1d95;
            font-weight: 700;
        }

        .message.bot .prose em {
            color: #5b21b6;
        }

        .message.bot .prose ul,
        .message.bot .prose ol {
            margin: 0.5rem 0;
            padding-left: 1.5rem;
        }

        .message.bot .prose li {
            margin: 0.125rem 0;
            line-height: 1.4;
            padding: 0.125rem 0;
        }

        .message.bot .prose ul {
            list-style-type: disc;
        }

        .message.bot .prose ol {
            list-style-type: decimal;
        }

        .message.bot .prose br {
            content: "";
            display: block;
            margin-bottom: 0.25rem;
        }

        /* Responsive Header Styles */
        @media (max-width: 640px) {
            .gradient-bg {
                padding: 1rem !important;
            }
        }

        /* Truncate text helper */
        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Better spacing for small screens */
        @media (max-width: 768px) {
            #chatMessages {
                height: 24rem;
            }
        }

        /* Mobile Menu Styles */
        #mobileMenu {
            transition: all 0.3s ease;
            transform-origin: top;
        }

        #mobileMenu.hidden {
            transform: scaleY(0);
            opacity: 0;
        }

        #mobileMenu:not(.hidden) {
            transform: scaleY(1);
            opacity: 1;
        }

        /* Mobile menu animation */
        @keyframes slideDown {
            from {
                transform: scaleY(0);
                opacity: 0;
            }

            to {
                transform: scaleY(1);
                opacity: 1;
            }
        }

        #mobileMenu:not(.hidden) {
            animation: slideDown 0.3s ease-out;
        }

        /* Ensure mobile menu is above other content */
        #mobileMenu {
            position: relative;
            z-index: 40;
        }

        /* Swiper Hero Styles */
        .hero-swiper {
            width: 100%;
            height: 80vh;
            min-height: 600px;
            position: relative;
        }

        .hero-slide {
            position: relative;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.85) 0%, rgba(118, 75, 162, 0.85) 100%);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            align-items: center;
        }

        .swiper-pagination-bullet {
            width: 12px;
            height: 12px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 1;
            transition: all 0.3s ease;
        }

        .swiper-pagination-bullet-active {
            background: white;
            transform: scale(1.3);
        }

        .swiper-button-next,
        .swiper-button-prev {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        .swiper-button-next::after,
        .swiper-button-prev::after {
            font-size: 20px;
        }

        /* Animation for hero content */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-animate {
            animation: fadeInUp 1s ease-out;
        }

        .hero-animate-delay-1 {
            animation-delay: 0.2s;
            opacity: 0;
            animation-fill-mode: forwards;
        }

        .hero-animate-delay-2 {
            animation-delay: 0.4s;
            opacity: 0;
            animation-fill-mode: forwards;
        }

        .hero-animate-delay-3 {
            animation-delay: 0.6s;
            opacity: 0;
            animation-fill-mode: forwards;
        }
    </style>
    @vite('resources/css/app.css')
</head>

<body class="scroll-smooth">
    <!-- Navbar -->
    <nav class="fixed w-full bg-white/90 backdrop-blur-md shadow-sm z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <img src="{{ asset('logo.png') }}" alt="SIPO Logo" class="h-10">
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-gray-700 hover:text-purple-600 transition">Beranda</a>
                    <a href="{{ route('ai-chat') }}"
                        class="text-purple-600 font-semibold hover:text-purple-700 transition">AI Chat</a>
                    <a href="#features" class="text-gray-700 hover:text-purple-600 transition">Fitur</a>
                    <a href="#about" class="text-gray-700 hover:text-purple-600 transition">Tentang</a>
                    <a href="#contact" class="text-gray-700 hover:text-purple-600 transition">Kontak</a>
                    <a href="{{ route('login') }}"
                        class="gradient-bg text-white px-6 py-2 rounded-full hover:opacity-90 transition">
                        Login
                    </a>
                </div>
                <button id="mobileMenuButton" class="md:hidden text-gray-700 hover:text-purple-600 transition">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-white/95 backdrop-blur-md shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 space-y-3">
                <a href="#home" class="block text-gray-700 hover:text-purple-600 transition py-2">Beranda</a>
                <a href="{{ route('ai-chat') }}"
                    class="block text-purple-600 font-semibold hover:text-purple-700 transition py-2">AI Chat</a>
                <a href="#features" class="block text-gray-700 hover:text-purple-600 transition py-2">Fitur</a>
                <a href="#about" class="block text-gray-700 hover:text-purple-600 transition py-2">Tentang</a>
                <a href="#contact" class="block text-gray-700 hover:text-purple-600 transition py-2">Kontak</a>
                <a href="{{ route('login') }}"
                    class="block gradient-bg text-white px-6 py-2 rounded-full hover:opacity-90 transition text-center">
                    Login
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Swiper -->
    <section id="home" class="relative">
        <!-- Swiper -->
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">
                <!-- Slide 1 -->
                <div class="swiper-slide hero-slide" style="background-image: url('{{ asset('banner.webp') }}');">
                    <div class="hero-overlay"></div>
                    <div class="hero-content">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                            <div class="grid md:grid-cols-2 gap-8 items-center">
                                <div class="text-white">
                                    <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight hero-animate">
                                        Sistem Informasi Poliklinik
                                    </h1>
                                    <p class="text-xl mb-8 text-purple-100 hero-animate hero-animate-delay-1">
                                        Solusi digital terpadu untuk manajemen pelayanan kesehatan karyawan yang
                                        efisien, modern, dan
                                        terintegrasi dengan teknologi AI.
                                    </p>
                                    <div class="flex flex-col sm:flex-row gap-4 hero-animate hero-animate-delay-2">
                                        <a href="{{ route('login') }}"
                                            class="bg-white text-purple-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition text-center">
                                            Mulai Sekarang
                                        </a>
                                        <a href="#features"
                                            class="border-2 border-white text-white px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-purple-600 transition text-center">
                                            Pelajari Lebih Lanjut
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="swiper-slide hero-slide" style="background-image: url('{{ asset('about.webp') }}');">
                    <div class="hero-overlay"></div>
                    <div class="hero-content">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                            <div class="grid md:grid-cols-2 gap-8 items-center">
                                <div class="text-white">
                                    <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight hero-animate">
                                        Rekam Medis Digital
                                    </h1>
                                    <p class="text-xl mb-8 text-purple-100 hero-animate hero-animate-delay-1">
                                        Akses riwayat medis karyawan secara real-time dengan sistem keamanan berlapis
                                        dan
                                        enkripsi data terpercaya.
                                    </p>
                                    <div class="flex flex-col sm:flex-row gap-4 hero-animate hero-animate-delay-2">
                                        <a href="{{ route('login') }}"
                                            class="bg-white text-purple-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition text-center">
                                            Coba Sekarang
                                        </a>
                                        <a href="#features"
                                            class="border-2 border-white text-white px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-purple-600 transition text-center">
                                            Lihat Fitur
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="swiper-slide hero-slide" style="background-image: url('{{ asset('ai.jpeg') }}');">
                    <div class="hero-overlay"></div>
                    <div class="hero-content">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                            <div class="grid md:grid-cols-2 gap-8 items-center">
                                <div class="text-white">
                                    <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight hero-animate">
                                        AI Assistant 24/7
                                    </h1>
                                    <p class="text-xl mb-8 text-purple-100 hero-animate hero-animate-delay-1">
                                        Chat dengan AI assistant powered by Gemini untuk informasi kesehatan, panduan
                                        penggunaan,
                                        dan bantuan cepat kapan saja.
                                    </p>
                                    <div class="flex flex-col sm:flex-row gap-4 hero-animate hero-animate-delay-2">
                                        <a href="{{ route('ai-chat') }}"
                                            class="bg-white text-purple-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition text-center">
                                            Mulai Chat
                                        </a>
                                        <a href="#features"
                                            class="border-2 border-white text-white px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-purple-600 transition text-center">
                                            Pelajari Lebih Lanjut
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
            <!-- Add Navigation -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>



    <!-- AI Chat Promo Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">
                    <span class="gradient-text">AI Assistant</span> SIPO
                </h2>
                <p class="text-xl text-gray-600">Tanyakan apapun tentang sistem kami, kami siap membantu Anda 24/7</p>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-3xl  p-8 md:p-12">
                    <div class="text-center">
                        <div
                            class="w-52 h-52 gradient-bg rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden mx-auto mb-4">
                            <img src="{{ asset('ai.jpeg') }}" alt="AI Avatar"
                                class="w-full h-full object-cover relative">
                        </div>
                        <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">AI Assistant Siap Membantu Anda
                        </h3>
                        <p class="text-lg text-gray-600 mb-8">Dapatkan jawaban instan untuk pertanyaan tentang sistem
                            kesehatan, riwayat medis, dan informasi kesehatan lainnya.</p>
                        <div class="grid md:grid-cols-3 gap-6 mb-8">
                            <div class="text-center">
                                <div
                                    class="w-16 h-16 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-comments text-purple-600 text-2xl"></i>
                                </div>
                                <h4 class="font-semibold text-gray-800 mb-2">Chat 24/7</h4>
                                <p class="text-gray-600 text-sm">Tersedia kapan saja Anda membutuhkan bantuan</p>
                            </div>
                            <div class="text-center">
                                <div
                                    class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-md text-blue-600 text-2xl"></i>
                                </div>
                                <h4 class="font-semibold text-gray-800 mb-2">Konsultasi Kesehatan</h4>
                                <p class="text-gray-600 text-sm">Dapatkan informasi medis yang akurat dan terpercaya</p>
                            </div>
                            <div class="text-center">
                                <div
                                    class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-history text-green-600 text-2xl"></i>
                                </div>
                                <h4 class="font-semibold text-gray-800 mb-2">Riwayat Medis</h4>
                                <p class="text-gray-600 text-sm">Akses riwayat kesehatan Anda dengan mudah</p>
                            </div>
                        </div>

                        <a href="{{ route('ai-chat') }}"
                            class="inline-flex items-center gap-3 gradient-bg text-white px-8 py-4 rounded-full text-lg font-semibold hover:opacity-90 transition shadow-lg hover:shadow-xl">
                            <i class="fas fa-comments"></i>
                            <span>Mulai Chat Sekarang</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">
                    Fitur <span class="gradient-text">Unggulan</span>
                </h2>
                <p class="text-xl text-gray-600">Dilengkapi dengan fitur-fitur canggih untuk pelayanan kesehatan
                    terbaik
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover">
                    <div class="w-16 h-16 gradient-bg rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-notes-medical text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Rekam Medis Digital</h3>
                    <p class="text-gray-600">
                        Sistem rekam medis elektronik yang aman dan mudah diakses. Mendukung pencatatan lengkap riwayat
                        kesehatan karyawan.
                    </p>
                </div>



                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover">
                    <div
                        class="w-16 h-16 gradient-bg rounded-2xl flex items-center justify-center mb-6 overflow-hidden">
                        <img src="{{ asset('ai.jpeg') }}" alt="AI Avatar" class="w-full h-full object-cover relative">
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">AI Assistant</h3>
                    <p class="text-gray-600">
                        Chat dengan AI assistant powered by Gemini untuk informasi kesehatan, panduan penggunaan sistem,
                        dan bantuan cepat.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover">
                    <div class="w-16 h-16 gradient-bg rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-pills text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Manajemen Obat</h3>
                    <p class="text-gray-600">
                        Kelola stok obat dengan efisien. Tracking otomatis, alert stok menipis, dan riwayat penggunaan
                        obat yang terperinci.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover">
                    <div class="w-16 h-16 gradient-bg rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Sistem Sidik Jari</h3>
                    <p class="text-gray-600">
                        Sistem identifikasi biometrik untuk verifikasi karyawan yang aman, cepat, dan akurat dengan
                        tingkat keandalan tinggi.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover">
                    <div class="w-16 h-16 gradient-bg rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Laporan & Analitik</h3>
                    <p class="text-gray-600">
                        Dashboard analitik komprehensif dengan visualisasi data kesehatan, tren penyakit, dan laporan
                        keuangan.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover">
                    <div class="w-16 h-16 gradient-bg rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Keamanan Data</h3>
                    <p class="text-gray-600">
                        Sistem keamanan berlapis dengan enkripsi data, role-based access control, dan audit trail
                        lengkap.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Section untuk 3 Post Terbaru --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">
                    <span class="gradient-text">Artikel</span> & Berita Terbaru
                </h2>
                <p class="text-xl text-gray-600">Informasi kesehatan dan berita terkini dari SIPO</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @php
                $latestPosts = \App\Models\Post::latest()->take(3)->get();
                @endphp

                @forelse($latestPosts as $post)
                <article class="bg-white rounded-2xl shadow-lg overflow-hidden card-hover border border-gray-100">
                    @if($post->image_path)
                    <div class="h-48 overflow-hidden">
                        <img src="{{ asset('storage/' . $post->image_path) }}" alt="{{ $post->title }}"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                    </div>
                    @else
                    <div class="h-48 bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    @endif

                    <div class="p-6">
                        <div class="flex items-center text-sm text-gray-500 mb-3">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $post->created_at->format('d M Y') }}
                        </div>

                        <h3
                            class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 hover:text-purple-600 transition-colors">
                            {{ $post->title }}
                        </h3>

                        <div class="text-gray-600 text-sm line-clamp-3 mb-4">
                            {!! Str::limit(strip_tags($post->body), 150) !!}
                        </div>

                        <a href="{{ route('landing.posts.show', $post) }}"
                            class="inline-flex items-center text-purple-600 hover:text-purple-700 font-medium transition-colors">
                            <span>Baca Selengkapnya</span>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </article>
                @empty
                <div class="md:col-span-3 text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada artikel</h3>
                    <p class="text-gray-500">Artikel terbaru akan segera ditampilkan di sini</p>
                </div>
                @endforelse
            </div>

            @if($latestPosts->count() > 0)
            <div class="text-center mt-12">
                <a href="{{ route('landing.posts.index') }}"
                    class="inline-flex items-center gap-3 gradient-bg text-white px-8 py-4 rounded-full text-lg font-semibold hover:opacity-90 transition shadow-lg hover:shadow-xl">
                    <span>Lihat Semua Artikel</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            @endif
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="px-5 py-12 md:px-14 md:py-32">
                    <img src="{{ asset('about.webp') }}" alt="About" class="w-full rounded-b-full">
                </div>
                <div>
                    <h2 class="text-4xl font-bold text-gray-800 mb-6">
                        Tentang <span class="gradient-text">SIPO</span>
                    </h2>
                    <p class="text-gray-600 mb-6 text-lg leading-relaxed">
                        SIPO adalah sistem informasi pelayanan kesehatan yang dirancang khusus untuk memenuhi
                        kebutuhan pengelolaan kesehatan karyawan di lingkungan.
                    </p>
                    <p class="text-gray-600 mb-6 text-lg leading-relaxed">
                        Dengan mengintegrasikan teknologi modern seperti AI-powered chatbot, kami
                        berkomitmen untuk memberikan pelayanan kesehatan yang lebih baik, cepat, dan efisien.
                    </p>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="text-center p-6 bg-purple-50 rounded-xl">
                            <div class="text-4xl font-bold gradient-text mb-2">500+</div>
                            <div class="text-gray-600">Karyawan Terdaftar</div>
                        </div>
                        <div class="text-center p-6 bg-purple-50 rounded-xl">
                            <div class="text-4xl font-bold gradient-text mb-2">1500+</div>
                            <div class="text-gray-600">Rekam Medis</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20 gradient-bg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 text-center text-white">
                <div>
                    <i class="fas fa-clock text-5xl mb-4"></i>
                    <div class="text-4xl font-bold mb-2">24 Jam</div>
                    <div class="text-purple-100">Akses Sistem</div>
                </div>
                <div>
                    <i class="fas fa-file-medical text-5xl mb-4"></i>
                    <div class="text-4xl font-bold mb-2">1500+</div>
                    <div class="text-purple-100">Rekam Medis</div>
                </div>
                <div>
                    <i class="fas fa-shield-alt text-5xl mb-4"></i>
                    <div class="text-4xl font-bold mb-2">Terlindungi</div>
                    <div class="text-purple-100">Data Terenkripsi</div>
                </div>
                <div>
                    <i class="fas fa-users text-5xl mb-4"></i>
                    <div class="text-4xl font-bold mb-2">500+</div>
                    <div class="text-purple-100">Karyawan Terdaftar</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">
                    Hubungi <span class="gradient-text">Kami</span>
                </h2>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <!-- Telepon & Komunikasi -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover">
                    <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-phone-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Telepon & Komunikasi</h3>
                    <div class="space-y-3 text-left">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-phone text-purple-600 mt-1"></i>
                            <div>
                                <p class="font-semibold text-gray-700">Telepon</p>
                                <p class="text-gray-600">+0511 4787 981</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fab fa-whatsapp text-purple-600 mt-1"></i>
                            <div>
                                <p class="font-semibold text-gray-700">WhatsApp</p>
                                <p class="text-gray-600">+6281293222772 / +6281349052799 / +6285248828285</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email & Alamat -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover">
                    <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-envelope text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Email & Alamat</h3>
                    <div class="space-y-4 text-left">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-envelope text-purple-600 mt-1"></i>
                            <div>
                                <p class="font-semibold text-gray-700">Email</p>
                                <a href="mailto:noodle.bjm@gmail.com"
                                    class="text-purple-600 hover:text-purple-700 transition">
                                    noodle.bjm@gmail.com
                                </a>
                                <br>
                                <a href="mailto:noodle.banjarmasin@gmail.com"
                                    class="text-purple-600 hover:text-purple-700 transition">
                                    noodle.banjarmasin@gmail.com
                                </a>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt text-purple-600 mt-1"></i>
                            <div>
                                <p class="font-semibold text-gray-700">Alamat</p>
                                <p class="text-gray-600">Jalan Ayani KM. 32 Liang Anggang, Pandahan, Kec. Bati Bati,
                                    Kabupaten Tanah Laut, Kalimantan Selatan - 70852</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="mb-5">
                        <img src="{{ asset('logo.png') }}" alt="SIPO Logo" class=" bg-white p-5 w-48 rounded-lg">
                    </div>
                    <p class="text-gray-400">
                        SIPO adalah solusi digital terpadu untuk manajemen pelayanan
                        kesehatan
                        karyawan yang efisien, modern, dan terintegrasi dengan teknologi AI.
                    </p>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Menu</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#home" class="hover:text-purple-400 transition">Beranda</a></li>
                        <li><a href="{{ route('ai-chat') }}" class="hover:text-purple-400 transition">AI Chat</a>
                        </li>
                        <li><a href="#features" class="hover:text-purple-400 transition">Fitur</a></li>
                        <li><a href="#about" class="hover:text-purple-400 transition">Tentang</a></li>
                        <li><a href="#contact" class="hover:text-purple-400 transition">Kontak</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Legal</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-purple-400 transition">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-purple-400 transition">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-purple-400 transition">Cookie Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Ikuti Kami</h4>
                    <div class="flex gap-4">
                        <a href="#"
                            class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-purple-600 transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-purple-600 transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-purple-600 transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-purple-600 transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} SIPO - All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden'); // Toggle icon between bars and times
                const icon = this.querySelector('i');
                if (icon) {
                    if (mobileMenu.classList.contains('hidden')) {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    } else {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-times');
                    }
                }
            });
        }

        // Close mobile menu when clicking on links
        document.querySelectorAll('#mobileMenu a').forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.add('hidden');
                const icon = mobileMenuButton.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (mobileMenuButton && mobileMenu &&
                !mobileMenuButton.contains(event.target) &&
                !mobileMenu.contains(event.target)) {
                mobileMenu.classList.add('hidden');
                const icon = mobileMenuButton.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });

        // Smooth Scroll for Navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- Initialize Swiper -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const swiper = new Swiper('.hero-swiper', {
                // Optional parameters
                direction: 'horizontal',
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },

                // If we need pagination
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                    dynamicBullets: true,
                },

                // Navigation arrows
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },

                // And if we need scrollbar
                scrollbar: {
                    el: '.swiper-scrollbar',
                },

                // Enable keyboard navigation
                keyboard: {
                    enabled: true,
                    onlyInViewport: false,
                },

                // Enable mousewheel control
                mousewheel: {
                    invert: false,
                },

                // Add transition effects
                speed: 1000,

                // Reset animation on slide change
                on: {
                    slideChange: function() {
                        // Reset animations
                        const animatedElements = document.querySelectorAll('.hero-animate');
                        animatedElements.forEach(el => {
                            el.style.animation = 'none';
                            el.offsetHeight; // Trigger reflow
                            el.style.animation = null;
                        });
                    },
                    init: function() {
                        // Trigger initial animations
                        setTimeout(() => {
                            const animatedElements = document.querySelectorAll('.hero-animate');
                            animatedElements.forEach(el => {
                                el.classList.add('hero-animate');
                            });
                        }, 100);
                    }
                }
            });
        });
    </script>
</body>

</html>