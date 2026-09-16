@extends('layouts.app')

@section('title', 'Laporan Tutor')
@section('page-title', 'Laporan Tutor')
@section('page-subtitle', 'Filter dan detail kehadiran mengajar')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/reports.css') }}">
@endpush

@section('content')
<div class="glass-card p-6 rounded-2xl border border-dark-200/50">
    <form method="GET" action="{{ route('reports.tutors') }}" class="grid grid-cols-1 md:grid-3 gap-4 mb-6">
        <div>
            <label class="block text-sm font-medium text-dark-700 mb-1" for="class_id">Kelas</label>
            <select name="class_id" id="class_id" class="w-full rounded-xl border border-dark-200 bg-dark-50/30 focus:bg-white focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition">
                <option value="">-- Semua Kelas --</option>
                @foreach(App\Models\Kelas::orderBy('nama')->get() as $kelas)
                    <option value="{{ $kelas->id }}" {{ request('class_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-700 mb-1" for="mapel">Mata Pelajaran</label>
            <input type="text" name="mapel" id="mapel" value="{{ request('mapel') }}" placeholder="Cari mapel..." class="w-full rounded-xl border border-dark-200 bg-dark-50/30 focus:bg-white focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition" />
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-700 mb-1" for="date_range">Rentang Tanggal</label>
            <input type="text" name="date_range" id="date_range" value="{{ request('date_range') }}" placeholder="Select range" class="w-full rounded-xl border border-dark-200 bg-dark-50/30 focus:bg-white focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition" readonly />
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-xl hover:from-primary-700 hover:to-primary-800 transition">
                <i class='bx bx-search'></i> Cari
            </button>
        </div>
    </form>

    <table class="min-w-full bg-dark-50/30 rounded-xl overflow-hidden">
        <thead class="bg-primary-600 text-white">
            <tr>
                <th class="p-2 text-left">Nama Tutor</th>
                <th class="p-2 text-left">Mata Pelajaran</th>
                <th class="p-2 text-left">Kelas</th>
                <th class="p-2 text-left">Tanggal</th>
                <th class="p-2 text-left">Jam</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tutors as $tutor)
                @forelse($tutor->jadwals as $jadwal)
                    <tr class="border-b border-dark-200/30">
                        <td class="p-2">{{ $tutor->nama }}</td>
                        <td class="p-2">{{ $tutor->mapel }}</td>
                        <td class="p-2">{{ $jadwal->kelas->nama ?? '-' }}</td>
                        <td class="p-2">{{ \Carbon\Carbon::parse($jadwal->tanggal)->isoFormat('DD MMM YYYY') }}</td>
                        <td class="p-2">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</td>
                    </tr>
                @empty
                    <tr class="border-b border-dark-200/30">
                        <td class="p-2" colspan="5">Tidak ada jadwal untuk tutor ini.</td>
                    </tr>
                @endforelse
            @empty
                <tr><td colspan="5" class="p-4 text-center text-dark-500">Tidak ada data tutor.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
    flatpickr('#date_range', {
        mode: 'range',
        dateFormat: 'Y-m-d',
        locale: 'id',
    });
</script>
@endpush
