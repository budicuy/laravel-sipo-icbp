# Fitur Switch Fingerprint - Dokumentasi

## Deskripsi

Fitur ini memungkinkan Super Admin untuk mengaktifkan atau menonaktifkan verifikasi fingerprint pada sistem. Ketika dinonaktifkan, sistem akan langsung menggunakan verifikasi manual dengan NIK dan Tanggal Lahir.

## Komponen yang Ditambahkan

### 1. Database

-   **Tabel**: `settings`

    -   `id` - Primary key
    -   `key` - Setting key (unique)
    -   `value` - Setting value
    -   `type` - Data type (string, boolean, integer, json)
    -   `description` - Deskripsi setting
    -   `created_at`, `updated_at` - Timestamps

-   **Default Data**:
    -   `fingerprint_enabled` = `1` (Aktif)

### 2. Model

-   **App\Models\Setting.php**
    -   Method `getValue($key, $default)` - Mengambil nilai setting
    -   Method `setValue($key, $value, $type, $description)` - Menyimpan/update setting
    -   Method `castValue($value, $type)` - Casting nilai berdasarkan tipe

### 3. Controller

-   **App\Http\Controllers\SettingController.php**
    -   `index()` - Menampilkan halaman settings
    -   `update(Request $request)` - Menyimpan perubahan settings
        -   Validasi: `required|in:0,1` (untuk menangani checkbox ON/OFF)
        -   Hidden input ditambahkan untuk memastikan nilai 0 dikirim saat checkbox OFF
    -   `getFingerprintStatus()` - API endpoint untuk cek status fingerprint

### 4. Routes

```php
// Settings Routes (Super Admin only)
Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
Route::get('/api/settings/fingerprint-status', [SettingController::class, 'getFingerprintStatus']);
```

### 5. Views

-   **resources/views/settings/index.blade.php**
    -   Halaman pengaturan dengan toggle switch
    -   Status badge (Aktif/Nonaktif)
    -   Informasi dan tips keamanan

### 6. Navigation

-   Menu "Pengaturan" ditambahkan di dropdown user navbar (khusus Super Admin)
-   Icon: Settings gear icon
-   Position: Antara "Kembali ke Beranda" dan "Keluar"

### 7. Logic Verifikasi

-   **resources/views/rekam-medis/create.blade.php**
    -   Method `checkFingerprintSetting()` - Cek status fingerprint dari API
    -   Method `init()` - Dimodifikasi untuk cek setting sebelum load templates
    -   Tombol "Verifikasi Sidik Jari" akan disembunyikan jika fingerprint nonaktif
    -   Form verifikasi manual akan langsung ditampilkan jika fingerprint nonaktif

## Cara Penggunaan

### Mengakses Halaman Settings

1. Login sebagai Super Admin
2. Klik dropdown user di navbar (pojok kanan atas)
3. Pilih menu "Pengaturan"

### Mengaktifkan/Menonaktifkan Fingerprint

1. Di halaman Settings, temukan section "Verifikasi Sidik Jari (Fingerprint)"
2. Gunakan toggle switch untuk mengaktifkan atau menonaktifkan
3. Status akan berubah secara visual (Aktif = hijau, Nonaktif = merah)
4. Klik tombol "Simpan Pengaturan"
5. Perubahan akan berlaku segera

### Behavior Sistem

#### Ketika Fingerprint AKTIF:

-   Modal verifikasi menampilkan tombol "Verifikasi Sidik Jari"
-   Sistem akan mencoba verifikasi menggunakan fingerprint reader
-   Verifikasi manual tetap tersedia sebagai alternatif jika fingerprint gagal ≥3 kali

#### Ketika Fingerprint NONAKTIF:

-   Modal verifikasi tidak menampilkan tombol "Verifikasi Sidik Jari"
-   Langsung menampilkan form verifikasi manual (NIK & Tanggal Lahir)
-   Pesan informasi: "Verifikasi Fingerprint Dinonaktifkan"

## Security

-   Hanya Super Admin yang dapat mengakses halaman settings
-   Middleware `role:Super Admin` diterapkan pada route settings
-   CSRF protection pada form update

## API Endpoint

### Get Fingerprint Status

```
GET /api/settings/fingerprint-status

Response:
{
  "enabled": true|false
}
```

## Testing

1. Login sebagai Super Admin
2. Akses halaman Settings
3. Nonaktifkan fingerprint → Simpan
4. Buka halaman "Tambah Rekam Medis"
5. Verifikasi bahwa form manual langsung ditampilkan
6. Kembali ke Settings → Aktifkan fingerprint → Simpan
7. Buka halaman "Tambah Rekam Medis"
8. Verifikasi bahwa tombol fingerprint muncul kembali

## Notes

-   Perubahan setting berlaku real-time tanpa perlu reload aplikasi
-   Default value: Fingerprint AKTIF
-   Jika terjadi error saat cek setting, sistem akan default ke AKTIF untuk keamanan
-   Toggle switch dengan animasi smooth untuk UX yang baik

## Migration Command

```bash
php artisan migrate
```

## Rollback (jika diperlukan)

```bash
php artisan migrate:rollback
```

Akan menghapus tabel `settings` dan mengembalikan state sebelumnya.
