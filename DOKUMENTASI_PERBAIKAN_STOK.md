# Dokumentasi Perbaikan Logika Perhitungan Stok Awal Obat

## Masalah yang Diperbaiki

1. **Stok awal tidak mengambil data dari stok akhir bulan sebelumnya**
   - Sebelumnya: Stok awal selalu di-set ke 0 atau nilai manual
   - Sesudah: Stok awal otomatis mengambil nilai dari stok akhir bulan sebelumnya

2. **Tidak ada validasi rumus perhitungan stok akhir**
   - Sebelumnya: Tidak ada validasi untuk rumus perhitungan
   - Sesudah: Ditambahkan validasi dan perhitungan otomatis dengan rumus baru

3. **Tidak ada fungsi untuk memperbaiki data stok yang tidak konsisten**
   - Sebelumnya: Data stok yang tidak konsisten harus diperbaiki manual
   - Sesudah: Ditambahkan fungsi untuk memperbaiki data stok secara otomatis

4. **Urutan kolom export/import tidak sesuai dengan kebutuhan**
   - Sebelumnya: Urutan kolom tidak konsisten
   - Sesudah: Urutan kolom distandarisasi menjadi "Stok Awal | Stok Pakai | Stok Akhir | Stok Masuk"

## Perubahan yang Dilakukan

### 1. Model StokObat (`app/Models/StokObat.php`)

Ditambahkan fungsi-fungsi baru:

- `getStokAkhirBulanSebelumnya($idObat, $periode)`: Mendapatkan stok akhir dari bulan sebelumnya
- `hitungStokAkhir($stokAwal, $stokPakai, $stokMasuk)`: Menghitung stok akhir berdasarkan rumus baru
- `validateStokConsistency()`: Validasi konsistensi data stok
- `updateStokAwalFromPreviousMonth($idObat, $periode)`: Update stok awal otomatis

### 2. Controller StokObat (`app/Http/Controllers/StokObatController.php`)

Modifikasi fungsi yang ada:

- `processHorizontalFormat()`: Ditambahkan logika untuk menghitung stok awal dari bulan sebelumnya dan urutan kolom baru
- `processVerticalFormat()`: Ditambahkan logika untuk menghitung stok awal dari bulan sebelumnya dan urutan kolom baru
- `export()`: Mengubah urutan kolom export menjadi "Stok Awal | Stok Pakai | Stok Akhir | Stok Masuk"
- `downloadTemplateStokObat()`: Mengubah urutan kolom template menjadi "Stok Awal | Stok Pakai | Stok Akhir | Stok Masuk"

Ditambahkan fungsi baru:

- `fixStokConsistency()`: Memperbaiki data stok yang tidak konsisten
- `updateStokAwalForNewPeriod()`: Update stok awal untuk periode baru

### 3. Controller Obat (`app/Http/Controllers/ObatController.php`)

Modifikasi fungsi:

- `store()`: Menggunakan stok awal dari bulan sebelumnya saat membuat obat baru
- `import()`: Menggunakan stok awal dari bulan sebelumnya saat import obat

### 4. Seeder StokObat (`database/seeders/StokObatSeeder.php`)

Modifikasi fungsi:

- `createInitialStok()`: Menggunakan stok awal dari bulan sebelumnya
- `processHorizontalFormat()`: Menggunakan stok awal dari bulan sebelumnya saat import dari CSV

### 5. Routes (`routes/web.php`)

Ditambahkan route baru:

- `POST /stok-obat/fix-consistency`: Untuk memperbaiki data stok
- `POST /stok-obat/update-stok-awal`: Untuk update stok awal periode baru

### 6. View Stok Obat (`resources/views/stok-obat/index.blade.php`)

Modifikasi tampilan:

- Mengubah urutan kolom tabel menjadi "Stok Awal | Stok Pakai | Stok Akhir | Stok Masuk"
- Menyesuaikan total footer sesuai urutan kolom baru

Ditambahkan tombol baru:

- "Perbaiki Stok": Untuk memperbaiki data stok yang tidak konsisten
- "Update Stok Awal": Untuk update stok awal periode baru

### 7. Migration Script (`database/migrations/2025_10_17_032700_fix_stok_calculation_formula_and_column_order.php`)

Ditambahkan migration script untuk:

