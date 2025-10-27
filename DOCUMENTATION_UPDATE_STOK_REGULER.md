# Dokumentasi: Update Stok Obat pada Edit Rekam Medis Reguler

## Overview
Sistem ini menggunakan **Event-Listener Pattern** untuk secara otomatis menyesuaikan stok obat ketika rekam medis reguler di-update. Ketika jumlah obat pada keluhan berubah (bertambah atau berkurang), stok pakai obat akan otomatis ter-update sesuai dengan perubahan tersebut.

## Alur Kerja Sistem

### 1. Event: `RekamMedisUpdated`
**File:** `app/Events/RekamMedisUpdated.php`

Event ini dipicu ketika rekam medis reguler di-update. Event ini membawa data:
- `$rekamMedis`: Data rekam medis yang baru saja di-update
- `$oldKeluhans`: Collection dari keluhan-keluhan lama sebelum di-update

```php
public function __construct(RekamMedis $rekamMedis, $oldKeluhans = [])
{
    $this->rekamMedis = $rekamMedis;
    $this->oldKeluhans = $oldKeluhans;
}
```

### 2. Listener: `AdjustStokObatListener`
**File:** `app/Listeners/AdjustStokObatListener.php`

Listener ini mendengarkan event `RekamMedisUpdated` dan melakukan penyesuaian stok obat.

#### Proses di Listener:

1. **Ambil Data Keluhan Baru**
   - Query keluhan baru dari database setelah update
   - Hanya ambil keluhan yang memiliki `id_obat` dan `jumlah_obat > 0`

2. **Ekstrak Tahun & Bulan**
   - Ambil tahun dan bulan dari `tanggal_periksa` untuk menentukan periode stok bulanan

3. **Grouping Data**
   - Group keluhan lama berdasarkan `id_obat` dan hitung total `jumlah_obat`
   - Group keluhan baru berdasarkan `id_obat` dan hitung total `jumlah_obat`

4. **Hitung Selisih**
   - Untuk setiap obat, hitung selisih antara jumlah baru dan jumlah lama
   - Rumus: `selisih = jumlah_baru - jumlah_lama`

5. **Update Stok Bulanan**
   - Jika selisih > 0: stok pakai bertambah (obat yang digunakan bertambah)
   - Jika selisih < 0: stok pakai berkurang (obat yang digunakan berkurang)
   - Jika selisih = 0: tidak ada perubahan, skip
   - Update `stok_pakai` di tabel `stok_bulanan` dengan rumus:
     ```php
     stok_pakai = max(0, stok_pakai_lama + selisih)
     ```
   - Jika record stok bulanan belum ada, akan dibuat otomatis

## Implementasi di Controller

### File: `app/Http/Controllers/RekamMedisController.php`

#### Method `update()`

```php
public function update(Request $request, $id)
{
    $rekamMedis = RekamMedis::findOrFail($id);
    
    // ... validasi ...
    
    try {
        \Illuminate\Support\Facades\DB::transaction(function () use ($rekamMedis, $validated, $request) {
            // 1. SIMPAN DATA LAMA sebelum dihapus
            $oldKeluhans = $rekamMedis->keluhans()->get();
            
            // 2. UPDATE rekam medis
            $rekamMedis->update([...]);
            
            // 3. HAPUS keluhan lama
            $rekamMedis->keluhans()->delete();
            
            // 4. SIMPAN keluhan baru
            foreach ($request->keluhan as $keluhanData) {
                // ... create keluhan baru ...
            }
            
            // 5. TRIGGER EVENT untuk adjust stok
            event(new RekamMedisUpdated($rekamMedis, $oldKeluhans));
        }, 3);
        
        return redirect()->route('rekam-medis.index')
            ->with('success', 'Data rekam medis berhasil diperbarui!');
    } catch (\Exception $e) {
        return redirect()->back()->withInput()
            ->with('error', 'Gagal memperbarui data: '.$e->getMessage());
    }
}
```

**Penting:** Event dipanggil **di dalam transaction** untuk memastikan konsistensi data.

## Registrasi Event-Listener

### File: `app/Providers/EventServiceProvider.php`

