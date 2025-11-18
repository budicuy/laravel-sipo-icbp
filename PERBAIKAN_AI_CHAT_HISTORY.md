# Perbaikan AI Chat History untuk Anggota Keluarga

## Masalah yang Diperbaiki

Sebelumnya, sistem AI Chat hanya mencatat history untuk karyawan saja. Ketika anggota keluarga login dan menggunakan AI chat, yang tercatat di database adalah nama karyawan (data master), bukan nama pasien dari hubungan keluarga.

## Perubahan yang Dilakukan

### 1. **Backend - LandingPageController.php**

#### a. Method `checkNik()` - Tambah pencatatan login karyawan

```php
// Sekarang mencatat login history untuk karyawan
AIChatHistory::recordLogin(
    $nik,
    $karyawan->nama_karyawan,
    $karyawan->departemen->nama_departemen ?? null,
    null,
    'karyawan'
);
```

#### b. Method `checkFamilyLogin()` - Tambah pencatatan login keluarga

```php
// Sekarang mencatat login history untuk anggota keluarga
AIChatHistory::recordFamilyLogin(
    $employeeNik,
    $kodeHubungan,
    $keluarga->nama_keluarga,
    $karyawan->departemen->nama_departemen ?? null
);
```

#### c. Method `preloadMedicalData()` - Tambah tracking AI chat access

```php
// Sekarang mencatat akses AI chat saat pasien dipilih
if ($userNik) {
    AIChatHistory::recordAIChatAccess($userNik);

    Log::info('Patient selected for AI chat', [
        'user_nik' => $userNik,
        'id_keluarga' => $idKeluarga,
        'is_family' => strpos($userNik, '-') !== false
    ]);
}
```

### 2. **Frontend - ai-chat.blade.php**

#### a. Function `handleLogin()` - Simpan data lengkap

```javascript
// Sekarang menyimpan tipe pengguna dan kode hubungan
const authData = {
    nik: result.data.nik, // NIK atau NIK-KodeHubungan
    nama: result.data.nama,
    departemen: result.data.departemen,
    tipe_pengguna: result.data.tipe || "karyawan",
    kode_hubungan: result.data.kode_hubungan || null,
    hubungan: result.data.hubungan || null,
    timestamp: Date.now(),
};
```

#### b. Function `handleLogin()` - Handle family member

```javascript
// Untuk user keluarga, gunakan NIK karyawan untuk load family members
// Tapi tetap gunakan NIK-KodeHubungan untuk tracking
let nikForFamilyList = result.data.nik;
if (result.data.tipe === "keluarga" && result.data.nik.includes("-")) {
    nikForFamilyList = result.data.nik.split("-")[0];
}
```

### 3. **Model - AIChatHistory.php**

Model ini sudah mendukung:

-   `recordLogin()` - Mencatat login untuk karyawan
-   `recordFamilyLogin()` - Mencatat login untuk anggota keluarga
-   `recordAIChatAccess()` - Mencatat akses AI chat (support both karyawan & keluarga)

## Alur Kerja Baru

### Untuk Karyawan:

1. User login dengan NIK (contoh: `123456`)
2. Backend mencatat login dengan `AIChatHistory::recordLogin()`
3. Data tersimpan dengan:
    - `nik`: `123456`
    - `nama_karyawan`: nama karyawan
    - `tipe_pengguna`: `karyawan`
4. User pilih pasien → mencatat AI chat access
5. User kirim chat → mencatat AI chat access lagi

### Untuk Anggota Keluarga:

1. User login dengan NIK-KodeHubungan (contoh: `123456-ANAK1`)
2. Backend mencatat login dengan `AIChatHistory::recordFamilyLogin()`
3. Data tersimpan dengan:
    - `nik`: `123456-ANAK1`
    - `nama_karyawan`: nama anggota keluarga (bukan karyawan!)
    - `tipe_pengguna`: `keluarga`
    - `kode_hubungan`: `ANAK1`
4. User pilih pasien → mencatat AI chat access dengan NIK `123456-ANAK1`
5. User kirim chat → mencatat AI chat access lagi dengan NIK `123456-ANAK1`

## Format Data di Database

### Tabel `ai_chat_histories`:

**Contoh data karyawan:**

```
id: 1
nik: 123456
nama_karyawan: John Doe
departemen: IT
tipe_pengguna: karyawan
kode_hubungan: null
login_count: 5
ai_chat_access_count: 20
```

**Contoh data keluarga:**

```
id: 2
nik: 123456-ANAK1
nama_karyawan: Jane Doe (nama anak, bukan John Doe!)
departemen: IT (dari karyawan)
tipe_pengguna: keluarga
kode_hubungan: ANAK1
login_count: 3
ai_chat_access_count: 10
```

## Testing

### Test Login Karyawan:

1. Buka AI Chat
2. Login dengan NIK: `123456`, Password: `123456`
3. Cek database `ai_chat_histories` → harus ada record dengan:
    - `nik = 123456`
    - `nama_karyawan = [Nama Karyawan]`
    - `tipe_pengguna = karyawan`
    - `login_count = 1` (atau increment jika sudah ada)

### Test Login Anggota Keluarga:

1. Buka AI Chat
2. Login dengan NIK: `123456-ANAK1`, Password: `123456`
3. Cek database `ai_chat_histories` → harus ada record dengan:
    - `nik = 123456-ANAK1`
    - `nama_karyawan = [Nama Anak]` ← **BUKAN nama karyawan!**
    - `tipe_pengguna = keluarga`
    - `kode_hubungan = ANAK1`
    - `login_count = 1` (atau increment jika sudah ada)

### Test AI Chat Access:

1. Login (karyawan atau keluarga)
2. Pilih pasien
3. Cek `ai_chat_access_count` → harus increment
4. Kirim chat
5. Cek `ai_chat_access_count` → harus increment lagi

### Test Console Log:

Buka Browser Console (F12) dan cek log:

-   `✅ Family login recorded` (untuk keluarga)
-   `📋 Patient context` dengan detail `is_family_member: YES/NO`
-   `🔄 Chat history cleared and patient ID updated`

## Troubleshooting

### Jika history masih mencatat nama karyawan untuk keluarga:

1. **Cek di console log** apakah `is_family_member: YES`
2. **Cek `nik` yang dikirim** harus berformat `NIK-KodeHubungan`
3. **Cek database** apakah `tipe_pengguna = keluarga`
4. **Clear cache Laravel**: `php artisan cache:clear`
5. **Restart Laravel queue** jika ada: `php artisan queue:restart`

### Jika tidak ada record di database:

1. Cek Laravel log: `storage/logs/laravel.log`
2. Cek apakah middleware `track.login` berjalan
3. Pastikan tabel `ai_chat_histories` sudah ada

## File yang Diubah

1. `/app/Http/Controllers/LandingPageController.php`
2. `/resources/views/landing/ai-chat.blade.php`

## File yang Tidak Diubah (Sudah Benar)

1. `/app/Models/AIChatHistory.php` - Sudah support kedua tipe
2. `/app/Http/Controllers/AIChatHistoryController.php` - Tidak perlu diubah
3. Database migration - Tidak perlu diubah

## Catatan Penting

-   NIK untuk karyawan: `123456`
-   NIK untuk keluarga: `123456-ANAK1` (format: `NIK-KodeHubungan`)
-   Sistem otomatis mendeteksi tipe berdasarkan adanya tanda `-` di NIK
-   Data yang disimpan di `nama_karyawan` adalah nama PASIEN (bukan master karyawan)
