# SIPO-ICBP - Sistem Informasi Poliklinik

## Kredensial Login

Berikut adalah akun yang sudah dibuat melalui seeder:

### 1. Super Admin
- **Username**: `superadmin`
- **Password**: `superadmin123`
- **Nama**: Super Administrator
- **Role**: Super Admin
- **Akses**: Full access ke seluruh sistem

### 2. Admin
- **Username**: `admin`
- **Password**: `admin123`
- **Nama**: Administrator
- **Role**: Admin
- **Akses**: Mengelola data pasien, rekam medis, dan obat

### 3. User
- **Username**: `user`
- **Password**: `user123`
- **Nama**: User Poliklinik
- **Role**: User
- **Akses**: Melihat dan mengelola data pasien

### 4. Dokter (Admin)
- **Username**: `dr.budi`
- **Password**: `dokter123`
- **Nama**: Dr. Budi Santoso
- **Role**: Admin
- **Akses**: Mengelola data pasien, rekam medis, dan obat

### 5. Perawat (User)
- **Username**: `perawat.ani`
- **Password**: `perawat123`
- **Nama**: Ani Wijaya
- **Role**: User
- **Akses**: Melihat dan mengelola data pasien

---

## Penjelasan Role

### Super Admin
- Memiliki akses penuh ke seluruh sistem
- Dapat mengelola user, master data, dan semua modul
- Biasanya untuk IT Administrator atau pemilik sistem

### Admin
- Dapat mengelola data pasien, rekam medis, resep obat
- Dapat mengelola data obat dan penyakit
- Biasanya untuk Dokter atau Kepala Klinik

### User
- Dapat melihat dan menambah data pasien
- Dapat melihat rekam medis
- Biasanya untuk Perawat atau Staff Administrasi

---

## Cara Menjalankan

1. **Clone repository**
   ```bash
   git clone <repository-url>
   cd sipo-icbp
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Copy file .env**
   ```bash
   cp .env.example .env
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Setup database**
   - Buat database baru
   - Update file `.env` dengan konfigurasi database Anda

6. **Jalankan migration dan seeder**
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Compile assets**
   ```bash
   npm run dev
   ```

8. **Jalankan server**
   ```bash
   php artisan serve
   ```

9. **Akses aplikasi**
   - Buka browser: `http://localhost:8000`
   - Login dengan salah satu kredensial di atas

---

## Fitur yang Sudah Dibuat

✅ Authentication System (Login/Logout)
✅ Role-based Access Control (Super Admin, Admin, User)
✅ Dashboard dengan informasi user
✅ Master Data Seeder:
  - Departemen (12 data)
  - Satuan Obat (8 data)
  - Jenis Obat (8 data)
  - Penyakit (42 data)
  - Obat (53 data)
  - Hubungan Keluarga (5 data)
  - User dengan berbagai role (5 data)

---

## Database Structure

- `departemen` - Data departemen karyawan
- `hubungan` - Data hubungan keluarga pasien
- `jenis_obat` - Jenis/kategori obat
- `satuan_obat` - Satuan kemasan obat
- `penyakit` - Master data penyakit
- `user` - Data user dengan role
- `karyawan` - Data karyawan ICBP
- `pasien` - Data pasien (karyawan + keluarga)
- `kunjungan` - Data kunjungan pasien
- `obat` - Master data obat
- `penyakit_obat` - Relasi penyakit dengan obat
- `rekam_medis` - Rekam medis pasien
- `resep_obat` - Resep obat untuk pasien

---

## Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Blade Templates + Tailwind CSS
- **Database**: MySQL/SQLite
- **Build Tool**: Vite

---

## Security Notes

⚠️ **PENTING**: Untuk production, pastikan untuk:
1. Ganti semua password default
2. Set `APP_ENV=production` di file `.env`
3. Set `APP_DEBUG=false` di file `.env`
4. Gunakan HTTPS
5. Enable CSRF protection
6. Backup database secara berkala
