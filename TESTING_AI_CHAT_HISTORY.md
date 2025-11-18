# Testing AI Chat History - Anggota Keluarga

## Problem yang Terjadi

Sofi Kemala Dewi (anggota keluarga) menggunakan AI Chat tapi tidak tercatat di database `ai_chat_histories`.

## Solusi yang Diterapkan

### 1. Tambah Endpoint Baru: `recordPatientSelection`

Endpoint ini akan dipanggil setiap kali user memilih pasien (termasuk anggota keluarga).

**Endpoint:** `POST /api/record-patient-selection`

**Fungsi:**

-   Mencatat pilihan pasien ke database
-   Membuat record dengan NIK format `NIK-KodeHubungan` untuk keluarga
-   Mengembalikan `family_nik` yang akan digunakan untuk tracking

### 2. Update Frontend: `selectPatient()`

Fungsi ini sekarang memanggil `recordPatientSelectionToDatabase()` untuk mencatat pilihan.

### 3. Update Frontend: `sendMessage()`

Sekarang menggunakan `current_patient_nik` (yang berformat `NIK-KodeHubungan`) untuk tracking.

## Langkah Testing

### A. Clear Cache dan Restart

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### B. Test Scenario - Login Karyawan Pilih Anggota Keluarga

#### Step 1: Login sebagai Karyawan

1. Buka AI Chat: `http://localhost:8000/ai-chat`
2. Klik tombol **Login**
3. Login dengan:
    - NIK: `5073241` (M. K. Ronggo Warsito)
    - Password: `5073241`

#### Step 2: Buka Browser Console (F12)

Perhatikan console log:

```
🚀 AI Chat initialized
📦 Current localStorage: {...}
```

#### Step 3: Pilih Pasien Keluarga

1. Klik icon pasien di header
2. Pilih **Sofi Kemala Dewi**
3. Perhatikan console log:

**Yang HARUS muncul:**

```
📝 Recording patient selection to database: {
  user_nik: "5073241",
  id_keluarga: [ID],
  nama_pasien: "Sofi Kemala Dewi"
}

✅ Patient selection recorded successfully: {
  family_nik: "5073241-B",  ← NIK dengan format NIK-KodeHubungan
  family_name: "Sofi Kemala Dewi",
  kode_hubungan: "B",
  hubungan: "Istri"
}

✅ Auth data updated with family NIK: {
  nik: "5073241",
  nama: "M. K. Ronggo Warsito",
  current_patient_nik: "5073241-B",  ← NIK tracking untuk Sofi
  current_patient_kode_hubungan: "B",
  current_patient_hubungan: "Istri",
  selected_patient_id: [ID],
  selected_patient_name: "Sofi Kemala Dewi"
}
```

#### Step 4: Cek Database

```sql
SELECT * FROM ai_chat_histories
WHERE nik LIKE '5073241%'
ORDER BY created_at DESC;
```

**Yang HARUS ada:**

| nik       | nama_karyawan        | tipe_pengguna | kode_hubungan | login_count | ai_chat_access_count |
| --------- | -------------------- | ------------- | ------------- | ----------- | -------------------- |
| 5073241   | M. K. Ronggo Warsito | karyawan      | NULL          | 1           | 0                    |
| 5073241-B | **Sofi Kemala Dewi** | keluarga      | B             | 1           | 1                    |

☝️ **PENTING:** Nama harus "Sofi Kemala Dewi", BUKAN "M. K. Ronggo Warsito"!

#### Step 5: Kirim Chat

1. Ketik: "Halo, apa kabar?"
2. Klik Kirim
3. Perhatikan console log:

```
🔍 Determining NIK for API: {
  current_patient_nik: "5073241-B",  ← Ini yang digunakan
  currentUserNik: "5073241",
  nikForApi: "5073241-B",
  contains_dash: true,
  is_family: "YES"
}

👨‍👩‍👧‍👦 Family member detected, using NIK-KodeHubungan: 5073241-B

🔍 Sending chat request: {
  user_nik: "5073241-B",  ← Ini yang dikirim ke API
  user_name: "Sofi Kemala Dewi",
  id_keluarga: [ID]
}
```

