# 🛠️ PANDUAN TROUBLESHOOTING

## 🚨 **MASALAH YANG DITEMUKAN**

1. **Migration tidak dijalankan**: "Nothing to migrate"
2. **Seeder gagal**: "No diagnosa emergency or obat data found"

## 🔧 **SOLUSI**

### **Masalah 1: Migration Tidak Dijalankan**

**Penyebab**: Laravel menganggap migration sudah dijalankan karena ada di tabel migrations

**Solusi**:

#### **Opsi 1: Tambahkan Relasi Keluhan-Emergency (Direkomendasikan)**
```bash
# Gunakan file SQL untuk menambahkan relasi keluhan ke rekam_medis_emergency
mysql -u username -p database_name < database/add_keluhan_emergency_relation.sql

# Atau import melalui phpMyAdmin
# 1. Buka phpMyAdmin
# 2. Pilih database Anda
# 3. Klik tab "Import"
# 4. Pilih file database/add_keluhan_emergency_relation.sql
# 5. Klik "Go"
```

#### **Opsi 2: Jalankan SQL Sederhana**
```bash
# Gunakan file SQL sederhana untuk perbaikan langsung
mysql -u username -p database_name < database/fix_emergency_relationships_simple.sql

# Atau import melalui phpMyAdmin
# 1. Buka phpMyAdmin
# 2. Pilih database Anda
# 3. Klik tab "Import"
# 4. Pilih file database/fix_emergency_relationships_simple.sql
# 5. Klik "Go"
```

#### **Opsi 3: Jalankan SQL Langsung**
```bash
# Gunakan file SQL untuk perbaikan langsung
mysql -u username -p database_name < database/fix_emergency_relationships.sql

# Atau import melalui phpMyAdmin
# 1. Buka phpMyAdmin
# 2. Pilih database Anda
# 3. Klik tab "Import"
# 4. Pilih file database/fix_emergency_relationships.sql
# 5. Klik "Go"
```

#### **Opsi 3: Gunakan migration final**
```bash
# Gunakan migration final yang paling aman
php artisan migrate --path=database/migrations/2025_10_22_150000_final_fix_emergency_relationships.php
```

#### **Opsi 4: Hapus dari tabel migrations dan jalankan ulang**
```bash
# Masuk ke MySQL/MariaDB
mysql -u username -p database_name

# Hapus migration dari tabel migrations (jika ada)
DELETE FROM migrations WHERE migration LIKE '%fix_emergency_relationships%';

# Keluar dari MySQL
exit

# Jalankan migration final
php artisan migrate --path=database/migrations/2025_10_22_150000_final_fix_emergency_relationships.php
```

#### **Opsi 5: Jalankan migration dengan force**
```bash
php artisan migrate --force --path=database/migrations/2025_10_22_150000_final_fix_emergency_relationships.php
```

#### **Opsi 3: Jalankan perintah SQL manual**
```sql
-- Tambahkan foreign key yang benar
ALTER TABLE rekam_medis_emergency 
ADD CONSTRAINT rekam_medis_emergency_id_external_employee_foreign 
FOREIGN KEY (id_external_employee) REFERENCES external_employees(id) 
ON UPDATE CASCADE ON DELETE RESTRICT;

-- Tambahkan kolom id_emergency ke tabel keluhan
ALTER TABLE keluhan ADD COLUMN id_emergency INT UNSIGNED NULL AFTER id_rekam;

-- Tambahkan foreign key untuk id_emergency
ALTER TABLE keluhan 
ADD CONSTRAINT keluhan_id_emergency_foreign 
FOREIGN KEY (id_emergency) REFERENCES rekam_medis_emergency(id_emergency) 
ON UPDATE CASCADE ON DELETE CASCADE;

-- Buat pivot table diagnosa_emergency_obat
CREATE TABLE IF NOT EXISTS diagnosa_emergency_obat (
    id_diagnosa_emergency INT UNSIGNED NOT NULL,
    id_obat INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id_diagnosa_emergency, id_obat),
    CONSTRAINT fk_diagnosa_emergency_obat_diagnosa FOREIGN KEY (id_diagnosa_emergency) REFERENCES diagnosa_emergency (id_diagnosa_emergency) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_diagnosa_emergency_obat_obat FOREIGN KEY (id_obat) REFERENCES obat (id_obat) ON DELETE CASCADE ON UPDATE CASCADE
);
```

### **Masalah 2: Tipe Data Tidak Kompatibel**

