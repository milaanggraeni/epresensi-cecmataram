<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Sesi: {{ $jadwal->mata_pelajaran }}</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .qr-container svg { width: 100%; height: auto; max-width: 400px; }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <div class="flex-1 flex flex-col items-center justify-center p-4">
        
        <div class="bg-white p-8 md:p-12 rounded-3xl shadow-2xl border border-gray-100 max-w-2xl w-full text-center relative overflow-hidden">
            <!-- decorative circles -->
            <div class="absolute -top-20 -left-20 w-40 h-40 bg-indigo-50 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-20 -right-20 w-40 h-40 bg-blue-50 rounded-full blur-3xl"></div>

            <div class="relative z-10">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Presensi Kehadiran</h1>
                <p class="text-gray-500 mb-8">Scan QR Code ini menggunakan aplikasi peserta</p>

                <div class="bg-white p-4 rounded-2xl inline-block shadow-lg border border-gray-100 mb-8 qr-container" id="qr-container">
                    {!! SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(350)->generate($qrContent) !!}
                </div>

                <div class="grid grid-cols-2 gap-4 text-left max-w-md mx-auto bg-gray-50 p-6 rounded-2xl">
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Mata Pelajaran</p>
                        <p class="font-bold text-gray-800 text-lg">{{ $jadwal->mata_pelajaran }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Kelas</p>
                        <p class="font-bold text-gray-800 text-lg">{{ $jadwal->kelas->nama_kelas }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Jam Kelas</p>
                        <p class="font-bold text-gray-800 text-lg">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Peserta Hadir</p>
                        <p class="font-bold text-indigo-600 text-lg"><span id="hadir-count">{{ $jadwal->absensis->count() }}</span> Orang</p>
                    </div>
                </div>

                <div class="mt-8 space-x-4">
                    <button onclick="refreshToken()" id="btn-refresh" class="inline-flex items-center gap-2 px-6 py-3 bg-white border-2 border-indigo-100 hover:border-indigo-500 text-indigo-600 rounded-xl font-bold transition-all shadow-sm">
                        <i class='bx bx-refresh text-xl'></i> Refresh QR
                    </button>
                    <a href="{{ route('sesi.kelas') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold transition-all">
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Attendees List -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 shadow-[0_-10px_40px_rgba(0,0,0,0.05)] transform translate-y-full transition-transform duration-300" id="attendees-panel">
        <div class="max-w-6xl mx-auto">
            <div class="flex items-center justify-between mb-4 cursor-pointer" onclick="toggleAttendees()">
                <h3 class="font-bold text-gray-800 flex items-center gap-2"><i class='bx bx-group text-indigo-500'></i> Daftar Hadir Real-time (<span id="panel-count">{{ $jadwal->absensis->count() }}</span>)</h3>
                <i class='bx bx-chevron-up text-2xl transition-transform' id="panel-icon"></i>
            </div>
            <div class="overflow-x-auto">
                <div class="flex gap-3 pb-2" id="attendees-list">
                    <!-- Attendees will be appended here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        let isPanelOpen = false;

        function toggleAttendees() {
            const panel = document.getElementById('attendees-panel');
            const icon = document.getElementById('panel-icon');
            
            if (isPanelOpen) {
                panel.classList.add('translate-y-full');
                panel.classList.remove('translate-y-0');
                icon.style.transform = 'rotate(0deg)';
            } else {
                panel.classList.remove('translate-y-full');
                panel.classList.add('translate-y-0');
                icon.style.transform = 'rotate(180deg)';
                panel.style.transform = 'translateY(calc(100% - 60px))'; // Show just the header initially
                if(!isPanelOpen) {
                    panel.style.transform = 'translateY(0)';
                }
            }
            isPanelOpen = !isPanelOpen;
        }

        function refreshToken() {
            const btn = document.getElementById('btn-refresh');
            const icon = btn.querySelector('i');
            icon.classList.add('bx-spin');
            
            fetch(`{{ route('sesi.kelas.refresh', $jadwal->id) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                icon.classList.remove('bx-spin');
                if (data.success) {
                    document.getElementById('qr-container').innerHTML = data.svg;
                }
            });
        }

        function fetchAttendees() {
            fetch(`{{ route('sesi.kelas.hadir', $jadwal->id) }}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('hadir-count').textContent = data.count;
                    document.getElementById('panel-count').textContent = data.count;
                    
                    const list = document.getElementById('attendees-list');
                    list.innerHTML = '';
                    
                    if (data.peserta.length === 0) {
                        list.innerHTML = '<p class="text-sm text-gray-500 italic">Belum ada peserta yang hadir.</p>';
                    } else {
                        data.peserta.forEach(p => {
                            list.innerHTML += `
                                <div class="shrink-0 bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">
                                        ${p.nama.charAt(0)}
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm text-gray-800">${p.nama}</p>
                                        <p class="text-xs text-gray-500">${p.jam_masuk}</p>
                                    </div>
                                </div>
                            `;
                        });
                    }
                }
            });
        }

        // Initialize panel header peek
        document.getElementById('attendees-panel').style.transform = 'translateY(calc(100% - 60px))';
        
        // Fetch attendees every 5 seconds
        setInterval(fetchAttendees, 5000);
        fetchAttendees();

        // Refresh token every 5 minutes automatically for security
        setInterval(refreshToken, 300000);
    </script>
</body>
</html>
