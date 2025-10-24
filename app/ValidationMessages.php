<?php

namespace App;

class ValidationMessages
{
    /**
     * Pesan validasi umum yang digunakan di seluruh aplikasi
     */
    public static function getMessages()
    {
        return [
            // Pesan validasi umum
            'required' => ':attribute harus diisi',
            'string' => ':attribute harus berupa teks',
            'integer' => ':attribute harus berupa angka',
            'numeric' => ':attribute harus berupa angka',
            'array' => ':attribute harus berupa array',
            'boolean' => ':attribute harus bernilai true atau false',
            'email' => ':attribute harus berupa alamat email yang valid',
            'unique' => ':attribute sudah digunakan',
            'exists' => ':attribute yang dipilih tidak valid',
            'min' => [
                'string' => ':attribute minimal :min karakter',
                'numeric' => ':attribute minimal :min',
                'array' => ':attribute minimal :min item',
                'file' => ':attribute minimal :min kilobyte',
            ],
            'max' => [
                'string' => ':attribute maksimal :max karakter',
                'numeric' => ':attribute maksimal :max',
                'array' => ':attribute maksimal :max item',
                'file' => ':attribute maksimal :max kilobyte',
            ],
            'between' => [
                'numeric' => ':attribute harus antara :min dan :max',
                'string' => ':attribute harus antara :min dan :max karakter',
            ],
            'date' => 'Format :attribute tidak valid. Gunakan format YYYY-MM-DD',
            'date_format' => 'Format :attribute tidak valid. Gunakan format :format',
            'in' => ':attribute harus :values',
            'digits_between' => ':attribute harus terdiri dari :min hingga :max digit',
            'mimes' => ':attribute harus berupa file dengan tipe: :values',
            'file' => ':attribute harus berupa file',

            // Pesan validasi khusus untuk atribut tertentu
            'id_keluarga.required' => 'Pasien harus dipilih',
            'id_keluarga.exists' => 'Pasien yang dipilih tidak valid',
            'id_karyawan.required' => 'Karyawan harus dipilih',
            'id_karyawan.exists' => 'Karyawan yang dipilih tidak valid',
            'id_diagnosa.required' => 'Diagnosa harus dipilih',
            'id_diagnosa.exists' => 'Diagnosa yang dipilih tidak valid',
            'id_diagnosa_emergency.required' => 'Diagnosa emergency harus dipilih',
            'id_diagnosa_emergency.exists' => 'Diagnosa emergency yang dipilih tidak valid',
            'id_obat.required' => 'Obat harus dipilih',
            'id_obat.exists' => 'Obat yang dipilih tidak valid',
            'external_employee_id.required' => 'Karyawan emergency harus dipilih',
            'external_employee_id.exists' => 'Karyawan emergency yang dipilih tidak valid',

            // Pesan validasi untuk tanggal dan waktu
            'tanggal_periksa.required' => 'Tanggal periksa harus diisi',
            'tanggal_periksa.date' => 'Format tanggal tidak valid. Gunakan format YYYY-MM-DD',
            'waktu_periksa.date_format' => 'Format waktu tidak valid. Gunakan format HH:MM',

            // Pesan validasi untuk status
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status harus "On Progress" atau "Close"',
            'status_rekam_medis.required' => 'Status rekam medis harus dipilih',
            'status_rekam_medis.in' => 'Status rekam medis harus "On Progress" atau "Close"',

            // Pesan validasi untuk jumlah
            'jumlah_keluhan.required' => 'Jumlah keluhan harus diisi',
            'jumlah_keluhan.integer' => 'Jumlah keluhan harus berupa angka',
            'jumlah_keluhan.min' => 'Jumlah keluhan minimal 1',
            'jumlah_keluhan.max' => 'Jumlah keluhan maksimal 3',
            'jumlah_obat.integer' => 'Jumlah obat harus berupa angka',
            'jumlah_obat.min' => 'Jumlah obat minimal 1',
            'jumlah_obat.max' => 'Jumlah obat maksimal 10000',

            // Pesan validasi untuk terapi
            'terapi.required' => 'Terapi harus dipilih',
            'terapi.in' => 'Terapi harus "Obat", "Lab", atau "Istirahat"',
            'terapi.string' => 'Terapi harus berupa teks',

            // Pesan validasi untuk keluhan
            'keluhan.required' => 'Keluhan harus diisi',
            'keluhan.string' => 'Keluhan harus berupa teks',

            // Pesan validasi untuk keterangan
            'keterangan.string' => 'Keterangan harus berupa teks',

            // Pesan validasi untuk aturan pakai
            'aturan_pakai.string' => 'Aturan pakai harus berupa teks',

            // Pesan validasi untuk catatan
            'catatan.string' => 'Catatan harus berupa teks',

            // Pesan validasi untuk NIK
            'nik.required' => 'NIK harus diisi',
            'nik.numeric' => 'NIK harus berupa angka',
            'nik.unique' => 'NIK sudah digunakan',
            'nik_pasien.required' => 'NIK pasien harus diisi',
            'nik_pasien.digits_between' => 'NIK pasien harus terdiri dari 1-16 digit angka',
            'nik_pasien.numeric' => 'NIK pasien harus berupa angka',

            // Pesan validasi untuk nama
            'nama.required' => 'Nama harus diisi',
            'nama.string' => 'Nama harus berupa teks',
            'nama.max' => 'Nama maksimal 255 karakter',
            'nama_pasien.required' => 'Nama pasien harus diisi',
            'nama_pasien.string' => 'Nama pasien harus berupa teks',
            'nama_pasien.max' => 'Nama pasien maksimal 255 karakter',
            'nama_lengkap.required' => 'Nama lengkap harus diisi',
            'nama_lengkap.string' => 'Nama lengkap harus berupa teks',
            'nama_lengkap.max' => 'Nama lengkap maksimal 255 karakter',

            // Pesan validasi untuk No. RM
            'no_rm.required' => 'No. RM harus diisi',
            'no_rm.string' => 'No. RM harus berupa teks',
            'no_rm.max' => 'No. RM maksimal 30 karakter',

            // Pesan validasi untuk jenis kelamin
            'jenis_kelamin.required' => 'Jenis kelamin harus dipilih',
            'jenis_kelamin.in' => 'Jenis kelamin harus "L" atau "P"',

            // Pesan validasi untuk username
            'username.required' => 'Username harus diisi',
            'username.string' => 'Username harus berupa teks',
            'username.max' => 'Username maksimal 50 karakter',
            'username.unique' => 'Username sudah digunakan',

            // Pesan validasi untuk password
            'password.required' => 'Password harus diisi',
            'password.string' => 'Password harus berupa teks',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',

            // Pesan validasi untuk file
            'file.required' => 'File harus dipilih',
            'file.mimes' => 'File harus berformat :values',
            'file.max' => 'Ukuran file maksimal :max kilobyte',

            // Pesan validasi untuk token
            'token.required' => 'Token harus diisi',
            'token.string' => 'Token harus berupa teks',

            // Pesan validasi untuk array
            'ids.required' => 'Item harus dipilih',
            'ids.array' => 'Item harus berupa array',
            'obat_list.array' => 'Daftar obat harus berupa array',
            'obat_list.*.id_obat.required' => 'Obat harus dipilih',
            'obat_list.*.id_obat.exists' => 'Obat yang dipilih tidak valid',
            'keluhan.*.id_diagnosa.required' => 'Diagnosa harus dipilih',
            'keluhan.*.id_diagnosa.exists' => 'Diagnosa yang dipilih tidak valid',
            'keluhan.*.terapi.required' => 'Terapi harus dipilih',
            'keluhan.*.terapi.in' => 'Terapi harus "Obat", "Lab", atau "Istirahat"',
        ];
    }

