# Penggunaan AI Chat History untuk Keluarga Karyawan

## Overview

Sistem AI Chat History telah diperbarui untuk mendukung tracking aktivitas AI Chat tidak hanya untuk karyawan tetapi juga untuk keluarga karyawan dengan format NIK-KodeHubungan.

## Cara Login

### Login Karyawan

-   Gunakan NIK karyawan (contoh: `1200730`)
-   Password: sama dengan NIK

### Login Keluarga

-   Gunakan format NIK-KodeHubungan (contoh: `1200730-01`)
-   Password: sama dengan NIK karyawan (tanpa kode hubungan)
-   Sistem akan otomatis mendeteksi dan mencatat sebagai keluarga

## Perubahan yang Dilakukan

### 1. Database Schema

-   Menambahkan kolom `kode_hubungan` untuk menyimpan kode hubungan keluarga
-   Menambahkan kolom `tipe_pengguna` untuk membedakan antara 'karyawan' dan 'keluarga'
-   Menambahkan index untuk performa query

### 2. Model Updates

-   `AIChatHistory` model telah diperbarui dengan:
    -   Method `recordFamilyLogin()` untuk mencatat login keluarga
    -   Method `recordFamilyAIChatAccess()` untuk mencatat akses AI Chat keluarga
    -   Accessors untuk label tipe pengguna dan hubungan
    -   Method untuk format NIK dan display name

### 3. Controller Updates

-   Update response JSON untuk menyertakan informasi keluarga
-   Update CSV export untuk mencakup data keluarga

### 4. View Updates

-   Tampilan index menampilkan badge tipe pengguna dan hubungan
-   Tampilan detail menampilkan informasi lengkap keluarga

## Cara Penggunaan

### Mencatat Login Keluarga

```php
// Format: NIK-KodeHubungan
$familyNik = "1234567890-01"; // NIK karyawan + kode hubungan

// Mencatat login keluarga
AIChatHistory::recordFamilyLogin(
    $nik,           // NIK karyawan (tanpa kode hubungan)
    $kodeHubungan,  // Kode hubungan dari tabel hubungan
    $namaKeluarga,  // Nama anggota keluarga
    $departemen     // Departemen karyawan (opsional)
);
```

### Mencatat Akses AI Chat Keluarga

```php
// Mencatat akses AI Chat untuk keluarga
AIChatHistory::recordFamilyAIChatAccess(
    $nik,           // NIK karyawan
    $kodeHubungan   // Kode hubungan
);
```

### Contoh Implementasi

```php
// Saat login keluarga
$karyawan = Karyawan::find($id_karyawan);
$keluarga = Keluarga::find($id_keluarga);

AIChatHistory::recordFamilyLogin(
    $karyawan->nik_karyawan,
    $keluarga->kode_hubungan,
    $keluarga->nama_keluarga,
    $karyawan->departemen->nama_departemen
);

// Saat akses AI Chat
AIChatHistory::recordFamilyAIChatAccess(
    $karyawan->nik_karyawan,
    $keluarga->kode_hubungan
);
```

## Format NIK Keluarga

Untuk keluarga, NIK disimpan dengan format:

```
[NIK_KARYAWAN]-[KODE_HUBUNGAN]
```

Contoh:

-   Karyawan: `1234567890`
-   Keluarga (Istri): `1234567890-01`
-   Keluarga (Anak): `1234567890-02`

## Tampilan

### Index View

-   Menampilkan badge "Karyawan" (hijau) atau "Keluarga" (biru)
-   Menampilkan hubungan untuk anggota keluarga
-   Filter dan search berfungsi untuk kedua tipe pengguna

### Detail View

-   Menampilkan informasi lengkap pengguna
-   Badge tipe pengguna dan hubungan
-   Statistik engagement yang sama untuk karyawan dan keluarga

## Export CSV

Export CSV sekarang mencakup:

-   NIK
-   Nama
-   Tipe (Karyawan/Keluarga)
-   Hubungan (untuk keluarga)
-   Departemen
-   Statistik login dan AI Chat

## Catatan Penting

1. Pastikan kode hubungan sudah ada di tabel `hubungan`
2. NIK karyawan harus valid di tabel `karyawan`
3. Format NIK-KodeHubungan harus konsisten
4. Sistem akan otomatis mendeteksi tipe pengguna berdasarkan format NIK
