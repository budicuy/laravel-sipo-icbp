<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi Emergency</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #e74c3c;
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 12px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        .info-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
        }
        .info-card h3 {
            margin: 0 0 10px;
            font-size: 14px;
            color: #e74c3c;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
        }
        .detail-section {
            margin-bottom: 20px;
        }
        .detail-section h3 {
            color: #e74c3c;
            margin-bottom: 10px;
            font-size: 14px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .diagnosa-group {
            margin-bottom: 15px;
            border: 1px solid #eee;
            border-radius: 5px;
        }
        .diagnosa-title {
            background-color: #f8f9fa;
            padding: 8px;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
        }
        .total-section {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            text-align: right;
        }
        .total-label {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .total-amount {
            font-size: 18px;
            font-weight: bold;
            color: #27ae60;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DETAIL TRANSAKSI EMERGENCY</h1>
        <p>Klinik PT. Industri Kapal Indonesia</p>
    </div>

    <div class="info-section">
        <div class="info-grid">
            <div class="info-card">
                <h3>Informasi Pasien</h3>
                <div class="info-row">
                    <span class="info-label">No. RM:</span>
                    <span>{{ $rekamMedisEmergency->no_rm }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nama Pasien:</span>
                    <span>{{ $rekamMedisEmergency->nama_pasien }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Hubungan:</span>
                    <span>{{ $rekamMedisEmergency->hubungan }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Alamat:</span>
                    <span>{{ $rekamMedisEmergency->externalEmployee->alamat ?? '-' }}</span>
                </div>
            </div>

            <div class="info-card">
                <h3>Informasi Kunjungan</h3>
                <div class="info-row">
                    <span class="info-label">No. Registrasi:</span>
                    <span>{{ $kodeTransaksi }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Periksa:</span>
                    <span>{{ $rekamMedisEmergency->tanggal_periksa->format('d-m-Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Petugas:</span>
                    <span>{{ $rekamMedisEmergency->user->nama_lengkap ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span>{{ $rekamMedisEmergency->status }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h3>Detail Diagnosa & Obat</h3>
        
        @forelse($keluhanByDiagnosa as $diagnosa => $keluhans)
        <div class="diagnosa-group">
            <div class="diagnosa-title">{{ $diagnosa }}</div>
            <table>
                <thead>
                    <tr>
                        <th width="30%">Obat</th>
                        <th width="15%">Jumlah</th>
                        <th width="25%">Aturan Pakai</th>
                        <th width="15%">Harga Satuan</th>
                        <th width="15%">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($keluhans as $keluhan)
                    <tr>
                        <td>{{ $keluhan->obat->nama_obat ?? '-' }}</td>
                        <td>{{ $keluhan->jumlah_obat ?? 0 }} {{ $keluhan->obat->satuanObat->nama_satuan ?? '' }}</td>
                        <td>{{ $keluhan->aturan_pakai ?: '-' }}</td>
                        <td>Rp{{ number_format($keluhan->harga_satuan ?? 0, 0, ',', '.') }}</td>
                        <td>Rp{{ number_format(($keluhan->jumlah_obat ?? 0) * ($keluhan->harga_satuan ?? 0), 0, ',', '.') }}</td>
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
        <div class="total-label">Total Biaya Transaksi Emergency:</div>
        <div class="total-amount">Rp{{ number_format($totalBiaya, 0, ',', '.') }}</div>
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dari sistem pada tanggal {{ now()->format('d-m-Y H:i:s') }}</p>
    </div>
</body>
</html>