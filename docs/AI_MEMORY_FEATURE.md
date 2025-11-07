# Fitur Memori AI Chat - SIPO ICBP

## 📋 Deskripsi
AI Assistant SIPO ICBP kini dilengkapi dengan **sistem memori percakapan** yang memungkinkan AI mengingat konteks chat sebelumnya untuk memberikan jawaban yang lebih relevan dan kontekstual.

## ✨ Fitur Utama

### 1. **Penyimpanan Riwayat Chat**
- Menggunakan **localStorage** browser untuk menyimpan riwayat percakapan
- Riwayat tetap tersimpan meskipun halaman di-refresh
- Otomatis memuat kembali percakapan saat halaman dibuka kembali

### 2. **Konteks Percakapan**
- AI dapat mengingat hingga **20 pesan terakhir** dalam percakapan
- Memberikan jawaban yang lebih koheren berdasarkan konteks sebelumnya
- Mendukung follow-up questions tanpa perlu mengulang konteks

### 3. **Manajemen Memori**
- Tombol **"Hapus Riwayat"** untuk membersihkan riwayat chat
- Otomatis membatasi jumlah pesan yang disimpan untuk menghindari token limit
- Efficient storage management

## 🔧 Implementasi Teknis

### Backend (Laravel Controller)

```php
public function chat(Request $request)
{
    $request->validate([
        'message' => 'required|string|max:1000',
        'history' => 'nullable|array',
        'history.*.role' => 'required|string|in:user,model',
        'history.*.text' => 'required|string',
    ]);

    // Build conversation history
    $contents = [];

    // Add chat history if exists
    $history = $request->input('history', []);
    if (!empty($history)) {
        foreach ($history as $message) {
            $contents[] = [
                'role' => $message['role'],
                'parts' => [
                    ['text' => $message['text']]
                ]
            ];
        }
    }

    // Add current user message
    $contents[] = [
        'role' => 'user',
        'parts' => [
            ['text' => $systemPrompt . "\n\nPertanyaan: " . $request->message]
        ]
    ];

    // Call Gemini API with conversation history
    $response = Http::timeout(30)->post("{$endpoint}/{$model}:generateContent?key={$apiKey}", [
        'contents' => $contents,
        // ... other configs
    ]);
}
```

### Frontend (JavaScript)

```javascript
// Chat history storage
let chatHistory = [];

// Load chat history from localStorage on page load
window.addEventListener('DOMContentLoaded', function() {
    const savedHistory = localStorage.getItem('sipo_chat_history');
    if (savedHistory) {
        chatHistory = JSON.parse(savedHistory);
        // Restore messages in chat UI
        chatHistory.forEach(msg => {
            addMessageToUI(msg.role === 'user' ? 'user' : 'bot', msg.text);
        });
    }
});

// Save chat history to localStorage
function saveChatHistory() {
    // Keep only last 20 messages to avoid token limits
    if (chatHistory.length > 20) {
        chatHistory = chatHistory.slice(-20);
    }
    localStorage.setItem('sipo_chat_history', JSON.stringify(chatHistory));
}

// Send message with history
function sendMessage(event) {
    event.preventDefault();

    const message = input.value.trim();

    // Add to history
    chatHistory.push({
        role: 'user',
        text: message
    });

    // Prepare history for API (exclude current message, keep last 20)
    const historyForAPI = chatHistory.slice(0, -1).slice(-20);

    // Send to backend
    fetch('/api/chat', {
        method: 'POST',
        body: JSON.stringify({
            message: message,
            history: historyForAPI
        })
    })
    .then(response => response.json())
    .then(data => {
        // Add bot response to history
        chatHistory.push({
            role: 'model',
            text: data.reply
        });
        saveChatHistory();
        addMessage('bot', data.reply);
    });
}

// Clear chat history
function clearChatHistory() {
    chatHistory = [];
    localStorage.removeItem('sipo_chat_history');
    // Reset UI to welcome message
}
```

## 📊 Struktur Data

