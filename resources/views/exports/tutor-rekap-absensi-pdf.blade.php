<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Tutor - {{ $namaBulan }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #1a1a1a;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 12px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .info-table .label {
            font-weight: bold;
            width: 100px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th, .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #f4f4f4;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
        }
        .signature {
            display: inline-block;
            text-align: center;
            width: 200px;
        }
        .signature p {
            margin: 0;
        }
        .signature .name {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>REKAPITULASI KEHADIRAN PESERTA</h1>
        <p>E-Presensi CEC Mataram</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Tutor</td>
            <td>: {{ $tutor->nama }}</td>
            <td class="label">Bulan/Tahun</td>
            <td>: {{ $namaBulan }}</td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td>: {{ $namaKelas }}</td>
            <td class="label">Dicetak Pada</td>
            <td>: {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y, HH:mm') }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%">Nama Peserta</th>
                <th width="20%">Kelas</th>
                <th width="10%">Hadir</th>
                <th width="10%">Izin</th>
                <th width="10%">Sakit</th>
                <th width="10%">Alpha</th>
                <th width="15%">Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapPeserta as $index => $r)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $r['nama'] }}</td>
                <td>{{ $r['kelas'] }}</td>
                <td class="text-center">{{ $r['hadir'] }}</td>
                <td class="text-center">{{ $r['izin'] }}</td>
                <td class="text-center">{{ $r['sakit'] }}</td>
                <td class="text-center">{{ $r['alpa'] }}</td>
                <td class="text-center">{{ $r['persentase'] }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada data kehadiran peserta pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p>Mataram, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
            <p>Tutor Pengajar,</p>
            <div class="name">{{ $tutor->nama }}</div>
        </div>
    </div>

</body>
</html>
