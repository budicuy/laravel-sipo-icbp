# Dokumentasi Perbaikan Duplicate Query Monitoring Harga Obat

## Masalah
Terdapat duplicate query pada bagian monitoring harga obat yang menyebabkan query yang sama dijalankan dua kali:

```
7 statements were executed (2 duplicates)Show All3.71ms
HargaObatPerBulan.php#348db_sipo_icbp540μsselect `obat`.`id_obat`, `obat`.`nama_obat`, `h1`.`periode` as `last_harga_periode` from `harga_obat_per_bulan` as `h1` inner join (select id_obat, MAX(periode) as latest_periode from `harga_obat_per_bulan` group by `id_obat`) as `h2` on `h1`.`id_obat` = `h2`.`id_obat` and `h1`.`periode` = `h2`.`latest_periode` inner join `obat` on `h1`.`id_obat` = `obat`.`id_obat` where `h1`.`periode` < '07-25' order by `h1`.`periode` asc
```

## Penyebab
Method `getObatWithStaleHarga` di `HargaObatPerBulan.php` dipanggil dua kali:
1. Pada baris 21 di `MonitoringHargaController.php` (untuk ditampilkan di view)
2. Pada baris 54 di `MonitoringHargaController.php` dalam method `getMonitoringStats` (untuk menghitung statistik)

## Solusi yang Diimplementasikan

### 1. Menghindari Pemanggilan Ganda
- Memodifikasi method `index` di `MonitoringHargaController` untuk menyimpan hasil query ke variabel
- Mengirimkan variabel tersebut ke method `getMonitoringStats` sebagai parameter
- Method `getMonitoringStats` menggunakan data yang sudah ada jika tersedia

### 2. Implementasi Caching
- Menambahkan cache pada method `getObatWithStaleHarga` dengan durasi 30 menit
- Cache key menggunakan format `stale_harga_obat_{months}_{thresholdPeriode}` untuk memastikan unik

### 3. Cache Management
- Menambahkan method `clearStaleHargaCache` untuk membersihkan cache ketika ada perubahan data
- Menggunakan model events (`saved` dan `deleted`) untuk otomatis membersihkan cache saat ada perubahan

## Detail Perubahan Kode

### File: app/Http/Controllers/MonitoringHargaController.php

#### Method index():
```php
// Get obat dengan harga yang belum diperbarui (hanya sekali)
$obatStaleHarga = HargaObatPerBulan::getObatWithStaleHarga($months);

// Get statistik monitoring (menggunakan data yang sudah diambil)
$stats = $this->getMonitoringStats($months, $obatStaleHarga);
```

#### Method getMonitoringStats():
```php
private function getMonitoringStats($months, $obatStaleHarga = null)
{
    // ... kode lainnya ...
    
    // Obat dengan harga kadaluarsa (gunakan data yang sudah diambil jika tersedia)
    if ($obatStaleHarga !== null) {
        $obatWithStaleHarga = $obatStaleHarga->count();
    } else {
        $obatWithStaleHarga = HargaObatPerBulan::getObatWithStaleHarga($months)->count();
    }
    
    // ... kode lainnya ...
}
```

### File: app/Models/HargaObatPerBulan.php

#### Method getObatWithStaleHarga():
```php
public static function getObatWithStaleHarga($months = 3)
{
    $currentPeriode = now()->format('m-y');
    $thresholdPeriode = self::getPeriodeMonthsAgo($months);
    
    // Gunakan cache untuk menghindari query berulang
    $cacheKey = "stale_harga_obat_{$months}_{$thresholdPeriode}";
    
    return Cache::remember($cacheKey, now()->addMinutes(30), function() use ($thresholdPeriode) {
        // ... query ...
    });
}
```

#### Method clearStaleHargaCache():
```php
public static function clearStaleHargaCache()
{
    // Clear semua cache yang mungkin terkait dengan stale harga
    $cacheKeys = [];
    
    // Generate cache keys untuk berbagai kemungkinan bulan (1-12)
    for ($months = 1; $months <= 12; $months++) {
        $thresholdPeriode = self::getPeriodeMonthsAgo($months);
        $cacheKeys[] = "stale_harga_obat_{$months}_{$thresholdPeriode}";
    }
    
    // Hapus cache satu per satu
    foreach ($cacheKeys as $key) {
        Cache::forget($key);
    }
}
```

#### Model Events:
```php
protected static function boot()
{
    parent::boot();
    
    // Clear cache ketika ada perubahan pada harga obat
    static::saved(function() {
        self::clearStaleHargaCache();
    });
    
    static::deleted(function() {
        self::clearStaleHargaCache();
    });
}
```

## Hasil yang Diharapkan
1. **Eliminasi Duplicate Query**: Query yang sama tidak akan dijalankan dua kali
2. **Performa Lebih Baik**: Mengurangi beban database dengan caching
3. **Data Konsisten**: Cache otomatis dibersihkan saat ada perubahan data
4. **Skalabilitas**: Sistem dapat menangani lebih banyak request tanpa meningkatkan beban database

## Testing
Untuk memverifikasi perbaikan:
1. Akses halaman monitoring harga obat
2. Periksa query log untuk memastikan tidak ada duplicate query
3. Lakukan perubahan data harga obat untuk memastikan cache terupdate dengan benar

## Catatan Tambahan
- Cache duration diset 30 menit sebagai balance antara performa dan data freshness
- Cache key dirancang unik berdasarkan parameter months dan threshold periode
- Model events memastikan cache konsisten dengan data aktual
