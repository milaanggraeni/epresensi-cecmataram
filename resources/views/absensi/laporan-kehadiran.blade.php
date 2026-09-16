@extends('layouts.app')

@section('title', 'Laporan Kehadiran')
@section('page-title', 'Laporan Kehadiran')
@section('page-subtitle', 'Laporan kehadiran peserta dan tutor')

@section('breadcrumb')
    <li><i class='bx bx-chevron-right text-dark-400'></i></li>
    <li class="text-primary-600 font-medium">Laporan Kehadiran</li>
@endsection

@section('content')
    <div class="flex flex-col gap-6">

        {{-- Tab Switcher --}}
        <div class="flex items-center gap-1 p-1 rounded-2xl bg-dark-100/50 border border-dark-200/50 w-fit">
            <a href="javascript:void(0)" onclick="switchTab('peserta')" id="tab-btn-peserta"
                class="tab-btn px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300
                       {{ (!isset($activeTab) || $activeTab === 'peserta') ? 'bg-gradient-to-r from-primary-600 to-primary-700 text-white shadow-lg shadow-primary-500/30' : 'text-dark-500 hover:text-white hover:bg-white/5' }}">
                <i class='bx bx-group mr-1'></i> Peserta
            </a>
            <a href="javascript:void(0)" onclick="switchTab('tutor')" id="tab-btn-tutor"
                class="tab-btn px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300
                       {{ (isset($activeTab) && $activeTab === 'tutor') ? 'bg-gradient-to-r from-primary-600 to-primary-700 text-white shadow-lg shadow-primary-500/30' : 'text-dark-500 hover:text-white hover:bg-white/5' }}">
                <i class='bx bx-chalkboard mr-1'></i> Tutor
            </a>
        </div>

        {{-- ============================================================ --}}
        {{-- TAB PESERTA --}}
        {{-- ============================================================ --}}
        <div id="panel-peserta" class="{{ (isset($activeTab) && $activeTab === 'tutor') ? 'hidden' : '' }}">

            {{-- Filter Card --}}
            <div class="glass-card rounded-2xl border border-dark-200/50 p-6 mb-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-primary-600/20 flex items-center justify-center">
                        <i class='bx bx-filter-alt text-primary-400'></i>
                    </div>
                    <h3 class="text-sm font-bold text-dark-800 uppercase tracking-wider">Filter Laporan Peserta</h3>
                </div>
                <form action="{{ route('laporan.kehadiran.peserta') }}" method="GET"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-dark-600 mb-1.5">Kelas</label>
                        <select name="kelas_id"
                            class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-dark-50/50 focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200">
                            <option value="">— Semua Kelas —</option>
                            @foreach ($kelasList as $k)
                                <option value="{{ $k->id }}"
                                    {{ (isset($selectedKelas) && $selectedKelas && $selectedKelas->id == $k->id) ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark-600 mb-1.5">Bulan</label>
                        <select name="bulan"
                            class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-dark-50/50 focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                    {{ $bulan == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromDate(null, $i, 1)->isoFormat('MMMM') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark-600 mb-1.5">Tahun</label>
                        <select name="tahun"
                            class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-dark-50/50 focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200">
                            @for ($y = date('Y'); $y >= date('Y') - 3; $y--)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full px-5 py-2.5 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-medium rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-primary-500/40 transition-all duration-200 flex items-center justify-center gap-2">
                            <i class='bx bx-search-alt text-lg'></i>
                            Tampilkan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Hasil Peserta --}}
            @if (isset($rekapPeserta))

                {{-- Info Bar dengan Export Buttons --}}
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6">
                    <div class="flex items-center gap-2 text-sm text-dark-600">
                        <i class='bx bx-calendar text-primary-500 text-lg'></i>
                        Periode: <strong class="text-dark-800">{{ $namaBulan }}</strong>
                        <span class="text-dark-400">|</span>
                        Hari Efektif: <strong class="text-dark-800">{{ $hariEfektif }} hari</strong>
                        @if (isset($selectedKelas) && $selectedKelas)
                            <span class="text-dark-400">|</span>
                            Kelas: <strong class="text-dark-800">{{ $selectedKelas->nama_kelas }}</strong>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-2 px-4 py-2 bg-white border border-dark-200 rounded-xl text-sm font-medium text-dark-600 shadow-sm">
                            <i class='bx bx-group text-primary-500'></i>
                            {{ count($rekapPeserta) }} Peserta
                        </div>
                        <div class="flex items-center gap-2">

                            <a href="{{ route('laporan.kehadiran.peserta.export-pdf', ['kelas_id' => $selectedKelas->id ?? '', 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-xl transition-all duration-200 shadow-lg shadow-red-500/30 hover:shadow-red-500/40"
                                title="Preview PDF" target="_blank">
                                <i class='bx bx-file-pdf text-lg'></i>
                                Preview PDF
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Summary Cards --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
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
                                <p class="text-2xl font-black text-emerald-400">{{ $totalHadir }}</p>
                            </div>
                        </div>
                    </div>
                    <div
                        class="glass-card rounded-2xl border border-blue-500/20 p-5 relative overflow-hidden group hover-lift">
                        <div
                            class="absolute -right-4 -top-4 w-20 h-20 bg-blue-500/10 rounded-full blur-xl group-hover:bg-blue-500/20 transition-all duration-500">
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center text-blue-400">
                                <i class='bx bx-envelope text-xl'></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-dark-500 uppercase tracking-wider">Izin</p>
                                <p class="text-2xl font-black text-blue-400">{{ $totalIzin }}</p>
                            </div>
                        </div>
                    </div>
                    <div
                        class="glass-card rounded-2xl border border-amber-500/20 p-5 relative overflow-hidden group hover-lift">
                        <div
                            class="absolute -right-4 -top-4 w-20 h-20 bg-amber-500/10 rounded-full blur-xl group-hover:bg-amber-500/20 transition-all duration-500">
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-amber-500/20 flex items-center justify-center text-amber-400">
                                <i class='bx bx-plus-medical text-xl'></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-dark-500 uppercase tracking-wider">Sakit</p>
                                <p class="text-2xl font-black text-amber-400">{{ $totalSakit }}</p>
                            </div>
                        </div>
                    </div>
                    <div
                        class="glass-card rounded-2xl border border-rose-500/20 p-5 relative overflow-hidden group hover-lift">
                        <div
                            class="absolute -right-4 -top-4 w-20 h-20 bg-rose-500/10 rounded-full blur-xl group-hover:bg-rose-500/20 transition-all duration-500">
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-rose-500/20 flex items-center justify-center text-rose-400">
                                <i class='bx bx-x-circle text-xl'></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-dark-500 uppercase tracking-wider">Alpa</p>
                                <p class="text-2xl font-black text-rose-400">{{ $totalAlpa }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabel Rekap Peserta --}}
                <div class="glass-card rounded-2xl border border-dark-200/50 overflow-hidden">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-dark-50/50 border-b border-dark-200/50">
                                    <th
                                        class="px-4 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider w-12 text-center sticky left-0 bg-dark-50/80 backdrop-blur-sm z-10">
                                        No</th>
                                    <th
                                        class="px-4 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider min-w-[180px] sticky left-12 bg-dark-50/80 backdrop-blur-sm z-10">
                                        Nama Peserta</th>
                                    <th
                                        class="px-4 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider text-center">
                                        Kelas</th>
                                    <th
                                        class="px-4 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider text-center bg-emerald-500/5">
                                        <span class="inline-flex items-center gap-1 text-emerald-400"><i
                                                class='bx bx-check-shield'></i> H</span>
                                    </th>
                                    <th
                                        class="px-4 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider text-center bg-blue-500/5">
                                        <span class="inline-flex items-center gap-1 text-blue-400"><i
                                                class='bx bx-envelope'></i> I</span>
                                    </th>
                                    <th
                                        class="px-4 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider text-center bg-amber-500/5">
                                        <span class="inline-flex items-center gap-1 text-amber-400"><i
                                                class='bx bx-plus-medical'></i> S</span>
                                    </th>
                                    <th
                                        class="px-4 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider text-center bg-rose-500/5">
                                        <span class="inline-flex items-center gap-1 text-rose-400"><i
                                                class='bx bx-x-circle'></i> A</span>
                                    </th>
                                    <th
                                        class="px-4 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider text-center">
                                        Detail</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-dark-200/50">
                                @forelse ($rekapPeserta as $index => $r)
                                    <tr class="hover:bg-dark-50/50 transition-colors duration-200">
                                        <td
                                            class="px-4 py-4 text-sm text-dark-600 text-center sticky left-0 bg-dark-50/30 backdrop-blur-sm">
                                            {{ $index + 1 }}</td>
                                        <td class="px-4 py-4 sticky left-12 bg-dark-50/30 backdrop-blur-sm">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-600/20 text-primary-400 flex items-center justify-center font-bold text-xs uppercase">
                                                    {{ substr($r['peserta']->nama, 0, 1) }}
                                                </div>
                                                <span
                                                    class="text-sm font-bold text-dark-800">{{ $r['peserta']->nama }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-sm font-bold text-dark-700 text-center">
                                            {{ $r['peserta']->kelas->nama_kelas ?? '-' }}
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span
                                                class="inline-flex items-center justify-center min-w-[32px] px-2 py-1 rounded-lg text-xs font-bold {{ $r['hadir'] > 0 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-dark-100 text-dark-500' }}">
                                                {{ $r['hadir'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span
                                                class="inline-flex items-center justify-center min-w-[32px] px-2 py-1 rounded-lg text-xs font-bold {{ $r['izin'] > 0 ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : 'bg-dark-100 text-dark-500' }}">
                                                {{ $r['izin'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span
                                                class="inline-flex items-center justify-center min-w-[32px] px-2 py-1 rounded-lg text-xs font-bold {{ $r['sakit'] > 0 ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-dark-100 text-dark-500' }}">
                                                {{ $r['sakit'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span
                                                class="inline-flex items-center justify-center min-w-[32px] px-2 py-1 rounded-lg text-xs font-bold {{ $r['alpa'] > 0 ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-dark-100 text-dark-500' }}">
                                                {{ $r['alpa'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <button onclick="toggleDetail({{ $index }})"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-primary-600/20 text-primary-400 hover:bg-primary-600/30 transition-all duration-200 border border-primary-500/20">
                                                <i class='bx bx-chevron-down text-sm transition-transform duration-200'
                                                    id="detail-icon-{{ $index }}"></i>
                                                Rincian
                                            </button>
                                        </td>
                                    </tr>
                                    {{-- Detail Row (Hidden) --}}
                                    <tr id="detail-row-{{ $index }}" class="hidden">
                                        <td colspan="8" class="px-0 py-0">
                                            <div
                                                class="mx-4 my-3 p-4 rounded-xl bg-dark-100/50 border border-dark-200/50">
                                                <h4 class="text-xs font-bold text-dark-500 uppercase tracking-wider mb-3">
                                                    <i class='bx bx-calendar-check mr-1 text-primary-400'></i>
                                                    Rincian Kehadiran — {{ $r['peserta']->nama }}
                                                </h4>
                                                <div class="overflow-x-auto custom-scrollbar">
                                                    <table class="w-full text-left border-collapse">
                                                        <thead>
                                                            <tr class="border-b border-dark-200/30">
                                                                <th
                                                                    class="px-3 py-2 text-[10px] font-semibold text-dark-500 uppercase">
                                                                    Tanggal</th>
                                                                <th
                                                                    class="px-3 py-2 text-[10px] font-semibold text-dark-500 uppercase">
                                                                    Hari</th>
                                                                <th
                                                                    class="px-3 py-2 text-[10px] font-semibold text-dark-500 uppercase text-center">
                                                                    Status</th>
                                                                <th
                                                                    class="px-3 py-2 text-[10px] font-semibold text-dark-500 uppercase text-center">
                                                                    Jam Masuk</th>
                                                                <th
                                                                    class="px-3 py-2 text-[10px] font-semibold text-dark-500 uppercase">
                                                                    Keterangan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-dark-200/20">
                                                            @foreach ($r['detail'] as $d)
                                                                <tr
                                                                    class="hover:bg-dark-200/20 transition-colors duration-150">
                                                                    <td
                                                                        class="px-3 py-2 text-xs font-medium text-dark-700">
                                                                        {{ $d['tanggal_format'] }}</td>
                                                                    <td class="px-3 py-2 text-xs text-dark-600">
                                                                        {{ $d['hari'] }}</td>
                                                                    <td class="px-3 py-2 text-center">
                                                                        @php
                                                                            $statusLower = strtolower($d['status']);
                                                                            $statusClass = match ($statusLower) {
                                                                                'hadir'
                                                                                    => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                                                                'izin'
                                                                                    => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                                                                'sakit'
                                                                                    => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                                                                                default
                                                                                    => 'bg-rose-500/20 text-rose-400 border-rose-500/30',
                                                                            };
                                                                        @endphp
                                                                        <span
                                                                            class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $statusClass }}">
                                                                            {{ $d['status'] }}
                                                                        </span>
                                                                    </td>
                                                                    <td
                                                                        class="px-3 py-2 text-xs text-dark-600 text-center font-mono">
                                                                        {{ $d['jam_masuk'] }}</td>
                                                                    <td class="px-3 py-2 text-xs text-dark-500">
                                                                        {{ $d['keterangan'] }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div
                                                    class="w-16 h-16 mb-4 rounded-full bg-dark-100 flex items-center justify-center">
                                                    <i class='bx bx-search-alt text-3xl text-dark-400'></i>
                                                </div>
                                                <h3 class="text-sm font-medium text-dark-800">Tidak ada data peserta</h3>
                                                <p class="mt-1 text-sm text-dark-500">Pilih kelas dan periode lalu klik
                                                    "Tampilkan".</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                {{-- Empty state sebelum filter --}}
                <div class="glass-card rounded-2xl border border-dark-200/50 p-12">
                    <div class="flex flex-col items-center justify-center text-center">
                        <div
                            class="w-20 h-20 mb-6 rounded-2xl bg-gradient-to-br from-primary-600/20 to-accent-500/20 flex items-center justify-center">
                            <i class='bx bx-bar-chart-alt-2 text-4xl text-primary-400'></i>
                        </div>
                        <h3 class="text-lg font-bold text-dark-800 mb-2">Laporan Kehadiran Peserta</h3>
                        <p class="text-sm text-dark-500 max-w-md">Pilih kelas, bulan, dan tahun pada filter di atas, lalu
                            klik <strong class="text-primary-400">"Tampilkan"</strong> untuk melihat rincian kehadiran
                            peserta.</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- ============================================================ --}}
        {{-- TAB TUTOR --}}
        {{-- ============================================================ --}}
        <div id="panel-tutor" class="{{ (!isset($activeTab) || $activeTab !== 'tutor') ? 'hidden' : '' }}">

            {{-- Filter Card --}}
            <div class="glass-card rounded-2xl border border-dark-200/50 p-6 mb-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-primary-600/20 flex items-center justify-center">
                        <i class='bx bx-search text-primary-400'></i>
                    </div>
                    <h3 class="text-sm font-bold text-dark-800 uppercase tracking-wider">Cari Laporan Tutor</h3>
                </div>
                <form action="{{ route('laporan.kehadiran.tutor') }}" method="GET"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-dark-600 mb-1.5">Nama Tutor</label>
                        <input type="text" name="nama_tutor" value="{{ $namaTutor ?? '' }}"
                            placeholder="Ketik nama tutor..."
                            class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-dark-50/50 focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200 placeholder:text-dark-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark-600 mb-1.5">Bulan</label>
                        <select name="bulan"
                            class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-dark-50/50 focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                    {{ $bulan == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromDate(null, $i, 1)->isoFormat('MMMM') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark-600 mb-1.5">Tahun</label>
                        <select name="tahun"
                            class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-dark-50/50 focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200">
                            @for ($y = date('Y'); $y >= date('Y') - 3; $y--)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full px-5 py-2.5 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-medium rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-primary-500/40 transition-all duration-200 flex items-center justify-center gap-2">
                            <i class='bx bx-search-alt text-lg'></i>
                            Cari
                        </button>
                    </div>
                </form>
            </div>

            {{-- Hasil Tutor --}}
            @if (isset($rekapTutor))

                {{-- Info Bar dengan Export Buttons --}}
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6">
                    <div class="flex items-center gap-2 text-sm text-dark-600">
                        <i class='bx bx-calendar text-primary-500 text-lg'></i>
                        Periode: <strong class="text-dark-800">{{ $namaBulan }}</strong>
                        @if (isset($namaTutor) && $namaTutor)
                            <span class="text-dark-400">|</span>
                            Pencarian: <strong class="text-dark-800">"{{ $namaTutor }}"</strong>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-2 px-4 py-2 bg-white border border-dark-200 rounded-xl text-sm font-medium text-dark-600 shadow-sm">
                            <i class='bx bx-chalkboard text-primary-500'></i>
                            {{ count($rekapTutor) }} Tutor
                        </div>
                        <div class="flex items-center gap-2">

                            <a href="{{ route('laporan.kehadiran.tutor.export-pdf', ['nama_tutor' => $namaTutor ?? '', 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-xl transition-all duration-200 shadow-lg shadow-red-500/30 hover:shadow-red-500/40"
                                title="Preview PDF" target="_blank">
                                <i class='bx bx-file-pdf text-lg'></i>
                                Preview PDF
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Tutor Cards --}}
                <div class="space-y-4">
                    @forelse ($rekapTutor as $index => $rt)
                        <div
                            class="glass-card rounded-2xl border border-dark-200/50 overflow-hidden hover-lift transition-all duration-300">
                            {{-- Tutor Header --}}
                            <div class="p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-600 to-accent-500 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-primary-500/30">
                                        {{ strtoupper(substr($rt['tutor']->nama, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-dark-800">{{ $rt['tutor']->nama }}</h3>
                                        <p class="text-xs text-dark-500 flex items-center gap-2 mt-0.5">
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-primary-600/20 text-primary-400 font-semibold">
                                                <i class='bx bx-book-open'></i> {{ $rt['tutor']->mapel }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="text-center px-4 py-2 rounded-xl bg-dark-100/50 border border-dark-200/50">
                                        <p class="text-lg font-black text-primary-400">
                                            {{ $rt['total_jadwal_per_minggu'] }}</p>
                                        <p class="text-[10px] font-medium text-dark-500 uppercase">Jadwal/Minggu</p>
                                    </div>
                                    <div class="text-center px-4 py-2 rounded-xl bg-dark-100/50 border border-dark-200/50">
                                        <p class="text-lg font-black text-emerald-400">{{ $rt['total_hari_mengajar'] }}</p>
                                        <p class="text-[10px] font-medium text-dark-500 uppercase">Sesi/Bulan</p>
                                    </div>
                                    <button onclick="toggleTutorDetail({{ $index }})"
                                        class="inline-flex items-center gap-1 px-3 py-2 rounded-xl text-xs font-semibold bg-primary-600/20 text-primary-400 hover:bg-primary-600/30 transition-all duration-200 border border-primary-500/20">
                                        <i class='bx bx-chevron-down text-sm transition-transform duration-200'
                                            id="tutor-detail-icon-{{ $index }}"></i>
                                        Jadwal
                                    </button>
                                </div>
                            </div>

                            {{-- Tutor Detail Jadwal (Hidden) --}}
                            <div id="tutor-detail-{{ $index }}"
                                class="hidden border-t border-dark-200/50 bg-dark-100/30">
                                <div class="p-4">
                                    <h4 class="text-xs font-bold text-dark-500 uppercase tracking-wider mb-3">
                                        <i class='bx bx-time-five mr-1 text-primary-400'></i>
                                        Jadwal Mengajar — {{ $namaBulan }}
                                    </h4>
                                    <div class="overflow-x-auto custom-scrollbar">
                                        <table class="w-full text-left border-collapse">
                                            <thead>
                                                <tr class="border-b border-dark-200/30">
                                                    <th
                                                        class="px-3 py-2 text-[10px] font-semibold text-dark-500 uppercase">
                                                        Hari</th>
                                                    <th
                                                        class="px-3 py-2 text-[10px] font-semibold text-dark-500 uppercase">
                                                        Kelas</th>
                                                    <th
                                                        class="px-3 py-2 text-[10px] font-semibold text-dark-500 uppercase">
                                                        Mata Pelajaran</th>
                                                    <th
                                                        class="px-3 py-2 text-[10px] font-semibold text-dark-500 uppercase text-center">
                                                        Jam</th>
                                                    <th
                                                        class="px-3 py-2 text-[10px] font-semibold text-dark-500 uppercase text-center">
                                                        Frekuensi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-dark-200/20">
                                                @forelse ($rt['detail_jadwal'] as $dj)
                                                    <tr class="hover:bg-dark-200/20 transition-colors duration-150">
                                                        <td class="px-3 py-2.5">
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-primary-600/20 text-primary-400 border border-primary-500/20">
                                                                {{ $dj['hari'] }}
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-2.5 text-xs font-bold text-dark-700">
                                                            {{ $dj['kelas'] }}</td>
                                                        <td class="px-3 py-2.5 text-xs text-dark-600">
                                                            {{ $dj['mata_pelajaran'] }}</td>
                                                        <td
                                                            class="px-3 py-2.5 text-xs text-dark-600 text-center font-mono">
                                                            {{ $dj['jam'] }}</td>
                                                        <td class="px-3 py-2.5 text-center">
                                                            <span
                                                                class="inline-flex items-center justify-center min-w-[32px] px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                                                {{ $dj['frekuensi_bulan'] }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="px-3 py-6 text-center text-xs text-dark-500">
                                                            Tidak ada jadwal mengajar.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="glass-card rounded-2xl border border-dark-200/50 p-12">
                            <div class="flex flex-col items-center justify-center text-center">
                                <div class="w-16 h-16 mb-4 rounded-full bg-dark-100 flex items-center justify-center">
                                    <i class='bx bx-user-x text-3xl text-dark-400'></i>
                                </div>
                                <h3 class="text-sm font-medium text-dark-800">Tutor tidak ditemukan</h3>
                                <p class="mt-1 text-sm text-dark-500">Coba kata kunci pencarian yang berbeda.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            @else
                {{-- Empty state sebelum filter --}}
                <div class="glass-card rounded-2xl border border-dark-200/50 p-12">
                    <div class="flex flex-col items-center justify-center text-center">
                        <div
                            class="w-20 h-20 mb-6 rounded-2xl bg-gradient-to-br from-primary-600/20 to-accent-500/20 flex items-center justify-center">
                            <i class='bx bx-chalkboard text-4xl text-primary-400'></i>
                        </div>
                        <h3 class="text-lg font-bold text-dark-800 mb-2">Laporan Jadwal Tutor</h3>
                        <p class="text-sm text-dark-500 max-w-md">Ketik nama tutor dan pilih periode pada filter di atas,
                            lalu klik <strong class="text-primary-400">"Cari"</strong> untuk melihat jadwal mengajar
                            tutor.</p>
                    </div>
                </div>
            @endif
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        // Tab Switching
        function switchTab(tab) {
            const panelPeserta = document.getElementById('panel-peserta');
            const panelTutor = document.getElementById('panel-tutor');
            const btnPeserta = document.getElementById('tab-btn-peserta');
            const btnTutor = document.getElementById('tab-btn-tutor');

            const activeClass =
                'bg-gradient-to-r from-primary-600 to-primary-700 text-white shadow-lg shadow-primary-500/30';
            const inactiveClass = 'text-dark-500 hover:text-white hover:bg-white/5';

            if (tab === 'peserta') {
                panelPeserta.classList.remove('hidden');
                panelTutor.classList.add('hidden');
                btnPeserta.className = 'tab-btn px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 ' +
                    activeClass;
                btnTutor.className = 'tab-btn px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 ' +
                    inactiveClass;
            } else {
                panelPeserta.classList.add('hidden');
                panelTutor.classList.remove('hidden');
                btnTutor.className = 'tab-btn px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 ' +
                    activeClass;
                btnPeserta.className =
                    'tab-btn px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 ' + inactiveClass;
            }
        }

        // Toggle Detail Peserta
        function toggleDetail(index) {
            const row = document.getElementById('detail-row-' + index);
            const icon = document.getElementById('detail-icon-' + index);

            if (row.classList.contains('hidden')) {
                row.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                row.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        // Toggle Detail Tutor
        function toggleTutorDetail(index) {
            const detail = document.getElementById('tutor-detail-' + index);
            const icon = document.getElementById('tutor-detail-icon-' + index);

            if (detail.classList.contains('hidden')) {
                detail.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                detail.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
    </script>
@endpush
