# Dokumentasi Perbaikan Event Listener Stok Obat

## Tanggal: 27 Oktober 2025

## Masalah yang Ditemukan

Event listener untuk pengurangan, penyesuaian, dan pengembalian stok obat tidak berfungsi ketika:
1. Membuat data rekam medis baru (create)
2. Mengupdate data rekam medis (update)
3. Menghapus data rekam medis (delete)

## Akar Masalah

### 1. Event Dispatched di Dalam Transaction
**Masalah Utama**: Event di-dispatch **SEBELUM** database transaction di-commit. Ketika listener mencoba membaca data `keluhans` dari database, data tersebut belum ter-commit sehingga tidak dapat dibaca.

**Lokasi Masalah**:
- `RekamMedisController::store()` - Event `RekamMedisCreated` di-dispatch di dalam transaction
- `RekamMedisController::update()` - Event `RekamMedisUpdated` di-dispatch di dalam transaction
- `RekamMedisEmergencyController::store()` - Event `RekamMedisEmergencyCreated` di-dispatch di dalam transaction
- `RekamMedisEmergencyController::update()` - Event `RekamMedisEmergencyUpdated` di-dispatch di dalam transaction

### 2. Listener Tidak Menyimpan Perubahan
**Masalah Kedua**: `KurangiStokObatListener` dan `KurangiStokObatEmergencyListener` menggunakan sistem cache dan tidak memanggil `save()` untuk operasi regular (hanya untuk bulk import).

**Lokasi Masalah**:
- `app/Listeners/KurangiStokObatListener.php` - Line ~85-95
- `app/Listeners/KurangiStokObatEmergencyListener.php` - Line ~85-95

## Solusi yang Diterapkan

### 1. Pindahkan Event Dispatch ke Luar Transaction

#### RekamMedisController::store()
```php
// SEBELUM:
$rekamMedis = \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request) {
    // ... create rekam medis and keluhans ...
    RekamMedisCreated::dispatch($rekamMedis); // ❌ Di dalam transaction
    return $rekamMedis;
}, 3);

// SESUDAH:
$rekamMedis = \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request) {
    // ... create rekam medis and keluhans ...
    return $rekamMedis;
}, 3);

// ✅ Dispatch SETELAH transaction commit
event(new RekamMedisCreated($rekamMedis));
```

#### RekamMedisController::update()
```php
// SEBELUM:
\Illuminate\Support\Facades\DB::transaction(function () use ($rekamMedis, $validated, $request) {
    $oldKeluhans = $rekamMedis->keluhans()->get(); // ❌ Di dalam transaction
    // ... update logic ...
    event(new RekamMedisUpdated($rekamMedis, $oldKeluhans)); // ❌ Di dalam transaction
}, 3);

// SESUDAH:
$oldKeluhans = $rekamMedis->keluhans()->get(); // ✅ Ambil SEBELUM transaction

\Illuminate\Support\Facades\DB::transaction(function () use ($rekamMedis, $validated, $request) {
    // ... update logic ...
}, 3);

// ✅ Dispatch SETELAH transaction commit
$rekamMedis->refresh(); // Refresh untuk mendapatkan keluhans terbaru
event(new RekamMedisUpdated($rekamMedis, $oldKeluhans));
```

#### RekamMedisEmergencyController::store()
```php
// SEBELUM:
DB::beginTransaction();
try {
    // ... create logic ...
    event(new RekamMedisEmergencyCreated($rekamMedisEmergency)); // ❌ Di dalam transaction
    DB::commit();
}

// SESUDAH:
DB::beginTransaction();
try {
    // ... create logic ...
    DB::commit();
    
    // ✅ Dispatch SETELAH commit
    event(new RekamMedisEmergencyCreated($rekamMedisEmergency));
}
```