**Error**: "Referencing column 'id_external_employee' and referenced column 'id' in foreign key constraint are incompatible"

**Penyebab**: Kolom `id_external_employee` dan `id` di tabel `external_employees` memiliki tipe data yang berbeda

**Solusi**:
Migration terbaru sudah diperbaiki untuk secara otomatis:
1. Mengecek tipe data kedua kolom
2. Mengubah tipe data kolom `id_external_employee` agar kompatibel
3. Menambahkan foreign key yang benar

Jalankan migration yang sudah diperbaiki:
```bash
php artisan migrate --path=database/migrations/2025_10_22_140000_manual_fix_emergency_relationships.php
```

### **Masalah 3: Seeder Gagal**

**Penyebab**: Tidak ada data di tabel diagnosa_emergency atau obat

**Solusi**:

#### **Step 1: Jalankan seeders dasar terlebih dahulu**
```bash
# Jalankan diagnosa emergency seeder
php artisan db:seed --class=DiagnosaEmergencySeeder

# Jalankan obat seeder
php artisan db:seed --class=ObatSeeder

# Cek apakah data sudah ada
php artisan tinker
```

Di tinker, jalankan:
```php
// Cek data diagnosa emergency
\App\Models\DiagnosaEmergency::count();

// Cek data obat
\App\Models\Obat::count();
```

#### **Step 2: Jalankan pivot table seeder**
```bash
php artisan db:seed --class=DiagnosaEmergencyObatSeeder
```

#### **Step 3: Jika masih gagal, jalankan semua seeder**
```bash
php artisan db:seed
```

## 🔍 **VERIFIKASI**

### **Cek Struktur Tabel**
```bash
php artisan tinker
```

Di tinker, jalankan:
```php
// Cek apakah kolom id_emergency sudah ada di tabel keluhan
Schema::hasColumn('keluhan', 'id_emergency');

// Cek apakah pivot table sudah ada
Schema::hasTable('diagnosa_emergency_obat');

// Cek foreign key di rekam_medis_emergency
DB::select("
    SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'rekam_medis_emergency'
    AND REFERENCED_TABLE_NAME IS NOT NULL
");

// Cek tipe data kolom
DB::select("
    SELECT COLUMN_NAME, DATA_TYPE, COLUMN_TYPE
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME IN ('rekam_medis_emergency', 'external_employees')
    AND COLUMN_NAME IN ('id_external_employee', 'id')
");
```

### **Cek Data**
```php
// Cek data diagnosa emergency
\App\Models\DiagnosaEmergency::with('obats')->get();

// Cek data di pivot table
DB::table('diagnosa_emergency_obat')->count();
```

### **Cek Relasi di phpMyAdmin**
1. Buka phpMyAdmin
2. Pilih database Anda
3. Klik tab "Designer"
4. Pastikan Anda melihat garis relasi antar tabel

## 🎯 **LANGKAH LENGKAP YANG DIREKOMENDASIKAN**

```bash
# 1. Gunakan migration manual (direkomendasikan)
php artisan migrate --path=database/migrations/2025_10_22_140000_manual_fix_emergency_relationships.php

# Alternative: Hapus migration lama dari tabel migrations
mysql -u username -p -e "DELETE FROM migrations WHERE migration LIKE '%fix_emergency_relationships%';" database_name

# 2. Jalankan migration manual
php artisan migrate --path=database/migrations/2025_10_22_140000_manual_fix_emergency_relationships.php

# 3. Jalankan seeders dasar
php artisan db:seed --class=DiagnosaEmergencySeeder
php artisan db:seed --class=ObatSeeder

# 4. Jalankan pivot table seeder
php artisan db:seed --class=DiagnosaEmergencyObatSeeder

# 5. Verifikasi hasilnya
php artisan tinker
```

## 🆘 **JIKA MASIH MASALAH**

### **Reset Migration (Hati-hati!)**
```bash
# Backup database terlebih dahulu!
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# Reset migration (akan menghapus semua tabel!)
php artisan migrate:fresh

# Jalankan semua migration dan seeders
php artisan migrate --seed
```

### **Manual Check di phpMyAdmin**
1. Buka phpMyAdmin
2. Cek tabel `migrations` - hapus baris yang bermasalah
3. Cek struktur tabel `rekam_medis_emergency`, `keluhan`, `diagnosa_emergency`, `obat`
4. Jalankan perintah SQL manual jika perlu

---

**Dibuat**: 22 Oktober 2025  
**Author**: Kilo Code  
**Version**: 1.0