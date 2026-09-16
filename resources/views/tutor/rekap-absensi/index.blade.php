@extends('layouts.app')

@section('title', 'Rekap Absensi')
@section('page-title', 'Rekap Absensi')
@section('page-subtitle', 'Rekapitulasi Kehadiran Peserta Berdasarkan Periode dan Kelas')

@section('breadcrumb')
    <li><i class='bx bx-chevron-right text-dark-400'></i></li>
    <li class="text-primary-600 font-medium">Rekap Absensi</li>
@endsection

@section('content')

    {{-- Filter Bar --}}
    <div class="glass-card rounded-2xl border border-dark-200/50 p-6 mb-8 fade-in-up">
        <form action="{{ route('tutor.rekap') }}" method="GET" class="flex flex-col sm:flex-row flex-wrap items-end gap-4">
            
            <div class="w-full sm:w-48">
                <label class="block text-xs font-medium text-dark-600 mb-1.5">Kelas</label>
                <select name="kelas_id"
                    class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-white text-sm text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelasList as $k)
                        <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="w-full sm:w-36">
                <label class="block text-xs font-medium text-dark-600 mb-1.5">Bulan</label>
                <select name="bulan"
                    class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-white text-sm text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all">
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" 
                            {{ $bulan == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="w-full sm:w-32">
                <label class="block text-xs font-medium text-dark-600 mb-1.5">Tahun</label>
                <select name="tahun"
                    class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-white text-sm text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all">
                    @for ($y = date('Y'); $y >= 2026; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="px-5 py-2.5 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white text-sm font-medium rounded-xl transition-all shadow-lg shadow-primary-500/30 hover:shadow-primary-500/40 flex items-center gap-2">
                    <i class='bx bx-filter-alt'></i> Tampilkan
                </button>
                <a href="{{ route('tutor.rekap') }}"
                    class="px-5 py-2.5 border border-dark-200 rounded-xl text-sm text-dark-600 font-medium hover:bg-dark-50 transition-colors flex items-center gap-2">
                    <i class='bx bx-reset'></i> Reset Filter
                </a>
            </div>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8 stagger-children">
        <div class="glass-card rounded-xl p-5 border border-dark-100 flex flex-col items-center justify-center text-center">
            <h3 class="text-2xl font-bold text-dark-800">{{ $totalPeserta }}</h3>
            <p class="text-xs font-medium text-dark-500 uppercase tracking-wider mt-1">Total Peserta</p>
        </div>
        <div class="glass-card rounded-xl p-5 border border-emerald-100/50 bg-emerald-50/10 flex flex-col items-center justify-center text-center">
            <h3 class="text-2xl font-bold text-emerald-600">{{ $totalHadir }}</h3>
            <p class="text-xs font-medium text-emerald-600/70 uppercase tracking-wider mt-1">Hadir</p>
        </div>
        <div class="glass-card rounded-xl p-5 border border-blue-100/50 bg-blue-50/10 flex flex-col items-center justify-center text-center">
            <h3 class="text-2xl font-bold text-blue-600">{{ $totalIzin }}</h3>
            <p class="text-xs font-medium text-blue-600/70 uppercase tracking-wider mt-1">Izin</p>
        </div>
        <div class="glass-card rounded-xl p-5 border border-amber-100/50 bg-amber-50/10 flex flex-col items-center justify-center text-center">
            <h3 class="text-2xl font-bold text-amber-600">{{ $totalSakit }}</h3>
            <p class="text-xs font-medium text-amber-600/70 uppercase tracking-wider mt-1">Sakit</p>
        </div>
        <div class="glass-card rounded-xl p-5 border border-rose-100/50 bg-rose-50/10 flex flex-col items-center justify-center text-center">
            <h3 class="text-2xl font-bold text-rose-600">{{ $totalAlpa }}</h3>
            <p class="text-xs font-medium text-rose-600/70 uppercase tracking-wider mt-1">Alpha</p>
        </div>
        <div class="glass-card rounded-xl p-5 border border-primary-100/50 bg-gradient-to-br from-primary-500 to-primary-600 flex flex-col items-center justify-center text-center shadow-lg shadow-primary-500/30">
            <h3 class="text-2xl font-bold text-white">{{ $persentaseKelas }}%</h3>
            <p class="text-[10px] font-semibold text-white/80 uppercase tracking-wider mt-1">Persentase Kehadiran</p>
        </div>
    </div>

    {{-- Main Data Table --}}
    <div class="glass-card rounded-2xl border border-dark-200/50 overflow-hidden fade-in-up">
        
        {{-- Header Data & Search --}}
        <div class="p-6 border-b border-dark-100 flex flex-col md:flex-row items-center justify-between gap-4 bg-dark-50/30">
            <form action="{{ route('tutor.rekap') }}" method="GET" class="w-full md:w-auto flex-1 max-w-md relative">
                {{-- Preserve other filters --}}
                <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class='bx bx-search text-dark-400 text-lg'></i>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari Nama Peserta..." 
                    class="block w-full pl-10 pr-3 py-2.5 border border-dark-200 rounded-xl bg-white text-sm text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all">
                @if($search)
                    <a href="{{ route('tutor.rekap', ['kelas_id'=>$kelasId, 'bulan'=>$bulan, 'tahun'=>$tahun]) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-dark-400 hover:text-dark-600">
                        <i class='bx bx-x text-xl'></i>
                    </a>
                @endif
            </form>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <a href="{{ route('tutor.rekap.pdf.stream', request()->query()) }}" target="_blank"
                    class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white text-sm font-medium rounded-xl transition-all shadow-lg shadow-primary-500/30 flex items-center gap-2">
                    <i class='bx bx-printer'></i> Cetak PDF
                </a>
                <a href="{{ route('tutor.rekap.pdf.download', request()->query()) }}"
                    class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-dark-200 hover:bg-dark-50 text-dark-700 text-sm font-medium rounded-xl transition-all shadow-sm">
                    <i class='bx bx-download'></i> Download
                </a>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-dark-50/50 text-dark-600 border-b border-dark-200">
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider w-16 text-center">No</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider">Nama Peserta</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-center">Hadir</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-center">Izin</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-center">Sakit</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-center">Alpha</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-center">Kehadiran</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-200/50 bg-white">
                    @forelse ($rekapPeserta as $index => $r)
                        <tr class="hover:bg-dark-50/50 transition-colors duration-200">
                            <td class="px-6 py-4 text-sm font-medium text-dark-500 text-center">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-dark-800 whitespace-nowrap">{{ $r['peserta']->nama }}</td>
                            <td class="px-6 py-4 text-sm text-dark-600">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-dark-50 text-dark-700 text-xs font-medium border border-dark-200">
                                    {{ $r['peserta']->kelas->nama_kelas ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-emerald-600 text-center">{{ $r['hadir'] }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-blue-600 text-center">{{ $r['izin'] }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-amber-600 text-center">{{ $r['sakit'] }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-rose-600 text-center">{{ $r['alpa'] }}</td>
                            <td class="px-6 py-4 text-sm text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold 
                                    {{ $r['persentase'] >= 80 ? 'bg-emerald-100 text-emerald-700' : ($r['persentase'] >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                    {{ $r['persentase'] }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                <button onclick='showDetail(@json($r), "{{ $namaBulan }}")'
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-primary-600 bg-primary-50 hover:bg-primary-100 hover:text-primary-700 transition-all font-medium text-xs border border-primary-200/50">
                                    <i class='bx bx-info-circle text-base'></i> Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 mb-4 rounded-full bg-dark-100 flex items-center justify-center">
                                        <i class='bx bx-folder-open text-3xl text-dark-400'></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-dark-900">Belum ada data peserta</h3>
                                    <p class="mt-1 text-xs text-dark-500">Coba ubah filter atau bulan/tahun.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('modals')
    {{-- Modal Detail Riwayat --}}
    <div id="modal-detail" class="fixed inset-0 z-[60] hidden">
        <div class="fixed inset-0 bg-dark-950/50 backdrop-blur-sm transition-opacity" onclick="closeModal('modal-detail')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-3xl fade-in">
                    
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <i class='bx bx-history text-xl'></i>
                            Detail Riwayat Kehadiran
                        </h3>
                        <button type="button" onclick="closeModal('modal-detail')" class="text-white/70 hover:text-white transition-colors">
                            <i class='bx bx-x text-2xl'></i>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="px-6 py-6">
                        
                        {{-- Ringkasan --}}
                        <div class="bg-dark-50 rounded-xl p-5 border border-dark-100 mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between border-b border-dark-200/50 pb-2">
                                        <span class="text-xs font-medium text-dark-500">Nama Peserta</span>
                                        <span class="text-sm font-bold text-dark-800" id="dtl-nama"></span>
                                    </div>
                                    <div class="flex items-center justify-between border-b border-dark-200/50 pb-2">
                                        <span class="text-xs font-medium text-dark-500">Program</span>
                                        <span class="text-sm font-semibold text-dark-800" id="dtl-program"></span>
                                    </div>
                                    <div class="flex items-center justify-between border-b border-dark-200/50 pb-2">
                                        <span class="text-xs font-medium text-dark-500">Kelas</span>
                                        <span class="text-sm font-semibold text-dark-800" id="dtl-kelas"></span>
                                    </div>
                                    <div class="flex items-center justify-between border-b border-dark-200/50 pb-2">
                                        <span class="text-xs font-medium text-dark-500">Periode</span>
                                        <span class="text-sm font-semibold text-dark-800" id="dtl-periode"></span>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div class="grid grid-cols-4 gap-2">
                                        <div class="bg-emerald-50 border border-emerald-100 rounded-lg p-2 text-center">
                                            <span class="block text-xs text-emerald-600/70 font-medium">Hadir</span>
                                            <span class="block text-lg font-bold text-emerald-600" id="dtl-hadir"></span>
                                        </div>
                                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-2 text-center">
                                            <span class="block text-xs text-blue-600/70 font-medium">Izin</span>
                                            <span class="block text-lg font-bold text-blue-600" id="dtl-izin"></span>
                                        </div>
                                        <div class="bg-amber-50 border border-amber-100 rounded-lg p-2 text-center">
                                            <span class="block text-xs text-amber-600/70 font-medium">Sakit</span>
                                            <span class="block text-lg font-bold text-amber-600" id="dtl-sakit"></span>
                                        </div>
                                        <div class="bg-rose-50 border border-rose-100 rounded-lg p-2 text-center">
                                            <span class="block text-xs text-rose-600/70 font-medium">Alpha</span>
                                            <span class="block text-lg font-bold text-rose-600" id="dtl-alpha"></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between pt-2">
                                        <span class="text-xs font-medium text-dark-500">Persentase Kehadiran</span>
                                        <span class="text-base font-bold text-primary-600" id="dtl-persentase"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tabel Riwayat --}}
                        <div class="border border-dark-200 rounded-xl overflow-hidden">
                            <div class="overflow-y-auto max-h-[300px] custom-scrollbar">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-dark-50 sticky top-0 shadow-sm">
                                        <tr>
                                            <th class="px-4 py-3 text-xs font-semibold text-dark-600 uppercase w-12 text-center">No</th>
                                            <th class="px-4 py-3 text-xs font-semibold text-dark-600 uppercase">Tanggal</th>
                                            <th class="px-4 py-3 text-xs font-semibold text-dark-600 uppercase">Hari</th>
                                            <th class="px-4 py-3 text-xs font-semibold text-dark-600 uppercase text-center">Jam Masuk</th>
                                            <th class="px-4 py-3 text-xs font-semibold text-dark-600 uppercase text-center">Status</th>
                                            <th class="px-4 py-3 text-xs font-semibold text-dark-600 uppercase">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dtl-tbody" class="divide-y divide-dark-100 bg-white">
                                        <!-- Rows generated by JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    
                    {{-- Footer --}}
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
    function showDetail(data, bulanStr) {
        document.getElementById('dtl-nama').innerText = data.peserta.nama;
        document.getElementById('dtl-program').innerText = data.program;
        document.getElementById('dtl-kelas').innerText = data.peserta.kelas ? data.peserta.kelas.nama_kelas : '-';
        document.getElementById('dtl-periode').innerText = bulanStr;
        
        document.getElementById('dtl-hadir').innerText = data.hadir;
        document.getElementById('dtl-izin').innerText = data.izin;
        document.getElementById('dtl-sakit').innerText = data.sakit;
        document.getElementById('dtl-alpha').innerText = data.alpa;
        document.getElementById('dtl-persentase').innerText = data.persentase + '%';
        
        const tbody = document.getElementById('dtl-tbody');
        tbody.innerHTML = '';
        
        if (data.detail && data.detail.length > 0) {
            data.detail.forEach((row, idx) => {
                let badgeClass = '';
                if (row.status === 'Hadir') badgeClass = 'bg-emerald-100 text-emerald-700 border border-emerald-200';
                else if (row.status === 'Izin') badgeClass = 'bg-blue-100 text-blue-700 border border-blue-200';
                else if (row.status === 'Sakit') badgeClass = 'bg-amber-100 text-amber-700 border border-amber-200';
                else badgeClass = 'bg-rose-100 text-rose-700 border border-rose-200';

                const tr = document.createElement('tr');
                tr.className = 'hover:bg-dark-50/50 transition-colors';
                tr.innerHTML = `
                    <td class="px-4 py-3 text-sm text-dark-500 text-center font-medium">${idx + 1}</td>
                    <td class="px-4 py-3 text-sm text-dark-800 font-medium whitespace-nowrap">${row.tanggal}</td>
                    <td class="px-4 py-3 text-sm text-dark-600">${row.hari}</td>
                    <td class="px-4 py-3 text-sm font-mono text-dark-700 text-center">${row.jam_masuk}</td>
                    <td class="px-4 py-3 text-sm text-center">
                        <span class="inline-flex items-center justify-center px-2 py-1 rounded text-[11px] font-bold uppercase tracking-wider ${badgeClass}">
                            ${row.status}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-dark-600">${row.keterangan}</td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-sm text-dark-500">Tidak ada data riwayat absensi.</td>
                </tr>
            `;
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
