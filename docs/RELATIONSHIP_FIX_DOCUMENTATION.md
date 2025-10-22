# 📋 DOKUMENTASI PERBAIKAN RELASI DATABASE EMERGENCY

## 🎯 **OVERVIEW**

Dokumentasi ini menjelaskan perbaikan relasi foreign key pada tabel-tabel emergency yang sebelumnya tidak terlihat di phpMyAdmin Designer.

## 🐛 **MASALAH YANG DITEMUKAN**

### **1. Foreign Key Salah di rekam_medis_emergency**
- **Masalah**: Foreign key `id_external_employee` merujuk ke kolom `id_external_employee` (salah)
- **Seharusnya**: Merujuk ke primary key `id` di tabel `external_employees`

### **2. Tidak Ada Relasi ke rekam_medis_emergency**
- **Masalah**: Tabel `keluhan` hanya berelasi dengan `rekam_medis` biasa
- **Kebutuhan**: Perlu relasi dengan `rekam_medis_emergency` untuk medical records emergency

### **3. Tidak Ada Relasi Many-to-Many**
- **Masalah**: `diagnosa_emergency` tidak berelasi dengan `obat`
- **Kebutuhan**: Satu diagnosa emergency bisa memiliki banyak obat

## 🔧 **SOLUSI YANG DIIMPLEMENTASIKAN**

### **1. Migration Baru**
**File Utama**: `database/migrations/2025_10_22_100000_fix_emergency_tables_relationships.php`
**File Alternatif**: `database/migrations/2025_10_22_110000_safe_fix_emergency_relationships.php`

**Perbaikan**:
- ✅ Perbaiki foreign key `rekam_medis_emergency.id_external_employee` → `external_employees.id`
- ✅ Tambahkan kolom `keluhan.id_emergency` untuk relasi dengan `rekam_medis_emergency`
- ✅ Buat pivot table `diagnosa_emergency_obat` untuk relasi many-to-many

### **2. Perbaruan Model Files**

#### **Keluhan.php**
```php
// Tambahkan kolom baru di fillable
'id_emergency', // Tambahkan kolom baru untuk relasi dengan rekam_medis_emergency

// Tambahkan relasi baru
public function rekamMedisEmergency()
{
    return $this->belongsTo(RekamMedisEmergency::class, 'id_emergency', 'id_emergency');
}

public function diagnosaEmergency()
{
    return $this->belongsTo(DiagnosaEmergency::class, 'id_diagnosa', 'id_diagnosa_emergency');
}
```

#### **RekamMedisEmergency.php**
```php
// Perbaiki relasi external employee
public function externalEmployee()
{
    return $this->belongsTo(ExternalEmployee::class, 'id_external_employee', 'id'); // Perbaikan di sini
}

// Tambahkan relasi diagnosa emergency
public function diagnosaEmergency()
{
    return $this->hasOneThrough(
        DiagnosaEmergency::class,
        Keluhan::class,
        'id_keluhan', // Foreign key on keluhan table
        'id_diagnosa_emergency', // Foreign key on diagnosa_emergency table
        'id_keluhan', // Local key on rekam_medis_emergency table
        'id_diagnosa'  // Local key on keluhan table (references id_diagnosa_emergency)
    );
}
```

#### **DiagnosaEmergency.php**
```php
// Tambahkan relasi many-to-many dengan obat
public function obats()
{
    return $this->belongsToMany(
        Obat::class,
        'diagnosa_emergency_obat',
        'id_diagnosa_emergency',
        'id_obat'
    )->withTimestamps();
}

// Tambahkan helper methods
public function attachObat($obatId, $attributes = [])
public function detachObat($obatId = null)
public function syncObat($obatIds)
```

#### **Obat.php**
```php
// Tambahkan relasi dengan diagnosa emergency
public function diagnosaEmergencies()
{
    return $this->belongsToMany(
        DiagnosaEmergency::class,
        'diagnosa_emergency_obat',
        'id_obat',
        'id_diagnosa_emergency'
    )->withTimestamps();
}
```

### **3. Seeder Baru**
**File**: `database/seeders/DiagnosaEmergencyObatSeeder.php`

**Fitur**:
- ✅ Auto-generate relasi antara diagnosa emergency dan obat
- ✅ Mapping obat yang umum untuk setiap diagnosa
- ✅ Error handling jika data tidak ditemukan

## 🚀 **CARA MENJALANKAN PERBAIKAN**

### **Step 1: Jalankan Migration**
Jika migration pertama gagal, gunakan versi yang lebih aman:

```bash
# Opsi 1: Coba migration pertama (sudah diperbaiki)
php artisan migrate

# Opsi 2: Jika masih gagal, gunakan migration alternatif
php artisan migrate --path=database/migrations/2025_10_22_110000_safe_fix_emergency_relationships.php
```

