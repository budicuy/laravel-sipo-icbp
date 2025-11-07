<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIPO ICBP - Sistem Informasi Pelayanan Kesehatan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        #chatMessages .message.user .bg-gradient-to-r {
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
    </style>
    @vite('resources/css/app.css')
</head>

<body class="scroll-smooth">
    <!-- Navbar -->
    <nav class="fixed w-full bg-white/90 backdrop-blur-md shadow-sm z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <img src="{{ asset('logo.png') }}" alt="SIPO ICBP Logo" class="h-10">
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-gray-700 hover:text-purple-600 transition">Beranda</a>
                    <a href="#ai-chat" class="text-gray-700 hover:text-purple-600 transition">AI Chat</a>
                    <a href="#features" class="text-gray-700 hover:text-purple-600 transition">Fitur</a>
                    <a href="#about" class="text-gray-700 hover:text-purple-600 transition">Tentang</a>
                    <a href="#contact" class="text-gray-700 hover:text-purple-600 transition">Kontak</a>
                    <a href="{{ route('login') }}"
                        class="gradient-bg text-white px-6 py-2 rounded-full hover:opacity-90 transition">
                        Login
                    </a>
                </div>
                <button class="md:hidden text-gray-700">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="relative overflow-hidden bg-purple-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
            <div class="grid md:grid-cols-2 gap-8 items-center h-full">
                <div class="text-white z-10 py-32">
                    <h1 class="text-5xl font-bold mb-6 leading-tight">
                        Sistem Informasi Pelayanan Kesehatan
                        <span class="block text-yellow-300">ICBP</span>
                    </h1>
                    <p class="text-xl mb-8 text-purple-100">
                        Solusi digital terpadu untuk manajemen pelayanan kesehatan karyawan yang efisien, modern, dan
                        terintegrasi dengan teknologi AI.
                    </p>
                    <div class="flex gap-4">
                        <a href="{{ route('login') }}"
                            class="bg-white text-purple-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition">
                            Mulai Sekarang
                        </a>
                        <a href="#features"
                            class="border-2 border-white text-white px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-purple-600 transition">
                            Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>
                <div class="hidden md:block relative h-full">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-purple-500 via-purple-500/40 to-transparent z-10">
                    </div>
                    <img src="{{ asset('banner.webp') }}" alt="Healthcare"
                        class="absolute right-0 top-0 h-full w-full object-cover object-center rounded-l-3xl">
                </div>
            </div>
        </div>
    </section>

    <!-- AI Chat Section -->
    <section id="ai-chat" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">
                    <span class="gradient-text">AI Assistant</span> SIPO ICBP
                </h2>
                <p class="text-xl text-gray-600">Tanyakan apapun tentang sistem kami, kami siap membantu Anda 24/7</p>
            </div>

            <div class="max-w-5xl mx-auto">
                <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-3xl shadow-2xl overflow-hidden">
                    <!-- Chat Header -->
                    <div class="gradient-bg p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center">
                                <i class="fas fa-robot text-purple-600 text-2xl"></i>
                            </div>
                            <div class="text-white flex-1">
                                <h3 class="text-xl font-bold">AI Assistant</h3>
                                <p class="text-purple-100 text-sm" id="chatSubtitle">Powered by Google Gemini</p>
                            </div>
                            <div class="ml-auto flex items-center gap-3">
                                <!-- Lock Icon & Login Button (shown when NOT logged in) -->
                                <div id="loginPrompt" class="hidden items-center gap-3">
                                    <span
                                        class="inline-flex items-center gap-2 bg-red-500/20 px-4 py-2 rounded-full text-white text-sm">
                                        <i class="fas fa-lock"></i>
                                        <span class="hidden sm:inline">Chat Terkunci</span>
                                    </span>
                                    <button onclick="showLoginModal()"
                                        class="bg-white text-purple-600 px-4 py-2 rounded-full hover:bg-purple-50 transition font-semibold flex items-center gap-2">
                                        <i class="fas fa-sign-in-alt"></i>
                                        <span>Login</span>
                                    </button>
                                </div>

                                <!-- User Info (shown when logged in) -->
                                <div id="userInfo"
                                    class="hidden items-center gap-2 bg-white/20 px-4 py-2 rounded-full text-white text-sm">
                                    <i class="fas fa-user"></i>
                                    <span id="userNik"></span>
                                </div>

                                <!-- Logout Button (shown when logged in) -->
                                <button id="logoutBtn" onclick="logout()"
                                    class="hidden bg-white/20 hover:bg-white/30 px-4 py-2 rounded-full text-white text-sm transition items-center gap-2"
                                    title="Logout">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span class="hidden sm:inline">Logout</span>
                                </button>

                                <button onclick="clearChatHistory()"
                                    class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-full text-white text-sm transition flex items-center gap-2"
                                    title="Hapus Riwayat Chat">
                                    <i class="fas fa-trash-alt"></i>
                                    <span class="hidden sm:inline">Hapus Riwayat</span>
                                </button>
                                <span
                                    class="inline-flex items-center gap-2 bg-white/20 px-4 py-2 rounded-full text-white text-sm">
                                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                                    Online
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Messages -->
                    <div class="bg-white p-6 h-96 overflow-y-auto" id="chatMessages">
                        <div class="message bot">
                            <div class="flex gap-3 mb-4">
                                <div
                                    class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-robot text-white"></i>
                                </div>
                                <div
                                    class="bg-gradient-to-r from-purple-100 to-blue-100 rounded-2xl rounded-tl-none px-5 py-3 max-w-2xl">
                                    <div class="text-gray-800 prose prose-sm max-w-none">
                                        <p>👋 <strong>Halo!</strong> Saya AI Assistant SIPO ICBP.</p>
                                        <p>Saya siap membantu menjawab pertanyaan Anda tentang:</p>
                                        <ul class="list-disc list-inside space-y-1 my-2">
                                            <li>Sistem informasi pelayanan kesehatan</li>
                                            <li>Fitur-fitur yang tersedia</li>
                                            <li>Cara penggunaan sistem</li>
                                            <li>Dan informasi umum lainnya</li>
                                        </ul>
                                        <p>Ada yang bisa saya bantu? 😊</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Input -->
                    <div class="bg-gray-50 p-6 border-t border-gray-200">
                        <form onsubmit="sendMessage(event)" class="flex gap-3">
                            <input type="text" id="chatInput" placeholder="Ketik pertanyaan Anda di sini..."
                                class="flex-1 px-6 py-4 border-2 border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-lg"
                                autocomplete="off">
                            <button type="submit" id="sendButton"
                                class="gradient-bg text-white px-8 py-4 rounded-full hover:opacity-90 transition font-semibold flex items-center gap-2 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-paper-plane"></i>
                                <span>Kirim</span>
                            </button>
                        </form>
                        <p class="text-xs text-gray-500 mt-3 text-center">
                            <i class="fas fa-brain"></i> AI ini memiliki memori percakapan dan dapat mengingat konteks
                            chat sebelumnya
                        </p>
                        <p class="text-xs text-gray-400 mt-1 text-center">
                            <i class="fas fa-info-circle"></i> Dapat menjawab pertanyaan tentang SIPO ICBP,
                            fitur-fitur,
                            dan informasi umum
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Modal -->
    <div id="loginModal" class="fixed inset-0 bg-black/25 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
            <div class="gradient-bg text-white p-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-lock text-2xl"></i>
                        <h3 class="text-2xl font-bold">Login AI Chat</h3>
                    </div>
                    <button onclick="closeLoginModal()" class="hover:bg-white/20 p-2 rounded-full transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <p class="text-purple-100 mt-2">Silakan login dengan NIK Anda</p>
            </div>

            <form id="loginForm" class="p-6 space-y-4">
                <div id="loginError" class="hidden bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                        <p class="text-red-700 text-sm font-medium" id="loginErrorMessage"></p>
                    </div>
                </div>

                <div>
                    <label for="loginNik" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user"></i> NIK
                    </label>
                    <input type="text" id="loginNik" name="nik" placeholder="Contoh: 1231231"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        required>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle"></i> Masukkan NIK Anda (hanya angka)
                    </p>
                </div>

                <div>
                    <label for="loginPassword" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-key"></i> Password
                    </label>
                    <input type="password" id="loginPassword" name="password" placeholder="NIK Anda"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        required>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle"></i> Password sama dengan NIK Anda
                    </p>
                </div>

                <button type="submit"
                    class="w-full gradient-bg text-white py-3 rounded-lg hover:opacity-90 transition font-semibold flex items-center justify-center gap-2 shadow-lg hover:shadow-xl">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login</span>
                </button>

                <p class="text-xs text-gray-500 text-center mt-4">
                    <i class="fas fa-shield-alt"></i> Data Anda aman dan terenkripsi
                </p>
            </form>
        </div>
    </div>

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
                    <div class="w-16 h-16 gradient-bg rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-pills text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Manajemen Obat</h3>
                    <p class="text-gray-600">
                        Kelola stok obat dengan efisien. Tracking otomatis, alert stok menipis, dan riwayat penggunaan
                        obat yang terperinci.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover">
                    <div class="w-16 h-16 gradient-bg rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-robot text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">AI Assistant</h3>
                    <p class="text-gray-600">
                        Chat dengan AI assistant powered by Gemini untuk informasi kesehatan, panduan penggunaan sistem,
                        dan bantuan cepat.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover">
                    <div class="w-16 h-16 gradient-bg rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-file-medical-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Surat Keterangan</h3>
                    <p class="text-gray-600">
                        Generate surat keterangan sakit, surat rujukan, dan dokumen medis lainnya secara otomatis dan
                        terstandar.
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

    <!-- About Section -->
    <section id="about" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="px-5 py-12 md:px-14 md:py-32">
                    <img src="{{ asset('about.webp') }}" alt="About" class="w-full rounded-b-full">
                </div>
                <div>
                    <h2 class="text-4xl font-bold text-gray-800 mb-6">
                        Tentang <span class="gradient-text">SIPO ICBP</span>
                    </h2>
                    <p class="text-gray-600 mb-6 text-lg leading-relaxed">
                        SIPO ICBP adalah sistem informasi pelayanan kesehatan yang dirancang khusus untuk memenuhi
                        kebutuhan pengelolaan kesehatan karyawan di lingkungan ICBP.
                    </p>
                    <p class="text-gray-600 mb-6 text-lg leading-relaxed">
                        Dengan mengintegrasikan teknologi modern seperti AI-powered chatbot dan cloud computing, kami
                        berkomitmen untuk memberikan pelayanan kesehatan yang lebih baik, cepat, dan efisien.
                    </p>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="text-center p-6 bg-purple-50 rounded-xl">
                            <div class="text-4xl font-bold gradient-text mb-2">5000+</div>
                            <div class="text-gray-600">Karyawan Terdaftar</div>
                        </div>
                        <div class="text-center p-6 bg-purple-50 rounded-xl">
                            <div class="text-4xl font-bold gradient-text mb-2">10000+</div>
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
                    <i class="fas fa-users text-5xl mb-4"></i>
                    <div class="text-4xl font-bold mb-2">99%</div>
                    <div class="text-purple-100">Kepuasan Pengguna</div>
                </div>
                <div>
                    <i class="fas fa-clock text-5xl mb-4"></i>
                    <div class="text-4xl font-bold mb-2">24/7</div>
                    <div class="text-purple-100">Akses Sistem</div>
                </div>
                <div>
                    <i class="fas fa-hospital text-5xl mb-4"></i>
                    <div class="text-4xl font-bold mb-2">15+</div>
                    <div class="text-purple-100">Klinik Terintegrasi</div>
                </div>
                <div>
                    <i class="fas fa-award text-5xl mb-4"></i>
                    <div class="text-4xl font-bold mb-2">ISO</div>
                    <div class="text-purple-100">Certified System</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">
                    Hubungi <span class="gradient-text">Kami</span>
                </h2>
                <p class="text-xl text-gray-600">Kami siap membantu Anda 24/7</p>
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
                                <p class="text-gray-600">(+62-21) 5795 8822</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fas fa-fax text-purple-600 mt-1"></i>
                            <div>
                                <p class="font-semibold text-gray-700">Fax</p>
                                <p class="text-gray-600">(+62-21) 5793 5960</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fas fa-headset text-purple-600 mt-1"></i>
                            <div>
                                <p class="font-semibold text-gray-700">Call Center</p>
                                <p class="text-gray-600">+62 800 1122 888</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fab fa-whatsapp text-purple-600 mt-1"></i>
                            <div>
                                <p class="font-semibold text-gray-700">WhatsApp</p>
                                <p class="text-gray-600">+62 889 1122 888</p>
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
                                <a href="mailto:corporate@indofood.co.id"
                                    class="text-purple-600 hover:text-purple-700 transition">
                                    corporate@indofood.co.id
                                </a>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt text-purple-600 mt-1"></i>
                            <div>
                                <p class="font-semibold text-gray-700">Alamat</p>
                                <p class="text-gray-600">Jalan Ayani KM. 32 Liang Anggang, Pandahan, Kec. Bati Bati,
                                    Kabupaten Tanah Laut, Kalimantan Selatan 70723</p>
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
                        <img src="{{ asset('logo.png') }}" alt="SIPO ICBP Logo"
                            class=" bg-white p-5 w-48 rounded-lg">
                    </div>
                    <p class="text-gray-400">
                        Sistem Informasi Pelayanan Kesehatan untuk ICBP
                    </p>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Menu</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#home" class="hover:text-purple-400 transition">Beranda</a></li>
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
                <p>&copy; {{ date('Y') }} SIPO ICBP. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Authentication System
        let isAuthenticated = false;
        let currentUserNik = '';
        let currentUserName = '';
        let currentUserDepartemen = '';

        // Chat history storage
        let chatHistory = [];

        // Main initialization on page load
        window.addEventListener('DOMContentLoaded', function() {
            // Check authentication first
            checkAuthentication();

            // Load chat history after checking auth
            const savedHistory = localStorage.getItem('sipo_chat_history');
            if (savedHistory && isAuthenticated) {
                try {
                    chatHistory = JSON.parse(savedHistory);
                    // Restore messages in chat UI
                    chatHistory.forEach(msg => {
                        addMessageToUI(msg.role === 'user' ? 'user' : 'bot', msg.text);
                    });
                } catch (e) {
                    console.error('Error loading chat history:', e);
                    chatHistory = [];
                }
            }

            // Setup login form handler
            const loginForm = document.getElementById('loginForm');
            if (loginForm) {
                loginForm.addEventListener('submit', handleLogin);
            }
        }); // Check if user is authenticated
        function checkAuthentication() {
            const authData = localStorage.getItem('sipo_auth');
            if (authData) {
                try {
                    const auth = JSON.parse(authData);
                    if (auth.nik && auth.nama && auth.timestamp) {
                        // Check if session is still valid (24 hours)
                        const now = Date.now();
                        const sessionDuration = 24 * 60 * 60 * 1000; // 24 hours
                        if (now - auth.timestamp < sessionDuration) {
                            isAuthenticated = true;
                            currentUserNik = auth.nik;
                            currentUserName = auth.nama;
                            currentUserDepartemen = auth.departemen || '';
                            updateAuthUI();
                            return;
                        }
                    }
                } catch (e) {
                    console.error('Error checking authentication:', e);
                }
            }
            // Not authenticated
            isAuthenticated = false;
            currentUserNik = '';
            currentUserName = '';
            currentUserDepartemen = '';
            updateAuthUI();
        }

        // Update UI based on authentication status
        function updateAuthUI() {
            const loginPrompt = document.getElementById('loginPrompt');
            const userInfo = document.getElementById('userInfo');
            const userNikSpan = document.getElementById('userNik');
            const logoutBtn = document.getElementById('logoutBtn');
            const chatInput = document.getElementById('chatInput');
            const sendButton = document.getElementById('sendButton');
            const chatSubtitle = document.getElementById('chatSubtitle');

            if (isAuthenticated) {
                // Hide login prompt
                loginPrompt.classList.add('hidden');
                loginPrompt.classList.remove('flex');
                // Show user info
                userInfo.classList.remove('hidden');
                userInfo.classList.add('flex');
                userNikSpan.innerHTML =
                    `<strong>${currentUserName}</strong> <span class="text-purple-200 text-xs">(${currentUserNik})</span>`;
                // Show logout button
                logoutBtn.classList.remove('hidden');
                logoutBtn.classList.add('flex');
                // Enable chat input and button
                chatInput.disabled = false;
                chatInput.placeholder = 'Ketik pertanyaan Anda di sini...';
                sendButton.disabled = false;
                // Update chat subtitle with user name
                chatSubtitle.innerHTML = `Melayani <strong>${currentUserName}</strong> | ${currentUserDepartemen}`;
            } else {
                // Show login prompt
                loginPrompt.classList.remove('hidden');
                loginPrompt.classList.add('flex');
                // Hide user info
                userInfo.classList.add('hidden');
                userInfo.classList.remove('flex');
                // Hide logout button
                logoutBtn.classList.add('hidden');
                logoutBtn.classList.remove('flex');
                // Disable chat input and button
                chatInput.disabled = true;
                chatInput.placeholder = 'Silakan login terlebih dahulu untuk menggunakan AI Chat...';
                sendButton.disabled = true;
                // Reset chat subtitle
                chatSubtitle.textContent = 'Powered by Google Gemini';
            }
        }

        // Show login modal
        function showLoginModal() {
            const modal = document.getElementById('loginModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            // Clear previous errors
            hideLoginError();
        }

        // Close login modal
        function closeLoginModal() {
            const modal = document.getElementById('loginModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            // Clear form
            document.getElementById('loginForm').reset();
            hideLoginError();
        }

        // Show login error
        function showLoginError(message) {
            const errorDiv = document.getElementById('loginError');
            const errorMessage = document.getElementById('loginErrorMessage');
            errorMessage.textContent = message;
            errorDiv.classList.remove('hidden');
        }

        // Hide login error
        function hideLoginError() {
            const errorDiv = document.getElementById('loginError');
            errorDiv.classList.add('hidden');
        }

        // Handle login form submission
        async function handleLogin(e) {
            e.preventDefault();

            const nik = document.getElementById('loginNik').value.trim();
            const password = document.getElementById('loginPassword').value.trim();

            // Validate NIK format (harus angka)
            if (!/^\d+$/.test(nik)) {
                showLoginError('NIK harus berupa angka saja');
                return;
            }

            // Validate password matches NIK
            if (password !== nik) {
                showLoginError('Password harus sama dengan NIK Anda');
                return;
            }

            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Memverifikasi...</span>';

            try {
                // Call API to check NIK
                const response = await fetch('/api/auth/check-nik', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        nik,
                        password
                    })
                });

                const result = await response.json();

                if (result.success) {
                    // Login successful
                    const authData = {
                        nik: result.data.nik,
                        nama: result.data.nama,
                        departemen: result.data.departemen,
                        timestamp: Date.now()
                    };
                    localStorage.setItem('sipo_auth', JSON.stringify(authData));

                    isAuthenticated = true;
                    currentUserNik = result.data.nik;
                    currentUserName = result.data.nama;
                    currentUserDepartemen = result.data.departemen;

                    // Update UI
                    updateAuthUI();
                    closeLoginModal();

                    // Show success message in chat
                    addMessageToUI('bot',
                        `<p>Selamat datang, <strong>${result.data.nama}</strong>! 👋</p><p>NIK: ${result.data.nik} | Departemen: ${result.data.departemen}</p><p>Anda telah berhasil login. Silakan tanyakan apapun tentang SIPO ICBP.</p>`
                    );
                } else {
                    showLoginError(result.message || 'Login gagal');
                }
            } catch (error) {
                console.error('Login error:', error);
                showLoginError('Terjadi kesalahan saat login. Silakan coba lagi.');
            } finally {
                // Restore button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }

        // Logout function
        function logout() {
            if (confirm('Apakah Anda yakin ingin logout?')) {
                // Clear authentication
                localStorage.removeItem('sipo_auth');
                isAuthenticated = false;
                currentUserNik = '';
                currentUserName = '';
                currentUserDepartemen = '';

                // Clear chat history
                clearChatHistory();

                // Update UI
                updateAuthUI();

                // Show login modal
                showLoginModal();
            }
        }

        // Save chat history to localStorage
        function saveChatHistory() {
            try {
                // Keep only last 20 messages to avoid token limits
                if (chatHistory.length > 20) {
                    chatHistory = chatHistory.slice(-20);
                }
                localStorage.setItem('sipo_chat_history', JSON.stringify(chatHistory));
            } catch (e) {
                console.error('Error saving chat history:', e);
            }
        }

        // Clear chat history function
        function clearChatHistory() {
            chatHistory = [];
            localStorage.removeItem('sipo_chat_history');
            const messagesContainer = document.getElementById('chatMessages');
            messagesContainer.innerHTML = `
                <div class="message bot">
                    <div class="flex gap-3 mb-4">
                        <div class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-robot text-white"></i>
                        </div>
                        <div class="bg-gradient-to-r from-purple-100 to-blue-100 rounded-2xl rounded-tl-none px-5 py-3 max-w-2xl">
                            <div class="text-gray-800 prose prose-sm max-w-none">
                                <p>👋 <strong>Halo!</strong> Saya AI Assistant SIPO ICBP.</p>
                                <p>Saya siap membantu menjawab pertanyaan Anda tentang:</p>
                                <ul class="list-disc list-inside space-y-1 my-2">
                                    <li>Sistem informasi pelayanan kesehatan</li>
                                    <li>Fitur-fitur yang tersedia</li>
                                    <li>Cara penggunaan sistem</li>
                                    <li>Dan informasi umum lainnya</li>
                                </ul>
                                <p>Ada yang bisa saya bantu? 😊</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Send Message Function
        function sendMessage(event) {
            event.preventDefault();

            // Check authentication first
            if (!isAuthenticated) {
                showLoginModal();
                return;
            }

            const input = document.getElementById('chatInput');
            const message = input.value.trim();

            if (message === '') return;

            // Add user message to chat history
            chatHistory.push({
                role: 'user',
                text: message
            });

            // Add user message to chat UI
            // Add user message to chat UI
            addMessage('user', message);

            // Clear input
            input.value = '';

            // Show typing indicator
            showTypingIndicator();

            // Prepare history for API (exclude current message, keep last 10 exchanges)
            const historyForAPI = chatHistory.slice(0, -1).slice(-20);

            // Send to Gemini API via backend
            fetch('{{ route('api.chat') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        message: message,
                        history: historyForAPI,
                        user_nik: currentUserNik,
                        user_name: currentUserName
                    })
                })
                .then(response => response.json())
                .then(data => {
                    hideTypingIndicator();
                    if (data.success) {
                        // Add bot response to history
                        chatHistory.push({
                            role: 'model',
                            text: data.reply
                        });
                        saveChatHistory();
                        addMessage('bot', data.reply);
                    } else {
                        addMessage('bot', data.reply || 'Maaf, terjadi kesalahan. Silakan coba lagi.');
                    }
                })
                .catch(error => {
                    hideTypingIndicator();
                    console.error('Error:', error);
                    addMessage('bot', 'Maaf, koneksi bermasalah. Silakan periksa koneksi internet Anda dan coba lagi.');
                });
        }

        // Sanitize and prepare AI HTML response
        function sanitizeAIResponse(html) {
            // Create a temporary div to parse HTML
            const temp = document.createElement('div');
            temp.innerHTML = html;

            // Allowed tags for security
            const allowedTags = ['P', 'BR', 'STRONG', 'EM', 'UL', 'OL', 'LI', 'H1', 'H2', 'H3', 'SPAN', 'DIV'];

            // Remove any script tags or dangerous content
            const scripts = temp.querySelectorAll('script, iframe, object, embed');
            scripts.forEach(script => script.remove());

            // Get all elements and check if they're allowed
            const allElements = temp.getElementsByTagName('*');
            for (let i = allElements.length - 1; i >= 0; i--) {
                const element = allElements[i];
                if (!allowedTags.includes(element.tagName)) {
                    // Replace disallowed tags with their text content
                    const textNode = document.createTextNode(element.textContent);
                    element.parentNode.replaceChild(textNode, element);
                }
            }

            return temp.innerHTML;
        }

        // Add Message to Chat (UI only, doesn't affect history)
        function addMessage(sender, text) {
            addMessageToUI(sender, text);
        }

        // Add Message to UI
        function addMessageToUI(sender, text) {
            const messagesContainer = document.getElementById('chatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;

            if (sender === 'bot') {
                // Sanitize AI HTML response
                const sanitizedHTML = sanitizeAIResponse(text);

                messageDiv.innerHTML = `
                    <div class="flex gap-3 mb-4">
                        <div class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-robot text-white"></i>
                        </div>
                        <div class="bg-gradient-to-r from-purple-100 to-blue-100 rounded-2xl rounded-tl-none px-5 py-3 max-w-2xl">
                            <div class="text-gray-800 prose prose-sm max-w-none">${sanitizedHTML}</div>
                        </div>
                    </div>
                `;
            } else {
                // Escape user input for security
                const escapedText = text.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>');

                messageDiv.innerHTML = `
                    <div class="flex gap-3 mb-4 justify-end">
                        <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl rounded-tr-none px-5 py-3 max-w-2xl">
                            <p class="text-white">${escapedText}</p>
                        </div>
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-gray-600"></i>
                        </div>
                    </div>
                `;
            }

            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Show Typing Indicator
        function showTypingIndicator() {
            const messagesContainer = document.getElementById('chatMessages');
            const typingDiv = document.createElement('div');
            typingDiv.className = 'message bot';
            typingDiv.id = 'typingIndicator';

            typingDiv.innerHTML = `
                <div class="flex gap-3 mb-4">
                    <div class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-robot text-white"></i>
                    </div>
                    <div class="bg-gradient-to-r from-purple-100 to-blue-100 rounded-2xl rounded-tl-none px-5 py-4">
                        <div class="flex gap-1">
                            <div class="w-2 h-2 bg-purple-400 rounded-full animate-bounce"></div>
                            <div class="w-2 h-2 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            <div class="w-2 h-2 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                        </div>
                    </div>
                </div>
            `;

            messagesContainer.appendChild(typingDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Hide Typing Indicator
        function hideTypingIndicator() {
            const typingIndicator = document.getElementById('typingIndicator');
            if (typingIndicator) {
                typingIndicator.remove();
            }
        }

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
</body>

</html>
