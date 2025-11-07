# Fitur Pemilihan Pasien untuk AI Chat

## Deskripsi
Fitur ini memungkinkan user untuk memilih pasien spesifik (diri sendiri atau anggota keluarga) saat login untuk konsultasi AI. Riwayat medis akan difilter berdasarkan pasien yang dipilih.

## Masalah yang Diselesaikan
Sebelumnya, ketika user login dengan NIK (misal: Lahmudin - 1201243), AI menampilkan riwayat medis **semua anggota keluarga** (termasuk Muhammad Hafiz) karena query mengambil data berdasarkan `id_karyawan` yang sama.

## Solusi
1. **Modal Pemilihan Pasien**: Setelah login, user akan melihat daftar anggota keluarga dan memilih siapa yang ingin berkonsultasi
2. **Filter Riwayat Medis**: Riwayat medis difilter berdasarkan `id_keluarga` yang dipilih
3. **UI Indicator**: Nama pasien terpilih ditampilkan di header chat dengan opsi untuk ganti pasien

## Perubahan Backend

### 1. LandingPageController.php

#### Method Baru: `getFamilyMembers($nik)`
- Mengambil daftar semua anggota keluarga dari NIK karyawan
- Return: list `id_keluarga`, `nama_pasien`, `hubungan`

#### Method Baru: `getFamilyList(Request $request)` (Public Endpoint)
- API endpoint untuk mendapatkan daftar keluarga
- Route: `POST /api/family-list`

#### Method Diperbarui: `getMedicalHistoryData($nik, $idKeluarga = null)`
- Tambah parameter `$idKeluarga` (opsional)
- Jika `$idKeluarga` diberikan, filter riwayat hanya untuk pasien tersebut
- Query: `->where('id_keluarga', $idKeluarga)` ditambahkan

#### Method Diperbarui: `chat(Request $request)`
- Tambah validasi untuk `id_keluarga`
- Kirim `$idKeluarga` ke `getMedicalHistoryData()`

### 2. Routes (web.php)
```php
Route::post('/api/family-list', [LandingPageController::class, 'getFamilyList'])
    ->name('api.family-list');
```

## Perubahan Frontend

### 1. Modal Baru: Patient Selection Modal
```html
<div id="patientSelectionModal">
  <!-- Daftar anggota keluarga sebagai tombol -->
</div>
```

### 2. UI Indicator
```html
<div id="patientInfo" onclick="showPatientSelectionModal()">
  <!-- Nama pasien terpilih dengan icon ganti -->
</div>
```

### 3. JavaScript Functions Baru

#### `loadFamilyMembers(nik)`
- Fetch daftar keluarga dari API
- Render tombol pemilihan pasien

#### `selectPatient(idKeluarga, namaPasien)`
- Simpan pilihan pasien ke localStorage
- Update UI
- Tampilkan pesan konfirmasi

#### `showPatientSelectionModal()` & `closePatientSelectionModal()`
- Toggle modal pemilihan pasien

### 4. JavaScript Functions Diperbarui

#### `handleLogin(e)`
- Setelah login sukses, panggil `loadFamilyMembers()`
- Tampilkan modal pemilihan pasien

#### `updateAuthUI()`
- Tampilkan nama pasien terpilih di header
- Sembunyikan jika belum pilih pasien

#### `sendMessage(event)`
- Kirim `id_keluarga` ke backend saat chat
- Data diambil dari localStorage

#### `logout()`
- Clear `selected_patient_id` dan `selected_patient_name`

## Flow User

1. **User login** dengan NIK (misal: 1201243 - Lahmudin)
2. **Modal login ditutup**, modal pemilihan pasien muncul
3. **User melihat daftar**:
   - Lahmudin (Karyawan)
   - Muhammad Hafiz (Anak/Istri/dll)
4. **User pilih pasien** (misal: Lahmudin)
5. **Nama "Lahmudin"** muncul di header dengan icon ganti
6. **User tanya riwayat**: "tampilkan riwayat kunjungan saya"
7. **AI menampilkan riwayat** hanya untuk Lahmudin (9 kunjungan), BUKAN Muhammad Hafiz
8. **User bisa ganti pasien** dengan klik nama di header

## Testing

### Test Case 1: Login dan Pilih Pasien
```
1. Login dengan NIK 1201243
2. Pilih "Lahmudin"
3. Tanya: "tampilkan riwayat kunjungan saya"
4. Expected: Hanya riwayat Lahmudin (9 kunjungan)
```

### Test Case 2: Ganti Pasien
```
1. Sudah login dan pilih "Lahmudin"
2. Klik nama "Lahmudin" di header
3. Modal muncul, pilih "Muhammad Hafiz"
4. Tanya: "tampilkan riwayat kunjungan"
5. Expected: Hanya riwayat Muhammad Hafiz
```

### Test Case 3: Logout dan Login Ulang
```
1. Logout
2. Login lagi dengan NIK 1201243
3. Pilih pasien berbeda
4. Expected: Data sesuai pasien baru
```

## Database Query Changes

### Sebelum
```php
$riwayatKunjungan = RekamMedis::whereHas('keluarga', function ($query) use ($karyawan) {
    $query->where('id_karyawan', $karyawan->id_karyawan);
})->get();
// Mengambil SEMUA keluarga dengan id_karyawan yang sama
```

### Sesudah
```php
$query = RekamMedis::whereHas('keluarga', function ($q) use ($karyawan) {
    $q->where('id_karyawan', $karyawan->id_karyawan);
});

if ($idKeluarga) {
    $query->where('id_keluarga', $idKeluarga); // Filter spesifik pasien
}

$riwayatKunjungan = $query->get();
```

## LocalStorage Structure

### Authentication Data
```javascript
{
  "nik": "1201243",
  "nama": "Lahmudin",
  "departemen": "Production",
  "timestamp": 1699372800000,
  "selected_patient_id": 123,        // NEW
  "selected_patient_name": "Lahmudin" // NEW
}
```

## Security Considerations
- Patient selection tersimpan di localStorage (client-side)
- Backend tetap validasi NIK user yang login
- User hanya bisa melihat data keluarganya sendiri
- Tidak bisa akses data keluarga user lain

## Future Improvements
1. Tambah filter berdasarkan tanggal kunjungan
2. Export riwayat medis per pasien
3. Notifikasi jika ada data medis baru untuk pasien terpilih
4. Multi-language support untuk hubungan keluarga