```php
protected $listen = [
    // ... event lainnya ...
    
    RekamMedisCreated::class => [
        KurangiStokObatListener::class,  // Untuk create
    ],
    
    RekamMedisUpdated::class => [
        AdjustStokObatListener::class,   // Untuk update
    ],
];
```

## Contoh Skenario

### Skenario 1: Menambah Jumlah Obat
**Data Lama:**
- Obat A: 10 tablet
- Obat B: 5 tablet

**Data Baru:**
- Obat A: 15 tablet (bertambah 5)
- Obat B: 5 tablet (tidak berubah)

**Hasil:**
- Stok pakai Obat A: +5 tablet
- Stok pakai Obat B: tidak berubah

### Skenario 2: Mengurangi Jumlah Obat
**Data Lama:**
- Obat A: 10 tablet
- Obat B: 5 tablet

**Data Baru:**
- Obat A: 7 tablet (berkurang 3)
- Obat B: 2 tablet (berkurang 3)

**Hasil:**
- Stok pakai Obat A: -3 tablet
- Stok pakai Obat B: -3 tablet

### Skenario 3: Mengganti Obat
**Data Lama:**
- Obat A: 10 tablet
- Obat B: 5 tablet

**Data Baru:**
- Obat A: 0 tablet (dihapus)
- Obat C: 8 tablet (ditambahkan)

**Hasil:**
- Stok pakai Obat A: -10 tablet
- Stok pakai Obat B: -5 tablet
- Stok pakai Obat C: +8 tablet

## Logging

Sistem mencatat setiap perubahan stok di log Laravel:

```php
Log::info('Menyesuaikan stok obat', [
    'id_rekam' => $rekamMedis->id_rekam,
    'id_obat' => $obatId,
    'jumlah_lama' => $oldJumlah,
    'jumlah_baru' => $newJumlah,
    'selisih' => $selisih,
    'tahun' => $tahun,
    'bulan' => $bulan,
]);
```

Anda dapat melihat log di: `storage/logs/laravel.log`

## Error Handling

Jika terjadi error dalam listener:
- Error akan dicatat di log dengan level `error`
- Proses tidak akan mengganggu update rekam medis
- Listener berjalan di luar transaction utama

## Testing

Untuk test manual:
1. Buat rekam medis baru dengan beberapa obat
2. Edit rekam medis tersebut dan ubah jumlah obat
3. Cek tabel `stok_bulanan` untuk memastikan `stok_pakai` ter-update
4. Cek log di `storage/logs/laravel.log`

## Keamanan & Performa

### Transaction Safety
- Update rekam medis dan trigger event berada dalam satu transaction
- Jika ada error, semua perubahan akan di-rollback

### Prevent Negative Stock
- Menggunakan `max(0, stok_pakai + selisih)` untuk mencegah stok negatif

### Efficient Querying
- Menggunakan groupBy dan collection untuk mengurangi query database
- Hanya proses obat yang mengalami perubahan

## Maintenance

### Update Cache Event
Jika menambah event atau listener baru, jalankan:
```bash
php artisan event:cache
```

Atau clear cache:
```bash
php artisan event:clear
```

### Debug Mode
Untuk debugging, aktifkan log level `debug` di `config/logging.php`:
```php
'level' => env('LOG_LEVEL', 'debug'),
```

## File Terkait

1. **Event:**
   - `app/Events/RekamMedisUpdated.php`

2. **Listener:**
   - `app/Listeners/AdjustStokObatListener.php`

3. **Controller:**
   - `app/Http/Controllers/RekamMedisController.php` (method `update`)

4. **Model:**
   - `app/Models/RekamMedis.php`
   - `app/Models/Keluhan.php`
   - `app/Models/StokBulanan.php`

5. **Provider:**
   - `app/Providers/EventServiceProvider.php`

## Changelog

### Version 1.0 (27 Oktober 2025)
- Implementasi awal sistem update stok otomatis untuk rekam medis reguler
- Menambahkan event `RekamMedisUpdated`
- Menambahkan listener `AdjustStokObatListener`
- Integrasi dengan `RekamMedisController@update`
