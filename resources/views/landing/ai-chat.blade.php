<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Chat - SIPO</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        textarea {
            field-sizing: content;
            min-height: 4rem;
            /* Optional: Set a minimum height */
            resize: none;
            /* Optional: Prevent manual resizing by the user */
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

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .chat-container {
            height: calc(100vh - 80px);
            display: flex;
            flex-direction: column;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            background: #f9fafb;
        }

        .chat-messages::-webkit-scrollbar {
            width: 8px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #667eea;
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
    </style>
    @vite('resources/css/app.css')
</head>

<body class="scroll-smooth">

    <!-- Main Chat Container -->
    <main class="chat-container">
        <div class="min-h-dvh flex flex-col">
            <!-- Chat Header -->
            <div class="gradient-bg p-4 md:p-6 sticky top-0 left-0 w-full z-[9999] shadow-md">
                <!-- Main Header Row -->
                <div class="flex items-center gap-3 mb-3">
                    <!-- Tombol Kembali ke Beranda -->
                    <a href="{{ route('landing') }}"
                        class="w-10 h-10 md:w-12 md:h-12 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center flex-shrink-0 transition-all duration-200 group"
                        title="Kembali ke Beranda">
                        <i
                            class="fas fa-home text-white text-lg md:text-xl group-hover:scale-110 transition-transform"></i>
                    </a>
                    <div
                        class="w-12 h-12 md:w-14 md:h-14 bg-white rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden">
                        <img src="{{ asset('ai.jpeg') }}" alt="AI Avatar" class="w-full h-full object-cover">
                    </div>
                    <div class="text-white flex-1 min-w-0">
                        <h1 class="text-xl md:text-2xl font-bold">AI Assistant SIPO</h1>
                        <p class="text-purple-100 text-sm md:text-base" id="chatSubtitle">Powered by Google Gemini</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <!-- Online Status -->
                        <span
                            class="inline-flex items-center gap-1.5 bg-white/20 px-3 py-1.5 rounded-full text-white text-sm">
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                            <span class="hidden sm:inline">Online</span>
                        </span>
                    </div>
                </div>

                <!-- User Info & Actions Row -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Lock Icon & Login Button (shown when NOT logged in) -->
                    <div id="loginPrompt" class="hidden items-center gap-2 flex-wrap">
                        <span
                            class="inline-flex items-center gap-2 bg-red-500/20 px-3 py-1.5 rounded-full text-white text-sm">
                            <i class="fas fa-lock"></i>
                            <span class="hidden sm:inline">Chat Terkunci</span>
                        </span>
                        <button onclick="showLoginModal()"
                            class="bg-white text-purple-600 px-4 py-1.5 rounded-full hover:bg-purple-50 transition font-semibold flex items-center gap-2 text-sm">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login</span>
                        </button>
                    </div>

                    <!-- User Info (shown when logged in) -->
                    <div id="userInfo"
                        class="hidden items-center gap-2 bg-white/20 px-3 py-1.5 rounded-full text-white text-sm max-w-xs">
                        <i class="fas fa-user flex-shrink-0"></i>
                        <span id="userNik" class="truncate"></span>
                    </div>

                    <!-- Selected Patient Info (shown when patient selected) -->
                    <div id="patientInfo"
                        class="hidden items-center gap-2 bg-blue-500/30 px-3 py-1.5 rounded-full text-white text-sm cursor-pointer hover:bg-blue-500/40 transition max-w-xs"
                        onclick="showPatientSelectionModal()" title="Klik untuk ganti pasien">
                        <i class="fas fa-user-injured flex-shrink-0"></i>
                        <span id="selectedPatientName" class="truncate"></span>
                        <i class="fas fa-exchange-alt text-xs flex-shrink-0"></i>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 ml-auto">


                        <!-- Logout Button (shown when logged in) -->
                        <button id="logoutBtn" onclick="logout()"
                            class="hidden bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-full text-white text-sm transition items-center gap-1.5"
                            title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="hidden lg:inline">Logout</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="bg-white p-4 md:p-6 chat-messages" id="chatMessages">
                <!-- Messages will be dynamically added here -->
            </div>

            <!-- Chat Input -->
            <div class="bg-gray-50 p-4 md:p-6 border-t border-gray-200 sticky bottom-0 left-0 w-full ">
                <form onsubmit="sendMessage(event)" class="space-y-3">

                    <div class="">
                        <textarea id="chatInput" placeholder="Ketik pertanyaan Anda di sini..."
                            class="w-full px-4 md:px-10 min-h-8 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-lg"
                            maxlength="5000" autocomplete="off"></textarea>
                        <div class="flex justify-between items-center px-4 md:px-10 mb-6">
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-info-circle"></i>
                                <span id="charCount">0</span>/5000 karakter
                            </span>
                            <button type="button" onclick="clearInput()"
                                class="text-xs text-gray-500 hover:text-red-500 transition">
                                <i class="fas fa-eraser"></i> Hapus
                            </button>
                        </div>

                        <button type="submit" id="sendButton"
                            class="w-full gradient-bg text-white px-8 py-4 rounded-full hover:opacity-90 transition font-semibold flex items-center justify-center gap-2 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-paper-plane"></i>
                            <span>Kirim</span>
                        </button>
                    </div>
                </form>
                <p class="text-xs text-gray-400 mt-1 text-center">
                    <i class="fas fa-info-circle"></i> Dapat menjawab pertanyaan tentang SIPO,
                    fitur-fitur, dan informasi umum
                </p>
            </div>
        </div>
    </main>

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

    <!-- Patient Selection Modal -->
    <div id="patientSelectionModal"
        class="fixed inset-0 bg-black/25 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
            <div class="gradient-bg text-white p-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-user-friends text-2xl"></i>
                        <h3 class="text-2xl font-bold">Pilih Pasien</h3>
                    </div>
                </div>
                <p class="text-purple-100 mt-2">Pilih pasien untuk konsultasi AI</p>
            </div>

            <div class="p-6">
                <p class="text-sm text-gray-600 mb-4">
                    <i class="fas fa-info-circle text-blue-500"></i> Silakan pilih nama pasien yang ingin
                    berkonsultasi:
                </p>
                <div id="patientListContainer" class="space-y-2 max-h-96 overflow-y-auto">
                    <!-- Patient list will be populated here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Authentication System
        let isAuthenticated = false;
        let currentUserNik = '';
        let currentUserName = '';
        let currentUserDepartemen = '';

        // Chat history storage
        let chatHistory = [];
        let currentPatientIdForHistory = null; // Track which patient the history belongs to

        // Main initialization on page load
        window.addEventListener('DOMContentLoaded', function() {
            // Check authentication first
            checkAuthentication();

            // Don't auto-show login modal - let user click "Login" button instead
            // This provides better UX and doesn't interrupt user on page load

            // Get current selected patient ID
            const authData = JSON.parse(localStorage.getItem('sipo_auth'));
            const selectedPatientId = authData?.selected_patient_id || null;
            currentPatientIdForHistory = selectedPatientId;

            // Load family members if authenticated (needed for patient switching)
            if (isAuthenticated && authData?.nik) {
                loadFamilyMembers(authData.nik).then(success => {
                    if (success) {
                        console.log('✅ Family members loaded on page load');
                    }
                });
            }

            // Load chat history after checking auth - ONLY if for same patient
            const savedHistory = localStorage.getItem('sipo_chat_history');
            const savedPatientId = localStorage.getItem('sipo_chat_patient_id');

            if (savedHistory && isAuthenticated && savedPatientId === String(selectedPatientId)) {
                // History is for same patient, load it
                try {
                    chatHistory = JSON.parse(savedHistory);
                    // Restore messages in chat UI - but SKIP hidden medical context
                    chatHistory.forEach(msg => {
                        // Only show messages that are not hidden (medical context is hidden)
                        if (!msg.isHidden) {
                            addMessageToUI(msg.role === 'user' ? 'user' : 'bot', msg.text);
                        }
                    });
                    console.log('✅ Loaded chat history for patient:', selectedPatientId);
                } catch (e) {
                    console.error('Error loading chat history:', e);
                    chatHistory = [];
                    showInitialWelcomeMessage(); // Show welcome if error
                }
            } else {
                // Different patient or no history - start fresh
                console.log('🔄 Chat history cleared - different patient or no history');
                chatHistory = [];
                localStorage.removeItem('sipo_chat_history');
                localStorage.removeItem('sipo_chat_patient_id');

                // Show appropriate welcome message
                showInitialWelcomeMessage();
            }

            // Setup login form handler
            const loginForm = document.getElementById('loginForm');
            if (loginForm) {
                loginForm.addEventListener('submit', handleLogin);
            }

            // Setup character counter for chat input
            const chatInput = document.getElementById('chatInput');
            if (chatInput) {
                chatInput.addEventListener('input', updateCharCount);
                chatInput.addEventListener('paste', () => {
                    // Update character count after paste with small delay
                    setTimeout(updateCharCount, 10);
                });
                // Initialize character count
                updateCharCount();
            }
        });

        // Show initial welcome message based on authentication and patient selection status
        function showInitialWelcomeMessage() {
            const messagesContainer = document.getElementById('chatMessages');
            const authData = JSON.parse(localStorage.getItem('sipo_auth'));
            const selectedPatientName = authData?.selected_patient_name;

            let welcomeMessage = '';

            if (isAuthenticated && selectedPatientName) {
                // User logged in AND has selected a patient
                welcomeMessage = `
                    <p style="margin-bottom: 12px; color: #374151;">👋 <strong>Halo, ${currentUserName}!</strong></p>
                    <p style="margin-bottom: 12px; color: #374151;">✅ Pasien terpilih: <strong style="color: #7C3AED;">${selectedPatientName}</strong></p>
                    <p style="margin-bottom: 12px; color: #374151;">Silakan tanyakan apapun tentang:</p>
                    <ul style="margin: 12px 0; padding-left: 24px; list-style: disc;">
                        <li>Riwayat kesehatan pasien</li>
                        <li>Informasi tentang SIPO</li>
                        <li>Fitur-fitur sistem</li>
                        <li>Konsultasi kesehatan umum</li>
                    </ul>
                    <p style="margin-top: 12px; color: #374151;">Ada yang bisa saya bantu? 😊</p>
                `;
            } else if (isAuthenticated && !selectedPatientName) {
                // User logged in but NO patient selected yet
                welcomeMessage = `
                    <p style="margin-bottom: 12px; color: #374151;">👋 <strong>Halo, ${currentUserName}!</strong></p>
                    <p style="margin-bottom: 12px; color: #374151;">🔹 Untuk konsultasi tentang riwayat kesehatan, silakan <strong>pilih pasien</strong> terlebih dahulu dengan klik icon di header.</p>
                    <p style="margin-bottom: 12px; color: #374151;">Atau Anda tetap bisa bertanya tentang:</p>
                    <ul style="margin: 12px 0; padding-left: 24px; list-style: disc;">
                        <li>Sistem SIPO</li>
                        <li>Fitur-fitur yang tersedia</li>
                        <li>Informasi kesehatan umum</li>
                    </ul>
                    <p style="margin-top: 12px; color: #374151;">Ada yang bisa saya bantu? 😊</p>
                `;
            } else {
                // User NOT logged in
                welcomeMessage = `
                    <p style="margin-bottom: 12px; color: #374151;">👋 <strong>Halo!</strong> Saya AI Assistant SIPO.</p>
                    <p style="margin-bottom: 12px; color: #374151;">🔒 Untuk menggunakan fitur chat, silakan <strong>login terlebih dahulu</strong> dengan NIK Anda.</p>
                    <p style="margin-bottom: 12px; color: #374151;">Setelah login, Anda dapat:</p>
                    <ul style="margin: 12px 0; padding-left: 24px; list-style: disc;">
                        <li>📋 Melihat riwayat kunjungan medis</li>
                        <li>💊 Konsultasi tentang obat</li>
                        <li>📊 Mendapatkan analisis kesehatan</li>
                        <li>❓ Bertanya tentang sistem SIPO</li>
                    </ul>
                    <p style="margin-top: 12px; color: #374151;"><strong>Klik tombol "Login"</strong> di atas untuk memulai! 🚀</p>
                `;
            }

            messagesContainer.innerHTML = `
                <div class="message bot">
                    <div class="flex gap-3 mb-4">
                        <div class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden">
                            <img src="{{ asset('ai.jpeg') }}" alt="AI Avatar" class="w-full h-full object-cover">
                        </div>
                        <div class="bg-linear-to-r from-purple-100 to-blue-100 rounded-2xl rounded-tl-none px-5 py-3 max-w-5xl">
                            <div class="text-gray-800 prose prose-sm max-w-none">
                                ${welcomeMessage}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Check if user is authenticated
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
            // Show appropriate welcome message for non-authenticated user
            clearChatHistory();
        }

        // Update UI based on authentication status
        function updateAuthUI() {
            const loginPrompt = document.getElementById('loginPrompt');
            const userInfo = document.getElementById('userInfo');
            const userNikSpan = document.getElementById('userNik');
            const patientInfo = document.getElementById('patientInfo');
            const selectedPatientName = document.getElementById('selectedPatientName');
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

                // Check if patient is selected
                const authData = JSON.parse(localStorage.getItem('sipo_auth'));
                if (authData?.selected_patient_name) {
                    patientInfo.classList.remove('hidden');
                    patientInfo.classList.add('flex');
                    selectedPatientName.textContent = authData.selected_patient_name;
                } else {
                    patientInfo.classList.add('hidden');
                    patientInfo.classList.remove('flex');
                }

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
                // Hide patient info
                patientInfo.classList.add('hidden');
                patientInfo.classList.remove('flex');
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
            document.getElementById('loginForm').reset();
        }

        // Show patient selection modal
        function showPatientSelectionModal() {
            const modal = document.getElementById('patientSelectionModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        // Close patient selection modal
        function closePatientSelectionModal() {
            const modal = document.getElementById('patientSelectionModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Load family members for patient selection
        async function loadFamilyMembers(nik) {
            try {
                const response = await fetch('{{ route('api.family-list') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        nik
                    })
                });

                const result = await response.json();

                if (result.success && result.data.anggota_keluarga) {
                    const container = document.getElementById('patientListContainer');
                    container.innerHTML = '';

                    result.data.anggota_keluarga.forEach(member => {
                        const button = document.createElement('button');
                        button.className =
                            'w-full p-4 border-2 border-gray-200 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition text-left flex items-center gap-3';
                        button.innerHTML = `
                            <div class="w-10 h-10 bg-linear-to-r from-purple-600 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800">${member.nama_pasien}</p>
                                <p class="text-xs text-gray-500">${member.hubungan}</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        `;
                        button.onclick = () => selectPatient(member.id_keluarga, member.nama_pasien);
                        container.appendChild(button);
                    });

                    return true;
                } else {
                    console.error('Failed to load family members');
                    return false;
                }
            } catch (error) {
                console.error('Error loading family members:', error);
                return false;
            }
        }

        // Select patient
        function selectPatient(idKeluarga, namaPasien) {
            console.log('👤 Selecting patient:', {
                id_keluarga: idKeluarga,
                id_type: typeof idKeluarga,
                nama_pasien: namaPasien
            });

            // Store selected patient
            localStorage.setItem('selected_patient_id', idKeluarga);
            localStorage.setItem('selected_patient_name', namaPasien);

            // Close modal
            closePatientSelectionModal();

            // Update auth data
            const authData = JSON.parse(localStorage.getItem('sipo_auth'));
            authData.selected_patient_id = idKeluarga;
            authData.selected_patient_name = namaPasien;
            localStorage.setItem('sipo_auth', JSON.stringify(authData));

            console.log('✅ Patient data saved to localStorage:', authData);

            // IMPORTANT: Clear chat history when switching patients to avoid AI confusion
            chatHistory = [];
            currentPatientIdForHistory = idKeluarga; // Update current patient for history tracking
            localStorage.removeItem('sipo_chat_history'); // Remove old history
            localStorage.setItem('sipo_chat_patient_id', String(idKeluarga)); // Set new patient ID

            console.log('🔄 Chat history cleared and patient ID updated:', idKeluarga);

            // Update UI
            updateAuthUI();

            // Clear chat UI and show loading message
            const messagesContainer = document.getElementById('chatMessages');
            messagesContainer.innerHTML = '';

            addMessageToUI('bot',
                `<p style="margin-bottom: 12px; color: #374151;">✅ Anda telah memilih <strong style="font-weight: bold; color: #6B21A8;">${namaPasien}</strong> untuk konsultasi.</p><p style="margin-bottom: 12px; color: #374151;">⏳ Memuat data rekam medis pasien...</p>`
            );

            // Pre-load medical data to AI memory
            preloadPatientMedicalData(authData.nik, idKeluarga, namaPasien);
        }

        // Pre-load medical data to AI memory (prevents hallucination!)
        function preloadPatientMedicalData(userNik, idKeluarga, namaPasien) {
            console.log('🔄 Pre-loading medical data for AI memory...');

            fetch('{{ route('api.preload-medical-data') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        user_nik: userNik,
                        id_keluarga: idKeluarga
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('✅ Medical data preloaded:', {
                            patient: data.patient_name,
                            total_visits: data.total_visits
                        });

                        // Store medical context as HIDDEN first message in chat history (system memory)
                        // isHidden = true means it won't be displayed in UI but will be sent to AI
                        chatHistory.push({
                            role: 'user',
                            text: data.medical_context,
                            isHidden: true // ← This prevents it from showing in chat UI
                        });

                        // Save to localStorage
                        saveChatHistory();

                        // Update welcome message
                        const messagesContainer = document.getElementById('chatMessages');
                        messagesContainer.innerHTML = '';

                        let welcomeMsg =
                            `<p style="margin-bottom: 12px; color: #374151;">✅ Anda telah memilih <strong style="font-weight: bold; color: #6B21A8;">${namaPasien}</strong> untuk konsultasi.</p>`;

                        if (data.total_visits > 0) {
                            welcomeMsg +=
                                `<p style="margin-bottom: 12px; color: #374151;">📋 Data rekam medis telah dimuat: <strong style="color: #7C3AED;">${data.total_visits} kali kunjungan</strong></p>`;
                            welcomeMsg +=
                                `<p style="margin-bottom: 12px; color: #374151;">Silakan tanyakan apapun tentang riwayat kesehatan pasien ini. Data sudah tersimpan di memori AI dan siap untuk menjawab pertanyaan Anda! 🤖</p>`;
                        } else {
                            welcomeMsg +=
                                `<p style="margin-bottom: 12px; color: #374151;">ℹ️ Pasien ini belum memiliki riwayat kunjungan.</p>`;
                            welcomeMsg +=
                                `<p style="margin-bottom: 12px; color: #374151;">Anda tetap bisa berkonsultasi tentang SIPO atau kondisi kesehatan umum.</p>`;
                        }

                        welcomeMsg +=
                            `<p style="margin-top: 8px; padding: 8px 12px; background: #DBEAFE; border-left: 4px solid #3B82F6; border-radius: 4px; font-size: 14px; color: #1E40AF;"><strong>💡 Keuntungan:</strong> Data rekam medis sudah tersimpan di memori AI. Anda bisa bertanya berulang kali tanpa perlu AI mengambil data lagi - lebih cepat dan akurat!</p>`;

                        addMessageToUI('bot', welcomeMsg);
                    } else {
                        console.error('Failed to preload medical data');

                        // Show error message
                        const messagesContainer = document.getElementById('chatMessages');
                        messagesContainer.innerHTML = '';
                        addMessageToUI('bot',
                            `<p style="margin-bottom: 12px; color: #374151;">✅ Anda telah memilih <strong style="font-weight: bold; color: #6B21A8;">${namaPasien}</strong> untuk konsultasi.</p><p style="margin-bottom: 12px; color: #EF4444;">⚠️ Gagal memuat data rekam medis. Anda tetap bisa chat, tapi AI mungkin tidak bisa menjawab pertanyaan tentang riwayat kesehatan.</p>`
                        );
                    }
                })
                .catch(error => {
                    console.error('Error preloading medical data:', error);

                    // Show fallback message
                    const messagesContainer = document.getElementById('chatMessages');
                    messagesContainer.innerHTML = '';
                    addMessageToUI('bot',
                        `<p style="margin-bottom: 12px; color: #374151;">✅ Anda telah memilih <strong style="font-weight: bold; color: #6B21A8;">${namaPasien}</strong> untuk konsultasi.</p><p style="margin-bottom: 12px; color: #374151;">Silakan tanyakan apapun tentang SIPO atau konsultasi kesehatan.</p>`
                    );
                });
        }

        // Show login error
        function showLoginError(message) {
            const errorDiv = document.getElementById('loginError');
            const errorMessage = document.getElementById('loginErrorMessage');
            if (errorMessage) errorMessage.textContent = message;
            if (errorDiv) errorDiv.classList.remove('hidden');
        }

        // Hide login error
        function hideLoginError() {
            const errorDiv = document.getElementById('loginError');
            if (errorDiv) errorDiv.classList.add('hidden');
        }

        // Handle login form submission
        async function handleLogin(e) {
            e.preventDefault();

            const nikInput = document.getElementById('loginNik');
            const passInput = document.getElementById('loginPassword');
            if (!nikInput || !passInput) return;

            const nik = nikInput.value.trim();
            const password = passInput.value.trim();

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
            const originalText = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Memverifikasi...</span>';
            }

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

                if (result && result.success) {
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

                    // Close login modal
                    closeLoginModal();

                    // Load family members and show patient selection if available
                    const familyLoaded = await loadFamilyMembers(result.data.nik);

                    if (familyLoaded) {
                        showPatientSelectionModal();
                    } else {
                        updateAuthUI();
                        addMessageToUI('bot',
                            `<p>Selamat datang, <strong>${result.data.nama}</strong>! 👋</p><p>NIK: ${result.data.nik} | Departemen: ${result.data.departemen}</p><p>Anda telah berhasil login. Silakan tanyakan apapun tentang SIPO.</p>`
                        );
                    }
                } else {
                    showLoginError((result && result.message) ? result.message : 'Login gagal');
                }
            } catch (error) {
                console.error('Login error:', error);
                showLoginError('Terjadi kesalahan saat login. Silakan coba lagi.');
            } finally {
                // Restore button state
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }
        }

        // Logout function
        function logout() {
            if (confirm('Apakah Anda yakin ingin logout?')) {
                // Clear authentication and patient selection
                localStorage.removeItem('sipo_auth');
                localStorage.removeItem('selected_patient_id');
                localStorage.removeItem('selected_patient_name');
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
                // Keep only last 50 messages to avoid token limits (increased from 20)
                if (chatHistory.length > 50) {
                    chatHistory = chatHistory.slice(-50);
                }
                localStorage.setItem('sipo_chat_history', JSON.stringify(chatHistory));

                // Save current patient ID to ensure history is patient-specific
                if (currentPatientIdForHistory !== null) {
                    localStorage.setItem('sipo_chat_patient_id', String(currentPatientIdForHistory));
                }
            } catch (e) {
                console.error('Error saving chat history:', e);
            }
        }

        // Clear chat history function
        function clearChatHistory() {
            chatHistory = [];
            localStorage.removeItem('sipo_chat_history');
            localStorage.removeItem('sipo_chat_patient_id');
            currentPatientIdForHistory = null;
            const messagesContainer = document.getElementById('chatMessages');

            // Show different message based on authentication status
            let welcomeMessage = '';
            if (isAuthenticated) {
                welcomeMessage = `
                    <p>👋 <strong>Halo!</strong> Saya AI Assistant SIPO.</p>
                    <p>Saya siap membantu menjawab pertanyaan Anda tentang:</p>
                    <ul class="list-disc list-inside space-y-1 my-2">
                        <li>Sistem informasi pelayanan kesehatan</li>
                        <li>Fitur-fitur yang tersedia</li>
                        <li>Cara penggunaan sistem</li>
                        <li>Dan informasi umum lainnya</li>
                    </ul>
                    <p>Ada yang bisa saya bantu? 😊</p>
                `;
            } else {
                welcomeMessage = `
                    <p>👋 <strong>Halo!</strong> Saya AI Assistant SIPO.</p>
                    <p>🔒 Untuk menggunakan fitur chat, silakan <strong>login terlebih dahulu</strong> dengan NIK Anda.</p>
                    <p>Setelah login, Anda dapat:</p>
                    <ul class="list-disc list-inside space-y-1 my-2">
                        <li>📋 Melihat riwayat kunjungan medis Anda</li>
                        <li>💊 Konsultasi tentang obat yang pernah Anda terima</li>
                        <li>📊 Mendapatkan analisis kesehatan pribadi</li>
                        <li>❓ Bertanya tentang sistem SIPO</li>
                    </ul>
                    <p><strong>Klik tombol "Login"</strong> di atas untuk memulai! 🚀</p>
                `;
            }

            messagesContainer.innerHTML = `
                <div class="message bot">
                    <div class="flex gap-3 mb-4">
                        <div class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden">
                            <img src="{{ asset('ai.jpeg') }}" alt="AI Avatar" class="w-full h-full object-cover">
                        </div>
                        <div class="bg-linear-to-r from-purple-100 to-blue-100 rounded-2xl rounded-tl-none px-5 py-3 max-w-5xl">
                            <div class="text-gray-800 prose prose-sm max-w-none">
                                ${welcomeMessage}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Character counter and input validation
        function updateCharCount() {
            const input = document.getElementById('chatInput');
            const charCount = document.getElementById('charCount');
            const currentLength = input.value.length;

            charCount.textContent = currentLength;

            // Change color based on character count
            if (currentLength > 4500) {
                charCount.className = 'text-red-500 font-semibold';
            } else if (currentLength > 4000) {
                charCount.className = 'text-orange-500 font-semibold';
            } else {
                charCount.className = '';
            }
        }

        // Clear input function
        function clearInput() {
            const input = document.getElementById('chatInput');
            input.value = '';
            updateCharCount();
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

            // Client-side validation for message length
            if (message.length > 5000) {
                addMessage('bot',
                    '<p style="color: #EF4444;">⚠️ Pesan terlalu panjang. Maksimal 5000 karakter diperbolehkan. Silakan perpendek pesan Anda.</p>'
                );
                return;
            }

            // Add user message to chat history
            chatHistory.push({
                role: 'user',
                text: message
            });

            // Add user message to chat UI
            addMessage('user', message);

            // Clear input and update character count
            input.value = '';
            updateCharCount();

            // Show typing indicator
            showTypingIndicator();

            // Prepare history for API (exclude current message, keep last 25 exchanges - increased from 20)
            const historyForAPI = chatHistory.slice(0, -1).slice(-50);

            // Get selected patient ID from localStorage
            const authData = JSON.parse(localStorage.getItem('sipo_auth'));
            const selectedPatientId = authData?.selected_patient_id ? parseInt(authData.selected_patient_id) : null;

            // Debug logging
            console.log('🔍 Sending chat request:', {
                user_nik: currentUserNik,
                user_name: currentUserName,
                id_keluarga: selectedPatientId,
                id_keluarga_type: typeof selectedPatientId,
                authData: authData
            });

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
                        user_name: currentUserName,
                        id_keluarga: selectedPatientId
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
            // First, clean up markdown code blocks and other formatting issues
            let cleanedHtml = html;

            // Remove markdown code blocks (```html...```, ```...```)
            cleanedHtml = cleanedHtml.replace(/```html\s*\n?([\s\S]*?)\n?```/gi, '$1');
            cleanedHtml = cleanedHtml.replace(/```\s*\n?([\s\S]*?)\n?```/gi, '$1');

            // Remove inline code blocks (`...`)
            cleanedHtml = cleanedHtml.replace(/`([^`]+)`/g, '$1');

        // Convert markdown bold (**text**) to HTML strong if not already HTML
        cleanedHtml = cleanedHtml.replace(/\*\*([^*]+)\*\*/g,
            '<strong style="font-weight: bold; color: #6B21A8;">$1</strong>');

        // Convert markdown italic (*text*) to HTML em if not already HTML
        cleanedHtml = cleanedHtml.replace(/\*([^*]+)\*/g, '<em style="font-style: italic; color: #7C3AED;">$1</em>');

        // Convert markdown headers to HTML if not already HTML
        cleanedHtml = cleanedHtml.replace(/^### (.+)$/gm,
            '<h3 style="font-size: 18px; font-weight: bold; color: #7C3AED; margin-bottom: 12px;">$1</h3>');
        cleanedHtml = cleanedHtml.replace(/^## (.+)$/gm,
            '<h2 style="font-size: 20px; font-weight: bold; color: #5B21B6; margin-bottom: 12px;">$1</h2>');
        cleanedHtml = cleanedHtml.replace(/^# (.+)$/gm,
            '<h1 style="font-size: 22px; font-weight: bold; color: #4C1D95; margin-bottom: 12px;">$1</h1>');

        // Convert markdown lists to HTML if not already HTML
        cleanedHtml = cleanedHtml.replace(/^\* (.+)$/gm, '<li style="margin-bottom: 4px;">$1</li>');
        cleanedHtml = cleanedHtml.replace(/(<li.*>.*<\/li>)/s,
            '<ul style="margin: 12px 0; padding-left: 24px;">$1</ul>');

        // Convert line breaks to <br> if not already HTML
        cleanedHtml = cleanedHtml.replace(/\n\n/g, '</p><p style="margin-bottom: 12px; color: #374151;">');

        // Wrap in paragraphs if not already wrapped
        if (!cleanedHtml.includes('<p>') && !cleanedHtml.includes('<div>') && !cleanedHtml.includes('<h1>') && !
            cleanedHtml.includes('<h2>') && !cleanedHtml.includes('<h3>')) {
            cleanedHtml = '<p style="margin-bottom: 12px; color: #374151;">' + cleanedHtml + '</p>';
        }

        // Create a temporary div to parse HTML
        const temp = document.createElement('div');
        temp.innerHTML = cleanedHtml;

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
                                                                                                <div class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden">
                                                                                                    <img src="{{ asset('ai.jpeg') }}" alt="AI Avatar" class="w-full h-full object-cover">
                                                                                                </div>
                                                                                                <div class="bg-linear-to-r from-purple-100 to-blue-100 rounded-2xl rounded-tl-none px-5 py-3 max-w-5xl">
                                                                                                    <div class="text-gray-800 prose prose-sm max-w-none">${sanitizedHTML}</div>
                                                                                                </div>
                                                                                            </div>
                                                                                        `;
        } else {
            // Escape user input for security
            const escapedText = text.replace(/</g, '<').replace(/>/g, '>');

            messageDiv.innerHTML = `
                                                                                            <div class="flex gap-3 mb-4 justify-end">
                                                                                                <div class="bg-linear-to-r from-purple-600 to-blue-600 rounded-2xl rounded-tr-none px-5 py-3 max-w-5xl">
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
                                                                                            <div class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden">
                                                                                                <img src="{{ asset('ai.jpeg') }}" alt="AI Avatar" class="w-full h-full object-cover">
                                                                                            </div>
                                                                                            <div class="bg-linear-to-r from-purple-100 to-blue-100 rounded-2xl rounded-tl-none px-5 py-4">
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

        // Mobile Menu Toggle
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');

                // Toggle icon between bars and times
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
    </script>
</body>

</html>