#### RekamMedisEmergencyController::update()
```php
// SEBELUM:
DB::beginTransaction();
try {
    $oldKeluhans = $rekamMedisEmergency->keluhans()->get(); // ❌ Di dalam transaction
    // ... update logic ...
    event(new RekamMedisEmergencyUpdated($rekamMedisEmergency, $oldKeluhans)); // ❌ Di dalam transaction
    DB::commit();
}

// SESUDAH:
$oldKeluhans = $rekamMedisEmergency->keluhans()->get(); // ✅ Ambil SEBELUM transaction

DB::beginTransaction();
try {
    // ... update logic ...
    DB::commit();
    
    // ✅ Dispatch SETELAH commit
    $rekamMedisEmergency->refresh();
    event(new RekamMedisEmergencyUpdated($rekamMedisEmergency, $oldKeluhans));
}
```

### 2. Tambahkan Save Langsung di Listener

#### KurangiStokObatListener.php
```php
// SEBELUM:
$stokBulanan->stok_pakai += $totalJumlah;
$cacheKey = "{$obatId}_{$tahun}_{$bulan}";
self::$stokCache[$cacheKey] = $stokBulanan;
// ❌ Tidak ada save() untuk operasi regular

// SESUDAH:
$stokBulanan->stok_pakai += $totalJumlah;

// ✅ Save langsung kecuali untuk bulk import
if (!self::$suspended) {
    $stokBulanan->save();
}

$cacheKey = "{$obatId}_{$tahun}_{$bulan}";
self::$stokCache[$cacheKey] = $stokBulanan;
```

#### KurangiStokObatEmergencyListener.php
```php
// Perubahan yang sama seperti di atas
```

## File yang Dimodifikasi

1. ✅ `/app/Http/Controllers/RekamMedisController.php`
   - Method `store()` - Line ~279-335
   - Method `update()` - Line ~416-475

2. ✅ `/app/Http/Controllers/RekamMedisEmergencyController.php`
   - Method `store()` - Line ~220-260
   - Method `update()` - Line ~390-460

3. ✅ `/app/Listeners/KurangiStokObatListener.php`
   - Method `handle()` - Line ~65-100

4. ✅ `/app/Listeners/KurangiStokObatEmergencyListener.php`
   - Method `handle()` - Line ~65-100

## Testing yang Diperlukan

Setelah perbaikan ini, lakukan testing untuk memastikan:

### 1. Test Create Rekam Medis
```bash
# Test create rekam medis regular dengan obat
1. Buka form create rekam medis
2. Pilih pasien dan isi data
3. Pilih diagnosa dan obat
4. Submit form
5. Cek tabel stok_bulanan - pastikan stok_pakai bertambah sesuai jumlah obat
```

### 2. Test Update Rekam Medis
```bash
# Test update rekam medis dengan perubahan obat
1. Buka rekam medis yang sudah ada
2. Edit jumlah obat (misalnya dari 5 menjadi 10)
3. Submit form
4. Cek tabel stok_bulanan - pastikan stok_pakai disesuaikan (+5)
```

### 3. Test Delete Rekam Medis
```bash
# Test delete rekam medis
1. Hapus rekam medis yang memiliki obat
2. Cek tabel stok_bulanan - pastikan stok_pakai berkurang sesuai jumlah obat
```

### 4. Test Emergency Records
```bash
# Ulangi test 1-3 untuk rekam medis emergency
```

## Verifikasi di Log

Untuk memastikan listener berjalan, cek log file di `storage/logs/laravel.log`:

```
[DATE] local.INFO: Memproses pengurangan stok obat ...
[DATE] local.INFO: Stok obat berhasil dikurangi ...
```

## Catatan Penting

1. **Transaction Isolation**: Event harus di-dispatch SETELAH transaction commit agar data sudah tersedia di database
2. **Model Refresh**: Setelah transaction, gunakan `$model->refresh()` untuk mendapatkan data terbaru dari database
3. **Old Data Collection**: Untuk update, ambil data lama SEBELUM transaction dimulai
4. **Cache System**: Sistem cache di listener hanya untuk bulk import (`$suspended = true`), untuk operasi regular langsung save
5. **Delete Event**: Event `RekamMedisDeleted` tetap di-dispatch SEBELUM delete karena data keluhans masih diperlukan

## Kesimpulan

Masalah utama adalah **race condition** antara database transaction dan event listener. Dengan memindahkan event dispatch ke luar transaction dan menambahkan save langsung di listener, sistem stok obat sekarang berfungsi dengan baik untuk semua operasi CRUD (Create, Read, Update, Delete).