- Memperbaiki rumus perhitungan stok akhir yang sudah ada di database
- Memastikan integritas data selama proses migrasi
- Menyediakan fungsi rollback jika diperlukan

## Logika Perhitungan Stok

### Rumus Perhitungan Stok Akhir
```
Stok Akhir = Stok Awal + Stok Masuk - Stok Pakai
```

### Logika Stok Awal
1. Stok awal bulan ini = stok akhir bulan sebelumnya
2. Jika stok akhir bulan sebelumnya = 0, maka stok awal = 0
3. Jika tidak ada data bulan sebelumnya, maka stok awal = 0

### Contoh Kasus

**Kasus 1: Periode Baru**
- Bulan September: Stok Akhir = 100
- Bulan Oktober: Stok Awal otomatis = 100
- Bulan Oktober: Stok Pakai = 20, Stok Masuk = 50
- Bulan Oktober: Stok Akhir = 100 + 50 - 20 = 130

**Kasus 2: Stok Habis**
- Bulan September: Stok Akhir = 0
- Bulan Oktober: Stok Awal otomatis = 0
- Bulan Oktober: Stok Pakai = 0, Stok Masuk = 100
- Bulan Oktober: Stok Akhir = 0 + 100 - 0 = 100

## Cara Penggunaan

### 1. Import Data Stok
- Saat import data stok, sistem akan otomatis:
  - Menggunakan stok awal dari bulan sebelumnya jika stok awal = 0
  - Menghitung stok akhir berdasarkan rumus baru jika stok akhir = 0
- Format export/import: Stok Awal | Stok Pakai | Stok Akhir | Stok Masuk

### 2. Perbaiki Data Stok
- Klik tombol "Perbaiki Stok" untuk memperbaiki data stok yang tidak konsisten
- Sistem akan memperbaiki stok awal dan stok akhir yang tidak sesuai rumus

### 3. Update Stok Awal Periode Baru
- Klik tombol "Update Stok Awal"
- Masukkan periode baru (format MM-YY)
- Sistem akan mengupdate stok awal untuk semua obat di periode tersebut

## Testing

Untuk melakukan testing perubahan:

1. Buat data stok untuk beberapa periode
2. Pastikan stok awal periode ini = stok akhir periode sebelumnya
3. Validasi rumus perhitungan stok akhir
4. Gunakan tombol "Perbaiki Stok" untuk memperbaiki data yang tidak konsisten
5. Gunakan tombol "Update Stok Awal" untuk periode baru

## Catatan Penting

- Pastikan format periode selalu MM-YY (contoh: 10-25)
- Sistem akan otomatis menghitung tahun berdasarkan 2 digit terakhir
- Jika stok awal di-input manual tidak sama dengan 0, sistem akan menggunakan nilai tersebut
- Jika stok akhir di-input manual tidak sama dengan 0, sistem akan menggunakan nilai tersebut
- **Rumus perhitungan yang digunakan: Stok Akhir = Stok Awal + Stok Masuk - Stok Pakai**
- **Urutan kolom export/import: Stok Awal | Stok Pakai | Stok Akhir | Stok Masuk**

## Proses Migrasi Data

Untuk memastikan integritas data saat migrasi dari rumus lama ke rumus baru:

1. Jalankan migration script:
   ```bash
   php artisan migrate
   ```

2. Migration script akan:
   - Menghitung ulang semua stok akhir dengan rumus baru
   - Memperbarui data yang tidak konsisten
   - Mencatat semua perubahan di log

3. Jika diperlukan rollback:
   ```bash
   php artisan migrate:rollback
   ```

## Update Template Import

Template import telah diperbarui dengan fitur-fitur baru:
- **Menampilkan semua obat**: Template akan menampilkan semua obat yang terdaftar di sistem
- **Periode dinamis**: Template akan menghasilkan kolom untuk semua periode yang ada di database
- **Data awal = 0**: Semua nilai stok diinisialisasi dengan 0 pada template
- **Urutan kolom**: Stok Awal | Stok Pakai | Stok Akhir | Stok Masuk

Cara penggunaan:
- Unduh template baru melalui tombol "Template Import"
- Template akan menampilkan semua obat dan semua periode yang ada
- Isi nilai stok yang sesuai untuk setiap obat dan periode
