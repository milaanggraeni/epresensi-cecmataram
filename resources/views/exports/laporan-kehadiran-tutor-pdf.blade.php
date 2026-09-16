<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kehadiran Tutor</title>
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
            margin-bottom: 20px;
        }
        
        .table-section h3 {
            font-size: 12px;
            margin-bottom: 10px;
            color: #4472C4;
            border-bottom: 2px solid #4472C4;
            padding-bottom: 5px;
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
        
        .tutor-block {
            page-break-inside: avoid;
            margin-bottom: 25px;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #4472C4;
        }
        
        .tutor-name {
            font-size: 12px;
            font-weight: bold;
            color: #4472C4;
            margin-bottom: 10px;
        }
        
        .tutor-info {
            display: flex;
            gap: 20px;
            margin-bottom: 10px;
            font-size: 10px;
        }
        
        .tutor-info-item {
            flex: 1;
        }
        
        .tutor-info-label {
            font-weight: bold;
            color: #666;
        }
        
        .tutor-info-value {
            color: #333;
        }
        
        .detail-table {
            margin-top: 10px;
        }
        
        .detail-table table {
            margin-bottom: 10px;
        }
        
        .detail-table th {
            background: #e8e8e8;
            color: #333;
            font-size: 9px;
        }
        
        .detail-table td {
            font-size: 9px;
            padding: 5px;
        }
        
        .footer {
            margin-top: 40px;
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
        <h1 style="font-size: 22px; font-weight: bold; text-transform: uppercase;">CEC Kampung Inggris Pare Mataram</h1>
        <hr style="border: none; border-top: 2px solid #000000ff; margin: 15px auto; width: 80%;">
        <p style="font-size: 11px; margin-bottom: 8px;">
           Jl. Abdul Kadir Munsyi Gang Dahlia No. 16, Punia, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat. 83115<br>
            Telp: +62 823 4031 1694 | Email: cecoffice9@gmail.com | Web: www.cecmataram.com
        </p>
        <p>Periode: {{ $namaBulan }}</p>
        @if($namaTutor)
            <p>Pencarian: <strong>{{ $namaTutor }}</strong></p>
        @endif
    </div>
    
    {{-- Tutor List --}}
    <div class="table-section">
        <h3>Daftar Tutor dan Jadwal Mengajar</h3>
        
        @forelse($rekapTutor as $index => $item)
            <div class="tutor-block">
                <div class="tutor-name">{{ $index + 1 }}. {{ $item['tutor']->nama }}</div>
                
                <div class="tutor-info">
                    <div class="tutor-info-item">
                        <span class="tutor-info-label">Jadwal per Minggu:</span>
                        <span class="tutor-info-value"><strong>{{ $item['total_jadwal_per_minggu'] }} mata pelajaran</strong></span>
                    </div>
                    <div class="tutor-info-item">
                        <span class="tutor-info-label">Total Hari Mengajar Bulan Ini:</span>
                        <span class="tutor-info-value"><strong>{{ $item['total_hari_mengajar'] }} hari</strong></span>
                    </div>
                </div>
                
                @if(count($item['detail_jadwal']) > 0)
                    <div class="detail-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Hari</th>
                                    <th>Kelas</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Jam</th>
                                    <th class="text-center">Frekuensi Bulan Ini</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($item['detail_jadwal'] as $jadwal)
                                    <tr>
                                        <td>{{ $jadwal['hari'] }}</td>
                                        <td>{{ $jadwal['kelas'] }}</td>
                                        <td>{{ $jadwal['mata_pelajaran'] }}</td>
                                        <td>{{ $jadwal['jam'] }}</td>
                                        <td class="text-center">{{ $jadwal['frekuensi_bulan'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p style="font-size: 10px; color: #999; font-style: italic;">Tidak ada jadwal untuk tutor ini</p>
                @endif
            </div>
        @empty
            <p style="text-align: center; color: #999;">Tidak ada data tutor</p>
        @endforelse
    </div>
    
    {{-- Keterangan --}}
    <div class="note">
        <strong>Keterangan:</strong><br>
        Laporan ini menampilkan informasi jadwal mengajar setiap tutor dalam periode yang dipilih.
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
