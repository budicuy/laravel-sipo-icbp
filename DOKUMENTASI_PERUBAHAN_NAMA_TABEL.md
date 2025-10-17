# Dokumentasi Perubahan Nama Tabel Stok Obat

## Perubahan yang Dilakukan

### Tabel
- **Nama tabel lama**: `stok_obat`
- **Nama tabel baru**: `stok_bulanan`

### Primary Key
- **Nama primary key lama**: `id_stok_obat`
- **Nama primary key baru**: `id_stok_bulanan`

## File yang Telah Diperbarui

### 1. Model (`app/Models/StokObat.php`)
- `$table = 'stok_obat'` → `$table = 'stok_bulanan'`
- `$primaryKey = 'id_stok_obat'` → `$primaryKey = 'id_stok_bulanan'`

### 2. Controller (`app/Http/Controllers/StokObatController.php`)
- Semua query langsung ke tabel `stok_obat` diubah ke `stok_bulanan`
- `whereIn('id_stok_obat', $ids)` → `whereIn('id_stok_bulanan', $ids)`
- Log error: `id_stok_obat` → `id_stok_bulanan`

### 3. View (`resources/views/stok-obat/index.blade.php`)
- Checkbox value: `id_stok_obat` → `id_stok_bulanan`
- Delete button parameter: `id_stok_obat` → `id_stok_bulanan`

### 4. Migration Script (`database/migrations/2025_10_17_032700_fix_stok_calculation_formula_and_column_order.php`)
- Query ke tabel `stok_obat` diubah ke `stok_bulanan`
- Log field: `id_stok_obat` → `id_stok_bulanan`

## Catatan Penting

1. **Tidak ada perubahan pada struktur tabel**, hanya nama tabel dan primary key yang berubah
2. **Semua relasi tetap sama**, hanya penyesuaian nama field
3. **Migration script sudah diperbarui** untuk menggunakan nama tabel yang benar
4. **Proses migrate:fresh --seed seharusnya berjalan normal** dengan perubahan ini

## Alasan Perubahan

Perubahan ini dilakukan untuk mencocokkan nama tabel dengan migration yang ada (`create_stok_bulanan_table.php`) dan memastikan konsistensi naming convention di seluruh aplikasi.

## Testing

Setelah perubahan ini, pastikan untuk:
1. Jalankan `php artisan migrate:fresh --seed` untuk memastikan migrasi berjalan normal
2. Test semua fungsi CRUD stok obat
3. Test import/export data stok
4. Test perhitungan stok dengan rumus baru
