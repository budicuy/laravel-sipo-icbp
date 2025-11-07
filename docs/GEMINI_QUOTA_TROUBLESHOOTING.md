# Troubleshooting: Gemini API Quota Exceeded (Error 429)

## 🚨 Error yang Terjadi

```
Error 429: RESOURCE_EXHAUSTED
"You exceeded your current quota, please check your plan and billing details"
```

## 📋 Penyebab Error

1. **API Key Tidak Valid**: API key yang digunakan salah atau sudah expired
2. **Free Tier Limit**: Mencapai batas gratis (15 requests per minute atau 1,500 per hari)
3. **Quota Habis**: Jika menggunakan paid plan, quota sudah habis
4. **Region Restriction**: API key tidak tersedia untuk region Anda

## ✅ Solusi

### Solusi 1: Generate API Key Baru (Recommended)

1. **Hapus API Key Lama** (jika ada):
   - Buka: https://makersuite.google.com/app/apikey
   - Delete API key yang lama

2. **Buat API Key Baru**:
   - Klik "Create API Key"
   - Pilih project atau buat project baru
   - Copy API key yang baru

3. **Update di .env**:
   ```env
   GEMINI_API_KEY=AIzaSy_YOUR_NEW_API_KEY_HERE
   ```

4. **Clear Cache**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

### Solusi 2: Gunakan Multiple API Keys (Rotation)

Jika traffic tinggi, gunakan sistem rotasi API key:

**File: `config/gemini.php`**
```php
return [
    'api_keys' => [
        env('GEMINI_API_KEY_1'),
        env('GEMINI_API_KEY_2'),
        env('GEMINI_API_KEY_3'),
    ],
    // ... config lainnya
];
```

**File: `.env`**
```env
GEMINI_API_KEY_1=AIzaSy_KEY_1
GEMINI_API_KEY_2=AIzaSy_KEY_2
GEMINI_API_KEY_3=AIzaSy_KEY_3
```

**Update Controller untuk Rotasi**:
```php
// Di LandingPageController.php
private function getRandomApiKey()
{
    $keys = array_filter(config('gemini.api_keys', []));
    if (empty($keys)) {
        return config('gemini.api_key');
    }
    return $keys[array_rand($keys)];
}

// Gunakan di method chat()
$apiKey = $this->getRandomApiKey();
```

### Solusi 3: Implement Rate Limiting

Batasi request dari user untuk menghindari spam:

**File: `app/Http/Controllers/LandingPageController.php`**
```php
use Illuminate\Support\Facades\RateLimiter;

public function chat(Request $request)
{
    // Rate limit: 5 requests per minute per IP
    $key = 'chat-' . $request->ip();

    if (RateLimiter::tooManyAttempts($key, 5)) {
        $seconds = RateLimiter::availableIn($key);

        return response()->json([
            'success' => false,
            'reply' => "Mohon tunggu {$seconds} detik sebelum mengirim pertanyaan lagi."
        ], 429);
    }

    RateLimiter::hit($key, 60); // 60 seconds decay

    // ... rest of code
}
```

### Solusi 4: Implement Caching

Cache pertanyaan yang sering ditanyakan:

```php
use Illuminate\Support\Facades\Cache;

public function chat(Request $request)
{
    // ... validation code

    $cacheKey = 'chat-' . md5(strtolower($request->message));

    // Cek cache (berlaku 1 jam)
    $cachedReply = Cache::get($cacheKey);
    if ($cachedReply) {
        return response()->json([
            'success' => true,
            'reply' => $cachedReply,
            'cached' => true
        ]);
    }

    // ... call API

    // Simpan ke cache
    if ($response->successful()) {
        Cache::put($cacheKey, $aiReply, 3600); // 1 hour
    }
}
```

### Solusi 5: Fallback Responses

Tambahkan respons fallback untuk pertanyaan umum:

```php
private function getFallbackResponse($message)
{
    $lowercaseMsg = strtolower($message);

    $responses = [
        'sipo' => 'SIPO ICBP adalah Sistem Informasi Pelayanan Kesehatan untuk ICBP Group. Sistem ini membantu mengelola data kesehatan karyawan dengan efisien.',
        'fitur' => 'SIPO ICBP memiliki fitur: Rekam Medis Digital, Manajemen Obat, Surat Keterangan, Laporan & Analitik, serta Keamanan Data.',
        'login' => 'Untuk login, klik tombol "Login" di pojok kanan atas atau kunjungi halaman /login.',
        'kontak' => 'Anda bisa menghubungi kami melalui: Telepon (021) 5795 8822, Call Center +62 800 1122 888, atau Email corporate@indofood.co.id',
    ];

    foreach ($responses as $keyword => $response) {
        if (strpos($lowercaseMsg, $keyword) !== false) {
            return $response;
        }
    }

    return null;
}

// Di method chat(), sebelum call API:
$fallback = $this->getFallbackResponse($request->message);
if ($fallback) {
    return response()->json([
        'success' => true,
        'reply' => $fallback
    ]);
}
```

## 📊 Monitoring Quota

1. **Cek Usage Dashboard**:
   - Buka: https://ai.google.dev/usage?tab=rate-limit
   - Login dengan Google Account yang sama
   - Lihat quota usage dan limit

2. **Monitor di Code**:
```php
// Log setiap request
Log::info('Gemini API Request', [
    'ip' => $request->ip(),
    'message_length' => strlen($request->message),
    'timestamp' => now()
]);
```

## 🎯 Best Practices

1. ✅ **Generate API Key Baru**: Paling mudah dan cepat
2. ✅ **Implement Rate Limiting**: Cegah spam dari user
3. ✅ **Cache Common Questions**: Kurangi API calls
4. ✅ **Fallback Responses**: Untuk pertanyaan umum
5. ✅ **Monitor Usage**: Pantau quota secara berkala

## 🔄 Rate Limits Gemini API (Free Tier)

| Limit Type | Value |
|------------|-------|
| Requests per Minute (RPM) | 15 |
| Requests per Day (RPD) | 1,500 |
| Tokens per Minute (TPM) | 1,000,000 |

## 💡 Quick Fix untuk Sementara

Jika tidak ingin setup kompleks, **disable AI chat sementara** dan tampilkan pesan:

**File: `resources/views/landing/index.blade.php`**

Update welcome message di chatbox:
```javascript
<div class="message bot">
    <div class="message-bubble">
        <div class="text-sm">
            👋 Halo! Untuk saat ini, AI Assistant sedang dalam maintenance.
            Silakan hubungi kami melalui:
            <br>📞 Call Center: +62 800 1122 888
            <br>💬 WhatsApp: +62 889 1122 888
            <br>📧 Email: corporate@indofood.co.id
        </div>
    </div>
</div>
```

Dan di sendMessage function, ganti dengan:
```javascript
function sendMessage(event) {
    event.preventDefault();
    const input = document.getElementById('chatInput');
    const message = input.value.trim();

    if (message === '') return;

    addMessage('user', message);
    input.value = '';

    setTimeout(() => {
        addMessage('bot', 'Terima kasih atas pertanyaan Anda. Untuk informasi lebih lanjut, silakan hubungi kami di Call Center +62 800 1122 888 atau email corporate@indofood.co.id');
    }, 1000);
}
```

## 📞 Support

Jika masalah berlanjut:
1. Check Gemini API Status: https://status.cloud.google.com/
2. Contact Google AI Support: https://ai.google.dev/support
3. Review Billing: https://console.cloud.google.com/billing

---

**Last Updated**: November 7, 2025
