# Fitur Verifikasi Publik Surat Pengantar

## Overview

Telah ditambahkan halaman verifikasi publik untuk surat pengantar yang dapat diakses oleh siapa saja (tanpa login) menggunakan link random yang di-generate otomatis.

## Fitur yang Ditambahkan

### 1. **Route Publik**

-   **URL Pattern**: `/verify/{token}`
-   **Route Name**: `surat-pengantar.verify`
-   **Akses**: Publik (tidak memerlukan autentikasi)
-   **Method**: GET

### 2. **Link Random (Token)**

-   Setiap surat pengantar memiliki token unik berupa string random 32 karakter
-   Token di-generate otomatis saat pembuatan surat baru
-   Token disimpan di kolom `link_random` di tabel `surat_pengantars`

### 3. **QR Code**

-   QR Code pada surat pengantar sekarang mengarah ke halaman verifikasi publik
-   URL yang di-encode: `https://yourdomain.com/verify/{token}`
-   Siapa pun yang scan QR code akan diarahkan ke halaman verifikasi publik

### 4. **Halaman Verifikasi Publik**

-   **File**: `resources/views/surat-pengantar/verify-public.blade.php`
-   **Layout**: Standalone (tidak menggunakan layout app, sehingga tidak ada menu/sidebar)
-   **Design**:
    -   Badge "VERIFIED" dengan watermark
    -   Card dengan gradient header
    -   Informasi lengkap surat pengantar
    -   Security information section
    -   Responsive design

### 5. **Perbedaan dengan Halaman Internal**

| Aspek           | Halaman Internal (`show`) | Halaman Publik (`verify-public`) |
| --------------- | ------------------------- | -------------------------------- |
| **Akses**       | Perlu login               | Publik                           |
| **URL**         | `/surat-pengantar/{id}`   | `/verify/{token}`                |
| **Layout**      | Menggunakan `layouts.app` | Standalone                       |
| **Menu**        | Ada navbar & sidebar      | Tidak ada                        |
| **Tombol Aksi** | Kembali & Cetak Surat     | Tidak ada tombol aksi            |
| **Design**      | Standar admin panel       | Card-based dengan watermark      |
| **Badge**       | Status card sederhana     | Badge "VERIFIED" animasi         |
| **Footer**      | Footer standar            | Footer dengan disclaimer publik  |

## File yang Dimodifikasi/Ditambahkan

### File Baru:

1. `resources/views/surat-pengantar/verify-public.blade.php` - View halaman verifikasi publik
2. `database/migrations/2025_11_14_071442_add_link_random_to_existing_surat_pengantars.php` - Migration untuk menambahkan kolom dan populate data existing

### File yang Dimodifikasi:

1. `routes/web.php` - Menambahkan route publik
2. `app/Http/Controllers/SuratPengantarController.php` - Menambahkan method `verifyPublic()`
3. `app/Models/SuratPengantar.php` - Update `getQrCodeUrlAttribute()` dan `$fillable`

## Cara Kerja

### Flow Pembuatan Surat Baru:

1. User membuat surat pengantar via form
2. System generate `nomor_surat` dan `link_random` (32 karakter)
3. Surat disimpan ke database
4. QR Code di-generate dengan URL: `route('surat-pengantar.verify', $link_random)`
5. Surat dapat dicetak dengan QR code

### Flow Verifikasi:

1. Seseorang scan QR code dari surat cetak
2. Browser redirect ke: `https://yourdomain.com/verify/abc123xyz...`
3. Controller `verifyPublic()` mencari surat berdasarkan `link_random`
4. Jika ditemukan: tampilkan halaman verifikasi dengan info lengkap
5. Jika tidak ditemukan: tampilkan 404 error

## Testing

### Cara Test Manual:

1. Buat surat pengantar baru via aplikasi
2. Lihat QR code pada halaman print
3. Scan QR code atau copy URL-nya
4. Buka URL di browser (tanpa login)
5. Verifikasi bahwa halaman muncul dengan design yang berbeda

### Cara Test dengan Data Existing:

```bash
# Jalankan migration untuk populate link_random
php artisan migrate

# Check data
php artisan tinker
>>> $surat = App\Models\SuratPengantar::first();
>>> $surat->link_random
>>> route('surat-pengantar.verify', $surat->link_random)
```

## Migration

Migration `2025_11_14_071442_add_link_random_to_existing_surat_pengantars` akan:

1. Menambahkan kolom `link_random` jika belum ada
2. Populate semua record existing dengan token random unique

Status: **DONE** ✅

## Security Considerations

1. **Token Length**: 32 karakter memberikan keamanan yang cukup tinggi
2. **Unique Token**: Setiap surat memiliki token unik
3. **Public Read-Only**: Halaman publik hanya menampilkan informasi, tidak ada aksi edit/delete
4. **No Authentication Required**: Memang disengaja untuk kemudahan verifikasi pihak ketiga

## Customization

### Mengganti Panjang Token:

Edit di `app/Http/Controllers/SuratPengantarController.php`:

```php
'link_random' => \Illuminate\Support\Str::random(32), // ubah 32 ke panjang lain
```

### Mengganti Design:

Edit `resources/views/surat-pengantar/verify-public.blade.php`

### Menambahkan Validasi Tambahan:

Edit method `verifyPublic()` di controller untuk menambahkan logic validasi.

## URL Examples

Contoh URL halaman verifikasi publik:

-   `https://yourdomain.com/verify/a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6`

## Notes

-   QR Code menggunakan package `simplesoftwareio/simple-qrcode`
-   Halaman publik menggunakan Tailwind CSS dari CDN untuk styling
-   Design responsive: mobile-friendly
-   Watermark "VERIFIED" untuk visual authenticity