    /**
     * Mendapatkan pesan validasi untuk atribut tertentu
     */
    public static function getAttributeMessages()
    {
        return [
            'id_keluarga' => 'Pasien',
            'id_karyawan' => 'Karyawan',
            'id_diagnosa' => 'Diagnosa',
            'id_diagnosa_emergency' => 'Diagnosa Emergency',
            'id_obat' => 'Obat',
            'external_employee_id' => 'Karyawan Emergency',
            'tanggal_periksa' => 'Tanggal Periksa',
            'waktu_periksa' => 'Waktu Periksa',
            'status' => 'Status',
            'status_rekam_medis' => 'Status Rekam Medis',
            'jumlah_keluhan' => 'Jumlah Keluhan',
            'jumlah_obat' => 'Jumlah Obat',
            'terapi' => 'Terapi',
            'keluhan' => 'Keluhan',
            'keterangan' => 'Keterangan',
            'aturan_pakai' => 'Aturan Pakai',
            'catatan' => 'Catatan',
            'nik' => 'NIK',
            'nik_pasien' => 'NIK Pasien',
            'nama' => 'Nama',
            'nama_pasien' => 'Nama Pasien',
            'nama_lengkap' => 'Nama Lengkap',
            'no_rm' => 'No. RM',
            'jenis_kelamin' => 'Jenis Kelamin',
            'username' => 'Username',
            'password' => 'Password',
            'file' => 'File',
            'token' => 'Token',
            'ids' => 'Item',
            'obat_list' => 'Daftar Obat',
        ];
    }
}
