# Dokumentasi Sistem Stok Obat Revisi

## Ringkasan

Dokumen ini menjelaskan sistem stok obat revisi yang telah dibangun ulang untuk mengatasi masalah pada sistem sebelumnya. Sistem baru ini memiliki fitur yang lebih baik dan lebih handal dalam mengelola stok obat.

## Fitur Utama

### 1. Manajemen Stok yang Lebih Baik
- Stok awal otomatis dari bulan sebelumnya
- Stok pakai dihitung otomatis dari data keluhan
- Validasi konsistensi data stok
- Tanda stok awal pertama kali

### 2. Tracking Stok yang Akurat
- Stok masuk terlacak dengan keterangan
- Stok pakai terhubung langsung dengan data keluhan
- Stok akhir dihitung otomatis berdasarkan rumus: `Stok Awal + Stok Masuk - Stok Pakai`

### 3. Interface yang Lebih User-Friendly
- Form tambah stok dengan preview real-time
- Visualisasi status stok (habis, rendah, tersedia)
- Filter dan sorting yang lebih baik

## Struktur Database

### Tabel `stok_obat`
- `id_stok_obat` - Primary key
- `id_obat` - Foreign key ke tabel obat
- `periode` - Format MM-YY (contoh: 10-24)
- `stok_awal` - Stok awal periode
- `stok_masuk` - Jumlah stok masuk
- `stok_pakai` - Jumlah stok yang terpakai (dihitung otomatis)
- `stok_akhir` - Stok akhir (dihitung otomatis)
- `is_initial_stok` - Tanda stok awal pertama kali
- `keterangan` - Keterangan untuk stok masuk
- `created_at` - Waktu pembuatan
- `updated_at` - Waktu pembaruan

## Model dan Method

### Model `StokObat`
Model ini memiliki method-method penting:

1. **`buatStokAwalPertama($idObat, $periode, $jumlah)`**
   - Membuat stok awal pertama kali untuk obat

2. **`tambahStokMasuk($idObat, $periode, $jumlah, $keterangan)`**
   - Menambah stok masuk untuk periode tertentu

3. **`hitungStokPakaiDariKeluhan($idObat, $periode)`**
   - Menghitung stok pakai dari data keluhan

4. **`hitungStokAkhir($stokAwal, $stokPakai, $stokMasuk)`**
   - Menghitung stok akhir berdasarkan rumus

5. **`getStokAkhirBulanSebelumnya($idObat, $periode)`**
   - Mendapatkan stok akhir dari bulan sebelumnya

6. **`validateStokConsistency()`**
   - Validasi konsistensi data stok

## Controller dan Method

### Controller `StokObatController`
Controller ini memiliki method-method:

1. **`index()`** - Menampilkan daftar stok obat
2. **`create()`** - Menampilkan form tambah stok
3. **`store()`** - Menyimpan data stok baru
4. **`edit()`** - Menampilkan form edit stok
5. **`update()`** - Memperbarui data stok
6. **`destroy()`** - Menghapus data stok
7. **`previewStok()`** - API endpoint untuk preview stok
8. **`updateStokPakai()`** - Update stok pakai otomatis
9. **`generateStokAwal()`** - Generate stok awal periode baru

## Views

### 1. `index.blade.php`
- Menampilkan daftar stok obat
- Filter berdasarkan periode, nama obat, status stok
- Sorting berdasarkan kolom
- Bulk delete
- Visualisasi status stok

### 2. `create.blade.php`
- Form tambah stok obat
- Preview real-time stok akhir
- Validasi form
- AJAX submission

### 3. `edit.blade.php`
- Form edit stok obat
- Preview stok akhir yang akan diperbarui
- Validasi form
- AJAX submission

## Routes

