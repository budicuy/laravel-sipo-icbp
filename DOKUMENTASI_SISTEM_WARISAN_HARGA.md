# Dokumentasi Sistem Pelaporan Transaksi Obat dengan Mekanisme Warisan Harga

## Overview

Sistem ini telah dikembangkan untuk mengimplementasikan mekanisme warisan harga otomatis pada pelaporan transaksi obat. Ketika harga obat tidak tersedia untuk bulan tertentu, sistem akan secara otomatis mengambil harga dari bulan sebelumnya secara berurutan hingga harga ditemukan.

## Fitur Utama

### 1. Mekanisme Warisan Harga (Fallback)

- **Fungsi Rekursif**: Implementasi fungsi `getHargaObatWithFallback()` yang mencari harga secara berurutan ke bulan-bulan sebelumnya
- **Bulk Processing**: Fungsi `getBulkHargaObatWithFallback()` untuk optimasi performa pada multiple obat
- **Depth Control**: Maksimal kedalaman pencarian dapat dikonfigurasi (default: 12 bulan ke belakang)

### 2. Notifikasi Warisan Harga

- **Visual Notification**: Tampilan notifikasi ketika harga yang digunakan berasal dari bulan sebelumnya
- **Detail Information**: Menampilkan sumber bulan asal dan kedalaman fallback
- **User-Friendly**: Interface yang jelas untuk memberikan informasi kepada pengguna

### 3. Validasi Kelengkapan Harga

- **Continuity Check**: Validasi untuk mencegah adanya gap harga dalam rentang waktu tertentu
- **Gap Detection**: Identifikasi periode-periode yang tidak memiliki harga
- **Comprehensive Reporting**: Laporan lengkap tentang kelengkapan data harga

### 4. Monitoring Admin

- **Stale Price Detection**: Monitoring obat-obat yang belum diperbarui harganya lebih dari 3 bulan
- **Price History**: Histori perubahan harga untuk setiap obat
- **Recommendations**: Rekomendasi harga berdasarkan periode sebelumnya
- **Bulk Operations**: Kemampuan untuk membuat harga secara批量

## Struktur Database

### Tabel `harga_obat_per_bulan`

```sql
- id_harga_obat (Primary Key)
- id_obat (Foreign Key ke tabel obat)
- periode (Format: MM-YY)
- jumlah_per_kemasan
- harga_per_satuan
- harga_per_kemasan
- created_at
- updated_at
```

### Indexing untuk Optimasi

```sql
- idx_harga_obat_periode_fallback (id_obat, periode)
- idx_harga_periode_desc (periode)
- idx_harga_obat_periode_validation (id_obat, periode)
```

## Implementasi Teknis

### 1. Model HargaObatPerBulan

Fungsi-fungsi utama yang ditambahkan:

```php
// Fungsi fallback rekursif
public static function getHargaObatWithFallback($idObat, $periode = null, $maxDepth = 12, &$result = [])

// Fungsi bulk processing untuk optimasi
public static function getBulkHargaObatWithFallback($obatPeriodes, $maxDepth = 12)

// Validasi kelengkapan harga
public static function validateHargaContinuity($idObat, $startPeriode, $endPeriode)

// Deteksi harga kadaluarsa
public static function getObatWithStaleHarga($months = 3)
```

### 2. Controller Updates

#### LaporanController
- Integrasi mekanisme fallback pada semua fungsi laporan
- Penambahan notifikasi fallback ke view
- Optimasi query untuk menghindari N+1 problems

#### MonitoringHargaController (Baru)
- Monitoring harga kadaluarsa
- Validasi kelengkapan harga
- Generate rekomendasi harga
- Export laporan monitoring

### 3. View Components

#### Fallback Notification Component
```blade
@include('components.fallback-notification')
```

#### Monitoring Interface
- Dashboard monitoring harga
- Tabel obat dengan harga kadaluarsa
- Validasi gap harga
- Modal untuk rekomendasi dan histori

## Alur Kerja Sistem

### 1. Saat Menampilkan Laporan Transaksi

1. Sistem mencari harga obat untuk periode transaksi
2. Jika harga tidak ditemukan, sistem akan:
   - Mencari ke periode sebelumnya (bulan-1)
   - Jika masih tidak ditemukan, lanjut ke bulan-2, dan seterusnya
   - Maksimal pencarian: 12 bulan ke belakang
