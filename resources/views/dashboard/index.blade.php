@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Absensi Peserta - E-Presensi CECMataram')

@section('content')

    {{-- Welcome Banner --}}
    <div
        class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary-600 via-primary-700 to-primary-800 p-6 sm:p-8 mb-8 shadow-lg shadow-primary-500/20">
        <div class="absolute top-0 right-0 w-64 h-64 opacity-10">
            <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="100" cy="100" r="80" stroke="white" stroke-width="2" />
                <circle cx="100" cy="100" r="60" stroke="white" stroke-width="2" />
                <circle cx="100" cy="100" r="40" stroke="white" stroke-width="2" />
                <circle cx="100" cy="100" r="20" stroke="white" stroke-width="2" />
            </svg>
        </div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
        <div class="absolute -top-10 -left-10 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>

        <div class="relative z-10">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-white text-xl sm:text-2xl font-bold tracking-tight">
                        Selamat Datang, {{ Auth::user()->name ?? 'User' }}! 👋
                    </h1>
                    <p class="text-primary-200 mt-1 text-sm sm:text-base max-w-xl">
                        Sistem E-Presensi CECMataram
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <div class="px-4 py-2 bg-white/10 backdrop-blur-sm rounded-xl border border-white/20">
                        <p class="text-primary-200 text-[11px] font-medium uppercase tracking-wider">Tanggal Hari Ini</p>
                        <p class="text-white font-semibold text-sm mt-0.5">
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (Auth::user()->role === 'admin')
        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
            {{-- Total Peserta --}}
            <div class="glass-card rounded-2xl p-5 hover-lift group border border-dark-200/50">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-dark-400 text-xs font-semibold uppercase tracking-wider">Total Peserta</p>
                        <h3 class="text-3xl font-bold text-dark-800 mt-2">{{ $totalPeserta }}</h3>
                        <p class="text-dark-400 text-xs mt-1">Peserta terdaftar</p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/25 group-hover:scale-110 transition-transform duration-200">
                        <i class='bx bx-user text-white text-2xl'></i>
                    </div>
                </div>
            </div>

            {{-- Total tutor --}}
            <div class="glass-card rounded-2xl p-5 hover-lift group border border-dark-200/50">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-dark-400 text-xs font-semibold uppercase tracking-wider">Total Tutor</p>
                        <h3 class="text-3xl font-bold text-dark-800 mt-2">{{ $totalTutor }}</h3>
                        <p class="text-dark-400 text-xs mt-1">Tutor aktif</p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/25 group-hover:scale-110 transition-transform duration-200">
                        <i class='bx bx-chalkboard text-white text-2xl'></i>
                    </div>
                </div>
            </div>

            {{-- Total Kelas --}}
            <div class="glass-card rounded-2xl p-5 hover-lift group border border-dark-200/50">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-dark-400 text-xs font-semibold uppercase tracking-wider">Total Kelas</p>
                        <h3 class="text-3xl font-bold text-dark-800 mt-2">{{ $totalKelas }}</h3>
                        <p class="text-dark-400 text-xs mt-1">Kelas tersedia</p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/25 group-hover:scale-110 transition-transform duration-200">
                        <i class='bx bx-building-house text-white text-2xl'></i>
                    </div>
                </div>
            </div>

            {{-- Hadir Hari Ini --}}
            <div class="glass-card rounded-2xl p-5 hover-lift group border border-dark-200/50">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-dark-400 text-xs font-semibold uppercase tracking-wider">Hadir Hari Ini</p>
                        <h3 class="text-3xl font-bold text-dark-800 mt-2">{{ $hadirHariIni }}</h3>
                        <p class="text-dark-400 text-xs mt-1">Peserta sudah absen</p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center shadow-lg shadow-primary-500/25 group-hover:scale-110 transition-transform duration-200">
                        <i class='bx bx-check-shield text-white text-2xl'></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rekap Absensi Table --}}
        <div class="glass-card rounded-2xl border border-dark-200/50 overflow-hidden">
            {{-- Header + Filter --}}
            <div class="px-6 py-5 border-b border-dark-100 bg-dark-50/30">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-dark-800">Rekap Absensi Peserta</h3>
                        <p class="text-sm text-dark-500 mt-1">Seluruh data kehadiran Peserta</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('dashboard.export.pdf', ['bulan' => $bulan, 'tahun' => $tahun, 'search' => $search]) }}"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl shadow-lg shadow-rose-500/25 hover:shadow-rose-500/35 transition-all duration-200 hover:-translate-y-0.5"
                            target="_blank">
                            <i class='bx bxs-file-pdf text-base'></i>
                            Cetak PDF
                        </a>
                        <a href="{{ route('dashboard.export.excel', ['bulan' => $bulan, 'tahun' => $tahun, 'search' => $search]) }}"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/35 transition-all duration-200 hover:-translate-y-0.5">
                            <i class='bx bxs-file text-base'></i>
                            Export Excel
                        </a>
                    </div>
                </div>

                <form action="{{ route('dashboard') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-3">
                    <div class="flex-1 w-full">
                        <label class="block text-xs font-medium text-dark-600 mb-1">Cari Peserta</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class='bx bx-search text-dark-400'></i>
                            </div>
                            <input type="text" name="search" value="{{ $search }}"
                                class="block w-full pl-9 pr-3 py-2 border border-dark-200 rounded-xl bg-white text-sm text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all"
                                placeholder="Nama / NIS...">
                        </div>
                    </div>
                    <div class="w-full sm:w-40">
                        <label class="block text-xs font-medium text-dark-600 mb-1">Bulan</label>
                        <select name="bulan"
                            class="block w-full px-3 py-2 border border-dark-200 rounded-xl bg-white text-sm text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                    {{ $bulan == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromDate(null, $i, 1)->isoFormat('MMMM') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="w-full sm:w-28">
                        <label class="block text-xs font-medium text-dark-600 mb-1">Tahun</label>
                        <select name="tahun"
                            class="block w-full px-3 py-2 border border-dark-200 rounded-xl bg-white text-sm text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all">
                            @for ($y = date('Y'); $y >= date('Y') - 3; $y--)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <button type="submit"
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-xl transition-colors flex items-center gap-1.5 whitespace-nowrap">
                        <i class='bx bx-filter-alt'></i> Filter
                    </button>
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-dark-50/50 border-b border-dark-200/50">
                            <th
                                class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider w-12 text-center">
                                No</th>
                            <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider">NIS / Nama
                            </th>
                            <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider text-center">
                                Kelas</th>
                            <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider text-center">
                                Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider">Keterangan
                            </th>
                            <th
                                class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider text-center w-28">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-200/50">
                        @forelse ($absensi as $a)
                            <tr class="hover:bg-dark-50/50 transition-colors duration-200">
                                <td class="px-6 py-3.5 text-sm text-dark-600 text-center">
                                    {{ $absensi->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-3.5 text-sm text-dark-800">
                                    {{ \Carbon\Carbon::parse($a->tanggal)->isoFormat('D MMM Y') }}
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold text-xs uppercase">
                                            {{ substr($a->peserta->nama ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-dark-800">{{ $a->peserta->nama ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-primary-50 text-primary-700 border border-primary-200">
                                        {{ $a->peserta->kelas->nama_kelas ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    @php $st = strtolower($a->status); @endphp
                                    @if ($st == 'hadir')
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class='bx bx-check-shield'></i> Hadir
                                        </span>
                                    @elseif($st == 'izin')
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            <i class='bx bx-envelope'></i> Izin
                                        </span>
                                    @elseif($st == 'sakit')
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class='bx bx-plus-medical'></i> Sakit
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class='bx bx-x-circle'></i> Alfa
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-sm text-dark-600 max-w-[150px] truncate"
                                    title="{{ $a->keterangan }}">
                                    {{ $a->keterangan ?? '-' }}
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button"
                                            onclick="openEditAbsensi({{ $a->id }}, '{{ $a->status }}', '{{ addslashes($a->keterangan) }}')"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors"
                                            title="Edit">
                                            <i class='bx bx-edit-alt text-lg'></i>
                                        </button>
                                        <form action="{{ route('dashboard.absensi.delete', $a->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="button"
                                                class="delete-confirm inline-flex items-center justify-center w-8 h-8 rounded-lg text-rose-600 bg-rose-50 hover:bg-rose-100 transition-colors"
                                                title="Hapus">
                                                <i class='bx bx-trash text-lg'></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-16 h-16 mb-4 rounded-full bg-dark-100 flex items-center justify-center">
                                            <i class='bx bx-folder-open text-3xl text-dark-400'></i>
                                        </div>
                                        <h3 class="text-sm font-medium text-dark-900">Tidak ada data</h3>
                                        <p class="mt-1 text-sm text-dark-500">Belum ada catatan absensi pada periode ini.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($absensi->hasPages())
                <div class="px-6 py-4 border-t border-dark-100 bg-dark-50/30">
                    {{ $absensi->links() }}
                </div>
            @endif
        </div>

        {{-- Modal Edit Absensi --}}
        @push('modals')
            <div id="modal-editabsensi-dash" class="fixed inset-0 z-[60] hidden">
                <div class="fixed inset-0 bg-dark-950/50 backdrop-blur-sm transition-opacity"
                    onclick="closeModal('modal-editabsensi-dash')"></div>
                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <div
                            class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md fade-in">
                            <div class="bg-dark-50/50 px-6 py-4 border-b border-dark-100 flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-dark-800 flex items-center gap-2">
                                    <i class='bx bx-edit-alt text-primary-600 text-xl'></i>
                                    Edit Absensi
                                </h3>
                                <button type="button" onclick="closeModal('modal-editabsensi-dash')"
                                    class="text-dark-400 hover:text-dark-600 transition-colors">
                                    <i class='bx bx-x text-2xl'></i>
                                </button>
                            </div>
                            <div class="px-6 py-5">
                                <form id="formEditAbsensiDash" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-dark-700 mb-1.5">Status
                                            Kehadiran</label>
                                        <select name="status" id="dash_edit_status" required
                                            class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-dark-50/50 focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200">
                                            <option value="Hadir">Hadir</option>
                                            <option value="Izin">Izin</option>
                                            <option value="Sakit">Sakit</option>
                                            <option value="Alfa">Alfa</option>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-dark-700 mb-1.5">Keterangan
                                            (Opsional)</label>
                                        <textarea name="keterangan" id="dash_edit_keterangan" rows="3"
                                            class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-dark-50/50 focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200"
                                            placeholder="Ketik alasan perubahan..."></textarea>
                                    </div>
                                    <div class="mt-6 flex items-center justify-end gap-3">
                                        <button type="button" onclick="closeModal('modal-editabsensi-dash')"
                                            class="px-4 py-2 border border-dark-200 rounded-xl text-dark-600 bg-white hover:bg-dark-50 font-medium transition-colors">
                                            Batal
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-medium rounded-xl shadow-lg shadow-primary-500/30 transition-all flex items-center gap-2">
                                            <i class='bx bx-save text-lg'></i> Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endpush
    @endif

    {{-- TAMPILAN KHUSUS peserta --}}
    @if (Auth::user()->role === 'peserta' && isset($peserta))
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            {{-- Card Data Diri --}}
            <div class="lg:col-span-2 glass-card rounded-2xl border border-dark-200/50 p-6">
                <h3 class="text-lg font-bold text-dark-800 mb-4 flex items-center gap-2">
                    <i class='bx bx-user-circle text-primary-500 text-2xl'></i>
                    Profil Peserta
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-dark-50/50 border border-dark-100">
                        <p class="text-xs text-dark-400 uppercase font-bold tracking-wider mb-1">Nama Lengkap</p>
                        <p class="text-dark-800 font-semibold">{{ $peserta->nama }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-dark-50/50 border border-dark-100">
                        <p class="text-xs text-dark-400 uppercase font-bold tracking-wider mb-1">Email</p>
                        <p class="text-dark-800 font-semibold">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-dark-50/50 border border-dark-100">
                        <p class="text-xs text-dark-400 uppercase font-bold tracking-wider mb-1">Kelas</p>
                        <p class="text-dark-800 font-semibold">{{ $peserta->kelas->nama_kelas ?? '-' }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-dark-50/50 border border-dark-100">
                        <p class="text-xs text-dark-400 uppercase font-bold tracking-wider mb-1">Jenis Kelamin</p>
                        <p class="text-dark-800 font-semibold">
                            {{ $peserta->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                </div>
            </div>

            {{-- Card QR Code --}}
            <div
                class="glass-card rounded-2xl border border-primary-100 bg-gradient-to-b from-white to-primary-50/30 p-6 flex flex-col items-center justify-center text-center relative overflow-hidden group">
                <!-- Background Decoration -->
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-primary-500/5 rounded-full blur-2xl -mr-10 -mt-10 transition-all duration-500 group-hover:bg-primary-500/10">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-24 h-24 bg-indigo-500/5 rounded-full blur-2xl -ml-8 -mb-8 transition-all duration-500 group-hover:bg-indigo-500/10">
                </div>

                <div class="relative z-10 w-full flex flex-col items-center">
                    <div
                        class="w-10 h-10 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center mb-3 shadow-inner">
                        <i class='bx bx-qr-scan text-xl'></i>
                    </div>
                    <h3 class="text-sm font-bold text-dark-800 mb-1 uppercase tracking-widest">ID Card Digital</h3>
                    <p class="text-xs text-dark-500 mb-5">Scan saat kelas untuk absensi</p>

                    <div
                        class="relative p-1 rounded-2xl bg-gradient-to-br from-primary-200 via-indigo-100 to-primary-200 mb-6 shadow-sm">
                        <div
                            class="bg-white p-3.5 rounded-[14px] transform transition-transform duration-300 group-hover:scale-105">
                            <img src="{{ asset('qrcodes/' . $peserta->qrcode) }}" alt="QR Code"
                                class="w-36 h-36 object-contain">
                        </div>
                    </div>

                    <a href="{{ route('dashboard.download.idcard') }}"
                        class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-700 hover:to-indigo-700 text-white rounded-xl font-bold transition-all duration-300 shadow-lg shadow-primary-500/25 hover:shadow-primary-500/40 hover:-translate-y-1">
                        <i class='bx bxs-file-pdf text-lg animate-bounce'></i>
                        Cetak ID Card (PDF)
                    </a>
                </div>
            </div>
            {{-- Card Waktu & Tanggal Realtime --}}
            <div
                class="glass-card rounded-2xl border border-dark-200/50 p-6 flex flex-col justify-center items-center text-center">
                <div class="w-16 h-16 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center mb-4">
                    <i class='bx bx-time-five text-3xl'></i>
                </div>
                <h2 id="clock" class="text-4xl font-bold text-dark-800 tracking-tight">00:00:00</h2>
                <p class="text-dark-500 font-medium mt-1">{{ $tanggalHariIni }}</p>
            </div>

            {{-- Card Status Kehadiran Hari Ini --}}
            <div class="lg:col-span-2 glass-card rounded-2xl border border-dark-200/50 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-dark-800">Status Kehadiran Anda</h3>
                        <p class="text-sm text-dark-500">Catatan sistem untuk hari ini</p>
                    </div>
                    <div
                        class="px-4 py-2 rounded-xl bg-dark-50 border border-dark-100 text-xs font-bold text-dark-600 uppercase tracking-widest">
                        {{ date('Y-m-d') }}
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-6">
                    @if ($absenHariIni)
                        @php
                            $status = strtolower($absenHariIni->status);
                            $config = [
                                'hadir' => [
                                    'icon' => 'bx-check-shield',
                                    'color' => 'emerald',
                                    'label' => 'Sudah Absen (Hadir)',
                                ],
                                'izin' => ['icon' => 'bx-envelope', 'color' => 'blue', 'label' => 'Izin'],
                                'sakit' => ['icon' => 'bx-plus-medical', 'color' => 'amber', 'label' => 'Sakit'],
                                'alfa' => ['icon' => 'bx-x-circle', 'color' => 'rose', 'label' => 'Alfa'],
                            ];
                            $current = $config[$status] ?? $config['alfa'];
                        @endphp

                        <div
                            class="w-20 h-20 rounded-2xl bg-{{ $current['color'] }}-100 text-{{ $current['color'] }}-600 flex items-center justify-center shadow-sm">
                            <i class='bx {{ $current['icon'] }} text-4xl'></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span
                                    class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-{{ $current['color'] }}-100 text-{{ $current['color'] }}-700 border border-{{ $current['color'] }}-200 uppercase">
                                    {{ $current['label'] }}
                                </span>
                                <span class="text-sm font-mono text-dark-400">Pukul
                                    {{ date('H:i', strtotime($absenHariIni->jam_masuk)) }}</span>
                            </div>
                            <h4 class="text-xl font-bold text-dark-800">Terima kasih atas kedisiplinan Anda.</h4>
                            <p class="text-dark-500 text-sm italic">"Pendidikan adalah tiket ke masa depan."</p>
                        </div>
                    @else
                        <div
                            class="w-20 h-20 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center animate-pulse">
                            <i class='bx bx-error-circle text-4xl'></i>
                        </div>
                        <div>
                            <span
                                class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200 uppercase">
                                Belum Absen
                            </span>
                            <h4 class="text-xl font-bold text-dark-800 mt-2">Anda belum melakukan absensi hari ini.</h4>
                            <p class="text-dark-500 text-sm">Segera lakukan absensi melalui menu <strong>Absensi
                                    Lokasi</strong> sebelum terlambat.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
    @if (Auth::user()->role === 'tutor' && isset($tutor))
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            {{-- Card Waktu & Tanggal Realtime (Sama dengan style peserta) --}}
            <div
                class="glass-card rounded-2xl border border-dark-200/50 p-6 flex flex-col justify-center items-center text-center">
                <div class="w-16 h-16 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center mb-4">
                    <i class='bx bx-time-five text-3xl'></i>
                </div>
                <h2 id="clock" class="text-4xl font-bold text-dark-800 tracking-tight">{{ $jamSekarang }}</h2>
                <p class="text-dark-500 font-medium mt-1">{{ $tanggalHariIni }}</p>
            </div>

            {{-- Card Detail tutor --}}
            <div class="lg:col-span-2 glass-card rounded-2xl border border-dark-200/50 p-6 flex items-center gap-6">
                <div
                    class="w-20 h-20 rounded-2xl bg-primary-100 text-primary-600 flex items-center justify-center shadow-sm shrink-0">
                    <i class='bx bx-user-circle text-5xl'></i>
                </div>
                <div>
                    <span
                        class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary-100 text-primary-700 border border-primary-200 uppercase tracking-wider">
                        Profil Pengajar
                    </span>
                    <h4 class="text-2xl font-bold text-dark-800 mt-1">{{ $tutor->nama }}</h4>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1 text-dark-500">

                        <p class="flex items-center gap-1.5 text-sm">
                            <i class='bx bx-book-bookmark'></i> Tutor {{ $tutor->mapel }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Jadwal Mengajar --}}
        <div class="glass-card rounded-2xl border border-dark-200/50 overflow-hidden shadow-sm">
            <div class="p-6 border-b border-dark-100/50 flex items-center justify-between bg-dark-50/30">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <i class='bx bx-calendar-event text-xl'></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-dark-800">Jadwal Mengajar Hari Ini</h3>
                        <p class="text-xs text-dark-500">Menampilkan jadwal berdasarkan hari
                            {{ \Carbon\Carbon::now()->locale('id')->dayName }}</p>
                    </div>
                </div>
                <div
                    class="px-4 py-2 rounded-xl bg-white border border-dark-100 text-xs font-bold text-dark-600 uppercase tracking-widest">
                    {{ count($jadwalHariIni) }} Sesi
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-dark-50/50">
                            <th class="px-6 py-4 text-xs font-bold text-dark-400 uppercase tracking-wider">Jam</th>
                            <th class="px-6 py-4 text-xs font-bold text-dark-400 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-4 text-xs font-bold text-dark-400 uppercase tracking-wider">Mata Pelajaran
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-dark-400 uppercase tracking-wider text-right">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-100/50">
                        @forelse($jadwalHariIni as $j)
                            <tr class="hover:bg-dark-50/30 transition-colors">
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1.5 rounded-lg bg-white border border-dark-100 text-sm font-semibold text-primary-600">
                                        {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-bold text-dark-800">{{ $j->kelas->nama_kelas }}</td>
                                <td class="px-6 py-4 text-dark-600">{{ $j->mata_pelajaran }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('absensi.harian') }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-600 text-white text-xs font-bold hover:bg-primary-700 transition-all shadow-sm shadow-primary-200">
                                        <i class='bx bx-edit-alt'></i> Absensi
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 rounded-full bg-dark-50 text-dark-300 flex items-center justify-center mb-3">
                                            <i class='bx bx-calendar-x text-3xl'></i>
                                        </div>
                                        <p class="text-dark-500 font-medium">Tidak ada jadwal mengajar untuk hari ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('clock').textContent = hours + ':' + minutes;
        }
        setInterval(updateClock, 1000);

        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const clockElement = document.getElementById('clock');
            if (clockElement) {
                clockElement.textContent = `${hours}:${minutes}:${seconds}`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
    <script>
        // Modal Functions
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        function openEditAbsensi(id, status, keterangan) {
            document.getElementById('formEditAbsensiDash').action = '/dashboard/absensi/' + id + '/update';

            let selectStatus = document.getElementById('dash_edit_status');
            for (let i = 0; i < selectStatus.options.length; i++) {
                if (selectStatus.options[i].value.toLowerCase() === status.toLowerCase()) {
                    selectStatus.selectedIndex = i;
                    break;
                }
            }

            document.getElementById('dash_edit_keterangan').value = keterangan && keterangan !== '-' ? keterangan :
                '';
            openModal('modal-editabsensi-dash');
        }

        // Delete Confirmation
        document.querySelectorAll('.delete-confirm').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                Swal.fire({
                    title: 'Hapus Data Absensi?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'font-inter rounded-2xl',
                        confirmButton: 'rounded-xl',
                        cancelButton: 'rounded-xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Close modal on escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal('modal-editabsensi-dash');
            }
        });
    </script>
@endpush
