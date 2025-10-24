# Dokumentasi Perbaikan Fitur Import Rekam Medis

## Overview

Fitur import rekam medis telah diperbaiki untuk mendukung format diagnosa tunggal, double, dan triple. Sistem sekarang dapat secara otomatis mendeteksi format Excel yang diunggah dan memproses data sesuai dengan jumlah diagnosa yang ada.

## Perbaikan yang Dilakukan

### 1. Perbaikan Logika Import

**Masalah Sebelumnya:**
- Logika import untuk format single-diagnosa menggunakan variabel yang tidak didefinisikan (`$diagnosa`, `$obat1`, dll)
- Deteksi format multi-diagnosa tidak akurat
- Penanganan data untuk format single-diagnosa tidak konsisten

**Solusi:**
- Memperbaiki variabel yang digunakan dalam format single-diagnosa
- Meningkatkan akurasi deteksi format dengan memeriksa header kolom yang benar
- Menstandardisasi logika processing untuk semua format

### 2. Update Template Excel

**Perubahan Template:**
- Memperbaiki header kolom (Qyt → Qty)
- Menambahkan contoh data untuk ketiga format:
  - Diagnosa Tunggal (hanya Diagnosa 1 yang diisi)
  - Diagnosa Double (Diagnosa 1 & 2 yang diisi)
  - Diagnosa Triple (ketiga Diagnosa diisi)
- Memperbaiki catatan penggunaan template

### 3. Format Kolom Excel

Format kolom yang didukung (sesuai requirement):

| Kolom | Deskripsi |
|--------|----------|
| A | Hari / Tgl (DD/MM/YYYY) |
| B | Time (HH:MM) |
| C | NIK Karyawan |
| D | Nama Karyawan |
| E | Kode RM |
| F | Nama Pasien |
| G | Diagnosa 1 |
| H | Keluhan 1 |
| I | Obat 1-1 |
| J | Qty |
| K | Obat 1-2 |
| L | Qty |
| M | Obat 1-3 |
| N | Qty |
| O | Diagnosa 2 |
| P | Keluhan 2 |
| Q | Obat 2-1 |
| R | Qty |
| S | Obat 2-2 |
| T | Qty |
| U | Obat 2-3 |
| V | Qty |
| W | Diagnosa 3 |
| X | Keluhan 3 |
| Y | Obat 3-1 |
| Z | Qty |
| AA | Obat 3-2 |
| AB | Qty |
| AC | Obat 3-3 |
| AD | Qty |
| AE | Petugas |
| AF | Status |

## Cara Penggunaan

### 1. Download Template
1. Buka menu Rekam Medis
2. Klik tombol "Import Excel"
3. Klik "Download Template"
4. Template Excel akan terunduh dengan format yang benar

### 2. Mengisi Data
**Untuk Diagnosa Tunggal:**
- Isi kolom A-F (data dasar pasien)
- Isi Diagnosa 1 (kolom G) dan Keluhan 1 (kolom H)
- Isi Obat 1-1 hingga Obat 1-3 (kolom I, K, M) beserta qty-nya
- Biarkan kolom Diagnosa 2 & 3 kosong (isi dengan "-")

**Untuk Diagnosa Double:**
- Isi data dasar pasien (kolom A-F)
- Isi Diagnosa 1 & 2 beserta keluhan dan obatnya
- Biarkan Diagnosa 3 kosong

**Untuk Diagnosa Triple:**
- Isi semua kolom termasuk Diagnosa 1, 2, & 3
- Setiap diagnosa dapat memiliki hingga 3 obat

### 3. Import Data
1. Buka menu Rekam Medis
2. Klik tombol "Import Excel"
3. Pilih file Excel yang telah diisi
4. Sistem akan otomatis mendeteksi format dan memproses data
5. Hasil import akan ditampilkan dengan notifikasi sukses/error

## Validasi Data

Sistem akan melakukan validasi berikut:
- Format tanggal harus DD/MM/YYYY
- NIK karyawan harus ada di database
- Nama pasien harus cocok dengan data keluarga
- Nama obat harus ada di database
- Status harus "Close", "On Progress", atau "Reguler"

## Error Handling

Jika terjadi error saat import:
- Sistem akan menampilkan baris dan pesan error yang spesifik
- Data yang valid tetap akan diimport
- Error ditampilkan maksimal 10 pesan pertama untuk menghindari overflow

## Testing

File test telah disediakan:
- `test_import_rekam_medis_[timestamp].xlsx` - Berisi 3 contoh data (tunggal, double, triple)
- `test_import_rekam_medis.php` - Script untuk generate file test

## Catatan Teknis

### Deteksi Format
Sistem mendeteksi format berdasarkan:
1. Jumlah kolom (harus sampai kolom AF)
2. Header kolom (harus mengandung "Diagnosa 1", "Diagnosa 2", "Diagnosa 3")

### Processing Logic
- **Multi-diagnosa format**: Memproses semua diagnosa yang diisi (1-3)
- **Single-diagnosa format**: Hanya memproses Diagnosa 1 (kolom G-N)

### Database Structure
Data disimpan dalam tabel:
- `rekam_medis`: Data utama pemeriksaan
- `keluhan`: Data detail diagnosa dan obat (multiple records per rekam medis)

## Troubleshooting

### Import Gagal
1. Periksa format file (harus .xlsx atau .xls)
2. Pastikan semua header kolom sesuai template
3. Validasi data required (tanggal, NIK, nama pasien)
4. Cek log error untuk detail masalah

### Data Tidak Sesuai
1. Pastikan NIK karyawan ada di tabel karyawan
2. Verifikasi nama pasien cocok dengan data keluarga
3. Cek nama obat di tabel obat
4. Validasi format tanggal dan waktu

## Future Enhancements

1. **Auto-detection improvement**: Enhanced format detection algorithm
2. **Batch processing**: Support for larger files with progress indicator
3. **Data validation preview**: Show validation errors before import
4. **Export failed records**: Export failed records for correction
5. **Import history**: Track import history with rollback capability

## Support

Untuk bantuan lebih lanjut, hubungi tim IT atau lihat dokumentasi lengkap di sistem SIPO ICBP.