3. Jika harga ditemukan, sistem akan:
   - Menggunakan harga tersebut untuk perhitungan
   - Menyimpan informasi sumber periode
   - Menampilkan notifikasi fallback

### 2. Saat Monitoring Harga

1. Sistem mengidentifikasi obat dengan harga kadaluarsa (>3 bulan)
2. Validasi kelengkapan harga untuk 6 bulan terakhir
3. Generate rekomendasi harga berdasarkan periode sebelumnya
4. Admin dapat melakukan bulk create harga dari rekomendasi

## Optimasi Performa

### 1. Query Optimization

- **Bulk Processing**: Menggunakan bulk query untuk menghindari N+1 problems
- **Indexing**: Composite index pada kolom id_obat dan periode
- **Caching**: Cache untuk hasil fallback yang sering digunakan

### 2. Memory Management

- **Lazy Loading**: Hanya mengambil data yang diperlukan
- **Collection Processing**: Efisien dalam processing large datasets
- **Stream Processing**: Untuk export data besar

## API Endpoints

### Monitoring Harga

- `GET /monitoring/harga` - Dashboard monitoring
- `GET /monitoring/harga/export` - Export laporan CSV
- `POST /monitoring/harga/validate-continuity` - Validasi kelengkapan
- `GET /monitoring/harga/recommendations` - Generate rekomendasi
- `POST /monitoring/harga/bulk-create` - Bulk create harga
- `GET /monitoring/harga/history/{idObat}` - Histori harga obat

## Penggunaan

### 1. Mengakses Monitoring Harga

1. Login ke sistem
2. Klik menu "Monitoring Harga" di sidebar
3. Filter berdasarkan jumlah bulan kadaluarsa
4. View laporan dan ambil tindakan yang diperlukan

### 2. Generate Rekomendasi Harga

1. Pada halaman monitoring, klik "Generate Rekomendasi"
2. Masukkan periode target (format: MM-YY)
3. Review rekomendasi harga
4. Pilih obat yang akan dibuat harganya
5. Klik "Buat Harga"

### 3. Validasi Kelengkapan Harga

1. Pada halaman monitoring, lihat section "Validasi Kelengkapan Harga"
2. Klik "Detail Validasi" untuk informasi lebih lengkap
3. Periode yang hilang akan ditampilkan

## Keamanan

### 1. Access Control

- Role-based access control untuk menu monitoring
- Hanya Admin dan Super Admin yang dapat mengakses
- Validasi input pada semua form

### 2. Data Integrity

- Transaction management untuk bulk operations
- Validasi data sebelum penyimpanan
- Audit trail untuk perubahan harga

## Troubleshooting

### 1. Common Issues

#### Harga Tidak Ditemukan
- Cek apakah ada harga untuk obat tersebut di periode manapun
- Pastikan format periode benar (MM-YY)
- Verify data di tabel harga_obat_per_bulan

#### Performance Issues
- Pastikan indexing sudah dijalankan
- Cek query log untuk slow queries
- Consider partitioning untuk data besar

#### Notifikasi Tidak Muncul
- Verify fallback notifications di-pass ke view
- Check component fallback-notification
- Ensure JavaScript tidak ada error

### 2. Debug Tools

```php
// Enable query logging
DB::enableQueryLog();
// ... run query
dd(DB::getQueryLog());
```

## Future Enhancements

### 1. Planned Features

- **Automated Price Updates**: Suggestion untuk update harga otomatis
- **Price Trend Analysis**: Analisis tren harga obat
- **Alert System**: Notifikasi otomatis untuk harga kadaluarsa
- **API Integration**: Integration dengan supplier obat

### 2. Performance Improvements

- **Database Partitioning**: Partition by periode
- **Redis Caching**: Cache untuk harga yang sering diakses
- **Background Jobs**: Process large data operations

## Conclusion

Sistem pelaporan transaksi obat dengan mekanisme warisan harga telah berhasil diimplementasikan dengan fitur-fitur:

1. ✅ Mekanisme warisan harga otomatis dengan fallback rekursif
2. ✅ Notifikasi ketika harga berasal dari bulan sebelumnya
3. ✅ Validasi untuk mencegah gap harga
4. ✅ Interface admin untuk monitoring
5. ✅ Optimasi query dengan indexing
6. ✅ Performa yang optimal untuk large datasets

Sistem ini memastikan kelengkapan data harga obat dan memberikan transparansi kepada pengguna mengenai sumber harga yang digunakan dalam perhitungan transaksi.
