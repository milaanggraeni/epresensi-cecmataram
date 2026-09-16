@extends('layouts.app')

@section('title', 'Absensi Kehadiran')
@section('page-title', 'Absensi Kehadiran')
@section('page-subtitle', 'Status dan riwayat kehadiran Anda')

@section('breadcrumb')
    <li><i class='bx bx-chevron-right text-dark-400'></i></li>
    <li class="text-primary-600 font-medium">Absensi</li>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left Column: Profil & Status --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Student ID Card --}}
            <div class="glass-card rounded-2xl border border-dark-200/50 p-6 relative overflow-hidden group">
                <div
                    class="absolute -right-6 -top-6 w-32 h-32 bg-primary-500/10 rounded-full blur-2xl group-hover:bg-primary-500/20 transition-all duration-500">
                </div>

                <h3 class="text-sm font-bold text-dark-800 uppercase tracking-wider mb-6 flex items-center gap-2">
                    <i class='bx bx-id-card text-primary-500 text-lg'></i> Profil Peserta
                </h3>

                <div class="flex items-center gap-4 mb-6">
                    <div
                        class="w-14 h-14 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center text-xl font-bold shadow-lg shadow-primary-500/30">
                        {{ strtoupper(substr($peserta->nama, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-dark-900">{{ $peserta->nama }}</h4>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-dark-50/50 border border-dark-100">
                        <span class="text-xs font-semibold text-dark-500 uppercase">Kelas</span>
                        <span
                            class="text-sm font-bold text-dark-800">{{ $peserta->kelas->nama_kelas ?? 'Belum Diatur' }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-dark-50/50 border border-dark-100">
                        <span class="text-xs font-semibold text-dark-500 uppercase">Status Hari Ini</span>
                        @if ($absenHariIni)
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class='bx bx-check-circle'></i> {{ ucfirst($absenHariIni->status) }}
                                ({{ date('H:i', strtotime($absenHariIni->jam_masuk)) }})
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                <i class='bx bx-x-circle'></i> Belum Absen
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Clock & Status --}}
            <div class="glass-card rounded-2xl border border-dark-200/50 p-6 text-center shadow-lg">
                <p class="text-sm font-medium text-dark-500 mb-1">
                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                </p>
                <div class="text-5xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-accent-600 mb-6 font-mono"
                    id="live-clock">
                    --:--:--
                </div>

                @if ($absenHariIni)
                    <div
                        class="p-5 rounded-xl bg-emerald-50 border border-emerald-200 flex flex-col items-center justify-center gap-2 mb-2">
                        <i class='bx bx-check-shield text-4xl text-emerald-500'></i>
                        <h4 class="font-bold text-emerald-700">Absensi Tercatat</h4>
                        <p class="text-xs text-emerald-600 text-center">Anda sudah tercatat hadir hari ini pada pukul
                            {{ date('H:i', strtotime($absenHariIni->jam_masuk)) }}. Selamat belajar!</p>
                    </div>
                @else
                    <div
                        class="p-5 rounded-xl bg-amber-50 border border-amber-200 flex flex-col items-center justify-center gap-2 mb-4">
                        <i class='bx bx-info-circle text-4xl text-amber-500'></i>
                        <h4 class="font-bold text-amber-700">Menunggu Absensi</h4>
                        <p class="text-xs text-amber-600 text-center">Silakan scan QR Code sesi kelas yang ditampilkan oleh tutor di depan kelas.</p>
                    </div>
                    <a href="{{ route('scan.absensi') }}" class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30 transition-all">
                        <i class='bx bx-qr-scan text-xl'></i> Scan Absensi Sekarang
                    </a>
                @endif
            </div>
        </div>

        {{-- Right Column: Rekap & Riwayat --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Rekap Bulan Ini --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div
                    class="glass-card rounded-2xl border border-emerald-500/20 p-5 relative overflow-hidden group hover-lift">
                    <div
                        class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-500/10 rounded-full blur-xl group-hover:bg-emerald-500/20 transition-all duration-500">
                    </div>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                            <i class='bx bx-check-shield text-xl'></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-dark-500 uppercase tracking-wider">Hadir</p>
                            <p class="text-2xl font-black text-emerald-400">{{ $rekap['hadir'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="glass-card rounded-2xl border border-blue-500/20 p-5 relative overflow-hidden group hover-lift">
                    <div
                        class="absolute -right-4 -top-4 w-20 h-20 bg-blue-500/10 rounded-full blur-xl group-hover:bg-blue-500/20 transition-all duration-500">
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center text-blue-400">
                            <i class='bx bx-envelope text-xl'></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-dark-500 uppercase tracking-wider">Izin</p>
                            <p class="text-2xl font-black text-blue-400">{{ $rekap['izin'] }}</p>
                        </div>
                    </div>
                </div>
                <div
                    class="glass-card rounded-2xl border border-amber-500/20 p-5 relative overflow-hidden group hover-lift">
                    <div
                        class="absolute -right-4 -top-4 w-20 h-20 bg-amber-500/10 rounded-full blur-xl group-hover:bg-amber-500/20 transition-all duration-500">
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/20 flex items-center justify-center text-amber-400">
                            <i class='bx bx-plus-medical text-xl'></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-dark-500 uppercase tracking-wider">Sakit</p>
                            <p class="text-2xl font-black text-amber-400">{{ $rekap['sakit'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="glass-card rounded-2xl border border-rose-500/20 p-5 relative overflow-hidden group hover-lift">
                    <div
                        class="absolute -right-4 -top-4 w-20 h-20 bg-rose-500/10 rounded-full blur-xl group-hover:bg-rose-500/20 transition-all duration-500">
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-500/20 flex items-center justify-center text-rose-400">
                            <i class='bx bx-x-circle text-xl'></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-dark-500 uppercase tracking-wider">Alpa</p>
                            <p class="text-2xl font-black text-rose-400">{{ $rekap['alpa'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Riwayat Kehadiran Bulan Ini --}}
            <div class="glass-card rounded-2xl border border-dark-200/50 overflow-hidden">
                <div class="px-6 py-5 border-b border-dark-100 bg-dark-50/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-dark-800 flex items-center gap-2">
                                <i class='bx bx-history text-primary-500'></i>
                                Riwayat Kehadiran
                            </h3>
                            <p class="text-sm text-dark-500 mt-0.5">Bulan
                                {{ \Carbon\Carbon::now()->isoFormat('MMMM Y') }}</p>
                        </div>
                        <div
                            class="flex items-center gap-2 px-4 py-2 bg-white border border-dark-200 rounded-xl text-sm font-medium text-dark-600 shadow-sm">
                            <i class='bx bx-calendar-check text-primary-500'></i>
                            {{ count($riwayatBulanIni) }} Catatan
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-dark-50/50 border-b border-dark-200/50">
                                <th
                                    class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider w-12 text-center">
                                    No</th>
                                <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider">Tanggal
                                </th>
                                <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider">Hari</th>
                                <th
                                    class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider text-center">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider text-center">
                                    Jam Masuk</th>
                                <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider">
                                    Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-dark-200/50">
                            @forelse ($riwayatBulanIni as $index => $r)
                                @php
                                    $statusLower = strtolower($r->status);
                                    $statusClass = match ($statusLower) {
                                        'hadir' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                        'izin' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                        'sakit' => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                                        default => 'bg-rose-500/20 text-rose-400 border-rose-500/30',
                                    };
                                    $statusIcon = match ($statusLower) {
                                        'hadir' => 'bx-check-shield',
                                        'izin' => 'bx-envelope',
                                        'sakit' => 'bx-plus-medical',
                                        default => 'bx-x-circle',
                                    };
                                @endphp
                                <tr class="hover:bg-dark-50/50 transition-colors duration-200">
                                    <td class="px-6 py-4 text-sm text-dark-600 text-center">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-dark-700">
                                        {{ \Carbon\Carbon::parse($r->tanggal)->isoFormat('D MMMM Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-dark-600">
                                        {{ \Carbon\Carbon::parse($r->tanggal)->isoFormat('dddd') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold border {{ $statusClass }}">
                                            <i class='bx {{ $statusIcon }} text-sm'></i>
                                            {{ ucfirst($r->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-dark-600 text-center font-mono">
                                        {{ $r->jam_masuk ? date('H:i', strtotime($r->jam_masuk)) : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-dark-500">
                                        {{ $r->keterangan ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div
                                                class="w-16 h-16 mb-4 rounded-full bg-dark-100 flex items-center justify-center">
                                                <i class='bx bx-calendar-x text-3xl text-dark-400'></i>
                                            </div>
                                            <h3 class="text-sm font-medium text-dark-800">Belum ada data kehadiran</h3>
                                            <p class="mt-1 text-sm text-dark-500">Belum ada catatan kehadiran untuk bulan
                                                ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Live Clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('live-clock').textContent = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Auto-refresh halaman setiap 15 detik untuk update realtime data absensi
        setInterval(() => {
            location.reload();
        }, 15000);
    </script>
@endpush
