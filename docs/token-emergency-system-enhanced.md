# Sistem Token Emergency - Dokumentasi

## Ringkasan

Sistem Token Emergency adalah fitur yang memungkinkan pengguna untuk mengakses rekam medis emergency menggunakan token unik. Sistem ini telah ditingkatkan dengan fitur manajemen token yang lebih komprehensif, termasuk permintaan token, monitoring, dan audit trail.

## Fitur-Fitur

### 1. Generate Token
- Admin dapat generate token dalam jumlah banyak
- Token dapat ditetapkan ke pengguna spesifik atau digunakan secara umum
- Panjang token dapat dikonfigurasi (4-6 digit)
- Setiap token unik dan dapat digunakan sekali saja

### 2. Validasi Token
- Pengguna dapat memasukkan token untuk mengakses rekam medis emergency
- Token divalidasi secara real-time
- Token yang valid akan disimpan di session

### 3. Permintaan Token
- Pengguna dapat mengajukan permintaan token
- Admin dapat menyetujui atau menolak permintaan
- Notifikasi permintaan ditampilkan di navbar dan sidebar

### 4. Monitoring Token
- Admin dapat memantau status token secara real-time
- Menampilkan pengguna dengan token rendah
- Menampilkan permintaan token yang menunggu
- Statistik token tersedia dan digunakan

### 5. Audit Trail
- Menyimpan riwayat semua aktivitas token
- Dapat difilter berdasarkan tanggal, status, dan pengguna
- Dapat diekspor ke format CSV

## Struktur Database

### Tabel token_emergency
- `id_token`: Primary key
- `token`: Token unik (4-6 digit)
- `status`: Status token (available, used, expired)
- `id_user`: ID pengguna yang menggunakan token
- `used_at`: Waktu token digunakan
- `generated_by`: ID admin yang generate token
- `requested_by`: ID pengguna yang meminta token
- `request_quantity`: Jumlah token yang diminta
- `request_status`: Status permintaan (pending, approved, rejected)
- `request_approved_at`: Waktu permintaan disetujui
- `request_approved_by`: ID admin yang menyetujui permintaan
- `notes`: Catatan tambahan
- `created_at`: Waktu dibuat
- `updated_at`: Waktu diperbarui

## Routes

### Token Emergency Routes
- `GET /token-emergency` - Daftar token
- `GET /token-emergency/create` - Form generate token
- `POST /token-emergency/generate` - Generate token
- `GET /token-emergency/validate` - Form validasi token
- `POST /token-emergency/validate` - Validasi token
- `DELETE /token-emergency/{id}` - Hapus token
- `POST /token-emergency/clear` - Hapus token dari session

### Token Request Routes
- `GET /token-emergency/request` - Form permintaan token
- `POST /token-emergency/request` - Simpan permintaan token

### Token Management Routes (Admin/Super Admin only)
- `GET /token-emergency/pending-requests` - Daftar permintaan menunggu
- `POST /token-emergency/approve-request/{id}` - Setujui permintaan
- `POST /token-emergency/reject-request/{id}` - Tolak permintaan
- `GET /token-emergency/monitoring` - Monitoring token
- `GET /token-emergency/audit-trail` - Audit trail
- `GET /token-emergency/user-profile/{userId}` - Profil pengguna

## Model

### TokenEmergency
- `generateMultipleTokens($count, $length, $userId = null, $generatedBy = null)` - Generate multiple token
- `isValidToken($token)` - Validasi token
- `getUserTokens($userId = null)` - Get token pengguna
- `getUsersWithLowTokens($threshold = 5)` - Get pengguna dengan token rendah
- `getPendingRequestsCount()` - Get jumlah permintaan menunggu
- `getAuditTrail()` - Get audit trail

## Controller

### TokenEmergencyController
- `index()` - Daftar token
- `create()` - Form generate token
- `generate(Request $request)` - Generate token
- `validateForm()` - Form validasi token
- `validateToken(Request $request)` - Validasi token
- `destroy($id)` - Hapus token
- `clearToken()` - Hapus token dari session
- `requestForm()` - Form permintaan token
- `storeRequest(Request $request)` - Simpan permintaan token
- `pendingRequests()` - Daftar permintaan menunggu
- `approveRequest(Request $request, $id)` - Setujui permintaan
- `rejectRequest(Request $request, $id)` - Tolak permintaan
- `monitoring()` - Monitoring token
- `auditTrail()` - Audit trail
- `userProfile($userId)` - Profil pengguna

## View

### Token Emergency Views
- `token-emergency/index.blade.php` - Daftar token
- `token-emergency/create.blade.php` - Form generate token
- `token-emergency/validate.blade.php` - Form validasi token
- `token-emergency/request.blade.php` - Form permintaan token
- `token-emergency/pending-requests.blade.php` - Daftar permintaan menunggu
- `token-emergency/monitoring.blade.php` - Monitoring token
- `token-emergency/audit-trail.blade.php` - Audit trail

## Komponen UI

### Sidebar
- Menu Kelola Token Emergency
- Menu Monitoring Token
- Menu Permintaan Token (dengan badge notifikasi)
- Menu Audit Trail

### Navbar
- Notifikasi permintaan token (untuk admin/super admin)

## JavaScript

### Token Modal
- Modal popup validasi token dengan SweetAlert2
- Integrasi dengan Alpine.js

## Cara Penggunaan

### 1. Generate Token (Admin)
1. Buka menu "Kelola Token Emergency" > "Generate Token"
2. Pilih jumlah token yang akan dibuat
3. Pilih panjang token (4-6 digit)
4. Opsional: Tetapkan token ke pengguna spesifik
5. Opsional: Tambahkan catatan
6. Klik "Generate Token"

### 2. Validasi Token (Pengguna)
1. Buka menu "Rekam Medis" > "Tambah Rekam Medis"
2. Pilih "Rekam Medis Emergency"
3. Masukkan token yang valid
4. Klik "Validasi Token"
5. Jika token valid, akan diarahkan ke form tambah rekam medis emergency

### 3. Permintaan Token (Pengguna)
1. Buka menu "Token Emergency" > "Permintaan Token"
2. Masukkan jumlah token yang dibutuhkan
3. Opsional: Tambahkan alasan permintaan
4. Klik "Ajukan Permintaan"
5. Tunggu persetujuan dari admin

### 4. Monitoring Token (Admin)
1. Buka menu "Monitoring Token"
2. Lihat statistik token
3. Lihat pengguna dengan token rendah
4. Lihat permintaan token yang menunggu
5. Lakukan aksi yang diperlukan

### 5. Audit Trail (Admin)
1. Buka menu "Audit Trail"
2. Lihat riwayat aktivitas token
3. Filter berdasarkan tanggal, status, atau pengguna
4. Opsional: Export data ke CSV

## Keamanan

- Token bersifat unik dan tidak dapat diprediksi
- Token hanya dapat digunakan sekali
- Token yang sudah digunakan akan dinonaktifkan
- Akses ke fitur manajemen token dibatasi untuk admin/super admin
- Semua aktivitas token dicatat dalam audit trail

## Troubleshooting

### Token tidak valid
- Pastikan token dimasukkan dengan benar
- Pastikan token belum digunakan
- Pastikan token belum kadaluarsa

### Tidak dapat generate token
- Pastikan Anda memiliki akses admin/super admin
- Pastikan jumlah token yang diminta tidak melebihi batas

### Permintaan token tidak disetujui
- Pastikan alasan permintaan jelas
- Hubungi admin untuk informasi lebih lanjut