### **Step 2: Jalankan Seeder (Opsional)**
```bash
php artisan db:seed --class=DiagnosaEmergencyObatSeeder
```

### **Step 3: Verifikasi di phpMyAdmin**
1. Buka phpMyAdmin
2. Pilih database Anda
3. Klik tab "Designer"
4. Sekarang Anda akan melihat:
   - ✅ Garis relasi dari `rekam_medis_emergency` ke `external_employees`
   - ✅ Garis relasi dari `rekam_medis_emergency` ke `keluhan`
   - ✅ Garis relasi dari `diagnosa_emergency` ke `obat` (melalui pivot table)

### **Troubleshooting**
Jika migration masih gagal:
1. **Backup database** terlebih dahulu
2. **Cek struktur tabel** manual di phpMyAdmin
3. **Hapus foreign key yang bermasalah** secara manual:
   - Buka tabel `rekam_medis_emergency`
   - Tab "Structure"
   - Klik "Relation view"
   - Hapus foreign key yang ada
4. **Jalankan migration lagi**

## 📊 **STRUKTUR RELASI BARU**

```mermaid
erDiagram
    external_employees ||--o{ rekam_medis_emergency : "memiliki"
    rekam_medis_emergency ||--o{ keluhan : "mencatat"
    diagnosa_emergency ||--o{ keluhan : "mendiagnosa"
    diagnosa_emergency }|--|| diagnosa_emergency_obat : "pivot"
    obat }|--|| diagnosa_emergency_obat : "pivot"
    
    external_employees {
        int id PK
        string nik_employee UK
        string nama_employee
        string kode_rm
        date tanggal_lahir
        enum jenis_kelamin
        text alamat
        string no_hp
        int id_vendor FK
        string no_ktp
        string bpjs_id
        int id_kategori FK
        string foto
        enum status
        timestamps
    }
    
    rekam_medis_emergency {
        int id_emergency PK
        int id_external_employee FK
        date tanggal_periksa
        time waktu_periksa
        enum status
        text keluhan
        text catatan
        int id_keluhan FK
        int id_user FK
        timestamps
    }
    
    keluhan {
        int id_keluhan PK
        int id_rekam FK
        int id_emergency FK
        int id_diagnosa FK
        enum terapi
        text keterangan
        int id_obat FK
        int jumlah_obat
        text aturan_pakai
        int id_keluarga FK
        timestamp created_at
    }
    
    diagnosa_emergency {
        int id_diagnosa_emergency PK
        string nama_diagnosa_emergency
        text deskripsi
        timestamps
    }
    
    diagnosa_emergency_obat {
        int id_diagnosa_emergency FK
        int id_obat FK
        timestamps
    }
    
    obat {
        int id_obat PK
        string nama_obat UK
        text keterangan
        int id_satuan FK
        datetime tanggal_update
    }
```

## 🔄 **CONTOH PENGGUNAAN RELASI BARU**

### **1. Mengambil Data Emergency dengan External Employee**
```php
$emergency = RekamMedisEmergency::with('externalEmployee')->find($id);
echo $emergency->externalEmployee->nama_employee;
```

### **2. Mengambil Keluhan Emergency dengan Diagnosa**
```php
$keluhan = Keluhan::with('diagnosaEmergency')->where('id_emergency', $emergencyId)->first();
echo $keluhan->diagnosaEmergency->nama_diagnosa_emergency;
```

### **3. Mengambil Obat untuk Diagnosa Emergency**
```php
$diagnosa = DiagnosaEmergency::with('obats')->find($diagnosaId);
foreach ($diagnosa->obats as $obat) {
    echo $obat->nama_obat;
}
```

### **4. Menambahkan Obat ke Diagnosa Emergency**
```php
$diagnosa = DiagnosaEmergency::find($diagnosaId);
$diagnosa->attachObat($obatId);
// atau multiple obat
$diagnosa->syncObat([1, 2, 3]);
```

## ⚠️ **CATATAN PENTING**

1. **Backup Database**: Sebelum menjalankan migration, pastikan untuk backup database
2. **Data Existing**: Migration ini aman untuk data existing
3. **Rollback**: Migration dapat di-rollback jika diperlukan
4. **Performance**: Perbaikan relasi ini akan meningkatkan query performance dengan proper indexing

## 🎉 **HASIL AKHIR**

Setelah menjalankan perbaikan ini:
- ✅ Relasi foreign key akan terlihat di phpMyAdmin Designer
- ✅ Query data akan lebih efficient dengan proper joins
- ✅ Data integrity terjamin dengan proper constraints
- ✅ Banyak kemungkinan query baru dengan relasi yang benar

---

**Dibuat**: 22 Oktober 2025  
**Author**: Kilo Code  
**Version**: 1.0