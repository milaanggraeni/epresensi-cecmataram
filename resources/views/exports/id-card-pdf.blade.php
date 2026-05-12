<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>ID Card Peserta</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        .card-container {
            width: 220px;
            height: 350px;
            border: 2px solid #2563eb;
            border-radius: 12px;
            position: relative;
            margin: 0 auto;
            background: #ffffff;
            overflow: hidden;
        }

        .header {
            background-color: #2563eb;
            color: #ffffff;
            text-align: center;
            padding: 15px 0;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .header p {
            margin: 4px 0 0 0;
            font-size: 10px;
            opacity: 0.9;
            letter-spacing: 0.5px;
        }

        .photo-area {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 15px;
        }

        .qr-code {
            width: 130px;
            height: 130px;
            border: 2px solid #e5e7eb;
            padding: 5px;
            background: #ffffff;
            border-radius: 8px;
            margin: 0 auto;
        }

        .details {
            text-align: center;
            padding: 0 15px;
        }

        .details h2 {
            margin: 0 0 8px 0;
            font-size: 16px;
            color: #1e40af;
            text-transform: uppercase;
        }

        .details p {
            margin: 4px 0;
            font-size: 12px;
            color: #374151;
        }

        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #1e3a8a;
            color: #ffffff;
            text-align: center;
            padding: 10px 0;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body>
    <div class="card-container">
        <div class="header">
            <h1>ID CARD PESERTA</h1>
            <p>E-Presensi CECMataram</p>
        </div>

        <div class="photo-area">
            <?php
            $qrPath = public_path('qrcodes/' . $peserta->qrcode);
            if (file_exists($qrPath)) {
                $data = file_get_contents($qrPath);
                $base64 = 'data:image/svg+xml;base64,' . base64_encode($data);
            } else {
                $base64 = '';
            }
            ?>
            @if ($base64)
                <img src="{{ $base64 }}" class="qr-code" alt="QR Code">
            @else
                <div style="width:130px;height:130px;border:1px solid #ccc;margin:0 auto;line-height:130px;">No QR</div>
            @endif
        </div>

        <div class="details">
            <h2>{{ $peserta->nama }}</h2>
            <p><strong>Kelas:</strong> {{ $peserta->kelas->nama_kelas ?? '-' }}</p>
            <p><strong>L/P:</strong> {{ $peserta->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
        </div>

        <div class="footer">
            Kartu Identitas Digital
        </div>
    </div>
</body>

</html>
