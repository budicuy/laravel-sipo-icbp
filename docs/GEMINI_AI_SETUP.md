# Setup Gemini AI Chat Assistant

## 📋 Panduan Setup

### 1. Dapatkan API Key dari Google AI Studio

1. Kunjungi: https://makersuite.google.com/app/apikey
2. Login dengan Google Account Anda
3. Klik tombol **"Create API Key"**
4. Copy API Key yang dihasilkan

### 2. Tambahkan API Key ke File .env

Buka file `.env` di root project dan tambahkan API key:

```env
GEMINI_API_KEY=YOUR_API_KEY_HERE
```

Ganti `YOUR_API_KEY_HERE` dengan API key yang Anda dapatkan dari Google AI Studio.

**Contoh:**
```env
GEMINI_API_KEY=AIzaSyC1234567890abcdefghijklmnopqrstuvw
```

### 3. Konfigurasi (Opsional)

Konfigurasi default sudah optimal, tapi Anda bisa menyesuaikan di `.env`:

```env
# Gemini AI Configuration
GEMINI_API_KEY=YOUR_API_KEY_HERE
GEMINI_MODEL=gemini-1.5-flash-latest
GEMINI_API_ENDPOINT=https://generativelanguage.googleapis.com/v1beta/models
GEMINI_TEMPERATURE=0.7
GEMINI_MAX_TOKENS=1024
GEMINI_TOP_P=0.95
GEMINI_TOP_K=40
```

**Parameter:**
- `GEMINI_MODEL`: Model yang digunakan (gemini-1.5-flash-latest sangat cepat dan efisien)
- `GEMINI_TEMPERATURE`: Kreativitas AI (0.0-1.0, lebih tinggi = lebih kreatif)
- `GEMINI_MAX_TOKENS`: Panjang maksimal respons
- `GEMINI_TOP_P`: Sampling probability (0.0-1.0)
- `GEMINI_TOP_K`: Top-k sampling (integer)

### 4. Clear Cache (Jika Diperlukan)

Setelah menambahkan API key, jalankan:

```bash
php artisan config:clear
php artisan cache:clear
```

### 5. Testing

1. Buka landing page: http://your-domain.com/
2. Klik tombol chat (icon robot) di kanan bawah
3. Ketik pertanyaan, contoh: "Apa itu SIPO ICBP?"
4. AI akan merespons menggunakan Gemini API

## 🔧 Implementasi Backend

### Controller: `LandingPageController.php`

Fungsi `chat()` menangani request dari frontend:

**Fitur:**
- ✅ Validasi input message (max 1000 karakter)
- ✅ Check API key configuration
- ✅ System prompt untuk konteks SIPO ICBP
- ✅ Error handling lengkap
- ✅ Logging untuk debugging
- ✅ Timeout 30 detik

**System Prompt:**
AI dilatih untuk:
- Menjawab tentang SIPO ICBP
- Memberikan informasi fitur-fitur
- Panduan penggunaan sistem
- Jawaban dalam Bahasa Indonesia yang ramah dan profesional

### Frontend: `landing/index.blade.php`

JavaScript `sendMessage()` function:
- Send POST request ke `/api/chat`
- Include CSRF token untuk keamanan
- Show typing indicator saat menunggu response
- Error handling untuk koneksi bermasalah

## 🚀 Model Gemini

### gemini-1.5-flash-latest
- **Kecepatan**: Sangat cepat ⚡
- **Biaya**: Gratis untuk penggunaan standar
- **Kemampuan**: Text generation, conversation
- **Rate Limit**: 15 RPM (requests per minute) untuk free tier

### Alternative Models:
- `gemini-1.5-pro-latest`: Lebih powerful, lebih lambat
- `gemini-1.0-pro`: Versi stabil sebelumnya

## 📊 Rate Limits (Free Tier)

- **Requests per minute**: 15 RPM
- **Requests per day**: 1,500 RPD
- **Tokens per minute**: 1 million TPM

## 🔒 Keamanan

1. **API Key**: Simpan di `.env`, JANGAN commit ke Git
2. **CSRF Protection**: Sudah diimplementasikan
3. **Input Validation**: Max 1000 karakter
4. **Error Handling**: Tidak expose detail error ke user
5. **Logging**: Error dicatat untuk monitoring

## 🐛 Troubleshooting

### Error: "AI Assistant belum dikonfigurasi"
**Solusi**: Tambahkan `GEMINI_API_KEY` di file `.env`

### Error: "terjadi kesalahan saat menghubungi AI Assistant"
**Kemungkinan penyebab:**
1. API key tidak valid
2. Rate limit tercapai
3. Network error

**Solusi:**
1. Cek API key di Google AI Studio
2. Tunggu beberapa menit jika rate limit
3. Cek koneksi internet

### Response Lambat
**Penyebab**: Model sedang busy atau koneksi lambat
**Solusi**: 
- Gunakan `gemini-1.5-flash-latest` (sudah default)
- Turunkan `GEMINI_MAX_TOKENS` jika diperlukan

### Error 429: Rate Limit
**Solusi**: 
1. Tunggu 1 menit
2. Upgrade ke paid plan jika diperlukan
3. Implement rate limiting di aplikasi

## 📝 Logs

Error akan dicatat di `storage/logs/laravel.log`:

```
[2025-11-07 10:30:00] local.ERROR: Gemini API Error {"status":400,"body":"..."}
[2025-11-07 10:31:00] local.ERROR: Gemini API Exception: Connection timeout
```

## 🎯 Best Practices

1. **Monitor Usage**: Pantau penggunaan API di Google AI Studio
2. **Set Timeout**: Default 30 detik sudah optimal
3. **Cache Responses**: Pertimbangkan caching untuk pertanyaan umum
4. **Rate Limiting**: Implement user rate limiting jika traffic tinggi
5. **Fallback**: Sediakan respons alternatif jika API down

## 📚 Resources

- **Google AI Studio**: https://makersuite.google.com/
- **Gemini API Docs**: https://ai.google.dev/docs
- **Pricing**: https://ai.google.dev/pricing
- **Models**: https://ai.google.dev/models/gemini

---

**Last Updated**: November 7, 2025  
**Version**: 1.0.0
