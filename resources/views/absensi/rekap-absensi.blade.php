@extends('layouts.app')

@section('title', 'Rekap Absensi Detail')
@section('page-title', 'Rekap Absensi Detail')
@section('page-subtitle', 'Daftar riwayat absensi dengan filter spesifik')

@section('breadcrumb')
    <li><i class='bx bx-chevron-right text-dark-400'></i></li>
    <li class="text-primary-600 font-medium">Rekap Absensi</li>
@endsection

@section('content')
    <div class="flex flex-col gap-6">

        {{-- Filter Form --}}
        <div class="glass-card rounded-2xl border border-dark-200/50 p-6">
            <form action="{{ route('rekapabsensi') }}" method="GET" class="flex flex-col gap-4">
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    {{-- Filter Kelas --}}
                    <div>
                        <label class="block text-sm font-medium text-dark-700 mb-1.5">Kelas</label>
                        <select name="kelas_id" class="block w-full px-3 py-2 border border-dark-200 rounded-xl bg-dark-50/50 focus:bg-white text-dark-800">
                            <option value="">-- Semua Kelas --</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Tutor --}}
                    <div>
                        <label class="block text-sm font-medium text-dark-700 mb-1.5">Tutor</label>
                        <select name="tutor_id" class="block w-full px-3 py-2 border border-dark-200 rounded-xl bg-dark-50/50 focus:bg-white text-dark-800">
                            <option value="">-- Semua Tutor --</option>
                            @foreach($tutorList as $tutor)
                                <option value="{{ $tutor->id }}" {{ request('tutor_id') == $tutor->id ? 'selected' : '' }}>{{ $tutor->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Jadwal --}}
                    <div>
                        <label class="block text-sm font-medium text-dark-700 mb-1.5">Jadwal</label>
                        <select name="jadwal_id" class="block w-full px-3 py-2 border border-dark-200 rounded-xl bg-dark-50/50 focus:bg-white text-dark-800">
                            <option value="">-- Semua Jadwal --</option>
                            @foreach($jadwalList as $jadwal)
                                <option value="{{ $jadwal->id }}" {{ request('jadwal_id') == $jadwal->id ? 'selected' : '' }}>
                                    {{ $jadwal->kelas->nama_kelas ?? '-' }} ({{ $jadwal->mata_pelajaran }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Tanggal --}}
                    <div>
                        <label class="block text-sm font-medium text-dark-700 mb-1.5">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="block w-full px-3 py-2 border border-dark-200 rounded-xl bg-dark-50/50 focus:bg-white text-dark-800">
                    </div>

                    {{-- Filter Status --}}
                    <div>
                        <label class="block text-sm font-medium text-dark-700 mb-1.5">Status</label>
                        <select name="status" class="block w-full px-3 py-2 border border-dark-200 rounded-xl bg-dark-50/50 focus:bg-white text-dark-800">
                            <option value="">-- Semua Status --</option>
                            <option value="Hadir" {{ request('status') == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="Izin" {{ request('status') == 'Izin' ? 'selected' : '' }}>Izin</option>
                            <option value="Sakit" {{ request('status') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="Alfa" {{ request('status') == 'Alfa' ? 'selected' : '' }}>Alfa</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end mt-2">
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-medium rounded-xl shadow-lg shadow-primary-500/30 transition-all duration-200 flex items-center gap-2">
                        <i class='bx bx-filter-alt text-lg'></i> Terapkan Filter
                    </button>
                    <a href="{{ route('rekapabsensi') }}" class="ml-3 px-5 py-2.5 bg-dark-100 hover:bg-dark-200 text-dark-700 font-medium rounded-xl transition-all duration-200 flex items-center gap-2">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Tabel Rekap Detail --}}
        <div class="glass-card rounded-2xl border border-dark-200/50 overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-dark-50/50 border-b border-dark-200/50">
                            <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider w-12 text-center">No</th>
                            <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider">Peserta & Kelas</th>
                            <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider">Tanggal & Jadwal</th>
                            <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider">Tutor</th>
                            <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-200/50">
                        @forelse ($rekap as $log)
                            <tr class="hover:bg-dark-50/50 transition-colors duration-200">
                                <td class="px-6 py-4 text-sm text-dark-600 text-center">{{ $loop->iteration + ($rekap->currentPage() - 1) * $rekap->perPage() }}</td>
                                
                                <td class="px-6 py-4">
                                    <div class="font-bold text-dark-800">{{ $log->peserta->nama ?? '-' }}</div>
                                    <div class="text-xs text-dark-500">{{ $log->peserta->kelas->nama_kelas ?? '-' }}</div>
                                </td>
                                
                                <td class="px-6 py-4 text-sm text-dark-800">
                                    <div class="font-medium">{{ \Carbon\Carbon::parse($log->tanggal)->isoFormat('D MMM Y') }}</div>
                                    @if($log->jadwal)
                                        <div class="text-xs text-dark-500">{{ $log->jadwal->mata_pelajaran }} ({{ date('H:i', strtotime($log->jadwal->jam_mulai)) }})</div>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 text-sm text-dark-800">
                                    {{ $log->jadwal->tutor->nama ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    @if (strtolower($log->status) == 'hadir')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class='bx bx-check-shield text-sm'></i> Hadir
                                        </span>
                                    @elseif(strtolower($log->status) == 'izin')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-primary-50 text-primary-700 border border-primary-200">
                                            <i class='bx bx-envelope text-sm'></i> Izin
                                        </span>
                                    @elseif(strtolower($log->status) == 'sakit')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class='bx bx-plus-medical text-sm'></i> Sakit
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class='bx bx-x-circle text-sm'></i> Alfa
                                        </span>
                                    @endif
                                    @if($log->jam_masuk)
                                        <div class="text-[10px] text-dark-500 mt-1"><i class='bx bx-time'></i> {{ date('H:i', strtotime($log->jam_masuk)) }}</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 mb-4 rounded-full bg-dark-100 flex items-center justify-center">
                                            <i class='bx bx-bar-chart-alt-2 text-3xl text-dark-400'></i>
                                        </div>
                                        <h3 class="text-sm font-medium text-dark-900">Belum ada data</h3>
                                        <p class="mt-1 text-sm text-dark-500">Tidak ada data absensi sesuai filter.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($rekap->hasPages())
                <div class="px-6 py-4 border-t border-dark-100 bg-dark-50/30">
                    {{ $rekap->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
