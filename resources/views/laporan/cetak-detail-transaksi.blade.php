<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Transaksi</title>
    <style>
        @page {
            margin: 20mm;
            size: A4 portrait;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0;
            font-weight: bold;
        }

        .header p {
            font-size: 12px;
            margin: 5px 0;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
            background-color: #f5f5f5;
            padding: 5px;
            border: 1px solid #ddd;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 5px;
            border: 1px solid #ddd;
        }

        .info-table td:first-child {
            font-weight: bold;
            width: 30%;
            background-color: #f9f9f9;
        }

        .obat-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .obat-table th, .obat-table td {
            padding: 5px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .obat-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .total-section {
            text-align: right;
            margin-top: 20px;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .diagnosa-section {
            margin-bottom: 15px;
        }

        .diagnosa-title {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DETAIL TRANSAKSI PEMERIKSAAN</h1>
        <p>Klinik Poliklinik ICBP</p>
        <p>{{ date('d-m-Y H:i') }}</p>
    </div>

    <div class="info-section">
        <div class="info-title">INFORMASI PASIEN</div>
        <table class="info-table">
            <tr>
                <td>No. RM</td>
                <td>{{ $kunjungan->no_rm }}</td>
            </tr>
            <tr>
                <td>Nama Pasien</td>
                <td>{{ $kunjungan->nama_pasien }}</td>
            </tr>
            <tr>
                <td>Hubungan</td>
                <td>{{ $kunjungan->hubungan }}</td>
            </tr>
            <tr>
                <td>Tanggal Lahir</td>
                <td>{{ $rekamMedis->keluarga->tanggal_lahir->format('d-m-Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="info-section">
        <div class="info-title">INFORMASI KUNJUNGAN</div>
        <table class="info-table">
            <tr>
                <td>No. Registrasi</td>
                <td>{{ $kunjungan->kode_transaksi ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal Periksa</td>
                <td>{{ $rekamMedis->tanggal_periksa->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td>Petugas</td>
                <td>{{ $rekamMedis->user->nama_lengkap ?? '-' }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>{{ $rekamMedis->status }}</td>
            </tr>
        </table>
    </div>

    <div class="info-section">
        <div class="info-title">INFORMASI KARYAWAN YANG BERTANGGUNG JAWAB</div>
        <table class="info-table">
            <tr>
                <td>NIK</td>
                <td>{{ $rekamMedis->keluarga->karyawan->nik_karyawan ?? '-' }}</td>
            </tr>
            <tr>
                <td>Nama Karyawan</td>
                <td>{{ $rekamMedis->keluarga->karyawan->nama_karyawan ?? '-' }}</td>
            </tr>
            <tr>
                <td>Departemen</td>
                <td>{{ $rekamMedis->keluarga->karyawan->departemen->nama_departemen ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="info-section">
        <div class="info-title">DETAIL DIAGNOSA & OBAT</div>

        @forelse($keluhanByDiagnosa as $diagnosa => $keluhans)
            <div class="diagnosa-section">
                <div class="diagnosa-title">Diagnosa: {{ $diagnosa }}</div>
                <table class="obat-table">
                    <thead>
                        <tr>
                            <th width="30%">Obat</th>
                            <th width="15%">Jumlah</th>
                            <th width="30%">Aturan Pakai</th>
                            <th width="12%">Harga Satuan</th>
                            <th width="13%">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($keluhans as $keluhan)
                        <tr>
                            <td>{{ $keluhan->obat->nama_obat ?? '-' }}</td>
                            <td>{{ $keluhan->jumlah_obat ?? 0 }} {{ $keluhan->obat->satuanObat->nama_satuan ?? '' }}</td>
                            <td>{{ $keluhan->aturan_pakai ?: '-' }}</td>
                            <td>Rp{{ number_format($keluhan->obat->harga_per_satuan ?? 0, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format(($keluhan->jumlah_obat ?? 0) * ($keluhan->obat->harga_per_satuan ?? 0), 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <p>Tidak ada data diagnosa dan obat</p>
        @endforelse
    </div>

    <div class="total-section">
        <table width="100%">
            <tr>
                <td width="80%" align="right"><strong>Total Biaya Transaksi:</strong></td>
                <td width="20%" align="right"><strong>Rp{{ number_format($totalBiaya, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Laporan ini dicetak secara otomatis dari Sistem Informasi Poliklinik ICBP</p>
        <p>Halaman 1 dari 1</p>
    </div>
</body>
</html>
