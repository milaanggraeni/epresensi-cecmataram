<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kehadiran Peserta</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-top: 40px;
            margin-bottom: 20px;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 16px;
            margin-bottom: 5px;
            color: #000;
        }
        
        .header p {
            font-size: 10px;
            color: #666;
        }
        
        .info-section {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        
        .info-item {
            flex: 1;
        }
        
        .info-item label {
            font-weight: bold;
            color: #4472C4;
            display: block;
            margin-bottom: 3px;
        }
        
        .info-item value {
            display: block;
            padding: 5px;
            background: #f5f5f5;
            border-left: 3px solid #4472C4;
            padding-left: 10px;
        }
        
        .table-section {
            margin-top: 20px;
        }
        

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table thead {
            background: #4472C4;
            color: white;
        }
        
        table th {
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #999;
            font-size: 10px;
        }
        
        table td {
            padding: 7px 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        table tbody tr:hover {
            background: #f0f0f0;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer {
            margin-top: 30px;
            border-top: 2px solid #ddd;
            padding-top: 15px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            font-size: 10px;
        }
        
        .footer-item {
            text-align: center;
        }
        
        .footer-item-title {
            margin-bottom: 30px;
            font-weight: bold;
        }
        
        .footer-item-sign {
            display: inline-block;
            width: 60px;
            height: 1px;
            background: #000;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .note {
            font-size: 9px;
            color: #666;
            margin-top: 10px;
            padding: 10px;
            background: #f5f5f5;
            border-left: 3px solid #ffc107;
        }
        
        .note strong {
            color: #333;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1 style="font-size: 22px; font-weight: bold; text-transform: uppercase;">Laporan Rekapitulasi
            Presensi Peserta CEC Kampung Inggris Pare Mataram</h1>
        <hr style="border: none; border-top: 2px solid #000000ff; margin: 15px auto; width: 80%;">
        <p style="font-size: 11px; margin-bottom: 8px;">
          Jl. Abdul Kadir Munsyi Gang Dahlia No. 16, Punia, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat. 83115<br>
            Telp: +62 823 4031 1694 | Email: cecoffice9@gmail.com | Web: www.cecmataram.com
        </p>
        <p>Periode: {{ $namaBulan }} | Hari Efektif: {{ $hariEfektif }} Hari</p>
        @if($selectedKelas)
            <p>Kelas: <strong>{{ $selectedKelas->nama_kelas }}</strong></p>
        @else
            <p>Kelas: <strong>Semua Kelas</strong></p>
        @endif
    </div>
    
    {{-- Main Table --}}
    <div class="table-section">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Peserta</th>
                    <th>Kelas</th>
                    <th class="text-center">Hadir</th>
                    <th class="text-center">Izin</th>
                    <th class="text-center">Sakit</th>
                    <th class="text-center">Alpa</th>
                    <th class="text-center">Persentase (%)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapPeserta as $index => $item)
                    @php
                        $persentase = $hariEfektif > 0 ? round(($item['hadir'] / $hariEfektif) * 100, 2) : 0;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item['peserta']->nama }}</td>
                        <td class="text-center">{{ $item['peserta']->kelas->nama_kelas ?? '-' }}</td>
                        <td class="text-center">{{ $item['hadir'] }}</td>
                        <td class="text-center">{{ $item['izin'] }}</td>
                        <td class="text-center">{{ $item['sakit'] }}</td>
                        <td class="text-center">{{ $item['alpa'] }}</td>
                        <td class="text-center"><strong>{{ $persentase }}%</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Keterangan --}}
    <div class="note">
        <strong>Keterangan:</strong><br>
        H = Hadir | I = Izin | S = Sakit | A = Alpa<br>
        Persentase kehadiran dihitung dari: (Hadir / Hari Efektif) × 100%
    </div>
    
    {{-- Footer dengan Tanda Tangan --}}
    <div class="footer">
        <div class="footer-item">
            <div class="footer-item-title">Kepala Sekolah</div>
            <div class="footer-item-sign"></div>
        </div>
        <div class="footer-item">
            <div class="footer-item-title">Dicetak oleh Admin</div>
            <div class="footer-item-sign"></div>
        </div>
        <div class="footer-item">
            <div class="footer-item-title">Tanggal: {{ date('d/m/Y') }}</div>
            <div class="footer-item-sign"></div>
        </div>
    </div>
</body>
</html>
