# Petunjuk Instalasi Fitur Harga Obat Per Bulan

## Langkah-langkah yang perlu dilakukan:

1. **Jalankan Migration**
   ```bash
   php artisan migrate
   ```

   Migration ini akan:
   - Membuat tabel `harga_obat_per_bulan`
   - Menghapus kolom harga dari tabel `obat`

2. **Clear Cache**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

3. **Restart Server** (jika perlu)
   ```bash
   php artisan serve
   ```

4. **Generate Harga Obat Awal**
   Setelah migration dijalankan, lakukan langkah berikut:
   - Buka menu Harga Obat di sidebar
   - Klik tombol "Generate Periode"
   - Masukkan periode saat ini (format MM-YY, contoh: 10-25)
   - Sistem akan otomatis membuat harga awal untuk semua obat

## Cara Menggunakan Fitur

### Menambah Harga Obat
1. Buka menu Harga Obat di sidebar
2. Klik tombol "Tambah Harga Obat"
3. Pilih obat, periode, jumlah per kemasan, dan harga
4. Klik tombol "Simpan"

### Generate Harga untuk Periode Baru
1. Buka menu Harga Obat
2. Klik tombol "Generate Periode"
3. Masukkan periode (format MM-YY)
4. Sistem akan otomatis membuat harga untuk semua obat

### Mengedit Harga Obat
1. Buka menu Harga Obat
2. Klik tombol edit pada data yang ingin diubah
3. Ubah data yang diperlukan
4. Klik tombol "Update"

### Menghapus Harga Obat
1. Buka menu Harga Obat
2. Pilih data yang ingin dihapus dengan checkbox
3. Klik tombol "Hapus Terpilih"

## Perubahan yang Dilakukan

### Database
- Tabel baru: `harga_obat_per_bulan`
- Kolom yang dihapus dari tabel `obat`: `jumlah_per_kemasan`, `harga_per_satuan`, `harga_per_kemasan`

### Model
- Model baru: `HargaObatPerBulan`
- Update model: `Obat`

### Controller
- Controller baru: `HargaObatController`
- Update controller: `ObatController`, `LaporanController`

### View
- View baru: `harga-obat/index.blade.php`, `harga-obat/create.blade.php`, `harga-obat/edit.blade.php`
- Update view: `obat/index.blade.php`, `components/sidebar.blade.php`

### Routes
- Routes baru untuk fitur harga obat per bulan

## Catatan Penting

- Pastikan untuk backup database sebelum menjalankan migration
- Setelah migration, data harga yang ada di tabel `obat` akan dihapus
- Setelah migration, WAJIB generate harga obat awal untuk periode saat ini
- Jika tidak ada harga obat untuk periode tertentu, laporan akan menampilkan harga 0

## Troubleshooting

Jika mengalami error setelah menjalankan migration:

1. **Clear Cache**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

2. **Restart Server**
   ```bash
   php artisan serve
   ```

3. **Generate Harga Obat Awal**
   - Buka menu Harga Obat di sidebar
   - Klik tombol "Generate Periode"
   - Masukkan periode saat ini (format MM-YY)
   - Sistem akan otomatis membuat harga awal untuk semua obat

4. **Jika Masih Ada Error**
   Error yang mungkin terjadi:
   - "Column not found: harga_per_satuan" -> Pastikan sudah menjalankan migration dan clear cache
   - "Table not found: harga_obat_per_bulan" -> Pastikan sudah menjalankan migration
