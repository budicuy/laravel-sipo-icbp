<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pengantar Istirahat - {{ $suratPengantar->nomor_surat }}</title>
    <style>
        @page {
            margin: 2cm;
            size: A4;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            margin: 0;
        }


        .header h1 {
            margin: 0;
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 14pt;
            font-weight: normal;
        }

        .header h3 {
            margin: 5px 0;
            font-size: 11pt;
            font-weight: normal;
        }

        .header-underline {
            border-bottom: 3px solid #000;
            margin: 10px 0;
        }

        .letter-info {
            margin: 20px 0;
        }

        .letter-info p {
            margin: 5px 0;
        }

        .content {
            text-align: justify;
            margin: 20px 0;
        }

        .content p {
            margin: 10px 0;
            text-indent: 50px;
        }

        .patient-info {
            margin: 20px 0 20px 50px;
        }

        .patient-info td {
            padding: 5px 10px;
        }

        .patient-info td:first-child {
            width: 200px;
            vertical-align: top;
        }

        .closing {
            margin-top: 30px;
        }

        .signature {
            text-align: center;
            margin-top: 80px;
        }

        .signature-table {
            width: 100%;
            margin-top: 30px;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-box {
            margin-top: 80px;
            border-bottom: 1px solid #000;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9pt;
        }

        .no-print {
            display: none;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }

        .qr-code-container {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            border-top: 2px solid #000;
        }

        .qr-code-container p {
            margin: 10px 0;
            font-size: 10pt;
            text-indent: 0;
        }
    </style>
</head>

<body style="position: relative;">
    <!-- Watermark Logo -->
    <div
        style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); opacity: 0.1; z-index: -1; width: 80%;">
        <img src="{{ public_path('indofood.png') }}" alt="Watermark" style="width: 100%; height: auto;">
    </div>

    <div class="header" style="line-height: -30px">
        <h1>PT. INDOFOOD CBP SUKSES MAKMUR TBK</h1>
        <span>Jalan Ayani KM. 32 Liang Anggang, Pandahan, Kec. Bati Bati, Kabupaten Tanah Laut, Kalimantan Selatan -
            70852
        </span>
        <span>Telp: +0511 4787 981, Email : noodle.banjarmasin@gmail.com</span>
        <div class="header-underline"></div>
    </div>
    <br>
    <div class="letter-info" style="margin-top: -20px;">
        <p style="text-align: left;">
            <strong>Nomor</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $suratPengantar->nomor_surat }}<br>
            <strong>Lampiran</strong>&nbsp;: -<br>
            <strong>Perihal</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <u>Surat Pengantar Istirahat</u>
        </p>
    </div>

    <div class="content">
        <p style="text-indent: 0; margin-bottom: 20px;">
            Kepada Yth.<br>
            <strong>HRD PT. Indofood CBP Sukses Makmur Tbk</strong><br>
            Di tempat
        </p>
        <p>Dengan hormat,</p>
        <p>
            Yang bertanda tangan di bawah ini, petugas medis Poliklinik PT. Indofood CBP Sukses Makmur Tbk Divisi
            Noodle Cabang Semarang, menerangkan bahwa:
        </p>

        <table class="patient-info">
            <tr>
                <td>Nama</td>
                <td>: <strong>{{ $suratPengantar->nama_pasien }}</strong></td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>:
                    <strong>{{ $suratPengantar->nik_karyawan_penanggung_jawab }}</strong>
                </td>
            </tr>
            <tr>
                <td>Diagnosa</td>
                <td>: {{ is_array($suratPengantar->diagnosa) ? implode(', ', $suratPengantar->diagnosa) :
                    $suratPengantar->diagnosa }}</td>
            </tr>
            @if($suratPengantar->catatan)
            <tr>
                <td>Catatan</td>
                <td>: {{ $suratPengantar->catatan }}</td>
            </tr>
            @endif
        </table>

        <p>
            Berdasarkan pemeriksaan medis yang telah dilakukan pada tanggal
            {{ \Carbon\Carbon::parse($suratPengantar->tanggal_pengantar)->format('d F Y') }},
            yang bersangkutan memerlukan <strong>istirahat sakit selama {{ $suratPengantar->lama_istirahat }}
                hari</strong>,
            terhitung mulai tanggal
            <strong>{{ \Carbon\Carbon::parse($suratPengantar->tanggal_mulai_istirahat)->format('d F Y') }}</strong>
            sampai dengan
            <strong>{{
                \Carbon\Carbon::parse($suratPengantar->tanggal_mulai_istirahat)->addDays($suratPengantar->lama_istirahat
                - 1)->format('d F Y') }}</strong>.
        </p>

        <p>
            Demikian surat pengantar ini dibuat untuk dapat digunakan sebagaimana mestinya. Atas perhatian dan
            kerjasamanya, kami ucapkan terima kasih.
        </p>
    </div>

    <!-- QR Code Section -->
    <div class="">
        <p><strong>Verifikasi Keaslian Surat</strong></p>
        @if($suratPengantar->qrcode_path)
        <img src="{{ storage_path('app/public/' . $suratPengantar->qrcode_path) }}" alt="QR Code"
            style="width: 100px; height: 100px;">
        @endif
        <p style="font-size: 9pt; color: #666;">Scan QR Code untuk memverifikasi keaslian surat ini</p>
    </div>

</body>

</html>