#### Step 6: Cek Database Lagi

```sql
SELECT * FROM ai_chat_histories WHERE nik = '5073241-B';
```

**Yang HARUS terjadi:**

-   `ai_chat_access_count` naik dari 1 menjadi 2

### C. Test Scenario - Login Langsung sebagai Keluarga

#### Step 1: Logout

Klik tombol **Logout**

#### Step 2: Login dengan NIK Keluarga

1. Klik tombol **Login**
2. Login dengan:
    - NIK: `5073241-B` (format: NIK-KodeHubungan)
    - Password: `5073241` (password tetap NIK karyawan)

#### Step 3: Cek Console Log

```
✅ Auth data: {
  nik: "5073241-B",  ← NIK dengan format lengkap
  nama: "Sofi Kemala Dewi",
  tipe_pengguna: "keluarga",
  kode_hubungan: "B",
  hubungan: "Istri"
}
```

#### Step 4: Pilih Pasien

Pilih **Sofi Kemala Dewi** (dirinya sendiri)

#### Step 5: Cek Database

```sql
SELECT * FROM ai_chat_histories WHERE nik = '5073241-B';
```

**Yang HARUS ada:**

-   Record dengan `nik = 5073241-B`
-   `nama_karyawan = Sofi Kemala Dewi`
-   `tipe_pengguna = keluarga`
-   `kode_hubungan = B`
-   `login_count >= 1`

## Troubleshooting

### Jika Record Tidak Muncul di Database

1. **Cek Laravel Log**

```bash
tail -f storage/logs/laravel.log
```

Cari log:

```
Family login recorded
Patient selection recorded for family member
Recording AI chat access for family member
```

2. **Cek Console Browser**
   Harus ada log:

```
✅ Patient selection recorded successfully
✅ Auth data updated with family NIK
```

Jika TIDAK ada, berarti:

-   Route tidak terdaftar
-   CSRF token error
-   Network error

3. **Cek Route**

```bash
php artisan route:list | grep record-patient
```

Harus ada:

```
POST  api/record-patient-selection  api.record-patient-selection
```

4. **Test Manual dengan Curl**

```bash
curl -X POST http://localhost:8000/api/record-patient-selection \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: [TOKEN]" \
  -d '{
    "user_nik": "5073241",
    "id_keluarga": [ID_KELUARGA]
  }'
```

### Jika Nama Masih Salah (Nama Karyawan)

Cek di `AIChatHistory::recordFamilyLogin()`:

```php
// Pastikan parameter ke-3 adalah $keluarga->nama_keluarga
AIChatHistory::recordFamilyLogin(
    $employeeNik,
    $kodeHubungan,
    $keluarga->nama_keluarga,  // ← HARUS nama keluarga!
    $karyawan->departemen->nama_departemen ?? null
);
```

## Expected Results

### Setelah Login Karyawan + Pilih Keluarga:

-   ✅ 1 record untuk karyawan (`nik = 5073241`)
-   ✅ 1 record untuk keluarga (`nik = 5073241-B`)
-   ✅ Nama di record keluarga = "Sofi Kemala Dewi"

### Setelah Login Langsung sebagai Keluarga:

-   ✅ 1 record untuk keluarga (`nik = 5073241-B`)
-   ✅ Nama = "Sofi Kemala Dewi"
-   ✅ `tipe_pengguna = keluarga`

### Setelah Chat:

-   ✅ `ai_chat_access_count` di record keluarga bertambah
-   ✅ `last_ai_chat_access_at` terupdate

## Files Changed

1. `/app/Http/Controllers/LandingPageController.php` - Tambah method `recordPatientSelection()`
2. `/routes/web.php` - Tambah route baru
3. `/resources/views/landing/ai-chat.blade.php` - Tambah function `recordPatientSelectionToDatabase()`