```php
// Stok Obat Routes - Sistem Revisi Baru
Route::get('/stok-obat', [StokObatController::class, 'index'])->name('stok-obat.index');
Route::get('/stok-obat/create', [StokObatController::class, 'create'])->name('stok-obat.create');
Route::post('/stok-obat', [StokObatController::class, 'store'])->name('stok-obat.store');
Route::get('/stok-obat/{id}/edit', [StokObatController::class, 'edit'])->name('stok-obat.edit');
Route::put('/stok-obat/{id}', [StokObatController::class, 'update'])->name('stok-obat.update');
Route::delete('/stok-obat/{id}', [StokObatController::class, 'destroy'])->name('stok-obat.destroy');
Route::post('/stok-obat/bulk-delete', [StokObatController::class, 'bulkDelete'])->name('stok-obat.bulk-delete');

// Additional routes for new stok system
Route::post('/stok-obat/update-stok-pakai', [StokObatController::class, 'updateStokPakai'])->name('stok-obat.update-stok-pakai');
Route::post('/stok-obat/generate-stok-awal', [StokObatController::class, 'generateStokAwal'])->name('stok-obat.generate-stok-awal');

// API Route untuk preview stok
Route::get('/api/stok-obat/preview', [StokObatController::class, 'previewStok'])->name('api.stok-obat.preview');
```

## Cara Penggunaan

### 1. Menambah Stok Awal Pertama Kali
1. Buka halaman `/stok-obat/create`
2. Pilih obat yang belum memiliki stok
3. Masukkan periode dan jumlah stok
4. Sistem akan otomatis menandai sebagai stok awal pertama

### 2. Menambah Stok Masuk
1. Buka halaman `/stok-obat/create`
2. Pilih obat yang sudah memiliki stok
3. Masukkan periode dan jumlah stok
4. Sistem akan otomatis mengambil stok awal dari bulan sebelumnya
5. Sistem akan menghitung stok pakai dari data keluhan
6. Stok akhir akan dihitung otomatis

### 3. Update Stok Pakai Otomatis
1. Klik tombol "Update Stok Pakai"
2. Masukkan periode yang ingin diupdate
3. Sistem akan menghitung ulang stok pakai dari data keluhan
4. Stok akhir akan diperbarui otomatis

### 4. Generate Stok Awal Periode Baru
1. Klik tombol "Generate Stok Awal"
2. Masukkan periode baru
3. Sistem akan membuat stok awal untuk semua obat
4. Stok awal diambil dari stok akhir bulan sebelumnya

## Testing

Untuk menjalankan test sistem stok obat revisi:

```bash
php artisan test tests/Feature/StokObatRevisiTest.php
```

Test mencakup:
- Pembuatan stok awal pertama kali
- Penambahan stok masuk
- Perhitungan stok pakai dari keluhan
- Validasi konsistensi data stok
- Update stok pakai per periode

## Migration

Untuk menjalankan migration sistem stok obat revisi:

```bash
php artisan migrate
```

Migration akan:
1. Menghapus tabel `stok_bulanan` lama
2. Membuat tabel `stok_obat` baru
3. Membuat procedure `calculate_stok_pakai`

## Keunggulan Sistem Baru

1. **Otomatisasi**: Stok pakai dihitung otomatis dari data keluhan
2. **Konsistensi**: Validasi konsistensi data stok
3. **Tracking**: Tracking stok awal pertama kali
4. **User-Friendly**: Interface yang lebih baik dan intuitif
5. **Real-time**: Preview stok akhir real-time
6. **Validasi**: Validasi form yang lebih baik

## Perbedaan dengan Sistem Lama

| Fitur | Sistem Lama | Sistem Baru |
|-------|-------------|-------------|
| Tabel | `stok_bulanan` | `stok_obat` |
| Stok Awal | Manual input | Otomatis dari bulan sebelumnya |
| Stok Pakai | Manual input | Otomatis dari data keluhan |
| Validasi | Terbatas | Lengkap dengan konsistensi check |
| Tracking | Tidak ada | Stok awal pertama kali |
| Preview | Tidak ada | Real-time preview |
| API | Tidak ada | Preview API endpoint |

## Troubleshooting

### 1. Stok Pakai Tidak Terupdate
- Pastikan data keluhan sudah ada untuk periode tersebut
- Jalankan update stok pakai manual
- Cek format periode (MM-YY)

### 2. Stok Awal Tidak Otomatis
- Pastikan sudah ada stok untuk bulan sebelumnya
- Cek apakah ini stok awal pertama kali
- Jalankan generate stok awal manual

### 3. Konsistensi Data Tidak Valid
- Jalankan perbaikan konsistensi stok
- Cek rumus perhitungan stok akhir
- Update stok pakai otomatis

## Future Enhancements

1. Export/Import data stok
2. Notifikasi stok rendah
3. History perubahan stok
4. Laporan stok obat
5. Integrasi dengan sistem pembelian