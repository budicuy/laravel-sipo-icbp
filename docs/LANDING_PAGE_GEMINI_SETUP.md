# SIPO ICBP - Landing Page dengan AI Chat (Gemini)

## 📋 Overview

Landing page SIPO ICBP telah berhasil dibuat dengan fitur AI Chat menggunakan Google Gemini API. Halaman ini menampilkan informasi tentang sistem, fitur-fitur unggulan, dan AI assistant yang dapat membantu pengunjung.

## ✨ Fitur Landing Page

### 1. **Hero Section**
- Headline yang menarik dengan gradient background
- Call-to-action buttons (Mulai Sekarang & Pelajari Lebih Lanjut)
- Ilustrasi floating animation

### 2. **Features Section**
- 6 Fitur unggulan dengan icon dan deskripsi:
  - Rekam Medis Digital
  - Manajemen Obat
  - AI Assistant
  - Surat Keterangan
  - Laporan & Analitik
  - Keamanan Data
- Hover effects pada setiap card

### 3. **About Section**
- Informasi tentang SIPO ICBP
- Statistik (5000+ Karyawan, 10000+ Rekam Medis)
- Ilustrasi informatif

### 4. **Stats Section**
- 99% Kepuasan Pengguna
- 24/7 Akses Sistem
- 15+ Klinik Terintegrasi
- ISO Certified System

### 5. **Contact Section**
- Telepon
- Email
- Alamat

### 6. **AI Chat Widget**
- Floating chat button di kanan bawah
- Chat interface yang modern
- Typing indicator
- Siap untuk integrasi dengan Gemini API

## 🤖 AI Chat Integration (Gemini API)

### Konfigurasi

File konfigurasi telah dibuat di `config/gemini.php` dengan parameter:
- Model: `models/gemini-flash-lite-latest`
- Temperature: 0.7
- Max Tokens: 1024

### Setup Gemini API Key

**Langkah 1:** Dapatkan API Key dari Google AI Studio
1. Kunjungi: https://makersuite.google.com/app/apikey
2. Login dengan Google Account
3. Klik "Create API Key"
4. Copy API Key yang dihasilkan

**Langkah 2:** Tambahkan ke file `.env`
```env
# Gemini AI Configuration
GEMINI_API_KEY=your_api_key_here
GEMINI_MODEL=models/gemini-flash-lite-latest
GEMINI_API_ENDPOINT=https://generativelanguage.googleapis.com/v1beta
GEMINI_TEMPERATURE=0.7
GEMINI_MAX_TOKENS=1024
GEMINI_TOP_P=0.95
GEMINI_TOP_K=40
```

**Langkah 3:** Install HTTP Client (jika belum ada)
```bash
composer require guzzlehttp/guzzle
```

### Backend Implementation (untuk nanti)

File `LandingPageController.php` sudah memiliki method `chat()` yang siap untuk diimplementasikan. Berikut contoh implementasi lengkap:

```php
public function chat(Request $request)
{
    $request->validate([
        'message' => 'required|string|max:1000'
    ]);

    try {
        $apiKey = config('gemini.api_key');
        $model = config('gemini.model');
        $endpoint = config('gemini.api_endpoint');
        
        $client = new \GuzzleHttp\Client();
        
        $response = $client->post("{$endpoint}/{$model}:generateContent?key={$apiKey}", [
            'json' => [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $request->message
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => config('gemini.temperature'),
                    'maxOutputTokens' => config('gemini.max_tokens'),
                    'topP' => config('gemini.top_p'),
                    'topK' => config('gemini.top_k'),
                ]
            ]
        ]);

        $result = json_decode($response->getBody(), true);
        $aiReply = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak dapat memproses permintaan Anda.';

        return response()->json([
            'success' => true,
            'reply' => $aiReply
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Gemini API Error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Maaf, terjadi kesalahan. Silakan coba lagi nanti.',
            'reply' => 'AI Assistant sedang tidak tersedia saat ini.'
        ], 500);
    }
}
```

### Update JavaScript di Landing Page

Untuk menghubungkan dengan backend Gemini API, update function `sendMessage()` di `landing/index.blade.php`:

```javascript
function sendMessage(event) {
    event.preventDefault();
    
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    
    if (message === '') return;
    
    // Add user message to chat
    addMessage('user', message);
    
    // Clear input
    input.value = '';
    
    // Show typing indicator
    showTypingIndicator();
    
    // Send to Gemini API
    fetch('{{ route("api.chat") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        hideTypingIndicator();
        if (data.success) {
            addMessage('bot', data.reply);
        } else {
            addMessage('bot', 'Maaf, terjadi kesalahan. Silakan coba lagi.');
        }
    })
    .catch(error => {
        hideTypingIndicator();
        console.error('Error:', error);
        addMessage('bot', 'Maaf, koneksi bermasalah. Silakan coba lagi.');
    });
}
```

## 🎨 Desain & Styling

### Color Scheme
- Primary Gradient: `#667eea` → `#764ba2` (Purple gradient)
- Accent: Yellow (`#fbbf24`)
- Background: White & Gray (`#f9fafb`)

### Font
- Font Family: Inter (Google Fonts)
- Modern, clean, dan professional

### Responsiveness
- Mobile-first design
- Responsive grid system
- Breakpoints: sm, md, lg

## 📁 File Structure

```
/routes/web.php                          # Routes untuk landing page & API
/app/Http/Controllers/
  └── LandingPageController.php          # Controller untuk landing page
/resources/views/landing/
  └── index.blade.php                     # Landing page view
/config/gemini.php                        # Gemini API configuration
```

## 🚀 Cara Mengakses

1. **Landing Page**: http://your-domain.com/
2. **Login Portal**: http://your-domain.com/portal atau http://your-domain.com/login

## 📝 TODO untuk Backend AI Chat

- [ ] Install Guzzle HTTP Client
- [ ] Setup Gemini API Key di `.env`
- [ ] Implementasi Gemini API di `LandingPageController@chat`
- [ ] Update JavaScript untuk connect ke backend
- [ ] Add rate limiting untuk API calls
- [ ] Add conversation history/context
- [ ] Add system prompts untuk AI personality
- [ ] Implement error handling & logging

## 🔧 Customization

### Mengubah Model Gemini
Edit file `.env`:
```env
GEMINI_MODEL=models/gemini-pro  # atau model lainnya
```

### Mengubah Temperature (Kreativitas AI)
```env
GEMINI_TEMPERATURE=0.9  # 0.0 - 1.0 (lebih tinggi = lebih kreatif)
```

### Menambah System Prompt
Tambahkan context di API request:
```php
'contents' => [
    [
        'role' => 'user',
        'parts' => [
            [
                'text' => 'Anda adalah AI Assistant untuk SIPO ICBP, sistem informasi pelayanan kesehatan. Jawab dengan ramah dan informatif.'
            ]
        ]
    ],
    [
        'role' => 'user',
        'parts' => [
            ['text' => $request->message]
        ]
    ]
]
```

## 📞 Support

Untuk pertanyaan atau bantuan, hubungi tim development SIPO ICBP.

---

**Last Updated**: November 7, 2025
**Version**: 1.0.0
