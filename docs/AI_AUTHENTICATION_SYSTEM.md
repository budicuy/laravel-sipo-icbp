# Sistem Autentikasi AI Chat - SIPO ICBP

## 📋 Deskripsi
Sistem autentikasi berbasis NIK untuk membatasi akses ke AI Chat pada landing page SIPO ICBP.

## 🔐 Metode Autentikasi

### Format Login
- **Username**: NIK karyawan (angka saja, contoh: `1231231`)
- **Password**: Sama dengan NIK (contoh: `1231231`)

### Validasi
1. **Format NIK**: Harus berupa angka saja (regex: `/^\d+$/`)
2. **Password Match**: Password harus sama persis dengan NIK yang diinput
3. **Session Duration**: 24 jam (86400000 ms)

## 🎨 Komponen UI

### 1. Lock Overlay
**Lokasi**: Di atas chat section
**Elemen**:
- Icon lock (besar)
- Judul "Chat Terkunci"
- Pesan informasi
- Tombol "Login Sekarang"

**HTML**:
```html
<div id="chatLockOverlay" class="absolute inset-0 bg-white/95 backdrop-blur-sm z-10">
    <!-- Lock UI elements -->
</div>
```

### 2. Login Modal
**Trigger**: Klik "Login Sekarang" atau mencoba chat tanpa login
**Form Fields**:
- NIK (text input, angka saja)
- Password (password input, harus sama dengan NIK)
- Submit button

**HTML**:
```html
<div id="loginModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm">
    <!-- Modal content -->
</div>
```

### 3. User Info Display
**Lokasi**: Chat header (kanan atas)
**Tampilan**:
- NIK user yang sedang login
- Tombol Logout
- Hanya muncul saat authenticated

## 💾 Storage

### Authentication Data
**Key**: `sipo_auth`
**Format**:
```json
{
    "nik": "1231231",
    "timestamp": 1234567890123
}
```

### Chat History
**Key**: `sipo_chat_history`
**Condition**: Hanya dimuat jika user authenticated
**Max Messages**: 20 pesan terakhir

## 🔧 Fungsi JavaScript

### 1. checkAuthentication()
Memeriksa status autentikasi saat page load
```javascript
function checkAuthentication() {
    // Cek localStorage untuk auth data
    // Validasi session (24 jam)
    // Update isAuthenticated flag
}
```

### 2. updateAuthUI()
Update tampilan berdasarkan status auth
```javascript
function updateAuthUI() {
    if (isAuthenticated) {
        // Hide lock overlay
        // Show user info
        // Show logout button
    } else {
        // Show lock overlay
        // Hide user info
    }
}
```

### 3. handleLogin(e)
Proses login form submission
```javascript
function handleLogin(e) {
    // Validate NIK format
    // Check password match
    // Store auth data
    // Update UI
}
```

### 4. logout()
Proses logout dengan konfirmasi
```javascript
function logout() {
    // Confirm dialog
    // Clear auth data
    // Clear chat history
    // Show login modal
}
```

### 5. showLoginModal() / closeLoginModal()
Toggle login modal visibility

## 🔒 Security Features

### 1. Client-Side Validation
- NIK format check (angka saja)
- Password matching validation
- Session timeout (24 jam)

### 2. Chat Protection
```javascript
function sendMessage(event) {
    // Check authentication first
    if (!isAuthenticated) {
        showLoginModal();
        return;
    }
    // ... rest of chat logic
}
```

### 3. Data Isolation
- Chat history hanya dimuat untuk authenticated users
- Logout menghapus chat history
- Auth data disimpan di localStorage (bisa di-upgrade ke backend session)

## 📱 User Flow

### Login Flow
1. User membuka landing page
2. Chat section terkunci (overlay tampil)
3. User klik "Login Sekarang"
4. Modal login muncul
5. User input NIK dan password
6. Validasi:
   - ✅ Success: Lock overlay hilang, welcome message
   - ❌ Error: Error message tampil
7. User dapat menggunakan chat

### Logout Flow
1. User klik tombol "Logout"
2. Konfirmasi dialog muncul
3. User konfirmasi:
   - Auth data dihapus
   - Chat history dihapus
   - Lock overlay muncul
   - Login modal ditampilkan

### Session Expiry
- Session berlaku 24 jam
- Setelah 24 jam, user perlu login ulang
- Chat history tetap tersimpan sampai logout manual

## 🎯 Fitur Tambahan

### 1. Error Handling
- NIK tidak valid (bukan angka)
- Password tidak cocok
- Form validation messages

### 2. Welcome Message
Setelah login berhasil:
```
Selamat datang, 1231231! 👋
Anda telah berhasil login. Silakan tanyakan apapun tentang SIPO ICBP.
```

### 3. Logout Confirmation
```
Apakah Anda yakin ingin logout?
```

## 🚀 Future Enhancements

### Backend Integration (Recommended)
Saat ini menggunakan localStorage (client-side only). Untuk production:

1. **Create Auth API Endpoint**
```php
// routes/web.php
Route::post('/api/auth/login', [AuthController::class, 'login']);
Route::post('/api/auth/logout', [AuthController::class, 'logout']);
Route::get('/api/auth/check', [AuthController::class, 'check']);
```

2. **Database Validation**
- Validasi NIK dengan database karyawan
- Cek role/department
- Log login attempts
- Rate limiting

3. **Session Management**
- Laravel session (lebih aman)
- CSRF protection
- Remember me functionality

4. **Additional Security**
- Password hashing
- Two-factor authentication (optional)
- IP whitelist (optional)
- Login attempt limits

## 📊 Testing Checklist

### Manual Testing
- [ ] Lock overlay muncul saat belum login
- [ ] Tombol "Login Sekarang" membuka modal
- [ ] Validasi NIK (harus angka)
- [ ] Validasi password match
- [ ] Error messages tampil dengan benar
- [ ] Login berhasil → unlock chat
- [ ] Welcome message muncul
- [ ] NIK tampil di header
- [ ] Logout button berfungsi
- [ ] Logout confirmation dialog
- [ ] Session 24 jam bekerja
- [ ] Chat history hanya untuk authenticated users
- [ ] Refresh page → session persist
- [ ] Setelah 24 jam → re-login required

### Security Testing
- [ ] Chat tidak bisa diakses tanpa auth
- [ ] Send message blocked jika tidak login
- [ ] localStorage validation proper
- [ ] XSS prevention pada NIK display
- [ ] Session timeout works

## 📝 Notes

### Current Implementation
- **Authentication**: Client-side (localStorage)
- **Validation**: Format check only (tidak validasi ke database)
- **Security**: Basic client-side validation
- **Session**: 24 jam, localStorage-based

### For Production
Disarankan untuk:
1. Implement backend validation
2. Database NIK verification
3. Server-side session management
4. CSRF protection
5. Rate limiting
6. Audit logging

## 🔗 Related Files

- `/resources/views/landing/index.blade.php` - Main UI & JavaScript
- `/docs/AI_MEMORY_FEATURE.md` - Chat history system
- `/docs/AI_RESPONSE_FORMATTING.md` - AI response handling
- `/docs/GEMINI_AI_SETUP.md` - Gemini API integration

---

**Version**: 1.0
**Last Updated**: {{ date }}
**Author**: Development Team