### Chat History Format
```json
[
  {
    "role": "user",
    "text": "Apa itu SIPO?"
  },
  {
    "role": "model",
    "text": "SIPO adalah Sistem Informasi Pelayanan Kesehatan..."
  },
  {
    "role": "user",
    "text": "Apa fiturnya?"
  },
  {
    "role": "model",
    "text": "Fitur SIPO meliputi rekam medis digital..."
  }
]
```

### API Request Format
```json
{
  "message": "Apa fiturnya?",
  "history": [
    {
      "role": "user",
      "text": "Apa itu SIPO?"
    },
    {
      "role": "model",
      "text": "SIPO adalah Sistem Informasi Pelayanan Kesehatan..."
    }
  ]
}
```

## 🎯 Manfaat

1. **Percakapan Lebih Natural**
   - AI dapat merujuk ke informasi yang sudah dibahas sebelumnya
   - Tidak perlu mengulang konteks di setiap pertanyaan

2. **Pengalaman Pengguna Lebih Baik**
   - Chat history tetap tersimpan meskipun refresh
   - Dapat melanjutkan percakapan kapan saja

3. **Respons Lebih Akurat**
   - AI memahami konteks lengkap percakapan
   - Jawaban lebih relevan dengan alur diskusi

4. **Efisiensi Komunikasi**
   - Follow-up questions lebih mudah
   - Tidak perlu menjelaskan ulang topik yang sama

## 🔐 Keamanan & Privasi

- **Local Storage**: Riwayat chat disimpan di browser pengguna (client-side)
- **Tidak Persistent di Server**: Server tidak menyimpan riwayat chat
- **User Control**: Pengguna dapat menghapus riwayat kapan saja dengan tombol "Hapus Riwayat"
- **Auto Cleanup**: Otomatis membatasi jumlah pesan untuk menghindari storage overflow

## 📝 Limitasi

1. **Token Limit**: Maksimal 20 pesan dalam history untuk menghindari API token limit
2. **Browser Based**: Riwayat tersimpan per browser/device
3. **Local Storage Limit**: Tergantung kapasitas localStorage browser (~5-10MB)

## 🚀 Penggunaan

### User Flow:
1. Pengguna membuka halaman landing
2. Chat history otomatis dimuat dari localStorage (jika ada)
3. Pengguna mengirim pesan, sistem menyimpan ke history
4. Setiap request ke API menyertakan context dari history
5. Bot response juga disimpan ke history
6. Pengguna dapat menghapus history dengan tombol "Hapus Riwayat"

### Contoh Percakapan:
```
User: "Apa itu SIPO?"
Bot: "SIPO adalah Sistem Informasi Pelayanan Kesehatan ICBP..."

User: "Apa fitur utamanya?"
Bot: "Fitur utama SIPO yang saya sebutkan tadi meliputi..."
           ↑
    (AI mengingat konteks tentang SIPO dari pertanyaan sebelumnya)

User: "Bagaimana cara mendaftarnya?"
Bot: "Untuk mendaftar ke sistem SIPO yang kita bahas, Anda bisa..."
           ↑
    (AI tetap dalam konteks pembahasan SIPO)
```

## 🎨 UI/UX Features

- **Tombol "Hapus Riwayat"** di header chat untuk membersihkan percakapan
- **Icon 🧠** menandakan AI memiliki memori percakapan
- **Auto-restore** riwayat chat saat halaman dimuat
- **Smooth scrolling** ke pesan terbaru

## 📅 Version History

- **v1.0** (7 November 2025)
  - Initial release fitur memori AI
  - Support untuk 20 pesan history
  - LocalStorage implementation
  - Clear history button

## 🔮 Future Enhancements

- [ ] Export chat history ke PDF/TXT
- [ ] Search dalam chat history
- [ ] Server-side session storage untuk authenticated users
- [ ] Kategorisasi percakapan by topic
- [ ] Share conversation link
- [ ] Multi-device sync untuk authenticated users

---

**Dibuat oleh**: SIPO ICBP Development Team
**Tanggal**: 7 November 2025
**Status**: ✅ Active
