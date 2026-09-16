@extends('layouts.app')

@section('title', 'Sesi Kelas Hari Ini')
@section('page-title', 'Sesi Kelas Hari Ini')
@section('page-subtitle', 'Kelola QR Code sesi kelas Anda')

@section('breadcrumb')
    <li><i class='bx bx-chevron-right text-dark-400'></i></li>
    <li class="text-primary-600 font-medium">Sesi Kelas</li>
@endsection

@section('content')
    <div class="glass-card rounded-2xl border border-dark-200/50 overflow-hidden bg-white">
        <div class="p-6 border-b border-dark-100 flex items-center justify-between bg-dark-50/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center shadow-sm">
                    <i class='bx bx-qr-scan text-xl'></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-dark-800">Daftar Sesi Kelas ({{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }})</h3>
                    <p class="text-xs text-dark-500">Pilih sesi untuk menampilkan QR Code absensi ke peserta.</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-dark-50/50 border-b border-dark-200/50">
                        <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider">Jam</th>
                        <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider text-center">Status Scan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-200/50">
                    @forelse($jadwalHariIni as $j)
                        <tr class="hover:bg-dark-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 font-semibold text-sm border border-primary-200">
                                    {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-dark-800">{{ $j->kelas->nama_kelas }}</td>
                            <td class="px-6 py-4 text-dark-600">{{ $j->mata_pelajaran }}</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $pesertaHadir = $j->absensis->count();
                                    $totalPeserta = clone $j; 
                                    $totalPeserta = collect($j->pesertas)->count() ?? $j->kelas->jumlah_peserta ?? 0; // Using relationship
                                @endphp
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold border border-emerald-200">
                                    {{ $pesertaHadir }} Hadir
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('sesi.kelas.qr', $j->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white rounded-xl text-sm font-medium transition-all shadow-md shadow-primary-500/20">
                                    <i class='bx bx-qr'></i> Buka Sesi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-full bg-dark-100 flex items-center justify-center mb-3">
                                        <i class='bx bx-calendar-x text-3xl text-dark-400'></i>
                                    </div>
                                    <p class="text-dark-600 font-medium">Tidak ada jadwal kelas untuk Anda hari ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
