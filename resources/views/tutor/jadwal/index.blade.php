@extends('layouts.app')

@section('title', 'Jadwal Mengajar')
@section('page-title', 'Jadwal Mengajar')
@section('page-subtitle', 'Informasi Jadwal Mengajar Tutor yang telah ditentukan oleh Admin.')

@section('breadcrumb')
    <li><i class='bx bx-chevron-right text-dark-400'></i></li>
    <li class="text-primary-600 font-medium">Jadwal Mengajar</li>
@endsection

@section('content')

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 stagger-children">
        <!-- Total Jadwal Mengajar -->
        <div class="glass-card rounded-2xl p-6 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary-500/10 rounded-full blur-2xl group-hover:bg-primary-500/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white shadow-lg shadow-primary-500/30">
                    <i class='bx bx-calendar text-2xl'></i>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-dark-800 mb-1">{{ $totalJadwal }}</h3>
                <p class="text-sm font-medium text-dark-500">Total Jadwal Mengajar</p>
            </div>
        </div>

        <!-- Jadwal Hari Ini -->
        <div class="glass-card rounded-2xl p-6 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                    <i class='bx bx-calendar-star text-2xl'></i>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-dark-800 mb-1">{{ $jadwalHariIni }}</h3>
                <p class="text-sm font-medium text-dark-500">Jadwal Hari Ini</p>
            </div>
        </div>

        <!-- Total Kelas Diampu -->
        <div class="glass-card rounded-2xl p-6 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-white shadow-lg shadow-amber-500/30">
                    <i class='bx bx-building text-2xl'></i>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-dark-800 mb-1">{{ $totalKelas }}</h3>
                <p class="text-sm font-medium text-dark-500">Total Kelas Diampu</p>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="glass-card rounded-2xl border border-dark-200/50 overflow-hidden fade-in-up">
        {{-- Filter Bar --}}
        <div class="px-6 py-4 border-b border-dark-100 bg-dark-50/30">
            <form action="{{ route('tutor.jadwal') }}" method="GET" class="flex flex-wrap items-end gap-3">
                
                <div class="w-full sm:w-auto">
                    <label class="block text-xs font-medium text-dark-600 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $request->tanggal }}"
                        class="block w-full px-3 py-2 border border-dark-200 rounded-xl bg-white text-sm text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all">
                </div>
                
                <div class="w-full sm:w-32">
                    <label class="block text-xs font-medium text-dark-600 mb-1">Hari</label>
                    <select name="hari"
                        class="block w-full px-3 py-2 border border-dark-200 rounded-xl bg-white text-sm text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all">
                        <option value="">Semua</option>
                        @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $h)
                            <option value="{{ $h }}" {{ $request->hari == $h ? 'selected' : '' }}>
                                {{ $h }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full sm:w-44">
                    <label class="block text-xs font-medium text-dark-600 mb-1">Mata Pelajaran</label>
                    <select name="mata_pelajaran"
                        class="block w-full px-3 py-2 border border-dark-200 rounded-xl bg-white text-sm text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all">
                        <option value="">Semua Program</option>
                        @foreach ($mapelOptions as $m)
                            <option value="{{ $m }}" {{ $request->mata_pelajaran == $m ? 'selected' : '' }}>
                                {{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full sm:w-44">
                    <label class="block text-xs font-medium text-dark-600 mb-1">Kelas</label>
                    <select name="kelas_id"
                        class="block w-full px-3 py-2 border border-dark-200 rounded-xl bg-white text-sm text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelasOptions as $k)
                            <option value="{{ $k->id }}" {{ $request->kelas_id == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-xl transition-colors flex items-center gap-1.5 whitespace-nowrap">
                        <i class='bx bx-search'></i> Cari
                    </button>
                    @if ($request->anyFilled(['tanggal', 'hari', 'mata_pelajaran', 'kelas_id']))
                        <a href="{{ route('tutor.jadwal') }}"
                            class="px-4 py-2 border border-dark-200 rounded-xl text-sm text-dark-600 hover:bg-dark-50 transition-colors whitespace-nowrap">
                            Reset Filter
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table Container --}}
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gradient-to-r from-primary-600 to-primary-700 text-white border-b border-primary-800">
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider w-16 text-center">No</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider">Hari</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-center">Jam</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-center">Program</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-center">Peserta</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-center">Ruangan</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-200/50">
                    @forelse ($jadwal as $s)
                        <tr class="hover:bg-gradient-to-r hover:from-primary-50 hover:to-blue-50 transition-all duration-200 border-l-4 {{ $s->status == 'Hari Ini' ? 'border-l-emerald-500 bg-emerald-50/30' : ($s->status == 'Selesai' ? 'border-l-gray-400 bg-gray-50/50' : 'border-l-primary-500') }}">
                            <td class="px-6 py-4 text-sm font-semibold text-primary-600 text-center font-mono">
                                {{ $jadwal->firstItem() + $loop->index }}
                            </td>
                            <td class="px-6 py-4 text-sm text-dark-800 font-medium whitespace-nowrap">
                                {{ $s->tanggal ? \Carbon\Carbon::parse($s->tanggal)->isoFormat('DD-MM-YYYY') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-dark-600">
                                {{ $s->hari }}
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-dark-100 text-dark-700 font-mono text-xs whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($s->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($s->jam_selesai)->format('H:i') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-dark-800 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 text-xs font-medium border border-purple-200">
                                    {{ $s->mata_pelajaran }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-dark-800">
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 text-xs font-semibold whitespace-nowrap">
                                    <i class='bx bx-building text-sm'></i>
                                    {{ $s->kelas->nama_kelas }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-dark-800 text-center">
                                {{ $s->jumlah_peserta }}
                            </td>
                            <td class="px-6 py-4 text-sm text-dark-800 text-center">
                                {{ $s->ruangan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                @if($s->status == 'Hari Ini')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200 whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Hari Ini
                                    </span>
                                @elseif($s->status == 'Selesai')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-bold border border-gray-200 whitespace-nowrap">
                                        <i class='bx bx-check'></i> Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold border border-amber-200 whitespace-nowrap">
                                        <i class='bx bx-time'></i> Akan Datang
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-sm text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick='showDetail(@json($s))'
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-indigo-600 bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-700 hover:shadow-md transition-all duration-200"
                                        title="Lihat Detail">
                                        <i class='bx bx-info-circle text-lg'></i>
                                    </button>

                                    @if($s->status == 'Hari Ini')
                                        <a href="{{ route('absensi.harian') }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 hover:text-emerald-700 hover:shadow-md transition-all duration-200"
                                            title="Mulai Absensi">
                                            <i class='bx bx-scan text-lg'></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 mb-4 rounded-full bg-dark-100 flex items-center justify-center">
                                        <i class='bx bx-calendar-x text-3xl text-dark-400'></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-dark-900">Belum ada data jadwal mengajar</h3>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($jadwal->hasPages())
            <div class="px-6 py-4 border-t border-dark-100 bg-dark-50/30">
                {{ $jadwal->links() }}
            </div>
        @endif
    </div>

    @push('modals')
    {{-- Modal Detail --}}
    <div id="modal-detail" class="fixed inset-0 z-[60] hidden">
        <div class="fixed inset-0 bg-dark-950/50 backdrop-blur-sm transition-opacity" onclick="closeModal('modal-detail')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg fade-in">
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <i class='bx bx-detail text-xl'></i>
                            Detail Jadwal Mengajar
                        </h3>
                        <button type="button" onclick="closeModal('modal-detail')" class="text-white/70 hover:text-white transition-colors">
                            <i class='bx bx-x text-2xl'></i>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="px-6 py-6 space-y-4">
                        <div class="flex flex-col border-b border-dark-100 pb-3">
                            <span class="text-xs text-dark-500 font-medium">Nama Tutor</span>
                            <span class="text-sm text-dark-800 font-semibold" id="dtl-tutor"></span>
                        </div>
                        <div class="flex flex-col border-b border-dark-100 pb-3">
                            <span class="text-xs text-dark-500 font-medium">Program / Mata Pelajaran</span>
                            <span class="text-sm text-dark-800 font-semibold" id="dtl-program"></span>
                        </div>
                        <div class="flex flex-col border-b border-dark-100 pb-3">
                            <span class="text-xs text-dark-500 font-medium">Nama Kelas</span>
                            <span class="text-sm text-dark-800 font-semibold" id="dtl-kelas"></span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 border-b border-dark-100 pb-3">
                            <div class="flex flex-col">
                                <span class="text-xs text-dark-500 font-medium">Tanggal</span>
                                <span class="text-sm text-dark-800 font-semibold" id="dtl-tanggal"></span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs text-dark-500 font-medium">Hari</span>
                                <span class="text-sm text-dark-800 font-semibold" id="dtl-hari"></span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 border-b border-dark-100 pb-3">
                            <div class="flex flex-col">
                                <span class="text-xs text-dark-500 font-medium">Jam Mengajar</span>
                                <span class="text-sm text-dark-800 font-semibold" id="dtl-jam"></span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs text-dark-500 font-medium">Ruangan</span>
                                <span class="text-sm text-dark-800 font-semibold" id="dtl-ruangan"></span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 pb-3">
                            <div class="flex flex-col">
                                <span class="text-xs text-dark-500 font-medium">Jumlah Peserta</span>
                                <span class="text-sm text-dark-800 font-semibold" id="dtl-peserta"></span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs text-dark-500 font-medium">Status</span>
                                <span class="text-sm font-semibold" id="dtl-status"></span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Footer Buttons --}}
                    <div class="px-6 py-4 bg-dark-50 border-t border-dark-100 flex justify-end">
                        <button type="button" onclick="closeModal('modal-detail')"
                            class="px-5 py-2 bg-dark-200 hover:bg-dark-300 text-dark-800 font-medium rounded-xl transition-colors duration-200">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endpush

@endsection

@push('scripts')
<script>
    function showDetail(data) {
        document.getElementById('dtl-tutor').innerText = data.tutor.nama;
        document.getElementById('dtl-program').innerText = data.mata_pelajaran;
        document.getElementById('dtl-kelas').innerText = data.kelas.nama_kelas;
        
        // Format tanggal (DD Juni YYYY)
        let tglStr = '-';
        if (data.tanggal) {
            const dateObj = new Date(data.tanggal);
            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            tglStr = dateObj.toLocaleDateString('id-ID', options);
        }
        document.getElementById('dtl-tanggal').innerText = tglStr;
        
        document.getElementById('dtl-hari').innerText = data.hari;
        
        const jamMulai = data.jam_mulai.substring(0, 5);
        const jamSelesai = data.jam_selesai.substring(0, 5);
        document.getElementById('dtl-jam').innerText = `${jamMulai} - ${jamSelesai}`;
        
        document.getElementById('dtl-ruangan').innerText = data.ruangan || '-';
        document.getElementById('dtl-peserta').innerText = `${data.jumlah_peserta} Orang`;
        
        const statusEl = document.getElementById('dtl-status');
        statusEl.innerText = data.status;
        
        if (data.status === 'Hari Ini') {
            statusEl.className = 'text-sm font-semibold text-emerald-600';
        } else if (data.status === 'Selesai') {
            statusEl.className = 'text-sm font-semibold text-gray-500';
        } else {
            statusEl.className = 'text-sm font-semibold text-amber-600';
        }

        const modal = document.getElementById('modal-detail');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }
</script>
@endpush
